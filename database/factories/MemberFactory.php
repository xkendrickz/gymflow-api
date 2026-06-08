<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'member_id'              => strtoupper($this->faker->bothify('MBR-####')),
            'nama_member'            => $this->faker->name(),
            'alamat'                 => $this->faker->address(),
            'tanggal_lahir'          => $this->faker->date('Y-m-d', '-20 years'),
            'tanggal_daftar'         => now()->toDateString(),
            'telepon'                => $this->faker->phoneNumber(),
            'email'                  => $this->faker->unique()->safeEmail(),
            'status'                 => 1,
            'sisa_deposit_reguler'   => 0,
            'sisa_deposit_paket'     => 0,
            'username'               => $this->faker->unique()->userName(),
            'password'               => Hash::make('password'),
        ];
    }
}