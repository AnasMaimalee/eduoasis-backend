<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\WebAuthnCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebAuthnService
{
    /**
     * Get the Relying Party ID safely.
     * - In local env: always 'localhost' (browser exception)
     * - In production/staging: extract host from config, strip 'www.', fallback to 'localhost' if invalid
     */
    private function getRpId(): string
    {
        if (app()->environment('local')) {
            return 'localhost';
        }

        $url = config('webAuthn.frontend_url', config('app.url', 'https://localhost'));

        $host = parse_url($url, PHP_URL_HOST);

        if ($host === null || $host === false || empty($host)) {
            // Fallback – should rarely hit in prod if config is set
            return 'localhost';
        }

        // Strip leading 'www.' for broader compatibility (allows www & non-www origins)
        $host = preg_replace('/^www\./i', '', $host);

        // Optional: Add more validation (e.g. reject IPs in prod)
        // if (filter_var($host, FILTER_VALIDATE_IP)) {
        //     throw new \RuntimeException("RP ID cannot be an IP address in production.");
        // }

        return $host;
    }

    /**
     * Registration options
     */
    public function registerOptions(User $user): array
    {
        $rp = [
            'name' => config('app.name', 'EduOasis'),
        ];

        // Only include 'id' if not localhost (browser defaults correctly when omitted)
        $rpId = $this->getRpId();
        if ($rpId !== 'localhost') {
            $rp['id'] = $rpId;
        }

        return [
            'rp' => $rp,
            'user' => [
                'id'          => base64_encode((string) $user->getKey()),
                'name'        => $user->email,
                'displayName' => $user->name ?? $user->email,
            ],
            'challenge' => base64_encode(random_bytes(32)),
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],
                ['type' => 'public-key', 'alg' => -257],
            ],
            'authenticatorSelection' => [
                'residentKey'     => 'preferred',
                'userVerification'=> 'discouraged',
            ],
            'timeout' => 60000,
            'attestation' => 'none',
        ];
    }

    /**
     * Save credential
     */
    public function register(Request $request): void
    {
        $user = $request->user();

        $data = $request->validate([
            'id' => 'required|string',
            'type' => 'required|in:public-key',
            'rawId' => 'required|string',
            'response' => 'required|array',
        ]);

        WebAuthnCredential::create([
            'id' => (string) Str::uuid(),
            'authenticatable_type' => User::class,
            'authenticatable_id' => $user->id,
            'credential_id' => $data['id'],
            'alias' => 'Biometric Device',
            'counter' => 0,
            'rp_id' => $this->getRpId(),  // Consistent scoping
        ]);
    }

    /**
     * Login options
     */
    public function loginOptions(): array
    {
        $rpId = $this->getRpId();

        $credentials = WebAuthnCredential::where('rp_id', $rpId)
            ->pluck('credential_id')
            ->toArray();

        $options = [
            'challenge' => base64_encode(random_bytes(32)),
            'timeout' => 60000,
            'userVerification' => 'discouraged',
            'allowCredentials' => array_map(fn ($id) => [
                'type' => 'public-key',
                'id' => $id,
            ], $credentials),
        ];

        // Only include rpId if not localhost (browser infers it)
        if ($rpId !== 'localhost') {
            $options['rpId'] = $rpId;
        }

        return $options;
    }

    /**
     * Verify login
     */
    public function login(Request $request): User
    {
        $data = $request->validate([
            'id' => 'required|string',
            'type' => 'required|in:public-key',
        ]);

        $credential = WebAuthnCredential::where('credential_id', $data['id'])
            ->where('rp_id', $this->getRpId())
            ->firstOrFail();

        $credential->increment('counter');

        return $credential->authenticatable;  // Assuming relation is 'authenticatable' (polymorphic)
    }

    /**
     * List credentials
     */
    public function credentials(User $user)
    {
        return WebAuthnCredential::where('authenticatable_id', $user->id)
            ->where('authenticatable_type', User::class)
            ->where('rp_id', $this->getRpId())
            ->latest()
            ->get();
    }

    /**
     * Delete credential
     */
    public function destroy(User $user, ?string $credentialId): bool
    {
        return WebAuthnCredential::where('authenticatable_id', $user->id)
            ->where('authenticatable_type', User::class)
            ->where('rp_id', $this->getRpId())
            ->when($credentialId, fn ($q) => $q->where('credential_id', $credentialId))
            ->delete() > 0;
    }
}