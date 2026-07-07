<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => config('site.user.name'),
            'email' => config('site.user.email'),
            'password' => config('site.user.password'),
            'is_admin' => true,
        ]);

        $this->call([
            SportSeeder::class,
            TeamSeeder::class,
            // PlayerSeeder::class,
            // GameSeeder::class,
            NewsSeeder::class,
            GallerySeeder::class,
            SponsorSeeder::class,
            SocialBodySeeder::class,
        ]);
    }
}
