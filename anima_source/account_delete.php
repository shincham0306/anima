<?php
// セッション
require_once("/opt/lampp/htdocs/anima/common_ses.php");
// ログインしているかどうかの確認
$error = "";
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['submit'])){
      if(!filter_input(INPUT_POST, 'confirm1'))$error.= "チェックを入れて下さい。";
      if(!filter_input(INPUT_POST, 'confirm2'))$error.= "チェックを入れて下さい。";
      if($error===""):
        try {
          $user_id = intval($_SESSION['user_id']);
          $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
          // データベースに接続する前にエラーを表示
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          // プリペアードステートメントをエミュレートすることにより、
          // データベースと接続する回数を減らし、負荷を軽減
          $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

          /*---------------
          アカウント削除処理
          ---------------*/
          $user_st= $pdo->prepare("DELETE FROM users WHERE user_id = ?");
          $user_st->bindValue(1, $user_id, PDO::PARAM_INT);
          $user_st->execute();

          $post_st = $pdo->prepare("DELETE FROM posts WHERE user_id = ?");
          $post_st->bindValue(1, $user_id, PDO::PARAM_INT);
          $post_st->execute();

          $comment_st = $pdo->prepare("DELETE FROM comments WHERE user_id = ?");
          $comment_st->bindValue(1, $user_id, PDO::PARAM_INT);
          $comment_st->execute();

          // セッションの削除
          $_SESSION = array();

          /*---------------
          リダイレクト処理
          ---------------*/
          header("Location:index.php");
          exit();


        } catch (PDOException $e) {
          echo $e->getMessage();
          header("Content-Type: text/plain ; charset = utf8", true, 500);
        }
      endif;
    }
}else{
  $redirectUrl = "404.html";
  header("HTTP/1.0 404 Not Found");
  print(file_get_contents($redirectUrl));
  exit;
}

header("Content-Type: text/html ;charset = utf8");
 ?>

  <!-- htmlヘッダ -->
<?php  require_once("navbar.php"); ?>

  <form action="account_delete.php" method="post">
 <div class="form-group w-60%">
   <label for="">アカウントはデータベースから永久に削除されます。よろしいですか？<br></label>
   <input type="checkbox" name="confirm1" value="yes">はい
 </div>
 <div class="form-group w-60%">
   <label for="">削除の操作は取り消せません。よろしいですか？<br></label>
   <input type="checkbox" name="confirm2" value="yes">はい
 </div>
 <input type="submit" name="submit" value="削除">
</form>
<?php echo "<p class=\"text-danger\">".$error."</p>"; ?>

<!--htmlフッタ-->
<?php require_once("html_footer.php") ?>
