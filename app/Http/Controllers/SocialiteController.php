<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{

    /**
     * Description: this function will redirect to Google
     * will use Socialite facade to redirect
     * @param NA
     * @reutrn void
     */

    public function googleLogin()
    {
        // return 'inside google login';
        //   dd(Socialite::driver('google')->);
        return Socialite::driver('google')->redirect();
    }


    /**
     * Description: this gets executed when google redirects the user after successful authentication
     * 
     */
    public function googleAuthnetication()
    {

        try {
            DB::beginTransaction();
            $googleUser =  Socialite::driver('google')->user();   // capture the authenticated user

            // create or update the table   
            $user = User::updateOrCreate(
                [
                    'google_id' => $googleUser->id
                ],
                [
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'avatar' => $googleUser->avatar
                ]
            );
            // return $user;
            if (!$user) {
                return 'Unable to log in from your Google account. Pleae try again';
            }
            DB::commit();
            // create token
            $token = $user->createToken($user->name);
            return redirect('http://localhost:5173?token='. $token->plainTextToken);

        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
