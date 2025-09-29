<?php

namespace Database\Seeders;

use App\Models\BookType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $booktypes = [
            ['name' => 'Ebook', 'slug' => 'ebook'],
            ['name' => 'Paperback', 'slug' => 'paperback'],
            ['name' => 'Hardcover', 'slug' => 'hardcover'],
            ['name' => 'Audiobook', 'slug' => 'audiobook'],
        ];

        foreach ($booktypes as $booktype) {
            BookType::create($booktype);
        }
    }
}
