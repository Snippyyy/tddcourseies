<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AddGivenCoursesSeeder extends Seeder
{
    public function run(): void
    {

        if ($this->isDataAlreadyGiven()) {
            return;
        }

        Course::create([
            'paddle_product_id' => '34779',
           'slug' => Str::of('Laravel for Beginners')->slug(),
            'title' => 'Laravel for Beginners',
            'tagline' => 'Make your first steps as a Laravel developer',
            'description' => 'A video course to teach you Laravel from scratch',
            'image_name' => 'Laravel_for_beginners.jpg',
            'learnings' => [
                'How to start with Laravel',
                'Where to start with Laravel',
                'Building your first Laravel app'
            ],
            'released_at' => now(),
        ]);

        Course::create([
            'paddle_product_id' => '34780',
            'slug' => Str::of('Advanced Laravel')->slug(),
            'title' => 'Advanced Laravel',
            'tagline' => 'Level up as a Laravel developer',
            'description' => 'A video course to teach you advanced techniques in Laravel',
            'image_name' => 'advance_laravel.jpg',
            'learnings' => [
                'How to use the service container',
                'Pipelines in laravel',
                'Secure your application'
            ],
            'released_at' => now(),
        ]);

        Course::create([
            'paddle_product_id' => '34781',
            'slug' => Str::of('TDD The Laravel Way')->slug(),
            'title' => 'TDD The Laravel way',
            'tagline' => 'Lear TDD with Laravel',
            'description' => 'A video course to teach you TDD with Laravel',
            'image_name' => 'tdd_laravel.jpg',
            'learnings' => [
                'How to use PEST',
                'Test pages in Laravel',
                'Test components in Laravel'
            ],
            'released_at' => now(),
        ]);
    }

    private function isDataAlreadyGiven(): bool
    {
        return Course::where('title', 'Laravel for Beginners')->exists()
            && Course::where('title', 'Advanced Laravel')->exists()
            && Course::where('title', 'TDD The Laravel way')->exists();
    }
}
