<?
require_once("config.php");
// Перевірка заголовка Оголошення на пустоту 
$zag = trim($zag);  
if ($zag == '') {
                  print ' Заголовок   Оголошення  '. $zag.'  НЕ Введено !!! '.'<br>';
                  print ' Вам необхідно ввести заголовок Вашого Оголошення '.'<br>';
                  print ' Поверніться на попередню сторінку '.'<br>';
                  exit;
}
// Перевірка тексту Оголошення на пустоту 
$zag = trim($zag);  
if ($mess == '') {
                  print ' Текст Оголошення    '. $mess.'  НЕ Введено !!! '.'<br>';
                  print ' Вам необхідно ввести текст Вашого Оголошення '.'<br>';
                  print ' Поверніться на попередню сторінку '.'<br>';
                  exit;
}
//
?>
<head><link rel="stylesheet" href="../style.css" type="text/css"></head>
<title>Панель додавання оголошення</title>
<b>З панелі додавання оголошення :</b><hr>
<?php

$text = "$data|$zag|$mess";
print ' <br>';
print '                                         Отже :  '.'<br>';
print '          Дата Вашого  Оголошення  :   '. $data.'<br>';
print ' Заголовок  Вашого  Оголошення  :  '. $zag.'<br>';
print '        Текст  Вашого  Оголошення  :  '. $mess.'<br>';



 //  Убрать 13, 10: перенос и возврат каретки
$text=str_replace(chr(13),'',$text);
$text=str_replace(chr(10),'',$text);
// $text = str_replace("\n", "", $text);
 

// strip_tags — Удаляет HTML и PHP-теги из строки
$text=strip_tags($text);

$file=file("../db.dat"); // путь к базе
$counter=count($file);          

if ($counter == "1000") {  $fp = @fopen("../db.dat","w+"); @fclose($fp); }

$fp=@fopen("../db.dat","a"); fputs($fp,"$text \r\n"); @fclose($fp);

print '<center><br><br><br><br><br>Ваше оголошення успішно додано !<br><a href="'.$_SERVER['HTTP_REFERER'].'">На попередню сторінку</a></center>'
?>
<?php require_once("include_options.php");?>
<hr>
