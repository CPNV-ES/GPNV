<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Memberships;
use Auth;
use Illuminate\Support\Facades\Input;
use Storage;
use Validator;
use DB;

use App\Http\Requests;

class UserController extends Controller
{
    /**
    * Return the user
    * @param $user The user item
    * @param $request Define the request data send by POST
    * @return view to see user
    */
    public function show(User $user) {
        return view('user.show', compact('user'));//['user' => $user]);
    }

    public function storeAvatar(User $user, Request $request)
    {
        $file = Input::file('avatar');

        $destinationPath = 'avatar/';

        $fileArray = array('image' => $file);

        // Define the extension accepted and the max size
        $rules = array(
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
        );

        $validator = Validator::make($fileArray, $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->getMessages()], 400);
        } else { // Add the avatar
            $extension = $file->getClientOriginalExtension();
            $fileName = md5(date('YmdHis') . rand(11111, 99999)) . '.' . $extension;
            $file->move($destinationPath, $fileName);
            $user->update(['avatar' => $fileName]);
        };

        return redirect()->route("user.show", ['id' => Auth::user()->id]);
    }

    public function search($name)
    {
      if(isset($name)) {
      $users = array('users_listing' => User::search($name));
      return $users;
      }
      else {
        return "no results";
      }
    }
}
