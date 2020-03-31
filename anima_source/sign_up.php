<?php

$name_error = $adress_error = $pass_error = $file_error = "";
// ポストリクエストによるものか判定
if ($_SERVER['REQUEST_METHOD'] === "POST"){
  // 送信ボタンが押されているかチェック
  if (isset($_POST['submit'])){
    if($_POST['user_name']==="") $name_error .="ユーザーネームが記入されていません。<br>";
    if($_POST['mail_adress']==="") $adress_error .="メールアドレスが記入されていません。<br>";
    // 下のフォームのvalue出力の際XSSを回避するためここでhtmlspecialcharsを使う
    $user_name = htmlspecialchars($_POST['user_name']);
    $password = htmlspecialchars($_POST['password_1']);
    $mail_adress = htmlspecialchars($_POST['mail_adress']);
    $description = htmlspecialchars($_POST['description']);

    if(!filter_var($mail_adress, FILTER_VALIDATE_EMAIL)) $adress_error .="メールアドレスを正しく入力してください。<br>";
    if($_POST['password_1'] === "" or $_POST['password_2']==="") $pass_error .="パスワードを正しく入力してください。<br>";
    if($_POST['password_1']!==$_POST['password_2'])  $pass_error .="パスワードが一致しません。<br>";
    if(!preg_match( "/[\@-\~]/" , $password)) $pass_error .="パスワードは英数字で入力してください。<br>";
    if(!is_uploaded_file($_FILES['profile_picture']['tmp_name'])){
      $file_error .="プロフィール写真がありません<br>";
    }else{
      // 拡張子の取得
      function getExtension($picture){
        $exploded = explode('.',$picture);
        return $exploded[count($exploded)-1];
      }
      $extension = getExtension($_FILES['profile_picture']['name']);
      if ($extension === "jpg" or $extension === "jpeg" or $extension === "JPG"){
        $extension = "jpg";
      } elseif ($extension === "png" or $extension ==="PNG"){
        $extension = "png";
      } elseif ($extension === "gif" or $extension === "GIF"){
        $extension = "gif";
      } else {
        $file_error .="非対応ファイルです<br>";
      }
      if(!$name_error && !$name_error && !$adress_error && !$pass_error && !$file_error ){
        try{
          $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
          // データベースに接続する前にエラーを表示
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          // プリペアードステートメントをエミュレートすることにより、
          // データベースと接続する回数を減らし、負荷を軽減
          $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

          // メールアドレスの複数登録防止
          $stmt = $pdo->prepare("SELECT COUNT(*) AS num FROM users WHERE mail_adress = ?");
          $stmt->bindValue(1, $mail_adress, PDO::PARAM_STR);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          if($row['num'] != 0){
            // すでにメールアドレスが登録されている
            $adress_error.= "このメールアドレスは既に登録されています。";
          }else{
            // パスワードをハッシュ化
            $hash = password_hash ($password, PASSWORD_DEFAULT);
            // プレースホルダを準備
            $sql = '
            INSERT INTO users(
              user_name,
              pass_word,
              description,
              pic_extension,
              mail_adress)
            VALUES(?,?,?,?,?)';
            $st = $pdo->prepare($sql);
            // 値を入力
            $st->bindValue(1,$user_name,PDO::PARAM_STR);
            $st->bindValue(2,$hash,PDO::PARAM_STR);
            $st->bindValue(3,$description,PDO::PARAM_STR);
            $st->bindValue(4,$extension);
            $st->bindValue(5,$mail_adress,PDO::PARAM_STR);
            $st->execute();
            // アイコンの処理
            $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
            // 一応？SQLインジェクション対策
            // $user_name = mysql_real_escape_string($user_name);
            $st = $pdo->prepare("SELECT * FROM users WHERE mail_adress = ?");
            $st->bindValue(1,$mail_adress,PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch();
            $user_id = $row['user_id'];
            // 旧ファイル名を変数に格納
            $tempfile = $_FILES['profile_picture']['tmp_name'];
            // 新ファイル名を変数に格納
            $newfile = './profile_pictures/' . $user_id . "." . $extension;
            move_uploaded_file($tempfile, $newfile);
            // 画像を300x300にリサイズ
            getResized($newfile);
            echo "登録完了しました！<br>";
            echo "<a href=\"login.php\">ログインする</a><br>";
            exit();
          }

        }catch (PDOException $e) {
          print $e->getMessage();
          header('Content-Type: text/plain; charset=utf8', true, 500);
        }

      }
    }
  }
}

// プロフィール画像のリサイズ処理

function getResized($file){
  // 最大の高さ・幅を設定します
  $w = 300;
  $h = 300;

  // 加工前の画像の情報を取得
  list($original_w, $original_h, $type) = getimagesize($file);

  // 加工前の画像の情報を取得
  list($original_w, $original_h, $type) = getimagesize($file);

  // 加工前のファイルをフォーマット別に読み出す（この他にも対応可能なフォーマット有り）
  switch ($type) {
      case IMAGETYPE_JPEG:
          $original_image = imagecreatefromjpeg($file);
          break;
      case IMAGETYPE_PNG:
          $original_image = imagecreatefrompng($file);
          break;
      case IMAGETYPE_GIF:
          $original_image = imagecreatefromgif($file);
          break;
      default:
          throw new RuntimeException('対応していないファイル形式です。: ', $type);
  }

  // 新しく描画するキャンバスを作成
  $canvas = imagecreatetruecolor($w, $h);
  imagecopyresampled($canvas, $original_image, 0,0,0,0, $w, $h, $original_w, $original_h);

  $resize_path = $file; // 保存先を指定

  switch ($type) {
      case IMAGETYPE_JPEG:
          imagejpeg($canvas, $resize_path);
          break;
      case IMAGETYPE_PNG:
          imagepng($canvas, $resize_path, 9);
          break;
      case IMAGETYPE_GIF:
          imagegif($canvas, $resize_path);
          break;
  }

  // 読み出したファイルは消去
  imagedestroy($original_image);
  imagedestroy($canvas);
}
// getResized終了



header("Content-Type: text/html ;charset = utf8");
?>


  <!-- ヘッダ -->
<?php  require_once("navbar.php"); ?>

   <form action="sign_up.php" method="post" enctype="multipart/form-data">
  <div class="form-group w-60%">
    <label for="name">ユーザーネーム</label>
    <p class="text-danger"><?php if($name_error) echo $name_error; ?></p>
    <input type="name" class="form-control" name="user_name" placeholder="(必須)" size="10" value="<?php if(isset($_POST['submit'])) echo $user_name; ?>">
  </div>
  <div class="form-group w-60%">
    <label for="email">メールアドレス</label>
    <p class="text-danger"><?php if($adress_error) echo $adress_error; ?></p>
    <input type="email" class="form-control" name="mail_adress" placeholder="(必須)" size="10" value="<?php if(isset($_POST['submit'])) echo $mail_adress; ?>">
  </div>
  <div class="form-group">
    <label for="passwd">パスワード</label>
    <p class="text-danger"><?php if($pass_error) echo $pass_error; ?></p>
    <input type="password" class="form-control" name="password_1" placeholder="(必須)" value="<?php if(isset($_POST['submit'])) echo $password; ?>">
  </div>
  <div class="form-group">
    <label for="passwd">パスワード(確認)</label>
    <input type="password" class="form-control" name="password_2" placeholder="(必須)" value="">
  </div>
  <div class="form-group">
    <label for="comment">プロフィール</label>
    <textarea class="form-control" rows="3" name="description" placeholder="(任意)"><?php if(isset($_POST['submit'])) echo $description; ?></textarea>
  </div>
  <div class="form-group">
    <label for="icon">プロフィール写真(jpg/png/gifのみ対応)(必須)</label>
    <p class="text-danger"><?php if($file_error) echo $file_error; ?></p>
    <input type="file" name="profile_picture">
  </div>
  <input type="submit" name="submit" value="送信">
</form>

<!--htmlフッタ-->
<?php require_once("html_footer.php") ?>
