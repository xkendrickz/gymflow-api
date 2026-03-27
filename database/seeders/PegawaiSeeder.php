<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            [
                'id_role'      => 1,
                'nama_pegawai' => 'Administrator',
                'tanggal_lahir' => '1990-01-01',
                'username'     => 'admin',
                'password'     => Hash::make('admin123'),
            ],
            [
                'id_role'      => 2,
                'nama_pegawai' => 'Manajer Operasional',
                'tanggal_lahir' => '1990-01-01',
                'username'     => 'mo',
                'password'     => Hash::make('mo123'),
            ],
            [
                'id_role'      => 3,
                'nama_pegawai' => 'Kasir',
                'tanggal_lahir' => '1990-01-01',
                'username'     => 'kasir',
                'password'     => Hash::make('kasir123'),
            ],
        ];

        foreach ($pegawai as $data) {
            Pegawai::firstOrCreate(
                ['username' => $data['username']],
                $data
            );
        }
    }
}