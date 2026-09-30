@php
    $value = fn ($field) => old($field, $client->{$field} ?? '');
    $dateValue = fn ($field) => old($field, $client->{$field} ? ($client->{$field} instanceof \Illuminate\Support\Carbon ? $client->{$field}->format('Y-m-d') : $client->{$field}) : '');
    $selected = fn ($field, $value) => old($field, $client->{$field} ?? '') == $value ? 'selected' : '';
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" x-data="{ docUrl: null }" @preview-document.window="docUrl = $event.detail.url">
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
                    <x-text-input id="middle_name" name="middle_name" value="{{ $value('middle_name') }}" class="mt-1 w-full" />
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

                <div>
                    <x-input-label for="state_of_origin_id" :value="__('State of Origin')" />
                    <select id="state_of_origin_id" name="state_of_origin_id" class="input mt-1 w-full">
                        <option value="">Select state</option>
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}" {{ $selected('state_of_origin_id', $state->id) }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('state_of_origin_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="lga_name" :value="__('Local Government Area')" />
                    <x-text-input id="lga_name" name="lga_name" value="{{ $value('lga_name') }}" class="mt-1 w-full" placeholder="Enter your LGA" />
                    <x-input-error :messages="$errors->get('lga_name')" class="mt-2" />
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
                    <x-input-label for="bank_name" :value="__('Bank Name')" />
                    <x-text-input id="bank_name" name="bank_name" value="{{ $value('bank_name') }}" class="mt-1 w-full" placeholder="Enter your bank" />
                    <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
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

    {{-- Portfolio Access --}}
    <div class="card mb-6">
        <div class="card-header">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Portfolio Access</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Create an access code the client must enter to view their portfolio through a secure link. Leave blank to disable access.</p>
            </div>
        </div>
        <div class="card-body">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <x-input-label for="portfolio_access_code" :value="__('Access Code')" />
                    <x-text-input id="portfolio_access_code" name="portfolio_access_code" value="{{ $value('portfolio_access_code') }}" class="mt-1 w-full" minlength="4" maxlength="20" autocomplete="off" placeholder="e.g. 4821-YB" />
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">4–20 characters. Share it with the client together with their link.</p>
                    <x-input-error :messages="$errors->get('portfolio_access_code')" class="mt-2" />
                </div>
            </div>

            @if ($client->exists && $client->hasPortfolioAccess())
                @include('clients._portfolio-access', ['client' => $client])
            @endif
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
            <div class="grid min-w-0 gap-4 lg:grid-cols-3">
                @foreach (\App\Models\Document::TYPES as $type => $label)
                    <div x-data="{
                            dragging: false,
                            fileName: '',
                            select(event) {
                                this.setFile(event.target.files[0] ?? null);
                            },
                            setFile(file) {
                                this.fileName = file ? file.name : '';
                            },
                            clear() {
                                this.$refs.input.value = '';
                                this.setFile(null);
                            },
                            handleDrop(event, inputId) {
                                this.dragging = false;
                                if (event.dataTransfer.files.length) {
                                    const input = document.getElementById(inputId);
                                    input.files = event.dataTransfer.files;
                                    input.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                            }
                        }" class="min-w-0 space-y-3">
                        <p class="label !mb-1">{{ $label }}</p>

                        @php($doc = $client->documents->firstWhere('type', $type))

                        @if ($doc)
                            <div class="flex flex-wrap items-center gap-3 rounded-lg border border-slate-200 dark:border-slate-700 p-3">
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-100">{{ $doc->original_name }}</p>
                                        <p class="text-xs text-slate-500">{{ number_format($doc->size / 1024, 1) }} KB</p>
                                    </div>
                                </div>
                                <div class="flex w-full shrink-0 items-center justify-end gap-1 sm:w-auto">
                                    @if ($doc->is_image)
                                        <button type="button"
                                                @click="$dispatch('preview-document', { url: @js(asset('storage/'.$doc->stored_path)) })"
                                                class="btn-ghost btn-sm" title="Preview">
                                            Preview
                                        </button>
                                    @else
                                        <a href="{{ route('clients.documents.download', [$client, $doc]) }}"
                                           class="btn-ghost btn-sm" title="Download">
                                            Download
                                        </a>
                                    @endif
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
                            </div>
                        @endif

                        <template x-if="fileName">
                            <div class="flex flex-wrap items-center gap-3 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-3">
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-100" x-text="fileName"></p>
                                        <p class="text-xs text-emerald-600 dark:text-emerald-400">Selected — save to upload.</p>
                                    </div>
                                </div>
                                <button type="button" class="btn-ghost btn-sm w-full shrink-0 justify-center text-red-600 hover:text-red-700 dark:text-red-400 sm:w-auto" @click="clear()">Remove</button>
                            </div>
                        </template>

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
                        <input id="document-{{ $type }}" x-ref="input" type="file" name="documents[{{ $type }}]" accept=".pdf,.jpg,.jpeg,.png" class="sr-only" x-on:change="select($event)">
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
    <div x-show="docUrl"
         x-cloak
         x-transition:opacity
         class="fixed inset-0 z-[95] flex items-center justify-center p-4"
         @keydown.escape.window="docUrl = null">
        <div class="fixed inset-0 bg-slate-900/70" @click="docUrl = null"></div>
        <div class="relative w-full max-w-2xl rounded-xl bg-white dark:bg-slate-800 p-4 shadow-2xl">
            <button type="button" class="absolute -top-3 -right-3 rounded-full bg-slate-800 p-1.5 text-white" @click="docUrl = null">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <img :src="docUrl" alt="Document preview" class="max-h-[70vh] w-full rounded-lg object-contain bg-slate-100 dark:bg-slate-900" />
        </div>
    </div>
</form>