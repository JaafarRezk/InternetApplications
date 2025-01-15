<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word . '.txt',
            'path' => 'files/' . $this->faker->word . '.txt',
            'mime_type' => 'text/plain',
            'size' => $this->faker->numberBetween(100, 5000),
            'checked' => 0,
            'file_holder_id' => null,
        ];
    }
}
