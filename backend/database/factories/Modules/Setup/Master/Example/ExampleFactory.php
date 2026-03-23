<?php

namespace Database\Factories\Modules\Setup\Master\Example;

use App\Modules\Setup\Master\Example\Models\Example;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExampleFactory extends Factory
{
    protected $model = Example::class;

    public function definition(): array
    {
        return [
            'code' => 'EX-' . $this->faker->unique()->numerify('#####'),
            'description' => 'Test Seed: ' . $this->faker->sentence(4),
            'transaction_date' => $this->faker->date(),
        ];
    }
}
