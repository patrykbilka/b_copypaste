<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|string',
            'regulations' => 'accepted'
        ];
    }

    public function messages() {
        return [
            'name.required' => 'Podaj nazwę użytkownika',
            'name.unique' => 'Nazwa użytkownika jest już w użyciu.',
            'email.required' => 'Podaj email.',
            'email.email' => 'Niepoprawny email.',
            'email.unique' => 'Email w użyciu.',
            'password.required' => 'Podaj hasło.',
            'password.confirmed' => 'Hasła nie pasują.',
            'regulations.accepted' => 'Akceptuj regulamin.',
        ];
    }
}
