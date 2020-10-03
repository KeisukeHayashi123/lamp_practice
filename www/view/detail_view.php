<!DOCTYPE html>
<head>
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>注文詳細</title>
</head>
<body>
    <?php 
    include VIEW_PATH . 'templates/header_logined.php'; 
    ?>
    <h1>購入詳細</h1>
    <?php if(count($details) > 0){ ?>
        <table class="table table-bordered">
            <tr>
                <th>商品名</th>
                <th>商品価格</th>
                <th>購入数</th>
                <th>小計</th>
            </tr>  
            <?php foreach($details as $detail){ ?>
                <tr>
                    <td><?php print h($detail['name']); ?></td>
                    <td><?php print h($detail['price']); ?></td>
                    <td><?php print h($detail['amount']); ?></td>
                    <td><?php print h($detail['(amount * purchase_detail.price)']); ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</body>