<?php

namespace App\Listeners;

use \Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Http\Middleware\SamlAuth;
use App\Models\StudentClass;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class LoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Saml2LoginEvent  $event
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {

        $user = $event->getSaml2User();



        $userData = [
            'id' => $user->getUserId(),
            'attributes' => $user->getAttributes(),
            'assertion' => $user->getRawSamlAssertion(),
            'sessionIndex' => $user->getSessionIndex(),
            'nameId' => $user->getNameId(),
        ];

		//check if email already exists and fetch user
		$user = User::where('mail', $userData['attributes']['mail'][0])->first();

		//if email doesn't exist, create new user
		if($user === null)
        {
            $user = new User();
            $user->firstname = $userData['attributes']['givenName'][0];
            $user->lastname = $userData['attributes']['sn'][0];
            $user->mail = $userData['attributes']['mail'][0];
            $user->password = bcrypt(str_random(8));
            $user->role_id = 1;
            $user->friendlyid = 1;
            $user->class_id = 1;
            $user->state_id = 1;
            $user->avatar = "default.png";
            $user->save();
        }

        //insert sessionIndex and nameId into session
        session(['sessionIndex' => $userData['sessionIndex']]);
        session(['nameId' => $userData['nameId']]);

		//login user



        \Auth::login($user);
    }
}
