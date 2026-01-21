<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Laragear\Webauthn\Models\WebauthnCredential;

class WebauthnCredentialRepository
{
    public function getByUser(User $user)
    {
        return WebauthnCredential::where('user_id', $user->id)->get();
    }

    public function findForUser(User $user, string $id): ?WebauthnCredential
    {
        return WebauthnCredential::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
    }

    public function delete(WebauthnCredential $credential): void
    {
        $credential->delete();
    }

    public function deleteAllForUser(User $user): void
    {
        WebauthnCredential::where('user_id', $user->id)->delete();
    }
}
