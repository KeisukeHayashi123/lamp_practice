<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'history.php';



session_start();

if(is_logined() === false){
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

if(get_post('history_id') !== ''){
  $histories = get_post('history_id');
  set_session($history_id, $histories);
  $history_id = get_session($history_id);
}else if(get_post('history_id') === ''){
  if(get_session($history_id) !== ''){
      $history_id = get_session($history_id);
  }else{
      redirect_to(HISTORY_URL);
  }
}


$details = select_detail($db, $history_id);


include_once VIEW_PATH . 'detail_view.php'; 