<?php

namespace App\Http\Controllers\Api\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        

        return response()->json([
            'data' => Feedback::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email',
            'message'   => 'required|string',
        ]);

        $feedback = Feedback::create([
            'full_name' => $validated['full_name'],
            'email'     => $validated['email'],
            'message'   => $validated['message'],
            'ip_address'=> $request->ip(),
            'status'    => 'pending',
        ]);

        return response()->json([
            'data' => $feedback,
        ], 201);
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|nullable',
        ]);

        $feedback->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['status'] === 'rejected'
                ? $validated['rejection_reason']
                : null,
        ]);

        return response()->json([
            'data' => $feedback,
        ]);
    }
}
