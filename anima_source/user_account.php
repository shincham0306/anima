<?php
// セッション
require_once("common_ses.php");

/*-----------------------------------------
ログインしてなければ、トップページへリダイレクトする
-------------------------------------------*/
if(!isset($_SESSION['user_id'])){
  header("Location:index.php");
}else{
  try {
    $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
    $stmt = $pdo->query("SELECT * FROM users WHERE user_id = $user_id");
    $user_row = $stmt->fetch(PDO::FETCH_ASSOC);
    // フォロー・フォロワー数の取得メソッド
    function getFollows($user_id){
      $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      // フォロー数の取得
      $following_st = $pdo->query("SELECT COUNT(*) AS num FROM follows WHERE following_id = $user_id");
      $following_row = $following_st->fetch();
      $following_number = $following_row['num'];
      // フォロワー数の取得
      $followed_st = $pdo->query("SELECT COUNT(*) AS num FROM follows WHERE followed_id = $user_id");
      $followed_row = $followed_st->fetch();
      $followed_number = $followed_row['num'];
      $follows = ["following" => $following_number, "followed" => $followed_number];
      return $follows;
    }
    // フォロー・フォロワーのユーザー情報
    function getFollowUser($user_id){
      $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      // フォローユーザーのデータ取得
      $following_user_st = $pdo->query("SELECT * FROM follows WHERE following_id = $user_id");
      $following_user_row = $following_user_st->fetch();
    }
    // ユーザーの投稿取得メソッド
    function getUserPost(){
      $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
      $user_id = $_SESSION['user_id'];
      $num_post_st = $pdo->query("SELECT COUNT(*) AS num_post FROM posts WHERE user_id = $user_id ");
      $num_post_row = $num_post_st->fetch(PDO::FETCH_ASSOC);
      if((int)$num_post_row['num_post'] === 0){
        $num_post = (int)$num_post_row['num_post'];
        echo "
        <div class=\"row\">
          <p>投稿はありません</p>
        </div>
        ";
      }else{
        $num_post = (int)$num_post_row['num_post'] - 1;
        for($i=0; $i <= $num_post ; $i++){
          $mypost_st = $pdo->query("SELECT * FROM posts WHERE user_id = $user_id ORDER BY post_id DESC LIMIT $i , 1");
          $post_row = $mypost_st->fetch();
          /*-----------------------
          投稿の表示
          ------------------------*/
          echo "
          <div class=\"text-center post_div\">
          <hr>
          <img class=\"postimage\" src=\" " . $post_row['media_path'] . "\" class=\"\">
              <h4 class=\"text-center\">" . $post_row['message'] . "</h4><br>
              ";
              // ポストIDの変数化
              $post_id = intval($post_row['post_id']);

              /*-----------------------
              コメントの表示
              ------------------------*/
           //    $comment_count_st = $pdo->prepare("SELECT COUNT(*) AS num FROM comments WHERE post_id = ?");
           //    $comment_count_st->bindValue(1, $post_id, PDO::PARAM_INT);
           //    $comment_count_st->execute();
           //    $comment_count_row = $comment_count_st->fetch();
           //    $comment_count = intval($comment_count_row['num']);
           //    if(0 < $comment_count):
           //      echo "
           //      <div class=\"comments panel-group\">
           //        <div class=\"panel panel-default\">
           //          <div class=\"panel-heading\">
           //            <h4 class=\"panel-title text-center\">
           //              <a data-toggle=\"collapse\" href=\"#collapse2\">コメントを表示</a>
           //            </h4>
           //          </div>
           //          <div id=\"collapse2\" class=\"text-right panel-collapse collapse\">
           //            <ul class=\"list-group\">
           //      ";
           //      $comment_count = $comment_count - 1;
           //      for($i=0; $i <= $comment_count; $i++):
           //        // $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
           //        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           //        // $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
           //        // コメントデータの取得
           //        $c_st = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? LIMIT ? , 1");
           //        $c_st->bindValue(1, $post_id, PDO::PARAM_INT);
           //        $c_st->bindValue(2, intval($i), PDO::PARAM_INT);
           //        $c_st->execute();
           //        $comment_row = $c_st->fetch();
           //        $commenter_id = intval($comment_row['user_id']);
           //        $commenter_st = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
           //        $commenter_st->bindValue(1, $commenter_id, PDO::PARAM_INT);
           //        $commenter_st->execute();
           //        $commenter_row = $commenter_st->fetch();
           //         echo "
           //         <li class=\"list-group-item\">
           //         <div class=\"text-center\">
           //         <img class=\"icon_image\" src=\"./profile_pictures/" . $commenter_id . "." . $commenter_row['pic_extension'] . "\" alt=\"\">
           //         ";
           //         if($commenter_id!==0):
           //           echo "
           //           <a href=\"profile.php?user_id=".$commenter_id."\">
           //           <p data-toggle=\"tooltip\" title=\"プロフィールを見る\" data-placement=\"bottom\">" . $commenter_row['user_name'] . "</p><br>
           //           </a>";
           //         else:
           //           echo "
           //           <p class=\"\">" . $commenter_row['user_name'] . "</p><br>
           //           ";
           //         endif;
           //         echo "
           //           <p class=\"text-center\">" . $comment_row['message'] . "</p><br>
           //           <p class=\"text-right\">" . $comment_row['created_at'] . "</p><br>
           //         <div>
           //         ";
           //       endfor;
           //       echo "
           //       </li>
           //       </ul>
           //     </div>
           //   </div>
           // </div>
           //       ";
           //
           //     endif;

               echo "
              <p class=\"text-right\">" . $post_row['created_at'] . "</p><br>
              <p class=\"text-right\"><a href=\"post_delete.php?post_id=".$post_id."\">投稿の削除</a></p><br>";

          //     echo "
          //     <div class=\"text-right\"><a href=\"comment.php?post_id=".$post_row['post_id']."\">コメントする</a></div>
          // </div>
          // ";
        }
      }

    }


  } catch (PDOException $e) {
    echo $e->getMessage();
    header("Content-Type: text/plain ; charset = utf8", true, 500);
  }
}
/*----------
自分の投稿の表示
-----------*/


/*----------
ログアウト処理
-----------*/
if(isset($_POST['logout'])){
  $_SESSION = array();
  header("Location:index.php");
  exit();
}

header("Content-Type: text/html ;charset = utf8");
 ?>

   <!-- ヘッダ -->
 <?php  require_once("navbar.php"); ?>

   <div class="container text-center">

       <!---
       メインプロフィール
                --->
       <h3 class="anima"><?php echo $user_row['user_name'] . "さんのプロフィール" ?></h3>
       <div class="main-profile">
         <div><?php echo "<img class=\"circle\" src=\"./profile_pictures/" . $user_id . "." . $user_row['pic_extension'] . "\" alt=\"\">" ?></div>
           <h4><?php echo "<span class=\"anima\">ひとこと</span>:".$user_row['description']."<br>"; ?></h4>
           <h5><?php echo $user_row['created_at']."からANIMAを利用しています"?></h5>
       </div>
       <!-- メインプロフィール終了 -->


       <?php $follows = getFollows($user_id); ?>
         <!---

         フォロー・フォロワーパネル！

         --->
         <div class="col-xs-6">
           <!-- フォローパネル -->
           <p><div class="comments panel-group">
             <div class="panel panel-default">
               <div class="panel-heading">
                 <h4 class="panel-title text-center">
                   <a data-toggle="collapse" href="#following"><?php echo  "<span class=\"anima\">フォロー</span>:".$follows['following'] ?></a>
                 </h4>
               </div>
               <div id="following" class="text-center panel-collapse collapse">
                 <ul class="list-group">
                   <?php
                   $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
                   $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                   // フォローユーザーのデータ取得
                   $following_number = $follows['following']-1;
                   if($following_number>=0){
                     for($i = 0; $i <= $following_number; $i++):
                       try {
                         $following_id_st = $pdo->query("SELECT * FROM follows WHERE following_id = $user_id LIMIT $i, 1");
                         $following_id_row = $following_id_st->fetch();
                         $following_id = $following_id_row['followed_id'];
                         $following_user_st = $pdo->query("SELECT * FROM users WHERE user_id = $following_id");
                         $following_user = $following_user_st->fetch();
                       } catch (PDOException $e) {
                         echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage(). "</p>";
                       }?>

                       <li class="list-group-item">
                         <img class="icon_image" src=<?php echo "./profile_pictures/" . $following_user['user_id'] . "." . $following_user['pic_extension']?>><br>
                         <a href="<?php echo "profile.php?user_id=".$following_user['user_id'] ?>">
                           <?php echo $following_user['user_name'] ?>
                         </a>
                       </li>
               <?php endfor;
             }else{
               echo "<li class=\"list-group-item\">フォローは0人です</li>";
             }?>
                 </ul>
               </div>
             </div>
           </div></p>
         </div>
           <!-- フォロワーパネル -->
          <div class="col-xs-6">
           <p><div class="comments panel-group">
             <div class="panel panel-default">
               <div class="panel-heading">
                 <h4 class="panel-title text-center">
                   <a data-toggle="collapse" href="#followed"><?php echo  "<span class=\"anima\">フォロワー</span>:".$follows['followed'] ?></a>
                 </h4>
               </div>
               <div id="followed" class="text-center panel-collapse collapse">
                 <ul class="list-group">
                   <?php
                   $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
                   $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                   // フォローユーザーのデータ取得
                   $followed_number = $follows['followed']-1;
                   if($followed_number>=0){
                     for($i = 0; $i <= $followed_number; $i++):
                       try {
                         $followed_id_st = $pdo->query("SELECT * FROM follows WHERE followed_id = $user_id LIMIT $i, 1");
                         $followed_id_row = $followed_id_st->fetch();
                         $followed_id = $followed_id_row['followed_id'];
                         $followed_user_st = $pdo->query("SELECT * FROM users WHERE user_id = $followed_id");
                         $followed_user = $followed_user_st->fetch();
                       } catch (PDOException $e) {
                         echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage(). "</p>";
                       }?>

                       <li class="list-group-item">
                         <img class="icon_image" src=<?php echo "./profile_pictures/" . $followed_user['user_id'] . "." . $followed_user['pic_extension']?>><br>
                         <a href="<?php echo "profile.php?user_id=".$followed_user['user_id'] ?>">
                           <?php echo $followed_user['user_name']; ?>
                         </a>
                       </li>
              <?php   endfor;
            }else{
              echo "<li class=\"list-group-item\">フォロワーは0人です。</li>";
            }?>
                 </ul>
               </div>
             </div>
           </div></p>
         </div>

       </div>
       <h4 class="anima text-center">↓投稿↓</h4>
       <div class="">
         <?php
         getUserPost();
          ?>
       </div>



   </div>



<script type="text/javascript">
  function confirmFunction1(){
    confirm("本当にアカウント削除しますか？");
  }
</script>

<!--htmlフッタ-->
<?php require_once("html_footer.php") ?>
