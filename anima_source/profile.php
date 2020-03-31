<?php
// セッション
require_once("common_ses.php");

$profile_id = intval(strip_tags($_GET['user_id']));

try {
  $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
  $profile_st = $pdo->query("SELECT * FROM users WHERE user_id = $profile_id");
  $profile_row = $profile_st->fetch();
} catch (PDOException $e) {
  echo $e->getMessage();
  header("Content-Type: text/plain ; charset = utf8", true, 500);
}

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

function getProfile($profile_id){
  try {
      $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
      $num_post_st = $pdo->query("SELECT COUNT(*) AS num_post FROM posts WHERE user_id = $profile_id");
      $num_post_row = $num_post_st->fetch(PDO::FETCH_ASSOC);
      if(intval($num_post_row['num_post']) === 0){
        $num_post = intval($num_post_row['num_post']);
        echo "
        <div class=\"row\">
          <p>投稿はありません</p>
        </div>
        ";
      }else{
        $num_post = intval($num_post_row['num_post']) - 1;
        for($i=0; $i <= $num_post ; $i++){
          $mypost_st = $pdo->query("SELECT * FROM posts WHERE user_id = $profile_id ORDER BY post_id DESC LIMIT $i , 1");
          $post_row = $mypost_st->fetch();
          /*-----------------------
          投稿の表示
          ------------------------*/
          echo "
          <div class=\"text-center post_div\">
          <hr>
          <img class=\"postimage\" src=\" " . $post_row['media_path'] . "\" class=\"\">
              <h4 class=\"text-center\">" . $post_row['message'] . "</h4><br>
              <p class=\"text-right\">" . $post_row['created_at'] . "</p><br>
          </div>
          ";
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


header("Content-Type: text/html ;charset = utf8");
 ?>

   <!-- ヘッダ -->
 <?php  require_once("navbar.php"); ?>

   <div class="container text-center">

       <!---
       メインプロフィール
                --->
       <h3 class="anima"><?php echo $profile_row['user_name'] . "さんのプロフィール" ?></h3>
       <div class="main-profile">
         <div><?php echo "<img class=\"circle\" src=\"./profile_pictures/" . $profile_id . "." . $profile_row['pic_extension'] . "\" alt=\"\">" ?></div>
           <h4><?php echo "<span class=\"anima\">ひとこと</span>:".$profile_row['description']."<br>"; ?></h4>
           <h5><?php echo $profile_row['created_at']."からANIMAを利用しています"?></h5>
       </div>
       <!-- メインプロフィール終了 -->

       <?php
       if(!isset($_SESSION['user_id'])):
         echo "<a href=\"login.php\">".$profile_row['user_name']."さんをフォローする</a>";
       else:
         try {
           $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
           $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
           // すでにフォローされてないか確認
           $exist_st = $pdo->query("SELECT COUNT(*) AS num from follows WHERE following_id = $user_id AND followed_id = $profile_id");
           $exist_row = $exist_st->fetch();
           $num = intval($exist_row['num']);
           if($num !== 0){
             echo $profile_row['user_name']."さんをフォロー中";
           }else{
             echo "<a href=\"follow.php?user_id=".$profile_id."\">".$profile_row['user_name']."さんをフォローする</a>";
           }
         } catch (PDOException $e) {
           echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage(). "</p>";
         }
       endif;?>

       <!--

       フォロー・フォロワーパネル
                         --->
                         <?php $follows = getFollows($profile_id); ?>
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
                                         $following_id_st = $pdo->query("SELECT * FROM follows WHERE following_id = $profile_id LIMIT $i, 1");
                                         $following_id_row = $following_id_st->fetch();
                                         $following_id = $following_id_row['followed_id'];
                                         $following_user_st = $pdo->query("SELECT * FROM users WHERE user_id = $following_id");
                                         $following_user = $following_user_st->fetch();
                                         if(!$following_user){
                                           echo "<li class=\"list-group-item\">このアカウントは削除されました</li>";
                                           continue;
                                         }
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
                         <!-- フォローパネル終了 -->

                         <!-- フォロワーパネル -->
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
                                       $followed_id_st = $pdo->query("SELECT * FROM follows WHERE followed_id = $profile_id LIMIT $i, 1");
                                       $followed_id_row = $followed_id_st->fetch();
                                       $followed_id = $followed_id_row['following_id'];
                                       $followed_user_st = $pdo->query("SELECT * FROM users WHERE user_id = $followed_id");
                                       $followed_user = $followed_user_st->fetch();
                                       if(!$followed_user){
                                         echo "<li class=\"list-group-item\">このアカウントは削除されました</li>";
                                         continue;
                                       }
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
                       <!-- フォロワーパネル終了 -->


                     <!---

                     フォロー・フォロワーパネル終了

                     --->




       <div class="">
         <?php
         getProfile($profile_id);
          ?>
       </div>


   </div>

   <!--htmlフッタ-->
   <?php require_once("html_footer.php") ?>
