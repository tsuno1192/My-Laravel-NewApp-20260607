<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(), // UUIDを生成
        'book_title' => $this->faker->sentence(3),
        'book_number' => $this->faker->bothify('BK-####'),
        'category' => $this->faker->word,
        'key_word' => $this->faker->words(3, true),
        'factory_name' => '工場A', // テスト用に固定値
        'facility_name' => '設備a', // テスト用に固定値
        'file_path' => 'dummy/path/to/file.pdf', // 必須カラムのため仮の値
        ];
    }
}
