<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Departemen extends Model
{
    use HasFactory, Notifiable;
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'nama_departemen',
        'nama_manager',
        'jumlah_pegawai',
    ];


}
        