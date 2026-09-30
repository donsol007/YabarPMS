<?php

use App\Models\Client;
use App\Models\ClientRegistrationInvite;
use App\Models\State;
use App\Models\User;
use App\Notifications\ClientRegisteredNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.register')] class extends Component
{
    public string $token = '';
    public $surname = '';
    public $first_name = '';
    public $middle_name = '';
    public $sex = '';
    public $date_of_birth = '';
    public $mobile_number = '';
    public $mother_maiden_name = '';
    public $residential_address = '';
    public $state_of_origin_id = null;
    public $lga_name = '';
    public $marital_status = '';
    public $religion = '';
    public $email = '';
    public $amount_to_invest = null;

    public $bank_name = null;
    public $account_name = '';
    public $account_number = '';
    public $account_type = '';
    public $bvn = '';
    public $account_opening_date = '';
    public $bank_address = '';

    public $occupation = '';
    public $employer_name = '';
    public $employer_address = '';
    public $hobbies = '';

    public array $next_of_kin = [
        'name' => '',
        'address' => '',
        'phone' => '',
        'relationship' => '',
        'email' => '',
    ];

    public bool $registered = false;
    public string $registeredId = '';

    public function mount(string $token): void
    {
        $this->token = $token;
    }

    #[Computed]
    public function invite(): ?ClientRegistrationInvite
    {
        return ClientRegistrationInvite::query()->where('token', $this->token)->first();
    }

    public function rules(): array
    {
        return [
            'surname' => ['required', 'string', 'max:191'],
            'first_name' => ['required', 'string', 'max:191'],
            'middle_name' => ['nullable', 'string', 'max:191'],
            'sex' => ['required', 'in:Male,Female'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'mobile_number' => ['required', 'string', 'max:20', 'regex:/^0[0-9]{10}$/'],
            'mother_maiden_name' => ['nullable', 'string', 'max:191'],
            'residential_address' => ['nullable', 'string'],
            'state_of_origin_id' => ['nullable', 'exists:states,id'],
            'lga_name' => ['nullable', 'string', 'max:191'],
            'marital_status' => ['nullable', 'in:Single,Married,Divorced,Widowed'],
            'religion' => ['nullable', 'in:Christianity,Islam,Traditional,Other'],
            'email' => ['required', 'email', 'max:191', 'unique:clients,email'],
            'amount_to_invest' => ['nullable', 'numeric', 'min:0', 'max:99999999999999'],

            'bank_name' => ['nullable', 'string', 'max:191'],
            'account_name' => ['nullable', 'string', 'max:191'],
            'account_number' => ['nullable', 'string', 'digits:10'],
            'account_type' => ['nullable', 'in:Savings,Current,Domiciliary'],
            'bvn' => ['nullable', 'string', 'digits:11'],
            'account_opening_date' => ['nullable', 'date'],
            'bank_address' => ['nullable', 'string'],

            'occupation' => ['nullable', 'string', 'max:191'],
            'employer_name' => ['nullable', 'string', 'max:191'],
            'employer_address' => ['nullable', 'string'],
            'hobbies' => ['nullable', 'string'],

            'next_of_kin.name' => ['nullable', 'string', 'max:191'],
            'next_of_kin.address' => ['nullable', 'string'],
            'next_of_kin.phone' => ['nullable', 'string', 'max:20'],
            'next_of_kin.relationship' => ['nullable', 'string', 'max:191'],
            'next_of_kin.email' => ['nullable', 'email', 'max:191'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile_number.regex' => 'The mobile number must be a valid Nigerian phone number (e.g. 08012345678).',
            'account_number.digits' => 'The account number must be exactly 10 digits.',
            'bvn.digits' => 'The BVN must be exactly 11 digits.',
        ];
    }

    public function register(): void
    {
        $invite = $this->invite;

        if (! $invite?->isActive()) {
            throw ValidationException::withMessages([
                'token' => __('This registration link is invalid, has expired, or has already been used.'),
            ]);
        }

        $data = $this->validate();

        $data = array_map(fn ($value) => $value === '' ? null : $value, $data);

        $data['amount_to_invest'] ??= 0;
        $data['client_id'] = Client::nextClientId();
        $data['created_by'] = null;
        $data['status'] = Client::STATUS_PENDING;

        $client = Client::create($data);

        if (($this->next_of_kin['name'] ?? '') !== '') {
            $client->nextOfKin()->create($this->next_of_kin);
        }

        Notification::send(User::all(), new ClientRegisteredNotification($client));

        $invite->update(['used_at' => now()]);

        $this->registered = true;
        $this->registeredId = $client->client_id;
    }

    public function with(): array
    {
        return [
            'states' => State::with('lgas')->orderBy('name')->get(),
            'invite' => $this->invite,
        ];
    }
}; ?>

<div>
    @if ($registered)
        <div class="py-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">Registration Successful</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Thank you, your client ID is
                <span class="font-semibold text-brand-600 dark:text-brand-400">{{ $registeredId }}</span>.
            </p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Our team will be in touch shortly to complete your investment setup.</p>
        </div>
    @elseif (! $invite?->isActive())
        <div class="py-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
                <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h2 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">Link Unavailable</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">This registration link is invalid, has expired, or has already been used.</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Please request a new registration link to continue.</p>
        </div>
    @else
        <div class="mb-6 text-center">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Register as a Client</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Fill in your details to begin your investment journey with us.</p>
        </div>

        <form wire:submit="register" x-data="{ submitted: false }" class="space-y-6">
            <x-input-error :messages="$errors->get('token')" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-600 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400" />
 {{-- Personal Information --}}
            <section>
                <h3 class="label">Personal Information</h3>
                <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <x-input-label for="surname" :value="__('Surname')" />
                        <x-text-input wire:model="surname" id="surname" class="mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('surname')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="first_name" :value="__('First Name')" />
                        <x-text-input wire:model="first_name" id="first_name" class="mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="middle_name" :value="__('Middle Name')" />
                        <x-text-input wire:model="middle_name" id="middle_name" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="sex" :value="__('Sex')" />
                        <select wire:model="sex" id="sex" class="input mt-1 w-full" required>
                            <option value="">Select sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                        <x-text-input wire:model="date_of_birth" id="date_of_birth" type="date" class="mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="mobile_number" :value="__('Mobile Number')" />
                        <x-text-input wire:model="mobile_number" id="mobile_number" class="mt-1 w-full" placeholder="08012345678" required />
                        <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input wire:model="email" id="email" type="email" class="mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="mother_maiden_name" :value="__('Mother\'s Maiden Name')" />
                        <x-text-input wire:model="mother_maiden_name" id="mother_maiden_name" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('mother_maiden_name')" class="mt-2" />
                    </div>
                    <div class="lg:col-span-2">
                        <x-input-label for="residential_address" :value="__('Residential Address')" />
                        <x-textarea wire:model="residential_address" id="residential_address" class="mt-1 w-full"></x-textarea>
                        <x-input-error :messages="$errors->get('residential_address')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="state_of_origin_id" :value="__('State of Origin')" />
                        <select wire:model="state_of_origin_id" id="state_of_origin_id" class="input mt-1 w-full">
                            <option value="">Select state</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('state_of_origin_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="lga_name" :value="__('Local Government Area')" />
                        <x-text-input wire:model="lga_name" id="lga_name" type="text" class="mt-1 w-full" placeholder="Enter your LGA" />
                        <x-input-error :messages="$errors->get('lga_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="marital_status" :value="__('Marital Status')" />
                        <select wire:model="marital_status" id="marital_status" class="input mt-1 w-full">
                            <option value="">Select marital status</option>
                            @foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="religion" :value="__('Religion')" />
                        <select wire:model="religion" id="religion" class="input mt-1 w-full">
                            <option value="">Select religion</option>
                            @foreach (['Christianity', 'Islam', 'Traditional', 'Other'] as $religion)
                                <option value="{{ $religion }}">{{ $religion }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('religion')" class="mt-2" />
                    </div>
                </div>
            </section>

            {{-- Investment Information --}}
            <section>
                <h3 class="label">Investment Information</h3>
                <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <x-input-label for="amount_to_invest" :value="__('Amount to Invest')" />
                        <x-text-input wire:model="amount_to_invest" id="amount_to_invest" type="number" step="0.01" min="0" class="mt-1 w-full" placeholder="0.00" />
                        <x-input-error :messages="$errors->get('amount_to_invest')" class="mt-2" />
                    </div>
                </div>
            </section>

            {{-- Banking Information --}}
            <section>
                <h3 class="label">Banking Information</h3>
                <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <x-input-label for="bank_name" :value="__('Bank Name')" />
                        <x-text-input wire:model="bank_name" id="bank_name" type="text" class="mt-1 w-full" placeholder="Enter your bank" />
                        <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="account_name" :value="__('Account Name')" />
                        <x-text-input wire:model="account_name" id="account_name" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('account_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="account_number" :value="__('Account Number')" />
                        <x-text-input wire:model="account_number" id="account_number" class="mt-1 w-full" maxlength="10" placeholder="10 digits" />
                        <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="account_type" :value="__('Account Type')" />
                        <select wire:model="account_type" id="account_type" class="input mt-1 w-full">
                            <option value="">Select account type</option>
                            @foreach (['Savings', 'Current', 'Domiciliary'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="bvn" :value="__('BVN')" />
                        <x-text-input wire:model="bvn" id="bvn" class="mt-1 w-full" maxlength="11" placeholder="11 digits" />
                        <x-input-error :messages="$errors->get('bvn')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="account_opening_date" :value="__('Date of Account Opening')" />
                        <x-text-input wire:model="account_opening_date" id="account_opening_date" type="date" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('account_opening_date')" class="mt-2" />
                    </div>
                    <div class="lg:col-span-3">
                        <x-input-label for="bank_address" :value="__('Bank Address')" />
                        <x-textarea wire:model="bank_address" id="bank_address" class="mt-1 w-full"></x-textarea>
                        <x-input-error :messages="$errors->get('bank_address')" class="mt-2" />
                    </div>
                </div>
            </section>

            {{-- Employment Information --}}
            <section>
                <h3 class="label">Employment Information</h3>
                <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <x-input-label for="occupation" :value="__('Occupation')" />
                        <x-text-input wire:model="occupation" id="occupation" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="employer_name" :value="__('Employer\'s Name')" />
                        <x-text-input wire:model="employer_name" id="employer_name" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('employer_name')" class="mt-2" />
                    </div>
                    <div class="lg:col-span-3">
                        <x-input-label for="employer_address" :value="__('Employer\'s Address')" />
                        <x-textarea wire:model="employer_address" id="employer_address" class="mt-1 w-full"></x-textarea>
                        <x-input-error :messages="$errors->get('employer_address')" class="mt-2" />
                    </div>
                </div>
            </section>

            {{-- Next of Kin --}}
            <section>
                <h3 class="label">Next of Kin</h3>
                <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <x-input-label for="nok_name" :value="__('Next of Kin Name')" />
                        <x-text-input wire:model="next_of_kin.name" id="nok_name" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('next_of_kin.name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nok_phone" :value="__('Next of Kin Phone Number')" />
                        <x-text-input wire:model="next_of_kin.phone" id="nok_phone" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('next_of_kin.phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nok_email" :value="__('Next of Kin Email Address')" />
                        <x-text-input wire:model="next_of_kin.email" id="nok_email" type="email" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('next_of_kin.email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nok_relationship" :value="__('Next of Kin Relationship')" />
                        <select wire:model="next_of_kin.relationship" id="nok_relationship" class="input mt-1 w-full">
                            <option value="">Select relationship</option>
                            @foreach (['Spouse', 'Sibling', 'Parent', 'Child', 'Friend', 'Other'] as $relationship)
                                <option value="{{ $relationship }}">{{ $relationship }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('next_of_kin.relationship')" class="mt-2" />
                    </div>
                    <div class="lg:col-span-2">
                        <x-input-label for="nok_address" :value="__('Next of Kin Address')" />
                        <x-textarea wire:model="next_of_kin.address" id="nok_address" class="mt-1 w-full"></x-textarea>
                        <x-input-error :messages="$errors->get('next_of_kin.address')" class="mt-2" />
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-between gap-3 border-t border-slate-200 dark:border-slate-700 pt-5">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Your details are kept confidential and used only to set up your investment portfolio.
                </p>
                <button type="submit" class="btn-primary shrink-0 px-6" wire:loading.attr="disabled" wire:target="register">
                    <svg wire:loading wire:target="register" class="h-3.5 w-3.5 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading.remove wire:target="register">Submit Registration</span>
                    <span wire:loading wire:target="register">Submitting…</span>
                </button>
            </div>
        </form>
    @endif
</div>