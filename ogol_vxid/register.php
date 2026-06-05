<?php
	require_once('common.php');

	if (isset($_POST['submitBtn'])){
		// Get user input
		$username  = isset($_POST['username']) ? $_POST['username'] : '';
		$password1 = isset($_POST['password1']) ? $_POST['password1'] : '';
		$password2 = isset($_POST['password2']) ? $_POST['password2'] : '';
        
		// Try to register the user
		$error = registerUser($username,$password1,$password2);
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
<?php if ((!isset($_POST['submitBtn'])) || ($error != '')) {?>
      <div class="caption">Реєстрація нового користувача <br>для додававння нового Оголошення : </div>
      <div id="icon">&nbsp;</div>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="registerform">
        <table width="100%">
          <tr><td align = right >Користувач : </td><td> <input class="text" name="username" type="text"  /></td></tr>
          <tr><td align = right >Пароль : </td><td> <input class="text" name="password1" type="password" /></td></tr>
          <tr><td align = right >Повторіть Пароль : </td><td> <input class="text" name="password2" type="password" /></td></tr>
          <tr><td colspan="2" align="center"><input class="text" type="submit" name="submitBtn" value="Реєстрація" /></td></tr>
        </table>  
      </form>
     
<?php 
}   
    if (isset($_POST['submitBtn'])){

?>
      <div class="caption">Результат реєстрації :</div>
      <div id="icon2">&nbsp;</div>
      <div id="result">
        <table width="100%"><tr><td><br/>
<?php
	if ($error == '') {
		echo "Користувач : $username був успішно зареєстрований !<br/><br/>";
		echo ' <a href="login.php">Ви вже можете ввійти в сайт тут</a>';
		
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
