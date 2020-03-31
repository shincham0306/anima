<?php


?>

    <style media="screen">
    .wall{
      margin-left: 100px;
      width: 120px;
      height: 80px;
      background: lightgrey;
      position: relative;
      -moz-border-radius: 10px;
      -webkit-border-radius: 10px;
      border-radius: 10px;
    }
    .wall:before{
      content: "";
      position: absolute;
      right: 100%;
      top: 10px;
      width: 0;
      height: 0;
      border-top: 13px solid transparent;
      border-right: 26px solid lightgrey;
      border-bottom: 13px solid transparent;

    }
    .wall-text{
      padding: 10px;
    }

    </style>
    <title></title>
  </head>
  <body>
    <h1>hello</h1>

    <div class="container">
      <div class="row">

          <div class="wall">
            <p class="wall-text"></p><br>
          </div>
      </div>
    </div>

    <!--htmlフッタ-->
    <?php require_once("html_footer.php") ?>
