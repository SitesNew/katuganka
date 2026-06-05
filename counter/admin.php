<? // WR-Counter v 1.5 UTF-8  //  12.01.19 г.  //  Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);
ini_set('register_globals','off');// Все скрипты написаны для этой настройки php

include "data/config.php";

$back="<center>Вернитесь <a href='javascript:history.back(1)'><B>назад</B></a>"; // Удобная строка

$skey="56754"; // Секретный ключ НЕ МЕНЯТЬ !!! 
$adminname="admin"; $adminpass=$password; // Авторизация

function replacer2 ($text) { // ФУНКЦИЯ очистки кода
$text=str_replace("&#032;",' ',$text);
$text=str_replace(">",'&gt;',$text);
$text=str_replace("<",'&lt;',$text);
$text=str_replace("\"",'&quot;',$text);
$text=preg_replace("/\n\n/",'<p>',$text);
$text=preg_replace("/\n/",'<br>',$text);
$text=preg_replace("/\\\$/",'&#036;',$text);
$text=preg_replace("/\r/",'',$text);
$text=preg_replace("/\\\/",'&#092;',$text);
// если magic_quotes включена - чистим везде СЛЭШи в этих случаях: одиночные (') и двойные кавычки ("), обратный слеш (\)
if (get_magic_quotes_gpc()) { $text=str_replace("&#092;&quot;",'&quot;',$text); $text=str_replace("&#092;'",'\'',$text); $text=str_replace("&#092;&#092;",'&#092;',$text); }
$text=str_replace("\r\n","<br> ",$text);
$text=str_replace("\n\n",'<p> ',$text);
$text=str_replace("\n",'<br> ',$text);
$text=str_replace("\t",'',$text);
$text=str_replace("\r",'',$text);
$text=str_replace('   ',' ',$text);
return $text; }


function unreplacer2 ($text) { // ФУНКЦИЯ замены спецсимволов конца строки на обычные
$text=str_replace("&lt;br&gt;","<br>",$text);
$text=str_replace("&#124;","|",$text);
return $text;}


// Выбран ВЫХОД - очищаем куки
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrcounter","",time()-3600); Header("Location: index.php"); exit; } }

if (isset($_COOKIE['wrcounter'])) { // Сверяем имя/пароль из КУКИ с заданным в конфиг файле
$text=$_COOKIE['wrcounter'];
$text=trim($text); // Вырезает ПРОБЕЛьные символы 
if (strlen($text)>60) exit("Попытка взлома - длина переменной куки сильно большая!");
$text=replacer2($text);
$exd=explode("|",$text); $name1=$exd[0]; $pass1=$exd[1];

if (($name1!=$adminname) or ($pass1!=$adminpass)) {sleep(1); setcookie("wrcounter", "0", time()-3600); Header("Location: admin.php"); exit;}

} else { // ЕСЛИ ваще нету КУКИ

if (isset($_POST['name']) & isset($_POST['pass'])) { // Если есть переменные из формы ввода пароля
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']);
$text="$name|$pass|";
$text=trim($text); // Вырезает ПРОБЕЛьные символы 
if (strlen($text)<4) exit("$back Вы не ввели имя или пароль!");
$text=replacer2($text);
$exd=explode("|",$text); $name=$exd[0]; $pass=$exd[1];

//$qq=md5("$pass+$skey"); exit("$qq"); // РАЗБЛОКИРУЙТЕ для получения MD5 своего пароля!

// Сверяем введённое имя/пароль с заданным в конфиг файле
$tektime=time();
// присваиваются куки АДМИНИСТРАТОРУ
if ($name==$adminname & md5("$pass+$skey")==$adminpass) {$wrcounter="$adminname|$adminpass|$tektime|"; setcookie("wrcounter", $wrcounter, time()+18000); Header("Location: admin.php"); exit;}
exit("Ваши данные <B>ОШИБОЧНЫ</B>!</center>");

} else { // если нету данных, то выводим ФОРМУ ввода пароля

echo '<html><head><META HTTP-EQUIV="Pragma" CONTENT="no-cache"><META HTTP-EQUIV="Cache-Control" CONTENT="no-cache"><META content="text/html; charset=UTF-8" http-equiv=Content-Type><style>input, textarea {font-family:Verdana; font-size:12px; text-decoration:none; color:#000000; cursor:default; background-color:#FFFFFF; border-style:solid; border-width:1px; border-color:#000000;}</style></head><body>
<BR><BR><BR><center>
<table border=#C0C0C0 border=1 cellpadding=3 cellspacing=0 bordercolor=#959595>
<form action="admin.php" method=POST name=pswrd>
<TR><TD bgcolor=#C0C0C0 align=center>Администрирование скрипта</TD></TR>
<TR><TD align=right>Введите логин: <input size=17 name=name value=""></TD></TR>
<TR><TD align=right>Введите пароль: <input type=password size=17 name=pass></TD></TR>
<TR><TD align=right>';

print"<TR><TD align=center><input type=submit style='WIDTH: 120px; height:20px;' value='Войти'>
<SCRIPT language=JavaScript>document.pswrd.name.focus();</SCRIPT></TD></TR></table>
<BR><BR><center><small>Powered by <a href=\"https://www.wr-script.ru\" title=\"Скрипт счётчика\" class='copyright'>WR-Counter</a>  &copy; 1.5<br></small></center></body></html>";
exit;}

} // АВТОРИЗАЦИЯ ПРОЙДЕНА!

$gbc=$_COOKIE['wrcounter']; $gbc=explode("|", $gbc); $gbname=$gbc[0];$gbpass=$gbc[1];$gbtime=$gbc[2];
if ($gbname==$adminname) $ktotut="1"; else $ktotut="2"; // Кто вошёл: админ или модер?



function prcmp2 ($a, $b) {if ($a==$b) return 0; if ($a<$b) return -1; return 1;}



// Блок используется для удаления ПОДПИСЧИКА рассылки
if(isset($_GET['xduser'])) {
if ($_GET['xduser'] =="") {print"произошёл глюк-переглюк :-("; exit;}

$xduser=$_GET['xduser']-1;
$file=file("database.php"); $i=count($file);
if ($xduser<"1") {print "$back. 1 строкая является защитной! Её <B>НЕЛЬЗЯ УДАЛЯТЬ!</B>"; exit;}
if ($i<"3") {print "$back. Необходимо оставить хотябы <B>ОДНОГО</B> участника!"; exit;}
// удаляем строку с участником
$fp=fopen("database.php","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xduser) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
@chmod("database.php", 0644);
Header("Location: admin.php?event=statv"); exit; }





$shapka="<html><head>
<title>Панель администрировния. Счётчик WR-Counter 1.5 UTF-8</title>
<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">
<META HTTP-EQUIV=\"Cache-Control\" CONTENT=\"no-cache\">
<META content='text/html; charset=UTF-8' http-equiv=Content-Type>
<style>
BODY {FONT-FAMILY: Verdana}

a {text-decoration: underline; color: #000000;}
a:visited {text-decoration: underline; color: #000000;}
a:hover, a:active {text-decoration: underline; color: #FF9C00;}

A.about_menu {TEXT-DECORATION: none}
A.about_menu:hover {COLOR: #996600}
A.pagesLine {COLOR: #006600}
A.menu {COLOR: #666666; TEXT-DECORATION: none}
A.menu:hover {COLOR: #009900; TEXT-DECORATION: none}

.maininput {FONT-SIZE: 12px; WIDTH: 200px; font-size: 10; color: 000000; border: #808080 1 solid;}
.simpleok {WIDTH: 50px; height:18px; background-color: cccccc; font-size: 10; color: 000000; font-weight: bold; border: #808080 1 solid;}
.longok {WIDTH: 100px; height:20px; background-color: cccccc; font-size: 10; color: 000000; border: #808080 1 solid;}

input, textarea {font-family:Verdana; font-size:12px; text-decoration:none; color:#000000; cursor:default; background-color:#FFFFFF; border-style:solid; border-width:1px; border-color:#707070;}

.small {FONT-SIZE: 11px;}
.smallest {FONT-SIZE: 9px;}

TD {FONT-SIZE: 11px}
TD.menu {FONT-SIZE: 11px; FONT-WEIGHT: bold}
TD.big_item_title {FONT-SIZE: 13px; FONT-WEIGHT: bold}
TD.pagesLine {FONT-SIZE: 10px}

#copyright {FONT-SIZE: 10px; font-color: #666666}
</STYLE>
</head>
<body bgcolor=\"#F3F3F3\"><center>

<table width=100% cellpadding=1 cellspacing=0 border=1 bordercolor=#666666>
<TR height=30><TD align=center class=big_item_title>
<b>
<a href='admin.php?event=makeform'>Код счётчика</a> :: 
<a href='admin.php?event=config'>Конфигурация</a> :: 
<a href='admin.php?event=statv'>Статистика хиты/хосты</a> :: 
<a href='admin.php?event=seebasa'>Экспорт статистики в Excel/Calc</a> ::
<a href='admin.php?event=clearcooke'>Выход</a> :: 
</td></tr>
<tr><td width=100%>
";


// ничего не выбрано
if(!isset($_GET['event'])) { print"$shapka <BR><BR><center><h3>Выберете действие в верхнем меню.</h2><BR><BR></TD></TR></TABLE>"; }  // if !isset($event')



	
// Вывод формы, которую необходимо установить для пописки
else  { if ($_GET['event'] == "makeform") {

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$cntrlurl="https://$host$self";
$cntrlurl=str_replace("/admin.php","",$cntrlurl);

print"$shapka <center><br><BR>Код счётчика для вставки на любую страницу Вашего сайта, в том числе html<BR>
<form><textarea rows=5 cols=70>
<a href=\"$cntrlurl/info.php\"><img src=\"$cntrlurl/counter.php\" width=88 height=31 border=0></a>
</textarea><BR><BR><br><BR></TD></TR></TABLE>";
}









if ($_GET['event']=="seebasa") { // Просмотр и ЭКСПОРТ СТАТИСТИКИ В Exclel / Calc с 11.2018


// ЭКСПОРТ 11.2018
if (isset($_GET['export'])) { $openfile=$_POST['openfile'];
if (!stristr($openfile, ".csv")) exit("Разрешён экспорт только содержимого базы скрипта (всех файлов с раширением csv!");
$records=file_get_contents("$coundir/$openfile");
iconv("UTF-8", "windows-1251",$records); // Преобразовываем в кодировку Windos-1251
//$records=str_replace(';',',',$records); если нужен разделитель запятая , то разкоментируйте
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=wr-counter-$openfile");
header("Content-Transfer-Encoding: Windows-1251");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
echo $records; exit;} // КОНЕЦ ЭКСПОРТА В CSV



print"<BR>$shapka";

// Выводим форму для ЭКСПОРТА
print"<BR><BR><form action='admin.php?event=seebasa&export' method=POST name=REPLIER>
<table align=center><tr><td class=row2>Экспортировать файл: </td><td class=row1><select class=input name=openfile>";
if ($handle=opendir($coundir)) {
while (($file=readdir($handle)) !== false)
if (!is_dir($file)) { 
$stroka=stristr($file, ".csv"); if (strlen($stroka)>"1") 
{ $marker=""; if (isset($_POST['openfile'])) { if ($_POST['openfile']===$file) $marker="selected"; }
print"<option $marker value=\"$file\">$file</option>"; }
} closedir($handle); } else echo'Ошибка!';
echo'</select></td><td><center><input type=submit value="ЭКСПОРТИРОВАТЬ файл"></form></td></tr></table>';

// Выводим форму для ПРОСМОТРА ФАЙЛА
print"<BR><BR><form action='admin.php?event=seebasa&see' method=POST name=REPLIER>
<table align=center><tr><td class=row2>Просмотреть содержимое файла: </td><td class=row1><select class=input name=openfile>";
if ($handle=opendir($coundir)) {
while (($file=readdir($handle)) !== false)
if (!is_dir($file)) { 
$stroka=stristr($file, ".csv"); if (strlen($stroka)>"1") 
{ $marker=""; if (isset($_POST['openfile'])) { if ($_POST['openfile']===$file) $marker="selected"; }
print"<option $marker value=\"$file\">$file</option>"; }
} closedir($handle); } else echo'Ошибка!';
echo'</select></td><td><center><input type=submit value="Просмотреть файл"></form></td></tr></table>';

if (isset($_POST['openfile'])) {

$openfile=$_POST['openfile'];
if (!stristr($openfile, ".csv")) exit("Разрешён просмотр только содержимого базы скрипта (всех файлов с раширением dat!");

$data=File("$coundir/$openfile");

echo "<b><i><h2><center>Содержимое файла \"$coundir/$openfile\"</b></i></h2>
* В первой строке указаны номера по порядку. Если Вам нужно считать в скрипте представленные данные, то здесь Вы можете быстро узнать их порядковый номер!
<table align=center border=0><tr>";

$dat_arr=explode(";",$data[0]);

for ($p=0;$p<count($dat_arr);$p++) echo "<td bgcolor=#04A2FF><center><b>$p</td>";
echo "</tr>";

for ($i=0;$i<count($data);$i++) {
    $data_array=explode(";", $data[$i]);
    echo "<tr>";
    for ($f=0;$f<count($data_array);$f++) echo "<td bgcolor=#AEE1FF><center><b>$data_array[$f] &nbsp;</td>";
    echo "</tr>";
}

echo "</table></center></form>
</body>
</html>";

} // if isset $_GET['openfile']
}	









if ($_GET['event']=="statv")  {  // просмотр всей СТАТИСТИКИ
print"<BR>$shapka"; include("info.php"); exit;}






if ($_GET['event'] =="config")   {  // КОНФИГУРИРОВАНИЕ - выбор настроек
// Получаем цвета для отображения
$s1=dechex($s1r); $s1.=dechex($s1g); $s1.=dechex($s1b);
$s2=dechex($s2r); $s2.=dechex($s2g); $s2.=dechex($s2b);
$s3=dechex($s3r); $s3.=dechex($s3g); $s3.=dechex($s3b);
if ($image=="counter1.png") {$i1="checked";} else {$i1="";}
if ($image=="counter2.png") {$i2="checked";} else {$i2="";}
if ($image=="counter3.png") {$i3="checked";} else {$i3="";}
if ($image=="counter4.png") {$i4="checked";} else {$i4="";}
if ($image=="counter5.png") {$i5="checked";} else {$i5="";}
if ($image=="counter6.png") {$i6="checked";} else {$i6="";}
if ($image=="counter7.png") {$i7="checked";} else {$i7="";}
if ($image=="counter8.png") {$i8="checked";} else {$i8="";}

if ($sendstat=="1") {$m1="checked"; $m2="";} else {$m2="checked"; $m1="";}
if ($gtype=="1") {$g1="checked"; $g2="";} else {$g2="checked"; $g1="";}
print "$shapka 
<BR><table border=1 width=750 align=center cellpadding=3 cellspacing=0 bordercolor=#DDDDDD class=forumline><tr bgcolor=#BBBBBB height=25 align=center>
<td><B>Переменная</B></td><td><B>Значение</B></td></tr><form action='admin.php?event=confignext' method=post name=REPLIER>
<tr><td class=row2>Пароль администратора *</td><td class=row1><input name=password type=hidden value='$password'><input class=post type=text value='скрыт' maxlength=20 name=newpassword size=15> (зашифрован и скрыт)</td></tr>
<tr><td>Емайл админа</td><td><input type=text value='$adminemail' name=adminemail size=30></tr></td>
<tr><td>Мылить статистику админу? Периодичность?</td><td><input type=radio name=sendstat value=\"1\"$m1> да&nbsp; <input type=radio name=sendstat value=\"0\"$m2> нет
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=text value='$sendday' maxlength=2 name=sendday size=2> (число от 1 до 99 дней)</tr></td>
<tr><td>Тип графика</td><td><input type=radio name=gtype value=\"1\"$g1> вертикальный&nbsp; <input type=radio name=gtype value=\"0\"$g2> горизонтальный</tr></td>
<tr><td>Коэффициент масштабирования<BR> графика ХИТОВ / ХОСТОВ ?</td><td><input type=text value='$scale1' maxlength=5 name=scale1 size=5> &nbsp;&nbsp;&nbsp; .:. &nbsp;&nbsp;&nbsp; <input type=text value='$scale2' name=scale2 maxlength=5 size=5> &nbsp; &nbsp; По умолчанию: <B><U>0.5</U> и <U>2</U></B>.</tr></td>
<tr><td>Сколько суток хранить статистику?</td><td><input type=text value='$days' name=days maxlength=2 size=5> (рекомендую месяц - 30 дней)</tr></td>
<tr><td>Путь к папке с данными</td><td><input type=hidden value='$coundir' name=coundir maxlength=20 size=15> &nbsp; &nbsp; По умолчанию: &quot<B><U>./data</U></B>&quot.</tr></td>";

// Определяем размер БД с 11.2018 г.
$bdsize=0; $itogofiles=0;
if ($handle=opendir($coundir)) 
{while (($file=readdir($handle))!==false)
	if (!is_dir($file)) {$itogofiles++; $bdsize=$bdsize+filesize("$coundir/$file");}
closedir($handle);}
$bdsize=round($bdsize/1048576,1);
$razmerbd="файлы статистики: <B>$bdsize MБ.</B> [$itogofiles файлов]";


print"<tr><td>Занято на диске</td><td>$razmerbd</td></tr>
<tr><td>файл с рисунком счётчика</td><td>
<input type=radio name=image value='counter1.png' $i1><img src='images/counter1.png'> &nbsp;
<input type=radio name=image value='counter2.png' $i2><img src='images/counter2.png'> &nbsp;
<input type=radio name=image value='counter3.png' $i3><img src='images/counter3.png'> &nbsp;
<input type=radio name=image value='counter4.png' $i4><img src='images/counter4.png'> <BR>
<input type=radio name=image value='counter5.png' $i5><img src='images/counter5.png'> &nbsp;
<input type=radio name=image value='counter6.png' $i6><img src='images/counter6.png'> &nbsp;
<input type=radio name=image value='counter7.png' $i7><img src='images/counter7.png'> &nbsp;
<input type=radio name=image value='counter8.png' $i8><img src='images/counter8.png'> &nbsp;
</tr></td>

<tr><td>RGB цвет 1-й цифры на счётчике</td><td><input type=text value='$s1r' name=s1r maxlength=3 size=3> <input type=text value='$s1g' name=s1g maxlength=3 size=3> <input type=text value='$s1b' name=s1b maxlength=3 size=3> &nbsp;&nbsp;&nbsp; (число от 001 до 255) Так выглядит: <B><font color='$s1'>1234567890</font></B></tr></td>
<tr><td>RGB цвет 2-й цифры на счётчике</td><td><input type=text value='$s2r' name=s2r maxlength=3 size=3> <input type=text value='$s2g' name=s2g maxlength=3 size=3> <input type=text value='$s2b' name=s2b maxlength=3 size=3> &nbsp;&nbsp;&nbsp; (число от 001 до 255) Так выглядит: <B><font color='$s2'>1234567890</font></B></tr></td>
<tr><td>RGB цвет 3-й цифры на счётчике</td><td><input type=text value='$s3r' name=s3r maxlength=3 size=3> <input type=text value='$s3g' name=s3g maxlength=3 size=3> <input type=text value='$s3b' name=s3b maxlength=3 size=3> &nbsp;&nbsp;&nbsp; (число от 001 до 255) Так выглядит: <B><font color='$s3'>1234567890</font></B></tr></td>

<TR><TD>Пример работы счётчика: </TD><TD><a href='info.php' target='_blank'><img src='counter.php' width='88' height='31' border=0></a></TD></TR>

<tr><td colspan=2><BR><center><input type=submit value='Сохранить конфигурацию'>
</form></td></tr></table><center><br>* Если хотите изменить пароль - сотрите слово <B>\"скрыт\"</B> и введите новый пароль.<br> Рекомендую использовать тольео буквы и/или цифры.<br><br></td></tr></table>";
}


// Конфигурирование ШАГ 2 - сохранение данных
if ($_GET['event'] =="confignext")  {

// обработка полей пароль админа/модератора
if (strlen($_POST['newpassword'])<1) exit("$back разрешается длина пароля МИНИМУМ 1 символ!");
if ($_POST['newpassword']!="скрыт") {$pass=trim($_POST['newpassword']); $_POST['password']=md5("$pass+$skey");}

$configdata="<? // WR-Counter v 1.5 UTF-8  //  24.12.18 г.  //  Miha-ingener@yandex.ru\r\n".
"$"."password=\"".$_POST['password']."\"; // Пароль админа защифрован md5()\r\n".
"$"."adminemail=\"".$_POST['adminemail']."\"; // Емайл админа\r\n".
"$"."sendstat=\"".$_POST['sendstat']."\"; // Мылить статистику посещений админу?\r\n".
"$"."sendday=\"".$_POST['sendday']."\"; // Периодичность рассылки статистики\r\n".
"$"."gtype=\"".$_POST['gtype']."\"; // Тип графика - горизонтальный / вертикальный ( 1/0 )\r\n".
"$"."days=\"".$_POST['days']."\"; // Сколько суток хранить статистику?\r\n".
"$"."scale1=\"".$_POST['scale1']."\"; // Коэффициент масштабирования графика ХИТОВ\r\n".
"$"."scale2=\"".$_POST['scale2']."\"; // Коэффициент масштабирования графика ХОСТОВ\r\n".
"$"."coundir=\"".$_POST['coundir']."\"; // Путь к папке с данными счётчика '.' - текущая папка\r\n".
"$"."image=\"".$_POST['image']."\"; // имя файла с рисунком счётчика\r\n".
"$"."s1r=\"".$_POST['s1r']."\"; $"."s1g=\"".$_POST['s1g']."\"; $"."s1b=\"".$_POST['s1b']."\"; // RGB - 1-й цифры\r\n".
"$"."s2r=\"".$_POST['s2r']."\"; $"."s2g=\"".$_POST['s2g']."\"; $"."s2b=\"".$_POST['s2b']."\"; // RGB - 2-й цифры\r\n".
"$"."s3r=\"".$_POST['s3r']."\"; $"."s3g=\"".$_POST['s3g']."\"; $"."s3b=\"".$_POST['s3b']."\"; // RGB - 3-й цифры\r\n".
"$"."date=date(\"d.m.Y\"); // число.месяц.год\r\n".
"$"."time=date(\"H:i:s\"); // часы:минуты:секунды\r\n?>";

$file=file("data/config.php");
$fp=fopen("data/config.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
fputs($fp,$configdata);
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }



} // if isset $event




print"<BR><small>Сегодня <b>$date г.</b></small>";

?>
</td></tr></table></td></tr></table>
<center><small>Powered by <a href="https://www.wr-script.ru/" target="_blank">WR-Counter</a> 1.5 UTF-8 &copy;<br /></small></center>
</body></html>
