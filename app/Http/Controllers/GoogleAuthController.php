<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Redirect to Google OAuth page.
     */
    public function redirectToGoogle()
    {
        $authUrl = $this->googleService->getClient()->createAuthUrl();
        return redirect()->away($authUrl);
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            Log::error('Google OAuth Error: ' . $request->get('error'));
            return redirect()->route('profile.edit')->with('error', 'Gagal menghubungkan Google Calendar.');
        }

        $code = $request->get('code');
        $token = $this->googleService->getClient()->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            Log::error('Google OAuth Token Error: ' . json_encode($token));
            return redirect()->route('profile.edit')->with('error', 'Gagal mendapatkan token Google.');
        }

        // Fetch Google profile to get the user's Google ID
        $this->googleService->getClient()->setAccessToken($token);
        $oauth2 = new \Google\Service\Oauth2($this->googleService->getClient());
        $googleUser = $oauth2->userinfo->get();

        $user = Auth::user();
        $user->update([
            'google_id' => $googleUser->id,
            'google_token' => json_encode($token),
            'google_refresh_token' => $token['refresh_token'] ?? $user->google_refresh_token,
            'google_token_expires_at' => now()->addSeconds($token['expires_in']),
        ]);

        return redirect()->route('profile.edit')->with('status', 'Google Calendar berhasil terhubung!');
    }
}
