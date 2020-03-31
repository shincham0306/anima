<?php
// セッション
require_once("common_ses.php");

// snsデータベースと接続
$pdo = new PDO("mysql:dbname=sns","root");

// 投稿の表示
$posts_count_st = $pdo->query("SELECT * FROM posts_data ORDER BY post_id DESC LIMIT 1");
$posts_count_row = $posts_count_st->fetch();
$posts_count = $posts_count_row['post_id'];

for ($i=$posts_count;$i>0;$i--){
  $posts_display_st = $pdo->query("SELECT * FROM posts_data WHERE post_id =$i");
  $posts_display_row = $posts_display_st->fetch();
  // user_idの取得
  $posts_user_id = $posts_display_row['user_id'];

  // 投稿ユーザーのユーザデータ取得
  $posts_userdata_st = $pdo->query("SELECT * FROM users WHERE user_id = $posts_user_id");
  $posts_userdata_row = $posts_userdata_st->fetch();

  // ユーザーネームの取得
  $posts_username = $posts_userdata_row['user_name'];
  // ユーザーのアイコン
  $posts_user_icon = $posts_userdata_row['profile_picture_path'];

  // 投稿写真のパス
  $posts_image_path = $posts_display_row['media_path_01'];
}

header("Content-Type: text/html ;charset = utf8");
 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <link rel="stylesheet" href="../css/bootstrap.css">
   <script type="text/javascript" src="../js/jquery-3.4.1.js"></script>
   <script type="text/javascript" src="../js/bootstrap.js"></script>
   <title>Document</title>
 </head>
 <body>
   <!-- ヘッダ -->
 <?php  require_once("navbar.php"); ?>
 </body>
 </html>
