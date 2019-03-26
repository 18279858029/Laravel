<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
class SessionsController extends Controller
{
   
   public function __construct()
   {
   	  $this -> middleware('guest',[
   	  		'only' => ['create']
   	  	]);
   }

   public function create()
   {
   	  return view('sessions.create');
   }

   public function store(Request $request)
   {
   	  $credentials = $this -> validate($request,[
   	  		'email' => 'required|email|max:255',
   	  		'password' => 'required',
   	  	]);
   	  if (Auth::attempt($credentials)){
   	  		session()->flash('success','欢迎回来');
   	  		return redirect()->intended(route('users.show', [Auth::user()]));
   	  } else {
   	  	    session()->flash('danger','您的邮箱或密码不正确！');
   	  	    return redirect()->back();
   	  }
   }

   public function destroy()
   {
       Auth::logout();
       session()->flash('success','您已成功退出！');
       return redirect('login');
   }
}
