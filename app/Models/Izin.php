<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

	protected $table = 'izin';
    protected $primaryKey = 'id_izin';
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_izin',
        'id_instruktur',
		'id_jadwal_harian',
        'detail_izin',
		'tanggal_izin',
        'konfirmasi',
    ];
}
