<? // WR-Forum v 2.3 win-1251 в UTF-8 конвертер // 07.01.23 г.


/// Ещё папку data-pm сконвертировать тоже в UTF и имя csv файлов сделать!

error_reporting (E_ALL); //error_reporting(0);

$datadir="data-old"; // Папка с данными форума версии 2.2 в кодировке win-1251
$newdatadir="data"; // Папка с данными в НОВОМ ФОРМАТЕ версии 2.3 в кодировке UTF-8

$datapmdir="data-pm"; // Папка с данными персональных сообщений версии 2.2 в кодировке win-1251



if (!isset($_GET['convert'])) {
print "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><meta http-equiv='Content-Language' content='ru'></head><body><center><h1>Конвертер WR-Forum версии 2.2 в версию 2.3 (win-1251 -> UTF-8)</h1>
<div style='width:600px; text-align:left;'><BR><BR><BR><B>ВНИМАНИЕ!</B> Данный скрипт обновит БД <B>форума версии 2.2 до версии 2.3 и выше (версия с 1 января 2023 года)</B><BR>
<B>Данный конвертер обработает все файлы данных, преобразует кодировку, изменит и добавит необходимые файлы в НОВУЮ СТРУКТУРУ БД!</B> в папку DATA!<BR>
<B>Рекомендую сделать резервную копию</B> в любом случае!<BR>
Предупреждаю, что данный конвертер пригоден <B>НЕ ДЛЯ ВСЕХ хостингов!</B> Если он не срабатывает у Вас на хостинге, попробуйте выполнить на другом или на своём локальном компютере, используя виртуальный сервер, например,
Open Server Panel!<BR><BR>

<h4>Подготовьтесь! Переименуйте папку data с данными форума версии 2.2 в <font color=red>$datadir</font><BR><BR>
Удалите все остальные папки и файлы (Сделайте копию обязательно)!<BR><BR>
Скопируйте из архива форума 2.3 все папки и файлы, особенно 'data' со всем содержимым на хостинг.<BR><BR>
</h4>

В случае, если после конвертирования форум выдаёт непонятные символы (кракозябры), попробуйте восстановить данные из копиии и 
повторить конвертирование данных, также можете попробуйте задать вопрос на <a href=\"http://www.wr-script.ru/forum/\">форуме техподдержки</a>. 
Вы можете прикрепить запароленный архив с файлом папки data на форуме, я сконвертирую вам и выложу сконвертированные данные.
<center><BR><BR>Для запуска скрипта перейдите по ссылке <h1><a href='go.php?convert'>Обновить БД</a></h1></div>"; exit; }

// Это самодельный парсер с win-1251 в UTF-8. Некоторые символы не обрабатывает.
$in_arr = array (
        chr(208), chr(192), chr(193), chr(194),
        chr(195), chr(196), chr(197), chr(168),
        chr(198), chr(199), chr(200), chr(201),
        chr(202), chr(203), chr(204), chr(205),
        chr(206), chr(207), chr(209), chr(210),
        chr(211), chr(212), chr(213), chr(214),
        chr(215), chr(216), chr(217), chr(218),
        chr(219), chr(220), chr(221), chr(222),
        chr(223), chr(224), chr(225), chr(226),
        chr(227), chr(228), chr(229), chr(184),
        chr(230), chr(231), chr(232), chr(233),
        chr(234), chr(235), chr(236), chr(237),
        chr(238), chr(239), chr(240), chr(241),
        chr(242), chr(243), chr(244), chr(245),
        chr(246), chr(247), chr(248), chr(249),
        chr(250), chr(251), chr(252), chr(253),
        chr(254), chr(255)
    );   
 
    $out_arr = array (
        chr(208).chr(160), chr(208).chr(144), chr(208).chr(145),
        chr(208).chr(146), chr(208).chr(147), chr(208).chr(148),
        chr(208).chr(149), chr(208).chr(129), chr(208).chr(150),
        chr(208).chr(151), chr(208).chr(152), chr(208).chr(153),
        chr(208).chr(154), chr(208).chr(155), chr(208).chr(156),
        chr(208).chr(157), chr(208).chr(158), chr(208).chr(159),
        chr(208).chr(161), chr(208).chr(162), chr(208).chr(163),
        chr(208).chr(164), chr(208).chr(165), chr(208).chr(166),
        chr(208).chr(167), chr(208).chr(168), chr(208).chr(169),
        chr(208).chr(170), chr(208).chr(171), chr(208).chr(172),
        chr(208).chr(173), chr(208).chr(174), chr(208).chr(175),
        chr(208).chr(176), chr(208).chr(177), chr(208).chr(178),
        chr(208).chr(179), chr(208).chr(180), chr(208).chr(181),
        chr(209).chr(145), chr(208).chr(182), chr(208).chr(183),
        chr(208).chr(184), chr(208).chr(185), chr(208).chr(186),
        chr(208).chr(187), chr(208).chr(188), chr(208).chr(189),
        chr(208).chr(190), chr(208).chr(191), chr(209).chr(128),
        chr(209).chr(129), chr(209).chr(130), chr(209).chr(131),
        chr(209).chr(132), chr(209).chr(133), chr(209).chr(134),
        chr(209).chr(135), chr(209).chr(136), chr(209).chr(137),
        chr(209).chr(138), chr(209).chr(139), chr(209).chr(140),
        chr(209).chr(141), chr(209).chr(142), chr(209).chr(143)
    );   
function cp1251_to_utf8 ($txt)  {GLOBAL $in_arr,$out_arr; $txt = str_replace($in_arr,$out_arr,$txt);return $txt;}
function utf8_to_cp1251 ($txt)  {GLOBAL $in_arr,$out_arr; $txt = str_replace($out_arr,$in_arr,$txt);return $txt;}

// ШАГ 1: Считываем содержимое директории $datadir с данными форума
$lines=null; unset($lines); if (!is_dir("$datadir/")) exit("папка $datadir не существует.");
$i=0; if ($handle=opendir("$datadir/")) {while (($file=readdir($handle)) !== false) if (!is_dir($file)) {$lines[$i]=$file; $i++;} closedir($handle);}
if (!isset($lines)) exit("В папке $datadir НЕТ данных! Ошибка считывания файлов из папки $datadir");
$itogo=count($lines); $k=0; $text=null;
print"<center><b>Считываем и обрабатывем файлы БД:</b><br>";
do {
$rdt=explode(".",$lines[$k]);

// Считываем содержимое файла в память
$records=file_get_contents("$datadir/$lines[$k]");

 // КОНВЕРТИРУЕМ ВСЁ СОДЕРЖИМОЕ ФАЙЛА ИЗ КОДИРОВКИ WIN-1251 В UTF-8
 
// Вариант 1 - основной
$records=iconv('windows-1251//IGNORE', 'UTF-8//IGNORE',$records);

// Вариант 2 - резервный
//$records=iconv('cp1251//IGNORE', 'utf-8//IGNORE',$records);

// Вариант 3 - резервный
//$records=cp1251_to_utf8($records);

// сохраняем данные в новый файл
$newfname=str_replace(".dat",'.csv',$lines[$k]);
$fp=fopen("$newdatadir/$newfname","w+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
fputs($fp,$records);
flock ($fp,LOCK_UN);
fclose($fp);
$records=null;

print"$lines[$k] <font color=green> считан</font>&nbsp;&nbsp;&nbsp; ";

$k++;
} while ($k<$itogo);

unlink("$newdatadir/best.csv"); // Удаляем, проблема прии конвертации

// if (!unlink("go.php")) $add="<h3>Внимание! Удалите файл go.php вручную.</h3><br><br>";
print"<h2>Обработано $itogo файлов!</h2><BR>";








// ШАГ 2: Считываем содержимое директории $datapmdir с данными персональых сообщений
$lines=null; unset($lines); if (!is_dir("$datapmdir/")) exit("папка $datapmdir не существует.");
$i=0; if ($handle=opendir("$datapmdir/")) {while (($file=readdir($handle)) !== false) if (!is_dir($file)) {$lines[$i]=$file; $i++;} closedir($handle);}
if (!isset($lines)) exit("В папке $datapmdir НЕТ данных! Ошибка считывания файлов из папки $datapmdir");
$itogo=count($lines); $k=0; $text=null;
print"<center><b>Считываем и обрабатывем файлы ПМ:</b><br>";
do {
$rdt=explode(".",$lines[$k]);

// Считываем содержимое файла в память
$records=file_get_contents("$datapmdir/$lines[$k]");

// КОНВЕРТИРУЕМ ВСЁ СОДЕРЖИМОЕ ФАЙЛА ИЗ КОДИРОВКИ WIN-1251 В UTF-8
// Вариант 1 - основной
if (strstr($lines[$k],'.dat')==TRUE) {
$records=iconv('windows-1251//IGNORE', 'UTF-8//IGNORE',$records);

// сохраняем данные в новый файл
$newfname=str_replace(".dat",'.csv',$lines[$k]);
$fp=fopen("$datapmdir/$newfname","w+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
fputs($fp,$records);
flock ($fp,LOCK_UN);
fclose($fp);
$records=null;

if ($lines[$k]!=".htaccess") {if ($lines[$k]!="pm.gif") unlink("$datapmdir/$lines[$k]");} // удаляем старый файл

print"$lines[$k] <font color=green> считан</font>&nbsp;&nbsp;&nbsp; ";}
$k++;
} while ($k<$itogo);




print"<BR><BR><center>Операция обновления форума до версии 2.3 UTF-8 <B>успешно выполнена!</B><BR>
<h2>Обработано $itogo файлов!</h2><BR>
<h3>В папку  <strong><font color=green>$newdatadir</font></strong> были экспортированы (сохранены) файлы с новым расширением и кодировкой.<br><br>

Чтобы начать работу просто перейдите в папку с форумом!<BR><BR>
* Страницу обновляйте не рекомендуется, удалите файл go.php!</h3>"; 
exit;
?>