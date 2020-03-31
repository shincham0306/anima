<?php

class Processing {


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
    echo "<img class = \"circle\" src=" .$resize_path . " alt=\"表示できません\">";
  }
  // getResized終了
}



 ?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <style media="screen">
  </style>
  <body>
    <?php
    if(isset($_POST['submit'])){
      if(is_uploaded_file($_FILES['image']['tmp_name'])){
        $file = $_FILES['image']['tmp_name'];
        $newfile = "./practice/".$_FILES['image']['name'];
        move_uploaded_file($file, $newfile);
        $resize_process = new Processing();
        $resize_process->getResized($newfile);
      }
    }
     ?>

  </body>
</html>
 <form class="" action="resize.php" method="post" enctype="multipart/form-data">
   <input type="file" name="image" value="">
   <input type="submit" name="submit" value="送信">
 </form>
