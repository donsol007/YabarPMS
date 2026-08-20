@php
    $value = fn ($field) => old($field, $client->{$field} ?? '');
    $dateValue = fn ($field) => old($field, $client->{$field} ? ($client->{$field} instanceof \Illuminate\Support\Carbon ? $client->{$field}->format('Y-m-d') : $client->{$field}) : '');
    $selected = fn ($field, $value) => old($field, $client->{$field} ?? '') == $value ? 'selected' : '';
    $lgasByState = $states->mapWithKeys(fn ($s) => [$s->id => $s->lgas->pluck('name', 'id')->all()])->all();
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" x-data="{ preview: null }">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    {{-- Personal Information --}}
    <div class="card mb-6">
        <div class="card-header">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Personal Information</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Identity and personal details of the client.</p>
            </div>
            @if ($client->client_id)
                <x-badge color="brand">{{ $client->client_id }}</x-badge>
            @endif
        </div>
        <div class="card-body">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @if ($client->client_id)
                    <div>
                        <x-input-label for="client_id" :value="__('Client ID')" />
                        <x-text-input id="client_id" value="{{ $client->client_id }}" class="mt-1 w-full bg-slate-50 dark:bg-slate-900" disabled />
                    </div>
                @endif
                <div>
                    <x-input-label for="surname" :value="__('Surname')" />
                    <x-text-input id="surname" name="surname" value="{{ $value('surname') }}" class="mt-1 w-full" required />
                    <x-input-error :messages="$errors->get('surname')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="first_name" :value="__('First Name')" />
                    <x-text-input id="first_name" name="first_name" value="{{ $value('first_name') }}" class="mt-1 w-full" required />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="middle_name" :value="__('Middle Name')" />
                    <x-text-input id="middle_name" name="middle_name" value="{{ $value('middle_name') }}" class="mt-1 w-full" required />
                    <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="sex" :value="__('Sex')" />
                    <select id="sex" name="sex" class="input mt-1 w-full" required>
                        <option value="">Select sex</option>
                        <option value="Male" {{ $selected('sex', 'Male') }}>Male</option>
                        <option value="Female" {{ $selected('sex', 'Female') }}>Female</option>
                    </select>
                    <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                    <x-text-input id="date_of_birth" type="date" name="date_of_birth" value="{{ $dateValue('date_of_birth') }}" class="mt-1 w-full" required />
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="mobile_number" :value="__('Mobile Number')" />
                    <x-text-input id="mobile_number" name="mobile_number" value="{{ $value('mobile_number') }}" class="mt-1 w-full" placeholder="08012345678" required />
                    <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="mother_maiden_name" :value="__('Mother\'s Maiden Name')" />
                    <x-text-input id="mother_maiden_name" name="mother_maiden_name" value="{{ $value('mother_maiden_name') }}" class="mt-1 w-full" />
                    <x-input-error :messages="$errors->get('mother_maiden_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="residential_address" :value="__('Residential Address')" />
                    <x-textarea id="residential_address" name="residential_address" class="mt-1 w-full">{{ $value('residential_address') }}</x-textarea>
                    <x-input-error :messages="$errors->get('residential_address')" class="mt-2" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2" x-data="lgaDropdown(@js(old('state_of_origin_id', $client->state_of_origin_id)), @js(old('lga_id', $client->lga_id)), @js($lgasByState))">
                    <div>
                        <x-input-label for="state_of_origin_id" :value="__('State of Origin')" />
                        <select id="state_of_origin_id" name="state_of_origin_id" class="input mt-1 w-full" x-on:change="onStateChange($el.value)">
                            <option value="">Select state</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}" {{ $selected('state_of_origin_id', $state->id) }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('state_of_origin_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="lga_id" :value="__('Local Government Area')" />
                        <select id="lga_id" name="lga_id" class="input mt-1 w-full">
                            <option value="">Select LGA</option>
                            <template x-for="lga in lgas" :key="lga[0]">
                                <option :value="lga[0]" :selected="String(lga[0]) === String(@js(old('lga_id', $client->lga_id)))" x-text="lga[1]"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('lga_id')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="marital_status" :value="__('Marital Status')" />
                    <select id="marital_status" name="marital_status" class="input mt-1 w-full">
                        <option value="">Select marital status</option>
                        @foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                            <option value="{{ $status }}" {{ $selected('marital_status', $status) }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="religion" :value="__('Religion')" />
                    <select id="religion" name="religion" class="input mt-1 w-full">
                        <option value="">Select religion</option>
                        @foreach (['Christianity', 'Islam', 'Traditional', 'Other'] as $religion)
                            <option value="{{ $religion }}" {{ $selected('religion', $religion) }}>{{ $religion }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('religion')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" :value="__('Email Address')" />
                    <x-text-input id="email" type="email" name="email" value="{{ $value('email') }}" class="mt-1 w-full" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    {{-- Investment Information --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Investment Information</h3>
        </div>
        <div class="card-body">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <x-input-label for="amount_to_invest" :value="__('Amount to Invest')" />
                    <x-text-input id="amount_to_invest" type="number" step="0.01" min="0" name="amount_to_invest" value="{{ $value('amount_to_invest') }}" class="mt-1 w-full" placeholder="0.00" />
                    <x-input-error :messages="$errors->get('amount_to_invest')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    {{-- Banking Information --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Banking Information</h3>
        </div>
        <div class="card-body">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <x-input-label for="bank_id" :value="__('Bank Name')" />
                    <select id="bank_id" name="bank_id" class="input mt-1 w-full">
                        <option value="">Select bank</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}" {{ $selected('bank_id', $bank->id) }}>{{ $bank->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('bank_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="account_name" :value="__('Account Name')" />
                    <x-text-input id="account_name" name="account_name" value="{{ $value('account_name') }}" class="mt-1 w-full" />
                    <x-input-error :messages="$errors->get('account_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="account_number" :value="__('Account Number')" />
                    <x-text-input id="account_number" name="account_number" value="{{ $value('account_number') }}" class="mt-1 w-full" maxlength="10" placeholder="10 digits" />
                    <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="account_type" :value="__('Account Type')" />
                    <select id="account_type" name="account_type" class="input mt-1 w-full">
                        <option value="">Select account type</option>
                        @foreach (['Savings', 'Current', 'Domiciliary'] as $type)
                            <option value="{{ $type }}" {{ $selected('account_type', $type) }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="bvn" :value="__('BVN')" />
                    <x-text-input id="bvn" name="bvn" value="{{ $value('bvn') }}" class="mt-1 w-full" maxlength="11" placeholder="11 digits" />
                    <x-input-error :messages="$errors->get('bvn')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="account_opening_date" :value="__('Date of Account Opening')" />
                    <x-text-input id="account_opening_date" type="date" name="account_opening_date" value="{{ $dateValue('account_opening_date') }}" class="mt-1 w-full" />
                    <x-input-error :messages="$errors->get('account_opening_date')" class="mt-2" />
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <x-input-label for="bank_address" :value="__('Bank Address')" />
                    <x-textarea id="bank_address" name="bank_address" class="mt-1 w-full">{{ $value('bank_address') }}</x-textarea>
                    <x-input-error :messages="$errors->get('bank_address')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    {{-- Employment Information --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Employment Information</h3>
        </div>
        <div class="card-body">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <x-input-label for="occupation" :value="__('Occupation')" />
                    <x-text-input id="occupation" name="occupation" value="{{ $value('occupation') }}" class="mt-1 w-full" />
                    <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="employer_name" :value="__('Employer\'s Name')" />
                    <x-text-input id="employer_name" name="employer_name" value="{{ $value('employer_name') }}" class="mt-1 w-full" />
                    <x-input-error :messages="$errors->get('employer_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="employer_address" :value="__('Employer\'s Address')" />
                    <x-textarea id="employer_address" name="employer_address" class="mt-1 w-full">{{ $value('employer_address') }}</x-textarea>
                    <x-input-error :messages="$errors->get('employer_address')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    {{-- Personal extras --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Additional Information</h3>
        </div>
        <div class="card-body">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <x-input-label for="hobbies" :value="__('Hobbies')" />
                    <x-textarea id="hobbies" name="hobbies" class="mt-1 w-full">{{ $value('hobbies') }}</x-textarea>
                    <x-input-error :messages="$errors->get('hobbies')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    {{-- Next of Kin --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Next of Kin</h3>
        </div>
        <div class="card-body">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <x-input-label for="next_of_kin_name" :value="__('Next of Kin Name')" />
                    <x-text-input id="next_of_kin_name" name="next_of_kin[name]" value="{{ old('next_of_kin.name', $client->nextOfKin?->name) }}" class="mt-1 w-full" />
                    <x-input-error :messages="$errors->get('next_of_kin.name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="next_of_kin_phone" :value="__('Next of Kin Phone Number')" />
                    <x-text-input id="next_of_kin_phone" name="next_of_kin[phone]" value="{{ old('next_of_kin.phone', $client->nextOfKin?->phone) }}" class="mt-1 w-full" />
                    <x-input-error :messages="$errors->get('next_of_kin.phone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="next_of_kin_email" :value="__('Next of Kin Email Address')" />
                    <x-text-input id="next_of_kin_email" type="email" name="next_of_kin[email]" value="{{ old('next_of_kin.email', $client->nextOfKin?->email) }}" class="mt-1 w-full" />
                    <x-input-error :messages="$errors->get('next_of_kin.email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="next_of_kin_relationship" :value="__('Next of Kin Relationship')" />
                    <select id="next_of_kin_relationship" name="next_of_kin[relationship]" class="input mt-1 w-full">
                        <option value="">Select relationship</option>
                        @foreach (['Spouse', 'Sibling', 'Parent', 'Child', 'Friend', 'Other'] as $relationship)
                            <option value="{{ $relationship }}" {{ old('next_of_kin.relationship', $client->nextOfKin?->relationship) == $relationship ? 'selected' : '' }}>{{ $relationship }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('next_of_kin.relationship')" class="mt-2" />
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <x-input-label for="next_of_kin_address" :value="__('Next of Kin Address')" />
                    <x-textarea id="next_of_kin_address" name="next_of_kin[address]" class="mt-1 w-full">{{ old('next_of_kin.address', $client->nextOfKin?->address) }}</x-textarea>
                    <x-input-error :messages="$errors->get('next_of_kin.address')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    {{-- Document Uploads --}}
    <div class="card mb-6">
        <div class="card-header">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Document Uploads</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">ID Card, Utility Bill and Signature. PDF, JPG or PNG up to {{ setting('upload_max_size', 5) }}MB each. Optional.</p>
            </div>
        </div>
        <div class="card-body">
            <div class="grid gap-4 lg:grid-cols-3">
                @foreach (\App\Models\Document::TYPES as $type => $label)
                    <div x-data="fileDrop" class="space-y-3">
                        <p class="label !mb-1">{{ $label }}</p>

                        @php($doc = $client->documents->firstWhere('type', $type))

                        @if ($doc)
                            <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-3 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-100">{{ $doc->original_name }}</p>
                                    <p class="text-xs text-slate-500">{{ number_format($doc->size / 1024, 1) }} KB</p>
                                </div>
                                <button type="button"
                                        x-data="filePreview"
                                        @click="open(@js($doc->is_image ? asset('storage/'.$doc->stored_path) : null)); if (!isImage) window.open('{{ route('clients.documents.download', [$client, $doc]) }}')"
                                        class="btn-ghost btn-sm" title="Preview">
                                    Preview
                                </button>
                                @can('edit clients')
                                    <x-confirm-dialog
                                        name="remove-doc-{{ $doc->id }}"
                                        title="Remove document"
                                        message="Remove this document?"
                                        confirmText="Remove"
                                        buttonText="Remove"
                                        buttonClass="btn-ghost btn-sm text-red-600"
                                        action="{{ route('clients.documents.destroy', [$client, $doc]) }}"
                                    />
                                @endcan
                            </div>
                        @endif

                        <label for="document-{{ $type }}"
                               x-on:dragover.prevent="dragging = true"
                               x-on:dragleave="dragging = false"
                               x-on:drop.prevent="handleDrop($event, 'document-{{ $type }}')"
                               class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed px-4 py-6 text-center transition"
                               :class="dragging ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20' : 'border-slate-300 dark:border-slate-700 hover:border-brand-400'">
                            <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Drop file here or <span class="text-brand-600 dark:text-brand-400">browse</span></span>
                            <span class="text-xs text-slate-400">PDF / JPG / PNG — max {{ setting('upload_max_size', 5) }}MB</span>
                        </label>
                        <input id="document-{{ $type }}" type="file" name="documents[{{ $type }}]" accept=".pdf,.jpg,.jpeg,.png" class="sr-only" x-on:change="preview = $event.target.files[0]">
                        @error("documents.{$type}")
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('clients.index') }}" class="btn-secondary" wire:navigate>Cancel</a>
        <button type="submit" class="btn-primary">
            <span>{{ $method === 'PUT' ? 'Save Changes' : 'Register Client' }}</span>
        </button>
    </div>

    {{-- File preview modal --}}
    <div x-data="filePreview"
         x-show="url"
         x-cloak
         x-transition:opacity
         class="fixed inset-0 z-[95] flex items-center justify-center p-4"
         @keydown.escape.window="url = null">
        <div class="fixed inset-0 bg-slate-900/70" @click="url = null"></div>
        <div class="relative w-full max-w-2xl rounded-xl bg-white dark:bg-slate-800 p-4 shadow-2xl">
            <button type="button" class="absolute -top-3 -right-3 rounded-full bg-slate-800 p-1.5 text-white" @click="url = null">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <template x-if="isImage">
                <img :src="url" alt="Document preview" class="max-h-[70vh] w-full rounded-lg object-contain bg-slate-100 dark:bg-slate-900" />
            </template>
            <template x-if="!isImage">
                <iframe :src="url" class="h-[70vh] w-full rounded-lg bg-slate-100 dark:bg-slate-900"></iframe>
            </template>
        </div>
    </div>
</form>