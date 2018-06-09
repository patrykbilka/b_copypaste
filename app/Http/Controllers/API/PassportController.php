<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\RegisterRequest;

class PassportController extends Controller
{
    public function login(Request $request) {

        $client = Client::where('password_client', 1)->first();

        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ];

        $messages = [
            'email.required' => 'Podaj email.',
            'email.email' => 'Niepoprawny email.',
            'password.required' => 'Podaj hasło.',
        ];

        $validator = validator($request->only('email', 'password'),
            $rules, $messages);

        if ($validator->fails()) {

            return response()->json(['errors' => $validator->errors()->messages()], 422);
        }

        $request->request->add([
            'grant_type' => 'password',
            'username' => $request->email,
            'password' => $request->password,
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'scope' => ''
        ]);

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        return \Route::dispatch($proxy);
    }

    public function register(Request $request)
    {
        $data = request()->only('email','name','password');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|string',
            'regulations' => 'accepted'
        ];

        $messages = [
            'name.required' => 'Podaj nazwę użytkownika',
            'name.unique' => 'Nazwa użytkownika jest już w użyciu.',
            'email.required' => 'Podaj email.',
            'email.email' => 'Niepoprawny email.',
            'email.unique' => 'Email w użyciu.',
            'password.required' => 'Podaj hasło.',
            'password.confirmed' => 'Hasła nie pasują.',
            'regulations.accepted' => 'Akceptuj regulamin.',
        ];

        $validator = validator($request->only('email', 'name', 'password', 'password_confirmation', 'regulations'),
            $rules, $messages);

        if ($validator->fails()) {

            return response()->json(['errors' => $validator->errors()->messages()], 422);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // And created user until here.

        $client = Client::where('password_client', 1)->first();

        // Is this $request the same request? I mean Request $request? Then wouldn't it mess the other $request stuff? Also how did you pass it on the $request in $proxy? Wouldn't Request::create() just create a new thing?

        $request->request->add([
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => $data['email'],
            'password'      => $data['password'],
            'scope'         => '',
        ]);

        // Fire off the internal request.
        $token = Request::create(
            'oauth/token',
            'POST'
        );
        return \Route::dispatch($token);
    }
}
