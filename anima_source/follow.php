<?php
// セッション
require_once("common_ses.php");

/*-----------------------------------------
ログインしてなければ、トップページへリダイレクトする
-------------------------------------------*/
if(!isset($_SESSION['user_id'])){
  header("Location:index.php");
}else{
  if(!isset($_GET['user_id'])){
    $redirectUrl = "404.html";
    header("HTTP/1.0 404 Not Found");
    print(file_get_contents($redirectUrl));
    exit;
  }
  $followed_id = strip_tags($_GET['user_id']);
  $user_id = intval($_SESSION['user_id']);
  try {
    $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // すでにフォローされてないか確認
    $stmt = $pdo->prepare("INSERT INTO follows (following_id, followed_id) VALUES (?,?)");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $followed_id, PDO::PARAM_INT);
    $stmt->execute();
    $redirect = "Location:profile.php?user_id=".$followed_id;
    header($redirect);
    exit();
  } catch (PDOException $e) {
    echo $e->getMessage();
    header("Content-Type: text/plain ; charset = utf8", true, 500);
  }
}
?>
