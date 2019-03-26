<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;
class UsersController extends Controller
{
	public function __construct()
	{
		$this -> middleware('auth',[
			'expect' => ['show','create','store']
			]);
		$this -> middleware('guest',[
			'only' => ['create']
			]);
	}
    public function create()
    {
    	return view('users.create');
    }

    public function show(User $user)
    {
    	return view('users.show',compact('user'));
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
             	'name' => 'required|max:50',
             	'email' => 'required|email|unique:users|max:255',
             	'password' => 'required|confirmed|min:3'
    		]);
    	$user = User::create([
 				'name' => $request->name,
 				'email' => $request->email,
 				'password' => bcrypt($request->password),
    		]);
    	Auth::login($user);
    	session()->flash('success','欢迎注册，您将开启一个新的旅程！！~');
    	return redirect()->route('users.show',[$user]);
    }

    public function edit(User $user)
    {
    	$this->authorize('update', $user);
    	return view('users.edit',compact('user'));
    }

    public function update(User $user,Request $request)
    {
       	$this -> validate($request,[
       		'name' => 'required|max:50',
       		'password' => 'required|confirmed|min:3',	
       		]);
       	$this->authorize('update', $user);
       	$data = [];
       	$data['name'] = $request->name;
       	if ($request->password){
       		$data['password'] = bcrypt($request->password);
       	}
       	$user->update($data);

       	session()->flash('success','修改成功');
       	return redirect()->route('users.show',$user->id);
    }
}
