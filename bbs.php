<?php
  // ①DB接続処理
  $dsn = 'mysql:dbname=LAA0808306-onelinebbs;host=mysql117.phy.lolipop.lan';
  $user = 'LAA0808306';
  $password = 'Y1054f3710';
  $dbh = new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf8');

  // 歯車アイコンクリック時
  // 変数の初期化
  $editName = '';
  $editComment = '';
  $id = '';
  $btnStr = 'つぶやく';
  if (!empty($_GET['action']) && $_GET['action'] == 'edit') {
      // パラメータにactionがあり、かつactionの中身がeditだったら処理
      $sql = 'SELECT * FROM `posts` WHERE `id` = ?';
      $data[] = $_GET['id'];
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
      $rec = $stmt->fetch(PDO::FETCH_ASSOC);
      // 変数に値を格納
      $editName = $rec['nickname'];
      $editComment = $rec['comment'];
      $id = $rec['id'];
      $btnStr = '更新';
  }
  // echo '<br>';
  // echo '<br>';
  // echo $editName . '<br>';
  // echo $editComment . '<br>';
  // echo $id . '<br>';
  // $_GETスーパーグローバル変数
  // URLの?以下 (パラメータ) をkey=valueの連想配列形式で取得する変数
  // 複数のパラメータを持つURL : bbs.php?key1=value1&key2=value2&key3=value3
  // $_GET = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');
  // ↑↑↑内部ではPHPが上記の式を処理している
  // ②ひとことデータの登録処理
  if (!empty($_POST)) { // フォームからPOST送信があった際にのみ実行される
      if (!empty($_POST['id'])) {
          // 更新処理用のsql文を作成
          $sql = 'UPDATE `posts` SET `nickname` = ?, `comment` = ? WHERE `id` = ?';
          $data[] = $_POST['nickname'];
          $data[] = $_POST['comment'];
          $data[] = $_POST['id'];
      } else {
          // 登録処理用のsql文を作成
          $sql = 'INSERT INTO `posts` (`nickname`, `comment`, `created`)
                        VALUES (?, ?, NOW())';
          $data[] = $_POST['nickname'];
          $data[] = $_POST['comment'];
      }
      // SQL実行
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
      header('Location: bbs.php'); // 指定したURLに遷移
      exit(); // これ以下の処理を停止
  }
  // データの削除処理
  if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
      // 物理削除
      // $sql = 'DELETE FROM `posts` WHERE `id` = ?';
      // 論理削除 (フラグデータを変更)
      $sql = 'UPDATE `posts` SET `delete_flag` = 1 WHERE `id` = ?';
      $data[] = $_GET['id'];
      // SQL実行
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
      header('Location: bbs.php'); // 指定したURLに遷移
      exit(); // これ以下の処理を停止
  }
  // 登録データ一覧表示
  $sql = 'SELECT * FROM `posts` WHERE `delete_flag` = 0 ORDER BY `created` DESC';
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
      echo $rec['nickname'] . '<br>';
      echo $rec['comment'] . '<br>';
      echo $rec['created'] . '<br>';
      echo '<hr>';
  }
  // echo '<pre>';
  // var_dump($posts);
  // echo '</pre>';
  // DB切断
  $dbh = null;
?>


  

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#page-top"><span class="strong-title"><i class="fa fa-linux"></i> Oneline bbs</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <!-- Bootstrapのcontainer -->
  <div class="container">
    <!-- Bootstrapのrow -->
    <div class="row">

      <!-- 画面左側 -->
      <div class="col-md-4 content-margin-top">
        <!-- form部分 -->
        <form action="bbs.php" method="post">
          <!-- nickname -->
          <div class="form-group">
            <div class="input-group">
              <input type="text" name="nickname" class="form-control" id="validate-text" placeholder="nickname" required value="<?php echo $editName; ?>">
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- comment -->
          <div class="form-group">
            <div class="input-group" data-validate="length" data-length="4">
              <textarea type="text" class="form-control" name="comment" id="validate-length" placeholder="comment" required><?php echo $editComment; ?></textarea>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- つぶやくボタン -->
          <button type="submit" class="btn btn-primary col-xs-12" disabled><?php echo $btnStr; ?></button>
          <input type="hidden" name="id" value="<?php echo $id; ?>">
        </form>
      </div>

      <!-- 画面右側 -->
      <div class="col-md-8 content-margin-top">

        <div class="timeline-centered">
          <?php foreach($posts as $post): ?>
            <article class="timeline-entry">
                <div class="timeline-entry-inner">
                  <a href="bbs.php?action=edit&id=<?php echo $post['id']; ?>">
                    <div class="timeline-icon bg-success">
                        <i class="entypo-feather"></i>
                        <i class="fa fa-cogs"></i>
                    </div>
                  </a>
                    <div class="timeline-label">
                        <h2><a href="#"><?php echo htmlspecialchars($post['nickname']); ?></a> <span><?php echo $post['created']; ?></span></h2>
                        <p><?php echo htmlspecialchars($post['comment']); ?></p>
                        <a onclick="return confirm('本当に削除しますか？');" href="bbs.php?action=delete&id=<?php echo $post['id']; ?>"><i class="fa fa-trash trash"></i></a>
                    </div>
                </div>
            </article>
          <?php endforeach; ?>

          <article class="timeline-entry begin">
              <div class="timeline-entry-inner">
                  <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                      <i class="entypo-flight"></i> +
                  </div>
              </div>
          </article>
        </div>

      </div>

    </div>
  </div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/form.js"></script>
</body>
</html>
