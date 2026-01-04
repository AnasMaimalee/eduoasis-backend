<?php

namespace App\Services\JambResult;

use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletDebited;
use App\Mail\JambResultServiceCompletedMail;
use App\Mail\JambResultServiceRejectionMail;
use App\Repositories\JambResult\JambResultRepository;
use App\Services\WalletService;


class JambResultService
{
    public function __construct(
        protected JambResultRepository $repo,
        protected WalletService $walletService
    ) {}

    /**
     * USER REQUESTS
     */
    public function my(User $user)
    {
        return $this->repo->userRequests($user->id);
    }

    public function submit(User $user, array $data)
    {
        $service = Service::where('active', true)
            ->whereRaw('LOWER(name) = ?', [strtolower('Jamb Original Result')])
            ->firstOrFail();

        // ✅ Check if user has enough balance
        if ($user->wallet->balance < $service->customer_price) {
            abort(422, 'Insufficient wallet balance');
        }

        return DB::transaction(function () use ($user, $data, $service) {

            // 1️⃣ Debit user immediately
            $this->walletService->debit(
                $user,
                $service->customer_price,
                'JAMB Original Result submission'
            );

            // 2️⃣ Create request
            $request = $this->repo->create([
                'user_id'            => $user->id,
                'service_id'         => $service->id,
                'email'              => $data['email'],
                'phone_number'       => $data['phone_number'] ?? null,
                'registration_number'=> $data['registration_number'] ?? null,
                'customer_price'     => $service->customer_price,
                'admin_payout'       => $service->admin_payout,
                'status'             => 'pending',
                'is_paid'            => true, // already paid
            ]);

            // 3️⃣ Send email to user
            Mail::to($request->email)->send(
                new WalletDebited($user, $service->customer_price, $user->wallet->balance)
            );

            return $request;
        });
    }

    /**
     * ADMIN TAKES JOB
     */
    public function take(string $id, User $admin)
    {
        $job = $this->repo->find($id);

        if ($job->status !== 'pending') {
            abort(422, 'Job already taken');
        }

        $job->update([
            'status'   => 'processing',
            'taken_by' => $admin->id,
        ]);

        return $job;
    }

    /**
     * ADMIN COMPLETES JOB
     */
    /**
     * =========================
     * ADMIN COMPLETES JOB
     * =========================
     */
    public function complete(string $id, string $filePath, User $admin)
    {
        return DB::transaction(function () use ($id, $filePath, $admin) {

            $job = $this->repo->find($id);

            // ✅ Ensure admin took the job
            if ($job->taken_by !== $admin->id) {
                abort(403, 'You did not take this job');
            }

            // ✅ Prevent double completion
            if ($job->status !== 'processing') {
                abort(422, 'Job is not in processing state');
            }

            // ✅ Update job only (NO WALLET ACTION HERE)
            $job->update([
                'status'       => 'completed_by_admin',
                'result_file'  => $filePath,
                'completed_by' => $admin->id,
            ]);

            // Load relations for response & email
            $job->load(['user', 'service', 'completedBy']);

            // Send completion email to user
            Mail::to($job->email)->send(
                new JambResultServiceCompletedMail($job)
            );

            return [
                'message' => 'Job completed and awaiting approval',
                'job' => [
                    'id' => $job->id,
                    'status' => $job->status,
                    'user' => [
                        'name' => $job->user->name,
                        'email' => $job->user->email,
                    ],
                    'service' => $job->service->name,
                    'completed_by' => $job->completedBy->name,
                    'result_file_url' => asset('storage/' . $job->result_file),
                    'created_at' => $job->created_at,
                ],
            ];
        });
    }

    /**
     * =========================
     * SUPER ADMIN APPROVES JOB
     * =========================
     */
    public function approve(string $id, User $superAdmin)
    {
        return DB::transaction(function () use ($id, $superAdmin) {

            $job = $this->repo->find($id);



            // Credit admin
            $this->walletService->credit(
                $job->completedBy,
                $job->admin_payout,
                'JAMB Result service payment'
            );

            $job->update([
                'status'          => 'approved',
                'platform_profit' => $job->customer_price - $job->admin_payout,
                'approved_by'     => $superAdmin->id,
            ]);

            return [
                'message' => 'Job approved and admin paid',
                'job_id' => $job->id,
                'admin_paid' => $job->admin_payout,
                'platform_profit' => $job->platform_profit,
            ];
        });
    }

    /**
     * =========================
     * SUPER ADMIN REJECTS JOB
     * =========================
     */
    public function reject(string $id, string $reason, User $superAdmin)
    {
        return DB::transaction(function () use ($id, $reason, $superAdmin) {

            $job = $this->repo->find($id);


            // 1️⃣ Refund user from SUPER ADMIN wallet
            $this->walletService->transfer(
                $superAdmin,
                $job->user,
                $job->customer_price,
                'Refund for rejected JAMB Result service'
            );

            // 2️⃣ Update job
            $job->update([
                'status'           => 'rejected',
                'rejected_by'      => $superAdmin->id,
                'rejection_reason' => $reason,
            ]);

            // 3️⃣ Notify user
            Mail::to($job->email)->send(
                new JambResultServiceRejectionMail($job)
            );

            // 4️⃣ Notify admin (optional but recommended)
            Mail::to($job->completedBy?->email)->send(
                new JambResultServiceRejectionMail($job)
            );

            return [
                'message' => 'Job rejected and user refunded',
                'job_id' => $job->id,
                'refund_amount' => $job->customer_price,
                'reason' => $reason,
            ];
        });
    }



    /**
     * ADMIN: Pending jobs
     */
    public function pending()
    {
        return $this->repo->pendingRequests();
    }

    public function all()
    {
        return $this->repo->allWithRelations();
    }


}
