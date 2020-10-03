<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';

function get_history($db, $user_id){
  $sql = "
  SELECT 
  history.history_id,
  create_datetime,
  SUM(price*amount) AS total
  FROM
  history
  JOIN purchase_detail
  ON purchase_detail.history_id = history.history_id
  WHERE history.user_id = ?
  GROUP BY purchase_detail.history_id
  ORDER BY create_datetime DESC;
  ";
  return fetch_all_query($db, $sql, array($user_id));
}



