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
            ['name' => 'Book', 'slug' => 'book', 'is_pdf' => '0'],
            ['name' => 'Ebook', 'slug' => 'ebook', 'is_pdf' => '1'],
            ['name' => 'Paperback', 'slug' => 'paperback', 'is_pdf' => '1'],
            ['name' => 'Hardcover', 'slug' => 'hardcover', 'is_pdf' => '1'],
            ['name' => 'Audiobook', 'slug' => 'audiobook', 'is_pdf' => '1'],
        ];

        foreach ($booktypes as $booktype) {
            BookType::create($booktype);
        }
    }
}
