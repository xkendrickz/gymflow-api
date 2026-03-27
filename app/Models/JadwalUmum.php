<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUmum extends Model
{
    use HasFactory;
	protected $table = 'jadwal_umum';
    protected $primaryKey = 'id_jadwal_umum';
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_jadwal_umum',
		'id_instruktur',
		'id_kelas',
		'jam',
		'hari'
    ];
}
