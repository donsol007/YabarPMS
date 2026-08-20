<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'company_name' => 'Yabar Finance Consult Limited',
            'currency_symbol' => '₦',
            'due_notice_days' => '7',
            'upload_max_size' => '5',
        ];

        foreach ($defaults as $key => $value) {
            if (Setting::where('key', $key)->doesntExist()) {
                Setting::create(['key' => $key, 'value' => $value]);
            }
        }
    }
}