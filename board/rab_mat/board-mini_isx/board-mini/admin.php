<? // WR-Board-mini v 2.3 //  12.02.23 г.  //  WR-Script.ru

//error_reporting(0); // Раскомментируйте для постоянной работы!
error_reporting (E_ALL); // ВКЛЮЧИТЕ для отображения ошибок скрипта

include "data/config.php";

$shapka="<html><head><meta http-equiv='Content-Language' content='ru'><META content='text/html; charset=UTF-8' http-equiv=Content-Type><link rel=stylesheet type='text/css' href='data/$boardm_skin.css'></head><body>";
$back="<center>Вернитесь <a href='javascript:history.back(1)'><B>назад</B></a>"; // Удобная строка

$skey="657567"; // !!! Секретный ключ админпанели !!! 
// Поменяйте на свой в случе подозрения на взлом админпанели
// !!! ПОСЛЕ СМЕНЫ - пароль администратора становится ошибочным!
// для получения нового пароля разкоменируйте строку № 98
// вставьте полученный код в config.php В ПЕРЕМЕННУЮ $password

// Определяем URL С 2023 поддержка ТОЛЬКО https! Если нужно без S - исправьте!
$boardm_url1="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; $host=$_SERVER['HTTP_HOST'];
$boardm_url=str_replace('index.php','',"$boardm_url1"); $boardm_url=preg_replace("/\?.+/","",$boardm_url); // удалить весь GET запрос: все символы после ?


// Авторизация
$adminname="admin"; // ЛОГИН администратора
$adminpass=$password;

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
$text=str_replace("\r\n","<br> ",$text);
$text=str_replace("\n\n",'<p>',$text);
$text=str_replace("\n",'<br> ',$text);
$text=str_replace("\t",'',$text);
$text=str_replace("\r",'',$text);
$text=str_replace('   ',' ',$text);
return $text; }


function nospam() { global $max_key,$rand_key; // Функция АНТИСПАМ
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
echo'Защитный код: <noindex>';
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из config.php + код меняющийся кажые 24 часа
print"</noindex> <input name='usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6>
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>";
return; }



// 01-2023 - обработчик ошибок мини - ФУНКЦИЯ сообщающая об ошибке КРАСИВО!
function error($info) { global $shapka,$back,$boradm_url; $info=replacer($info);
header('HTTP/1.1 404 Not Found'); // или 301 302 код. Надо смотреть google webmaster
exit("$shapka <center><div id='dalee-galka'>&#10006;</div><div id='dalee-main'><P><B><font color=red>Обнаружена ошибка!<BR>
$info!</font></P><font color=red>$back</B></font><BR>Если Вы попали на страницу случайно, то перейдите на <a href='$boardm_url'>главную страницу</a> доски объявлений.
<BR><BR></div></center></body></html>");
return $info;}



// Выбран ВЫХОД - очищаем куки
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrboardmini","",time()-3600); Header("Location: index.php"); exit; } }

if (isset($_COOKIE['wrboardmini'])) { // Сверяем имя/пароль из КУКИ с заданным в конфиг файле
$text=$_COOKIE['wrboardmini'];
$text=str_replace("\r\n","",$text); $text=str_replace(" ","",$text); // Вырезает ПРОБЕЛьные символы 
if (mb_strlen($text)>60) error("Попытка взлома - длина переменной куки сильно большая!");
$text=replacer($text);
$exd=explode("|",$text); $name1=$exd[0]; $pass1=$exd[1];

if ($name1!=$adminname or $pass1!=$adminpass) 
{sleep(1); setcookie("wrboardmini", "0", time()-3600); Header("Location: admin.php"); exit;} // убаваем НЕВЕРНУЮ КУКУ!!!

} else { // ЕСЛИ ваще нету КУКИ


if (isset($_POST['name']) & isset($_POST['pass'])) { // Если есть переменные из формы ввода пароля
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']);
$text="$name|$pass|";
$text=trim($text); // Вырезает ПРОБЕЛьные символы 
if (mb_strlen($text)<4) error("Вы не ввели имя или пароль!");
$text=replacer($text);
$exd=explode("|",$text); $name=$exd[0]; $pass=$exd[1];

//$msgonpage=md5("$pass+$skey"); exit("$msgonpage"); // РАЗБЛОКИРУЙТЕ для получения MD5 своего пароля!

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) error("данные из формы не поступили");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) error("введён ОШИБОЧНЫЙ код!");}

// Сверяем введённое имя/пароль с заданным в конфиг файле
// АДМИНИСТРАТОРУ присваиваются куки
if ($name==$adminname & md5("$pass+$skey")==$adminpass) 
{$tektime=time(); $wrforumm="$adminname|$adminpass|$tektime|";
setcookie("wrboardmini", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}

error("Ваш данные <B>ОШИБОЧНЫ");

} else { // если нет данных, то выводим ФОРМУ ввода пароля



// БЛОК авторизации
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
Авторизация: WR-Board mini 2.3<BR><BR>
<form action="admin.php" method=POST name=pswrd>
<input type="text" name=name value="" placeholder="логин"/>
<input type="password" name=pass placeholder="пароль"/>';

if ($antispam==TRUE) nospam(); // АНТИСПАМ !

print"<button>Войти</button><p class=\"message\">Проблемы при входе? <a href=\"admin.php?event=clearcooke\">Очистить КУКИ</a></p></form></div></div>
<SCRIPT language=JavaScript>document.pswrd.name.focus();</SCRIPT>
<center><small>Powered by <a href=\"https://www.wr-script.ru\" title=\"Скрипт форума\" class='copyright'>WR-Board mini</a> simple &copy; 2.3<br></small></center></body></html>";
exit; } // АВТОРИЗАЦИЯ ПРОЙДЕНА!
} 




// Выбран ВЫХОД - очищаем куки
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrboardmini","",time()-3600); Header("Location: index.php"); exit; } }




$gbc=$_COOKIE['wrboardmini']; $gbc=explode("|", $gbc); $gbname=$gbc[0];$gbpass=$gbc[1];$gbtime=$gbc[2];

$fullshapka="<html><head><title>АДМИНПАНЕЛЬ: $boardm_name</title>
<META HTTP-EQUIV='Pragma' CONTENT='no-cache'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'>
<META content='text/html; charset=UTF-8' http-equiv=Content-Type>
<link rel=stylesheet type='text/css' href='data/$boardm_skin.css'>
<style>
// Стили для КНОПОК-ЧЕКБОКСОВ !!!
div{clear: both; margin:0 0px;}
input.key:empty {margin-left:-9999px;}
input.key:empty ~ label {position:relative; float:left; line-height:1.6em; text-indent:4em; margin:0.2em 0; cursor:pointer;}

input.key:empty ~ label:before {content:'\\2718'; text-indent:2.4em; color:#900; position:absolute; display:block;
top:0; bottom:0; left:0; width:3.6em; background-color:#c33; border-radius:0.3em; box-shadow:inset 0 0.2em 0 rgba(0,0,0,0.3);}

input.key:empty ~ label:after {position: absolute; display: block; top: 0; bottom: 0; left: 0; content: ' ';
width: 3.6em; background-color: #c33; border-radius: 0.3em; box-shadow: inset 0 0.2em 0 rgba(0,0,0,0.3);}

input.key:empty ~ label:after { width: 1.4em; top: 0.1em; bottom: 0.1em; margin-left: 0.1em;
background-color: #fff; border-radius: 0.15em; box-shadow: inset 0 -0.2em 0 rgba(0,0,0,0.2);}

input.key:checked ~ label:before {background-color:#393; content:'\\2714'; text-indent:0.5em; color:#6f6;}
input.key:checked ~ label:after {margin-left:2.1em;}
</style>
<SCRIPT language=JavaScript>
function x () {return;}
function FocusText() {
 document.REPLIER.msg.focus();
 document.REPLIER.msg.select();
 return true; }
function DoSmilie(addSmilie) {
 var revisedmsgage;
 var currentmsgage = document.REPLIER.msg.value;
 revisedmsgage = currentmsgage+addSmilie;
 document.REPLIER.msg.value=revisedmsgage;
 document.REPLIER.msg.focus();
 return;
}
function DoPrompt(action) { var revisedmsgage; var currentmsgage = document.REPLIER.qmsgage.value; }
</SCRIPT></head>
<body>
<center><h1>АДМИНПАНЕЛЬ: $boardm_name</h1>
<DIV>Сегодня <b>$date г.</b>
<a href='admin.php'><button class='knopka color'>Главная</button></a>
<a href='admin.php?event=configure'><button class='knopka color'>Настройка</button></a>
<a href='admin.php?addmsg'><button class='knopka color'>Добавить объявление</button></a>
<a href='admin.php?event=blockip'><button class='knopka color'>БАН по IP</button></a>
<a href='https://www.wr-script.ru/forum/index.php?id=104' target='_blank'><button class='knopka color'>Помощь</button></a>
<a href='admin.php?event=seebasa'><button class='knopka color'>Экспорт в Excel / Calc</button></a>
<a href='admin.php?event=clearcooke'><button class='knopka color'>Выход</button></a>
<hr size=-1 width=80%><center>$boardm_info<BR>
</div><BR>";


function replace_links($string=null)  {
$string=preg_replace ("#([^\[img\]])(http|https|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)#i", "\\1<noindex><a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a></noindex>\\4", $string);
$string=preg_replace('#\[img\](.+?)\[/img\]#','<img src="$1" border="0">',$string); // запускать ТОЛЬКО после замены АДРЕСА URL!
return $string; }



// БЛОК показывает ОДНО ОБЪЯВЛЕНИЕ ПОЛНОСТЬЮ
if (isset($_GET['viewall'])) { if ($_GET['viewall']!="") {
$lines=file("$boarddir/board.csv"); $itogo=count($lines)-1; $id=$_GET['viewall'];

$num=-1; $last=-1; $next=-1;

// Ищем номер текущего, предыдущего и следующего объявлений с 02.2023
for ($i=0;$i<=$itogo;$i++) { $dtt=explode("|",$lines[$i]);
if ($id==$dtt[10]) {$num=$i; if ($i>0) $last=$i-1; if ($i<$itogo) $next=$i+1;}}

if ($num==-1) error("Срок размещения данного объявления окончен"); // Нет объявления - выдаём ошибку

$dt=explode("|",$lines[$num]);
$dt[5]=replacer($dt[5]);
$dt[5]=str_replace("[b]","<B>", $dt[5]); $dt[5]=str_replace("[/b]","</B>", $dt[5]);
$dt[5]=str_replace("[RB]","<B><font color=red>", $dt[5]); $dt[5]=str_replace("[/RB]","</font></B>", $dt[5]);
$dt[5]=str_replace("&lt;br&gt;","<BR>", $dt[5]);
$dt[5]=replace_links($dt[5]);
$deldate=date("d.m.Y",$dt[7]); // конвертируем дату удаления в человеческий формат
$fd=explode(".",$dt[6]);
$then=mktime(0,0,0,$fd[1],$fd[0],$fd[2]);
$firstday=floor(($dt[7]-$then)/86400); // формируем кол-во дней леквидности объявления
$today=time();

$tektday=floor(($today-$then)/86400); // формируем кол-во дней прошедших со дня подачи объявления
if ($tektday<1) $tektday=1;
$zdt2=explode("[email]",$dt[2]); if (!isset($zdt2[1])) $zdt2[1]="";
if (mb_strlen($zdt2[1])>3) $name="<a href='mailto:$zdt2[1]'>$zdt2[0]</a>"; else $name="$zdt2[0]";

print"$fullshapka
<div class='set_item2'>
    <div class='boxmax'>
		<h2>$dt[3]</h2>
       	<p style='min-height: 220px;'>$dt[5]</p>
		<p><small>Объявление разместил:</small> $name</p>
		<P><small>Дата подачи: $dt[6]<BR> Дата удаления: $deldate</small><BR></p>
		<font size=+1>Действует: <font size=+2 color=navy>$tektday/$firstday</font> дн.</font>
		<div class='knopka del'>Бан по IP: <a href='admin.php?badip&ip_get=$dt[21]'>$dt[21]</div>
		</P>
    </div>
</div>";

$navmsg=null; // БЛОК НАВИГАЦИИ ВПЕРЁД-НАЗАД для быстрого перемещения по объявлениям с 02.2023
if ($last!=-1) {$dtlast=explode("|",$lines[$last]); $navmsg.="<div class='wr_block'><a class='wr_knopki dark' title='Предыдущее объявление' href='admin.php?viewall=$dtlast[10]'>&#9668; предыдущее объявление</a></div>";}
    $navmsg.="<div class='wr_block'><a class='wr_knopki red' title='Вернуться на главную страницу' href='admin.php'>Вернуться</a></div>";
if ($next!=-1) {$dtnext=explode("|",$lines[$next]); $navmsg.="<div class='wr_block'><a class='wr_knopki blue' title='Следующее объявление' href='admin.php?viewall=$dtnext[10]'>следующее объявление &#9658;</a></div>";}
print $navmsg;
exit; } }




// Блок УДАЛЕНИЯ по RN и TIMESTAMP выбранного объявления // изм. 02.2023
if (isset($_GET['del'])) { $deltime=replacer($_GET['deltime']);
if (isset($_GET['page'])) $page=$_GET['page']; else $page=1;
$file=file("$boarddir/board.csv"); $itogo=count($file)-1;
$del=$_GET['del'];
$fp=fopen("$boarddir/board.csv","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { $dtt=explode("|",$file[$i]); if ($del==$dtt[10] & $dtt[7]==$deltime) unset($file[$i]);}
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?page=$page"); exit; }



// изменить редактирование - по RN и TIMESTAMP
if (isset($_GET['rd']))  { // РЕДАКТИРОВАНИЕ объявления
if (isset($_GET['page'])) $page=$_GET['page']; else $page="1";
$lines=file("$boarddir/board.csv"); $itogo=count($lines)-1;
$rd=$_GET['rd']; // изм. 02.2023
// ищем объявление в базе по КЛЮЧУ RN и выводим в форму для редактирования
for ($i=0;$i<sizeof($lines);$i++) { $dtt=explode("|",$lines[$i]); if ($dtt[10]==$rd) $dt=explode("|",$lines[$i]);}

if (stristr($dt[2],"[email]")) $tdt=explode("[email]", $dt[2]);
$dt[5]=str_replace("<br>","\r\n",$dt[5]);
$td=time()/86400;
$dayx=$dt[7]/86400;
$daysto=round($dayx-$td);
print "$fullshapka 
<TABLE width=80% align=center cellPadding=0 cellSpacing=0>
<TR><TD align=center><BR><h3>Редактирование объявления</h3></td></tr>
<center><table border=1>
<form action=admin.php?event=add&rwd=$rd&page=$page method=post name=REPLIER>
<tr><td><B>Имя</B> и E-mail</td><td><input type=text value='$tdt[0]' name=name size=26 style='width:48%'>&nbsp; <input type=text value='$tdt[1]' name=email size=26 style='width:48%'></td></tr>
<tr><td><B>Заголовок</B> (не более 50 символов)</td><td><input type=text value='$dt[3]' name=zag size=56 style='width:100%'></td></tr>
<tr><td><B>URL</B> (не забывайте https://)</td><td><input type=text value='$dt[25]' name=www size=56 style='width:100%'></td></tr>
<tr><td>Спецвыделение<BR> (увеличивает рейтинг объявления)</td><td align=center><input type=button class=button value=' B ' style='font-weight:bold; width: 30px' onclick=\"DoSmilie(' [b]  [/b] ');\">&nbsp; <input type=button class=button value=' RB ' style='font-weight:bold; color:red' onclick=\"DoSmilie('[RB] [/RB]');\">&nbsp; <input type=button class=button value=' IMG ' style='font-weight:bold; color:navy' onclick=\"DoSmilie('[img] [/img]');\">&nbsp; </td></tr>
<tr><td><B>Текст объявления</B> <BR>(не более 500 символов)</td><td><textarea cols=55 rows=16 size=500 name=msg style='width:100%;height:230px;'>$dt[5]</textarea></td></tr>
<TR><TD><B>Срок хранения объявления</B>:<BR> (7-365 дней)</TD><TD><SELECT name=days><OPTION selected value=$daysto>$daysto дней</OPTION><OPTION value=7>7 дней</OPTION><OPTION value=14>14 дней</OPTION><OPTION value=30>30 дней</OPTION><OPTION value=60>60 дней</OPTION><OPTION value=90>90 дней</OPTION><OPTION value=365>365 дней</OPTION></SELECT>
&nbsp;&nbsp;&nbsp;&nbsp; IP адрес: $dt[21]</TD></TR>
<input type=hidden name=rtoday value='$dt[11]'>
<input type=hidden name=rid value='$dt[10]'>
<input type=hidden name=rdate value='$dt[6]'>
<tr><td colspan=2 align=center><input type=submit value='Сохранить изменения'></form></td></tr>
</table>
</TD></TR></TABLE>";
exit; }




// Добавление IP-юзера в БАН
if (isset($_GET['badip']))  {
if (isset($_POST['ip'])) {$ip=$_POST['ip']; $badtext=$_POST['text'];}
if (isset($_GET['ip_get'])) {$ip=$_GET['ip_get']; $badtext="За добавление нежелательных сообщений на доску объявлений! ЗА СПАМ!!!";}
if (mb_strlen($ip)<8) error("Введите IP по формату X.X.X.X, где Х - число от 1 до 255! Сейчас запрос пуст или IP НЕ указан!");
$text="$ip|$badtext|"; $text=stripslashes($text); $text=htmlspecialchars($text); $text=str_replace("\r\n", "<br>", $text);
$fp=fopen("data/bad_ip.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit;}



// Удаления юзера из БАНА
if (isset($_GET['delip']))  { $xd=$_GET['delip'];
$file=file("data/bad_ip.csv"); $dt=explode("|",$file[$xd]); 
$fp=fopen("data/bad_ip.csv","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit;}


if (isset($_GET['event'])) {


if ($_GET['event']=="blockip") { // - БЛОКИРОВКА по IP

$itogo=0;
if (is_file("data/bad_ip.csv")) { $lines=file("data/bad_ip.csv"); $i=count($lines); $itogo=$i;

print "$fullshapka <div class=box1>"; // инклюдим ШАПКУ

if ($i>0) { // Если есть IP-шнички

echo'<table width=100% border=0 cellpadding=1 cellspacing=0><TR><TD>
<table border=0 width=100% cellpadding=2 cellspacing=1 class=forumline><tr> 
<th class=thCornerL width=50 height=25 nowrap=nowrap>.X.</th>
<th class=thCornerL width=150>IP</th>
<th class=thCornerL >Формулировка</th>
</tr>';

do {$i--; $idt=explode("|", $lines[$i]);
   print"<TR bgcolor=#F7F7F7><td width=10 align=center><table><tr><td width=10 bgcolor=#FF2244><B><a href='admin.php?delip=$i'>.X.</a></B></td></tr></table></td><td>$idt[0]</td><td>$idt[1]</td></tr>";
} while($i > "0");
echo'</table><br>';
} else print"<H1 align=center>Заблокированные IP-адреса отсутствуют</H1>";
} else print"<H1 align=center>Заблокированные IP-адреса отсутствуют</H1>";
print"<CENTER><form action='admin.php?badip' method=POST>
Добавь IP НЕдруга! &nbsp; <input type=text style='FONT-SIZE: 14px; WIDTH: 125px' maxlength=15 name=ip> Формулировка: <input type=text style='FONT-SIZE: 14px; WIDTH: 300px' maxlength=50 name=text
value='За систематическое нарушение правил сайта! За спам!'>
<input type=submit value=' добавить '></form><br>* вводите IP аккуратно, формат Х.Х.Х.Х. Используйте только цифры от 1 до 3 символов и точку в качестве разделителя.
<br><BR>Всего заБАНено пользователей - <B>$itogo</B><BR></td></tr></table></div>";}





if ($_GET['event']=="seebasa") { // Просмотр и ЭКСПОРТ СТАТИСТИКИ В Exclel / Calc


// ЭКСПОРТ БД
if (isset($_GET['export'])) { $openfile=$_POST['openfile'];
if (!stristr($openfile, ".csv")) error("Разрешён экспорт только содержимого базы скрипта (всех файлов с раширением csv!");
$records=file_get_contents("$boarddir/$openfile");
setlocale(LC_ALL,'Russian_Russia.65001');
$records=iconv("utf-8", "cp1251",$records); // Преобразовываем в кодировку Windos-1251
$records=str_replace('|',';',$records); 
//$records=str_replace(';',',',$records); // если нужен разделитель запятая , то разкоментируйте
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=wr-board-datafile-$openfile.csv");
header("Content-Transfer-Encoding: Windows-1251");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
echo $records; exit;} // КОНЕЦ ЭКСПОРТА В CSV


print"<BR>$fullshapka";

// Выводим форму для ПРОСМОТРА ФАЙЛА
print"<form action='admin.php?event=seebasa&see' method=POST name=REPLIER><h2>Просмотреть содержимое файла БД</h2><select class=input name=openfile>";
if ($handle=opendir($boarddir)) {
while (($file=readdir($handle)) !== false)
if (!is_dir($file)) { 
$stroka=stristr($file, ".csv"); if (mb_strlen($stroka)>"1") 
{ $marker=""; if (isset($_POST['openfile'])) { if ($_POST['openfile']===$file) $marker="selected"; }
print"<option $marker value=\"$file\">$file</option>"; }
} closedir($handle); } else echo'Ошибка!';
echo'</select> &nbsp; <input type=submit value="Просмотреть файл"></form>';

// Выводим форму для ЭКСПОРТА
print"<form action='admin.php?event=seebasa&export' method=POST name=REPLIER><h2>Экспортировать файл</h2><select class=input name=openfile>";
if ($handle=opendir($boarddir)) {
while (($file=readdir($handle)) !== false)
if (!is_dir($file)) { 
$stroka=stristr($file, ".csv"); if (mb_strlen($stroka)>"1") 
{ $marker=""; if (isset($_POST['openfile'])) { if ($_POST['openfile']===$file) $marker="selected"; }
print"<option $marker value=\"$file\">$file</option>"; }
} closedir($handle); } else echo'Ошибка!';
echo'</select> &nbsp; <input type=submit value="ЭКСПОРТИРОВАТЬ файл"></form>';

if (isset($_POST['openfile'])) {

$openfile=$_POST['openfile'];
if (!stristr($openfile, ".csv")) error("Разрешён просмотр только содержимого базы скрипта (всех файлов с раширением csv!");

$data=File("$boarddir/$openfile"); $size=sizeof($data);

echo "<b><i><h2><center>Содержимое файла \"$boarddir/$openfile\"</b></i></h2>
* В первой строке указаны номера по порядку. Если Вам нужно считать в скрипте представленные данные, то здесь Вы можете быстро узнать их порядковый номер!
<table border=0><tr>";

if ($size>0) {$dat_arr=explode("|",$data[0]);

for ($p=0;$p<count($dat_arr);$p++) echo "<td bgcolor=#04A2FF><center><b>$p</td>";
echo "</tr>";

for ($i=0;$i<count($data);$i++) {
    $data_array=explode("|", $data[$i]);
    echo "<tr>";
    for ($f=0;$f<count($data_array);$f++) echo "<td bgcolor=#AEE1FF><center><b>$data_array[$f] &nbsp;</td>";
    echo "</tr>";
}} else print"<h1>Файл пуст!<h1>";

echo "</table></center></form></body></html>";

} // if isset $_GET['openfile']
}	






if ($_GET['event']=="add") { // Блок ДОБАВЛЕНИЯ ОБЪЯВЛЕНИЯ

// возвращаем значения данных из формы и присваиваем их переменным
if (isset ($_POST['name']) & isset ($_POST['msg']) & isset ($_POST['email'])) {$name=$_POST['name']; $msg=$_POST['msg']; $email=$_POST['email'];} else {exit;}
if (isset($_GET['page'])) $page=$_GET['page']; else $page=1;

if ($name=="" || mb_strlen($name) > $maxname) error("Ваше имя или пустое, или превышает $maxname символов");
if ($msg=="" || mb_strlen($msg) > $maxmsg) error("Ваше сообщение или пустое или превышает $maxmsg символов");
if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$email) and mb_strlen($email)>30 and $email!="") error("и введите корректный E-mail адрес!");
$www=$_POST['www']; if (mb_strlen($www) > 50) error("Вы ввели слишком длинный URL. Он превышает 50 символов");
$zag=$_POST['zag']; if ($zag == "" || mb_strlen($zag) > $maxzag) error("Вы не ввели заголовок объявления, или он превышает $maxzag символов!");
$days=$_POST['days']; if ($days>"365") $days="365";

$deldt=time()+$days*86400; // формируем дату удаления объявления
$name=str_replace("|","I",$name);
$zag=str_replace("|","I",$zag);
$www=str_replace("|","I",$www);
$msg=str_replace("|","I",$msg);
$name=wordwrap($name,24,' ',1); // разрываем слишком длинные строки
$msg=wordwrap($msg,75,' ',1);

//$id - уникальный идентификатор объявления. Сгенерировать с начиная с 1011000
// БЛОК ГЕНЕРИРУЕТ СЛЕДУЮЩИЙ ПО ПОРЯДКУ НОМЕР ОБЪЯВЛЕНИЮ с 02-2023
// считываем весь файл в объявлениями в память
$allid=null; $records=file("data/board.csv"); $imax=count($records); $i=$imax;
if ($i > 0) { do {$i--; $rd=explode("|",$records[$i]); $allid[$i]=$rd[10]; } while($i>0);
$id=1000; $id="101$id";
do { $id++;} while(in_array($id,$allid));
} else $id="1011000"; // if ($i > 0)

// Структура БД как в доске лайт / ЛЮКС 2.0 и выше
$dano=$name."[email]".$email; $today=time(); $ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
$text="1|Разные объявления|$dano|$zag|П|$msg|$date|$deldt|101|no|$id|$today|0|||||||0||$ip||||$www|1|";
//$text="$name|$email|$zag|$www|$msg|$date|$time|$deldt|||";

if (isset($_GET['rwd'])) { $rd=$_GET['rwd']; $rtoday=$_POST['rtoday'];$rid=$_POST['rid'];$rdate=$_POST['rdate'];
$dano=$name."[email]".$email;
$text="1|Разные объявления|$dano|$zag|П|$msg|$rdate|$deldt|101|no|$rid|$rtoday|0|||||||0||$ip||||$www|1|"; }
//$text="$name|$email|$zag|$www|$msg|$da|$ti|$deldt|$rdeldt||";

$text=replacer($text);

if (isset($_GET['rwd'])) { // РЕДАКТИРОВАНИЕ объявления
$file=file("$boarddir/board.csv");
$fp=fopen("$boarddir/board.csv","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА

for ($i=0;$i< sizeof($file);$i++) { 
$dtt=explode("|",$file[$i]); // изм. 02.2023
if ($rd!=$dtt[10]) fputs($fp,$file[$i]); else fputs($fp,"$text\r\n"); }
fflush ($fp);//очищение буфера
flock ($fp,LOCK_UN);
fclose($fp);

} else  {

$fp=fopen("$boarddir/board.csv","a+"); // добавление объявления
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);//очищение буфера
flock ($fp,LOCK_UN);
fclose($fp);
}

Header("Location: admin.php?page=$page"); exit; }





if ($_GET['event']=="configure")  {

if ($antispam==TRUE) {$as1="checked"; $as2="";} else {$as2="checked"; $as1="";}
if ($sendmail==TRUE) {$s1="checked"; $s2="";} else {$s2="checked"; $s1="";}
if ($use_www==TRUE) {$uw1="checked"; $uw2="";} else {$uw2="checked"; $uw1="";}

// считываем рекламные блоки
$reclama_top=file_get_contents("data/top.html");
$reclama_bot=file_get_contents("data/bottom.html");

print " $fullshapka
<TABLE border=1 width=80% align=center cellPadding=0 cellSpacing=0><TBODY>
<TD><table width=100%><TR><TD><center><h1>Настройка</h1>
<form action=admin.php?event=config method=POST name=REPLIER><table>
<tr bgcolor=#E6E6E6><td><B>Имя скрипта</B></td><td><input type=text value='$boardm_name' name=boardm_name maxlength=50 size=50 style='width:100%;'></tr></td>
<tr><td><B>Текст приветствия</B></td><td><textarea cols=50 rows=4 size=500 name=boardm_info style='width:100%;height:100px;'>$boardm_info</textarea></tr></td>
<tr bgcolor=#E6E6E6><td>Рекламный блок вверху</td><td><textarea name=reklama_top style='width:100%;height:120px;'>$reclama_top</textarea></tr></td>
<tr><td>Рекламный блок внизу</td><td><textarea name=reklama_bot style='width:100%;height:120px;'>$reclama_bot</textarea></tr></td>
<tr bgcolor=#E6E6E6><td>Ссылка на главную сайта</td><td><input type=text value='$mainpage' name=mainpage style='width:100%;' size=65></td></tr>
<tr><td>Е-майл / Отсылать объявления на почту?</td><td><input type=text value='$adminemail' maxlength=50 name=adminemail size=35> &nbsp; / &nbsp; <input type=radio name=sendmail value=\"1\"$s1/> да&nbsp;&nbsp; <input type=radio name=sendmail value=\"0\"$s2/> нет</tr></td>
<tr bgcolor=#E6E6E6><td>Пароль администратора *</td><td><input name=password type=hidden value='$password'><input type=text value='скрыт'  maxlength=30 name=newpassword size=15> (зашифрован и скрыт)</td></tr>
<tr><td class=row1>Задействовать АНТИСПАМ / кол-во символов в коде ЦИФРОЗАЩИТЫ</td><td class=row2><input type=radio name=antispam value=\"1\"$as1/> да&nbsp;&nbsp; <input type=radio name=antispam value=\"0\"$as2/> нет &nbsp;&nbsp; .:. &nbsp;&nbsp; <input type=text value='$max_key' name=max_key size=4 maxlength=1> (от 1 до 9)</tr></td>
<tr bgcolor=#E6E6E6><td>Максимальная длина <B>Имени</B> / <B>заголовка</B> / <B>текста объявления</B></td><td><input type=text value='$maxname' name=maxname  maxlength=2 size=2> &nbsp;&nbsp;&nbsp; <input type=text value='$maxzag' name=maxzag  maxlength=2 size=2> &nbsp;&nbsp;&nbsp; <input type=text value='$maxmsg' name=maxmsg  maxlength=4 size=4></tr></td>
<tr><td><B>Объявлений на страницу</B></td><td><input type=text value='$msgonpage' name=msgonpage maxlength=2 size=2></tr></td>
<tr bgcolor=#E6E6E6><td class=row1>Разрешить поле САЙТ?</td><td class=row2><input type=radio name=use_www value=\"1\"$uw1/> да&nbsp;&nbsp; <input type=radio name=use_www value=\"0\"$uw2/> нет</tr></td>

<tr><td>Стиль с оформлением доски</td><td><select class=input name=boardm_skin>
<option value=\"$boardm_skin\">Текущий</option>
<option value='style-white-blue' style='color: #FFFFFF; background: #99ccff'>Светло-голубой</option>
<option value='style-white-red' style='color: #CC0000; background: #FFFFFF'>Бело-красный</option>
<option value='style-white-yellow' style='color: #000000; background: #FFC800'>Светло-жёлтый</option>
</select></nobr> Текущий стиль - <B>data/$boardm_skin.css</B></tr></td>

<tr bgcolor=#E6E6E6><td><B>Cледование сообщений</B></td><td><select class=input name=msginout><option value='$msginout'>Текущее</option><option value='1'>по убыванию</option><option value='0'>по возрастанию</option></select></tr></td>
<tr><td colspan=2><center><table><tr><td><input type=submit value='Сохранить'></form></td></tr></table>
</td></tr></table>
</TD></TR></TABLE></TD></TR></TBODY></TABLE>
<center><br>* Если хотите изменить пароль - сотрите слово <B>\"скрыт\"</B> и введите новый пароль."; }



if ($_GET['event']=="config")  {

mt_srand(time()+(double)microtime()*1000000); $rand_key=mt_rand(1000,9999); // Генерируем случайное число для цифрозащиты

if (mb_strlen($_POST['newpassword'])<1) print"$back разрешается длина пароля МИНИМУМ 1 символ!";
if ($_POST['newpassword']!="скрыт") {$pass=trim($_POST['newpassword']); $_POST['password']=md5("$pass+$skey");}

$configdata="<? // WR-Board mini v 2.3 //  12.02.23 г.  //  WR-Script.ru\r\n".
"$"."boardm_name=\"".$_POST['boardm_name']."\"; // Название доски объявлений отображается в теге TITLE и заголовке\r\n".
"$"."boardm_info=\"".$_POST['boardm_info']."\"; // Текст, выводящийся перед формой ввода сообщения\r\n".
"$"."mainpage=\"".$_POST['mainpage']."\"; // Ссылка на главную страницу\r\n".
"$"."adminemail=\"".$_POST['adminemail']."\"; // Емайл админа\r\n".
"$"."sendmail=\"".$_POST['sendmail']."\"; // отсылать сообщения на Емайл админу\r\n".
"$"."password=\"".$_POST['password']."\"; // Пароль администратора защифрован md5()\r\n".
"$"."antispam=\"".$_POST['antispam']."\"; // Задействовать АНТИСПАМ\r\n".
"$"."max_key=\"".$_POST['max_key']."\"; // Кол-во символов в коде ЦИФРОЗАЩИТЫ\r\n".
"$"."rand_key=\"".$rand_key."\"; // Случайное число для цифрозащиты\r\n".
"$"."boarddir=\"./data\"; // папка с файлами данных доски\r\n".
"$"."boardm_skin=\"".$_POST['boardm_skin']."\"; // Стиль с оформлением доски объявлений мини\r\n".
"$"."maxname=\"".$_POST['maxname']."\"; // Максимальное кол-во символов в имени\r\n".
"$"."maxzag=\"".$_POST['maxzag']."\"; // Максимальное кол-во символов в заголовке\r\n".
"$"."maxmsg=\"".$_POST['maxmsg']."\"; // Максимальное кол-во символов в сообщении\r\n".
"$"."use_www=\"".$_POST['use_www']."\"; // Разрешить поле Сайт?\r\n".
"$"."msginout=\"".$_POST['msginout']."\"; // порядок просмотра сообщений: возрастание/убывание - 1/0\r\n".
"$"."msgonpage=\"".$_POST['msgonpage']."\"; // кол-во отображаемых объявлений на каждой странице доски\r\n".
"$"."date=date(\"d.m.Y\"); // число.месяц.год\r\n".
"$"."time=date(\"H:i:s\"); // часы:минуты:секунды \r\n?>";

$file=file("data/config.php");
$fp=fopen("data/config.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
fputs($fp,$configdata);
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);

// Записываем ВЕРХНИЙ рекламный блок
$file=file("data/top.html");
$fp=fopen("data/top.html","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
fputs($fp,$_POST['reklama_top']);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// Записываем НИЖНИЙ рекламный блок
$file=file("data/bottom.html");
$fp=fopen("data/bottom.html","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
fputs($fp,$_POST['reklama_bot']);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

Header("Location: admin.php?event=configure"); exit; }







}   else   {      // if isset($event)



if (isset($_GET['addmsg']))  { // ДОБАВЛЕНИЕ НОВОГО объявления
print "$fullshapka
<TABLE width=80% align=center cellPadding=0 cellSpacing=0>
<TR><TD align=center><BR><h1>Добавление объявления</h1></td></tr>
<center><table border=1>
<form action=admin.php?event=add method=post name=REPLIER>
<tr><td><B>Имя</B> и E-mail</td><td><input type=text value='' name=name size=26>&nbsp; <input type=text value='' name=email size=26></td></tr>
<tr><td><B>Заголовок</B> (не более 50 символов)</td><td><input type=text value='' name=zag size=56></td></tr>
<tr><td>URL</td><td><input type=text value='' name=www size=56 placeholder='https://ваш-сайт.ru'></td></tr>
<tr><td>Спецвыделение<BR> (увеличивает рейтинг объявления)</td><td align=center><input type=button class=button value=' B ' style='font-weight:bold; width: 30px' onclick=\"DoSmilie(' [b]  [/b] ');\">&nbsp; <input type=button class=button value=' RB ' style='font-weight:bold; color:red' onclick=\"DoSmilie('[RB] [/RB]');\">&nbsp; <input type=button class=button value=' IMG ' style='font-weight:bold; color:navy' onclick=\"DoSmilie('img] [/img]');\">&nbsp; </td></tr>
<tr><td><B>Текст объявления</B> <BR>(не более 500 символов)</td><td><textarea cols=55 rows=16 size=500 name=msg></textarea></td></tr>
<TR><TD><B>Срок хранения объявления</B>:<BR> (7-365 дней)</TD><TD><SELECT name=days><OPTION value=7>7 дней</OPTION><OPTION value=14>14 дней</OPTION><OPTION selected value=30>30 дней</OPTION><OPTION value=60>60 дней</OPTION><OPTION value=90>90 дней</OPTION><OPTION value=365>365 дней</OPTION></SELECT></TD></TR>
<tr><td colspan=2 align=center><input type=submit value='Добавить'></form></td></tr>
</table></TD></TR></TABLE><BR><BR>"; exit; }




if (isset($_GET['page'])) $page=$_GET['page']; else $page="1";

print"$fullshapka";

if (is_file("$boarddir/board.csv")) { // проверяем есть данные в файле
$lines=file("$boarddir/board.csv"); $itogo=count($lines); $maxi=$itogo-1; $pageinfo=null;

if ($itogo > 0) {

$maxpage=ceil(($maxi+1)/$msgonpage); if ($page>$maxpage) {$page=$maxpage;}

if ($msginout=="1") 
{ $fm=$msgonpage*($page-1); if ($fm>$maxi) {$fm=$maxi-$msgonpage;}
  $lm=$fm+$msgonpage; if ($lm>$maxi) {$lm=$maxi+1;} }
else 
{ $fm=$maxi-$msgonpage*($page-1); if ($fm<"0") {$fm=$msgonpage;}
  $lm=$fm-$msgonpage; if ($lm<"0") {$lm="-1";} }

print"Всего объявлений: <B>$itogo</B><BR>";

// Печатаем список страниц вверху 02.2023
$pageinfo="<div style='clear:both;'><BR> Страницы:&nbsp; ";
for($i=0; $i<$maxi+1;) {$ip=$i/$msgonpage+1;
if ($page==$ip) $pageinfo.="<B>$ip</B> &nbsp;"; else $pageinfo.="<a href=\"admin.php?page=$ip\">$ip</a> &nbsp;"; $i=$i+$msgonpage;} $pageinfo.="</div>";

print"$pageinfo
<div class='wrapper'>"; $cm=1;

do { $dt = explode("|", $lines[$fm]); $num=1+$maxi-$fm;
if ($msginout=="1") {$fm++;} else {$fm--;}

$dt[4]=preg_replace ("#([^\[img\]])(http|https|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)#i", "\\1<noindex><a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a></noindex>\\4", $dt[4]);
$dt[4]=preg_replace('#\[img\](.+?)\[/img\]#','<img src="$1" border="0">',$dt[4]); // запускать ТОЛЬКО после замены АДРЕСА URL!!!
$dt[4]=str_replace("[b]","<B>", $dt[4]);
$dt[4]=str_replace("[/b]","</B>", $dt[4]);
$dt[4]=str_replace("[RB]","<B><font color=red>", $dt[4]);
$dt[4]=str_replace("[/RB]","</font></B>", $dt[4]);
$dt[4]=str_replace("<br>"," &nbsp;", $dt[4]);
$dt[4]=wordwrap($dt[4],25,' ',1);

$deldate=date("d.m.Y",$dt[7]); // конвертируем дату удаления в человеческий формат

$fd=explode(".",$dt[6]);
$then=mktime(0,0,0,$fd[1],$fd[0],$fd[2]);
$firstday=floor(($dt[7]-$then)/86400); // формируем кол-во дней леквидности объявления

$today=time(); $tektday=round(($today-$then)/86400); // Кол-во дней прошедших со дня подачи объявления

if (mb_strlen($dt[2])>55) {$dt[2]=mb_substr($dt[2],0,52); $dt[2].="...";}
if (mb_strlen($dt[4])>400) {$dt[4]=mb_substr($dt[4],0,380); $dt[4].="... <a href='admin.php?viewall=$dt[10]'> Подробнее...</a>";}

$zdt2=explode("[email]",$dt[2]); if (!isset($zdt2[1])) $zdt2[1]="";
if (mb_strlen($zdt2[1])>3) $name="<a href='mailto:$zdt2[1]'>$zdt2[0]</a>"; else $name="$zdt2[0]";

print"
<div class='set_item'>
    <div class='box2'>
		<h2><a href='admin.php?viewall=$dt[10]'>$dt[3]</a></h2>
        <p>$dt[5]</p>
		<p><small>Объявление № <B>$num</B><BR> разместил:</small> $name</p>
		<P><small>Дата подачи: $dt[6] г.<BR> Дата удаления: $deldate г.</small><BR></p>			
		<font size=+1>Действует: <font size=+2 color=navy>$tektday/$firstday</font> дн.</font>
		<div class=wrapper>
		<div class=edit><a href='admin.php?rd=$dt[10]&page=$page'>Редактировать</a></div>			
		<div class=del><a href='admin.php?del=$dt[10]&deltime=$dt[7]&page=$page' title='УДАЛИТЬ объявление?' onclick=\"return confirm('Удалить объявление? Уверены?')\">Удалить</a></div>
		<div class='knopka del'>Бан по IP: <a href='admin.php?badip&ip_get=$dt[21]'>$dt[21]</div>		
		</div>
    </div>
</div>";

if ($msginout=="1") {$whm=$fm; $whe=$lm;} else {$whm=$lm; $whe=$fm;}
} while($whm < $whe);

} else print"<h1>В базе нет объявлений! Добавьте скорее хотя бы одно.</h1>"; // if ($itogo > 0)
} // if is_file("$boarddir/board.csv")


print "</div>$pageinfo";
}
?>
<BR><BR><center><small>Powered by <a href='https://www.wr-script.ru/'>WR-Board mini</a> &copy; 2.3 UTF-8</small></body></html>