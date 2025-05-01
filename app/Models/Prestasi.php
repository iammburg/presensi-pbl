<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'prestasis';

    /**
     * Kolom-kolom yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'jenis_prestasi',
        'kategori_prestasi',
        'bukti', // Kolom untuk menyimpan path file bukti
        'status', // Status: pending, proses, selesai
        'poin',
    ];

    /**
     * Default atribut untuk model ini.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending', // Nilai default untuk kolom status
    ];

    /**
     * Scope untuk mendapatkan data dengan status tertentu.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Mendapatkan label status dengan format yang lebih user-friendly.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'Menunggu';
            case 'proses':
                return 'Sedang Diproses';
            case 'selesai':
                return 'Selesai';
            default:
                return 'Tidak Diketahui';
        }
    }
}
