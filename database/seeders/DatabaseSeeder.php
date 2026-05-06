<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Article;
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
        // Create test users
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $regularUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin'
        ]);

        // Create categories
        $categories = [
            ['title' => 'Technology', 'status' => 1],
            ['title' => 'Business', 'status' => 1],
            ['title' => 'Entertainment', 'status' => 1],
            ['title' => 'Health', 'status' => 1],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create sample articles
        Article::create([
            'title' => 'Getting Started with Laravel',
            'body' => 'Laravel is a powerful PHP framework for building modern web applications.',
            'user_id' => $adminUser->id,
            'category_id' => 1,
            'status' => 1,
            'image' => null
        ]);

        Article::create([
            'title' => 'Business Tips for Entrepreneurs',
            'body' => 'Here are some valuable tips for starting and growing your business.',
            'user_id' => $regularUser->id,
            'category_id' => 2,
            'status' => 1,
            'image' => null
        ]);

        Article::create([
            'title' => 'Latest Movie Reviews',
            'body' => 'Check out the latest reviews of trending movies.',
            'user_id' => $adminUser->id,
            'category_id' => 3,
            'status' => 1,
            'image' => null
        ]);

        Article::create([
            'title' => 'Health and Wellness Guide',
            'body' => 'A comprehensive guide to maintaining good health and wellness.',
            'user_id' => $regularUser->id,
            'category_id' => 4,
            'status' => 0,
            'image' => null
        ]);
    }
}