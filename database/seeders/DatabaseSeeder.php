<?php

namespace Database\Seeders;

use App\Models\Blog;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Phone;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        // $this->call([
        //     // UserSeeder::class,
        //     BlogSeeder::class,
        // ]);

        Blog::factory(100)->create();
        Phone::factory(50)->create();
    }
}
