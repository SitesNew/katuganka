<?php
require_once('common.php');

$error = '0';

if (isset($_POST['submitBtn'])){
	// Get user input
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';
        
	// Try to login the user
	$error = loginUser($username,$password);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
   <title>Micro Login System</title>
   <link href="style/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="main">
<?php if ($error != '') {?>
      <div class="caption">Для додавання нового Оголошення <br>ввійдіть в сайт :</div>
      <div id="icon">&nbsp;</div>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="loginform">
        <table width="100%" border = 1 >
          <tr><td align = right >Користувач : </td><td> <input class="text" name="username" type="text"  /></td></tr>
          <tr><td align = right >Пароль : </td><td> <input class="text" name="password" type="password" /></td></tr>
          <tr><td colspan="2" align="center"><input class="text" type="submit" name="submitBtn" value="Вхід" /></td></tr>
        </table>  
      </form> 
      
      &nbsp;<a href="register.php">Реєстрація нового користувача</a>
      
<?php 
}   
    if (isset($_POST['submitBtn'])){

?>
      <div class="caption">Результат входу в сайт :</div>
      <div id="icon2">&nbsp;</div>
      <div id="result">
        <table width="100%"><tr><td><br/>
<?php
	if ($error == '') {
		echo "Шановний  $username  <br/> ми Вас впізнали ! <br/><br/>";
		echo '<a href="index.htmlallogol_add.html" target = _top >Ввести нове Оголошення Ви можете тут !</a>';
	}
	else echo $error;

?>
		<br/><br/><br/></td></tr></table>
	</div>
<?php            
    }
?>
	<div id="source"> ---------- </div>
    </div>
</body>   
