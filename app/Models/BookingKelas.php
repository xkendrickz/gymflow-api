<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingKelas extends Model
{
    use HasFactory;

	protected $table = 'booking_kelas';
    protected $primaryKey = 'id_booking_kelas';
	public $timestamps = false;
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
		'id_booking_kelas',
        'id_member',
        'id_jadwal_harian',
        'no_booking',
		'jenis',
		'status',
    ];
}
