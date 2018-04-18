<?php

namespace App\Http\Controllers;

#use Illuminate\Http\Request;
use Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class SessionController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Header = Request::header('X-Forwarded-User');
        if($Header!=''){
          $user = User::where('friendlyid', '=', $Header)->first();
          if($user){
              Auth::login($user);
              return redirect()->route('project.index');
          }
        }
        return view('auth/nologin');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateLogin($request);

        $username = $request->input('email');
        $password = $request->input('password');
        $user = User::where('mail', '=', $username)->first();

        // Verify the password and the login are correct
        if($user){
            if(Hash::check($password,$user->password)) {
                Auth::login($user);
                return redirect()->route('project.index');
            }else{ // return a error message
                echo "Erreur de login";
            }
        }
    }

    public function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        //Auth::login(User::first());
        //return redirect()->route('home');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    public function noLogin(){
        if(env("UTILISATION", "SERVER") == "LOCAL") {
            $user = User::first();
            \Auth::login($user);
            return redirect()->route('home');
        }
        return view('auth.nologin');
    }
}
