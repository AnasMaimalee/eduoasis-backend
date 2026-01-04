<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminAccountCreated;

class AuthService
{
    public function __construct(protected UserRepository $userRepo) {}

// Register user
    public function registerUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'user';
        return $this->userRepo->create($data);
    }

// Login
    public function loginUser(array $data)
    {
        $user = $this->userRepo->findByEmail($data['email']);
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'token' => $token,
            'me'    => $this->mePayload($user),
        ];
    }

// Create administrator
    public function createAdministrator(array $data)
    {
        $password = Str::random(12);
        $data['password'] = Hash::make($password);
        $data['role'] = 'administrator';

        $admin = $this->userRepo->create($data);

// Send email
        Mail::to($admin->email)->send(new AdminAccountCreated($admin, $password));

        return $admin;
    }

// Me payload (user + roles + permissions + menus)
    public function mePayload($user): array
    {
        return [
            'user'        => $user,
            'role'        => $user->role,
            'permissions' => $this->permissions($user->role),
            'menus'       => $this->menus($user->role),
        ];
    }

    private function permissions(string $role): array
    {
        return match ($role) {
            'superadmin' => ['*'],
            'administrator' => [
                'pick_job',
                'upload_result',
                'request_withdrawal',
            ],
            default => [
                'submit_job',
                'download_result',
            ],
        };
    }

    private function menus(string $role): array
    {
        return match ($role) {
            'superadmin' => [
                ['name' => 'Dashboard', 'route' => '/admin'],
                ['name' => 'Admins', 'route' => '/admin/admins'],
                ['name' => 'Approvals', 'route' => '/admin/approvals'],
            ],
            'administrator' => [
                ['name' => 'Dashboard', 'route' => '/admin'],
                ['name' => 'Jobs', 'route' => '/admin/jobs'],
                ['name' => 'Wallet', 'route' => '/admin/wallet'],
            ],
            default => [
                ['name' => 'Home', 'route' => '/'],
                ['name' => 'OLevel Upload', 'route' => '/olevel'],
                ['name' => 'JAMB Service', 'route' => '/services'],
            ],
        };
    }
}
