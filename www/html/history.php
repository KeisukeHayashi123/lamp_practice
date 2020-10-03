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
$histories = get_history($db, $user['user_id']);
//var_dump($histories);
$carts = get_user_carts($db, $user['user_id']);
$total_price = sum_histories($histories['total']);
include_once VIEW_PATH . 'history_view.php';


 