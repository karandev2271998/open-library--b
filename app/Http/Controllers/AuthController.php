<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassword;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $token = $user->createToken('my-app')->plainTextToken;

        $response = [
            'status' => true,
            'userDetail' => $user,
            'token' => $token,
            'message' => "User register successfully!"
        ];

        return response()->json($response);
    }

    public function login(Request $request)
    {
        $response = [];

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $response = [
                'status' => false,
                'userDetail' => [],
                'token' => '',
                'message' => 'Invalid Credentials!'
            ];
        } else {
            $token = $user->createToken('my-app')->plainTextToken;

            $response = [
                'status' => true,
                'userDetail' => $user,
                'token' => $token,
                'message' => 'User login successfully'
            ];
        }
        return response()->json($response);
    }

    public function forgetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $mailInfo = [
                "name" => $user->name,
                "email" => $user->email,
                "userId" => $user->id,
            ];
            Mail::to($user->email)->send(new ForgetPassword($mailInfo));
            return response()->json(['status' => true, 'message' => 'Email has been sent succfully. Please check you mail box']);
        }
        return ['status' => false, 'message' => 'User not exist in our database'];
    }

    public function updatePassword(Request $request)
    {
        try {
            $updateUserPassword = User::find($request->id)->update([
                'password' => $request->password,
            ]);
            if ($updateUserPassword) {
                return response()->json(['status' => true, 'message' => 'Password updated successfully, Please login you account']);
            }
            return ['status' => false, 'message' => 'Something went wrong'];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
