<strong>Последние обсуждаемые темы форума</strong><p>

<? // СТАТИСТИКА: Последние сообщения с форума

$lasttemprint="10"; // Сколько вывести последних сообщений
$msg_onpage="10"; // НАСТРОЙКА ФОРУМА: кол-во сообщений на страницу
$forumdir="forum"; // Папка с форумом
$date=date("d.m.Y", time()+0); // число.месяц.год

if (is_file("$forumdir/data/news.dat")) { $newmessfile="$forumdir/data/news.dat";

// проходим по всем разделам и форумам и ищем запращиваемый. Если wrforum.dat пуст, то подключаем резервную копию.
$realbase=TRUE; if (is_file("$forumdir/data/wrforum.dat")) $mainlines=file("$forumdir/data/wrforum.dat");
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$forumdir/data/wrf-copy.dat")) {$realbase=FALSE; $mainlines=file("$forumdir/data/wrf-copy.dat"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных, файл данных пуст - обратитесь к администратору");

$lines=file($newmessfile); $i=count($lines);
if ($i>10 and $i<$lasttemprint) $i=$lasttemprint;
if ($i>1) {
$a1=$i-1;$u="-1"; // выводим данные по возрастанию или убыванию
do {$dt=explode("|", $lines[$a1]); $a1--;
if (isset($dt[1])) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим
$msg=htmlspecialchars($dt[14]);
$msg=str_replace('[b]'," ",$msg); $msg=str_replace('[/b]'," ",$msg);
$msg=str_replace('[RB]'," ",$msg); $msg=str_replace('[/RB]'," ",$msg);
$msg=str_replace('[Code]'," ",$msg); $msg=str_replace('[/Code]'," ",$msg);
$msg=str_replace('[Quote]',"Цитата:\r\n ",$msg); $msg=str_replace('[/Quote]',"конец цитаты\r\n ",$msg);
$msg=str_replace('[img]'," картинка: ",$msg); $msg=str_replace('[/img]'," ",$msg);
$msg=str_replace("<br>","\r\n", $msg);
$msg=str_replace("'","`",$msg);
$msg=str_replace('&amp;lt;br&amp;gt;'," \r\r\n", $msg);
$msg=str_replace('&lt;br&gt;'," \r\r\n", $msg);

$mmax=count($mainlines);
$k=$mmax; $mainr=""; // Ищем название рубрики, как находим - присваимваем значение и выходим из цикла!
do {$k--; $mdt=explode("|",$mainlines[$k]);
if ($mdt[2]==$dt[2]) {$mainr="<a href='$forumdir/index.php?id=$mdt[2]'>$mdt[4]</a>"; $k=0;}
} while($k>0);

if (date("d.m.Y",$dt[4])==$date)  $dt[4]="сегодня в ".date("H:i",$dt[4]); else $dt[4]=date("d.m.y - H:i",$dt[4]);

if ($dt[17]>$msg_onpage) $page=ceil($dt[17]/$msg_onpage); else $page=1;
if ($page!=1) $pageadd="&page=$page"; else $pageadd="";

if ($dt[6]==TRUE) {$codename=urlencode($dt[8]); $name="$dt[8]";} else $name="гость $dt[8]";
print"<div  style='line-height:1.5em;'>$dt[4]: <strong>$mainr</strong> » <B><a href='$forumdir/index.php?id=$dt[2]$dt[3]$pageadd#m$dt[17]' title='$msg \r\n\r\n Отправлено $dt[4]'>$dt[5]</a></B> - $name.<br></div>";
}
$a11=$u; $u11=$a1;
} while($a11 < $u11);
}}
?>
</p>