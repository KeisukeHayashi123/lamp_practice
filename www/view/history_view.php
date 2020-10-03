<!DOCTYPE html>
<html lang="ja">
<head>
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入履歴</title>
</head>
<body>
    <?php 
    include VIEW_PATH . 'templates/header_logined.php'; 
    ?>
    
    
    <div class="container">
        <h1>購入履歴</h1>
        <table>
            <tr>
                <th>注文番号</th>
                <th>購入日時</th>
                <th>該当の注文の合計金額</th>
                <th>詳細確認</th>
            </tr>
              <?php foreach($histories as $history){ ?>
                <tr>
                  <td><p><?php print $history['history_id']; ?> 番</p></td>
                  <td><p><?php print $history['create_datetime']; ?></p></td>
                  <td><p>合計<?php print $history['total'];?> 円</P></td>
                  <td>
                    <form method="post" action="./detail.php">
                      <input type="hidden" name="history_id" value="<?php print h($history['history_id']); ?>">
                      <input type="hidden" name="token" value="<?php print h($token); ?>">
                      <input type="submit" value="詳細">
                    </form>
                  </td>
                </tr>   
                <?php } ?>
        </table>
    </body>
</html> 