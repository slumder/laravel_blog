<?php

namespace App\Http\Controllers;

use App\Entity\Board;
use App\Entity\Mind;
use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Module\ShareData;

class HomeController extends Controller {
  public $page = "";

  public function indexPage() {
    $name = 'home';
    $userList = User::all();
    $binding = [
      'title' => ShareData::TITLE,
      'page' => $this->page,
      'name' => $name,
      'User' => $this->GetUserData(),
      'userList' => $userList,
    ];

    return view('home', $binding);
  }

  public function userPage($user_id) {
    $this->page = 'user';
    $name = 'user';

    $userData = User::where('id', $user_id)->first();

    if (!$userData) {
      return redirect('/');
    }

    // $userData->sex = ShareData::GetSex($userData->sex);
    $userData->sex = '1';

    $binding = [
      'title' => ShareData::TITLE,
      'page' => $this->page,
      'name' => $name,
      'User' => $this->GetUserData(),
      'userData' => $userData,
    ];

    return view('blog.user', $binding);
  }

  public function mindPage($user_id) {
    $this->page = 'user';
    $name = 'mind';

    $userData = User::where('id', $user_id)->first();

    if (!$userData) {
      return redirect('/');
    }

    $mindList = Mind::where('user_id', $user_id)->orderby('created_at', 'desc')->get();

    $binding = [
      'title' => ShareData::TITLE,
      'page' => $this->page,
      'name' => $name,
      'User' => $this->GetUserData(),
      'userData' => $userData,
      'mindList' => $mindList,
    ];

    return view('blog.mind', $binding);
  }

  public function boardPage($user_id) {
    $this->page = 'user';
    $name = 'board';

    $userData = User::where('id', $user_id)->first();

    if (!$userData) {
      return redirect('/');
    }

    $boardList = board::where('board_id', $userData->id)->orderby('created_at', 'desc')->get();

    $binding = [
      'title' => ShareData::TITLE,
      'page' => $this->page,
      'name' => $name,
      'User' => $this->GetUserData(),
      'userData' => $userData,
      'boardList' => $boardList,
    ];

    return view('blog.board', $binding);
  }
}
