<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\Client;
use App\Models\Lga;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $state = State::inRandomOrder()->first() ?? State::factory()->create();
        $lga = Lga::inRandomOrder()->where('state_id', $state->id)->first()
            ?? Lga::factory()->create(['state_id' => $state->id]);
        $bank = Bank::inRandomOrder()->first() ?? Bank::factory()->create();

        return [
            'client_id' => Client::nextClientId(),
            'surname' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'sex' => fake()->randomElement(['Male', 'Female']),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'mobile_number' => '080'.random_int(10000000, 99999999),
            'mother_maiden_name' => fake()->lastName(),
            'residential_address' => fake()->streetAddress(),
            'state_of_origin_id' => $state->id,
            'lga_id' => $lga->id,
            'marital_status' => fake()->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'religion' => fake()->randomElement(['Christianity', 'Islam', 'Traditional', 'Other']),
            'email' => fake()->unique()->safeEmail(),
            'amount_to_invest' => fake()->randomFloat(2, 100000, 50000000),
            'bank_id' => $bank->id,
            'account_name' => fake()->name(),
            'account_number' => (string) random_int(1000000000, 9999999999),
            'account_type' => fake()->randomElement(['Savings', 'Current', 'Domiciliary']),
            'bvn' => (string) random_int(10000000000, 99999999999),
            'account_opening_date' => fake()->date(),
            'bank_address' => fake()->streetAddress(),
            'occupation' => fake()->jobTitle(),
            'employer_name' => fake()->company(),
            'employer_address' => fake()->address(),
            'hobbies' => fake()->words(3, true),
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}