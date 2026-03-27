<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiKelas extends Model
{
    use HasFactory;

	protected $table = 'presensi_kelas';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'id_presensi_kelas',
        'id_booking_kelas',
		'no_struk',
		'tanggal',
    ];
}
