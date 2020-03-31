<?php
// セッション
require_once("common_ses.php");
if(!isset($_SESSION['user_id'])){
  header("Locaiton:login.php");
}
$Day = getdate();
$error = "";
if(isset($_POST['submit'])){
  if($_POST['message']==="")$error.="メッセージを追加してください。<br>";
  if($_POST['csrf_token']==="")header("Location:login.php?error=termover");
  if(!isset($_SESSION['user_id'])){
    if(!isset($_POST['postCheck'])){
      $error.="チェックを入れて下さい。<br>";
    }
  }
  $message = htmlspecialchars($_POST['message']);
    if(!is_uploaded_file($_FILES['post_pic']['tmp_name'])){
      $error.="ファイルを選択してください。<br>";
    }else{
      // 拡張子の取得
      function getExtension($picture){
        $exploded = explode('.',$picture);
        return $exploded[count($exploded)-1];
      }
      $extension = getExtension($_FILES['post_pic']['name']);
      if ($extension === "jpg" or $extension === "jpeg" or $extension === "JPG"){
        $extension = "jpg";
      } elseif ($extension === "png" or $extension ==="PNG"){
        $extension = "png";
      } else {
        $error .="非対応ファイルです。<br>";
      }
      if ($_FILES['post_pic']['error'] !== UPLOAD_ERR_OK) {
        $error .="アップロードに失敗しました。<br>もう一度やり直して下さい。";
      }
      if(!$error){
        postIntoDB($extension);
      }
    }
}

function getResized($file){
  // 最大の高さ・幅を設定します
  $w = 1080;
  $h = 1080;

  // 加工前の画像の情報を取得
  list($original_w, $original_h, $type) = getimagesize($file);

  $ratio_orig = $original_w/$original_h;

  if ($w/$h > $ratio_orig) {
     $w = $h*$ratio_orig;
  } else {
     $h = $w/$ratio_orig;
  }

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


function postIntoDB($extension){
  try {
    $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
    // データベースに接続する前にエラーを表示
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // プリペアードステートメントをエミュレートすることにより、
    // データベースと接続する回数を減らし、負荷を軽減
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // メディアパスの生成
    $Day = getdate();
    $sha = sha1($Day['minutes'].$Day['weekday']);
    $mediapath = "./post_pictures/". $sha . "." . $extension;
    if(isset($_SESSION['user_id'])){
      // ログインしている場合
      $user_id = $_SESSION['user_id'];
      $stmt = $pdo->prepare("INSERT INTO posts(user_id,message,media_path) VALUES(?,?,?)");
      $stmt->bindValue(1,(int)$user_id,PDO::PARAM_INT);
      $stmt->bindValue(2,$_POST['message'],PDO::PARAM_STR);
      $stmt->bindValue(3,$mediapath,PDO::PARAM_STR);
      $stmt->execute();
    }else{
      // ログインしてない場合
      $stmt = $pdo->prepare("INSERT INTO posts(user_id,message,media_path) VALUES(?,?,?)");
      $no_user_id = 0;
      $stmt->bindValue(1,(int)$no_user_id,PDO::PARAM_INT);
      $stmt->bindValue(2,$_POST['message'],PDO::PARAM_STR);
      $stmt->bindValue(3,$mediapath,PDO::PARAM_STR);
      $stmt->execute();
    }


    // 画像ファイルを./post_picturesに格納
    $post_pic = $_FILES['post_pic']['tmp_name'];
    move_uploaded_file($post_pic,$mediapath);
    list($original_w, $original_h, $type) = getimagesize($mediapath);
    if($original_w > 1080 || $original_h > 1080){
      getResized($mediapath);
    }
    echo "投稿完了！";
    echo "<a href=\"index.php\">ホームへ</a><br>";
    exit();


  } catch (PDOException $e) {
    echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage()."</p>";
    header('Content-Type: text/plain; charset=utf8', true, 500);
  }
}

header("Content-Type: text/html ;charset = utf8");
 ?>

  <!-- ヘッダ -->
<?php  require_once("navbar.php"); ?>
  <h1 class="anima">写真をアップ</h1>
  <p class="text-danger"><?php if(isset($_POST['submit']))echo $error; ?></p>


   <form method="post" enctype="multipart/form-data">
   <div class="form-group">
     <label for="message">メッセージ</label><br>
     <textarea name="message" rows="8" cols="80"><?php if(isset($_POST['submit']))echo $message ?></textarea>
     <small id="emailHelp" class="form-text text-muted">写真についてひとことメッセージをどうぞ！</small>
   </div>
   <div class="form-group">
     <label for="post_pic">投稿画像</label>
     <input type="file" class="form-control" name="post_pic" id="target">
     <small id="postHelp" class="form-text text-muted">↓ここに選択された画像を表示します。</small>
   </div>
   <div class="form-group">
     <label for="csrf_token"></label>
     <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
   </div>
   <div class="img-display">
     <img id="myImage" class="img-responsive">
   </div>
   <?php if(!isset($_SESSION['user_id'])){?>
   <div class="form-check">
     <input type="checkbox" class="form-check-input" name="postCheck">
     <label class="form-check-label" for="postCheck">匿名で投稿する場合、一度投稿した写真は削除できません。よろしいですか？</label>
   </div>
 <?php } ?>
   <div class="form-submit">
     <input type="submit" name="submit" value="投稿">
   </div>
 </form>

<script src="load.js"></script>

<!--htmlフッタ-->
<?php require_once("html_footer.php") ?>
