<?php
// セッション
require_once("common_ses.php");
// ログインしているかどうかの確認
if(isset($_SESSION['user_id'])){
  header("Location:user_account.php");
}
$mail_adress = $password = "";
// エラーメッセージの定義
$adress_error = $pass_error = $term_error = "";
if(isset($_POST['submit'])){
  if($_POST['mail_adress']==="")$adress_error.= "メールアドレスが入力されていません。";
  if($_POST['password']==="")$pass_error.= "パスワードが入力されていません。";
  if(isset($_GET['error'])){
    if($_GET['error']==="termover"){
      $term_error.= "ログイン有効期限切れです。再度ログインして下さい。";
    }
  }
  if(!$adress_error &&!$pass_error){
    // 下記の出力の XSS対策
    $mail_adress.= htmlspecialchars($_POST['mail_adress']);
    $password.= htmlspecialchars($_POST['password']);
    try {
      $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
      // 存在確認
      $stmt = $pdo->prepare("SELECT COUNT(*) AS num FROM users WHERE mail_adress = ?");
      $stmt->bindValue(1, $mail_adress, PDO::PARAM_STR);
      $row = $stmt->fetch();
      if($row['num'] === 0){
        // データベースが存在しない
        $adress_error.= "アカウントが見つかりません。";
      }else{
        // パスワードの正誤判定
        $stmt = $pdo->prepare("SELECT * FROM users WHERE mail_adress = ?");
        $stmt->bindValue(1, $mail_adress, PDO::PARAM_STR);
        $stmt->execute();
        // $user_dataを連想配列に
        $user_data = $stmt->fetch();
        $user_true_password = $user_data['pass_word'];
        if(password_verify($password,$user_true_password)){
          // 入力さえれたパスワードが正しかった場合
          // ユーザー認識のセッションを開始
          $_SESSION['user_id'] = $user_data['user_id'];
          $_SESSION['user_name'] = $user_data['user_name'];
          // CSRFトークン
          $csrf_token = sha1($user_data['user_id']);
          $_SESSION['csrf_token'] = $csrf_token;
          header("Location: index.php");
          exit();
        }else{
          // パスワードが間違っている場合
          $pass_error.="パスワードが間違っています。";
        }

      }

    } catch (PDOException $e) {
      print $e->getMessage();
      header('Content-Type: text/plain; charset=utf8', true, 500);
    }
  }
}

header("Content-Type: text/html ;charset = utf8");
 ?>

<?php  require_once("navbar.php"); ?>

  <form action="login.php" method="post">
    <p class="text-danger"><?php if($adress_error) echo $term_error; ?></p>
 <div class="form-group w-60%">
   <label for="email">メールアドレス</label>
   <p class="text-danger"><?php if($adress_error) echo $adress_error; ?></p>
   <input type="email" class="form-control" name="mail_adress" placeholder="(必須)" size="10" value="<?php if(isset($_POST['submit'])) echo $mail_adress; ?>">
 </div>
 <div class="form-group">
   <label for="passwd">パスワード</label>
   <p class="text-danger"><?php if($pass_error) echo $pass_error; ?></p>
   <input type="password" class="form-control" name="password" placeholder="(必須)" value="">
 <input type="submit" name="submit" value="送信">
</form>

    <p>アカウントをお持ちでない場合は、</p><a href="sign_up.php">新規登録</a>

    <!--htmlフッタ-->
    <?php require_once("html_footer.php") ?>
