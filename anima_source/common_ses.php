<?php

// ユーザーがログインしているかの確認
$user_id = $user_name = "";
session_start();
if(isset($_SESSION['user_id'])){
  $user_id = $_SESSION['user_id'];
  $user_name = $_SESSION['user_name'];
}

?>
