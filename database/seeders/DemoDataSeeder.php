<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Equity;
use App\Models\FixedDebtInstrument;
use App\Models\InstrumentDetail;
use App\Models\PaymentBreakdown;
use App\Models\PaymentHistory;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Client::query()->exists()) {
            return;
        }

        $staff = User::role('staff')->first() ?? User::first();
        $admin = User::role('admin')->first() ?? User::first();

        $clients = [
            [
                'name' => ['surname' => 'Adebayo', 'first_name' => 'Oluwaseun', 'middle_name' => 'Ade'],
                'sex' => 'Male',
                'email' => 'oluwaseun.adebayo@example.com',
                'amount' => 15000000,
                'state' => 'Lagos',
                'lga' => 'Ikeja',
                'bank' => 'Access Bank',
            ],
            [
                'name' => ['surname' => 'Okafor', 'first_name' => 'Ngozi', 'middle_name' => 'Chiamaka'],
                'sex' => 'Female',
                'email' => 'ngozi.okafor@example.com',
                'amount' => 8500000,
                'state' => 'Anambra',
                'lga' => 'Awka South',
                'bank' => 'Zenith Bank',
            ],
            [
                'name' => ['surname' => 'Mohammed', 'first_name' => 'Ibrahim', 'middle_name' => 'Bello'],
                'sex' => 'Male',
                'email' => 'ibrahim.mohammed@example.com',
                'amount' => 23000000,
                'state' => 'Kano',
                'lga' => 'Kano Municipal',
                'bank' => 'First Bank of Nigeria',
            ],
            [
                'name' => ['surname' => 'Eze', 'first_name' => 'Chinedu', 'middle_name' => 'Emeka'],
                'sex' => 'Male',
                'email' => 'chinedu.eze@example.com',
                'amount' => 5000000,
                'state' => 'Enugu',
                'lga' => 'Enugu North',
                'bank' => 'UBA',
            ],
            [
                'name' => ['surname' => 'Bello', 'first_name' => 'Aisha', 'middle_name' => 'Fati'],
                'sex' => 'Female',
                'email' => 'aisha.bello@example.com',
                'amount' => 12000000,
                'state' => 'FCT',
                'lga' => 'Municipal Area Council',
                'bank' => 'GTBank',
            ],
        ];

        $creator = [$admin->id, $staff->id];

        foreach ($clients as $key => $data) {
            $stateId = DB::table('states')->where('name', $data['state'])->value('id');
            $lgaId = $stateId
                ? DB::table('lgas')->where('state_id', $stateId)->where('name', $data['lga'])->value('id')
                : null;
            $bankId = DB::table('banks')->where('name', $data['bank'])->value('id');

            $client = Client::create([
                'client_id' => Client::nextClientId(),
                'surname' => $data['name']['surname'],
                'first_name' => $data['name']['first_name'],
                'middle_name' => $data['name']['middle_name'],
                'sex' => $data['sex'],
                'date_of_birth' => fake()->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d'),
                'mobile_number' => '080'.random_int(10000000, 99999999),
                'mother_maiden_name' => fake()->lastName(),
                'residential_address' => fake()->streetAddress().', Nigeria',
                'state_of_origin_id' => $stateId,
                'lga_id' => $lgaId,
                'marital_status' => fake()->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
                'religion' => fake()->randomElement(['Christianity', 'Islam', 'Traditional', 'Other']),
                'email' => $data['email'],
                'amount_to_invest' => $data['amount'],
                'bank_id' => $bankId,
                'account_name' => $data['name']['first_name'].' '.$data['name']['surname'],
                'account_number' => (string) random_int(1000000000, 9999999999),
                'account_type' => fake()->randomElement(['Savings', 'Current', 'Domiciliary']),
                'bvn' => (string) random_int(10000000000, 99999999999),
                'account_opening_date' => fake()->dateTimeBetween('-5 years')->format('Y-m-d'),
                'bank_address' => fake()->streetAddress().', Nigeria',
                'occupation' => fake()->jobTitle(),
                'employer_name' => fake()->company(),
                'employer_address' => fake()->address(),
                'hobbies' => fake()->words(4, true),
                'created_by' => $creator[$key % count($creator)],
            ]);

            $client->nextOfKin()->create([
                'name' => fake()->name(),
                'address' => fake()->streetAddress().', Nigeria',
                'phone' => '080'.random_int(10000000, 99999999),
                'relationship' => fake()->randomElement(['Spouse', 'Sibling', 'Parent', 'Child', 'Friend']),
                'email' => fake()->safeEmail(),
            ]);

            if (($key % 5) < 4) {
                $portfolio = Portfolio::create(['client_id' => $client->id]);

                $instruments = [
                    ['description' => 'Commercial Paper 2026', 'amount' => $data['amount'] * 0.4, 'status' => 'Active'],
                    ['description' => 'Treasury Bill 2026', 'amount' => $data['amount'] * 0.35, 'status' => 'Active'],
                    ['description' => 'Fixed Deposit 2025', 'amount' => $data['amount'] * 0.25, 'status' => 'Matured'],
                ];

                foreach ($instruments as $i => $instrument) {
                    $fixed = FixedDebtInstrument::create([
                        'portfolio_id' => $portfolio->id,
                        'description' => $instrument['description'],
                        'amount' => $instrument['amount'],
                        'status' => $instrument['status'],
                    ]);

                    $fixed->detail()->create([
                        'subscription_date' => now()->subMonths(6 - $i)->toDateString(),
                        'instrument' => 'CP',
                        'tenor' => ['91 days', '182 days', '365 days'][$i],
                        'rental_rate' => [12.5, 11.0, 10.5][$i],
                        'settlement_date' => now()->addMonths(6 - $i)->toDateString(),
                    ]);

                    for ($p = 0; $p < 6; $p++) {
                        $paymentDate = now()->addMonths($p)->toDateString();
                        $status = $paymentDate < now()->toDateString() ? 'paid' : ($p === 0 ? 'unpaid' : 'blank');

                        $fixed->paymentBreakdowns()->create([
                            'roi' => 'ROI '.($p + 1),
                            'amount' => round($instrument['amount'] * 0.01, 2),
                            'payment_date' => $paymentDate,
                            'status' => $status,
                        ]);
                    }
                }

                if ($key > 1) {
                    PaymentHistory::create([
                        'portfolio_id' => $portfolio->id,
                        'date' => now()->subMonths(1)->toDateString(),
                        'amount_paid' => 120000,
                        'payment_status' => 'paid',
                    ]);
                }

                $equities = [
                    ['stock' => 'GUARANTY', 'unit' => 5000, 'price' => 42.50],
                    ['stock' => 'DANGOTE', 'unit' => 1200, 'price' => 310.00],
                    ['stock' => 'MTNN', 'unit' => 3000, 'price' => 205.00],
                ];

                foreach ($equities as $eq) {
                    $portfolio->equities()->create([
                        'stock' => $eq['stock'],
                        'unit' => $eq['unit'],
                        'price' => $eq['price'],
                    ]);
                }
            }
        }
    }
}