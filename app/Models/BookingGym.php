<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingGym extends Model
{
    use HasFactory;

	protected $table = 'booking_gym';
    protected $primaryKey = 'id_booking_gym';
	public $timestamps = false;
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
		'id_booking_gym',
        'id_member',
        'tanggal',
        'slot_waktu',
		'status',
    ];
}
