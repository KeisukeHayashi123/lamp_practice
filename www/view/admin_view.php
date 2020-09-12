<!DOCTYPE html>
//商品管理ページ
<html lang="ja">
<head>
  //VIEWファイルの読み込み テンプレートのヘッド画面表示
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>商品管理</title>
  //STYLESHEETのadmin.cssを採用　h関数追加が必要
  <link rel="stylesheet" href="<?php print (STYLESHEET_PATH . 'admin.css'); ?>">
</head>
<body>
  //VIEWファイルの読み込み　テンプレートのログインされた後の画面ページを表示
  <?php 
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>

  <div class="container">
    <h1>商品管理</h1>
//VIEWファイルの読み込み ようこそ~さんのとこ　テンプレートのメッセージを表示
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <form 
      method="post" 
      action="admin_insert_item.php" 
      enctype="multipart/form-data"
      class="add_item_form col-md-6">
      <div class="form-group">
        <label for="name">名前: </label>
        <input class="form-control" type="text" name="name" id="name">
      </div>
      <div class="form-group">
        <label for="price">価格: </label>
        <input class="form-control" type="number" name="price" id="price">
      </div>
      <div class="form-group">
        <label for="stock">在庫数: </label>
        <input class="form-control" type="number" name="stock" id="stock">
      </div>
      <div class="form-group">
        <label for="image">商品画像: </label>
        <input type="file" name="image" id="image">
      </div>
      <div class="form-group">
        <label for="status">ステータス: </label>
        <select class="form-control" name="status" id="status">
          <option value="open">公開</option>
          <option value="close">非公開</option>
        </select>
      </div>
      
      <input type="submit" value="商品追加" class="btn btn-primary">
    </form>


    <?php if(count($items) > 0){ ?>
      <table class="table table-bordered text-center">
        <thead class="thead-light">
          <tr>
            <th>商品画像</th>
            <th>商品名</th>
            <th>価格</th>
            <th>在庫数</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items as $item){ ?>
          //h関数追加が必要
          <tr class="<?php print h(is_open($item) ? '' : 'close_item'); ?>">
            <td><img src="<?php print h(IMAGE_PATH . $item['image']);?>" class="item_image"></td>
            <td><?php print ($item['name']); ?></td>
            <td><?php print (number_format($item['price'])); ?>円</td>
            <td>
              <form method="post" action="admin_change_stock.php">
                <div class="form-group">
                  <!-- sqlインジェクション確認のためあえてtext -->
                  //h関数追加が必要
                  <input  type="text" name="stock" value="<?php print ($item['stock']); ?>">
                  個
                </div>
                <input type="submit" value="変更" class="btn btn-secondary">
                //h関数追加が必要
                <input type="hidden" name="item_id" value="<?php print ($item['item_id']); ?>">
              </form>
            </td>
            <td>

              <form method="post" action="admin_change_status.php" class="operation">
                <?php if(is_open($item) === true){ ?>
                  <input type="submit" value="公開 → 非公開" class="btn btn-secondary">
                  <input type="hidden" name="changes_to" value="close">
                <?php } else { ?>
                  <input type="submit" value="非公開 → 公開" class="btn btn-secondary">
                  <input type="hidden" name="changes_to" value="open">
                <?php } ?>
                //h関数追加
                <input type="hidden" name="item_id" value="<?php print ($item['item_id']); ?>">
              </form>

              <form method="post" action="admin_delete_item.php">
                <input type="submit" value="削除" class="btn btn-danger delete">
                //h関数追加
                <input type="hidden" name="item_id" value="<?php print h($item['item_id']); ?>">
              </form>

            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>商品はありません。</p>
    <?php } ?> 
  </div>
  <script>
    $('.delete').on('click', () => confirm('本当に削除しますか？'))
  </script>
</body>
</html>
