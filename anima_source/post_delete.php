<?php
function getDelete($post_id){
  try {
    $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
    // データベースに接続する前にエラーを表示
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // プリペアードステートメントをエミュレートすることにより、
    // データベースと接続する回数を減らし、負荷を軽減
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $stmt = $pdo->query("SELECT * FROM posts WHERE post_id = $post_id");
    $post_row = $stmt->fetch();

    // 削除するファイルリンクを取得
    $delete_file = glob($post_row['media_path']);
    // 画像を削除
    unlink($delete_file);

    // データベースの削除
    $stmt = $pdo->prepare("DELETE FROM posts WHERE post_id = ?");
    $stmt->bindValue(1, $post_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location:user_account.php");
  } catch (PDOException $e) {
    echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage(). "</p>";
  }
}
if($_SERVER['REQUEST_METHOD']==='GET'){
  $post_id = intval(strip_tags($_GET['post_id']));
  getDelete($post_id);

}else{
  $redirectUrl = "404.html";
  header("HTTP/1.0 404 Not Found");
  print(file_get_contents($redirectUrl));
  exit;
}
 ?>
