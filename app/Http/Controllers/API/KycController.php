<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    public function submit(Request $request)
    {
        $user = $request->user();

        if ($user->kyc_status === 'verified' || $user->kyc_status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'KYC is already pending or verified.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'citizenship_number'      => 'required|string|max:50',
            'citizenship_front_image' => 'required|image|max:5120', // 5MB max
            'citizenship_back_image'  => 'required|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Handle file uploads
        if ($request->hasFile('citizenship_front_image')) {
            if ($user->citizenship_front_image) {
                Storage::disk('public')->delete($user->citizenship_front_image);
            }
            $user->citizenship_front_image = $request->file('citizenship_front_image')->store('kyc', 'public');
        }

        if ($request->hasFile('citizenship_back_image')) {
            if ($user->citizenship_back_image) {
                Storage::disk('public')->delete($user->citizenship_back_image);
            }
            $user->citizenship_back_image = $request->file('citizenship_back_image')->store('kyc', 'public');
        }

        $user->citizenship_number = $request->citizenship_number;
        $user->kyc_status = 'pending';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'KYC documents submitted successfully. Please wait for admin approval.',
            'data'    => [
                'kyc_status' => $user->kyc_status,
                'citizenship_number' => $user->citizenship_number,
            ]
        ]);
    }

    public function status(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data'    => [
                'kyc_status'           => $user->kyc_status,
                'citizenship_number'   => $user->citizenship_number,
                'kyc_rejection_reason' => $user->kyc_rejection_reason,
                'kyc_verified_at'      => $user->kyc_verified_at,
            ]
        ]);
    }
}
