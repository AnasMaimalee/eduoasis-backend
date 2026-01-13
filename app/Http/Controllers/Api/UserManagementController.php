<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    public function __construct(
        protected UserManagementService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->service->ensureSuperadmin();

        $users = $this->service->getUsers(
            $request->query('search'),
            $request->query('role'),
            $request->boolean('trashed', false),
            $request->get('per_page', 20)
        );

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data'    => $users,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->service->ensureSuperadmin();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'state' => 'required|string',
            'role'  => 'required|in:user,administrator',
        ]);

        $user = $this->service->createUser($validated);

        return response()->json([
            'message' => ucfirst($validated['role']) . ' created. Password setup link sent to email.',
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'phone'  => $user->phone,
                'state'  => $user->state,
                'role'   => $user->roles->pluck('name')->first(),
                'wallet' => $user->wallet,
            ],
        ], 201);
    }

    public function show(string $userId): JsonResponse
    {
        $this->service->ensureSuperadmin();

        return response()->json([
            'message' => 'User retrieved successfully',
            'data'    => $this->service->findUserById($userId),
        ]);
    }

    public function destroy(string $userId): JsonResponse
    {
        $this->service->ensureSuperadmin();

        $this->service->softDeleteUser(
            $this->service->findUserById($userId)
        );

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    public function restore(string $userId): JsonResponse
    {
        $this->service->ensureSuperadmin();

        $this->service->restoreUser($userId);

        return response()->json([
            'success' => true,
            'message' => 'User restored successfully',
        ]);
    }

    public function forceDelete(string $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'User permanently deleted',
        ]);
    }

    public function trashed(): JsonResponse
    {
        return response()->json([
            'data' => User::onlyTrashed()->get(),
        ]);
    }

    /**
     * ✅ Manually fund wallet (FIXED)
     */
    public function fundWallet(Request $request, string $userId): JsonResponse
    {
        $this->service->ensureSuperadmin();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $amount = (float) $validated['amount'];

        $this->service->manuallyCreditWallet(
            $this->service->findUserById($userId),
            $amount,
            $validated['reason'] ?? 'Manual funding by superadmin'
        );

        return response()->json([
            'success' => true,
            'message' => 'Wallet funded successfully',
            'amount'  => $amount,
        ], 200);
    }

    /**
     * ✅ Manually debit wallet (FIXED)
     */
    public function debitWallet(Request $request, string $userId): JsonResponse
    {
        $this->service->ensureSuperadmin();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $amount = (float) $validated['amount'];

        $this->service->manuallyDebitWallet(
            $this->service->findUserById($userId),
            $amount,
            $validated['reason'] ?? 'Manual debit by superadmin'
        );

        return response()->json([
            'success' => true,
            'message' => 'Your work has been successfully submitted.',
            'amount'  => $amount,
        ], 200);
    }

    public function transactions(string $userId): JsonResponse
    {
        $this->service->ensureSuperadmin();

        return response()->json(
            $this->service->getWalletTransactions($userId)
        );
    }
}
