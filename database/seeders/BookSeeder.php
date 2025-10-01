<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookLaw;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'book_type_id' => 1,
                'title' => 'THE 48 LAWS OF DIVINE POWER',
                'slug' => 'the-48-laws-of-divine-power',
                'author' => 'Dr. C. Errol Ball',
                'isbn' => 'LDP-12345',
                'price' => '2.99',
                'free_laws' => '7',
                'image' => 'uploads/book-images/48-laws-of-divine-power.jpg',
                'description' => 'This is a Test Description',
                'publication_year' => '2025-10-01',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        $bookLaws = [
            [
                'book_id' => 1,
                'title' => 'Test Law 1',
                'slug' => 'test-law-1',
                'content' => 'This is a test law 1',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 2',
                'slug' => 'test-law-2',
                'content' => 'This is a test law 2',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 3',
                'slug' => 'test-law-3',
                'content' => 'This is a test law 3',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 4',
                'slug' => 'test-law-4',
                'content' => 'This is a test law 4',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 5',
                'slug' => 'test-law-5',
                'content' => 'This is a test law 5',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 6',
                'slug' => 'test-law-6',
                'content' => 'This is a test law 6',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 7',
                'slug' => 'test-law-7',
                'content' => 'This is a test law 7',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 8',
                'slug' => 'test-law-8',
                'content' => 'This is a test law 8',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 9',
                'slug' => 'test-law-9',
                'content' => 'This is a test law 9',
            ],
            [
                'book_id' => 1,
                'title' => 'Test Law 10',
                'slug' => 'test-law-10',
                'content' => 'This is a test law 10',
            ],
        ];

        foreach ($bookLaws as $bookLaw) {
            BookLaw::create($bookLaw);
        }
    }
}
