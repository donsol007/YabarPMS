<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can($this->route('client') ? 'edit clients' : 'create clients');
    }

    public function rules(): array
    {
        $clientId = $this->route('client')?->id;

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
            'email' => ['required', 'email', 'max:191', 'unique:clients,email,'.($clientId ?? 'NULL')],
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

            'portfolio_access_code' => ['nullable', 'string', 'min:4', 'max:20'],

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
}
