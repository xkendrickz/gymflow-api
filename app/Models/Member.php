<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'member';
    protected $primaryKey = 'id_member';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_member',
        'member_id',
        'nama_member',
        'alamat',
        'tanggal_lahir',
        'tanggal_daftar',
        'telepon',
        'email',
        'status',
        'sisa_deposit_reguler',
        'sisa_deposit_paket',
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
