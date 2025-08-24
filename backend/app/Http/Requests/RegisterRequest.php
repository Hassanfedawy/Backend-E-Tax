<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[\p{Arabic}\s]+$/u' // only Arabic letters and spaces
        ],
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'profile_picture' => 'nullable|image|max:5120', // optional, max 5MB
        'national_id' => [
            'required',
            'string',
            'unique:users,national_id',
            'regex:/^[23][0-9]{13}$/'
, // must start with 2 or 3 + 13 digits
        ],
        'national_id_image' => 'required|image|max:5120',
    ];
            
    }
    
   public function messages(): array
    {
        return [
           'name.regex' => 'Please enter the name in Arabic characters only.',
            'email.unique' => 'This email already exists.',
            'national_id.regex' => 'National ID must start with 2 or 3 and be 14 digits long.',
            'national_id.unique' => 'This national ID is already registered.',
            'national_id_image.required' => 'The national ID image is required.',
        ];
    }
}
