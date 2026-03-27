<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Instruktur extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'instruktur';
    protected $primaryKey = 'id_instruktur';
    public $timestamps = false;

    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_instruktur',
        'nama_instruktur',
        'tanggal_lahir',
        'waktu_terlambat',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
