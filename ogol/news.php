<?php

  $xfile =file("db.dat"); // путь где находиться файл с базой новостей здесь она находить в том же каталоге что скрипт обработчик
$num = count($xfile); // подсчет новостей в базе
$max= $num - 1; 
$udata = explode("|",$xfile[$max]); // определяем последнюю новость
   print "<p>$udata[0] г.<br/><b>$udata[1]</b><br/>$udata[2]</p>"; // вывод последней новости (Здесь можно отредактировать дизайн и оформление выводного текста)
   $udata = explode("|",$xfile[$max-1]); // определяем предпоследнюю новость
   print "<p>$udata[0] г.<br/><b>$udata[1]</b><br/>$udata[2]</p>";
   $udata = explode("|",$xfile[$max-2]); // определяем третью с конца новость
   print "<p>$udata[0] г.<br/><b>$udata[1]</b><br/>$udata[2]</p>";
   $udata = explode("|",$xfile[$max-3]); // определяем четвертую с конца новость
   print "<p>$udata[0] г.<br/><b>$udata[1]</b><br/>$udata[2]</p>";
 
 //  create www.kinyabulatov.info 
?>
<div align="right"><a href="allnews.php">Читать все</a>


 
<?php require_once("include_options.php");?>
