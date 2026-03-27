<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiGym extends Model
{
    use HasFactory;

	protected $table = 'presensi_gym';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'id_presensi_gym',
        'id_booking_gym',
		'no_struk',
		'tanggal',
    ];
}
