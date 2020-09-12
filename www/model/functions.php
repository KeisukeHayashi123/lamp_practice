<?php
//エラー時対処関数
function dd($var){
  var_dump($var);
  exit();
}
//ログインページへリダイレクト関数
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}
//$_GETで渡ってきているのかの関数
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}
//$_POSTで渡ってきているのかの関数
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}
//$_FILESで渡ってきているのかの関数
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}
//$_SESSIONで渡ってきているのかの関数  
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}
//何がしたいかわからない　$valueに格納?
function set_session($name, $value){
  $_SESSION[$name] = $value;
}
//?
function set_error($error){
  $_SESSION['__errors'][] = $error;
}
//?
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

function set_message($message){
  $_SESSION['__messages'][] = $message;
}

function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}
//ログインチェック用関数
function is_logined(){
  return get_session('user_id') !== '';
}
//ファイルのアップロード用関数
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}
//?　不規則な文字列(20文字)
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}
//? アップロードされたファイルの移動、一時保存でなくて正式にアップロード完了させる関数?
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}
//ファイルの削除関数
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}


//ちゃんとした文字列の長さかの関数
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}
//アルファベット関数 文字列がアルファベットなら正当なフォーマットを返す
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}
//文字列が自然数なら正当なフォーマットを返す
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}
//うまく表現できない　要はちゃんとわかってない
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}

//ファイル形式の選定関数
function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}

//ここ追加
//html内での特殊文字をエスケープするユーザー定義関数
function h ($key) {
  $str = htmlspecialchars($key, ENT_QUOTES, 'utf-8');
  return $str;
}

