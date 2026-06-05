<? // WR-Board-mini v 2.3 //  12.02.23 г.  //  WR-Script.ru

// error_reporting(0); // Раскомментируйте для постоянной работы!
error_reporting (E_ALL); // ВКЛЮЧИТЕ для отображения ошибок скрипта

// НОВАЯ СТРУКТУРА БД с 02.2023
//$text="1|Разные объявления|$dano|$zag|П|$msg|$date|$deldt|101|no|$id|$today|0|||||||0||$ip||||$www||";

@ini_set('register_globals','off');// Все скрипты написаны для этой настройки php

include "data/config.php";

$shapka="<html><head><META content='text/html; charset=UTF-8' http-equiv=Content-Type><link rel=stylesheet type='text/css' href='data/$boardm_skin.css'></head><body>";
$back="<center>Вернитесь <a href='javascript:history.back(1)'><B>назад</B></a>"; // Удобная строка

// Определяем URL скрипта, подставялем http
$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"]; $boardm_url=str_replace('index.php','',"http://$host$self");

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
$text=str_replace("\n\n",'<p> ',$text);
$text=str_replace("\n",'<br> ',$text);
$text=str_replace("\t",'',$text);
$text=str_replace("\r",'',$text);
$text=str_replace('   ',' ',$text);
return $text; }


function nospam() { global $max_key,$rand_key; // Функция АНТИСПАМ изм. 02.2023
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
echo'<BR><div style="width:50%"><div style="float:left;">Защитный код:</div>';
$nummax=0; for ($i=0; $i<=$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
$secret=mt_rand(0,1); $styles='#FFFF00';
if ($nummax<3) { if ($secret==1 or $i==0) {$styles='#77FF77'; $xkey=$xkey.$snum[$i]; $nummax++;}}
echo "<div style='float:left; width:20px; height:20px;background:$styles;'> <img src=antispam.php?image=$psnum border=0 alt=''></div>\r\n";}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из config.php + код меняющийся кажые 24 часа
print"<div style='float:left;'><input name='usernum' class=post type='text' maxlength=$nummax size=6> (введите цифры, которые на <font style='font-weight:bold'> зелёном фоне</font>)
<input name=xkey type=hidden value='$xkey'><input name=stime type=hidden value='$stime'></div></div>";
return; }


function replace_links($string=null)  {
$string=preg_replace ("#([^\[img\]])(http|http|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)#i", "\\1<noindex><a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a></noindex>\\4", $string);
$string=preg_replace('#\[img\](.+?)\[/img\]#','<img src="$1" border="0">',$string); // запускать ТОЛЬКО после замены АДРЕСА URL!!!
return $string; }


// 02-2023 - обработчик ошибок мини - ФУНКЦИЯ сообщающая об ошибке КРАСИВО!
function error($info) { global $shapka,$back,$boardm_url; $info=replacer($info);
header('HTTP/1.1 404 Not Found'); // или 301 302 код. Надо смотреть google webmaster
exit("$shapka <center><div id='dalee-galka'>&#10006;</div><div id='dalee-main'><P><B><font color=red>Обнаружена ошибка!<BR>$info!</font></P><font color=red>$back</B></font><BR>Если Вы попали на страницу случайно, то перейдите на <a href='$boardm_url'>главную страницу</a> доски объявлений.
<BR><BR></div></center></body></html>"); return $info;}


// Формируем КЛЮЧЕВЫЕ СЛОВА в переменную guest_keywords начало С 02.2023
if (mb_strlen($boardm_info)<5) $boardm_info=null;
function seokeywords($contents,$symbol=3,$records=25){
    $contents = @preg_replace(array("'<[\/\!]*?[^<>]*?>'si","'([\r\n])[\s]+'si","'&[a-z0-9]{1,6};'si","'( +)'si"),
    array("","\\1 "," "," "),strip_tags($contents));
    $rearray = array("~","!","@","#","$","%","^","&","*","(",")","_","+","`",'"',"№",";",":","?","-","=","|",
					"\"","\\","/","[","]","{","}","'",",",".","<",">","\r\n","\n","\t","«","»","<br>","<BR>");
    $adjectivearray = array("ые","ое","ие","ий","ая","ый","ой","ми","ых","ее","ую","их","ым","как","для","что",
	"или","это","этих","всех","вас","они","оно","еще","когда","где","эта","лишь","уже","вам","нет","если","надо",
	"все","так","его","чем","при","даже","мне","есть","только","очень","сейчас","точно","обычно","можно","может",
	"длина","ширина","высота","глубина","время","минут","секунд","лучше","также","чтобы","когда","тогда");

	$contents = @str_replace($rearray," ",$contents);
    $keywordcache = @explode(" ",$contents);
    $rearray = array();

    foreach($keywordcache as $record){
        if(mb_strlen($record)>=$symbol && !is_numeric($record)){
            $adjective = mb_substr($record,-2);
            if(!in_array($adjective,$adjectivearray) && !in_array($record,$adjectivearray)){
                $rearray[$record] = (array_key_exists($record,$rearray)) ? ($rearray[$record] + 1) : 1;
            }
        }
    }
    @arsort($rearray);
    $keywordcache = @array_slice($rearray,0,$records);
    $keywords = "";
    foreach($keywordcache as $record=>$count){
		$record=preg_replace("/\n\n/",'<p>',$record);
		$record=preg_replace("/\n/",'<br>',$record);
		$record=preg_replace("/\\\$/",'&#036;',$record);
		$record=preg_replace("/\r/",'',$record);
		$record=preg_replace("/\\\/",'&#092;',$record);
		$record=str_replace("\r\n","<br> ",$record);
		$record=str_replace("\n\n",'<p> ',$record);
		$record=str_replace("\n",'<br> ',$record);
		$record=str_replace("\t",'',$record);
		$record=str_replace("\r",'',$record);
		$record=str_replace('   ',' ',$record);
		$keywords.= ",".$record;
    }
    return mb_substr($keywords,1);
} //seokeywords

$exd="$boardm_name, $boardm_info"; $exd=seokeywords("$exd"); // формируем КЛЮЧЕВЫЕ СЛОВА через запятую для роботов

// Собираем в $boardm_keywords ключевые слова: включаем слова более 3 символов. Итого до 160 символов
$exd=explode(",",$exd); $boardm_keywords=null;
$exd=array_unique($exd); $exd=array_values($exd); // оставляем уникальные значения и перестраиваем ключи
$maxi2=count($exd); $i=0;

do { if (mb_strlen($exd[$i])>3 and mb_strlen($boardm_keywords)<160) $boardm_keywords.=$exd[$i].","; $i++; } while ($i<$maxi2);
// конец 02.2023


if (isset($_GET['page'])) $page=$_GET['page']; else $page="1"; if ($page==0) $page="1"; else $page=abs($page);

$fullshapka="<!DOCTYPE html>
<html xml:lang=\"ru\" lang=\"ru\">
<head><title>$boardm_name</title>
<meta http-equiv='Content-Language' content='ru'>
<META HTTP-EQUIV='Pragma' CONTENT='no-cache'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'>
<META content='text/html; charset=UTF-8' http-equiv=Content-Type>
<META name='Description' content='$boardm_info лучшая бесплатная доска объявлений.'>
<META name='Keywords' content='$boardm_keywords'>
<meta name=viewport content='width=device-width, initial-scale=1'>
<link rel='preload' as='style' href='data/$boardm_skin.css' onload=\"this.rel='stylesheet'\">
<link rel=stylesheet type='text/css' href='data/$boardm_skin.css'>
"; // КАНОНИЧЕСКИЕ СТРАНИЦЫ С 02-2023
if ($page==1) $fullshapka.='<link rel="canonical" href="'.$boardm_url.'"/>'; else $fullshapka.='<link rel="canonical" href="'.$boardm_url.'index.php?page='.$page.'"/>';
$fullshapka.="
<SCRIPT language=JavaScript>function x () {return;}
function FocusText() {document.REPLIER.msg.focus(); document.REPLIER.msg.select(); return true;}
function DoSmilie(addSmilie) {var revisedmsgage; var currentmsgage = document.REPLIER.msg.value;
revisedmsgage=currentmsgage+addSmilie; document.REPLIER.msg.value=revisedmsgage; document.REPLIER.msg.focus(); return;}
function DoPrompt(action) {var revisedmsgage; var currentmsgage = document.REPLIER.qmsgage.value;}
</SCRIPT>
</head>
<body>
<h1>$boardm_name</h1><center><DIV>
<a href='$mainpage'><button class='knopka knopka-red'>Главная сайта</button></a>
<a href='$boardm_url'><button class='knopka knopka-red'>Главная доски объявлений</button></a>
<a href='index.php?add'><button class='knopka knopka-red'>Добавить объявление</button></a>
<div><FORM action='index.php?find' method=post>Поиск: <INPUT name=findme> <input type=submit class=mainoption value=' ИСКАТЬ '></form></div>
<hr size=-1 width=80%><center>$boardm_info</div><BR>";



if (isset($_GET['find']))  { // ПОИСК

$pageinfo=""; $itogo="";
setlocale(LC_ALL,'Russian_Russia.65001'); // 2018 - РАЗРЕШАЕМ РАБОТУ ФУНКЦИЙ, работающих с регистором и с РУССКИМИ БУКВАМИ
// setlocale(LC_ALL,'ru_RU.CP1251'); 

if (isset($_POST['findme']))  {   // Поиск объявления

$minfindme="2"; // минимальное кол-во символов, в поисковой фразе

$findme=$_POST['findme'];
$findme=htmlspecialchars($findme);
$findme=stripslashes($findme);
$findme=str_replace("|","I",$findme);
$findme=mb_substr($findme,0,50);
if ($findme=="" || mb_strlen($findme)<$minfindme) error("Ваш запрос пуст, или менее $minfindme символов");

print"$fullshapka";

// Открываем файл с темами формума и запоминаем имена файлов с сообщениями
$lines = file("data/board.csv"); $i=count($lines); $itogo=$i; $number="0";

do {$i--;
    $dt = explode("|", $lines[$i]);
    $stroka=stristr($dt[5], $findme);

    if (mb_strlen($stroka)>"2") {
        $stroka=mb_substr($stroka,0,180); // обрезаем лишнее в сообщении
        $stroka=str_replace("<br>", " &nbsp;&nbsp;", $stroka);
        $stroka=str_replace("<BR>", " &nbsp;&nbsp;", $stroka);
        $stroka=str_replace("[b]","<B>", $stroka);
        $stroka=str_replace("[/b]","</B>", $stroka);
        $stroka=str_replace("[RB]","<B><font color=red>", $stroka);
        $stroka=str_replace("[/RB]","</font></B>", $stroka);
        $stroka=preg_replace ("#([^\[img\]])(http|http|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)#i", "\\1<noindex><a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a></noindex>\\4", $stroka);
        $stroka=preg_replace('#\[img\](.+?)\[/img\]#','<img src="$1" border="0">',$stroka); // запускать ТОЛЬКО после замены АДРЕСА URL!!!
        $stroka=str_replace("<br>"," &nbsp;", $stroka);
        if (!isset($m)) {print "<BR>По запросу '<U><B>$findme</B></U>' найдено:<BR><BR><table border=1 width=100%><TR height=25 align=center class=small bgColor=#cccccc><TD><B>№</B></TD><TD width=100><B>Имя</B></TD><TD width=300><B>Заголовок</B></TD><TD width=*><B>краткое содержание объявления</B></TD></TR>"; $m="1"; }
        $number++;
        $msgnumber=$i-1;
$zdt2=explode("[email]",$dt[2]); if (!isset($zdt2[1])) $zdt2[1]="";
if (mb_strlen($zdt2[1])>3) $name="<a href='mailto:$zdt2[1]'>$zdt2[0]</a>"; else $name="$zdt2[0]";		
print "<TR height=25 class=small bgColor=#FFFFFF>
<TD><B>$number</B></TD><TD>$name</TD><TD><A class=listlink href='index.php?id=$dt[10]'><B>$dt[3]</B></A></TD><TD>$stroka</TD>
</TR><TR><TD colspan=4><hr size=-2 color=#DDDDDD></TD></TR>";
}

} while($i > "0");

if (!isset($m)) print "По вашему запросу ничего не найдено.";
if (!isset($m)) print "<BR><BR><BR>Специфика поиска:<BR> - поисковичок простой, поэтому используйте слово или часть слова. Не рекомедуем использовать фразы.<BR> - поиск ведётся только по тексту объявления.<BR><BR>
Ограничение на поиск: <BR>- минимальное кол-во символов: <B>$minfindme</B>.<BR>";
echo "</table><BR>";
}
}




// БЛОК показывает ОДНО ОБЪЯВЛЕНИЕ

if (isset($_GET['id'])) { if ($_GET['id']!="") {
$lines=file("data/board.csv"); $itogo=count($lines)-1;
$id=replacer($_GET['id']); $num=-1; $last=-1; $next=-1;

// Ищем номер текущего, предыдущего и следующего объявлений с 02.2023
for ($i=0;$i<=$itogo;$i++) { $dtt=explode("|",$lines[$i]);
if ($id==$dtt[10]) {$num=$i; if ($i>0) $last=$i-1; if ($i<$itogo) $next=$i+1;}}

if ($num==-1) error("Срок размещения данного объявления окончен."); // Нет объявления - выдаём ошибку

print"$fullshapka";

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

print"
<div class='set_item2'>
    <div class='boxmax'>
		<h2>$dt[3]</h2>
       	<p style='min-height: 220px;'>$dt[5]</p>
		<p><small>Объявление разместил:</small> $name</p>
		<P><small>Дата подачи: $dt[6]<BR> Дата удаления: $deldate</small><BR></p>
		<font size=+1>Действует: <font size=+2 color=navy>$tektday/$firstday</font> дн.</font>
		</P>
    </div>
</div>";

$navmsg=null; // БЛОК НАВИГАЦИИ ВПЕРЁД-НАЗАД для быстрого перемещения по объявлениям с 02.2023
if ($last!=-1) {$dtlast=explode("|",$lines[$last]); $navmsg.="<div class='wr_block'><a class='wr_knopki dark' title='Предыдущее объявление' href='index.php?id=$dtlast[10]'>&#9668; предыдущее объявление</a></div>";}
    $navmsg.="<div class='wr_block'><a class='wr_knopki red' title='Вернуться на главную страницу' href='$boardm_url'>Вернуться</a></div>";
if ($next!=-1) {$dtnext=explode("|",$lines[$next]); $navmsg.="<div class='wr_block'><a class='wr_knopki blue' title='Следующее объявление' href='index.php?id=$dtnext[10]'>следующее объявление &#9658;</a></div>";}
print $navmsg; $pageinfo="";
} }





if (isset($_GET['save'])) { // Блок ДОБАВЛЕНИЯ ОБЪЯВЛЕНИЯ

// возвращаем значения данных из формы и присваиваем их переменным
if (isset ($_POST['name']) & isset ($_POST['msg']) & isset ($_POST['email'])) {$name=replacer($_POST['name']); $msg=replacer($_POST['msg']); $email=$_POST['email'];} else error("Из формы не поступили данные");
if (isset($_GET['page'])) $page=replacer($_GET['page']); else $page=1;

sleep(1); // мелкая защита от БОТОВ. Человеку секунда не время - а прога по подбору ключа - будет работать долго и не загружать сервер

$ip=replacer($_SERVER['REMOTE_ADDR']); // определяем IP юзера

//Проверка ЗАПРЕТА IP-пользователя на добавление объявлений (файл bad_ip.csv)
if (is_file("data/bad_ip.csv")) { $lines=file("data/bad_ip.csv"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) error("Админитратор заблокировал для Вашего IP: $ip возможность добавлять что-либо по следующей причине:<br> $idt[1].<br>Вам разрешено просматривать сообщения,<br> а вот ДОБАВЛЯТЬ ОБЪЯВЛЕНИЯ категорически ЗАПРЕЩЕНО!");
} while($i > "1");} unset($lines);}

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) error("данные из формы не поступили");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) error("введён ОШИБОЧНЫЙ код");}

if ($name=="" || mb_strlen($name) > $maxname) error("Ваше имя пустое, или превышает $maxname символов");
if (mb_strlen($msg)<50 || mb_strlen($msg)>$maxmsg) error("Ваше сообщение содержит или менее 50 символов или превышает $maxmsg символов.");
if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$email) and mb_strlen($email)>30 and $email!="") error("и введите корректный E-mail адрес");
if (isset($_POST['www'])) {$www=replacer($_POST['www']); if (mb_strlen($www) > 55) error("Вы ввели слишком длинный URL. Он превышает 50 символов");} else $www=null;
$zag=replacer($_POST['zag']); if ($zag == "" || mb_strlen($zag) > $maxzag) error("Вы не ввели заголовок объявления, или он превышает $maxzag символов");
$days=replacer($_POST['days']); if ($days>"365") $days="365";

$deldt=time()+$days*86400; // формируем дату удаления объявления
$name=str_replace("|","I",$name);
$zag=str_replace("|","I",$zag);
$www=str_replace("|","I",$www);
$msg=str_replace("|","I",$msg);
if (preg_match ("/(href)/", $msg)) error("Неудача! Из-за частого СПАМА публикация ссылок в объявлениях запрещена");

$name=wordwrap($name,25,' ',1); $msg=wordwrap($msg,75,' ',1); // разрываем слишком длинные строки

// Структура БД до 02-2023 устарела
//$text="$name|$email|$zag|$www|$msg|$date|$time|$deldt|||"; $text=replacer($text);

//$id - уникальный идентификатор объявления. Сгенерировать с начиная с 1011000
// БЛОК ГЕНЕРИРУЕТ СЛЕДУЮЩИЙ ПО ПОРЯДКУ НОМЕР ОБЪЯВЛЕНИЮ с 02-2023
// считываем весь файл в объявлениями в память
$allid=null; $records=file("data/board.csv"); $imax=count($records); $i=$imax;
if ($i > 0) { do {$i--; $rd=explode("|",$records[$i]); $allid[$i]=$rd[10]; } while($i>0);
$id=1000; $id="101$id";
do { $id++;} while(in_array($id,$allid));
} else $id="1011000"; // if ($i > 0)

$dano=$name."[email]".$email; $today=time(); $ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
// Структура БД как в доске лайт / ЛЮКС 2.0 и выше
$text="1|Разные объявления|$dano|$zag|П|$msg|$date|$deldt|101|no|$id|$today|0|||||||0||$ip||||$www|1|";
$text=replacer($text);

$fp=fopen("data/board.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

if ($sendmail==TRUE) { // отправка сообщения админу на мыло
$headers=""; // Настройки для отправки писем
$headers.="Content-Type: text/html; charset=UTF-8\r\n";
$headers.="From: ".$name." <".$email.">\r\n";
$headers.="X-Mailer: WR-Sendmail PHP/".phpversion();
// Собираем всю информацию в теле письма
$allmsg = $boardm_name.chr(13).chr(10).'Новое объявление : '.$boardm_url.chr(13).chr(10).'Имя: '.$name.chr(13).chr(10).'E-mail: '.$email.chr(13).chr(10).'Текст объявления: '.$msg.chr(13).chr(10);
mail("$adminemail", "$boardm_name (объявление)", $allmsg, $headers);} // Отправляем письмо майлеру на съедение 

if ($page==1) $loc=$boardm_url; else $loc="index.php?page=$page";

print"$shapka<script language='Javascript'>function reload() {location='$loc'}; setTimeout('reload()', 3000);</script>
<div id='dalee-galka'>&#10004;</div>
<div id='dalee-main'><P>Спасибо, <B>$name</B>, Ваше объявление успешно добавлено.<BR>Через несколько секунд Вы будете автоматически перемещены <BR><B>на первую страницу доски объявлений.</B></P>
<div id='dalee-start'><div id='dalee-ramka'><div id='dalee-zalivka'><div id='dalee-line'></div></div></div></div><BR>
<B><a href='$loc'>ДАЛЬШЕ >>></a></B></div></body></html>"; exit;}





// ГЛАВНАЯ СТРАНИЦА ДОСКИ
if (!isset($_GET['id']) and !isset($_GET['find'])) {

// считываем все сообщения
if (is_file("data/board.csv")) { $lines=file("data/board.csv"); $itogo=count($lines); $maxi=$itogo-1;
$maxpage=ceil(($maxi+1)/$msgonpage); } else $maxpage=1;

// Изм. 01-2023
if (!isset($_GET['page'])) $page=1; else {$page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1;}
if ($page>$maxpage) { $page=$maxpage;
$error_msg="Данной страницы не существует. Вы можете перейти на главную страницу доски объявлений";
if ($page>1) $error_msg.=",\r\n либо на последнюю $maxpage страницу";
error($error_msg);}

print "$fullshapka";



if (isset($_GET['add'])) { // ЕСЛИ нажата кнопка ДОБАВИТЬ ОБЪЯВЛЕНИЕ

print"<div class=box1>
<form action='index.php?save' method=post name=REPLIER>
<center><h2>Добавить объявление</h2>
<B>Имя</B>: <input type=text placeholder='Ваше имя' value='' name=name size=26 style='width:38%'>&nbsp;
E-mail: <input type=text placeholder='email@site.ru' value='' name=email size=26 style='width:38%'><br><br>
<B>Заголовок объявления</B>: <input type=text placeholder='Тема объявления' value='' name=zag size=56 style='width:64%'><br><br>";

if ($use_www==TRUE) print"Сайт: <input placeholder='http://ваш-сайт.ru' type=text value='' name=www style='width:84%'><br><br>";

print"<B>Текст объявления:</B> (от 100 до $maxmsg символов)<br><textarea cols=55 rows=16 size=2000 name=msg style='width:90%;height:120px;'></textarea><br><br>
<input type=button class=button value=' B ' style='font-weight:bold; width: 30px' onclick=\"DoSmilie(' [b]  [/b] ');\">&nbsp; <input type=button class=button value=' RB ' style='font-weight:bold; color:red' onclick=\"DoSmilie('[RB] [/RB]');\">&nbsp; <input type=button class=button value=' IMG ' style='font-weight:bold; color:navy' onclick=\"DoSmilie('[img][/img]');\"> - спецвыделение увеличивает рейтинг объявления в поисковых системах.<br>

<B>Срок хранения объявления</B>: (7-365 дней) <SELECT name=days><OPTION value=7>7 дней</OPTION><OPTION value=14>14 дней</OPTION><OPTION selected value=30>30 дней</OPTION><OPTION value=60>60 дней</OPTION><OPTION value=90>90 дней</OPTION><OPTION value=365>365 дней</OPTION></SELECT><BR><BR>";

if ($antispam==TRUE) nospam(); // АНТИСПАМ

print"</center><center><input type='submit' value=' Добавить '></center><br></div>";
}


// ВЫВОДИМ ОБЪЯВЛЕНИЯ

if (is_file("data/board.csv")) { // проверяем есть данные в файле
$lines=file("data/board.csv");
$itogo=count($lines); $maxi=$itogo-1;


//    функция АВТОУДАЛЕНИЯ здесь!
if ($itogo>0) {
$tekdate=time(); $i="0"; $newi="-1"; $todelete=1;
do {$dt=explode("|",$lines[$i]);
    if ($dt[7]<$tekdate) {$todelete++;} else {$newi++; $newlines[$newi]=$lines[$i];} $i++;
} while($i<$itogo);
if (isset($newlines)) {$newitogo=count($newlines)-1;} else {$newitogo="0"; $newlines[0]="";}
if ($todelete>1) {
$fp=fopen("data/board.csv","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0; $i <= $newitogo; $i++) {fputs($fp,$newlines[$i]);}
flock ($fp,LOCK_UN);
fclose($fp);
// считываем данные раздела в память вновь - так как мы удалили просроченные
$lines = file("data/board.csv");
$itogo=count($lines); $maxi=$itogo-1;
} //    функция АВТОУДАЛЕНИЯ выше!
} // if ($itogo>0)


$itogo=count($lines); $maxi=$itogo-1;

if ($itogo>0) {

$maxpage=ceil(($maxi+1)/$msgonpage); if ($page>$maxpage) {$page=$maxpage;}

$fm=$maxi-$msgonpage*($page-1); if ($fm<"0") {$fm=$msgonpage;}
  $lm=$fm-$msgonpage; if ($lm<"0") {$lm="-1";}

if (is_file("data/top.html")) include "data/top.html";

// формируем $pageinfo - СПИСОК СТРАНИЦ
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$msgonpage); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div style='padding:6px;' class=pgbutt>Страницы: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=$boardm_url>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="index.php?page=$i"; $pageinfo.="<a href=$boardm_url$addpage>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=index.php?page=$maxpage>$maxpage</a>";
$pageinfo.='</div>';

print"<BR>$pageinfo<div class='wrapper'>"; $cm=1;

do { $dt=explode("|", $lines[$fm]); $num=1+$maxi-$fm;
$fm--;

//$text="1|Разные объявления|$dano|$zag|П|$msg|$date|$deldt|101|no|$id|$today|0|||||||0||$ip|||$www|||";
$msg=$dt[5];

if (mb_strlen($dt[5])>400) {$dt[5]=mb_substr($dt[5],0,380); $dt[5]=preg_replace("#([^\[img\]])(http|http|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)#i", "\\1<noindex><a href=\"\\2://\\3\" target=\"_blank\">ссылка</a></noindex>\\4", $dt[5]); $dt[5].="... <a href='index.php?id=$dt[10]'> Подробнее...</a>";}
else $dt[5]=preg_replace("#([^\[img\]])(http|http|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)#i", "\\1<noindex><a href=\"\\2://\\3\" target=\"_blank\">ссылка</a></noindex>\\4", $dt[5]);

$dt[5]=preg_replace('#\[img\](.+?)\[/img\]#','<a href="$1">рисунок</a>', $dt[5]);
$dt[5]=str_replace("[b]","<B>",$dt[5]); $dt[5]=str_replace("[/b]","</B>",$dt[5]);
$dt[5]=str_replace("[RB]","<B><font color=red>",$dt[5]); $dt[5]=str_replace("[/RB]","</font></B>",$dt[5]);
$dt[5]=str_replace("<br>","&nbsp;",$dt[5]); $dt[5]=wordwrap($dt[5],50,' ',1);

$deldate=date("d.m.Y",$dt[7]); // конвертируем дату удаления в человеческий формат

$fd=explode(".",$dt[6]);
$then=mktime(0,0,0,$fd[1],$fd[0],$fd[2]);
$firstday=floor(($dt[7]-$then)/86400); // формируем кол-во дней ликвидности объявления

$today=time();
$tektday=floor(($today-$then)/86400); // формируем кол-во дней прошедших со дня подачи объявления
$tday=($dt[7]-$then)/86400;
if ($tektday<1) $tektday=1;

if (mb_strlen($dt[0])>12) {$dt[0]=mb_substr($dt[0],0,9); $dt[0].="...";}
$dtitle=$dt[2]; if (mb_strlen($dt[2],'UTF-8')>50) {$dt[2]=mb_substr($dt[2],0,47); $dt[2].="...";}

$zdt2=explode("[email]",$dt[2]); if (!isset($zdt2[1])) $zdt2[1]="";
if (mb_strlen($zdt2[1])>3) $name="<a href='mailto:$zdt2[1]'>$zdt2[0]</a>"; else $name="$zdt2[0]";

print"
<div class='set_item'>
    <div class='box2'>
		<h2><a href='index.php?id=$dt[10]' title='$dtitle'>$dt[3]</a></h2>
       	<p style='min-height: 220px;'>$dt[5]</p>
		<p><small>Объявление № <B>$num</B> разместил:</small> $name</p>
		<P><small>Дата подачи: $dt[6]<BR> Дата удаления: $deldate</small><BR></p>
		<font size=+1>Действует: <font size=+2 color=navy>$tektday / $firstday</font> дн.</font>
		</P>
    </div>
</div>";
		
} while($lm < $fm);

}  // if ($itogo > 0)
} // if is_file("data/board.csv")
} // если нет id

print"</div><div style='clear:both;'>$pageinfo <BR>Всего объявлений: <B>$itogo</B><BR>";

if (is_file("data/bottom.html")) include "data/bottom.html";

?>
<br><center><small>Powered by <a href='http://www.wr-script.ru/'>WR-Board mini</a> &copy; 2.3 UTF-8</small></body></html>