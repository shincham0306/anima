<?php
// try {
//   $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
//   $stmt = $pdo->query("SELECT * FROM users WHERE user_id = $user_id");
//   $user_row = $stmt->fetch(PDO::FETCH_ASSOC);
//
// } catch (PDOException $e) {
//   echo $e->getMessage();
//   header("Content-Type: text/plain ; charset = utf8", true, 500);
// }


 ?>
  <style media="screen">
    body{
      padding-top: 60px;
      background-color: whitesmoke;
    }
      @font-face{
          font-family: 'CheckPoint';
          src: url(./anima_fonts/CP_Revenge.otf);
      }
      @font-face{
          font-family: 'MarkerFelt';
          src: url(./anima_fonts/Marker Felt.ttf);
      }
      .anima{
          color: lightcoral;
          font-family: CheckPoint;
      }


  </style>


   <!-- ヘッダー部 -->
   <header>

   <nav class="navbar navbar-inverse navbar-fixed-top">
       <div class="container-fluid">
           <div class="nav-header">
               <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#anima_nav" aria-expanded="false" aria-controls="navbar">
                   <span class="sr-only">Toggle navigation</span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
               </button>
               <a class="navbar-brand anima" href="index.php">ANIMA</a>
           </div><!-- nav-header -->
           <div id="anima_nav" class="navbar-collapse collapse in" area-expanded="true">
               <ul class="nav navbar-nav navbar-left">
                   <li><a href="login.php">
                     <?php
                     // ログインしているかどうかで表示を変える
                     if (isset($_SESSION['user_id'])){
                       echo $user_name . "さんのアカウント";
                     }else{
                       echo "ログイン/新規登録";
                     }
                      ?>
                   </a></li>
                   <li><a href="
                     <?php
                     // ログインしているかどうかで表示を変える
                     if (isset($_SESSION['user_id'])){
                       echo "follow_view.php";
                     }else{
                       echo "login.php";
                     }
                      ?>
                     ">フォローしたアカウント</a></li>
                   <li class="post-window"><a href="post.php">投稿</a></li>

               <!-- <form class="nav navbar-form navbar-right">
                   <input type="text" class="form-control" placeholder="検索">
               </form> -->




           </div><!-- navbar -->
       </div><!-- container-fluid -->
   </nav>
   </header>
