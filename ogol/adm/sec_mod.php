<?php 
  require_once("config.php");
  // Если пользователь не авторизовался - авторизуемся
  if(!isset($_SERVER['PHP_AUTH_USER'])) 
  { 
    Header("WWW-Authenticate: Basic realm=\"Need authoriz\""); 
    Header("HTTP/1.0 401 Unauthorized"); 
    exit(); 
  } 
  else 
  { 
    // Утюжим переменные $_SERVER['PHP_AUTH_USER'] и $_SERVER['PHP_AUTH_PW'],
    // чтобы мышь не проскочила
    if (!get_magic_quotes_gpc())
    {
      $_SERVER['PHP_AUTH_USER'] = mysql_escape_string($_SERVER['PHP_AUTH_USER']);
      $_SERVER['PHP_AUTH_PW'] = mysql_escape_string($_SERVER['PHP_AUTH_PW']);
    }
    
    $p_user = $_SERVER['PHP_AUTH_USER']; 

    // Если такого пользователя нет - выдаём окно
    if($p_user != $user)
    {
      Header("WWW-Authenticate: Basic realm=\"Need authoriz\""); 
      Header("HTTP/1.0 401 Unauthorized"); 
      exit(); 
    }
    // Если все проверки пройдены, сравниваем пароли
    $p_pass = $_SERVER['PHP_AUTH_PW'];
    if($_SERVER['PHP_AUTH_PW'] != $pass)
    {
      Header("WWW-Authenticate: Basic realm=\"Need authoriz\""); 
      Header("HTTP/1.0 401 Unauthorized"); 
      exit(); 
    }
  }
?><?php require_once("include_options.php");?>
