<?php
//関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//データベースファイルの読み込み
require_once MODEL_PATH . 'db.php';
//ユーザーの取得(idによる)
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql,array($user_id));
}
//ユーザーの取得(名前)
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = ?
    LIMIT 1
  ";


  return fetch_query($db, $sql,array($name));
}
//?? 既にログインしたことあるユーザーかの確認?
function login_as($db, $name, $password){
  //get_user_name関数を変数$userに格納
  $user = get_user_by_name($db, $name);
  //もし変数$userが異なっているかパスワードが違ったらfalseを返す
  if($user === false || $user['password'] !== $password){
    return false;
  }
  set_session('user_id', $user['user_id']);
  return $user;
}
//ログインユーザー情報の取得
function get_login_user($db){
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}
//ユーザー情報の登録
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}
//管理者が$userと一致している場合
function is_admin($user){
  //$userのタイプを管理者にして返す
  return $user['type'] === USER_TYPE_ADMIN;
}
//ユーザーが正規かどうかの関数
function is_valid_user($name, $password, $password_confirmation){
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}
//ユーザー名が正規か正当かどうかの関数
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//パスワードが正規か正当かどうかの関数
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}
//新規ユーザー追加関数
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
      VALUES(?,?);
  ";
  


  return execute_query($db, $sql,array($name,$password));
}

