<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiInstruktur extends Model
{
    use HasFactory;

	protected $table = 'presensi_instruktur';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'id_presensi_instruktur',
        'id_jadwal_harian',
        'mulai_kelas',
        'selesai_kelas',
    ];

}
