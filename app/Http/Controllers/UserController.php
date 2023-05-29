<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponseJson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request) {
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        $user = User::where('email', $request->email)->get();

        if (!count($user)) {
            $err = ['message' => 'No user with that email found!',
            'errors' => [
                'email' => 'Invalid email!'
            ]];
            return response(json_encode($err), 404)
            ->header('Content-Type', 'application/json');
        }

        $correctPassword = Hash::check($request->password, $user[0]->password);

        if (! $correctPassword) {
            $err = ['message' => 'incorrect password',
            'errors' => [
                'password' => 'Incorrect password.'
            ]];
            return response(json_encode($err), 404)
            ->header('Content-Type', 'application/json');
        }
        
        $userForToken = User::find($user[0]->id);

        $token = $userForToken->createToken('user_token')->plainTextToken;

        $request->session()->regenerate();

        return response([
            'data' => [
                'user_name' => $user[0]->name,
                'user_id' => $user[0]->id,
                'token' => $token,
                'is_admin' => (bool) $user[0]->is_admin
            ]
        ], 200)->header('Content-Type', 'application/json');
    }

    public function create(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);
        
        $hashedPassword = bcrypt($request->password);

        DB::insert("
            INSERT INTO users (name, password, email)
            VALUES (?, ?, ?)
        ", [$request->name, $hashedPassword, $request->email]);

        return response(['message' => 'Success'], 201)
        ->header('Content-Type', 'application/json');
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }

    public function makeAdmin(Request $request) {

        $request->validate([
            'user_id' => 'required',
            "make_admin_password" => 'required',
            "is_admin" => "required|boolean"
        ]);

        $correctPassword = $request->make_admin_password === env('MAKE_ADMIN_PASSWORD');

        if ($correctPassword) {
            DB::update("
                UPDATE users
                SET is_admin = ?
                WHERE id = ?
            ", [$request->is_admin, $request->user_id]);

            return ['message' => 'success'];
        }

        return ['message' => 'unauthorized'];
    }
}
