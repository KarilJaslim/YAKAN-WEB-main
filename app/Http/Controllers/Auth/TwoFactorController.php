<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA setup page
     */
    public function showSetup()
    {
        $user = Auth::user();
        
        if ($user->two_factor_secret) {
            return redirect()->route('dashboard')->with('info', '2FA is already enabled for your account.');
        }

        $secret = $this->google2fa->generateSecretKey();
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.2fa-setup', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
            'recoveryCodes' => $this->generateRecoveryCodes()
        ]);
    }

    /**
     * Enable 2FA for user
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|digits:6',
            'secret' => 'required|string',
        ]);

        $user = Auth::user();
        $secret = $request->secret;

        if (!$this->google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($this->generateRecoveryCodes())),
            'two_factor_confirmed_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Two-factor authentication has been enabled for your account.');
    }

    /**
     * Show 2FA challenge page
     */
    public function showChallenge()
    {
        return view('auth.2fa-challenge');
    }

    /**
     * Verify 2FA code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|digits:6',
        ]);

        $user = Auth::user();
        $secret = decrypt($user->two_factor_secret);

        if ($this->google2fa->verifyKey($secret, $request->code)) {
            session(['2fa_verified' => true]);
            return redirect()->intended(route('dashboard'));
        }

        // Check recovery codes
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        
        if (in_array($request->code, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_diff($recoveryCodes, [$request->code]);
            $user->update([
                'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes)))
            ]);
            
            session(['2fa_verified' => true]);
            return redirect()->intended(route('dashboard'))->with('warning', 'You used a recovery code. Consider regenerating your recovery codes.');
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'code' => 'required|string|digits:6',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        $secret = decrypt($user->two_factor_secret);

        if (!$this->google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Generate recovery codes
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        return $codes;
    }

    /**
     * Check if user has 2FA enabled
     */
    public function isEnabled(): bool
    {
        $user = Auth::user();
        return !empty($user->two_factor_secret) && !empty($user->two_factor_confirmed_at);
    }
}
