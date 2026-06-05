 <? // WR-Golos v 1.5  // 05.12.2018 г. // Miha-ingener@yandex.ru

error_reporting (E_ALL);

include "data/config.php";

$logo="fongolos-1";

$shapka="<html><head><title>Голосование</title>
<META HTTP-EQUIV='Pragma' CONTENT='no-cache'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'>
<META content='text/html; charset=UTF-8' http-equiv=Content-Type>
<link href='images/style.css' rel='stylesheet' type='text/css' />
</head><body>";

if (isset($_GET['id'])) {$id=$_GET['id']; if ((!ctype_digit($id)) or (strlen($id)>2)) exit("<B>Поддерживаются номера голосований от 1 до 99 включительно!!!</B>");} else $id=1;

$lines=file("$golosdir/vote$id.csv");
$itogo=count($lines); $i=1; $glmax=0;

// Считаем общее кол-во голосов
do {$dt=explode(";",$lines[$i]); $glmax=$glmax+$dt[1]; $i++; } while($i<$itogo); $i=1; $all=$glmax;

$vdt = explode(";",$lines[0]);
print"$shapka

<FORM name=wrvote action='submit.php' method=post>
<div id='container'>
<div class='bubble' style=\"BACKGROUND-IMAGE: url(images/$logo.jpg); background-size: cover; BACKGROUND-REPEAT: no-repeat\">
<div class='rectangle'><h2>Результаты</h2></div>
<div class='triangle-l'></div>
<div class='triangle-r'></div>
<div class='info'>
<h2>$vdt[0]</h2><BR>";

do {$dt=explode(";",$lines[$i]);
if ($glmax==0) {$glmax=0.1;}
$glpercent=round(10000*$dt[1]/$glmax)/100;
$hcg=round($glpercent);
if ($glpercent<1) {$hcg=1;} if ($glpercent>100) {$hcg=100;}
print"<B>&nbsp;$dt[0]</B> <B>&nbsp;$dt[1]</B> (<B>$glpercent</B> %)
<div class=zalivka style=\"width:".$hcg."px; height:11px;\">&nbsp;</div><BR></P>";
$i++;
} while($i<$itogo);


//<a href='rezult.php?id=$id' onClick='gorez();' target='WRRezultGolos'><button class='knopka knopka-red'>Результаты</button></a>";

print"
</p>
<BR><BR><B>Итого проголосовало:</B><h2 align=center>&nbsp;$all</h2><BR>
<a href='rezult.php' onClick='self.close()'>Закрыть окно</b></a></div></div></div>
</FORM>";
?>

<BR><center><small>Powered by <a href='https://www.wr-script.ru/'>WR-Golos</a> &copy; 1.5 UTF-8</small></body></html>