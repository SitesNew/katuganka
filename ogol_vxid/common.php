<?php

session_start();

function registerUser($user,$pass1,$pass2){
	$errorText = '';
	
	// Check passwords
	if ($pass1 != $pass2) $errorText = "Ваш Пароль на сайті не знайдено !";
	elseif (strlen($pass1) < 6) $errorText = "Пароль закороткий <br>він повинен бути <br>довжиною більше 5 символів !";
	
	// Check user existance	
	$pfile = fopen("userpwd.txt","a+");
    rewind($pfile); 

    while (!feof($pfile)) {
        $line = fgets($pfile);
        $tmp = explode(':', $line);
        if ($tmp[0] == $user) {
            $errorText = "Вибране імя користувача приймається !";
            break;
        }
    }
	
    // If everything is OK -> store user data
    if ($errorText == ''){
		// Шифрувати пароль Користувача 
		// $userpass = md5($pass1);

		// НЕ Шифрувати пароль Користувача 
		$userpass = $pass1;
    	
		fwrite($pfile, "\r\n$user:$userpass");
    }
    
    fclose($pfile);
	
	
	return $errorText;
}

function loginUser($user,$pass){
	$errorText = '';
	$validUser = false;
	
	// Check user existance	
	$pfile = fopen("userpwd.txt","r");
    rewind($pfile);

    while (!feof($pfile)) {
        $line = fgets($pfile);
        $tmp = explode(':', $line);
        if ($tmp[0] == $user) {
            // Користувача знайдено, перевірити пароль
            // Перевірити зашифрований  пароль
            // if (trim($tmp[1]) == trim(md5($pass))){
            // Перевірити НЕ зашифрований  пароль
           if (trim($tmp[1]) == trim($pass)){
            	$validUser= true;
            	$_SESSION['userName'] = $user;
            }
            break;
        }
    }
    fclose($pfile);

    if ($validUser != true) $errorText = "Невірне імя Користувача або його Пароль !";
    
    if ($validUser == true) $_SESSION['validUser'] = true;
    else $_SESSION['validUser'] = false;
	
	return $errorText;	
}

function logoutUser(){
	unset($_SESSION['validUser']);
	unset($_SESSION['userName']);
}

function checkUser(){
	if ((!isset($_SESSION['validUser'])) || ($_SESSION['validUser'] != true)){
		header('Location: login.php');
	}
}

?>
