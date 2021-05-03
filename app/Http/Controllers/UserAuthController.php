<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Module\ShareData;
use App\Entity\User;
use Validator;
use Hash;
use DB;
use Mail;

class UserAuthController extends Controller
{
	public $page = "";

	public function signUpPage(){
		$name = 'sign_up';
		$binding = [
			'title' => ShareData::TITLE,
			'page' => $this->page,
			'name' => $name,
			'User' => '',
		];

		return view('user.sign-up', $binding);
	}

	public function signUpProcess(){
		$input = request()->all();

		$rules = [
			'name' => [
				'required',
				'max:50',
			],
			'account' => [
				'required',
				'max:50',
				'email',
			],
			'password' => [
				'required',
				'min:5',
			],
			'password_confirm' => [
				'required',
				'same:password',
				'min:5',
			],
		];

		$validator = Validator::make($input, $rules);

		if($validator->fails()){
			return redirect('/user/auth/sign-up')
							->withErrors($validator)
							->withInput();
		}

		$input['password'] = Hash::make($input['password']);

		// 啟用紀錄sql語法
		DB::enableQueryLog();

		// 新增使用者資料
		User::create($input);

		Log::notice(print_r(DB::getQueryLog(), true));

		$mail_binding = [
			'name' => $input['name'],
		];

		Mail::send('email.signUpEmailNotification', $mail_binding,
			function ($mail) use ($input) {
				//收件人
				$mail->to($input['account']);
				//寄件人
				$mail->from('war79511@gmail.com');
				//郵件主旨
				$mail->subject('恭喜註冊Laravel部落格成功!');
 	 });

		return redirect('/user/auth/sign-in');

		var_dump($input);
		exit;
	}

	public function signInPage(){
		$name = 'sign_in';
		$binding = [
			'title' => ShareData::TITLE,
			'page' => $this->page,
			'name' => $name,
			'User' => '',
		];

		return view('user.sign-in', $binding);
	}

	public function signInProcess(){
		$input = request()->all();

		$rules = [
			'account' => [
				'required',
				'max:50',
				'email',
			],
			'password' => [
				'required', 
				'min:5',
			],
		];

		$validator = Validator::make($input, $rules);

		if($validator->fails()){
			return redirect('/user/auth/sign-in')
						->withErrors($validator)
						->withInput();
		}

		$User = User::where('account', $input['account'])->first();

		if(!$User){
			$error_message = [
				'msg' => [
					'帳號輸入錯誤',
				],
			];

			return redirect('/user/auth/sign-in')
						->withErrors($error_message)
						->withInput();
		}

		$is_password_correct = Hash::check($input['password'], $User->password);

		if(!$is_password_correct){
			$error_message = [
				'msg' => [
					'密碼輸入錯誤',
				],
			];

			return redirect('/user/auth/sign-in')
						->withErrors($error_message)
						->withInput();
		}

		session()->put('user_id', $User->id);

		// 重新導向到原先使用者造訪頁面，沒有嘗試造訪頁則重新導向回自我介紹頁
		return redirect()->intended('/admin/user');
	}

	public function signOut(){
		session()->forget('user_id');

		return redirect('/');
	}
}