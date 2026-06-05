<? // WR-forum v 2.3  //  07.01.2023 г.  //  WR-Script.ru

error_reporting(0); //error_reporting (E_ALL); // E_ALL включить на время тестирования и отладки скрипта

@ini_set('register_globals','off');// Все скрипты написаны для этой настройки php

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
//if (get_magic_quotes_gpc()) { $text=str_replace("&#092;&quot;",'&quot;',$text); $text=str_replace("&#092;'",'\'',$text); $text=str_replace("&#092;&#092;",'&#092;',$text); }
$text=str_replace("\r\n","<br> ",$text);
$text=str_replace("\n\n",'<p> ',$text);
$text=str_replace("\n",'<br> ',$text);
$text=str_replace("\t",'',$text);
$text=str_replace("\r",'',$text);
$text=str_replace('   ',' ',$text);
return $text; }

$shapka="<html><head><title>Голосование</title><META HTTP-EQUIV='Pragma' CONTENT='no-cache'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'><META content='text/html; charset=UTF-8' http-equiv=Content-Type>
<link rel='stylesheet' href='$forum_skin/style.css' type='text/css'>
<meta name=\"Robots\" content=\"noindex,follow\"></head><body>";

// Проверяем ID на взлом
if (isset($_GET['id'])) $id=replacer($_GET['id']); else exit("$shapka<B>Отсутствует обязательный параметр - ID: уникальный номер голосования!!!</B>");
if ((!ctype_digit($id)) or (mb_strlen($id)>7)) exit("$shapka<B>Номер голосования должен состоять только из 7-и цифр!</B>");
if (!is_file("$datadir/$id-vote.csv")) exit("$shapka<B>Отсутствует файл голосования!</B>");




if (isset($_GET['rezultat'])) { // ПРОСМОТР РЕЗУЛЬТАТА голосования

// Считаем общее кол-во голосов
$lines=file("$datadir/$id-vote.csv"); $itogo=count($lines); $i=1; $glmax=0;
do {$dt=explode("|",$lines[$i]); $glmax=$glmax+$dt[1]; $i++; } while($i<$itogo); $i=1; $all=$glmax;
$vdt=explode("|",$lines[0]);

print"$shapka <FORM name=wrvote action='vote.php' method=post><center><h4>Голосование: $vdt[0]</h4><TABLE cellSpacing=0 cellPadding=2>";

do {$dt=explode("|",$lines[$i]);
if ($glmax==0) $glmax=0.1;
$glpercent=round(100*$dt[1]/$glmax); $hcg=round($glpercent);
if ($glpercent<2) $hcg=2;
if ($glpercent>100) $hcg=100;
if (($i/2)==round($i/2)) echo'<TR height=25 class=row1>'; else echo'<TR height=25 class=row2>';
print"<TD width=25>&nbsp;</TD><TD><B>$dt[0]</B></TD><TD><B>$dt[1]</B></TD><TD>(<B>$glpercent</B> %)</TD>
<TD><TABLE cellSpacing=0 cellPadding=5 width=$hcg height=11><TR bgcolor=#FF8000><TD><img src='$forum_skin/spacer.gif' border=0></TD></TR></TABLE></TD></TR>";
$i++;
} while($i<$itogo);

print"<TR height=25><TD>&nbsp;</TD><TD>Итого проголосовало:</B></TD><TD cospan=3><B>$all</B></TD></TR>
</FORM></TD></TR></TABLE></TD></TR></TABLE><br><a href='vote.php' onClick='self.close()'>Закрыть окно</b></a>";
exit; } // конец блока результатов




// БЛОК голосования
if (isset($_POST["votec"])) $numv=replacer($_POST["votec"]); else exit("$shapka<center><B>Сделайте выбор одого любого пункта</B> голосования!");
$ip=(isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:0;

if (is_file("$datadir/$id-ip.csv")) { // Проверка на IP-юзера
$iplines=file("$datadir/$id-ip.csv"); $sizef=count($iplines);
if ($sizef >= 1) { $itip=$sizef;
do {$itip--; $idt=explode("|",$iplines[$itip]);
if ($ip==$idt[0]) { $dayx=date("d.m.Y в H:i:s",$idt[1]); $stime=$idt[1]; $today=time();
if ($antiflud=="1") {if (($today-$stime)<$fludtime)
exit("$shapka<center><br><br><br>Включена <B>защита от ФЛУДА</B>.<br> Чаще <B>$fludtime секунд</B> 
голосовать запрещено.<br><br> <B><a href='vote.php' onClick='self.close()'>Закройте окно</b></a>,
подождите указаное время<br> и повторите попытку.</B>");
}
$allredy="Вы <B>уже голосовали $dayx!</B></center>";}
} while ($itip>0); } }

if ($ipblok==FALSE) {$allredy=""; unset($allredy);}

if (!isset($allredy)) {$allredy="<B>Ваш голос принят.</B>";
$mkdate=time(); // формируем дату голосования в UNIX-формате
$lines=file("$datadir/$id-vote.csv");
$itogo=count($lines); $i=$itogo;

do { $i--; if ($numv==$i) $vote=$i; } while ($i>0);
$i=$itogo;
do {$i--; $dt=explode("|",$lines[$i]); 
if ($vote==$i) $dt[1]++;
$lines[$i]="$dt[0]|$dt[1]|\r\n";
} while ($i>0);

$fp=fopen("$datadir/$id-vote.csv","a+"); // Добавляем +1 в файл с голосами
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($i=0; $i<$itogo; $i++) fputs($fp,$lines[$i]);
fflush ($fp);
flock ($fp,LOCK_UN);

$fp=fopen("$datadir/$id-ip.csv","a+"); // Пишем IP и дату время голосования
flock ($fp,LOCK_EX);
fputs($fp,"$ip|$mkdate|\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
}

print "$shapka<center><script language='Javascript'>function reload() {location='vote.php?rezultat&id=$id'}; setTimeout('reload()', 3000);</script><BR><BR><BR> $allredy";

?>