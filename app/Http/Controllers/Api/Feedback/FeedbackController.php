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
        $superadmin = Auth::user();
        if(!$superadmin->role === "superadmin"){
            abort(403, 'Unauthorized action.');
        }
        $feedbacks = Feedback::all();
        return response()->json([
            'feedbacks' => $feedbacks,
            'message' => 'Feedbacks retrieved successfully.'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $feedback = Feedback::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'status' => 'pending',
        ]);

        return response()->json([
            'feedback' => $feedback,
            'message' => 'Feedback created successfully.'
        ], 201);
    }


    public function showUserFeedback()
    {
        $feedback = Feedback::where('status',  'accepted')->first();
        return response()->json([
            'feedback' => $feedback,
            'message' => 'Feedback retrieved successfully.'
        ]);
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $feedback->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'feedback' => $feedback,
            'message' => 'Feedback status updated successfully.'
        ]);
    }

}
