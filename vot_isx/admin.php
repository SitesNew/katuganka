 <? // WR-Golos v 1.5  // 05.12.2018 г. // Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);
ini_set('register_globals','off'); // Все скрипты написаны для этой настройки php

include "data/config.php";

$antispam=FALSE; // нет в этом скрипте модуля антиспам
$adminname="admin"; // логин администратора
$back="<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><meta http-equiv='Content-Language' content='ru'></head><body><center>Вернитесь <a href='javascript:history.back(1)'><B>назад</B></a>"; // Удобная строка

$skey="54332"; // Секретный ключ НЕ МЕНЯТЬ !!! 
$adminpass=$password; // Авторизация

function replacer ($text) { // ФУНКЦИЯ очистки кода
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


// Выбран ВЫХОД - очищаем куки 11-11-18
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { 
$url="http".(($_SERVER['SERVER_PORT']==443)?"s":"")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; $forum_url=str_replace('admin.php?event=clearcooke','',"$url");
setcookie("wrgolos","",time()-3600); Header("Location: $forum_url"); exit; } }

if (isset($_COOKIE['wrgolos'])) { // Сверяем имя/пароль из КУКИ с заданным в конфиг файле
$text=$_COOKIE['wrgolos'];
$text=trim($text); // Вырезает ПРОБЕЛьные символы 
if (strlen($text)>60) exit("Попытка взлома - длина переменной куки сильно большая!");
$text=replacer($text);
$exd=explode("|",$text); $name1=$exd[0]; $pass1=$exd[1];
if (($name1!=$adminname and $name1!=$modername) or ($pass1!=$adminpass and $pass1!=$moderpass)) {sleep(1); setcookie("wrgolos", "0", time()-3600); Header("Location: admin.php"); exit;}

} else { // ЕСЛИ ваще нету КУКИ

if (isset($_POST['name']) & isset($_POST['pass'])) { // Если есть переменные из формы ввода пароля
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']);
$text="$name|$pass|";
$text=trim($text); // Вырезает ПРОБЕЛьные символы 
if (strlen($text)<4) exit("$back Вы не ввели имя или пароль!");
$text=replacer($text);
$exd=explode("|",$text); $name=$exd[0]; $pass=$exd[1];

//$msg_onpage=md5("$pass+$skey"); exit("$msg_onpage"); // РАЗБЛОКИРУЙТЕ для получения MD5 своего пароля!

// присваиваются куки АДМИНИСТРАТОРУ
$tektime=time();
if ($name==$adminname & md5("$pass+$skey")==$adminpass) {$wrgolos="$adminname|$adminpass|$tektime|"; setcookie("wrgolos", $wrgolos, time()+18000); Header("Location: admin.php"); exit;}
exit("Ваши данные <B>ОШИБОЧНЫ</B>!</center>");

} else { // если нету данных, то выводим ФОРМУ ввода пароля


// 11-11-2018 г. Новый блок авторизации
echo '<html><head><META HTTP-EQUIV="Pragma" CONTENT="no-cache"><META HTTP-EQUIV="Cache-Control" CONTENT="no-cache"><META content="text/html; charset=UTF-8" http-equiv=Content-Type><style>
body {background: #D5EAFF; font-family: "Roboto", sans-serif; font-size: 15px;}
.login-page {width: 350px;padding: 6% 0 0;margin: auto;}
.form button:hover,.form button:active,.form button:focus {background: #1CB5FF;}
.form .message {margin: 15px 0 0;color: #b3b3b3;font-size: 12px;}
.form .message a {color: #0080FF;text-decoration: none;}
.form {position: relative;z-index: 1;background: #FFFFFF;max-width: 350px;margin: 0 auto 100px;padding: 45px;text-align: center;box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);}
.form input {font-family: "Roboto", sans-serif;outline: 0;background: #f2f2f2;width: 100%;border: 0;margin: 0 0 15px;padding: 15px;box-sizing: border-box;font-size: 14px;}
.form button {font-family: "Roboto", sans-serif;text-transform: uppercase;outline: 0;background: #0080C0;width: 100%;border: 0;padding: 15px;color: #FFFFFF;font-size: 14px;-webkit-transition: all 0.3 ease;transition: all 0.3 ease;cursor: pointer;}
</style></head><body>
<div class="login-page">
<div class="form">
Авторизация: WR-Golos 1.5<BR><BR>
<form action="admin.php" method=POST name=pswrd>
<input type="text" name=name value="" placeholder="логин"/>
<input type="password" name=pass placeholder="пароль"/>';

print"<button>Войти</button><p class=\"message\">Проблемы при входе? <a href=\"admin.php?event=clearcooke\">Очистить КУКИ</a></p></form></div></div>
<SCRIPT language=JavaScript>document.pswrd.name.focus();</SCRIPT>
<center><small>Powered by <a href=\"https://www.wr-script.ru\" title=\"Скрипт голосования\" class='copyright'>WR-Golos</a> &copy; 1.5 UTF-8<br></small></center></body></html>";
exit;}





} // АВТОРИЗАЦИЯ ПРОЙДЕНА!



$shapka="<html><head>
<title>Голосование - Админка</title>
<META HTTP-EQUIV=Pragma\" CONTENT='no-cache'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'>
<META content='text/html; charset=windows-1251' http-equiv=Content-Type>
<STYLE>BODY {FONT-FAMILY: Verdana}
a {text-decoration: none; color: #000000;}
a:visited {text-decoration: underline; color: #000000;}
a:hover, a:active {text-decoration: underline; color: #FF9C00;}
input {FONT-SIZE: 12px; WIDTH: 70px; font-size: 13; color: 000000; border: #808080 1 solid;}
.text {FONT-SIZE: 12px; WIDTH: 700px; font-size: 13; color: 000000; border: #808080 1 solid;}
.midle {FONT-SIZE: 12px; WIDTH: 250px; font-size: 13; color: 000000; border: #808080 1 solid;}
.small {FONT-SIZE: 11px;}
TD {FONT-SIZE: 15px}
TD.big_item_title {FONT-SIZE: 16px; FONT-WEIGHT: bold}
</STYLE>
</head>
<body bgcolor=#F3F3F3><center>
<center><table width=100% cellpadding=1 cellspacing=0 border=1 bordercolor=#666666>
<TR height=30><TD align=center class=big_item_title>
<b><a href='admin.php'>Вид и код голосований</a> ::
<a href='admin.php?event=all'>Управление голосованиями</a> ::
<a href=admin.php?event=cfg>Конфигурирование</a> ::
<a href='admin.php?event=clearcooke'>Выход</a></B>
</td></tr>
<tr><td width=100%>
";




if(isset($_GET['delfile'])) { // Блок УДАЛЕНИЯ любого файла по маске

$id=$_GET['delfile']; if ((!ctype_digit($id)) or (strlen($id)>2)) exit("<B>Поддерживаются номера голосований от 1 до 99 включительно!!!</B>");
unlink ("$golosdir/ip$id.csv"); unlink ("$golosdir/vote$id.csv"); // удаляем файлы

Header("Location: admin.php?event=all"); exit;}



if (isset($_GET['event'])) {



if ($_GET['event']=="add") { // ДОБАВЛЕНИЕ голосования

if (isset($_POST['id'])) {$id=$_POST['id']; if ((!ctype_digit($id)) or (strlen($id)>2)) {exit("<B>Поддерживаются номера голосований от 1 до 99 включительно!!!</B>");}} else {$id=1;}

//$kolvo$i - количество поголосовавших на вопрос № $i
// $otv$i - ответ № $i
// $golositogo - итого голосований
// $toper - заголовок 

$golositogo=$_POST['golositogo']; $toper=$_POST['toper'];
$i=1; $itgo=0; $text="$toper;;\r\n";
do {
 $otv=$_POST["otv$i"]; $otv=str_replace(";",",",$otv); $otv=str_replace("\r\n","<br>",$otv);
 $kolvo=$_POST["kolvo$i"]; $kolvo=str_replace(";",",",$kolvo); $kolvo=str_replace("\r\n","<br>",$kolvo);
 if (strlen($otv)>2) {$itgo++; $text.="$otv;$kolvo;\r\n";}
 $i++;
} while ($i<$golositogo);

if ($itgo<1) {print"Должен быть хотябы ОДИН вариант ответа!"; exit;}

// создаём файл с голосованием
$fp=fopen("$golosdir/vote$id.csv","w");
flock ($fp,LOCK_EX);
fputs($fp,"$text");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
@chmod("$golosdir/vote$id.csv", 0644);

// создаём файл для записи IP-шников голосовавших
$fp=fopen("$golosdir/ip$id.csv","w");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
@chmod("$golosdir/ip$id.csv", 0644);

Header("Location: admin.php?id=$id"); exit; }





// КОНФИГУРИРОВАНИЕ - ввод и выбор данных
if ($_GET['event']=="cfg")  {
if ($ipblok=="1") {$sm1="checked"; $sm2="";} else {$sm2="checked"; $sm1="";}
if ($antiflud=="1") {$af1="checked"; $af2="";} else {$af2="checked"; $af1="";}
if ($sendmailadmin=="1") {$ma1="checked"; $ma2="";} else {$ma2="checked"; $ma1="";}
print "$shapka

<table width=100%><TR><TD>
<center><B><font size=+1>Конфигурирование</font></b>

<form action=admin.php?event=config method=POST name=REPLIER>
<table>
<tr><td>Логин и пароль администратора<BR> (данные входа в админ.панель со 100% набором прав)*</td><td>Логин: <input name=adminname type=text value='$adminname'> Пароль: <input name=password type=hidden value='$password'><input class=post type=text value='скрыт' maxlength=20 name=newpassword size=15></td></tr>
<!--<tr><td>Емайл админа / отсылать сообщения (функцинал настраивается и пока не работает)</td><td><input type=text value='$adminemail' class=midle name=adminemail size=30>&nbsp;&nbsp;&nbsp;&nbsp; <input type=radio name=sendmailadmin value=\"1\"$ma1> да&nbsp;&nbsp; <input type=radio name=sendmailadmin value=\"0\"$ma2> нет</tr></td>-->
<tr><td>Запретить голосовать более раза с одного IP</td><td><input type=radio name=ipblok value=\"1\"$sm1> да&nbsp; <input type=radio name=ipblok value=\"0\"$sm2> нет</tr></td>
<tr><td>Задействовать АНТИФЛУД / время в секундах</td><td><input type=radio name=antiflud value=\"1\"$af1> да&nbsp;&nbsp; <input type=radio name=antiflud value=\"0\"$af2> нет &nbsp;&nbsp;&nbsp;&nbsp; <input type=text value='$fludtime' name=fludtime maxlength=3 size=10> * Рекомендую ставить от 10 до 30 сек.</tr></td>
<tr><td>Максимальное количество голосований от 0 до 99 включительно</td><td><input type=text value='$glsnum' name=glsnum size=15 maxlength=2></tr></td>
<tr><td>Папка с данными</td><td><input type=hidden value='$golosdir' name=golosdir size=20 class=midle> $golosdir</tr></td>
<tr><td colspan=2><center><table><tr><td><input type=submit value='Сохранить конфигурацию' class=text></form></td></tr></table>
</td></tr></table>

</TD></TR></TABLE>
</TD></TR></TBODY></TABLE>";
}



if ($_GET['event']=="config")  {

// обработка полей пароль админа
if (strlen($_POST['newpassword'])<1) exit("$back разрешается длина пароля МИНИМУМ 1 символ!");
if ($_POST['newpassword']!="скрыт") {$pass=trim($_POST['newpassword']); $_POST['password']=md5("$pass+$skey");}

$configdata="<? // WR-Golos v 1.5  //  05.12.2018 г.  //  Miha-ingener@yandex.ru\r\n".
"$"."adminname=\"".$_POST['adminname']."\"; // Логин администратора\r\n".
"$"."password=\"".$_POST['password']."\"; // Пароль администратора зашифрован md5() с ключём \r\n".
"$"."adminemail=\"".$_POST['adminemail']."\"; // Емайл админа\r\n".
"$"."sendmailadmin=\"".$_POST['sendmailadmin']."\"; // Отправлять сообщения с новыми объявлениями админу?\r\n".
"$"."antiflud=\"".$_POST['antiflud']."\"; // задействовать АНТИФЛУД (увеличивает надёжность и защиту от взлома)\r\n".
"$"."fludtime=\"".$_POST['fludtime']."\"; // Антифлуд-время\r\n".
"$"."glsnum=\"".$_POST['glsnum']."\"; // Сколько разрешить разных голосований (от 1 до 99 включительно)\r\n".
"$"."ipblok=\"".$_POST['ipblok']."\"; // Запретить голосовать более раза с одного IP 0/1\r\n".
"$"."golosdir=\"".$_POST['golosdir']."\"; // папка с данными\r\n".
"$"."date=date(\"d.m.Y\"); // число.месяц.год\r\n".
"$"."time=date(\"H:i:s\"); // часы:минуты:секунды \r\n?>";

$file=file("$golosdir/config.php");
$fp=fopen("$golosdir/config.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
fputs($fp,$configdata);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

Header("Location: admin.php"); exit; }






if ($_GET['event']=="all")  { // показываем ВСЕ голосования


print "$shapka <BR><center>";


$i=0;
if ($handle = opendir($golosdir)) {
while (($file = readdir($handle)) !== false)
if (!is_dir($file) and $file!=".htaccess") {$lines[$i]=$file; $i++;}
closedir($handle);
} else {print 'В папке, которую вы указали нет голосований!';}

sort($lines); $itogo=count($lines); $k=0; $text=null;

print"<font size=+1>Имеющиеся голосования (номер голосования, название, кол-во ответов)<br><br><div align=left><UL><UL><UL><table>";
$i=0; $max=0;
do {
if (preg_match("/vote/",$lines[$i])) {
$vline=file("$golosdir/$lines[$i]");
$vcount=count($vline)-1;
$lin=str_replace("vote","",$lines[$i]);
$lin=str_replace(".csv","",$lin);
$vline[0]=str_replace(";;","",$vline[0]);
print"<TR><td width=10 bgcolor=#FF2244><B><a href='admin.php?delfile=$lin' title='УДАЛИТЬ' onclick=\"return confirm('Будет удалено голосование и ВСЕ РЕЗУЛЬТАТЫ! Удалить? Уверены?')\" >.X.</a></B></td>";
print"<td>$lin: <B><a href='admin.php?id=$lin'>$vline[0]</a></B> - <small>$vcount (вариантов ответа)</small></td></tr>";
if ($lin>$max) $max=$lin; }



$i++;
} while ($i<$itogo);

if ($max<=$glsnum) {$max++; print"</table><br><UL><B><a href='admin.php?id=$max'>Добавить новое голосование № $max</a></B><br></UL>";} else {print"<br><UL>Больше голосований добавить нельзя. Вы привысили ограничение = $glsnum голосов. заданное в настройках!!!</UL>";}

print"</font><br><br>
В файле index.php приведена функция и пример вызова функции для инклюдинга на Ваши страници.<BR>
В самом простом варианте скрипт для вызова будет таким:<BR><BR>

&lt;?<BR>
include(\"golos/index.php\");<BR>
golos(\"1\",\"fongolos-1\");  // выводим голосование № 1<BR>
?&gt;<BR><BR>

* В Конфигурировании есть такой пункт: <B>Номер голосования от 1 до 99 включительно</B>.<br>
Этот номер как раз м соответствует указанному выше номеру.<br>
Максимальное кол-во голосований равно $glsnum.<br><br>
<br>";
}

} // if isset['event']






// ГЛАВНАЯ СТРАНИЦА

else { print "$shapka <BR>";

if (isset($_GET['id'])) {$id=$_GET['id']; if ((!ctype_digit($id)) or (strlen($id)>2)) {exit("<B>Поддерживаются номера голосований от 1 до 99 включительно!!!</B>");}} else $id=1;

if (is_file("$golosdir/vote$id.csv")) { // Если файл с голосование существует, то открываем его


// считываем все имеющиеся голосования
$i=0; if ($handle = opendir($golosdir)) {
while (($file = readdir($handle)) !== false)
if (!is_dir($file) and $file!=".htaccess") {$records[$i]=$file; $i++;}
closedir($handle);
sort($records); $itogo=count($records); $k=0; $text=null;
$i=0;

print"<font size=+1>Имеющиеся голосования:<br><br><div align=left><UL><UL><UL>";
$i=0; $max=0; $rnd=mt_rand(1,3);
do {
if (preg_match("/vote/",$records[$i])) {
$vline=file("$golosdir/$records[$i]");
$vcount=count($vline)-1;
$lin=str_replace("vote","",$records[$i]);
$lin=str_replace(".csv","",$lin);
$vline[0]=str_replace(";;","",$vline[0]);
print"$lin: <B><a href='admin.php?id=$lin'>$vline[0]</a></B> - <small>$vcount (вариантов ответа)</small>, картинка fongolos-$rnd.jpg (обновите для выбора другой)<br>";
if ($lin>$max) $max=$lin; }
$i++;
} while ($i<$itogo);
}



$lines=file("$golosdir/vote$id.csv");
$itogo=count($lines); $i=1; $glmax=0;

// Считаем общее кол-во голосов
do {$dt=explode(";",$lines[$i]); $glmax=$glmax+$dt[1]; $i++; } while($i<$itogo); $i=1; $all=$glmax;

$vdt = explode(";",$lines[0]);
print"<h4 align=center>Голосование № $id</h4><TABLE align=center cellPadding=3 align=center border=0><TBODY><TR><TD vAlign=top align=middle><h4>Текущий вид</h4><br>
<TABLE border=0 bgcolor=navy cellSpacing=1 cellPadding=0 align=center><TR><TD>
<TABLE border=0 bgcolor=#ffffff cellSpacing=0 cellPadding=1 align=center border=0>
<TR><TD colspan=3 align=middle bgColor=#FFFFFF><FONT face=arial size=2><B>&nbsp;$vdt[0]&nbsp;</B></FONT></TD></TR>
<TR><TD><TABLE border=0 cellSpacing=0 cellPadding=2 width=100%><TBODY>";

do {$dt=explode(";",$lines[$i]);
if ($glmax==0) {$glmax=0.1;}
$glpercent=round(10000*$dt[1]/$glmax)/100;
$hcg=round($glpercent);
if ($glpercent<1) {$hcg=1;} if ($glpercent>100) {$hcg=100;}
print"<TR>
<TD width=25>&nbsp;</TD><TD><B>&nbsp;$dt[0]</B></TD>
<TD><FONT face=arial size=2><B>&nbsp;$dt[1]</B></FONT></TD>
<TD>(<B>$glpercent</B> %)</TD>
<TD><table border=0><TR><TD height=11 width=$hcg bgcolor=navy></TD></TR></TABLE>

</TR>";
$i++;
} while($i<$itogo);


print"<TR>
<TD>&nbsp;</TD><TD>Итого проголосовало:</B></TD>
<TD><FONT face=arial size=2><B>&nbsp;$all</B></FONT></TD>
</TR>
</FORM></TBODY></TABLE>
</TBODY></TABLE>
</TBODY></TABLE>

</TD><TD><h4 align=center>Редактирование</h4><table border=0><form action=admin.php?event=add method=POST name=REPLIER>";
$i=0;
do {
if (isset($lines[$i])) {$dt=explode(";",$lines[$i]);} else {$dt[0]=""; $dt[1]="0";}
if ($i==0) {print"<tr><td><B>Название голосования:</B></td><td><input type=text value='$dt[0]' name=toper size=15 class=midle></td><td width=30 align=center>отве- тов</td></tr>";
   } else {print"<TR>
   <TD align=right>$i ответ:</TD><TD><input type=text value='$dt[0]' name='otv$i' size=15 class=midle></B></TD>
<TD><input type=text value='$dt[1]' name='kolvo$i' maxlength=2 size=15></TD></TR>";}
$i++;
} while($i<11);
print"<TR><TD colspan=3 align=center><input type=hidden name=golositogo value='$i'><input type=hidden name=id value='$id'><input class=midle type=submit value='Редактировать'></TD></TR></table></form>
* оставьте поля пустыми, если хотите создать голосование с меньшим кол-вом ответов.
</TD></TR><TR><TD colspan=2 align=center>";




print"<h4>Код для установки на html-страницу</h4>";

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$golosurl="http://$host$self";
$golosurl=str_replace("/admin.php", "", $golosurl);

$lines=file("$golosdir/vote$id.csv");
$itogo=count($lines); $i=1;
$vdt=explode(";",$lines[0]);

$kod="<!-- // WR-Golos v 1.5  -  $date г. // Голосование № $id -->

<script language=JavaScript><!--
function gosub() {WRSub=window.open('$golosurl/vote.php?id=$id','WRGolos','width=450,height=350,left=250,top=100'); WRSub.focus();}
function gorez() {WRSub=window.open('$golosurl/rezult.php?id=$id','WRRezultGolos','width=450,height=350,left=250,top=100'); WRSub.focus();}//--></script>
<link href='$golosurl/images/style.css' rel='stylesheet' type='text/css' />

<FORM name=wrvote action='$golosurl/vote.php?id=2' method=post target='WRGolos'>
<div id='container'>
<div class='bubble' style=\"BACKGROUND-IMAGE: url($golosurl/images/fongolos-$rnd.jpg); background-size: cover; BACKGROUND-REPEAT: no-repeat\">
<div class='rectangle'><h2>Голосование</h2></div>
<div class='triangle-l'></div>
<div class='triangle-r'></div>
<div class='info'>
<h2>$vdt[0]</h2><p>";
					
do {$dt=explode(";",$lines[$i]); $kod.="<INPUT name='votec' type=radio value='$i'><B>$dt[0]</B></BR>\r\n"; $i++;} while($i<$itogo);
$kod.="</p>
<p><a href='#' onClick='gosub();'><button class='knopka knopka-red'>Проголосовать</button></a></p>
</div>
</div>
</div>
</FORM>
<a href='$golosurl/rezult.php?id=$id' onClick='gorez();' target='WRRezultGolos'><button class='knopka knopka-red'>Результаты</button></a>";


print"<TEXTAREA name=msg style='HEIGHT: 200px; WIDTH: 800px'>$kod</TEXTAREA><br>"; $kod=null;


print"<h4>Код для установки на php-страницу</h4>";

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$golosurl="http://$host$self";
$golosurl=str_replace("/admin.php", "", $golosurl);

$kod='<? // WR-Golos v 1.5  -  '.$date.' г. // Голосование № '.$id.'

$id='.$id.';

$lines = file("'.$golosdir.'/vote$id.csv"); // Здесь путь до папки data указан если уровень одинаков, если вы ставите голосование на уровень ниже - скорректируйте - добавьте  ../
$itogo=count($lines); $i=1;
$vdt=explode(";",$lines[0]);

print"<script language=JavaScript><!--
function gosub() {WRSub=window.open(\''.$golosurl.'/vote.php?id='.$id.'\',\'WRGolos\',\'width=450,height=350,left=250,top=100\'); WRSub.focus();}
function gorez() {WRSub=window.open(\''.$golosurl.'/rezult.php?id='.$id.'\',\'WRRezultGolos\',\'width=450,height=350,left=250,top=100\'); WRSub.focus();}//--></script>
<link href=\''.$golosurl.'/images/style.css\' rel=\'stylesheet\' type=\'text/css\' />";

print"<FORM name=wrvote action=\''.$golosurl.'/vote.php?id=2\' method=post target=\'WRGolos\'>
<div id=\'container\'>
<div class=\'bubble\' style=\"BACKGROUND-IMAGE: url('.$golosurl.'/images/fongolos-'.$rnd.'.jpg); background-size: cover; BACKGROUND-REPEAT: no-repeat\">
<div class=\'rectangle\'><h2>Голосование</h2></div>
<div class=\'triangle-l\'></div>
<div class=\'triangle-r\'></div>
<div class=\'info\'>
<h2>$vdt[0]</h2><p>";
					
do {$dt=explode(";",$lines[$i]); print"<INPUT name=\'votec\' type=radio value=\'$i\'><B>$dt[0]</B></BR>\r\n"; $i++;} while($i<$itogo);
print"</p>
<p><a href=\'#\' onClick=\'gosub();\'><button class=\'knopka knopka-red\'>Проголосовать</button></a></p>
</div>
</div>
</div>
</FORM>
<a href=\''.$golosurl.'/rezult.php?id='.$id.'\' onClick=\'gorez();\' target=\'WRRezultGolos\'><button class=\'knopka knopka-red\'>Результаты</button></a>";

?>';

print"<TEXTAREA name=msg style='HEIGHT: 200px; WIDTH: 800px'>$kod</TEXTAREA><br>";

print"* Если меняете пункты или текст голосования - заново копируйте текст.</TD></TR></TABLE>";




} else { // if (is_file)


print "<center><h4>Cоздание нового голосования № $id</h4>
<table align=center border=0><form action=admin.php?event=add method=POST name=REPLIER>";

$i=0; do {

if ($i==0) {print"<tr><td><B>Название голосования:</B></td><td><input maxlength=50 type=text value='' name=toper size=15 class=midle></td><td width=30 align=center>отве- тов</td></tr>";
   } else { print"<TR>
   <TD align=right>$i ответ:</TD><TD><input type=text value='' maxlength=30 name='otv$i' size=15 class=midle></B></TD>
<TD><input type=text value='0' name='kolvo$i' maxlength=2 size=15></TD></TR>";}
$i++;
} while($i<11);

print"<TR><TD colspan=3 align=center><input type=hidden name=id value='$id'><input type=hidden name=golositogo value='$i'><input class=midle type=submit value='Создать голосование'></TD></TR></table></form><br>
* оставьте поля пустыми, если хотите создать голосование с меньшим кол-вом ответов <br><br>";


}

print"</TD></TR></TABLE>";
}




print "</td></tr></table>
<BR><center><font size=-2>Powered by <a href='http://www.wr-script.ru/'>WR-Golos</a> &copy; 1.5 UTF-8</font></body></html>";
?>