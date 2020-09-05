<?php
//関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//データベースファイルの読み込み
require_once MODEL_PATH . 'db.php';

// DB利用
//商品データの取得
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = {$item_id}
  ";

  return fetch_query($db, $sql);
}
//itemsテーブルから非公開公開の商品を取得
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  //ステータスが公開ならステータス1
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}
//すべての商品を取得
function get_all_items($db){
  return get_items($db);
}
//ステータス1の商品のみ取得
function get_open_items($db){
  return get_items($db, true);
}
//登録される商品が正常かの関数
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}
//トランザクション 登録される商品が正常ならコミット、ダメならロールバックする　
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}
//追加する商品関数
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES('{$name}', {$price}, {$stock}, '{$filename}', {$status_value});
  ";

  return execute_query($db, $sql);
}
//商品の公開非公開の更新関数
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = {$status}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}
//商品の数量更新関数
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = {$stock}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}
//商品破壊? 何がしたい関数かわかんない
function destroy_item($db, $item_id){
  //変数$itemに商品データを取得し格納
  $item = get_item($db, $item_id);
  //取得した商品データがfalseなら
  if($item === false){
    //returnでfalseを返す
    return false;
  }
  //トランザクション開始
  $db->beginTransaction();
  //もし削除する商品と画像が変数$itemの中に格納されているなら??　わからん
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    //コミットする
    $db->commit();
    return true;
  }
  //違ったらロールバックする
  $db->rollback();
  return false;
}
//商品削除関数
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}


// 非DB
//商品一覧でステータスが1公開時かの関数
function is_open($item){
  return $item['status'] === 1;
}
//商品の各項目バリデーション　各々変数に格納
function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}
//商品名バリデーション関数　上限下限があっていなければメッセージを出す
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}
//商品価格バリデーション関数　自然数でなければメッセージを出す
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//商品数バリデーション関数 在庫数が自然数でなければメッセージを出す
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//ファイル名バリデーション関数 空ならfalseを出す
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}
//ステータスバリデーション関数 
function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}
