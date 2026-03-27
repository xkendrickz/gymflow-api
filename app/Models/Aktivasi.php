<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivasi extends Model
{
    use HasFactory;

	protected $table = 'aktivasi';
    protected $primaryKey = 'id_aktivasi';
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_aktivasi',
        'id_member',
        'id_pegawai',
		'no_struk',
        'tanggal_aktivasi',
        'harga',
		'masa_aktif',
    ];
}
