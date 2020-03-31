<?php
require_once("/opt/lampp/htdocs/petty/common_ses.php");

header("Content-Type: text/html ;charset = utf8");

// 投稿の表示の処理
  try {
    $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
    // データベースに接続する前にエラーを表示
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ポスト数の取得
    $stmt = $pdo->query("SELECT COUNT(*) AS num FROM posts");
    $row = $stmt->fetch();
    $post_count = $row['num'];
    $post_num = $post_count - 1;
      $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      // ポストデータの取得
      $stmt = $pdo->query("SELECT * FROM posts ORDER BY post_id DESC");
      $post_row = $stmt->fetch();

      // ユーザーデータの取得
      $u_st = $pdo->query("SELECT * FROM users");
      $user_row = $u_st->fetch();

      /*-----------------------
      投稿の表示
      ------------------------*/
        // echo "
        // <div class=\"col-sm-12 post_div\">
        //     <hr>
        //     <img class=\"icon_image\" src=\"./profile_pictures/" . $post_user_id . "." . $user_row['pic_extension'] . "\" alt=\"\">
        //     <p class=\"\">" . $user_row['user_name'] . "</p><br>
        //     <img class=\"postimage\" src=\" " . $post_row['media_path'] . "\" class=\"\">
        //     <h4 class=\"text-center\">" . $post_row['message'] . "</h4><br>
        //     <a class=\"text-right\" href=\"comment.php?post_id=".$post_row['post_id']."\">コメントする</a>
        //     <p class=\"text-right\">" . $post_row['created_at'] . "</p><br>
        // </div>
        // ";
  } catch (PDOException $e) {
    echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage(). "</p>";
  }

 ?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="../css/bootstrap.css">
  <script type="text/javascript" src="../js/jquery-3.4.1.js"></script>
  <script type="text/javascript" src="../js/bootstrap.js"></script>
  <style media="screen">
    body{
      padding-top: 60px;
    }
    .postimage{
      width: 60%;
    }
    .icon_image{
      width: 50px;
      border-radius: 50%;
    }
  </style>
  <script type="text/javascript">

  </script>
  <title>home</title>
</head>
<body>
  <!-- ヘッダ -->
  <?php require_once("/opt/lampp/htdocs/petty/navbar.php"); ?>

   <div class="container-fluid">

       <div class="col-sm-12 col-md-12 main text-center">
           <!-- メインコンテンツ -->
           <div class="main text-center">
               <div class="container-fluid">
                   <div class="row">
                     <!--    <div class="clearfix"></div>-->
                         <!-- <div class="col-lg-3 sidebar position-fixed">
                             <ul class="nav nav-sidebar">
                                 <li class="active"><a href="">料理</a></li>
                                 <li><a href="">アクセス</a></li>
                                 <li><a href="">皆様のご意見</a></li>
                                 <li><a href="/questionnaire">アンケート</a></li>
                             </ul>
                         </div> -->
                     <div id="desc" class="col-sm-12 text-center">
                         <h1 class="text-danger mb-5">動物の写真投稿サイトPetty！</h1>
                         <p class="text-muted">可愛い動物のための写真投稿サイト。</p><br>
                         <p class="text-muted">犬、猫、小動物までなんでもOK!</p><br>
                         <h3>↓みんなの投稿！↓</h3>
                     </div>

                     <!--投稿の表示 -->
                     <?php for($i = 0; $i <= $post_num; $i++) :
                             $present_post_row = $post_row[$i];
                             $user_id = $present_post_row['user_id'];
                             $user_present_row = $user_row[array_search($user_id, array_column($user_row, "user_id"))];

                      ?>
                     <div class=\"col-sm-12 post_div\">
                       <hr>
                       <img class="icon_image" src="./profile_pictures/""." . $user_row['pic_extension'] . <?php echo "./profile_pictures/". ?>>
                       <p class=""><?php echo $user_row['user_name'] ?></p><br>
                       <img class=\"postimage\" src=\" " . $post_row['media_path'] . "\" class=\"\">
                       <h4 class=\"text-center\">" . $post_row['message'] . "</h4><br>
                       <a class=\"text-right\" href=\"comment.php?post_id=".$post_row['post_id']."\">コメントする</a>
                       <p class=\"text-right\">" . $post_row['created_at'] . "</p><br>
                     </div>
                   <?php endfor ; ?>
                   </div>
               </div>
           </div>
       </div>
     <div class="row">
       <footer class="text-center text-muted py-4">
           Copyright ©️ 2019 muu.com
       </footer>
     </div>

</div><!-- container-fluid -->



</body>
</html>
