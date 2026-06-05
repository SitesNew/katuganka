<? // WR-forum v 2.3  //  07.01.2023 г.  //  WR-Script.ru
   // WR-Chat mod 1.1 - 2012 г.

error_reporting(0); //error_reporting (E_ALL); // E_ALL включить на времЯ тестированиЯ и отладки скрипта

include "data/config.php";

if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc,ENT_COMPAT); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc);  $wrfname=$wrfc[0];} else {unset($wrfname); unset($wrfpass);}

$datafile="$datadir/chat.csv"; // файл c данными

// Нужно считывать смайлы из конфигурационного файла! БЛОК ПЕРЕДЕЛАТЬ!
$s1="<img src=\"smile/smile.gif\" border=0>";
$s2="<img src=\"smile/biggrin.gif\" border=0>";
$s3="<img src=\"smile/razz.gif\" border=0>";
$s4="<img src=\"smile/cool.gif\" border=0>";
$s5="<img src=\"smile/mad.gif\" border=0>";
$s6="<img src=\"smile/redface.gif\" border=0>";
$s7="<img src=\"smile/wink.gif\" border=0>";
$s8="<img src=\"smile/rolleyes.gif\" border=0>";
$s9="<img src=\"smile/confused.gif\" border=0>";
$s10="<img src=\"smile/eek.gif\" border=0>";
$s11="<img src=\"smile/cry.gif\" border=0>";


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

// Чтение сообщений
$query=$_SERVER["REQUEST_METHOD"]; $eq1=$_SERVER['QUERY_STRING'];

if ($eq1=="") {
print"<html><head><title>МиниЧат</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<link rel=\"stylesheet\" href=\"$forum_skin/style.css\" type=\"text/css\"></head>
<frameset rows=\"140,*\" frameborder=no border=0><frame name=mainFrame src=\"chat.php?event=msgwindow\">
<frame name=bottomFrameright scrolling=No noresize src=\"chat.php?event=login\"></frameset></html>";

} else {

if (isset($_GET['event'])) {
if ($_GET['event']=="msgwindow") { // Фрейм с сообщениями чата

print"<html><head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv='Content-Language' content='ru'>
<meta http-equiv=refresh content=$chatrefresh>
<link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head>
<body style='margin:2px;padding:0px;' topmargin=0 leftmargin=0><span class=ser>";

// считываем данные, заменяем текстовые смайлики на графические, выводим на экран

if (is_file($datafile)) {
$lines=file($datafile); $datasize=sizeof($lines);
if ($datasize==0) exit("<B>Файл с данными чата пуст. Добавьте сообщение!</B>");

$lines=file($datafile); $n=count($lines)-1; $i="-1";
do {$dt=explode("|", $lines[$n]);
$lines[$n]=replacer($lines[$n]);
$dt[4]=str_replace(":-))",$s2,$dt[4]);
$dt[4]=str_replace(":-)",$s1,$dt[4]);
$dt[4]=str_replace(":-P",$s3,$dt[4]);
$dt[4]=str_replace("8-)",$s4,$dt[4]);
$dt[4]=str_replace(":-(",$s5,$dt[4]);
$dt[4]=str_replace(":-{",$s6,$dt[4]);
$dt[4]=str_replace(";-)",$s7,$dt[4]);
$dt[4]=str_replace(":roll:",$s8,$dt[4]);
$dt[4]=str_replace(":omg:",$s9,$dt[4]);
$dt[4]=str_replace(":-/",$s10,$dt[4]);
$dt[4]=str_replace("`-(",$s11,$dt[4]);
$dt[4]=preg_replace("#((http?|ftp)://[[:alnum:]_=/-]+(\\.[[:alnum:]_=/-]+)*(/[[:alnum:]+&._=/%]*(\\?[[:alnum:]?+&_=/%]*)?)?)#is", "<a href=\"$1\" title=\"$1\" target=\"_blank\">$1</a>", $dt[4]);

print"<small>[$dt[0] $dt[1]] <b>$dt[3]</b></font> <span class=small>•</span> $dt[4]</small><BR>\r\n"; //
$n--;
} while($n > $i);

} else exit("Нет сообщений! Добавьте!"); // if is_file

print "</span></body></html>";
} // if $event=="msgwindow"


// Нижний фрейм - находимся в чате уже пишем сообщения
if ($_GET['event']=="login")  {

if (isset($wrfname)) $name=replacer($wrfname); else $name="";

print"<link rel=stylesheet href=\"$forum_skin/style.css\" type=\"text/css\">
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
function DoPrompt(action) {var revisedmsgage; var currentmsgage = document.REPLIER.qmsgage.value;}
</SCRIPT>

<script language='Javascript' type='text/javascript'>
function setType(symbol) {
obj=opener.document.REPLIER.msg;
obj.value=obj.value + symbol;
obj.focus(); }
</script>
";

if(@$name){ ?>

<script>
function l(){top.list.location.reload();top.online.location.reload()}
function f(){parent.send.s.msg.focus()}
</script>

<body style='margin:0px;padding:0px;' onfocus=f() topmargin=0 leftmargin=0 class=frameenter>
<div align=center>
<table border=0 width="100%" cellpadding=0 cellspacing=0><tr><td width="205px">
<form method="post" action="chat.php?event=login" name=REPLIER><A href='javascript:%20x()' onclick="DoSmilie(' :-) ');"><?=$s1?></a><a href='javascript:%20x()' onclick="DoSmilie(' :-)) ');"><?=$s2?></a><a href='javascript:%20x()' onclick="DoSmilie(' :-P ');"><?=$s3?></a><a href='javascript:%20x()' onclick="DoSmilie(' 8-) ');"><?=$s4?></a><a href='javascript:%20x()' onclick="DoSmilie(' :-( ');"><?=$s5?></a><A href='javascript:%20x()' onclick="DoSmilie(' :-{ ');"><?=$s6?></a><A href='javascript:%20x()' onclick="DoSmilie(' ;-) ');"><?=$s7?></a><A href='javascript:%20x()' onclick="DoSmilie(' :roll: ');"><?=$s8?></a><A href='javascript:%20x()' onclick="DoSmilie(' :omg: ');"><?=$s9?></a><A href='javascript:%20x()' onclick="DoSmilie(' :-/ ');"><?=$s10?></a></td>
<td valign="top"><input type="text" size=<?=$chatinput?> name="msg" class=box style="height:17"><input type=hidden name=name value=<?=$name?>> <input class=but style="width:32;height:20" type=submit value="OK" name=Submit2></td></tr></table></form></div>

<? 

} else {  // Нижний фрейм - НЕ участник, то выводим текст, что пользователь не зарегистрирован!

print"</tr></table></td></tr><body style=\"margin:2px;padding:0px;\" topmargin=0 leftmargin=0><table cellspacing=0 cellpadding=0 align=center>
<tr><td align=center valign=top><font style=\"font-size:12px;font-family:tahoma;\">Вы не зарегистрированы на форуме!</font></td></tr></tr></table>";
}



} //if isset(event)

if(isset($_POST['msg'])) { // ДОБАВЛЕНИЕ СООБЩЕНИЯ

$date=date("d.m.y"); // число.месяц.год
$time=date("H:i:s"); // часы:минуты:секунды

$name=replacer($_POST['name']); $name=str_replace("\n","",$name); 
$name=str_replace("|","I",$name); $name=wordwrap($name,30,' ',1); // разрываем длинные строки
$msg=replacer($_POST['msg']);
$msg=str_replace("|","I",$msg); $msg=str_replace("\r\n", "<br>", $msg);
$msg=wordwrap($msg,100,' ',1); $msg=mb_substr($msg,0,$chatmaxmsg); // обрезаем лишнее в сообщении
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
$text="$date|$time|000000|$name|$msg|$ip|||\r\n";
$fp=fopen($datafile,"a+"); flock ($fp,LOCK_EX); fputs($fp,$text); flock ($fp,LOCK_UN); fclose($fp); // ЗАПИСЬ данных в файл

// Удаление последней строки
$filedat=file($datafile);$i=count($filedat);
if ($i>=$chatmsg_onpage) { $fp=fopen($datafile,"w"); flock ($fp,LOCK_EX); unset($filedat[0]); fputs($fp, implode("",$filedat)); flock ($fp,LOCK_UN); fclose($fp);}
}
}


} // if isset(event)

?>