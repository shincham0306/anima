<?php
$animes = [
    0 => ["animeId" => 1, "animeName" => "狼と香辛料"],
    1 => ["animeId" => 2, "animeName" => "宝石の国"],
    2 => ["animeId" => 3, "animeName" => "宝石の国"],
    3 => ["animeId" => 4, "animeName" => "AAA"]
];

$stores = [
  0 => ["name" => "a", "animeName" => "宝石の国"],
  1 => ["name" => "b", "animeName" => "不死鳥の城"]
];

$animeName = $stores[0]['animeName'];
echo $animeName."<br>";
$result = array_keys($animes, "宝石の国");
var_dump($result);
var_dump(count($animes));
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <link rel="stylesheet" href="../css/bootstrap.css">
  <script type="text/javascript" src="../js/jquery-3.4.1.js"></script>
  <script type="text/javascript" src="../js/bootstrap.js"></script>
  <body>
    <style media="screen">
      comments{
        width: 30%;
      }
    </style>
    <nav class="navbar navbar-dark bg-dark"></nav>
    
    
    <div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    ドロップダウン
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
    <li><a href="#">メニュー1</a></li>
    <li><a href="#">メニュー2</a></li>
    <li><a href="#">メニュー3</a></li>
    <li><a href="#">メニュー4</a></li>
  </ul>
</div>
<div class="comments panel-group">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title text-right">
        <a data-toggle="collapse" href="#collapse2">click me</a>
      </h4>
    </div>
    <div id="collapse2" class="text-right panel-collapse collapse">
      <ul class="list-group">
        <li class="list-group-item">Three</li>
      </ul>
    </div>
  </div>
</div>
<button type="button" class="btn btn-primary" data-toggle="tooltip" title="Tooltip message">
  Tooltip
</button>
<p>ああああ<span class="text-danger" data-toggle="tooltip" title="Tooltip message">ああ</span></p>
<script type="text/javascript">
  $('[data-toggle="tooltip"]').tooltip();
</script>
  </body>
</html>
echo "<p class=\"text-danger\">DBのエラー:".$e->getMessage(). "</p>";
