<?php
// 一旦auto_loginを削除
if(!empty($_COOKIE['auto_login'])){
  $this->delete_auto_login($_COOKIE['auto_login']);
}

// 新たにauto_loginをセット
if(!empty($auto_login)){
  $this->setup_auto_login($user_name);
}

/*------------------------------------------------
オートログイン　セットアップ
--------------------------------------------------*/
public function setup_auto_login($user_name)
{
  $cookieName = 'auto_login';
  $auto_login_key = sha1(uniqid(). mtrand( 1,999999999) . '_auto_login');
  $cookieExpire = time() + 3600 * 24 * 7; // 7日間
	$cookiePath = '/';
	$cookieDomain = $_SERVER['SERVER_NAME'];

  /*
	$sql = "
	INSERT INTO auto_login ( user_name , auto_login_key )
	VALUES ( :user_name , :auto_login_key )";
	*/


}

 ?>
