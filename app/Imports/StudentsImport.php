<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithBatchInserts
{
    use SkipsErrors;

    protected $headerMap = [];

    public function __construct()
    {
        // Mapping berbagai kemungkinan nama kolom
        $this->headerMap = [
            'nisn' => ['nisn'],
            'nama' => ['nama', 'name', 'nama_lengkap'],
            'alamat' => ['alamat', 'address', 'alamat_lengkap'],
            'telepon' => ['telepon', 'telp', 'hp', 'no_hp', 'nomor_telepon'],
            'nama_orang_tua' => ['nama_orang_tua', 'parent_name'],
            'telepon_orang_tua' => ['telepon_orang_tua', 'parent_phone'],
            'email_orang_tua' => ['email_orang_tua', 'parent_email'],
            'jenis_kelamin' => ['jenis_kelamin', 'jenis_kelamin_lp', 'gender'],
            'tanggal_lahir' => ['tanggal_lahir', 'tanggal_lahir_yyyy_mm_dd', 'birth_date'],
            'tahun_masuk' => ['tahun_masuk', 'enter_year'],
            'id_kelas' => ['id_kelas', 'id_kelas_referensi_id_kelas_di_tabel_school_classes'],
        ];
    }

    protected function findHeaderKey($row, $field)
    {
        $possibleHeaders = $this->headerMap[$field];
        $headers = array_keys($row);

        // Cari header yang cocok (case insensitive)
        foreach ($headers as $header) {
            $normalizedHeader = strtolower(str_replace([' ', '_', '-'], '', $header));
            foreach ($possibleHeaders as $possibleHeader) {
                if (strtolower(str_replace([' ', '_', '-'], '', $possibleHeader)) === $normalizedHeader) {
                    return $header;
                }
            }
        }

        return null;
    }

    public function model(array $row)
    {
        try {
            // Debug log
            Log::info('Processing row:', $row);

            // Temukan key yang sesuai untuk setiap field
            $nisnKey = $this->findHeaderKey($row, 'nisn');
            $namaKey = $this->findHeaderKey($row, 'nama');
            $alamatKey = $this->findHeaderKey($row, 'alamat');
            $teleponKey = $this->findHeaderKey($row, 'telepon');
            $namaOrangTuaKey = $this->findHeaderKey($row, 'nama_orang_tua');
            $teleponOrangTuaKey = $this->findHeaderKey($row, 'telepon_orang_tua');
            $emailOrangTuaKey = $this->findHeaderKey($row, 'email_orang_tua');
            $jenisKelaminKey = $this->findHeaderKey($row, 'jenis_kelamin');
            $tanggalLahirKey = $this->findHeaderKey($row, 'tanggal_lahir');
            $tahunMasukKey = $this->findHeaderKey($row, 'tahun_masuk');
            $idKelasKey = $this->findHeaderKey($row, 'id_kelas');

            // Validasi semua field required ada
            if (!$nisnKey || !$namaKey || !$alamatKey || !$teleponKey || !$namaOrangTuaKey ||
                !$teleponOrangTuaKey || !$emailOrangTuaKey || !$jenisKelaminKey || !$tanggalLahirKey ||
                !$tahunMasukKey || !$idKelasKey) {
                throw new \Exception('Ada kolom wajib yang tidak ditemukan di file Excel');
            }

            // Validasi jenis kelamin
            $gender = strtoupper(trim($row[$jenisKelaminKey]));
            if (!in_array($gender, ['L', 'P'])) {
                throw new \Exception('Jenis kelamin harus L atau P.');
            }

            // Format tanggal lahir
            $birthDate = $this->parseDate($row[$tanggalLahirKey]);
            if (!$birthDate) {
                throw new \Exception('Format tanggal lahir tidak valid.');
            }

            // Validasi ID Kelas
            $class = SchoolClass::find($row[$idKelasKey]);
            if (!$class) {
                throw new \Exception("Kelas dengan ID '{$row[$idKelasKey]}' tidak ditemukan.");
            }

            // Check jika NISN sudah ada
            if (Student::where('nisn', $row[$nisnKey])->exists()) {
                Log::warning('NISN sudah ada, dilewati: ' . $row[$nisnKey]);
                return null;
            }

            // Mulai transaksi database
            return DB::transaction(function () use ($row, $nisnKey, $namaKey, $alamatKey, $teleponKey, $namaOrangTuaKey, $teleponOrangTuaKey, $emailOrangTuaKey, $jenisKelaminKey, $birthDate, $tahunMasukKey, $class) {
                // Buat user account
                $user = User::firstOrCreate(
                    ['email' => strtolower($row[$emailOrangTuaKey])],
                    [
                        'name' => $row[$namaKey],
                        'password' => Hash::make($row[$nisnKey]),
                    ]
                );

                $user->assignRole('Siswa');

                // Buat data siswa
                return Student::create([
                    'nisn' => $row[$nisnKey],
                    'name' => $row[$namaKey],
                    'gender' => $row[$jenisKelaminKey],
                    'birth_date' => $birthDate,
                    'address' => $row[$alamatKey],
                    'phone' => $row[$teleponKey],
                    'parent_name' => $row[$namaOrangTuaKey],
                    'parent_phone' => $row[$teleponOrangTuaKey],
                    'parent_email' => $row[$emailOrangTuaKey],
                    'enter_year' => $row[$tahunMasukKey],
                    'class_id' => $class->id,
                    'user_id' => $user->id,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in row:', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            // Jika input adalah numeric (Excel date)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Jika format sudah YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }

            // Coba parse dengan Carbon (mendukung berbagai format)
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error('Date parsing error:', [
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function rules(): array
    {
        return [];  // Validasi dilakukan secara manual di model()
    }

    public function customValidationMessages()
    {
        return [];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function headingRow(): int
    {
        return 1;
    }
}