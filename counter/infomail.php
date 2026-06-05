<? // WR-Counter v 1.5 UTF-8  //  25.12.18 г.  //  Miha-ingener@yandex.ru

error_reporting(0); //error_reporting (E_ALL); 

$robotemail=$adminemail; // Вы можете прописать свой адрес электронной почты

include("data/config.php");

$months=array("Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
$deldt=time()-$days*86400; // формируем дату удаления объявления

function prcmp ($a, $b) {if ($a==$b) return 0; if ($a<$b) return -1; return 1;}


$i=0; if ($handle = opendir($coundir)) {
while (($file = readdir($handle)) !== false)
if (!is_dir($file)) {
if (preg_match("/[0123456789]/",$file)==true) {$records[$i]=$file; $i++;}}
closedir($handle);
}

$itogo=count($records); $k=0; $text=null;

do {
$fline=file("$coundir/$records[$k]");
$fitogo=count($fline);
if ($fitogo!=0 and preg_match("/[0123456789]/",$records[$k])) {
$thendayx=str_replace(".csv","",$records[$k]);
$dt=explode(".",$thendayx);
$then=mktime(0,0,0,$dt[1],$dt[0],$dt[2]);
$tekdate=date("d.m.Y",$then);

// Удаляем старые данные
if ($deldt>$then) {unlink ("$coundir/$records[$k]");}

// Блок считает ХОСТЫ (уникальных посетителей)
usort($fline,"prcmp");
$numip="0"; $numsys="0"; $hi=0;  $ab="0"; $ac="0";

do {
$dt=explode(";",$fline[$hi]); 
if ($ab!=$dt[0]) {$ab=$dt[0]; $numip++;}
if (isset($dt[2])) {if ($ac!=$dt[2]) {$ac=$dt[2]; $numsys++;}}
$hi++;
} while ($hi<$fitogo);

$text.="$then;$fitogo;$numip;$numsys;\r\n";
}
$k++;
} while ($k<$itogo);


$fp=fopen("$coundir/mainbase.csv","w");
flock ($fp,LOCK_EX);
fputs($fp,"$text");
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);
@chmod("$coundir/mainbase.csv", 0644);


// сортируем данные 
$records=file("$coundir/mainbase.csv");
$maxi=count($records); usort($records,"prcmp");

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$cnturl1="https://$host$self"; $cnturl1=str_replace("counter.php", "info.php", $cnturl1);
$cnturl=str_replace("infomail.php", "counter.php", $cnturl1);



// Формируем статистику посещений (ХИТЫ/ХОТСЫ)
$msg="<HTML><head><META content='text/html; charset=UTF-8' http-equiv=Content-Type><style>BODY {FONT-FAMILY: Verdana; FONT-SIZE: 11px} TD {FONT-SIZE: 10px}</style></head>
<BODY text=#000000 leftMargin=0 topMargin=0 rightMargin=0 bottomMargin=0 marginheight=0 marginwidth=0><center>
<B>Информация по посещаемости сайта</B><BR>Сгенерирована по данным счётчика за $days дней: <a href='$cnturl'>$cnturl</a>
<TABLE cellSpacing=0 cellPadding=0 width='98%'><TR><TD>\r\n";

// выводим 1 график - ХИТЫ
$msg.="<table cellSpacing=0 cellPadding=0 align=center><tr height=250 align=center valign=bottom><TD valign=middle>К<BR>О<BR>Л<BR>-<BR>В<BR>О<BR><B><BR>Х<BR>И<BR>Т<BR>О<BR>В</TD>\r\n";

for ($i=0; $i<$maxi; $i++)  {
$dtt=explode(";",$records[$i]);
$dtt[0]=date("d.m.y",$dtt[0]);
$dttn=round($dtt[1]*$scale1);
$msg.="<TD><table cellPadding=0><TR><TD align=center>$dtt[1]</TD></TR><TR><TD><table cellPadding=0><TR><TD height=$dttn width=15 bgcolor=#3E8D32>&nbsp;&nbsp;&nbsp;&nbsp;</TD></TR></table></TD></TR></TABLE></td>\r\n";
}
$msg.="</TR><TR><TD align=center>Дата</TD>\r\n";

for ($i=0; $i<$maxi; $i++)  {
$dtt=explode(";",$records[$i]);
if (!isset($m0)) {$m0=date("m",$dtt[0])-1;}
if ($i==$maxi-1) {$m1=date("m",$dtt[0])-1;}
$xday=date("d",$dtt[0]);
$dayn=date("w",$dtt[0]); // Считаваем день недели по порядку
if ($dayn=="0" or $dayn=="6") $weekstyle="bgcolor=#FF7979"; else $weekstyle="";
$msg.="<td $weekstyle align=center><a href='$cnturl1?day=$dtt[0]'>$xday</a></TD>\r\n"; }

$mm1=$months[$m0]; $mm2=$months[$m1]; if ($mm1==$mm2) {$mm1="";} else {$mm1.=" - ";}
$msg.="</tr><TR><TD>Месяц</TD><TD align=center colspan=$i>$mm1 $mm2</TD></TR></TABLE>\r\n";


// выводим 2 график - ХОСТЫ
$msg.="<table cellSpacing=0 cellPadding=0 align=center><tr align=center valign=bottom><TD valign=middle>К<BR>О<BR>Л<BR>-<BR>В<BR>О<BR><B><BR>Х<BR>О<BR>С<BR>Т<BR>О<BR>В</TD>\r\n";

for ($i=0; $i<$maxi; $i++)  {
$dtt=explode(";",$records[$i]);
$dtt[0]=date("d.m.y",$dtt[0]);
$dttn=round($dtt[2]*$scale2);
$msg.="<TD><table cellPadding=0><TR><TD align=center>$dtt[2]</TD></TR><TR><TD><table cellPadding=0><TR><TD height=$dttn width=15 bgcolor=#275EC2>&nbsp;&nbsp;&nbsp;&nbsp;</TD></TR></table></TD></TR></TABLE></td>\r\n";
}
$msg.="</TR><TR><TD align=center>Дата</TD>\r\n";

for ($i=0; $i<count($records);$i++)  {
$dtt=explode(";",$records[$i]);
if (!isset($m0)) {$m0=date("m",$dtt[0])-1;}
if ($i==$maxi-1) {$m1=date("m",$dtt[0])-1;}
$xday=date("d",$dtt[0]);
$dayn=date("w",$dtt[0]); // Считаваем день недели по порядку
if ($dayn=="0" or $dayn=="6") $weekstyle="bgcolor=#FF7979"; else $weekstyle="";
$msg.="<td $weekstyle align=center><a href='$cnturl1?day=$dtt[0]'>$xday</a></TD>\r\n"; }

$mm1=$months[$m0]; $mm2=$months[$m1]; if ($mm1==$mm2) {$mm1="";} else {$mm1.=" - ";}
$msg.="</tr><TR><TD>Месяц</TD><TD align=center colspan=$i>$mm1 $mm2</TD></TR></TABLE>\r\n";

$msg.="</TD></TR></TABLE>
</center>Пояснение: <B>Хиты</B> - кол-во посещений страниц где установлен счётчик;<BR>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <B>Хосты</B> - кол-во посетителей с уникальным IP-адресом. В 90% случаев - уникальные посетители.<BR>
* Данное сообщение сгенерировано и отправлено роботом, отвечать на него не нужно.<BR><BR>
<center>Powered by <a href='https://www.wr-script.ru/' target='_blank'>WR-Counter</a> &copy; 1.5 UTF-8<br></center>
</TD></TR></table>
</body></html>";

// Пишем в файл дату отправки статистики на емайл
$text=time();
$fp=fopen("$coundir/last.csv","w");
flock ($fp,LOCK_EX);
fputs($fp,"$text");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

$zag=null; // Формируем заголовок письма
$zag.="Content-Type: text/html; charset=UTF-8\r\n";
$zag.="From: Администратор <".$robotemail.">\r\n";
$zag.="X-Mailer: PHP/".phpversion()."\r\n";

mail("$adminemail", "WR-Counter: статистика посещаемости сайта ($host)",$msg,$zag);
?>
