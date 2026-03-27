<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositReguler extends Model
{
    use HasFactory;

	protected $table = 'deposit_reguler';
    protected $primaryKey = 'id_deposit_reguler';
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_deposit_reguler',
        'id_member',
        'id_pegawai',
		'no_struk',
        'tanggal',
        'deposit',
		'bonus',
		'total_deposit_reguler',
    ];
}
