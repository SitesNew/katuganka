<? // WR-Counter v 1.5.1 UTF-8  //  21.08.19 г.  //  Miha-ingener@yandex.ru

error_reporting(0); //error_reporting (E_ALL); 

include "data/config.php";

function prcmp ($a, $b) {if ($a==$b) return 0; if ($a<$b) return -1; return 1;}
$months=array("Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
$weeks=array("Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота");
$deldt=time()-$days*86400; // формируем дату удаления файлов статистики


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

// 26-12-2018 - таблица стилей простая

$shapka="<HTML><head><META content='text/html; charset=UTF-8' http-equiv=Content-Type><title>Информация по посещаемости сайта</title><meta name=\"Robots\" content=\"noindex,nofollow\">$style
<style>
.block{width: 80%; min-width: 300px; min-height: 50px; box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);
border: 1px solid #f44336; transition:box-shadow .25s;padding:20px;margin:0.5rem 0 1rem 0;border-radius:2px;}
H3 {text-align:center;font-size:16px;}
</style>
</head>
<BODY text=#000000 leftMargin=0 topMargin=0 rightMargin=0 bottomMargin=0 marginheight=0 marginwidth=0>";


$host=replacer($_SERVER["HTTP_HOST"]); $self=replacer($_SERVER["PHP_SELF"]); $cnturl="https://$host$self"; $cnturl=str_replace("info.php", "", $cnturl);

if (is_file("$coundir/mainbase.csv")) $ftime=filemtime("$coundir/mainbase.csv")+300; else $ftime=0; // + 5 минут
$tektime=time();

if ($ftime<$tektime)  { // Если дата создания файла mainbase.csv менее 5 минут назад, статистику НЕ ПЕРЕСТРАИВАЕМ! изм. 11.2018

$i=0; if ($handle = opendir($coundir)) {
while (($file = readdir($handle)) !== false)
if (!is_dir($file)) {
if (preg_match("/[0123456789]/",$file)==true) {$records[$i]=$file; $i++;}}
closedir($handle);
} else print 'В папке, которую вы указали нет данных счётчика!';
$itogo=count($records); $k=0; $text=null;

do {
$fline=file("$coundir/$records[$k]"); $fitogo=count($fline);

if ($fitogo!=0 and preg_match("/[0123456789]/",$records[$k]))
{
	
$thendayx=str_replace(".csv","",$records[$k]);
$dt=explode(".",$thendayx);
$then=mktime(0,0,0,$dt[1],$dt[0],$dt[2]);
$tekdate=date("d.m.Y",$then);

if ($deldt>$then) unlink ("$coundir/$records[$k]"); // Удаляем статистику позднее $maxdays (XX дней, указано в админке)

// Блок считает ХОСТЫ (уникальных посетителей)
usort($fline,"prcmp"); $numip="0"; $hi=0; $ab="0"; $ac="0";

do { $dt=explode(";",$fline[$hi]);
if ($ab!=$dt[0]) {$ab=$dt[0]; $numip++;}
$hi++; } while ($hi<$fitogo);

$text.="$then;$fitogo;$numip;;$records[$k];\r\n";
}
unset($fitogo);
unset($fline);
$k++;
} while ($k<$itogo);

$fp=fopen("$coundir/mainbase.csv","w");
flock ($fp,LOCK_EX);
fputs($fp,"$text");
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);
@chmod("$coundir/mainbase.csv", 0644);

} // if ($ftime<$tektime)

// сортируем данные 
$records=file("$coundir/mainbase.csv"); $maxi=count($records); usort($records,"prcmp"); $recordsm=$records; $maxm=$maxi;






if (isset($_GET['month'])) { // изм. 12.2018 - ПРОСМОТР СТАТИСТИКИ ЗА МЕСЯЦ(весь период) (сумма по всем страницам)
$i=0; // Собираем всю статистику в файл

if (is_file("$coundir/month.csv")) $ftime=filemtime("$coundir/month.csv")+259200; else $ftime=0; // + 3 ДНЯ (обновляется статистика раз в 3 дня!)
$tektime=time();

if ($ftime<$tektime)  { // Если дата создания файла менее Х дней назад, статистику НЕ ПЕРЕСТРАИВАЕМ!


$fp=fopen("$coundir/month.csv","w");
flock ($fp,LOCK_EX);
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);

do { $dt=explode(";",$records[$i]);
// Собираем все дни в один файл
$dayfilename="$dt[4]"; // Собираем все просмотренные страницы и потом их сортируем
$data=file_get_contents("$coundir/$dayfilename");
$fp=fopen("$coundir/month.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,$data);
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);
$i++; } while ($i<$maxi);

} // if ($ftime<$tektime)


if (is_file("$coundir/month.csv")) { $month=file("$coundir/month.csv"); $maxy=count($month); $hitm=$maxy;}

// Считываем дату создания страницы
$ftime=filemtime("$coundir/month.csv"); $tekdate=date("d.m.Y г, H:i:s",$ftime);

$i=0; do { $dt=explode(";",$month[$i]);
$newlines[$i]="$dt[4]"; // итого по каждой странице
$lines_os[$i]="$dt[3]"; // Собираем все операционные системы
$lines_br[$i]="$dt[2]"; // Собираем все браузеры
$i++; } while ($i<$maxy);

///////////// блок новый 24-12-2018 г.
$newlines2=array_count_values($newlines); // считаем кол-во посещений каждой страницы
$lines_os2=array_count_values($lines_os); // считаем кол-во операционных систем
$lines_br2=array_count_values($lines_br); // считаем кол-во браузеров

arsort($newlines2); reset($newlines2); // сортируем
arsort($lines_os2); reset($lines_os2);
arsort($lines_br2); reset($lines_br2);




//print"<PRE>"; print_r ($lines_br2); exit;

// Работаем с ОС
$msg_os="<h3 align=center>Статистика по операционным системам (по хитам)</h3><TABLE border=0 align=center>
<TR align=center><TD>№ п/п</TD><TD>ОС</TD><TD>Просмотров</TD></TR>";
$i=0; $maxs="0"; do { $massiv=each($lines_os2);
$num=$i+1;
if ($maxs=="0") $maxs=$massiv[1];
$m2=intval(($massiv[1]*500)/$maxs); if ($m2<3) $m2=3; // 500 - ширина максимальной ячейки в пикселях!
$delta=100*$massiv[1]/$maxy;
$addstyle="bgcolor=#CAFFCA";
$procent=round($delta,1); $procent="($procent %)";
if (strlen($massiv[0])<1) $massiv[0]="ОС НЕ РАСПОЗНАНА";
$msg_os.="<TR $addstyle><TD align=center>$num</TD><TD>";
$hrtext=$massiv[0]; if (strlen($massiv[0])>70) { $hrtext=substr($hrtext,0,70); $hrtext.="...";}
if ($massiv[1]>1 and $massiv[0]!="СТРАНИЦА НЕ РАСПОЗНАНА") $msg_os.="$hrtext"; else $msg_os.="$hrtext";
$msg_os.="</td><td><img src='images/g1.gif' width=$m2 height=16 border=0> <B>$massiv[1]</B> $procent</TD></TR>"; $i++; } while ($i<count($lines_os2));
$msg_os.="</TABLE><br>";
//print_r($lines_os2);

// Работаем с браузерами
$msg_br="<h3 align=center>Статистика по браузерам (по хитам)</h3><TABLE border=0 align=center><TR align=center><TD>№ п/п</TD><TD>Браузер</TD><TD>Просмотров</TD></TR>"; 
$msg_br_c=""; $msg_br_s=""; $msg_br_f=""; $msg_br_o=""; $itogo=nul; $maxs="0"; $msg_brouser=null;
$i=0; do { $massiv=each($lines_br2);
$num=$i+1; $itogo=$itogo+$massiv[1];
if ($maxs=="0") $maxs=$massiv[1];
$m2=intval(($massiv[1]*500)/$maxs); if ($m2<3) $m2=3; // 500 - ширина максимальной ячейки в пикселях!
$delta=100*$massiv[1]/$maxy; $addstyle="";
if(stristr($massiv[0],"chrome")) {$addstyle="bgcolor=#C1FFAA";  $msg_brouser['CHROME']=$msg_brouser['CHROME']+$massiv[1];}
if(stristr($massiv[0],"firefox")) {$addstyle="bgcolor=#FFB366"; $msg_brouser['Firefox']=$msg_brouser['Firefox']+$massiv[1];}
if(stristr($massiv[0],"opera")) {$addstyle="bgcolor=#FFAAAA";  $msg_brouser['Opera']=$msg_brouser['Opera']+$massiv[1];}
if(stristr($massiv[0],"safari")) {$addstyle="bgcolor=#FDFFAA"; $msg_brouser['Safari']=$msg_brouser['Safari']+$massiv[1];}
if(stristr($massiv[0],"MSIE")) {$addstyle="bgcolor=#FFFFAA"; $msg_brouser['MSIE']=$msg_brouser['MSIE']+$massiv[1];}
//Safari 

$procent=round($delta,0); $procent="($procent %)";
if (strlen($massiv[0])<1) $massiv[0]="ОС НЕ РАСПОЗНАНА";
$msg_br.="<TR $addstyle><TD align=center>$num</TD><TD>";
$hrtext=$massiv[0]; if (strlen($massiv[0])>70) { $hrtext=substr($hrtext,0,70); $hrtext.="...";}
if ($massiv[1]>1 and $massiv[0]!="СТРАНИЦА НЕ РАСПОЗНАНА") $msg_br.="$hrtext"; else $msg_br.="$hrtext";
$msg_br.="</td><td><img src='images/g1.gif' width=$m2 height=16 border=0> <B>$massiv[1]</B> $procent</TD></TR>"; 
if ($massiv[1]=="3") $i=count($lines_br2);
$i++; } while ($i<count($lines_br2));


// переделать блок! Изм. 01-2019г.
$msg_br.='<tr><td colspan=3 align=center><h3>ИТОГО (по самым популярным браузерам):</h3></td></tr>';
$maxs=$msg_brouser['CHROME']+$msg_brouser['Firefox']+$msg_brouser['Opera']+$msg_brouser['Safari'];
// собираем данные в переменную
for ($p=0;$p<6;$p++) {
if ($p==1) {$msgp=$msg_brouser['CHROME']; $brozer="CHROME";}
if ($p==2) {$msgp=$msg_brouser['Firefox']; $brozer="Firefox";}
if ($p==3) {$msgp=$msg_brouser['Opera']; $brozer="Opera";}
if ($p==4) {$msgp=$msg_brouser['Safari']; $brozer="Safari";}
if ($p==5) {$msgp=$msg_brouser['MSIE']; $brozer="MSIE";}
if ($msgp>10) { $procent=round($msgp*100/$maxs);
$msg_br.="<TR><TD align=center>-</TD><TD>$brozer</td><td><img src='images/g1.gif' width=$procent height=16 border=0> <B>$msgp</B> ($procent %)</TD></TR>";}
} $msg_br.='</TABLE><br>';




















// Работаем со страницами
$msg.="<h3 align=center>Статистика по просматриваем страницам (по хитам)</h3> <center>Дата и время генерации статистики: $tekdate<BR></center>";
$i=0; $deltaitog=0; $pages=0; $maxs="0";
do { $massiv=each($newlines2);
if ($maxs=="0") $maxs=$massiv[1];
$m2=intval(($massiv[1]*500)/$maxs); if ($m2<3) $m2=3; // 500 - ширина максимальной ячейки в пикселях!
$delta=100*$massiv[1]/$maxy;
if ($delta>2) $addstyle="bgcolor=#E6FFE6"; else $addstyle="";
if ($delta>5) $addstyle="bgcolor=#CAFFCA";
if ($delta>10) $addstyle="bgcolor=#64FF64";

if ($delta>"0.1") { // Если просмотров больше 0.1%
$deltaitog=$deltaitog+$delta;
$pages=$pages+$massiv[1];
$procent=round($delta,1); $procent="($procent %)";
if (strlen($massiv[0])<1) $massiv[0]="СТРАНИЦА НЕ РАСПОЗНАНА";

$msg.="<TR $addstyle><TD align=center>$i</TD><TD>";
$hrtext=$massiv[0]; if (strlen($massiv[0])>70) { $hrtext=substr($hrtext,0,70); $hrtext.="...";}
if ($massiv[1]>1 and $massiv[0]!="СТРАНИЦА НЕ РАСПОЗНАНА") $msg.="<a href='$massiv[0]'>$hrtext</a>"; else $msg.="$hrtext";
$msg.="</td><td><img src='images/g1.gif' width=$m2 height=16 border=0> <B>$massiv[1]</B> $procent</TD></TR>";
} // if ($delta>0,1) {

$i++; } while ($i<=count($newlines2));
$deltaitog=round($deltaitog,1);
$msg.="<TR $addstyle><TD align=center>-</TD><TD><B>ИТОГО с посещаемостью >= 0.1%:</B></td><td><B>$pages ($deltaitog %)</B></TD></TR>";


// +++ Выводим статистику по ОПЕРАЦИОННЫМ СИСТЕМАМ и браузерам

print"$shapka<center><a href='https://$host'>Главная сайта</a> :: <a href='info.php'>Страница статистики</a> :: <a href='info.php?month'>Статистика за период</a><BR><BR>
<table border=0 cellSpacing=0 cellPadding=0 align=center>
<TR align=center><TD>№ п/п</TD><TD>Страница</TD><TD>Просмотров *</TD></TR>
$msg </table> $msg_os $msg_br </center>";

//echo'<PRE>'; print_r($newlines2);

exit; } // if isset($month)




 
 





if (isset($_GET['day'])) { $day=$_GET['day']; // ПРОСМОТР СТАТИСТИКИ ЗА СУТКИ (по каждой странице)

if (strlen($day)<12 and ctype_digit($day)) {
$file=date("d.m.Y",$day);
if (is_file("$coundir/$file.csv")) { $records=file("$coundir/$file.csv"); $maxi=count($records); $hitov=$maxi;

if (isset($_POST['findme'])) {
$findme=replacer($_POST['findme']);
$stroka=strlen($findme); if($stroka>2 and $stroka<30) {
$temprecord=$records; $records=null; $i=0; $hitov=count($temprecord);
foreach($temprecord as $v) {if (strpos($v,$findme)) {$i++; $records[$i]=$v;}}
$maxi=$i; $hitov="$hitov / $maxi";}} else $findme="";

// Блок считает ХОСТЫ (уникальных посетителей)
usort($records,"prcmp");
$numip=0; $numsys=0; $i=0; $ab="0"; $ac="0"; $ad="0"; $newlines=null;

do { $dt=explode(";",$records[$i]); 
// Собираем все страницы
$newlines[$i]="$dt[4]"; // Собираем все просмотренные страницы и потом их сортируем
$lines_os[$i]="$dt[3]"; // Собираем все операционные системы
$lines_br[$i]="$dt[2]"; // Собираем все браузеры
$lines_tm[$i]=date("H",$dt[1]); // Собираем время просмотра
$i++; } while ($i<$maxi);
usort($newlines,"prcmp");  $i=0;

do { $dt=explode(";",$records[$i]);
if ($ab!=$dt[0]) {$ab=$dt[0]; $numip++;}
if ($ad!=$newlines[$i]) {$ad=$newlines[$i]; $numsys++;}
$i++; } while ($i<$maxi);

$i=1; $msg=""; // Собираем информацию о посещаемых страницах

$newlines2=array_count_values($newlines); // считаем кол-во посещений каждой страницы
$lines_os2=array_count_values($lines_os); // считаем кол-во операционных систем
$lines_br2=array_count_values($lines_br); // считаем кол-во браузеров
$lines_tm2=array_count_values($lines_tm); // сворачиваем по времени

arsort($newlines2); reset($newlines2); // сортируем
arsort($lines_os2); reset($lines_os2);
arsort($lines_br2); reset($lines_br2);
ksort($lines_tm2); reset($lines_tm2); // сортируем по КЛЮЧУ. изм. 21-08-19
//print"<PRE>"; print_r ($lines_tm2); exit; // проверка что в переменной

// Статистика по часам. изм. 21-08-2019
$t="0"; $msg_tm="<div class=block><h3 align=center>Статистика по времени просмотра страниц (время посещения)</h3>";
$msg_tm.="
<TABLE border=0 cellspacing=0 cellpadding=10 align=center><TR>";
do { // начало FOR
if ($t<10) $p="0$t"; else $p=$t;
if ($t<24)  {
if (isset($lines_tm2[$p])) {$tm2=$lines_tm2[$p]; $hey=round($tm2*$scale1*5); $msg_tm.="<TD valign=bottom align=center><small>$tm2</small><BR><div style='height:$hey;BACKGROUND-COLOR:red;'></dv></TD>";} else 
$msg_tm.="<TD valign=bottom align=center>0</dv></TD>";}
if ($t==25) $msg_tm.="<TR align=center>";
if ($t>25) {$ta=$t-26; $msg_tm.="<TD>$ta</TD>";}
$t++;
} while ($t<50);
$msg_tm.="</TR><TR><TD colspan=24 align=center>ВРЕМЯ суток, распределение посещений по часам</TD></TR></table></div>";




// Работаем со страницами
$msg.="<h3 align=center>Статистика по просматриваем страницам (по хитам)</h3>";
do { $massiv=each($newlines2);
$m2=round($massiv[1]*$scale1*5);
$delta=100*$massiv[1]/$maxi;
if ($delta>2) $addstyle="bgcolor=#E6FFE6"; else $addstyle="";
if ($delta>5) $addstyle="bgcolor=#CAFFCA";
if ($delta>10) $addstyle="bgcolor=#64FF64";
$procent=round($delta,1); $procent="($procent %)";
if (strlen($massiv[0])<1) $massiv[0]="СТРАНИЦА НЕ РАСПОЗНАНА";

$msg.="<TR $addstyle><TD align=center>$i</TD><TD>";
$hrtext=$massiv[0]; if (strlen($massiv[0])>70) { $hrtext=substr($hrtext,0,70); $hrtext.="...";}
if ($massiv[1]>1 and $massiv[0]!="СТРАНИЦА НЕ РАСПОЗНАНА") $msg.="<a href='$massiv[0]'>$hrtext</a>"; else $msg.="$hrtext";
$msg.="</td><td><img src='images/g1.gif' width=$m2 height=16 border=0> <B>$massiv[1]</B> $procent</TD></TR>"; $i++; } while ($i<=count($newlines2));


// Работаем с ОС
$msg_os="<h3 align=center>Статистика по операционным системам (по хитам)</h3><TABLE border=0>
<TR align=center><TD>№ п/п</TD><TD>ОС</TD><TD>Просмотров</TD></TR>";
$i=0; do { $massiv=each($lines_os2);
$num=$i+1;
$m2=round($massiv[1]*$scale1*2);
$delta=100*$massiv[1]/$maxi;
$addstyle="bgcolor=#CAFFCA";
$procent=round($delta,0); $procent="($procent %)";
if (strlen($massiv[0])<1) $massiv[0]="ОС НЕ РАСПОЗНАНА";
$msg_os.="<TR $addstyle><TD align=center>$num</TD><TD>";
$hrtext=$massiv[0]; if (strlen($massiv[0])>70) { $hrtext=substr($hrtext,0,70); $hrtext.="...";}
if ($massiv[1]>1 and $massiv[0]!="СТРАНИЦА НЕ РАСПОЗНАНА") $msg_os.="$hrtext"; else $msg_os.="$hrtext";
$msg_os.="</td><td><img src='images/g1.gif' width=$m2 height=16 border=0> <B>$massiv[1]</B> $procent</TD></TR>"; $i++; } while ($i<count($lines_os2));
$msg_os.="</TABLE><br>";

//print_r($lines_os2);



// Работаем с браузерами
$msg_br="<h3 align=center>Статистика по браузерам (по хитам)</h3><TABLE border=0><TR align=center><TD>№ п/п</TD><TD>Браузер</TD><TD>Просмотров</TD></TR>"; 
$msg_br_c=""; $msg_br_s=""; $msg_br_f=""; $msg_br_o=""; $itogo=nul;
$i=0; do { $massiv=each($lines_br2);
$num=$i+1; $itogo=$itogo+$massiv[1];
$m2=round($massiv[1]*$scale1*2);
$delta=100*$massiv[1]/$maxi; $addstyle="";
if(stristr($massiv[0],"chrome")) {$addstyle="bgcolor=#C1FFAA";  $msg_br_c=$msg_br_c+$massiv[1];}
if(stristr($massiv[0],"firefox")) {$addstyle="bgcolor=#FFB366"; $msg_br_f=$msg_br_f+$massiv[1];}
if(stristr($massiv[0],"opera")) {$addstyle="bgcolor=#FFAAAA";  $msg_br_o=$msg_br_o+$massiv[1];}
if(stristr($massiv[0],"safari")) {$addstyle="bgcolor=#FDFFAA"; $msg_br_s=$msg_br_s+$massiv[1];}
//Safari 

$procent=round($delta,0); $procent="($procent %)";
if (strlen($massiv[0])<1) $massiv[0]="ОС НЕ РАСПОЗНАНА";
$msg_br.="<TR $addstyle><TD align=center>$num</TD><TD>";
$hrtext=$massiv[0]; if (strlen($massiv[0])>70) { $hrtext=substr($hrtext,0,70); $hrtext.="...";}
if ($massiv[1]>1 and $massiv[0]!="СТРАНИЦА НЕ РАСПОЗНАНА") $msg_br.="$hrtext"; else $msg_br.="$hrtext";
$msg_br.="</td><td><img src='images/g1.gif' width=$m2 height=16 border=0> <B>$massiv[1]</B> $procent</TD></TR>"; 
if ($massiv[1]=="3") $i=count($lines_br2);
$i++; } while ($i<count($lines_br2));

$msg_br.='<tr><td colspan=3 align=center><h3>ИТОГО (по самым популярным браузерам):</h3></td></tr>';

if ($msg_br_c>10) {
$mc=round($msg_br_c*$scale1); $delta=100*$msg_br_c/$maxi; $procent=round($delta,0); $procent="($procent %)";
$msg_br.="<TR><TD align=center>-</TD><TD>CHROME</td><td><img src='images/g1.gif' width=$mc height=16 border=0> <B>$msg_br_c</B> $procent</TD></TR>";}

if ($msg_br_f>10) {
$mc=round($msg_br_f*$scale1); $delta=100*$msg_br_f/$maxi; $procent=round($delta,0); $procent="($procent %)";
$msg_br.="<TR><TD align=center>-</TD><TD>FIREFOX</td><td><img src='images/g1.gif' width=$mc height=16 border=0> <B>$msg_br_f</B> $procent</TD></TR>";}

if ($msg_br_s>10) {
$mc=round($msg_br_s*$scale1); $delta=100*$msg_br_s/$maxi; $procent=round($delta,0); $procent="($procent %)";
$msg_br.="<TR><TD align=center>-</TD><TD>SAFARI</td><td><img src='images/g1.gif' width=$mc height=16 border=0> <B>$msg_br_s</B> $procent</TD></TR>";}

if ($msg_br_o>10) {
$mc=round($msg_br_o*$scale1); $delta=100*$msg_br_o/$maxi; $procent=round($delta,0); $procent="($procent %)";
$msg_br.="<TR><TD align=center>-</TD><TD>OPERA</td><td><img src='images/g1.gif' width=$mc height=16 border=0> <B>$msg_br_o</B> $procent</TD></TR>";}

$msg_br.='</TABLE><br>'; //print_r($lines_br2);


$dayn=date("w",$day); // Считаваем день недели по порядку
$lastday1=date("d.m.Y",$day-86400); $lastday2=$day-86400;
$nextday1=date("d.m.Y",$day+86400);$nextday2=$day+86400;

print"$shapka<center><a href='https://$host'>Главная сайта</a> :: <a href='info.php'>Страница статистики</a> :: <a href='info.php?month'>Статистика за период</a><BR><BR>
<table align=center width=50% border=0><TR><TD align=center bgcolor=#F2F2F2>";

// Если нет данных за выбранную дату, то выдаём сообщение с ошибкой!
if (!is_file("$coundir/$lastday1.csv")) print"<a href='#' onclick=\"alert('Нет данных за дату - $lastday1 г.'); return false;\">«««<br> Предыдущая дата:<br>$lastday1 г.</a>";
else print"<a href='info.php?day=$lastday2'>«<br> Предыдущая дата:<br>$lastday1 г.</a>";

print"</TD>
<TD><center>Дата: <B>$file</B> г.<br><B>";
if ($dayn!=0 and $dayn!=6) print"$weeks[$dayn]"; else print "<font color=red>$weeks[$dayn]</font>";
print"</B><br>
Итого хиты / хосты: <font size=+2><B>$hitov</B></font> / <font size=+2><B>$numip</B></font><br>
Уникальных страниц: <font size=+2><B>$numsys</B></font></TD>
<TD align=center bgcolor=#F2F2F2>";

// Если нет данных за выбранную дату, то выдаём сообщение с ошибкой!
if (!is_file("$coundir/$nextday1.csv")) print"<a href='#' onclick=\"alert('Нет данных за дату - $nextday1 г.'); return false;\">Следующая дата:<br>$nextday1 г.<br> »</a>";
else print"<a href='info.php?day=$nextday2'>Следующая дата:<br>$nextday1 г.<br> »</a>";

print"</TD></TR></TABLE><br>";



// Выводим все даты, когда были посещения
echo'<table><TR><TD>Все даты: ';
for ($m=0; $m<$maxm; $m++)  { // начало FOR
	$dtt=explode(";",$recordsm[$m]);
	if (!isset($m0)) {$m0=date("m",$dtt[0])-1;}
	if ($m==$maxm-1) {$m1=date("m",$dtt[0])-1;}
	$xday=date("d",$dtt[0]);
	$dayn=date("w",$dtt[0]); // Считаваем день недели по порядку
	if ($dayn=="0" or $dayn=="6") $weekstyle="bgcolor=#FF7979"; else $weekstyle="";
	print"<td width=30 $weekstyle align=center><font size=+1>";
	if ($day==$dtt[0]) print"<B>$xday</B>"; else print"<a href='info.php?day=$dtt[0]'>$xday</a>";
	print"</font></TD>\r\n"; }  // конец FOR
echo'</TR></TABLE>';


print"<br><br>

<div><form action='info.php?day=$day' method=POST name=finder>
Фильтр по адресу страницы: <input name='findme' value='$findme' class=post type='text' maxlength=50 size=20>
<input type=submit value='выбрать'>
</form></div>";



print"<table border=0 cellSpacing=0 cellPadding=0 align=center>
<TR align=center><TD>№ п/п</TD><TD>Страница</TD><TD>Просмотров *</TD></TR>
$msg
</table>
";

// Выводим статистику по ОПЕРАЦИОННЫМ СИСТЕМАМ и браузерам
print "$msg_tm $msg_os $msg_br </center>";


} else  { header("HTTP/1.1 404 Moved Permanently"); header("Location: info.php"); exit(); } // if is_file
}

echo'<br><small>* При использовании поиска количество хостов, уникальных страниц, а также процент считается от<br>
того количества страниц, которое попало под фильтр, а не от общего количества страниц!</small>';

} else { // if isset($day). ИНАЧЕ показываем общую страницу статистики



print"$shapka<center><B>Информация по посещаемости</B> *<TABLE cellSpacing=0 cellPadding=0 width=\"98%\"><TR><TD valign=top>";



if (sizeof($records)>=1) { // ЕСЛИ есть данные - то выводим графики

if ($gtype=="1") {  $itogohit=0; $itogohost=0; // ГОРИЗОНТАЛЬНЫЙ график

$xdaym="</TR><TR><TD align=center><small>Дата</TD><TD>&nbsp;</TD>\r\n "; $graph1=""; $graph2=""; // Формируем данные для графиков
$g1shapka="<table border=0 cellSpacing=0 cellPadding=0 align=center><tr align=center valign=bottom><TD valign=middle><small>К<BR>О<BR>Л<BR>-<BR>В<BR>О<BR><B><BR>Х<BR>И<BR>Т<BR>О<BR>В</TD><TD><img src='images/v1scale.gif' border=0></TD>\r\n";
$g2shapka="<table border=0 cellSpacing=0 cellPadding=0 align=center><tr align=center valign=bottom><TD valign=middle><small>К<BR>О<BR>Л<BR>-<BR>В<BR>О<BR><B><BR>Х<BR>О<BR>С<BR>Т<BR>О<BR>В</TD><TD><img src='images/v2scale.gif' border=0></TD>\r\n";



for ($i=0; $i<$maxi; $i++)  { // начало FOR
$dtt=explode(";",$records[$i]);

$dttn1=round($dtt[1]*$scale1);  // шкала 1-го графика
$dttn2=round($dtt[2]*$scale2); // шкала 2-го графика
$itogohit=$itogohit+$dtt[1];
$itogohost=$itogohost+$dtt[2];

$graph1.="<TD><table cellSpacing=0 cellPadding=0><TR><TD width=24 align=center><small>$dtt[1]</small></TD></TR><TR><TD align=center><img src='images/v1.gif' height=$dttn1 width=20></TD></TR></TABLE></td>\r\n";
$graph2.="<TD><table cellSpacing=0 cellPadding=0><TR><TD width=24 align=center><small>$dtt[2]</small></TD></TR><TR><TD align=center><img src='images/v2.gif' height=$dttn2 width=20></TD></TR></TABLE></td>\r\n";

if (!isset($m0)) {$m0=date("m",$dtt[0])-1;}
if ($i==$maxi-1) {$m1=date("m",$dtt[0])-1;}
$xday=date("d",$dtt[0]);
$dayn=date("w",$dtt[0]); // Считаваем день недели по порядку
if ($dayn=="0" or $dayn=="6") $weekstyle="bgcolor=#FF7979"; else $weekstyle="";
$xdaym.="<td $weekstyle align=center><small><a href='info.php?day=$dtt[0]'>$xday</a></small></TD>\r\n"; 
}  // конец FOR

$xdaym.="<td width=100 align=center>&nbsp;</TD><td align=center><small><a href='info.php?month'>ИТОГО</a></small></TD>";
$xdaym.="<td align=center><small><a href='info.php?month'>Среднее</a></small></TD>";

$graph1.="<td align=center>&nbsp;</TD><TD><table cellSpacing=0 cellPadding=0><TR><TD width=24 align=center><small>$itogohit</small></TD></TR><TR><TD align=center><img src='images/v1.gif' height=230 width=20></TD></TR></TABLE></td>\r\n";
$srednee=ceil($itogohit/$maxi); $srednee1=ceil($srednee*$scale1);
$graph1.="<TD><table cellSpacing=0 cellPadding=0><TR><TD width=70 align=center><small>$srednee</small></TD></TR><TR><TD align=center><img src='images/v1.gif' height='$srednee1' width=20></TD></TR></TABLE></td>\r\n";
$graph2.="<td align=center>&nbsp;</TD><TD><table cellSpacing=0 cellPadding=0><TR><TD width=24 align=center><small>$itogohost</small></TD></TR><TR><TD align=center><img src='images/v2.gif' height=230 width=20></TD></TR></TABLE></td>\r\n";
$srednee=ceil($itogohost/$maxi); $srednee2=ceil($srednee*$scale2);
$graph2.="<TD><table cellSpacing=0 cellPadding=0><TR><TD width=70 align=center><small>$srednee</small></TD></TR><TR><TD align=center><img src='images/v2.gif' height='$srednee2' width=20></TD></TR></TABLE></td>\r\n";

$mm1=$months[$m0]; $mm2=$months[$m1]; if ($mm1==$mm2) {$mm1="";} else {$mm1.=" - ";}
$msdat="</TR></TABLE><center>Период: &nbsp; <B>$mm1 $mm2</B> ($days дн.)</center>\r\n";

print"$g1shapka $graph1 $xdaym </TR></TABLE>"; // печатаем 1-ый ГРАФИК
print"$g2shapka $graph2 $xdaym $msdat";   // печатаем 2-ой ГРАФИК


}  else   {   // ВЕРТИКАЛЬНЫЙ график


$g1shapka="<table cellSpacing=0 cellPadding=0 align=center><tr align=center valign=bottom><TD valign=middle><small>Дата</TD><TD>КОЛ-ВО <B>ХИТОВ</TD></TR><TR><TD>&nbsp;</TD><TD><img src='images/g1scale.gif' border=0></TD>\r\n";
$g2shapka="<table cellSpacing=0 cellPadding=0 align=center><tr align=center valign=bottom><TD valign=middle><small>Дата</TD><TD>КОЛ-ВО <B>ХОСТОВ</TD></TR><TR><TD>&nbsp;</TD><TD><img src='images/g2scale.gif' border=0></TD>\r\n";
$xdaym=""; $graph1=""; $graph2=""; // Формируем данные для графиков

for ($i=0; $i<$maxi; $i++)  { // начало FOR
$dtt=explode(";",$records[$i]);

//$dtt[0]=date("d.m.y",$dtt[0]); 
if (!isset($m0)) {$m0=date("m",$dtt[0])-1;}
if ($i==$maxi-1) {$m1=date("m",$dtt[0])-1;}
$xday=date("d",$dtt[0]);

$dttn1=round($dtt[1]*$scale1);  // шкала 1-го графика
$dttn2=round($dtt[2]*$scale2); // шкала 2-го графика

$graph1.="<tr><TD align=center><small>$xday</small></TD><td><table cellSpacing=0 cellPadding=0><TR><TD><img src='images/g1.gif' height=16 width=$dttn1></TD><TD align=center>&nbsp; <small>$dtt[1]</small></TD></TR></TABLE></td></tr>";
$graph2.="<tr><TD align=center><small>$xday</small></TD><td><table cellSpacing=0 cellPadding=0><TR><TD><img src='images/g2.gif' height=16 width=$dttn2></TD><TD align=center>&nbsp; <small>$dtt[2]</small></TD></TR></TABLE></td></tr>";

}  // конец FOR


$mm1=$months[$m0]; $mm2=$months[$m1]; if ($mm1==$mm2) {$mm1="";} else {$mm1.=" - ";}
$msdat="</tr><TR><TD colspan=2><center><small>Период:</small> <B>$mm1 $mm2</B></center>\r\n";

print"<BR>$g1shapka $graph1 $xdaym </TR></TABLE></TD><TD width='50%' valign=top> <!-- Делим экран пополам -->"; // печатаем 1-ый ГРАФИК
print"<BR>$g2shapka $graph2 $xdaym </TR></TABLE></TD>$msdat<BR>"; // печатаем 2-ой ГРАФИК


} // else ($gtype)

print"</center><small>P.S. <B>Хиты</B> - общее количество просмотров страниц где установлен счётчик;<BR>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <B>Хосты</B> - количество посетителей с уникальным IP-адресом.<BR>
Статистика обновляется каждые 5 минут. Следующее обновление в ". date("H:i:s",$ftime)."</small>";

} else {print"<center><br><br>Данные для графика отсутствуют.<br> Посещений за последний период не было.<br><br>";}

} // else от if isset($day)

?>
<center><small>Powered by <a href="https://www.wr-script.ru/" target="_blank">WR-Counter</a> &copy; 1.5 UTF-8<br></small></center>
</TD></TR></table>
</body></html>