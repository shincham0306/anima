<?php
// セッション
require_once("/opt/lampp/htdocs/anima/common_ses.php");
$error = $post_id = "";
if(isset($_POST['submit'])){
  if($_POST['message']==="")$error.="メッセージを追加してください。<br>";
  if($error === ""){
    commentIntoDB(strip_tags($_POST['hidden']),$_POST['message']);
  }
}else{
  $post_id = $_GET['post_id'];
}

function commentIntoDB($post_id,$message){
  try {
    $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
    // データベースに接続する前にエラーを表示
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // プリペアードステートメントをエミュレートすることにより、
    // データベースと接続する回数を減らし、負荷を軽減
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    if(isset($_SESSION['user_id'])){
      $user_id = $_SESSION['user_id'];
    }else{
      $user_id = 0;
    }
    $stmt = $pdo->prepare("INSERT INTO comments (post_id,user_id,message) VALUES(?,?,?)");
    $stmt->bindValue(1,$post_id,PDO::PARAM_INT);
    $stmt->bindValue(2,$user_id,PDO::PARAM_INT);
    $stmt->bindValue(3,$message,PDO::PARAM_STR);
    // 実行
    $stmt->execute();
    echo "投稿完了！";
    echo "<a href=\"index.php\">ホームへ</a><br>";
    exit();
  } catch (PDOException $e) {
    echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage()."</p>";
    header('Content-Type: text/plain; charset=utf8', true, 500);
  }


}

 ?>

  <!-- ヘッダ -->
<?php  require_once("/opt/lampp/htdocs/anima/navbar.php"); ?>

<h1 class="text-center">コメントの投稿</h1>
<div class="text-center">
  <form class="form-inline justify-content-center" action="comment.php" method="post">

    <div class="form-group col-8">
      <label for="message">投稿内容</label><br>
      <textarea name="message" rows="8" cols="80"></textarea>
    </div><br>
    <div class="form-group col-8">
      <input type="hidden" name="hidden" value="<?php echo $post_id;?>">
    </div><br>

    <p class="text-danger"><?php if(isset($_POST['submit']))echo $error; ?></p>

    <input class="form-gruop" type="submit" name="submit" value="送信">
  </form>
  </div>

  <!--htmlフッタ-->
  <?php require_once("html_footer.php") ?>
