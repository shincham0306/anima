<?php
require_once("common_ses.php");

header("Content-Type: text/html ;charset = utf8");

function getPost($user_id){
// 投稿の表示の処理
  try {
    $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
    // データベースに接続する前にエラーを表示
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ポスト数の取得
    $stmt = $pdo->query("SELECT COUNT(*) AS num FROM posts");
    $row = $stmt->fetch();
    $post_count = intval($row['num']) - 1;
    for($i=0; $i <= $post_count; $i++):
      $pdo = new PDO("mysql:dbname=sns;host=localhost;charset=utf8", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      // ポストデータの取得
      $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY post_id DESC LIMIT ?, 1");
      $stmt->bindValue(1, intval($i), PDO::PARAM_INT);
      $stmt->execute();
      $post_row = $stmt->fetch();
      $post_user_id = intval($post_row['user_id']);
      // ユーザーデータの取得
      $u_st = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
      $u_st->bindValue(1, $post_user_id, PDO::PARAM_INT);
      $u_st->execute();
      $user_row = $u_st->fetch();

      /*-----------------------
      投稿の表示
      ------------------------*/
        echo "
        <div class=\"col-sm-12 post_div\">
            <hr>
            <img class=\"icon_image\" src=\"./profile_pictures/" . $post_user_id . "." . $user_row['pic_extension'] . "\" alt=\"\">
            ";
            if($post_user_id!==0):
              if($post_user_id === $user_id){
                echo "
                <a href=\"user_account.php\">
                <p class=\"\" data-toggle=\"tooltip\" title=\"プロフィールを見る\" data-placement=\"bottom\">" . $user_row['user_name'] . "</p><br>
                </a>";
              }else{
                echo "
                <a href=\"profile.php?user_id=".$post_user_id."\">
                <p class=\"\" data-toggle=\"tooltip\" title=\"プロフィールを見る\" data-placement=\"bottom\">" . $user_row['user_name'] . "</p><br>
                </a>";
              }
            else:
              echo "
              <p class=\"\" >" . $user_row['user_name'] . "</p><br>
              ";
            endif;
            echo "
            <img class=\"postimage\" src=\" " . $post_row['media_path'] . "\" class=\"\">
            <h4 class=\"text-center\">" . $post_row['message'] . "</h4><br>
            <p class=\"text-right\">" . $post_row['created_at'] . "</p><br>";

            /*-----------------------
            コメントの表示
            ------------------------*/
         //    $post_id = intval($post_row['post_id']);
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

         /*-----------
         コメントの処理終わり
         -------------*/

            // コメントボタンの表示
        //     echo "
        // <div class=\"text-right\"><a href=\"comment.php?post_id=".$post_row['post_id']."\">コメントする</a></div>";
        echo "
        </div>
        ";
      endfor;

  } catch (PDOException $e) {
    echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage(). "</p>";
  }
}


 ?>


  <!-- ヘッダ -->
  <?php require_once("navbar.php"); ?>

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
                         <h1 class="anima mb-7" style="font-family:CheckPoint;">動物の写真投稿サイトANIMA！</h1>
                         <h4 class="text-muted">可愛い動物のための写真投稿サイト。</h4><br>
                         <h4 class="text-muted">犬、猫、小動物までなんでもOK!</h4><br>
                         <h3 class="anima">↓みんなの投稿！↓</h3>
                     </div>
                     <?php getPost($user_id); ?>



                   </div>
               </div>
           </div>
       </div>


</div><!-- container-fluid -->


<script type="text/javascript">
  $('[data-toggle="tooltip"]').tooltip();
</script>

<!--htmlフッタ-->
<?php require_once("html_footer.php") ?>
