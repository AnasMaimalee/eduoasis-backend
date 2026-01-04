<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminAccountCreated;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class MeController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'state' => 'required|string',
            'password' => 'sometimes|string|min:6'
        ]);

        $password = $request->password ?? Str::random(8);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'state' => $request->state,
            'password' => bcrypt($password),
        ]);

        // assign default role
        $user->assignRole('user');

        return response()->json([
            'message' => 'User registered',
            'user' => $user
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'token' => $token,
            'me' => $this->formatUser($user)
        ]);
    }

    // CREATE ADMINISTRATOR
    public function createAdministrator(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'state' => 'required|string',
        ]);

        $password = Str::random(8);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'state' => $request->state,
            'password' => bcrypt($password),
        ]);

        $user->assignRole('administrator');

        // Send email notification
        Mail::to($user->email)->send(new AdminAccountCreated($user, $password));

        return response()->json([
            'message' => 'Administrator created',
            'user' => $this->formatUser($user)
        ]);
    }

    // ME endpoint
    public function me()
    {
        $user = auth()->user();
        return response()->json($this->formatUser($user));
    }

    // Format user data for API
    private function formatUser(User $user)
    {
        $user->load(['wallet']);

        return [
            'user' => $user,
            'role' => $user->getRoleNames()->first(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'wallet' => [
                'balance' => $user->wallet?->balance ?? 0,
            ],
            'menus' => $this->getMenusForUser($user),
        ];
    }


    // Define menu items per role
    private function getMenusForUser(User $user)
    {
        $menus = [
            'user' => [
                ['name' => 'Home', 'route' => '/'],
                ['name' => 'OLevel Upload', 'route' => '/olevel'],
                ['name' => 'JAMB Service', 'route' => '/services'],
            ],
            'administrator' => [
                ['name' => 'Dashboard', 'route' => '/admin/dashboard'],
                ['name' => 'Pending Jobs', 'route' => '/admin/jobs'],
            ],
            'superadmin' => [
                ['name' => 'Dashboard', 'route' => '/super/dashboard'],
                ['name' => 'Manage Admins', 'route' => '/super/admins'],
                ['name' => 'Pricing', 'route' => '/super/pricing'],
            ]
        ];

        return $menus[$user->getRoleNames()->first()] ?? [];
    }
}
