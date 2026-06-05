<? // WR-forum v 2.3  //  07.01.2023 г.  //  WR-Script.ru
   // Модуль персональных сообщений (сообщений хранящихся внутри форума)

error_reporting(0); //error_reporting (E_ALL); // E_ALL включить на время тестирования и отладки скрипта
   
include "data/config.php";

$maxpmmsg="10000"; // Максимальное количество символов в персональном сообщении
$datapmdir="data-pm"; // Папка с данными перональных сообщений
$date=date("d.mmmmm.y"); $time=date("H:i");

$shapka='<html><head>
<title>Отправка / Просмотр ПС: '.$forum_name.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" href="'.$forum_skin.'/style.css" type="text/css">
<SCRIPT language=JavaScript>
<!--
function x () {return;}
function FocusText(){document.REPLIER.msg.focus();document.REPLIER.msg.select();return true;}
function DoSmilie(addSmilie){var revisedMessage; var currentMessage=document.REPLIER.msg.value; revisedMessage=currentMessage+addSmilie; document.REPLIER.msg.value=revisedMessage; document.REPLIER.msg.focus(); return;}
function DoPrompt(action){var revisedMessage; var currentMessage=document.REPLIER.msg.value;}
//-->
</SCRIPT>
</head>
<body bgcolor="#E5E5E5" text="#000000" link="#006699" vlink="#5493B4">
<center><BR><BR><BR>';

$name=""; $flag=FALSE;// Очищаем переменные

if (isset($_COOKIE['wrfcookies'])) { // ищем В КУКАХ wrfcookies чтобы вывести ИМЯ
$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc,ENT_COMPAT,"UTF-8"); $wrfc=stripslashes($wrfc); 
$wrfc=explode("|", $wrfc); $wrfname=$wrfc[0]; $wrfpass=$wrfc[1]; $name=urldecode($wrfname);$name=strtolower($name);

// ФАЗА 2: Сверяем логин и пароль в базе с тем, что у нас хранится в КУКАХ
$lines=file("$datadir/user.php"); $maxi=count($lines); $i="1";
do {$dt=explode("|",$lines[$i]); $i++;
$dt[2]=strtolower($dt[2]); $pass=$wrfpass;
if ($dt[2]===$name and $dt[3]===$pass) {$flag=TRUE; $i=$maxi;} // ЕСЛИ нашли юзера, значит последний раз цикл работает
} while($i < $maxi);
} else echo"$shapka <br><br><br>Система внутренних сообщений работает только для ЗАРЕГИСТРИРОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ!<br> "; // ЕСЛИ есть КУКИ

if ($flag===TRUE) { // ПОЛЬЗОВАТЕЛЬ ПРОШЁЛ КОНТРОЛЬ ДОСТОВЕРНОСТИ УЧЁТНОЙ ЗАПИСИ

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

if (isset($_GET['id']) or isset($_POST['id'])) { // Если есть ИМЯ ПОЛЬЗОВАТЕЛЯ
if (isset($_GET['id'])) $id=replacer($_GET['id']);
if (isset($_POST['id'])) $id=replacer($_POST['id']);
$id=urldecode($id);
$id=strtolower($id); // переводим все ники в нижний регистр
if (is_file("$datapmdir/$id.csv")) { $linesn=file("$datapmdir/$id.csv"); $in=count($linesn); if ($in > 500) exit("$back <B>Максимальное количество сообщение достигнуто/B>! Удалите свои сообщения или попросите пользователя, которому Вы отправляете сообщение почистить свой ящик!</center>"); }


if(isset($_GET['deletemsg'])) { // Блок УДАЛЕНИЯ выбранного СООБЩЕНИЯ
$num=replacer($_GET['deletemsg']); if ($num=="" or mb_strlen($num)<5) exit("$shapka Ошибка, выбирите сообщение для удаления, либо ошибка скрипта!");
if (is_file("$datapmdir/$id.csv")) {
$file=file("$datapmdir/$id.csv");
$fp=fopen("$datapmdir/$id.csv","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) {$dt=explode("|",$file[$i]); if ($dt[1]==$num) unset($file[$i]);}
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp); } // if is_file
Header("Location: pm.php?readpm&id=$id"); exit; }



if (isset($_GET['alldelete'])) { // ОЧИСТКА ЯЩИКА
if ($id==$name & is_file("$datapmdir/$id.csv")) { unlink ("$datapmdir/$id.csv"); print"$shapka <p align=center><b>Личные сообщения удалены!<br>Вы можете перейти на главную страницу форума <a href='index.php'>по этой ссылке</a></b></p>";}
else exit("$shapka $back У ВАС отсутствуют сообщения! Ваша папка ПУСТА."); }



if (isset($_GET['sendpm'])) { // ОТПРАВКА СООБЩЕНИЯ
print"$shapka <center><br><br><table width=33%><tr><td align=center><B>Сообщение отправлено!<br></B><br> Вы можете закрыть это окно!
Либо перейти в папку с Вашими входящими письмами по этой ссылке:<br><a href='pm.php?readpm&id=$name'>pm.php?readpm&id=$name</a>.
<br>Либо перейти на главную страницу форума <a href='index.php'>по этой ссылке</a>
</td></tr></table>"; exit; }



if (isset($_GET['readpm'])) { // ПРОСМОТР СООБЩЕНИЙ

touch("$datapmdir/$id.csv"); // если нет файла с ПС, то создаём файл юзеру
if (is_file("$datapmdir/$id.csv") & $id===strtolower($name)) {

$rlines=file("$datapmdir/$id.csv"); $ri=count($rlines); $key="0";
if ($ri>0) { // Если файл не пуст


$dir="Входящие"; $type=0;

print"$shapka";
echo'<br><center><font style=font-size:14px;font-family:tahoma>Личные сообщения <b>'.$id.'</b><br><b>['.$dir.': '.$ri.' шт.]</b></font></center>';

print"
<style>
.avatar-comment-ostani {
    margin: 20px;
    padding: 20px;
    border: 1px solid #009;
    border-radius: 10px;
    -moz-border-radius: 10px;
    -webkit-border-radius: 10px;
    overflow: hidden;
    z-index: 2;
    background: #DDD;
}</style>";


echo'<br><table border=0 class=forumline width=100% cellspacing=1 cellpadding=0><tr><th class=thLeft width=170 height=22 nowrap=nowrap>Отправитель</th><th class=thRight nowrap=nowrap><p align=center>Сообщение</p></th></tr>';

//print"<tr><td align=center width=150><b></b></td><td align=center><b></b></td><td><B>.X.</B></td></tr>";

do {$ri--; $edt=explode("|",$rlines[$ri]);
$number=$ri+1;
$data=date("d.m.Y г. H:i",$edt[1]);
$edt[7]=replacer($edt[7]); //if (mb_strlen($edt[7])>1) $edt[7]="";
$edt[5]=replacer($edt[5]);
$edt[8]=replacer($edt[8]); $edt[8]=str_replace("&lt;br&gt;",'<BR>',$edt[8]);

if ($key==0) {$cvet="#E2F1FC"; $key=1;} else {$cvet="#F1F9FE"; $key=0;}

print"<tr height=150><td class=row2 valign=top><span class=name><br><center>
<div class=guestavatar>
<b><a href='tools.php?event=profile&pname=$edt[5]'><font color='#000000'>$edt[5]</a></font></b></div>

</span><br><br>
<font color=#777777 size=-1><b>$data</b></font><br>
";

if ($type==0) print"<br><a href=\"pm.php?id=$edt[5]\" style=\"text-decoration:;font-weight:bold;font-size:13px;\">ответить</a> &nbsp;&nbsp;&nbsp;&nbsp; ";

print"<a href='pm.php?id=$id&deletemsg=$edt[1]' title='УДАЛИТЬ' onclick=\"return confirm('Сообщение будет удалено. Удалить? Уверены?')\" style=\"text-decoration:;font-weight:bold;font-size:13px;\">удалить</a><BR><BR>
</td>
<td class=row2 width=100% height=100 valign=top><table width=100% height=100%><tr valign=center><td height=120>";


print"<div class=\"avatar-comment-ostani\">
<B>Сообщение № $number</b><br><br>
тема: <font color=navy><U>$edt[7]</U></font><BR><BR>
<br><em>$edt[8]<BR><BR></em><br></div>

</td></tr></table>";

/*<p align=justify><font size=+1>тема: <font color=navy><U>$edt[7]</U></font><br><br><br>
<font face=verdana size=2>$edt[8]</font></P></span>";
*/

// БЛОК быстрого ОТВЕТА

print"



<FORM action='pm.php?savepm&id=$edt[5]' method=post name=REPLIER><center>
<details><summary>Быстрый ответ пользователю</summary>
<table cellpadding=1 cellspacing=0 width=800 class=forumline><br><br>
<tr><th class=thHead colspan=2 height=20><b>Быстрый ответ пользователю $edt[5]*</b></th></tr>
<tr><td class=row2 width=90%>Кому: </center><b>$edt[5]</b></span></td></tr>
<td class=row2 valign=top><span class=gen><table width=580 border=0>
<tr><td>Тема: <input type=text name=tema value='RE $edt[7]' style='width:600px'></td></tr>
<tr><td><span class=gen>Сообщение:<br><textarea name=msg cols=92 rows=11 class=post style='width:680px'></textarea></span></td></tr></table></span></td></tr>
</form>
<tr bgcolor=#F1F9FE><td colspan=2 align=center>

<input type=submit tabindex=5 class=mainoption style='width:192px' value='Отправить сообщение'>&nbsp; <br>
</td></tr></table></details><BR>

";


} while($ri>0);
echo"</TD></tr></table><br><B><a href='pm.php?alldelete&id=$name' onclick=\"return confirm('Будут удалены ВСЕ СООБЩЕНИЯ! Удалить? Уверены?')\">Удалить все сообщения</a></B>";
} else {













// 01-2019
print"$shapka";
echo'<br><center><font style=font-size:14px;font-family:tahoma>Личные сообщения <b>'.$id.'</b><br></font>
<br><table class=forumline width=80% cellspacing=1 cellpadding=0><tr><th class=thLeft width=170 height=22></th><th class=thRight>&nbsp;<p align=center></p></th></tr>';
print"<tr height=150><td class=row2 valign=top colspan=2><span class=name><br><center>
<center><h2>Ваша папка ПУСТА<br></h2>";


if (isset($_GET['user'])) $useremail=$_GET['user']; else $useremail="";
if (isset($_GET['autoscribe'])) $autoscribe=$_GET['autoscribe']; else $autoscribe="";
if (($autoscribe=="1") and ($useremail=="")) exit("<br><br><h2>Вернитесь назад! Вам необходимо отредактировать текст, выбрать 1-го пользователи и нажать кнопку 'Сохранить и отправить рассылку'!");
print"<center><TABLE class=forumline cellPadding=2 cellSpacing=1 width=775>
<br><br><FORM action='pm.php?savepm' method=post>
<TBODY><TR><TD class=thTop align=middle colSpan=2>Быстрое сообщение.</TD></TR>
<TR bgColor=#ffffff><TD>&nbsp; Имя отправителя: <B>$id</B> &nbsp; Получатель: ";
echo'
<SELECT name=id class=maxiinput><option value="">Выберите участника</option>\r\n';

// Блок считывает всех пользователей из файла
if (is_file("$datadir/user.php")) $lines=file("$datadir/user.php");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines)-1;
if ($datasize<=0) exit("$back. Проблемы с Базой пользователей, файл данных пуст.");
$imax=count($lines); $i="1";
do {$dt=explode("|", $lines[$i]);
print "<OPTION value=\"$dt[2]\">$dt[2] [$i]</OPTION>\r\n";
$i++; } while($i < $imax);

echo'</optgroup></SELECT></TD></TR>
<tr><td>Тема: <input type=text name=tema value="Новое сообщение" style="width:600px"></td></tr>
<TR bgColor=#ffffff><TD>&nbsp; Сообщение:<br>
<TEXTAREA name=msg style="FONT-SIZE: 14px; HEIGHT: 200px; WIDTH: 800px"></TEXTAREA></TD></TR>
<TR><TD bgColor=#FFFFFF colspan=2><center><INPUT type=submit value="ОТПРАВИТЬ"></form></TD></TR></TBODY></TABLE><br><br></center> * Блок в процессе написания';
print"
<h3>Вы можете закрыть это окно! Либо перейти на главную страницу форума по ссылке: <a href='index.php'>по этой ссылке</a>.</h3></center></td></TR></TABLE>";
echo'';} // if ($ri>0)
} } // isset($readpm)








if(isset($_GET['savepm'])) { // СОХРАНЕНИЕ сообщения

$msg=replacer($_POST['msg']); $msg=str_replace("|","I",$msg);
$tema=replacer($_POST['tema']); $tema=str_replace("|","I",$tema);
if ($msg=="" || mb_strlen($msg)>$maxpmmsg) exit("$shapka $back Ваше <B>сообщение пустое или превышает $maxpmmsg символов.</B></center>");
if (mb_strlen($tema)>$maxzag) exit("$shapka $back Тема Вашего <B>сообщения превышает $maxzag символов.</B></center>");

// Считываем всех пользователей, ищем того, которому адресовано сообщение
$i="0"; $from_rn=""; $tektime=time(); $to_rn=FALSE; $rn=mt_rand(10000,99999); // Колдуем случайный ключ
$lines=file("$datadir/userstat.csv"); $maxi=count($lines);
do {$dt=explode("|",$lines[$i]); $i++; $dt[2]=strtolower($dt[2]);

if ($dt[2]===$name) $from_rn="$dt[0]"; // Ищем RN-ключ юзера, отправившего сообщение
// Этот ключ НЕЛЬЗЯ передавать через форму! ОН нужен для безопасности скрипта!

if ($dt[2]===$id) $to_rn=$dt[0]; // ЕСЛИ нашли юзера, которому адресовано сообщение, то выставляем ФЛАГ
} while($i < $maxi);

if ($to_rn!=FALSE) { // Новая структура файла: rn|time|status|from_rn|to_rn|from_name|to_name|tema|msg|rezerved|
$text="$rn|$tektime|0|$from_rn|$to_rn|$name|$id|$tema|$msg||\r\n";
$fp=fopen("$datapmdir/$id.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,$text);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);}

exit("<script language='Javascript'>function reload() {location = \"pm.php?sendpm&id=$id\"}; setTimeout('reload()', 1000);</script>");
}




if ($name!=$id) { // Если нет никакого вывода и ЧТОБЫ самому себе письма не отправлять!
print"$shapka <FORM action='pm.php?savepm&id=$id' method=post name=REPLIER><center>
<table cellpadding=1 cellspacing=0 width=800 class=forumline align=center><br><br>
<tr><th class=thHead colspan=2 height=20><b>Отправка персонального сообщения для $id*</b></th></tr>
<tr><td class=row1 width=10% height=20><span class=genmed><center><b>Кому</b></center></span></td>
<td class=row2 width=90%><span class=genmed><b>$id</b></span></td></tr>

<tr><td class=row1 valign=top><span class=genmed><center><br><br><br>";

if ($showsmiles==TRUE) {$i=count($smiles)-1;
for($k=0; $k<$i; $k=$k+2) {$j=$k+1; print"<A href='javascript:%20x()' onclick=\"DoSmilie(' $smiles[$j]');\"><img src='smile/$smiles[$k].gif' border=0></a> ";} }

print"<br><br><a name='add' href='#add' onclick=\"window.open('tools.php?event=moresmiles','smiles','width=400,height=200,left=50,top=150,toolbar=0,status=0,border=0,scrollbars=1')\">Ещё смайлы</a></center></span></td>

<td class=row2 valign=top><span class=gen><table width=580 border=0>
<tr><td>Тема: <input type=text name=tema style='width:600px'></td></tr>
<tr><td><span class=gen>Сообщение:<br><textarea name=msg cols=92 rows=11 class=post style='width:680px'></textarea></span></td></tr></table></span></td></tr>

<tr bgcolor=#F1F9FE><td colspan=2 align=center>

<input type=submit tabindex=5 class=mainoption style='width:192px' value='Отправить сообщение'>&nbsp; <input type=reset tabindex=6 class=mainoption style='width:92px' value='Очистить'><br><br>
</td></tr></table>
<br><br>* Сообщение будет сохранено здесь. на форуме. Когда пользователь $id<br>
зайдёт на форум, то увидит новое сообщение в своём ящике.";}


} else echo'Отсутствует обязательный ключ id = ИМЯ_ПОЛЬЗОВАТЕЛЯ_КОМУ_БУДЕТ_ОТПРАВЛЕНО_СООБЩЕНИЕ'; //if isset($id)
} else exit("Идентификатор пользователя не закодирован или пользователь не найден в БД!");// if ($flag==TRUE)

?>
</body></html>