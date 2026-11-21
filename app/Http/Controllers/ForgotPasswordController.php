<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use GuzzleHttp\Client;

class ForgotPasswordController extends Controller
{
    /**
     * Step 1: Show the form to request a password reset.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 2: Find user and send OTP.
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string', // Can be email, mobile, or user_id
        ]);

        $input = $request->input('login_id');

        // Find user by Email, Mobile, or User ID
        $user = User::where('email', $input)
            ->orWhere('mobile_no', $input)
            ->orWhere('user_id', $input)
            ->first();

        if (!$user) {
            // Security: Don't reveal if user exists or not to prevent enumeration
            return back()->with('status', 'If an account exists, an OTP has been sent.');
        }

        if (!$user->mobile_no) {
            return back()->withErrors(['login_id' => 'No mobile number associated with this account. Please contact admin.']);
        }

        // Generate OTP
        $otp = mt_rand(1000, 9999);
        $user->otp_password = $otp;
        $user->save();

        // Send SMS (Refactored from your provided snippet)
        try {
            $msg = "Your OTP for password reset is " . $otp . ". Do not share this code.";
            // Simple URL encoding for the message logic
            // Note: Ideally use a dedicated Service class for SMS
            $client = new Client();
            $url = "http://206.189.158.212/EACEI/registersms.php";

            $client->request('GET', $url, [
                'query' => [
                    'mobile' => $user->mobile_no,
                    'otp' => $otp // Your API seems to take 'otp' param directly based on your example
                    // Or if it takes message text: 'message' => $msg
                ]
            ]);

            // Store the intent in session to prevent direct access to later steps
            Session::put('reset_user_id', $user->id);

            return redirect()->route('password.otp.verify', $user->id)
                ->with('success', 'OTP sent to your registered mobile number: ' . substr($user->mobile_no, 0, 3) . '*******' . substr($user->mobile_no, -2));

        } catch (\Exception $e) {
            Log::error("SMS Sending Failed: " . $e->getMessage());
            return back()->withErrors(['login_id' => 'Failed to send OTP. Please try again later.']);
        }
    }

    /**
     * Step 3: Show OTP Verification Form.
     */
    public function showOtpVerifyForm($userId)
    {
        // Ensure the user is trying to verify the correct session
        if (Session::get('reset_user_id') != $userId) {
            return redirect()->route('password.request')->withErrors(['msg' => 'Session expired. Please try again.']);
        }

        $user = User::findOrFail($userId);
        return view('auth.verify-otp', compact('user'));
    }

    /**
     * Step 4: Verify OTP.
     */
    public function verifyOtp(Request $request, $userId)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:4',
        ]);

        $user = User::findOrFail($userId);

        if ($request->otp == $user->otp_password) {
            // OTP is correct.
            // Mark session as verified to allow access to the next page (reset form)
            Session::put('otp_verified_for_user', $user->id);

            // Clear the OTP from DB to prevent reuse
            $user->otp_password = null;
            $user->save();

            return redirect()->route('password.reset', $user->id)
                ->with('success', 'OTP verified! Please set your new password.');
        }

        return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
    }

    /**
     * Step 5: Show Password Reset Form.
     */
    public function showResetForm($userId)
    {
        // Security Check: Ensure OTP was verified in this session for this user
        if (Session::get('otp_verified_for_user') != $userId) {
            return redirect()->route('password.request')->withErrors(['msg' => 'Unauthorized access. Please verify OTP first.']);
        }

        $user = User::findOrFail($userId);
        return view('auth.reset-password', compact('user'));
    }

    /**
     * Step 6: Final Password Reset.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Double check session security
        if (Session::get('otp_verified_for_user') != $request->user_id) {
            return redirect()->route('login')->withErrors(['msg' => 'Session expired.']);
        }

        $user = User::findOrFail($request->user_id);

        $user->update([
            'password' => Hash::make($request->password),
            'otp_password' => null // Ensure it's cleared
        ]);

        // Clear session
        Session::forget(['reset_user_id', 'otp_verified_for_user']);

        return redirect()->route('login')->with('success', 'Password has been reset successfully. You can now login.');
    }
}
