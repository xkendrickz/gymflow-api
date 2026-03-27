<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositKelas extends Model
{
    use HasFactory;

	protected $table = 'deposit_paket';
    protected $primaryKey = 'id_deposit_paket';
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_deposit_paket',
        'id_member',
        'id_pegawai',
		'id_kelas',
		'no_struk',
		'harga',
        'tanggal',
        'deposit',
		'jumlah_deposit_paket',
		'berlaku_sampai',
    ];
}
