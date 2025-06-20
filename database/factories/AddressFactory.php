<?php

namespace Sharenjoy\NoahShop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Sharenjoy\NoahShop\Models\Address;
use Sharenjoy\NoahShop\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Sharenjoy\NoahShop\Models\Menu>
 */
class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'country' => fake('en')->country(),
            'city' => fake('en')->city(),
            'district' => fake('en')->state(),
            'address' => fake('en')->streetAddress(),
            'postcode' => fake('en')->postcode(),
        ];
    }
}
