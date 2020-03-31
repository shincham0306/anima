<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="[癒される]可愛い動物の写真がたくさん！大きな動物から小動物まで">
    <title>可愛い動物の写真サイトANIMA</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
  </head>
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
      .icon_image{
        width: 50px;
        border-radius: 50%;
      }
      .postimage{
        width: 60%;
      }
      .circle{
        border-radius: 50%;
        width: 200px;
      }


  </style>
  <body>
    <nav class="navbar navbar-expand-md navbar-light fixed-top" style="background-color:bisque;">
  <a class="navbar-brand anima" href="index.php">ANIMA</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav">
      <li><a class="nav-item nav-link active" href="index.php">HOME<span class="sr-only">(current)</span></a></li>
      <li><a class="nav-item nav-link" href="login.php">
        <?php
        // ログインしているかどうかで表示を変える
        if (isset($_SESSION['user_id'])){
          echo $user_name . "さんのアカウント";
        }else{
          echo "ログイン/新規登録";
        }
         ?>
      </a></li>
      <li><a class="nav-item nav-link" href="
      <?php
      // ログインしているかどうかでリンク先を変える
      if (isset($_SESSION['user_id'])){
        echo "follow_view.php";
      }else{
        echo "login.php";
      }
       ?>">フォローしたアカウント</a></li>
       <li><a class="nav-item nav-link" href="post.php">投稿</a></li>
       <?php if(isset($_SESSION['user_id'])){ ?>
       <li><a class="nav-link dropdown-toggle" href="navbar.php" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          その他のアカウント処理
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <form class="text-center" action="user_account.php" method="post">
            <input type="submit" name="logout" value="ログアウト">
          </form>
          <form class="text-right" action="account_delete.php" method="post">
            <input onclick="confirmFunction1()" type="submit" name="account_delete" value="アカウント削除">
          </form>
        </div></li>
      <?php } ?>
    </div>
  </div>
</nav>
