<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
{
    User::create([
        'name' => 'ines',
        'email' => 'ines@gmail.com',
        'password' => Hash::make('1108ines'),
        'role' => Role::ADMIN->value
    ]);

    client::create([
        'name' => 'hadil',
        'email' => 'hadil@gmail.com',
        'password' => Hash::make('hadil2505'),
        'role' => Role::CLIENT->value
    ]);

    Manager::create([
        'name' => 'afnane',
        'email' => 'afnane@gmail.com',
        'password' => Hash::make('afnane1708'),
        'role' => Role::MANAGER->value
    ]);

    Employee::create([
        'name' => 'rihab',
        'email' => 'rihab@gmail.com',
        'password' => Hash::make('rihab2006'),
        'role' => Role::EMPLOYEE->value
    ]);
 
 
    // Créez de la même façon un manager, employee et client
}
}
