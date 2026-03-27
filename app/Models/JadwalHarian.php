<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalHarian extends Model
{
    use HasFactory;
	
	protected $table = 'jadwal_harian';
    protected $primaryKey = 'id_jadwal_harian';
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_jadwal_harian ',
		'id_jadwal_umum ',
		'hari',
    ];
}
