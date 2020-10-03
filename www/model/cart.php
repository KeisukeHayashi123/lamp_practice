<?php 
//関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//データベースファイルの読み込み
require_once MODEL_PATH . 'db.php';

//カートの商品データ
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
   return fetch_all_query($db, $sql,array($user_id));
}
//カートに追加するために必要なデータ
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id

    WHERE
     
      carts.user_id = ?
    AND
      
      items.item_id = ?
  ";
 

   return fetch_query($db, $sql,array($user_id,$item_id));

}

//カートに追加 もし既に同じ商品があったらプラスで1個数追加,0なら新規追加
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//上との違い　データ新規作成数量1個
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )

      VALUES(?,?,?)
  ";
   return execute_query($db, $sql,array($item_id,$user_id,$amount));
}

//追加テーブルへのデータ保存
function insert_history($db,$user_id){
  $sql = "
  INSERT INTO
  history(
    user_id,
    create_datetime
    )
    VALUES(?,NOW())
  ";

  //var_dump($user_id,$sql);
  return execute_query($db,$sql,array($user_id));
}
//追加テーブルへのデータ保存
function insert_detail($db,$history_id,$item_id,$amount,$price){
  $sql = "
  INSERT INTO
  purchase_detail(
    history_id,
    item_id,
    amount,
    price
  )
  VALUES(?,?,?,?)
  ";
  return execute_query($db,$sql,array($history_id,$item_id,$amount,$price));
}

 /* function select_history($db, $user_id){
    $sql = "
    SELECT 
  history.history_id,
  create_datetime,
  SUM(price*amount) AS total
  FROM
  history
  JOIN purchase_detail
  ON purchase_detail.history_id = history.history_id
  GROUP BY purchase_detail.history_id";
  return fetch_all_query($db, $sql, array($user_id));
  }
*/
function select_detail($db, $history_id){
  $sql = "
      SELECT
          name,purchase_detail.price,amount,(amount * purchase_detail.price)
      FROM
          purchase_detail
      JOIN
          items
      ON
          purchase_detail.item_id = items.item_id
      WHERE
          purchase_detail.history_id = ?
   ";
  return fetch_all_query($db, $sql, array($history_id));
}




//カート画面での数量変更　LIMIT句(一つの商品ごと?)
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
      
    WHERE
      cart_id = ?

     
    LIMIT 1
  ";
 
   return execute_query($db, $sql,array($amount,$cart_id));

}

//カート画面での商品削除(一つの商品ごと?)
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?

     
    LIMIT 1
  ";

  

  return execute_query($db, $sql,array($cart_id));
}

//購入処理 カート内の商品を購入できない場合、falseを返す
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }


//これより下カート内の商品を購入できた場合の処理だから

  
  $db->beginTransaction();

   insert_history($db,$carts[0]['user_id']);
  $history_id = $db->lastInsertId();
  foreach($carts as $cart){
    insert_detail($db,$history_id,$cart['item_id'],$cart['amount'],$cart['price']);
  }
//商品購入後の在庫数の変更・連動させる
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  //カートの中身を削除
  delete_user_carts($db, $carts[0]['user_id']);

  //エラーがある場合ロールバックなければコミット
  if(has_error()){
    $db->rollback();
  }else{
    $db->commit();
  }
}

//カートの中身を削除↓
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?

     
  ";

  

  execute_query($db, $sql,array($user_id));
}

//商品の合計金額
function sum_carts($carts){
  //$total_priceの初期化
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

//カートのバリデーション
function validate_cart_purchase($carts){
  //カートに何もなければメッセージを出して購入できない
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  //商品が非公開ならメッセージを出して購入できない
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    //商品の在庫が足りなければメッセージを出して購入できず、購入可能数のメッセージを出す
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  //エラーがなければ購入できる
  if(has_error() === true){
    return false;
  }
  return true;
}

