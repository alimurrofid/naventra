<?php

namespace Database\Factories\Modules\Setup\Master\Example;

use App\Modules\Setup\Master\Example\Models\ExampleDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExampleDetailFactory extends Factory
{
    protected $model = ExampleDetail::class;

    public function definition(): array
    {
        return [
            'item_name' => $this->faker->words(3, true),
            'qty' => $this->faker->numberBetween(1, 50),
            'price' => $this->faker->randomFloat(2, 5000, 1000000),
        ];
    }
}
