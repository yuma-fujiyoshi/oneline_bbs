<?php
  // ①DB接続処理
  $dsn = 'mysql:dbname=oneline_bbs;host=localhost';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf8');
  // ②ひとことデータの登録処理
  if (!empty($_POST)) { // フォームからPOST送信があった際にのみ実行される
      // 登録処理用のsql文を作成
      $sql = 'INSERT INTO `posts` (`nickname`, `comment`, `created`)
                    VALUES (?, ?, NOW())';
      $data[] = $_POST['nickname'];
      $data[] = $_POST['comment'];
      // SQL実行
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
      header('Location: bbs_no_css.php'); // 指定したURLに遷移
      exit(); // これ以下の処理を停止
  }
  // 登録データ一覧表示
  $sql = 'SELECT * FROM `posts` ORDER BY `created` DESC';
  // ORDER BY句
    // 指定したカラムの値を元に順番を決めて取得
    // DESC (降順) と ASC (昇順) がある
    // 指定しない場合はデフォルトで昇順 (古い順)で取得
  // SQL実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  // objectからarrayに変換して表示
  $posts = array(); // データ格納用の空配列を用意
  while (1) {
      $rec = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($rec == false) {
          break;
      }
      // １レコードずつデータを格納
      $posts[] = $rec;
      // echo $rec['nickname'] . '<br>';
      // echo $rec['comment'] . '<br>';
      // echo $rec['created'] . '<br>';
      // echo '<hr>';
  }
  // echo '<pre>';
  // var_dump($posts);
  // echo '</pre>';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>
</head>
<body>
    <form method="post" action="bbs_no_css.php">
      <p><input type="text" name="nickname" placeholder="nickname"></p>
      <p><textarea type="text" name="comment" placeholder="comment"></textarea></p>
      <p><button type="submit" >つぶやく</button></p>
    </form>
    <!-- ここにニックネーム、つぶやいた内容、日付を表示する -->
    <?php foreach($posts as $post): ?>
      <p>
        <?php echo $post['nickname']; ?> - <?php echo $post['comment']; ?> (<?php echo $post['created']; ?>)
      </p>
    <?php endforeach; ?>
</body>
</html>
