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
        ];
    }

    protected function findHeaderKey($row, $field)
    {
        $possibleHeaders = $this->headerMap[$field];
        $headers = array_keys($row);

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
            Log::info('Processing row:', $row);

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

            if (!$nisnKey || !$namaKey || !$alamatKey || !$teleponKey || !$namaOrangTuaKey ||
                !$teleponOrangTuaKey || !$emailOrangTuaKey || !$jenisKelaminKey || !$tanggalLahirKey ||
                !$tahunMasukKey) {
                throw new \Exception('Ada kolom wajib yang tidak ditemukan di file Excel');
            }

            $gender = strtoupper(trim($row[$jenisKelaminKey]));
            if (!in_array($gender, ['L', 'P'])) {
                throw new \Exception('Jenis kelamin harus L atau P.');
            }

            $birthDate = $this->parseDate($row[$tanggalLahirKey]);
            if (!$birthDate) {
                throw new \Exception('Format tanggal lahir tidak valid.');
            }

            // Ambil kelas pertama atau kosongkan jika tidak ada
            $class = SchoolClass::first();
            $classId = $class ? $class->id : null;

            if (Student::where('nisn', $row[$nisnKey])->exists()) {
                Log::warning('NISN sudah ada, dilewati: ' . $row[$nisnKey]);
                return null;
            }

            return DB::transaction(function () use ($row, $nisnKey, $namaKey, $alamatKey, $teleponKey, $namaOrangTuaKey, $teleponOrangTuaKey, $emailOrangTuaKey, $jenisKelaminKey, $birthDate, $tahunMasukKey, $classId) {
                $user = User::firstOrCreate(
                    ['email' => strtolower($row[$emailOrangTuaKey])],
                    [
                        'name' => $row[$namaKey],
                        'password' => Hash::make($row[$nisnKey]),
                    ]
                );

                $user->assignRole('Siswa');

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
                    'class_id' => $classId,
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
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }

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
        return [];
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
