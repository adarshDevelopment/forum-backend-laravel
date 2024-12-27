<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Post::factory()->count(10)->create();
        User::create([
            'name' => 'John Cena',
            'email' => 'john@cena.com',
            'password' => 'tiger123'
        ]);

        User::create([
            'name' => 'Randy Orton',
            'email' => 'randy@orton.com',
            'password' => 'tiger123'
        ]);

        User::create([
            'name' => 'Big Show',
            'email' => 'big@show.com',
            'password' => 'tiger123'
        ]);
        
        

        // Tag::factory(10)->create();
        // Post::factory()->count(10)->create();
        // Comment::factory(10)->create();




        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
