<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BanksSeeder extends Seeder
{
    public function run(): void
    {
        $banks = require __DIR__.'/Data/banks.php';

        foreach ($banks as $bank) {
            Bank::firstOrCreate(['name' => $bank['name']], ['code' => $bank['code']]);
        }
    }
}