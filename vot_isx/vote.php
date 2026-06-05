 <? // WR-Golos v 1.5  // 05.12.2018 г. // Miha-ingener@yandex.ru

error_reporting (E_ALL);

include "data/config.php";

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

if (isset($_POST["votec"])) $numv=replacer($_POST["votec"]); else exit("<BR><BR><BR><center> Вы <B>не выбрали ни один пункт</B> голосования!</center>");

$ip=(isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:0;
$ip=replacer($ip);

if (isset($_GET['id'])) {$id=replacer($_GET['id']); if ((!ctype_digit($id)) or (strlen($id)>2)) {exit("<B>Поддерживаются номера голосований от 1 до 9 включительно!!!</B>");}} else {$id=1;}

$lines = file("$golosdir/vote$id.csv");

// Проверка на IP-юзера
if (is_file("$golosdir/ip$id.csv")) { 
$iplines=file("$golosdir/ip$id.csv"); $sizef=count($iplines);
if ($sizef > 1) { $itip=$sizef;
do {$itip--; $idt=explode(";",$iplines[$itip]); 
if ($ip==$idt[0]) { $dayx=date("d.m.Y в H:i:s",$idt[1]); $stime=$idt[1]; $today=time();
if ($antiflud=="1") {if (($today-$stime)<$fludtime)
{print"<center><br><br><br>Включена <B>защита от ФЛУДА</B>.<br> Чаще <B>$fludtime секунд</B> голосовать запрещено.<br><br> <B><a href='vote.php' onClick='self.close()'>Закройте окно</b></a>, подождите указаное время<br> и повторите попытку.</B>"; exit;}}
$allredy="Вы <B>уже голосовали $dayx!</B></center>";}
} while ($itip>0); } }


if ($ipblok!="1") {$allredy=""; unset($allredy);}

if (!isset($allredy)) {$allredy="<B>Ваш голос принят.</B>";
$mkdate=time(); // формируем дату голосования в UNIX-формате
$lines=file("$golosdir/vote$id.csv");
$itogo=count($lines); $i=$itogo;

do {$i--; if ($numv==$i) {$vote=$i;}} while ($i>0);

$i=$itogo;
do {$i--; $dt=explode(";",$lines[$i]); 
if ($vote==$i) {$dt[1]++;}
$lines[$i]="$dt[0];$dt[1];\r\n";
} while ($i>0);

$fp=fopen("$golosdir/vote$id.csv","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0; $i<$itogo; $i++) {fputs($fp,$lines[$i]);}
fflush ($fp);
flock ($fp,LOCK_UN);
@chmod("$fp", 0644);

$fp=fopen("$golosdir/ip$id.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$ip;$mkdate;\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
@chmod("$fp", 0644);
}

print "<center><script language='Javascript'>function reload() {location = 'rezult.php?id=$id'}; setTimeout('reload()', 2000);</script>
<BR><BR><BR> $allredy";
?>