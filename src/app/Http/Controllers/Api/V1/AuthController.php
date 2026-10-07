<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Socialite;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
          $start = microtime(true);
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        $afterUser = microtime(true);
        $user->sendEmailVerificationNotification();
       $afterNotification = microtime(true);
        return response()->json([
            'message' => 'Registration successful. Please verify your email before logging in.',
             'debug' => [
            'user_creation' => round($afterUser - $start, 3),
            'notification_dispatch' => round($afterNotification - $afterUser, 3),
            'total' => round($afterNotification - $start, 3),
        ],
        ], 201);
    }

    public function verifyEmail(Request $request, int $id, string $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            $hash
        )) {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email is already verified.',
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Email verified successfully.',
        ]);
    }

    public function resendVerificationEmail(ResendVerificationRequest $request ): JsonResponse {

        $user = User::where('email', $request->validated('email'))->first();
        if (! $user) {
            return response()->json([
                'message' => 'If an account exists with this email, a verification email has been sent.',
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Your email address is already verified.',
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'A new verification email has been sent.',
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], 401);
        }

        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();

            return response()->json([
                'message' => 'Please verify your email address before logging in.',
            ], 403);
        }

        $token = $user->createToken('taskflow-api')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }
    public function changePassword(ChangePasswordRequest $request): JsonResponse {
        $user = $request->user();
        $user->update([
            'password' => $request->validated('password'),
        ]);

        $user->tokens()->delete();
        $user->notify(new PasswordChangedNotification());

        return response()->json([
            'message' => 'Password changed successfully. Please log in again.',
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse {
        $status = Password::sendResetLink(
            $request->validated()
        );

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Please wait before requesting another password reset link.',
            ], 429);
        }

        return response()->json([
            'message' => 'If an account exists with this email, a password reset link has been sent.',
        ]);
    }

     public function resetPassword( ResetPasswordRequest $request): JsonResponse {
        $resetUser = null;

        $status = Password::reset(
            $request->validated(),
            function ($user, $password) use (&$resetUser) {
                $resetUser = $user;

                $user->update([
                    'password' => $password,
                ]);

                // Revoke all existing Sanctum tokens.
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Unable to reset password.',
            ], 422);
        }

        // Send security notification only after a successful reset.
        $resetUser->notify(new PasswordChangedNotification());

        return response()->json([
            'message' => 'Password reset successfully. Please log in again.',
        ]);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function handleGoogleCallback(): JsonResponse
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'password' => null,
            ]);
        } else {
            $user->update([
                'google_id' => $googleUser->getId(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

        $token = $user->createToken('taskflow-api')->plainTextToken;

        return response()->json([
            'message' => 'Google login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }
}
