<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Module\ShareData;
use App\Enum\ESexType;

use App\Entity\User;
use App\Entity\Mind;

use Image;

class AdminController extends Controller{
	public $page = "admin";

	public function editUserPage(){
		$User = $this->GetUserData();

		if(!$User){
			return redirect('/');
		}

		$name = 'user';
		$binding = [
			'title' => ShareData::TITLE,
			'page' => $this->page,
			'name' => $name,
			'User' => $User,
			'result' => '',
		];
		
		return view('admin.edituser', $binding);
	}

	public function editUserProcess(){
		$User = $this->GetUserData();

		if(!$User){
			return redirect('/');
		}

		$name = 'user';

		$input = request()->all();

		$rules = [
			'sex' => [
				'required',
				'integer',
				'in:'.ESexType::MALE.','.ESexType::FEMALE,
			],
			'height' => [
				'required',
				'numeric',
				'min:1',
			],
			'weight' => [
				'required',
				'numeric',
				'min:1',
			],
			'interest' => [
				'required',
				'max:50',
			],
			'introduce' => [
				'required',
				'max:500',
			],
			'file' => [
				'file',
				'image',
				'max:10240',
			],
		];

		$validator = Validator::make($input, $rules);

		$User->sex = $input['sex'];
		$User->height = $input['height'];
		$User->weight = $input['weight'];
		$User->interest = $input['interest'];
		// $user->introduce = $input['introduce'];

		Log::notice('file='.print_r($input['file'], true));

		if($validator->fails()){

			$binding = [
				'tltle'=>ShareData::TITLE,
				'page'=> $thid->page,
				'name'=>$name,
				'User'=>$User,
			];

			return view('admin.edituser', $binding)
							->withErrors($validator);
		}

		if(isset($input['file'])){
			// 取得檔案物件
			$picture = $input['file'];
			// 檔案副檔名
			$extension = $picture->getClientOriginalExtension();
			// 產生隨機檔案名稱
			$filename = uniqid() . '.' . $extension;
			// 相對路徑
			$relative_path = 'images\\user\\' . $filename;
			// 取得public目錄下的完整位置
			$fullpath = public_path($relative_path);
			// 裁切圖片
			$image = Image::make($picture)->fit(300, 300)->save($fullpath);
			// 儲存圖片檔案相對位置
			$User->picture = $relative_path;
		}

		$User->save();

		$binding = [
			'title' => ShareData::TITLE,
			'page' => $this->page,
			'name' => $name,
			'User' => $User,
			'result' => 'success',
		];

		return view('admin.edituser', $binding)
			->withErrors($validator);
	}

	public function mindListPage(){
		Log::notice('取得心情隨筆列表');

		$User = $this->GetUserData();
		$mindPaginate = Mind::where('user_id', $User->id)->paginate(5);
		$name = 'mind';

		$input = request()->all();

		$result = '';
		if(isset($input['result'])){
			$result = $input['result'];
		}

		$binding = [
			'title' => ShareData::TITLE,
			'page' => $this->page,
			'name' => $name,
			'User' => $User,
			'mindPaginate' => $mindPaginate,
			'result' => $result,
		];

		return view('admin.mindlist', $binding);
	}

	public function addMindPage(){
		Log::notice('新增心情隨筆');

		$User = $this->GetUserData();

		$Mind = new Mind;
		$name = 'mind';
		$action = '新增';

		$binding = [
			'title' => ShareData::TITLE,
			'page' => $this->page,
			'name' => $name,
			'User' => $User,
			'Mind' => $Mind,
			'action' => $action,
			'result' => '',
		];

		return view('admin.mind', $binding);
	}

	public function editMindProcess(){
		Log::notice('處理心情隨筆資料');

		$User = $this->GetUserData();

		if(!$User){
			Log::notice('找不到使用者');
			return redirect('/');
		}

		$name = 'mind';

		$input = request()->all();

		$rules = [
			'content' => [
				'required',
				'max:400',
			],
		];

		$validator = Validator::make($input, $rules);

		if($input['id']==''){
			$action = '新增';
			$Mind = new Mind();
			$Mind->content = $input['content'];
		}else{
			$action = '修改';
			$Mind = Mind::where('id', $input['id'])
									->where('user_id', $User->id)
									->first();
			if(!$Mind){
				return redirect('/admin/mind');
			}

			$Mind->content = $input['content'];
		}

		if($validator->fails()){
			$binding = [
				'title' => ShareData::TITLE,
				'page' => $this->page,
				'name' => $name,
				'User' => $User,
				'Mind' => $Mind,
				'action' => $action,
				'result' => '',
			];

			return view('admin.mind', $binding)
						->withErrors($validator);
		}

		if($input['id']==''){
			$input['user_id'] = $User->id;
			$input['enabled'] = 1;
			Mind::create($input);
		}else{
			$Mind->save();
		}

		return redirect('/admin/mind/?result=success');
	}

	public function editMindPage($mind_id){
		Log::notice('修改心情隨筆資料');

		$User = $this->GetUserData();
		$Mind = Mind::where('id', $mind_id)
								->where('user_id', $User->id)
								->first();

		if(!$Mind){
			return redirect('/admin/mind');
		}

		$name = 'mind';
		$action = '修改';

		$binding = [
			'title' => ShareData::TITLE,
			'name' => $name,
			'page' => $this->page,
			'User' => $User,
			'Mind' => $Mind,
			'action' => $action,
			'result' => '',
		];

		return view('admin.mind', $binding);
	}

	public function deleteMindProcess($mind_id){
	}
}
