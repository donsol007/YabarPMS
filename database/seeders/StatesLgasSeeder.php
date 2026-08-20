<?php

namespace Database\Seeders;

use App\Models\Lga;
use App\Models\State;
use Illuminate\Database\Seeder;

class StatesLgasSeeder extends Seeder
{
    public function run(): void
    {
        $states = require __DIR__.'/Data/states_lgas.php';

        foreach ($states as $stateName => $lgas) {
            $state = State::firstOrCreate(['name' => $stateName]);

            foreach ($lgas as $lgaName) {
                Lga::firstOrCreate(['state_id' => $state->id, 'name' => $lgaName]);
            }
        }
    }
}