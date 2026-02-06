<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Luis Gerardo',
            'first_last_name' => 'Huízar',
            'second_last_name' => 'Martínez',
            'birthday' => '1997-04-02',
            'gender' => 'Hombre',
            'telephone' => '3111925638',
            'cellphone' => '3111119374',
            'country' => 'México',
            'colony_id' => 201,
            'street' => 'Lucio Cabañas',
            'no_ext' => '367',
            'status' => 'Activo',
            'url_image' => 'https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.prydwen.gg%2Fzenless%2Fcharacters%2Fyixuan&psig=AOvVaw1FshCnbNKRkaLkJCLZs0bE&ust=1754031947150000&source=images&cd=vfe&opi=89978449&ved=0CBUQjRxqFwoTCODv-bDE5o4DFQAAAAAdAAAAABAb'
        ]);
    }
}
