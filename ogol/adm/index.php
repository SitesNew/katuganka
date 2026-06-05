<hr>
<?
// print "-------------------------------------------------"."<br>";
// require_once("sec_mod.php");
?>
<head><link rel="stylesheet" href="../style.css" type="text/css"></head>
<title>Панель додавання нового оголошення</title>
<b>Панель додавання нового оголошення :</b>
<form action="add.php" method="POST" name="news">
Дата оголошення ( у виді  ЧЧ.MM.РР):<br>
<input type="text" size="50" name="data" value="<? $time = date ("d.m.y"); print "$time" ?>" /><br>
Заголовок оголошення :<br>
<input type="text" size="50" name="zag" /><br>
Текст оголошення :<br>
<textarea name="mess" cols="50" rows="10" wrap="ON"></textarea>
<br>
<input type="submit" value="Додати оголошення" name="submit" />
<input type="reset"   value="Очистити поле" name="submit" />
</form>
<? 
$time = date ("G:i"); 
$date = date ("d.m.y");
print "Поточні дата та час на сервері : ".$date. " р.  ".$time." год ."."<br>";
// print "-------------------------------------------------";
//  create www.kinyabulatov.info
?>
<?php require_once("include_options.php");?>
<hr>
