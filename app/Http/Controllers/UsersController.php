<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;
use Mail;
class UsersController extends Controller
{
	public function __construct()
	{
		$this -> middleware('auth',[
			'except' => ['show','create','store','index','confirmEmail']
			]);
		$this -> middleware('guest',[
			'only' => ['create']
			]);
	}

    //用户列表页
	public function index()
	{
 		$users = User::paginate(10);
 		return view('users.index',compact('users'));
	}

	//注册页
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
      $this->sendEmailConfirmationTo($user);
      session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
      return redirect('/');
    	Auth::login($user);
    	session()->flash('success','欢迎注册，您将开启一个新的旅程！！~');
    	return redirect()->route('users.show',[$user]);
    }
    
    //发送邮件
    public function sendEmailConfirmationTo($user)
    {
        // var_dump(333);exit;
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = '感谢注册 Sample 应用！请确认你的邮箱。';

        Mail::send($view,$data,function ($message) use ($to,$subject) {
          $message->to($to)->subject($subject);
        });
    }

    //激活功能
      public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
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

    //删除用户、
    public function destroy(User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','删除成功');
        return back();
    }
}
