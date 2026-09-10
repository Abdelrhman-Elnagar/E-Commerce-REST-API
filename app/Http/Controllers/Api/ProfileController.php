<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //update profile
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'user' => new UserResource($user->fresh()),
            ],
        ]);
    }

    //change password
    public function changePassword(ChangePasswordRequest $request)
{
    $user = $request->user();

    $user->update([
        'password' => $request->validated('password'),
    ]);

    $user->tokens()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Password changed successfully. Please login again.',
        //forward to login page
    ]);
}
}
