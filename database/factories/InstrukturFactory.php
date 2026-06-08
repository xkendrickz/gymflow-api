<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class InstrukturFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_instruktur' => $this->faker->name(),
            'tanggal_lahir'   => $this->faker->date('Y-m-d', '-25 years'),
            'username'        => $this->faker->unique()->userName(),
            'password'        => Hash::make('password'),
        ];
    }
}