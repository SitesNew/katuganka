<? // WR-forum v 2.3  //  07.01.2023 г.  //  WR-Script.ru

//error_reporting(0); 
error_reporting (E_ALL); // E_ALL включить на время тестирования и отладки скрипта
@ini_set('register_globals','off');// Все скрипты написаны для этой настройки php

$pageur1="http://".$_SERVER['HTTP_HOST']; // Адрес ГЛАВНОЙ СТРАНИЦЫ САЙТА для хлебных крошек

include "data/config.php";

// Определяем URL форума С 2020 поддержка ТОЛЬКО http! Если нужно без S - исправьте!
$url1="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$url=str_replace('index.php','',"$url1"); $forum_url=preg_replace("/\?.+/","",$url); // удалить весь GET запрос: все символы после ?
if (isset($_GET['new-tema']) or isset($_GET['razdel'])) { header("HTTP/1.1 404 Moved Permanently"); header("Location: $forum_url");}
// Запрет поисковикам на индексацию и похождение по страницам без index.php, например www.wr/?id=101
if (isset($_GET['id'])) {if (strstr($url1,"/index.php")==FALSE) {header("HTTP/1.1 404 Moved Permanently"); header("Location: $forum_url");}}


$valid_types=array("zip","rar","7z","doc","docx","rtf","xls","xlsx","ppt","pptx","pdf","jpg","jpeg","bmp","gif","png"); // допустимые расширения загружаемых файлов

$navigate=FALSE; // TRUE; // Включить/выключить показа блока НАВИГАЦИЯ по темам. Лучше выключить для мобильных
	
function replacer ($text) { // ФУНКЦИЯ очистки кода
$text=str_replace("&#032;",' ',$text);
$text=str_replace(">",'&gt;',$text);
$text=str_replace("<",'&lt;',$text);
$text=str_replace("\"",'&quot;',$text);
$text=preg_replace("/\n\n/",' [br] ',$text);
$text=preg_replace("/\n/",' [br] ',$text);
$text=preg_replace("/\\\$/",'&#036;',$text);
$text=preg_replace("/\r/",'',$text);
$text=preg_replace("/\\\/",'&#092;',$text);
// если magic_quotes включена - чистим везде СЛЭШи в этих случаях: одиночные (') и двойные кавычки ("), обратный слеш (\)
//if (get_magic_quotes_gpc()) { $text=str_replace("&#092;&quot;",'&quot;',$text); $text=str_replace("&#092;'",'\'',$text); $text=str_replace("&#092;&#092;",'&#092;',$text); }
$text=str_replace("\r\n",' [br] ',$text);
$text=str_replace("\n\n",' [br][br] ',$text);
$text=str_replace("\n",' [br] ',$text);
$text=str_replace("\t",'',$text);
$text=str_replace("\r",'',$text);
$text=str_replace('   ',' ',$text);
return $text; }


// Функция содержит ПРОДОЛЖЕНИЕ ШАПКИ. Вызывается: addtop();
function addtop() { global $wrfname,$forum_skin,$date,$time;
// ищем КУКи и выводим ИМЯ
if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=replacer($wrfc); $wrfc=explode("|", $wrfc); $wrfname=$wrfc[0];} else {unset($wrfname); unset($wrfpass); $wrfpass="";}
echo'<TD align=right>';
if (isset($wrfname)) {print "
<a href=\"tools.php?event=who\" class=\"mainmenu\"><img src=\"$forum_skin/ico-members.gif\" border=0 hspace=3 />Участники</a> &nbsp;
<a href='tools.php?event=profile&pname=$wrfname' rel='nofollow' class=mainmenu><img src=\"$forum_skin/icon_mini_profile.gif\" border=0 hspace=3 />Ваш профиль</a>&nbsp; <a href='index.php?event=clearcooke' class=mainmenu><img src=\"$forum_skin/ico-login.gif\" border=0 hspace=3 />Выход [<B>$wrfname</B>]</a>";
$name=strtolower($wrfname); if (is_file("data-pm/$name.csv")) {$linespm=file("data-pm/$name.csv"); $pmi=count($linespm); 
if ($pmi>0) print"<img src=\"$forum_skin/icon_mini_profile.gif\" border=0 hspace=3 />[<a href='pm.php?readpm&id=$wrfname'><font color=red><B>Есть сообщения в ПМ: $pmi шт.</b></font></a>]";}
}
if (!isset($_COOKIE['wrfcookies']) and !isset($_GET['id'])) {print "<span class=mainmenu>
<a href='tools.php?event=reg' rel='nofollow' class=mainmenu><img src=\"$forum_skin/icon_mini_register.gif\" border=0 hspace=3 />Регистрация</a>&nbsp;&nbsp;
<a href='tools.php?event=login' rel='nofollow' class=mainmenu> <img src=\"$forum_skin/buttons_spacer.gif\" border=0 hspace=3>Вход</a></td>";}
if (is_file("$forum_skin/tiptop.html")) include("$forum_skin/tiptop.html"); // подключаем дополнение к ВЕРХУШКе
print"</span></td></tr></table></td></tr></table><span class=gensmall>Сегодня: $date - $time</span>";
return true;}


function nospam() { global $max_key,$rand_key,$antispam2k,$antispam2kv; // Функция АНТИСПАМ (2 в одном)
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
echo'<table cellspacing=0 cellpadding=0><tr height=30><TD>Защитный код:</TD>';
$nummax=0; for ($i=0; $i<=$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
$secret=mt_rand(0,1); $styles='bgcolor=#FFFFCE';
if ($nummax<3) { if ($secret==1 or $i==0) {$styles='bgcolor=#93C9FF'; $xkey=$xkey.$snum[$i]; $nummax++;}}
echo "<td width=20 $styles><img src='antispam.php?image=$psnum' border=0 alt=''>\n<img src='antispam.php?image=$psnum' height=1 width=1 border=0></td>\r\n";}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из data/config.php + код меняющийся кажые 24 часа
print"<td><input name='usernum' class=post type='text' maxlength=$nummax size=6> (введите цифры, которые на <font style='font-weight:bold'> синем фоне</font>)
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>
</td></tr></table>";
if ($antispam2k==TRUE) print"Ответ на вопрос: <input name='antispam2ko' class=post type='text' maxlength=20 size=10>($antispam2kv)";
return; }


function addmsg($qm) { // ФУНКЦИЯ добавления темы/сообщения
global $wrfname,$maxname,$can_up_file,$antispam,$max_key,$rand_key,$max_upfile_size,$showsmiles,$smiles,$valid_types,$datadir;

//Проверка ЗАПРЕТА IP-пользователя на добавление объявлений (файл ipblock.csv)
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
if (is_file("$datadir/ipblock.csv")) { $lines=file("$datadir/ipblock.csv"); $i=count($lines);
$tektime=time();
if ($i>0) {do {$i--; $idt=explode("|",$lines[$i]);

if ($idt[2]==$ip and $tektime<$idt[1]) { $date=date("d.m.Y");
$idt[0]=date("d.m.Y в H:i",$idt[0]);
$idt[1]=date("d.m.Y в H:i",$idt[1]);
exit("<center><br><br><B>Администратор $idt[0] заблокировал для Вашего IP: $ip<br> возможность добавлять что-либо по следующей 
причине:<br><br> <font color=red><B>$idt[4].</B></font><br><br>
До $idt[1] Вам разрешено просматривать сообщения,<br> а вот ДОБАВЛЯТЬ ТЕМЫ/СООБЩЕНИЯ категорически ЗАПРЕЩЕНО!</B><br><br>");}
} while($i>"1");} unset($lines);}

echo'<tr><td class=row1 width=10% height=25><span class=gen><b>Имя</b></span></td>
<td class=row2 width=80%><span class=genmed>';

//$wrfguest=null; if (isset($_COOKIE['wrfguest'])) $wrfguest=replacer($_COOKIE['wrfguest']); // 01-2019
if (!isset($wrfname)) print "<input type=text name=name class=post maxlength=$maxname size=28> E-mail <input type=text name=email class=post size=30>";
else {
if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc,ENT_COMPAT,"UTF-8"); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc); $wrfpass=replacer($wrfc[1]);} else {unset($wrfpass); $wrfpass="";}
print "<b>$wrfname</b><input type=hidden name=name value='$wrfname'><input type=hidden name=who value='1'><input type=hidden name=userpass value=\"$wrfpass\">";}

echo'</span></td></tr>
<tr><td class=row1 valign=top><span class=genmed><b>Сообщение</b><br><br>Нажмите на точку возле имени для обращения к участнику<br><br><center>Смайлы:<br>';

if ($showsmiles==TRUE) {$i=count($smiles)-1;
for($k=0; $k<$i; $k=$k+2) {$j=$k+1; 
if (is_file("smile/$smiles[$k].gif")) print"<A href='javascript:%20x()' onclick=\"DoSmilie(' $smiles[$j]');\"><img src='smile/$smiles[$k].gif' border=0></a> ";} }
print"<BR><a name='add' href='#add' onclick=\"window.open('tools.php?event=moresmiles&type=1','smiles','width=450,height=300,left=50,top=300,toolbar=0,status=0,border=0,scrollbars=1')\">Ещё смайлы</a><BR>

<a name='add' href='#add' onclick=\"window.open('tools.php?event=moresmiles&type=2','smile-emodzi','width=500,height=400,left=50,top=50,toolbar=0,status=0,border=0,scrollbars=1')\">Эмодзи</a>

</center></span></td>
<td class=row2 valign=top><span class=gen>
<input type=button class=button value='Ж' title='Выделить жирным' onclick=\"DoSmilie(' [b]  [/b] ');\">&nbsp;
<input type=button class=button value='КЖ' style='color:red' title='Выделить красным и жирным' onclick=\"DoSmilie('[RB] [/RB]');\">&nbsp;
<input type=button class=button value=' Код ' style='color:green' title='Вставка php,htm,js и др. кода' onclick=\"DoSmilie(' [Code]  [/Code] ');\">&nbsp;
<input type=button class=button value=' IMG ' title='Вставьте ссылку на картинку в Иинтернете' style='color:navy' onclick=\"DoSmilie('[img][/img]');\">&nbsp;
<input type=button class=button value=' Youtube' title='Видео с YouTube: ВВЕДИТЕ ТОЛЬКО \"хвостик\", например: BoUUbs3CvHs' onclick=\"DoSmilie(' [Youtube][/Youtube] ');\">&nbsp;
<INPUT type=button class=button value='Цитировать выделенное' title='Выделите часть сообщения, затем нажмите эту кнопку' onclick='REPLIER.msg.value += \"[Quote]\"+(window.getSelection?window.getSelection():document.selection.createRange().text)+\"[/Quote]\"'></span>
<textarea name=msg cols=95 rows=10 style='width:100%' class=post>$qm</textarea></span>";

if ($can_up_file==TRUE and isset($wrfname)) { $max=round($max_upfile_size/10485.76)/100;
print "Присоединить файл <input type=file name=file class=post size=70> Разрешены файлы: ";
foreach($valid_types as $v) print "$v, "; print" размер до <i>$max Mб.</i>";}

if ($antispam==TRUE and !isset($wrfname)) nospam(); // АНТИСПАМ !

echo'
<tr><td class=row1 colspan=2 align=center height=28>
<table width=100%><TR><TD align=center>
<input type=submit tabindex=5 class=mainoption value=" Отправить ">&nbsp;&nbsp;&nbsp;
<!--<input type=button class=mainoption value="Просмотр" onClick="seeTextArea(this.form)">--></td>
<td align=right style="width:94px"><input type=reset class=mainoption value="Очистить">
</td></tr></table></form>
</td></tr></table>';
return;} // КОНЕЦ функции-формы ДОБАВЛЕНИЯ ТЕМЫ/ОТВЕТА



// Выбран ВЫХОД из форума - очищаем куки
if(isset($_GET['event'])) {if ($_GET['event']=="clearcooke") {setcookie("wrfcookies","",time()); Header("Location: $forum_url"); exit;}}















// ДОБАВЛЕНИЕ ТЕМЫ или ОТВЕТА
if(isset($_GET['add_tema']) or isset($_GET['add_msg'])) {

if (isset($_GET['add_tema'])) {$add_tema=TRUE; $add_msg=FALSE;} // Если добавляем ТЕМУ
if (isset($_GET['add_msg'])) {$add_msg=TRUE; $add_tema=FALSE;} // Если добавляем СООБЩЕНИЕ

if ($forum_lock==TRUE) exit("Временно добавление тем и сообщений приостановлено!");

// ПОЛУЧАЕМ ДАННЫЕ ИЗ ФОРМЫ
if (isset($_POST['who'])) $who=$_POST['who']; else $who="";
if (isset($_POST['email'])) $email=$_POST['email']; else $email="";
if (isset($_POST['page'])) $page=$_POST['page']; else $page="";
if (isset($_POST['maxzd'])) $maxzd=$_POST['maxzd']; else $maxzd="0"; if ($maxzd==null) $maxzd="0";
if ((!ctype_digit($maxzd)) or (mb_strlen($maxzd)>2)) exit("$back. Попытка взлома по звёздам или ошибка в файле статистики");
if (isset($_POST['name'])) $name=$_POST['name']; else $name=""; $name=trim($name);
if (isset($_POST['msg'])) $msg=$_POST['msg']; else $msg="";
if (isset($_POST['zag'])) $zag=$_POST['zag']; else $zag="";
if (isset($_GET['id'])) {$fid=$_GET['id']; $id=$fid;} else {$fid=""; $id="";}
if (mb_strlen($fid)>3) $fid=mb_substr($fid,0,3); if (mb_strlen($id)==7) $id=mb_substr($id,3,4);
if (!ctype_digit($fid) or mb_strlen($fid)!=3 or !is_file("$datadir/$fid.csv")) exit("$back. Попытка взлома через номер рубрики. Рубрика отсутствует. Номер должен содержать только 3 цифры!");

//Проверка ЗАПРЕТА ПО IP на добавление тем/сообщений
$ip=$_SERVER['REMOTE_ADDR']; $tektime=time(); $pageadd="";
if (is_file("$datadir/ipblock.csv")) { $lines=file("$datadir/ipblock.csv"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|",$lines[$i]);
if ($idt[2]==$ip and $tektime<$idt[1]) exit("<noindex><script language='Javascript'>function reload() {location=\"$forum_url\"}; setTimeout('reload()', 10000);</script>
<center><br><br><B>Админитратор заблокировал для Вашего IP: $ip<br> возможность добавлять что-либо по следующей причине:<br><br> 
<font color=red><B>$idt[4].</B></font><br><br>Вам разрешено просматривать сообщения,<br> а вот ДОБАВЛЯТЬ ТЕМЫ/СООБЩЕНИЯ категорически ЗАПРЕЩЕНО!</B></noindex>");
} while($i>"1");} unset($lines);}

if ($nosssilki==TRUE and $add_msg==TRUE and preg_match("/href|http|http|www|\.ru|\.com|\.net|\.info|\.org/i",$msg) and preg_match("/\.jpg|\.gif|\.jpeg|\.png/is",$msg)<>TRUE and !isset($wrfname))
exit("<center>Уважаемый гость с IP $ip , если Вы хотите делиться ссылками с другими пользователями форума - пройдите регистрацию.<BR>Если Вы спаммер - выйдете вон! </center><BR><BR>");

// проходим по всем разделам и форумам и ищем запращиваемый. Если wrforum.csv пуст, то подключаем резервную копию.
$realbase=TRUE; if (is_file("$datadir/wrforum.csv")) $mainlines=file("$datadir/wrforum.csv");
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/wrf-copy.csv")) {$realbase=FALSE; $mainlines=file("$datadir/wrf-copy.csv"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных, файл данных пуст - обратитесь к администратору");

$realfid=FALSE; $fotodetali=""; $i=count($mainlines);
$lines_tema=file("$datadir/$fid.csv"); $itogotem=count($lines_tema); $j=$itogotem;
do {$i--; $dt=explode("|",$mainlines[$i]);
if ($dt[2]==$fid) { $realfid=$i+1; $i=0;
 if ($dt[3]==TRUE) exit("$back. Данной ветки форума не существует (есть только раздел с таким именем!)"); // присваиваем $realfid - № п/п строки
 if ($itogotem>=$dt[8] and $add_tema==TRUE) exit("$back. Превышено ограничение на кол-во допустимых тем в данной рубрике! Не более <B>$dt[8]</B> тем!");
}
} while($i>0);
if ($realfid==FALSE) exit("$back. Ошибка с номером рубрики. Рубрика отсутствует в базе!");
$realfid--;

// проверка Логина/Пароля юзера. Может он хакер, тогда облом ему
// ШАГ 1
if (isset($_POST['userpass'])) $userpass=replacer($_POST['userpass']); else $userpass=""; $realname="";
if (isset($_COOKIE['wrfcookies'])) {
    $wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc,ENT_COMPAT,"UTF-8"); $wrfc=stripslashes($wrfc);
    $wrfc=explode("|", $wrfc); $wrfname=$wrfc[0]; $wrfpass=$wrfc[1];
} else {$who="0"; $wrfname=null; $wrfpass=null; } //setcookie("wrfguest", $name, time()+1728000); 02-2019 уточнить что это и зачем!!!???

// ШАГ 2
$rn_user=FALSE; if ($who==TRUE) { $who=0;
if ($wrfname!=null & $wrfpass!=null) {
$lines=file("$datadir/user.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
   if (mb_strlen($rdt[3])>1) { $realname=strtolower($rdt[2]);
   if (strtolower($wrfname)===$realname & $wrfpass===$rdt[3] & $userpass===$rdt[3])
   {$rn_user=$rdt[0]; $name=$wrfname; $who="1";} }
} while($i > "1");
if ($rn_user==FALSE) {setcookie("wrfcookies","",time()); exit("Ошибка при работе с КУКИ! <font color=red><B>
Вы не сможете оставить сообщение, попробуйте подать его как гость.</B></font> Ваш логин и пароль не найдены 
в базе данных, попробуйте зайти на форум вновь. Если ошибка повторяется - обратитесь к администратору форума.");}
}}

if ($antispam==TRUE and !isset($wrfname)) { //--А-Н-Т-И-С-П-А-М--проверка кода--
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("данные из формы не поступили!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key"); $userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey) or mb_strlen($usernum)<1) exit("введён ОШИБОЧНЫЙ код!");}

// АНТИСПАМ 2!
if ($antispam2k==TRUE and !isset($wrfname)) { $ao=replacer($_POST['antispam2ko']);
if (strtolower($antispam2ko)!=strtolower($ao) or mb_strlen($ao)<1) exit("введён ошибочный ответ на вопрос!");}

if ($add_tema==TRUE and $g_add_tema==FALSE and !isset($wrfname)) exit("$back Администратор запретил гостям создавать темы!</center>");
if ($add_msg==TRUE and $g_add_msg==FALSE and !isset($wrfname)) exit("$back Администратор запретил гостям отвечать в темах!</center>");

$ok=FALSE;

if ($j>0) { do {$j--; $tdt=explode("|",$lines_tema[$j]); // Если есть темы в разделе

if ($add_msg==TRUE) { // Если добавляем СООБЩЕНИЕ
if ($tdt[3]==$id) { $ok=TRUE; if ($tdt[11]==FALSE) exit("$back тема закрыта и добавление сообщений запрещено!"); }
} // $add_msg==TRUE

if ($add_tema==TRUE) { // ЕСЛИ добавляем ТЕМУ
$ok=TRUE;
// функция АНТИФЛУД: повторное добавление темы запрещено!
if ($tdt[5]==$zag) exit("$back. Тема с заголовком \"$tdt[5]\" (<a href='index.php?id=$tdt[2]$tdt[3]'>ссылка на тему</a>) уже создана на форуме! Спамить на форуме запрещено!");
} // $add_tema==TRUE

} while($j>0);
} // if $j>0

// БЛОК ГЕНЕРИРУЕТ СЛЕДУЮЩИЙ ПО ПОРЯДКУ НОМЕР ТЕМЫ, начиная просмотр с 1000
if ($add_tema==TRUE) { $id=1000; $id="$fid$id";
$allid=null; $records=file("$datadir/$fid.csv"); $imax=count($records); $i=$imax;
if ($i > 0) { do {$i--; $rd=explode("|",$records[$i]); 
$allid[$i]="$dt[2]$dt[3]"; } while($i>0);
do $id++; while(in_array($id,$allid) or is_file("$datadir/$id.csv"));
} else $id=$fid."1000"; 
if (mb_strlen($id)!=7) exit("$back. Номер темы должен быть числом. Критическая ошибка скрипта или попытка взлома");
$id=mb_substr($id,3,4); // Нам нужен чистый ID из 4-х символов
} // if $add_tema==TRUE


if ($add_msg==TRUE) { // Если добавляем СООБЩЕНИЕ
$temka=file("$datadir/$fid$id.csv"); $kmax=count($temka); $k=$kmax;
do {$k--; $dtt=explode("|",$temka[$k]);
if ($dtt[3]==$id) { $zag=$dtt[5]; 
if ($dtt[11]==FALSE) exit("$back тема закрыта и добавление сообщений запрещено!");
if ($msg==$dtt[14]) exit("$back. Такое сообщение уже размещено последним в данной теме. Спамить на форуме запрещено!");
}
} while($j>0);
} // $add_msg==TRUE


$name=wordwrap($name,30,' ',1); // разрываем длинные строки
$zag=wordwrap($zag,50,' ',1); if (mb_strlen(ltrim($zag))<3) exit("$back ! Ошибка в вводе данных заголовка!");
$name=str_replace("|","I",$name);
$who=str_replace("|","&#124;",$who);
$email=str_replace("|","&#124;",$email);
$zag=str_replace("|","&#124;",$zag);
$msg=str_replace("|","&#124;",$msg);

$vip_tema="0"; // VIP-тема. Если указать 1, то такая тема будет показываться вверху!
$open_tema="1"; // Тема открыта для добавления сообщений? 1/0
$golos="0"; // Есть голосование в теме? 1/0
$rn="10000";

// Если добавляем ТЕМУ
if ($add_tema==TRUE) {$rn_tema="10000"; $text_tema="$rn|$rn_tema|$fid|$id|$tektime|$zag|$who|$rn_user|$name|$email|$vip_tema|$open_tema|$ip||"; $text_tema=replacer($text_tema);}
else {
if (is_file("$datadir/$fid$id.csv")) {$linesn=file("$datadir/$fid$id.csv"); $in=count($linesn)-1;}
$rn_tema=10000+($in+1)*10; } // Генерируем следующий по порядку RN с шагом в 10 единиц!

$text_msg="$rn_tema|$golos|$fid|$id|$tektime|$zag|$who|$rn_user|$name|$email|$vip_tema|$open_tema|$ip|"; // добавление сообщения!
$text_msg=replacer($text_msg); $exd=explode("|",$text_msg); $name=$exd[8]; $zag=$exd[5]; $msg=replacer($msg);

if (!isset($name) || mb_strlen($name) > $maxname || mb_strlen($name) <1) exit("$back Ваше <B>Имя пустое, или превышает $maxname</B> символов!</B></center>");
if (preg_match("/[^(\\w)|(\\x7F-\\xFF)|(\\-)]/",$name)) exit("$back Ваше имя содержит запрещённые символы. Разрешены русские и английские буквы, цифры, подчёркивание и тире.");
if (mb_strlen(ltrim($zag))<3 || mb_strlen($zag) > $maxzag) exit("$back Слишком короткое название темы или <B>название превышает $maxzag</B> символов!</B></center>");
if (mb_strlen(ltrim($msg))<2 || mb_strlen($msg) > $maxmsg) exit("$back Ваше <B>сообщение короткое или превышает $maxmsg</B> символов.</B></center>");
if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$email) and mb_strlen($email)>30 and $email!="") exit("$back и введите корректный E-mail адрес!</B></center>");


print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>";

if (isset($_FILES['file']['name'])) { // ЕСЛИ ДОБАВЛЯЕМ ФАЙЛ
$fotoname=replacer($_FILES['file']['name']); if (mb_strlen($fotoname)>3) { $fotosize=$_FILES['file']['size']; // Имя и размер файла

//---- ЗАЩИТЫ от ВЗЛОМА -----

// 1. Проверяем РАСШИРЕНИЕ
$ext=strtolower(mb_substr($fotoname, 1 + strrpos($fotoname, ".")));
if (!in_array($ext, $valid_types)) {echo "<BR>$back<B <B>ФАЙЛ НЕ загружен.</B> Возможные причины:<BR>
- разрешена загрузка только файлов с такими расширениями: <B>";
$patern=""; foreach($valid_types as $v) print"$v, ";
echo'</B><BR>- Вы пытаетесь загрузить файл с двойным расширением;<BR>
- неверно введён адрес или выбран испорченный файл;</B><BR>'; exit;}

// 2. считаем КОЛ-ВО ТОЧЕК в выражении - если большей одной - СВОБОДЕН!
$findtchka=substr_count($fotoname, "."); if ($findtchka>1) exit("ТОЧКА встречается в имени файла $findtchka раз(а). Это ЗАПРЕЩЕНО! <BR>\r\n");

// 2. если в имени есть .php, .html, .htm - свободен! 
$bag="Извините, но в имени ФАйла <B>запрещено</B> использовать .php, .html, .htm";
if (preg_match("/\.php/i",$fotoname)) exit("Вхождение <B>.php</B> найдено. $bag");
if (preg_match("/\.html/i",$fotoname)) exit("Вхождение <B>.html</B> найдено. $bag");
if (preg_match("/\.htm/i",$fotoname)) exit("Вхождение <B>.htm</B> найдено. $bag");

// 3. защищаем от РУССКИХ букв в имени файла и проверка РАСШИРЕНИЯ файла 
$patern=""; foreach($valid_types as $v) $patern.="$v|";
if (!preg_match("/^[a-z0-9\.\-_]+\.(".$patern.")+$/is",$fotoname)) exit("$fotoname - <br>Запрещено использовать РУССКИЕ буквы в имени файла, а также запрещено загружать файлы с расширением отличным от заданных!!");

// 4. Проверяем, может быть файл с таким именем уже есть на сервере, тогда присвоим ему имя случайным образом
if (file_exists("$filedir/$fotoname")) $random_name=TRUE;

// 5. Размер файла
$fotoksize=round($fotosize/10.24)/100; // размер ЗАГРУЖАЕМОГО файла в Кб.
$fotomax=round($max_upfile_size/10.24)/100; // максимальный размер файла в Кб.
if ($fotoksize>$fotomax) exit("Вы превысили допустимый размер файла! <BR><B>Максимально допустимый</B> размер: <B>$fotomax </B>Кб.<BR> <B>Вы пытаетесь</B> загрузить файл размером: <B>$fotoksize</B> Кб!");

// ЕСЛИ включен порядок присвоения файлу случайного имени при загрузке - генерируем случайное имя
if ($random_name==TRUE) {do $key=mt_rand(100000,999999); while (file_exists("$filedir/$key.$ext")); $fotoname="$key.$ext";}

if (copy($_FILES['file']['tmp_name'], $filedir."/".$fotoname)) {print "<br><br>Файл УСПЕШНО загружен: $fotoname (Размер: $fotosize байт)"; $fotodetali=$fotoname;}
else echo "ОШИБКА загрузки файла - $fotoname...\n"; }

} // ЕСЛИ ДОБАВЛЯЕМ ФАЙЛ

// Добавляем в переменную $text_msg кусочек о загруженном файле!
$fotodetali=replacer($fotodetali); $text_msg=$text_msg."$fotodetali|$msg||";



if ($add_msg==TRUE) { //при ОТВЕТе В ТЕМЕ
$in=$in+2; $pageadd=""; $page=ceil($in/$msg_onpage); if ($page!=1) $pageadd="&page=$page";

// ЗАЩИТА ОТ ФЛУДА: проверяем, давно ли реактивировали тему
$timetek=time(); $timefile=filemtime("$datadir/$fid$id.csv"); $timer=$timetek-$timefile; // сколько секунд назад?
if ($timer<$antiflud and $timer>0) exit("$back тема была активна менее $timer секунд назад. Подождите чуть-чуть.");

// ЕСЛИ введена команда АП!, то меняем дату создания файла и тема самая первая будет
if (strtolower($msg)=="ап!") { touch("$datadir/$fid$id.csv");
print "<script language='Javascript'>function reload() {location=\"index.php?id=$fid$id&$pageadd#m$in\"}; setTimeout('reload()', 2000);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Спасибо, <B>$name</B>, тема реактивирована.<BR><BR>Через несколько секунд Вы будете автоматически перемещены в текущую тему <BR><B>$zag</B>.<BR><BR>
<div id='cssbar-wrapper'><div id='cssbar-border'><div id='cssbar-whitespace'><div id='cssbar-line'></div></div></div></div><BR><BR>
<B><a href='index.php?id=$fid$id&$pageadd#m$in'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit;}
} // $add_msg==TRUE

$razdelname="";
if ($realbase==TRUE and $maxzd<1) { // Если подключена рабочая база, а не копия
$lines=file("$datadir/wrforum.csv"); $max=sizeof($lines)-1;
$dt=explode("|",$lines[$realfid]); $dt[7]++; $main_id="$fid$id";
if ($add_tema==TRUE) $dt[6]++;
$txtdat="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$main_id|$tektime|$name|$zag|";
$razdelname=$dt[4];
$fp=fopen("$datadir/wrforum.csv","a+"); // запись данных на главную страницу
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0;$i<=$max;$i++) {if ($i==$realfid) fputs($fp,"$txtdat\r\n"); else fputs($fp,$lines[$i]);}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
} // if ($realbase==TRUE)


if ($maxzd<1) { // запись в отдельный файл НОВОГО СООБЩЕНИЯ
$newmessfile="$datadir/news.csv";
if (is_file("$datadir/$fid.csv")) $nlines=count(file("$datadir/$fid.csv")); else $nlines=1;
if (is_file("$datadir/$fid$id.csv")) $nlines2=count(file("$datadir/$fid$id.csv"))+1; else $nlines2=1;
$newlines=file($newmessfile); $ni=count($newlines)-1; $flag=FALSE; $newlineexit="";
$ntext=$text_msg."$nlines|$nlines2|"; $ntext=str_replace("
", "<br>", $ntext);
// Блок проверяет, есть ли уже новое сообщение в этой теме. Если есть - отсеивает. На выходе - массив без этой строки.
if($ni>=15) {unset($newlines[0]); $ni--; $newlines=array_values($newlines); $flag=TRUE;} // Если в файле более 15 сообщений, то старое (первое) удаляем!
for ($i=0;$i<=$ni;$i++) { $ndt=explode("|",$newlines[$i]); if ("$fid$id"!="$ndt[2]$ndt[3]") $newlineexit.="$newlines[$i]"; else $flag=TRUE; }
if ($flag==TRUE) {$newlineexit.=$ntext; $fp=fopen($newmessfile,"w");} else {$newlineexit=$ntext; $fp=fopen($newmessfile,"a+");}
flock ($fp,LOCK_EX);
fputs($fp,"$newlineexit\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
} // if ($maxzd<1)


// БЛОК добавляет +Х к сообщению, репе, кол-ву тем, созданных юзером
if (isset($_COOKIE['wrfcookies']) and ($ok!=FALSE)) {
$ufile="$datadir/userstat.csv"; $ulines=file("$ufile"); $ui=count($ulines)-1; $ulinenew=""; $fileadd=0;
// Если юзер загружает файл - то ему ещё +Х в РЕПУ
if (isset($_FILES['file']['name']) and $repaaddfile!=FALSE) {if (mb_strlen($_FILES['file']['name'])>1) $fileadd=$repaaddfile;}

// Записываем данные в файл REPA.csv
$repa=$fileadd; if ($add_tema==TRUE) {$repa=$repa+$repaaddtem; $pochemu="За добавление темы <a href='index.php?id=$fid$id' target=_blank>$zag</a>";
} else {$repa=$repa+$repaaddmsg; $pochemu="За добавление сообщения в теме <a href='index.php?id=$fid$id' target=_blank>$zag</a>";}
$today=time();
$fp=fopen("$datadir/repa.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$today|+$repa|$wrfname||$pochemu|$fid$id|||\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

for ($i=0;$i<=$ui;$i++) { $udt=explode("|",$ulines[$i]); // Ищем юзера по имени в файле userstat.csv
if ($udt[2]==$wrfname) { $udt[6]++; $udt[7]=$udt[7]+$fileadd;
if ($add_tema==TRUE) {$udt[5]++; $udt[7]=$udt[7]+$repaaddtem;} else $udt[7]=$udt[7]+$repaaddmsg;
$ulines[$i]="$udt[0]|$tektime|$udt[2]|$udt[3]|$udt[4]|$udt[5]|$udt[6]|$udt[7]|$udt[8]|$udt[9]|$ip|$udt[11]|\r\n";}
$ulinenew.="$ulines[$i]";}
$fp=fopen("$ufile","w"); // Пишем данные в файл
flock ($fp,LOCK_EX);
fputs($fp,"$ulinenew");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp); }

if ($admin_send==TRUE) { // отправляем сообщение АДМИНИСТРАТОРУ
$headers="Content-Type: text/html; charset=UTF-8\r\n";
$headers.="From: Robot <".$adminemail.">\r\n";
$headers.="X-Mailer: WR-Forum PHP/".phpversion();
$msgmax=mb_strlen($msg); if ($msgmax>1000) $msgmax=1000; $msg=mb_substr($msg,0,$msgmax); // Урезаем сообщение до 1000 символов
// Формируем тело письма
$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><meta http-equiv='Content-Language' content='ru'></head><body>
<BR><BR><center>Это сообщение отправлено вам роботом<BR> форума <a href='$forum_url' target='_blank'><B>$forum_name</B></a><BR><BR>
<table cellspacing=0 width=700 bgcolor=navy><tr><td><table cellpadding=6 cellspacing=1 width='100%'>
<tr bgcolor=#F7F7F7><td> Сообщение:</td><td><BR><b>$name</b> на форуме добавил <a href='$forum_url"."index.php?id=$fid$id$pageadd' target='_blank'>новое сообщение</a>.<br>
Вот часть сообщения:<br><br><font color=navy>$msg</font><br>Посмотрите его полностью.<BR><BR>Всего Вам доброго!<BR></td></tr>
<tr bgcolor=#F7F7F7><td>Дата отправки сообщения:</td><td>$time - <B>$date г.</B></td></tr>
<tr bgcolor=#F7F7F7><td>Перейти на главную страницу:</td><td><a href='$forum_url'>$forum_url</a></td></tr>
</table></td></tr></table></center><BR><BR>* Данное письмо сгенерировано и отправлено роботом, отвечать на него не нужно.</body></html>";
mail("$adminemail","=?UTF-8?B?".base64_encode("Извещение с форума $forum_name")."?=",$allmsg,$headers);
} // if $admin_send==TRUE


if ($add_tema==TRUE) { // Добавление ТЕМЫ - запись данных
// Пишем В ТОПИК
$fp=fopen("$datadir/$fid.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text_tema\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// Пишем В ТЕМУ
$fp=fopen("$datadir/$fid$id.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text_msg\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

print "<script language='Javascript'>function reload() {location=\"index.php?id=$fid$id\"}; setTimeout('reload()', 2000);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Спасибо, <B>$name</B>, за добавление темы!<BR><BR>Через несколько секунд Вы будете автоматически перемещены в созданную тему.<BR><BR>
<div id='cssbar-wrapper'><div id='cssbar-border'><div id='cssbar-whitespace'><div id='cssbar-line'></div></div></div></div><BR><BR>
<B><a href='index.php?id=$fid$id'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }



if ($add_msg==TRUE) { //ОТВЕТ В ТЕМЕ - запись данных
$timetek=time(); $timefile=filemtime("$datadir/$fid$id.csv"); 
$timer=$timetek-$timefile; // узнаем сколько прошло времени (в секундах) 
$fp=fopen("$datadir/$fid$id.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text_msg\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
if ($timer<0) {$viptime=strtotime("+2 year"); touch("$datadir/$fid$id.csv",$viptime);}

print "<script language='Javascript'>function reload() {location=\"index.php?id=$fid$id$pageadd#m$in\"}; setTimeout('reload()', 2000);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Спасибо, <B>$name</B>, Ваш ответ успешно добавлен.<BR><BR>Через несколько секунд Вы будете автоматически перемещены в текущую тему <BR><B>$zag</B>.<BR><BR>
<div id='cssbar-wrapper'><div id='cssbar-border'><div id='cssbar-whitespace'><div id='cssbar-line'></div></div></div></div><BR><BR>
<B><a href='index.php?id=$fid$id$pageadd#m$in'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }
}






//--------------- ШАПКА для всех страниц форума ------------//
// Определяем дату последнего визита. +5 минут погрешности
if (isset($_COOKIE['wrfcookies'])) {
$wrfc=$_COOKIE['wrfcookies']; $wrfc=explode("|",replacer($wrfc));
$wrfname=$wrfc[0];$wrfpass=$wrfc[1];$wrftime1=$wrfc[2];$wrftime2=$wrfc[3];
if (time()>($wrftime1+240)) { $tektime=time(); $wrfcookies="$wrfc[0]|$wrfc[1]|$tektime|$wrftime1|";
setcookie("wrfcookies", $wrfcookies, time()+1728000);
$wrfc=$_COOKIE['wrfcookies']; $wrfc=explode("|",replacer($wrfc));
$wrfname=$wrfc[0];$wrfpass=$wrfc[1];$wrftime1=$wrfc[2];$wrftime2=$wrfc[3];}}
// ------------

// Начиная с версии 2.2 используются глобальные переменные:
// $forum_title - заголовок форума
// $forum_description - описание форума для людей
// $forum_keywords - ключевые слова через запятую для роботов
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
$error=FALSE; $frname=null; $frtname=""; $rfid="0"; $opisanietem=null;
$forum_title="$frtname $frname $forum_name"; // формируем ЗАГОЛОВОК ФОРУМА
$forum_keywords="$frtname $frname $forum_name $opisanietem"; // формируем КЛЮЧЕВЫЕ СЛОВА через запятую для роботов
$forum_keywords=seokeywords("$forum_keywords");
$forum_description=strip_tags($forum_info); // если больше 150 символов - обрезать по пробелу!







// БЛОК подключает копию главного файла при повреждении и др.
if (isset($_GET['razdel']) or isset($_GET['id'])) {

if (is_file("$datadir/wrforum.csv")) $mainlines=file("$datadir/wrforum.csv"); $imax=count($mainlines); $i=$imax;
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/wrf-copy.csv")) {$mainlines=file("$datadir/wrf-copy.csv"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("<center><b>Файл РУБРИК несуществует! Зайдите в <a href='admin.php'>админку</a> и создайте рубрики!</b>");

if (isset($_GET['id'])) { // Блок выводит в статусной строке: ТЕМА » РАЗДЕЛ » ФОРУМ
$id=$_GET['id'];
if (isset($_GET['quotemsg'])) $error=TRUE;
if (mb_strlen($id)<=3 and !is_file("$datadir/$id.csv")) $error="ый Вами раздел";
if (mb_strlen($id)> 3 and !is_file("$datadir/$id.csv")) $error="ая Вами тема";
if (!ctype_digit($id)) $error="ая Вами тема или раздел";

if(mb_strlen($id)>3) {$fid=mb_substr($id,0,3); $id=mb_substr($id,3,4);} else $fid=$id;

// проходим по всем разделам и топикам - ищем запрашиваемый
do {$i--; $dt=explode("|",$mainlines[$i]);
if ($dt[2]==$fid) { $frname="$dt[4] »"; $i=0; if($dt[8]>0) $maxtem=$dt[8]; else $maxtem="999";
$opisanietem="$dt[5]"; // 01-2019 надо для повышения рейтинга в поисковиках страниц с темами
$description=$opisanietem; // 02-2019 выводим в мета-тэге!!!!!!!!!!!!!!!!!!! внедрить во все 64 СКИНА
}
} while($i>0);

// Блок считывает название темы для отображения в шапке форума
if (mb_strlen($id)>3 and is_file("$datadir/$fid.csv")) {
$lines=file("$datadir/$fid.csv"); $imax=count($lines); $i=$imax;
do {$i--; $dt=explode("|",$lines[$i]);
if("$dt[2]$dt[3]"=="$fid$id") $frtname="$dt[5] »";
} while ($i>0); }

} //если isset razdel или id





$forum_title="$frtname $frname $forum_name"; // формируем ЗАГОЛОВОК ФОРУМА
$forum_keywords="$frtname $frname $forum_name $opisanietem"; // формируем КЛЮЧЕВЫЕ СЛОВА через запятую для роботов
$forum_keywords=seokeywords("$forum_keywords");
$keywords=$frtname; // $keywords.="Форум ёжиков,всё о рыбка,классный форум"; // здесь можете добавить ключевые слова
if (mb_strlen($keywords)>150) { $ttl=mb_substr($keywords,0,150); $dtemp=strrpos($ttl,' '); $keywords=mb_substr($ttl,0,$dtemp);} 





if ($error!=FALSE) { // ЗАПРЕЩАЕМ ИНДЕКСАЦИЮ страниц с цитированием / УДАЛЁННЫЕ РАЗДЕЛЫ / ТЕМЫ!

$topurl="$forum_skin/top.html";
ob_start(); include $topurl; $topurl=ob_get_contents(); ob_end_clean();
$topurl=str_replace("<meta name=\"Robots\" content=\"index,follow\">",'<meta name="Robots" content="noindex,follow">',$topurl);
print"$topurl";
if (mb_strlen($error)>1) exit("</td></tr></table><div align=center><br>Извините, но запрашиваем$error отсутствует.<br>
Рекомендую перейти на главную страницу форума по <a href='$forum_url'>этой ссылке</a>,<br>
и найти интересующую Вас тему.<br></div></td></tr></table></td></tr></table></td></tr></table></body></html>"); }


// здесь проверяем СУЩЕСТВУЕТ ЛИ СТРАНИЦА СПИСКА ТЕМ ИЛИ СТРАНИЦА ТЕКУЩЕЙ ТЕМЫ, на которую пришёл юзер
if (isset($_GET['id'])) { $id=replacer($_GET['id']);
if (mb_strlen($id)==3 or mb_strlen($id)==7) { 

if (is_file("$datadir/$id.csv")) {$lines=file("$datadir/$id.csv"); $imax=count($lines)+1;}
if (isset($_GET['page'])) $page=replacer($_GET['page']); else $page=1;

if (mb_strlen($id)==3) $maxikpage=ceil($imax/$tem_onpage); else $maxikpage=ceil($imax/$msg_onpage);
if ($page>$maxikpage and $imax!=0) {
$forum_title="$frtname $frname $forum_name"; // формируем ЗАГОЛОВОК ФОРУМА
$topurl="$forum_skin/top.html";
ob_start(); include $topurl; $topurl=ob_get_contents(); ob_end_clean();
$topurl=str_replace("<meta name=\"Robots\" content=\"index,follow\">",'<meta name="Robots" content="noindex,follow">',$topurl);
print"$topurl";

// Исключаем вызов страницы с page=1
if ($maxikpage==1) $lasturl="index.php?id=$id"; else $lasturl="index.php?id=$id&page=$maxikpage";

exit("<noindex><br><br><br><br><center><table class=forumline width=700><tr><th class=thHead colspan=4 height=25>Страница отсутствует!</th></tr>
<tr class=row2><td class=row1><center><BR><BR><B><span style='FONT-SIZE: 14px'>Такой страницы нет. В этой рубрике всего $maxikpage страниц.<br> Вы можете перейти 
<a href='index.php?id=$id'>на первую</a> или <a href='$lasturl'>последнюю страницу</a>.<br>
Через несколько секунд Вы будете перемещены на последнюю страницу.<br><br></td></tr></table>
<script language='Javascript'>function reload() {location=\"$lasturl\"}; setTimeout('reload()', 10000);</script></noindex>
</td></tr></table></td></tr></table></body></html>");
$error=TRUE; }}}

} // if (isset($_GET['id']))

if ($error==FALSE) {$forum_title="$frtname $frname $forum_name"; // формируем ЗАГОЛОВОК ФОРУМА

// с 28-01-23 нужно для поисковиков! Повышает рейтинг!
$forum_description="Обсуждение темы: $frtname $frname ".$forum_description;

// 12-2019 - канонические страницы
$forum_canonical=null; $pageadd=null; if (isset($page)) {if ($page!="0" and $page!=1) $pageadd="&page=$page";}
$idadd=null; if (isset($id)) { if (mb_strlen($id)==3 or mb_strlen($id)==7) $idadd="index.php?id=$id";
$forum_canonical='<link rel="canonical" href="'.$forum_url.$idadd.$pageadd.'"/>';}
else $forum_canonical='<link rel="canonical" href="'.$forum_url.'"/>';

include("$forum_skin/top.html"); addtop(); } // подключаем ШАПКУ форума






if ($onlineb==TRUE) { // Кто на форуме

if (isset($wrfname)) {$st_user=$wrfname;} else {$st_user="Гость";}
$st_time=time();
$st_ip=$_SERVER['REMOTE_ADDR'];
$st_date=date("d.m.Y - H:i:s");
$st_line="$st_user|$st_time|$st_ip|$st_date|\r\n";
$st_file=$datadir."/userlist.csv";

$st_guests=0; //число гостей
$st_info="0"; //строка со списком пользователей
$list_ip=" "; //строка со списком айпишников гостей
$timeout=5; //бездействие в минутах
$st_interval=$timeout*60; //бездействие в секундах

$writestart=0;
$onlfilee="$datadir/userlist.csv";
$cfilee=file_get_contents($onlfilee);

if (is_file($st_file))
{
   $st_all=file($st_file);
   $st_all[count($st_all)]=$st_line;

   for ($i=0; $i<count($st_all); $i++)
   {
       $st_arr=explode("|",$st_all[$i]);

       if (($st_time-$st_arr[1])>$st_interval)
       {
           $writestart=$i;
       } else {
           if ($st_arr[0]<>"Гость")
           {
               if ($st_user=="Гость")
               {$findstr=" $st_arr[0], ";} else {$findstr=" <a href='tools.php?event=profile&pname=$st_arr[0]' rel='nofollow'>$st_arr[0]</a>, ";}
               if (!strstr($st_info,(" ".$findstr))) {$st_info.=$findstr;}
           } else {
               $findstr=$st_arr[2]." "; //подсчет гостей

               if (!strstr($list_ip," ".$findstr)) {$list_ip.=$findstr; ++$st_guests;}
           }
       }
   }
   $fp=fopen($st_file, "w");
   flock($fp,LOCK_EX);
   for ($i=$writestart; $i<count($st_all); $i++) {fputs($fp,$st_all[$i]);}
   flock($fp,LOCK_UN);
   fclose($fp);
} else {
   $fp=fopen($st_file, "w");
   flock($fp,LOCK_EX);
   fputs($fp,$st_line);
   flock($fp,LOCK_UN);
   fclose($fp);

   if ($st_user=="Гость") {++$st_guests;} else {$st_info="$st_user ";}
}
} // if $onlineb==TRUE



// выводим ГЛАВНУЮ СТРАНИЦУ ФОРУМА

if (is_file("$datadir/user.php")) { // считываем имя последнего зарегистрировавшегося
$userlines=file("$datadir/user.php"); $dayx="";
$usercount=count($userlines); $ui=$usercount-1; $uu=$ui;
$tdt=explode("|", $userlines[$ui]);} else { $fp=fopen("$datadir/user.php","a+"); fputs($fp,"<?die;?>\r\n"); fflush ($fp); fclose($fp); $ui=""; $tdt[0]="";}
$today=time();

if (!isset($_GET['id'])) {
if (mb_strlen($frname)>1) {$frname=str_replace('»','',$frname); $frname="» <B>$frname</B>";}
print"<table><tr><td><span class=nav>&nbsp;&nbsp;&nbsp;<a href='$forum_url' class=nav>$forum_name</a> $frname</span></td></tr></table>
<table width=100% cellpadding=2 cellspacing=1 class=forumline>
<tr>
<th width=62% colspan=2 class=thCornerL height=25>Форумы</th>
<th width=7% class=thTop>Тем</th>
<th width=7% class=thCornerR>Ответов</th>
<th width=28% class=thCornerR nowrap=nowrap>Обновление</th>
</tr>";


// Выводим все РУБРИКИ НА ГЛАВНОЙ
$adminmsg=""; if (is_file("$datadir/wrforum.csv")) $mainlines=file("$datadir/wrforum.csv");
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/wrf-copy.csv")) {$mainlines=file("$datadir/wrf-copy.csv"); $datasize=sizeof($mainlines);} $adminmsg="<font color=red><B>Администратор, внимание!!!</B> Файл БД с рубриками повреждён. Восстановите его из резервной копии в админке!</font><br>";}
if ($datasize<=0) exit("Проблемы с Базой данных - обратитесь к администратору");
$i=count($mainlines); 
$n="0"; $u=$i-1; $a1=$rfid-1; $fid="0"; $itogotem="0"; $itogomsg="0"; $alt=""; $konec="";


do {$a1++; $dt=explode("|", $mainlines[$a1]);

if (isset($dt[6])) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим

// определяем тип: топик или заголовок
if ($dt[3]==TRUE) print "<tr height=28><td class=catLeft colspan=2 align=center>$dt[4]</td><td class=catRight colspan=4 align=right>&nbsp;</td></tr>";
 else {
// Если $dt[12] дата размещения сообщ. раньше (т.е. больше) $wrftime2 значит раздел форума - новый
$foldicon="folder.gif"; if (isset($wrfname)) {if ($dt[12]>$wrftime2) $foldicon="foldernew.gif";}
$fid=replacer($dt[2]);

// ФИШКА: если есть файлы типа main-FID.gif, где FID - число, то вместо обычных фолдерозаглушек, ставим этот файл
if (is_file("$forum_skin/main-$fid.gif")) {$foldicon="main-$fid.gif"; $alt="$dt[4]";} else $alt="";

$newtema=""; $page=1; $msgsize=""; $pageadd="";
if (is_file("$datadir/$dt[11].csv")) { $msgsize=sizeof(file("$datadir/$dt[11].csv")); // считаем кол-во страниц в файле
if (mb_strlen($dt[13])>20) {$dt[13]=mb_substr($dt[13],0,20); $dt[13].="..";}
if (mb_strlen($dt[14])>28) {$dt[14]=mb_substr($dt[14],0,28); $dt[14].="..";}
if ($msgsize>$msg_onpage) $page=ceil($msgsize/$msg_onpage); else $page=1;
if ($page!=1) $pageadd="&page=$page";
if (mb_strlen($dt[12])>3) {if (date("d.m.Y",$dt[12])==$date)  $dt[12]="<B>сегодня</B> в ".date("H:i",$dt[12]); else $dt[12]=date("d.m.y - H:i",$dt[12]);}
$newtema="<span class=gensmall>тема: <a href=\"index.php?id=$dt[11]$pageadd#m$msgsize\" title='$dt[14]'>$dt[14]</a> <BR>автор: <B>$dt[13]</B><BR>дата: $dt[12]</span>";
} // is_file...$dt[11]

$dt[8]=mb_substr($dt[8],0,-3);
$dt[10]=replacer($dt[10]);
$itogotem=$itogotem+$dt[6];
$itogomsg=$itogomsg+$dt[7];

if ($dt[9]>=1) {$maxzvezd="*Доступна участникам, имеющим <font color=red><B>$dt[9]</B> звезд";
$dt[6]=""; $dt[7]=""; // Маскируем кол-во тем и сообщений в закрытой теме
if ($dt[9]==1) $maxzvezd.="у"; if ($dt[9]>=2 and $dt[9]<=4) $maxzvezd.="ы";
$maxzvezd.=" минимум</font>";} else $maxzvezd=null;

print "<tr align=center valign=middle height=50>
<td width=3% class=row1><img src=\"$forum_skin/$foldicon\" alt='$alt' border=0></td>
<td width=60% class=row1 align=left><span class=forumlink><a href=\"index.php?id=$fid\">$dt[4]</a> $maxzvezd<BR></span>$dt[5]</td>
<td class=row2><small>$dt[6]</small></td>
<td class=row2><small>$dt[7]</small></td>
<td class=row2 align=left>$newtema</td></tr>\r\r\n";

 } 
if ($konec==2) $a1=$u;
} // if isset($dt[6]
} while($a1 < $u);
echo('</table><BR>');



if ($navigate==TRUE) { // БЫСТРЫЙ ПЕРЕХОД к теме, если разрешено
echo '<table width=100% cellpadding=3 cellspacing=1 class=forumline><TR><TD class=catHead><span class=cattitle>Навигация</span></td></tr>
<tr><td class=row1 align=right><span class=gensmall>
Быстрый переход по рубрикам &nbsp; <select onchange="window.location=(\'index.php?id=\'+this.options[this.selectedIndex].value)">
<option>Выберите рубрику</option>';
$ii=count($mainlines); $cn=0; $i=0;
do {$dt=explode("|",$mainlines[$i]);
if ($dt[3]==TRUE) {if ($cn!=0) {echo'</optgroup>'; $cn=0;} $cn++; print"<optgroup label='$dt[4]'>";} else print" <option value='$dt[2]'>|-$dt[4]</option>";
$i++;} while($i<$ii);
echo'</optgroup></select></TD></TR></TABLE><br>'; } // $navigate=TRUE






// изменения в блоке 12.2022 г.
if ($statistika==TRUE) { // СТАТИСТИКА ИТОГО ТЕМ/СООБЩЕНИЙ/ПРАВА ЮЗЕРОВ
?>
<style>
{margin: 0; box-sizing: border-box;}
.post-wrap {margin: 0 10px; display:flex; flex-wrap:wrap;}
.post-item-wrap {max-width: 250px; position: relative;}
.post-title {font-size: 20px; margin-bottom: 15px;}
.post-item-wrap:hover .post-title {font-size: 24px;}
@media (min-width: 300px) {.post-item {flex-basis: 20%; flex-shrink: 0;}}
</style>

<table width=100% cellpadding=2 cellspacing=1 class=forumline><tr><th class=catHead colspan=2 height=28 align=left><span class=cattitle>Статистика форума</span></th></tr><tr>
<td class=row1 align=center valign=middle rowspan=2><img src="<?print"$forum_skin/whosonline.gif";?>"></td>
<td class=row1 align=left width=95%>
   <div class="container">
      <div class="post-wrap">
         <div class="post-item">
            <div class="post-item-wrap">
                  <h3 class="post-title"><?echo"<b>$itogomsg</b>";?></h3>
                  <p class="post-content">Сообщений</p>
            </div>
         </div>
         <div class="post-item">
            <div class="post-item-wrap">
                  <h3 class="post-title"><?echo"<b>$itogotem</b>";?></h3>
                  <p class="post-content">Тем</p>
            </div>
         </div>
         <div class="post-item">
            <div class="post-item-wrap">
                  <h3 class="post-title"><?
if (!isset($wrfname)) print"$ui"; else print"<a href=\"tools.php?event=who\" rel='nofollow'>$ui</a>";?></h3>
                  <p class="post-content"><?
				  echo'Пользователей<br>Самый новый:';
$codename=urlencode($tdt[2]);
if (!isset($wrfname)) print"$tdt[2]"; else print"<a href=\"tools.php?event=profile&pname=$codename\" rel='nofollow'>$tdt[2]</a>";
?></p>
            </div>
		</div>	
<?if ($onlineb==TRUE){ print'
		<div class="post-item">
            <div class="post-item-wrap">
                  <h3 class="post-title">';echo $st_guests;print'</h3>
                  <p class="post-content">';
// БЛОК "КТО ОНЛАЙН"
print "Онлайн пользователей:<BR> <b>$st_guests</b> -  Участники, $st_info - Гости. <BR>(за последние $timeout мин)";echo'
				  </p>
            </div>
		</div>	';}?>
		<div class="post-item">
            <div class="post-item-wrap">
                  <h3 class="post-title">Доступ</h3>
                  <p class="post-content">Гостям <?
				  if ($g_add_tema==TRUE) $c1="разрешено"; else $c1="запрещено"; print "<B>$c1</B>";?> создавать темы и <?if ($g_add_msg ==TRUE) $c2="разрешено"; else $c2="запрещено"; print "<B>$c2</B>";?> отвечать в темах</p>
            </div>
         </div>
      </div>
</div>
<?
print"$adminmsg</span></td></tr></table>"; 



// СТАТИСТИКА -=ДНИ РОЖДЕНИЯ=-
if (is_file("$datadir/user.php")) { // считываем всех юзеров, ищем дни варения
$ui=$usercount-1; $t_people=""; $z_people=""; $s_people=""; $p_people=""; $s_p="0";
if($pozdravka==TRUE) $pozdrflag=TRUE; else $pozdrflag=FALSE;
do {$udt=explode("|",$userlines[$ui]);
if (isset($udt[1])) {
$udt[7]=mb_substr("$udt[7]",0, 10); // обрезаем дату рождения по формату
if (preg_match("(\d{2}\.\d{2}\.\d{4})",$udt[7])) { // соответствует шаблону ДД.ММ.ГГГГ
$bday=mb_substr("$udt[7]",0,-5);
$dmtoday=date("d.m");
$todaydt=explode(".",$dmtoday);
$codename=urlencode($udt[2]);


if($pozdrflag==TRUE) { // 01-2019 - отправка письма в ПМ от робота - поздравление с днём варения!
if ($bday==$dmtoday) { // только сегодняшние

$flagpm=FALSE; $tektime=time();
$to_name=strtolower($udt[2]);;
// идея такая - есть есть файл с ПМ, узнаём его дату создания. Если меньше суток - то не отправляем письмо
$pmfile="data-pm/$to_name.csv";
if (is_file("$pmfile")) {
$filedat=filemtime($pmfile);
$info=round(($tektime-$filedat)); // поздравляем раз в сутки
//print "$info=$tektime-$filedat<BR>";
if ($info>86400) $flagpm=TRUE; // Если файл старше 24 часов, то надо поздравлять!
} else $flagpm=TRUE;

$rn=mt_rand(10000,99999); // Колдуем случайный ключ
$from_rn="10000"; // от первого пользователя (администратора) все сообщения будут
$from_name="Робот_форума";
$to_rn=$udt[0]; // именнинник

$tema="С Днём Вашего рождения!";
$msg="Робот и администрация форума поздравляем С Днём Вашего рождения! Ура!";
//      rn|time|status|from_rn|to_rn|from_name|to_name|tema|msg|rezerved|
$text="$rn|$tektime|0|$from_rn|$to_rn|$from_name|$to_name|$tema|$msg||\r\n";
if ($flagpm==TRUE) { // Если надо поздравлять - поздравляем!
$fp=fopen("data-pm/$to_name.csv","a+");
flock ($fp,LOCK_EX);
fputs($fp,$text);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);}
} // if ($bday==$dmtoday)
} // $pozdravka=TRUE


// собираем для СЕГОДНЯШНИХ
if ($bday==$dmtoday) $t_people.="<a href='tools.php?event=profile&pname=$codename' rel='nofollow'>$udt[2]</a>,&nbsp;&nbsp; ";
$dt=explode(".",$udt[7]);
if ($dt[1]==12) $year=1972; else $year=1973; // для того, чтобы верно считать с декабря по январь
$newdate=mktime(0,0,0,$dt[1],$dt[0],$year); // для дня рождения
$tekdt=mktime(0,0,0,$todaydt[1],$todaydt[0],$year); // текущую дату переводим в этот формат
$deystodate=round(($newdate-$tekdt)/86400); // через сколько дней наступит событие

// собираем для ВЧЕРАШНИХ
if ($deystodate=="-1") $z_people.="<a href='tools.php?event=profile&pname=$codename' rel='nofollow'>$udt[2]</a>,&nbsp; ";
// собираем ПРИБЛИЖАЮЩИХСЯ в массив (БОЛЬШЕ 1 и МЕНЬШЕ 7 дней)
if ($deystodate>=1 and $deystodate<7) {if ($deystodate<10) $deystodate="0$deystodate"; $s_peo[$s_p]="$deystodate|$udt[2]|"; $s_p++;}
} // if указан день варения в переменной $udt[7]
} // if iiset($udt[1])
$ui--; } while ($ui>0);

if (isset($s_peo)) {
sort($s_peo); // сортируем дни по возрастанию
$i=0; do {$sdt=explode("|",$s_peo[$i]);
$sdt[0]=intval($sdt[0]); // преобразуем строку в число, чтобы отбросить 0 в начале
$codename=urlencode($sdt[1]);
$s_people.="<b><noindex><a href='tools.php?event=profile&pname=$codename' rel='nofollow'>$sdt[1]</a></noindex></b> ($sdt[0] дн.), "; $i++;
} while ($i<$s_p); }

// На тот случай, если дней рождения нет, делаем заглушки
if (mb_strlen($z_people)>1) $z_people="<span class=gensmall><br>Вчера: <B>$z_people</B><font color=#800040> поздравляем с Днём рождения!</font><br>";
if (mb_strlen($t_people)>1) $t_people="<span class=gensmall><br>Сегодня: <B>$t_people</B><font color=red>искренне поздравляем!</font><br>";
if (mb_strlen($s_people)>1) $s_people="<span class=gensmall><br>Скоро: $s_people <font color=#55002B>совсем скоро День рождения!</font><br>";

if (mb_strlen($z_people)>1 or mb_strlen($t_people)>1 or mb_strlen($s_people)>1){
print"<br><table width=100% cellpadding=2 cellspacing=1 class=forumline>
<tr><th class=catHead colspan=2 height=28 align=left><span class=cattitle>Дни рождения</span></th></tr><tr>
<td class=row1 align=center valign=middle rowspan=2><img src=\"$forum_skin/whosonline.gif\"></td>
<td class=row1 align=left width=95%>
$z_people $t_people $s_people<br>
</span></td></tr></table>";}
} // КОНЕЦ БЛОКА ДНИ РОЖДЕНИЯ



// СТАТИСТИКА: БЛОК последних сообщения с форума
if (is_file("$datadir/news.csv")) { $newmessfile="$datadir/news.csv";
$lines=file($newmessfile); $i=count($lines); //if ($i>10) $i=10; // Если нужно более 10 - укажите здесь сколько
if ($i>0) {
echo'<br><table width=100% cellpadding=2 cellspacing=1 class=forumline>
<tr><th class=catHead colspan=3 height=28 align=left><span class=cattitle>Новые сообщения</span></th></tr><tr>
<td class=row1 align=center valign=middle rowspan=2><img src="'.$forum_skin.'/whosonline.gif"></td>
<td class=row1 align=left valign=top width=95%><span class=gensmall style="line-height:1.5em;">';

$mmax=count($mainlines);
$a1=$i-1;$u="-1"; // выводим данные по возрастанию или убыванию
do {$dt=explode("|",$lines[$a1]); $a1--;

if (isset($dt[1])) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим
$msg=htmlspecialchars($dt[14],ENT_COMPAT,"UTF-8");
$msg=replacer($msg);
$msg=str_replace('[b]'," ",$msg); $msg=str_replace('[/b]'," ",$msg);
$msg=str_replace('[RB]'," ",$msg); $msg=str_replace('[/RB]'," ",$msg);
$msg=str_replace('[Code]'," ",$msg); $msg=str_replace('[/Code]'," ",$msg);
$msg=str_replace('[Quote]'," ",$msg); $msg=str_replace('[/Quote]'," ",$msg);
$msg=str_replace('[img]'," картинка: ",$msg); $msg=str_replace('[/img]'," ",$msg);
$msg=str_replace("[br]","\r\n", $msg);
$msg=str_replace("<br>","\r\n", $msg);
$msg=str_replace("'","`",$msg);
$msg=str_replace('&amp;lt;br&amp;gt;'," \r\r\n", $msg);
$msg=str_replace('&lt;br&gt;'," \r\r\n", $msg);

$k=$mmax; $mainr=""; // Ищем название рубрики, как находим - присваимваем значение и выходим из цикла!
do {$k--; $mdt=explode("|",$mainlines[$k]);
if ($mdt[2]==$dt[2]) {$mainr="<a href='".$forum_url."index.php?id=$mdt[2]' class=nav>$mdt[4]</a>"; $k=0;}
} while($k>0);

if (date("d.m.Y",$dt[4])==$date)  $dt[4]="сегодня в ".date("H:i",$dt[4]); else $dt[4]=date("d.m.y - H:i",$dt[4]);

if ($dt[17]>$msg_onpage) $page=ceil($dt[17]/$msg_onpage); else $page=1; // Считаем страницу
if ($page!=1) $pageadd="&page=$page"; else $pageadd="";

if ($dt[6]==TRUE) {$codename=urlencode($dt[8]); if (!isset($wrfname)) $name="$dt[8]"; else $name="<B><a href='tools.php?event=profile&pname=$codename' rel='nofollow'>$dt[8]</a></B>";} else $name="гость $dt[8]";
print"$dt[4]: 
<strong>$mainr</strong> » <B><a href='index.php?id=$dt[2]$dt[3]$pageadd#m$dt[17]' title='$msg \r\n\r\n Отправлено $dt[4]'>$dt[5]</a></B> - $name.<br>";
} // если строчка потерялась
$a11=$u; $u11=$a1;
} while($a11 < $u11);

echo'</span></td></tr>';

// Инклюдим БЫСТРЫЙ ЧАТ
if ($quikchat==TRUE) print"<tr><td class=row1 align=center width=95%><iframe src=chat.php width=$chatframesize height=180 frameborder=no border=no></iframe></td>";

echo'</tr></table>';

}
} // Конец блока последних сообщений

	


// ОБЩИЕ ДАННЫЕ ДЛЯ БЛОКОВ САМЫХ ТЕМ И ЮЗЕРОВ

$timetek=time(); $timefile=0; $i=0; $msgtoprint="";
if(is_file("$datadir/best.csv")) $timefile=filemtime("$datadir/best.csv"); // Проверяем дату создания файла best.csv
$timer=$timetek-$timefile; // узнаем сколько прошло времени в секундах С ПОСЛЕДНЕГО ОБНОВЛЕНИЯ
$srokobnov="86400"; // 86400 = 24 часа
$maxtemsuper="15"; // По сколько максимум выводить самых обсуждаемых тем
$maxusrsuper="10"; // --/-- участников

if ($specblok1==TRUE) { // формируем раз в 24 часа и выводим БЛОК 15 самых ОБСУЖДАЕМЫХ ТЕМ

if ($timer>$srokobnov) { // ЕСЛИ прошёл период, то пересоздаём информационный блок

// НАЧАЛО создания файла - пробегаем по директории и считываем файлы
if ($handle=opendir($datadir)) { while (($file=readdir($handle))!==FALSE)
if (!is_dir($file)) { $id=str_replace(".csv","",$file);
if (ctype_digit($id) and mb_strlen($id)==7) {$lines=file("$datadir/$file"); $itogo=count($lines);
   if ($itogo<10) $itogo="0$itogo"; if ($itogo<100) $itogo="0$itogo"; $massiv[$i]="$itogo|$file|";  $i++;} } }
sort($massiv); // сортируем дни по возрастанию
$maxi=count($massiv)-$maxtemsuper; if ($maxi<0) $maxi=0; $i=count($massiv)-1;
$msgtoprint.="<br><table width=100% cellpadding=2 cellspacing=1 class=forumline>
<tr><th class=catHead colspan=2 height=28 align=left><span class=cattitle>Самые обсуждаемые темы ($maxtemsuper)</span></th></tr><tr>
<td class=row1 align=center valign=middle rowspan=2><img src=\"$forum_skin/whosonline.gif\"></td><td class=row1 align=left width=95%><span class=gensmall style='line-height:1.5em;'>";

do {
$dt=explode("|",$massiv[$i]);
$lines=file("$datadir/$dt[1]"); $max=$dt[0]-1;
$tdt=explode("|",$lines[$max]);
if ($dt[0]>$msg_onpage) $page=ceil($dt[0]/$msg_onpage); else $page=1;
if ($page!=1) $addpage="&page=$page"; else $addpage="";
if (date("d.m.Y",$tdt[4])==$date)  $tdt[4]="<B>сегодня</B> в ".date("H:i",$tdt[4]); else $tdt[4]=date("d.m.y - H:i",$tdt[4]);
$dt[0]++; $dt[0]--;
$msgtoprint.="$tdt[4] [<B>$dt[0] сообщ.</B>] <B><a href='index.php?id=$tdt[2]$tdt[3]$addpage#m$dt[0]'>$tdt[5]</a></B> - $tdt[8]<BR>\r\n";
$i--; } while($i>$maxi);
$msgtoprint.=" * блок сгенерирован $date г. в $time</span></td></tr></table>";
} // if $timer
} // if $specblok1



if ($specblok2==TRUE) { // формируем раз в 24 часа и выводим БЛОК 10 самых АКТИВНЫХ ПОЛЬЗОВАТЕЛЕЙ

if ($timer>$srokobnov) { // ЕСЛИ прошёл период, то пересоздаём информационный блок

$lines=file("$datadir/userstat.csv"); $i=count($lines); $maxi=$i;
if ($i>3) { // Если зарегестрировано БОЛЕЕ 2-х участников
do {$i--; $dt=explode("|",$lines[$i]);
if ($dt[6]<10) $dt[6]="0$dt[6]"; if ($dt[6]<100) $dt[6]="0$dt[6]"; if ($dt[6]<1000) $dt[6]="0$dt[6]";
if ($dt[7]<10) $dt[7]="0$dt[7]"; if ($dt[7]<100) $dt[7]="0$dt[7]"; if ($dt[7]<1000) $dt[7]="0$dt[7]";
$record1[$i]="$dt[6]|$dt[5]|$dt[2]|"; $record2[$i]="$dt[7]|$dt[5]|$dt[2]|";
} while($i>1);
sort($record1); sort($record2);

if ($maxi<=$maxusrsuper) $itogousers=$maxi-1; else $itogousers=$maxusrsuper;

$text="<br><table width=100% cellpadding=3 cellspacing=1 class=forumline>
<tr><td class=catHead colspan=2 height=28><span class=cattitle>Самые активные участники ($itogousers)</span></td></tr>
<tr><td class=row1 align=center valign=middle rowspan=2><img src=\"$forum_skin/whosonline.gif\"></td>
<td class=row1 align=left width=95%><table width=500><TR><TD><span class=gensmall style='line-height:1.5em;'>По количеству сообщений:<br><br>";
$text2='</TD><TD><span class=gensmall style="line-height:1.5em;">По репутации:<br><br><noindex>'; $k=0;

do {$k++; $numus=$maxi-$k-1; $dt=explode("|",$record1[$numus]);
$dtt=explode("|",$record2[$numus]);
if ($dtt[0]>0) { // Если репа>0
$codename2=urlencode($dtt[2]); $codename=urlencode($dt[2]);
$text2.="<B><a href='tools.php?event=profile&pname=$codename2' rel='nofollow'>$dtt[2]</a></B>";
$dtt[0]++; $dtt[0]--; $text2.=" - $dtt[0]<br>";

$text.="<B><a href='tools.php?event=profile&pname=$codename' rel='nofollow'>$dt[2]</a></B>";
$dt[0]++; $dt[0]--; $text.=" - $dt[0]<br>";
} // Если репа > 0
} while($k<$itogousers);
if (mb_strlen($text)>30) $msgtoprint.="$text $text2</noindex></td></tr></table><small>* блок сгенерирован $date г. в $time</small></td></tr></table>"; else $msgtoprint="";
} // if ($i>3)
} // if $timer
} // if $specblok2

if (mb_strlen($msgtoprint)>1) { // Если сформированы новые данные, то сохраняем их в файл
$fp=fopen("$datadir/best.csv","w");
flock ($fp,LOCK_EX);
fputs($fp,"$msgtoprint");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);}

if ($specblok1==TRUE or $specblok2==TRUE) {$msg=file_get_contents("$datadir/best.csv"); print"$msg";} // СЧИТЫВАЕМ содержимое файла best.csv и выводим его



} // конец БЛОКОВ СТАТИСТИКИ if (statistika==TRUE)

if (is_file("$forum_skin/bottom.html")) include("$forum_skin/bottom.html"); // подключаем НИЖНИЙ БЛОК форума

} // if !isset($id) // конец главной страницы











if (isset($_GET['id'])) {  // страница С ТЕМАМИ выбранной РУБРИКИ

$id=$_GET['id']; if (mb_strlen($id)==3) { $fid=$id;

// Защиты
if (!ctype_digit($fid) or mb_strlen($fid)>3) exit("$back. Номер рубрики должен быть цифровым и содержать 3 символа!");
$imax=count($mainlines); if (($fid>999) or (mb_strlen($fid)==0)) exit("<b>Данный раздел удалён или не существует.</b>");

// Исключаем ошибку вызова несуществующей страницы
if (!isset($_GET['page'])) $page=1; else {$page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1;}

// Уточняем статус по кол-ву ЗВЁЗД юзера. Если меньше допустимых N в этой рубрике - то досвиданья!
$maxzd=null;
do {$imax--; $ddt=explode("|", $mainlines[$imax]); if ($ddt[2]==$fid) $maxzd=$ddt[9]; } while($imax>"0");
if ($maxzd>=1) {
if (!ctype_digit($maxzd)) exit("$back звёзды исчисляются в цифрах. а в файле данных - ерунда!");

$noacsess="<br><br><br><br><center><table class=forumline width=700><tr><th class=thHead colspan=4 height=25>Доступ в раздел ограничен</th></tr>
<tr class=row2><td class=row1><center><BR><BR><B><span style='FONT-SIZE: 14px'>Для просмотра данного раздела необходимо быть зарегистрированным и иметь рейтинг не менее $maxzd звёзд.";

// БЛОК проверяет логин и пароль юзера, считывает кол-во его звёзд и сравнивает с заданным в рубрике
if (isset($_COOKIE['wrfcookies'])) { // весь блок работает при наличии КУКИ
$text=$_COOKIE['wrfcookies']; 
$text=replacer($text);
$wrfc=explode("|",$text); $wrfname=$wrfc[0]; if (isset($wrfc[1])) $wrfpass=$wrfc[1]; else exit("$back попытка взлома - в куки нет пароля. Куда он делся ;-) ?");

// пробегаем файл с юзерами
$iu=$usercount; $ok=FALSE;
do {$iu--; $du=explode("|",$userlines[$iu]);
if (isset($du[1])) { $realname=strtolower($du[2]);
if (strtolower($wrfname)===$realname & $wrfpass===$du[3]) {$ok="$i"; if ($du[4]<$maxzd) exit("$noacsess У Вас всего $du[4] звёзд.</B></center><br><br></td></table><br>"); }}
} while($iu > 1);
} else exit("$noacsess</B></center><br><br></td></table><br>"); // если юзер тот, за кого себя выдаёт то его пускаем, иначе - обломаем
if ($ok==FALSE) exit("$noacsess</B></center><br><br></td></table><br>");
}

$addbutton="<table width=100%><tr><td align=left valign=middle>";
if ($forum_lock!=TRUE) $addbutton.="<noindex><span class=nav><a name='add' href=\"index.php?newtema=add&id=$fid#add\" rel='nofollow'><img src='$forum_skin/newthread.gif' border=0></a>&nbsp;</span></noindex></td>";
else $addbutton.="Извините за неудобство, но администратор временно приостановил добавление тем и сообщений!";



// определяем есть ли информация в файле с данными
if (is_file("$datadir/$fid.csv")) {
$msglines=file("$datadir/$fid.csv"); $maxi=count($msglines); $i=$maxi;

if (isset($_POST['findme']) or isset($_GET['findme'])) {
// ЕСЛИ есть фильтр по названию темы, то:
// - Считываем файл с темами и отбираем в отдельный массив только те, которые содаржат в названии искомую фразу
// - в $maxi записываем кол-во тем
// - в $msglines[$i] записываем данные
setlocale(LC_ALL,'en_US.utf8'); // ! РАЗРЕШАЕМ РАБОТУ ФУНКЦИЙ, работающих с регистором и с РУССКИМИ БУКВАМИ
if (isset($_POST['findme'])) $findme=replacer($_POST['findme']);
if (isset($_GET['findme'])) { $findme=replacer($_GET['findme']); $findme=urldecode($findme);}
$stroka=mb_strlen($findme,"utf8"); // - изменение 01-2019
if($stroka<3 or $stroka>30) exit("разрешается поиск в количестве от 3-х до 30-и символов!");
$tmplines=$msglines; $msglines=null; $i=0;
foreach($tmplines as $v) {$dt=explode("|", $v); if (stristr($dt[5],$findme,false)) {$msglines[$i]=$v; $i=$i+1;}}
$maxi=$i;} else $findme=""; //  $maxi=$i; - изменение 01-2019

$frname=str_replace(' »','',$frname); //вырезаем лишние символы


// 2019  - хлебные крошки:  Главная  »  Форум  »  РУБРИКА  »  СТРАНИЦА 1/2/3..Х
// $pageur1 - Ссылка на главную [1 уровень]
$pageur2=$forum_url; // Ссылка на все статьи [2 уровень]
$pageur3="index.php?id=$id"; // Ссылка рубрику стр 1 [3 уровень]
// текст № страницы без ссылки [4 уровень]

print"<table border=0 width=100%><TR height=40><td>
<div class=pgbutt style='padding:0px;margin:0px;'>
<div itemscope='' itemtype='http://schema.org/BreadcrumbList' id='breadcrumbs'>";
if (mb_strlen($pageur1)>5) print"
	   <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
       <a itemprop='item' title='Главная страница сайта' href='$pageur1'>
          <span itemprop='name'>Главная сайта</span>
          <meta itemprop='position' content='1'>
       </a>
   </span>&nbsp; » &nbsp;";
print"   <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
       <a itemprop='item' title='$forum_name' href='$pageur2'>
          <span itemprop='name'>$forum_name</span>
          <meta itemprop='position' content='2'>
       </a>
   </span>&nbsp; » &nbsp;
   <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
       <a itemprop='item' title='Темы в рубрике: $frname' href='$pageur3'>
          <span itemprop='name'>$frname</span>
          <meta itemprop='position' content='3'>
       </a>
   </span>&nbsp; » &nbsp;
      <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
		  <span itemprop='name'>Страница $page</span>
          <meta itemprop='position' content='4'>
   </span>
</div>
</div>";

print"<h1>$frname</h1><TD align=right><form action='index.php?id=$fid&find' method=POST name=finder>Фильтр по названию темы: 
<input name='findme' value='$findme' class=post type='text' maxlength=30 size=20>
<input type=submit class=mainoption value='Искать'></form></td></tr></table>";

// 2019 надо для поисковиков!
print"<table class=forumline align=center width=100% cellspacing=1 cellpadding=2><tr><th class=thHead height=20>Информация о разделе</th></tr><tr><td class=row1 align=center>$opisanietem<br></td></table>";

print"<table width=100% cellpadding=2 cellspacing=1 class=forumline><tr>
<th width=60% colspan=2 class=thCornerL height=25>Тема</th>
<th width=10% class=thTop nowrap=nowrap>Cообщений</th>
<th width=12% class=thCornerR nowrap=nowrap>Автор</th>
<th width=18% class=thCornerR>Обновления</th>
</tr>";

if ($maxi>0) {

if ($maxi>$maxtem-1) $addbutton="<table width=100%><TR><TD>Количество допустимых тем в рубрике исчерпано. Обратитесь к администритору за увеличением лимита.";

// БЛОК СОРТИРОВКИ: последние ответы ВВЕРХУ (по времени создания файла с темой)!
do {$i--; $dt=explode("|", $msglines[$i]);
   $filename="$dt[2]$dt[3].csv"; if (is_file("$datadir/$filename")) $ftime=filemtime("$datadir/$filename"); else $ftime="";
   $newlines[$i]="$dt[10]|$ftime|$dt[2]$dt[3]|$i|";
} while($i > 0);
sort($newlines);
//print"<PRE>"; print_r($newlines); exit;
// $newlines - массив с данными: ДАТА | ИМЯ_ФАЙЛА_С_ТЕМОЙ | № п/п |
// $msglines - массив со всеми темами выбранной рубрики
$i=$maxi;
do {$i--; $dtn=explode("|", $newlines[$i]);
  $numtp="$dtn[3]"; $lines[$i]="$msglines[$numtp]";
} while($i > 0);
// КОНЕЦ блока сортировки

// Показываем tem_onpage ТЕМ
$fm=$maxi-$tem_onpage*($page-1);
if ($fm<"0") $fm=$tem_onpage; $lm=$fm-$tem_onpage; if ($lm<"0") $lm="0";

do {$fm--; $num=$fm+2;
$dt=explode("|", $lines[$fm]);

// нужно для определения темы на VIP-статус
$dtn=explode("|", $newlines[$fm]);
$timer=time()-$dtn[0]; // узнаем сколько прошло времени (в секундах) 


$filename="$dt[2]$dt[3]"; 
if (is_file("$datadir/$filename.csv")) { // если файл с темой существует - то показать тему в списке!
$msgsize=sizeof(file("$datadir/$filename.csv"));
// 12-2019 Считываем Первое сообщение из темы и ставим в title к ссылке. Нужно для удобства и поисковиков!
$rlines=file("$datadir/$dt[2]$dt[3].csv"); $dr=explode("|",$rlines[0]); $addinfo=$dr[14];

// --------- Выделяем новые сообщения
$linetmp=file("$datadir/$filename.csv"); if (sizeof($linetmp)!=0) {
$pos=$msgsize-1; $dtt=explode("|", $linetmp[$pos]);
$foldicon="folder.gif";
// Если последнее сообщение в форуме произошло раньше посещения - значит раздел форума - новый
if (isset($wrfname)) {if (isset($dtt[4])) {if ($dtt[4]>$wrftime2) $foldicon="foldernew.gif";}}
if ($dt[11]==FALSE) { if ($msgsize<"20") $foldicon="close.gif"; else $foldicon="closed.gif"; } } else $foldicon="foldernew.gif";
// --------- Конец

print "<tr height=50 align=center valign=middle>
<td width=3% class=row1><img src=\"$forum_skin/$foldicon\" border=0></td>
<td class=row1 align=left><span class=forumlink><b>";

if ($dt[10]==TRUE) echo'<font color=red>VIP </font>';

$dt[5]=replacer($dt[5]);
print"<a href=\"index.php?id=$dt[2]$dt[3]\" title=\"$dt[5]:\r\n\r\n $addinfo\">$dt[5]</a></B>";

if ($msgsize>$msg_onpage) { // ВЫВОДИМ СПИСОК ДОСТУПНЫХ СТРАНИЦ ТЕМЫ
$maxpaget=ceil($msgsize/$msg_onpage); $addpage="";
echo'</b></span><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="padding:6px;" class=pgbutt>Страницы: ';
if ($maxpaget<=5) $f1=$maxpaget; else $f1=5;
for($i=1; $i<=$f1; $i++) {if ($i!=1) $addpage="&page=$i"; print"<a href=index.php?id=$dt[2]$dt[3]$addpage>$i</a> &nbsp;";}
if ($maxpaget>5) print "... <a href=index.php?id=$dt[2]$dt[3]&page=$maxpaget>$maxpaget</a>"; }


print"</div></td><td class=row2>$msgsize</td><td class=row2><span class=gensmall>";

$codename=urlencode($dt[8]);
if ($dt[6]==TRUE) {
if (!isset($wrfname)) print "$dt[8]"; else print "<a href='tools.php?event=profile&pname=$codename' rel='nofollow'>$dt[8]</a>";
print"<BR><small>$user_name</small>"; } else print"$dt[8]<BR><small>$guest_name</small>";

// защита if (strlen...) только если файл есть и имеет верный формат - выводим
if ($msgsize>=2) {$linesdat=file("$datadir/$filename.csv"); $dtdat=explode("|", $linesdat[$msgsize-1]);
if (mb_strlen($linesdat[$msgsize-1])>10) {$dt[0]=$dtdat[0]; $dt[1]=$dtdat[1]; $dt[2]=$dtdat[2]; $dt[5]=$dtdat[5]; $dt[6]=$dtdat[6];}}

if (date("d.m.Y",$dtt[4])==$date)  $dtt[4]="<B>сегодня</B> в ".date("H:i:s",$dtt[4]); else $dtt[4]=date("d.m.y - H:i",$dtt[4]);
print "</span></td><td class=row2 align=left><span class=gensmall>Автор: <B>$dtt[8]</B><BR>дата/время: $dtt[4]</span></td></tr>\r\r\n";

} //if is_file

} while($lm < $fm);

// формируем переменную $pageinfo - со СПИСКОМ СТРАНИЦ
if (mb_strlen($findme)>1) $findadd="&findme=$findme"; else $findadd="";
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$tem_onpage); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div style='padding:6px;' class=pgbutt>Страницы: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=index.php?id=$fid$findadd>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=index.php?id=$fid$addpage$findadd>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=index.php?id=$fid&page=$maxpage$findadd>$maxpage</a>";
$pageinfo.='</div>';

print"
$addbutton<TD><table width=100%><tr><td align=right colspan=3>
$pageinfo</b></span></td></tr></table>";

} else print"$addbutton";

} else print"$addbutton";

echo'</tr></table><BR>';


if (isset($_GET['newtema'])) { if ($g_add_tema==FALSE and !isset($wrfname)) print"<center><h5>Администратор запретил создавать гостям темы! Для регистрации пройдите по ссылке: <B><a href='tools.php?event=reg' rel='nofollow'>зарегистрироваться</a></B></h5></center><BR><BR>"; else {
$maxzag=$maxzag-10; // так нужно!!!
print"<form action=\"index.php?add_tema&id=$fid\" method=post enctype=\"multipart/form-data\" name=REPLIER><table width=100% class=forumline><tr><td class=catHead colspan=2 height=28><span class=cattitle>Добавление темы</span></td></tr>
<tr><td class=row1 valign=top><B>Заголовок темы</B></TD><TD class=row2>
<input type=hidden name=maxzd value='$maxzd'><input type=text class=post name=zag maxlength=$maxzag size=70>
</TD></TR>";
addmsg("");
} }


if ($maxi>0) { // БЫСТРЫЙ ПЕРЕХОД к теме
if ($navigate==TRUE) {  // БЫСТРЫЙ ПЕРЕХОД к теме, если разрешено. 2018
echo '<br><table width=100% cellpadding=3 cellspacing=1 class=forumline><TR><TD class=catHead><span class=cattitle>Навигация</span></td></tr>
<tr><td class=row1 align=right><span class=gensmall>
Быстрый переход по темам &nbsp; <select onchange="window.location=(\'index.php?id=\'+this.options[this.selectedIndex].value)">
<option>Выберите тему</option>';
$ii=$maxi; $cn=0; $i=0;
do {$dt=explode("|", $lines[$i]); print" <option value='$dt[2]$dt[3]'>$dt[5]</option>"; $i++;} while($i<$ii);
echo'</optgroup></select></TD></TR></TABLE>'; }} // if($maxi>0)
}








// показываем СООБЩЕНИЯ выбранной темы
if (mb_strlen($id)>6) { $fid=mb_substr($id,0,3);

// определяем есть ли информация в файле с данными
if (!is_file("$datadir/$id.csv")) exit("$back. Извините, но такой темы на форуме не существует.<BR> Скорее всего её удалил администратор.");
$lines=file("$datadir/$id.csv"); $mitogo=count($lines); $i=$mitogo; $maxi=$i-1;

if ($mitogo>0) { $tblstyle="row1"; $printvote=null;

// Считываем СТАТИСТИКУ ВСЕХ УЧАСТНИКОВ
if (is_file("$datadir/userstat.csv")) {$ufile="$datadir/userstat.csv"; $ulines=file("$ufile"); $ui=count($ulines)-1;}

// Ищем тему в списке тем ХХХ.csv - проверяем не закрыта ли тема? и сразу же ищем есть ли в топике
// Заодно формируем № строки предыдущей и следующей темы
$ok=FALSE; $closed=FALSE; $lasttema=FALSE; $nexttema=FALSE;
if (is_file("$datadir/$fid.csv")) {
$msglines=file("$datadir/$fid.csv"); $mg=count($msglines); $mgmax=$mg-1;
do {$mg--; $mt=explode("|",$msglines[$mg]);
if ("$mt[2]$mt[3]"==$id) { $ok=1;
if ($mt[11]==FALSE) $closed=TRUE;
if ($mg>=1) $lasttema=$mg; // № строки предыдущей темы
if ($mg<$mgmax) $nexttema=$mg+1; // № строки следующей темы
$mg=0; }
} while($mg >"0");}

// Уточняем статус по кол-ву ЗВЁЗД юзера. Если меньше допустимых N в этой рубрике - то досвиданья!
$i=count($mainlines); $imax=$i; $maxzd=null; do {$imax--; $ddt=explode("|",$mainlines[$imax]); if ($ddt[2]==$fid) $maxzd=$ddt[9]; } while($imax>"0");

if ($maxzd>=1) {
if (!ctype_digit($maxzd)) exit("$back звёзды исчисляются в цифрах. а в файле данных - ерунда!");
$noacsess="<br><br><br><br><center><table class=forumline align=center width=700><tr><th class=thHead colspan=4 height=25>Доступ в раздел ограничен</th></tr>
<tr class=row2><td class=row1><center><BR><BR><B><span style='FONT-SIZE: 14px'>Для просмотра данного раздела необходимо быть зарегистрированным и иметь рейтинг не менее $maxzd звёзд.";

if ($ok==FALSE) exit("$back. Номер раздела указан ошибочно!<br> В указаном разделе нет такой темы!");
// БЛОК проверяет логин и пароль юзера, считывает кол-во его звёзд и сравнивает с заданным в рубрике
if (isset($_COOKIE['wrfcookies'])) { // весь блок работает при наличии КУКИ
$text=$_COOKIE['wrfcookies']; 
$text=replacer($text);
$wrfc=explode("|",$text); $wrfname=$wrfc[0]; if (isset($wrfc[1])) $wrfpass=$wrfc[1]; else exit("$back попытка взлома - в куки нет пароля. Куда он делся ;-) ?");
// пробегаем файл с юзерами и считываем его в память
$iu=$usercount; $ok=FALSE;
do {$iu--; $du=explode("|",$userlines[$iu]);
if (isset($du[1])) { $realname=strtolower($du[2]);
if (strtolower($wrfname)===$realname & $wrfpass===$du[3]) {$ok="$i"; if ($du[4]<$maxzd) exit("$noacsess У Вас всего $du[4] звёзд.</B></center><br><br></td></table><br>"); }}
} while($iu > "0");
} else exit("$noacsess</B></center><br><br></td></table><br>"); // если юзер тот, за кого себя выдаёт то его пускаем, иначе - обломаем
if ($ok==FALSE) exit("$noacsess</B></center><br><br><br></td></table><br>");
}



// Исключаем ошибку вызова несуществующей страницы
if (!isset($_GET['page'])) $page=1; else {$page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1;}

$fm=$msg_onpage*($page-1); if ($fm>$maxi) $fm=$maxi-$msg_onpage;
$lm=$fm+$msg_onpage; if ($lm>$maxi) $lm=$maxi+1; if ($maxi==1) {$fm=0; $lm=2;}

// формируем переменную $pageinfo - со СПИСКОМ СТРАНИЦ
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$msg_onpage); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div align=center style='padding:6px;' class=pgbutt>Страницы: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=index.php?id=$id>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=index.php?id=$id$addpage>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=index.php?id=$id&page=$maxpage>$maxpage</a>";
$pageinfo.='</div>';

$qm=null; $flag=0;
do {$dt=explode("|",replacer($lines[$fm]));

$youwr=null; $fm++; $num=$maxi-$fm+2; $status="";

if (mb_strlen($lines[$fm-1])>5) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим

if (isset($_GET['quotemsg'])) {
$quottime=date("d.m.y в H:i",$dt[4]);
$quotemsg=replacer($_GET['quotemsg']); if(ctype_digit($quotemsg) and $quotemsg==$fm) $qm="[Quote][b]$dt[8] $quottime пишет:[/b]\r\n".$dt[14]."[/Quote]";}

$msg=str_replace("[b]","<b>",$dt[14]);
$msg=str_replace("[/b]","</b>", $msg);
$msg=str_replace("[RB]","<font color=red><B>", $msg);
$msg=str_replace("[/RB]","</B></font>", $msg);
$msg=str_replace("&lt;br&gt;","<br>",$msg); // ЗАКОМЕНТИРОВАТЬ при ЧИСТОЙ установке скрипта
$msg=str_replace("[br]","<br>",$msg); // c 2015 г.
$msg=str_replace("[Quote]","<br><B><U><small>Цитата:</small></U></B><table width=95% cellpadding=5 cellspacing=1 style=\"margin-left:5px;padding:5px;\"><tr><td class=quote>",$msg); $msg=str_replace("[/Quote]","</td></tr></table>",$msg);
$msg=str_replace("[Code]","<br><B><U>Код:</U></B><table width=95% cellpadding=10 cellspacing=1 style=\"margin-left:5px;padding:5px;\"><tr><td class=code>",$msg); $msg=str_replace("[/Code]","</td></tr></table>",$msg);

// Новая конструкция в цикле. Протестировать! Пока что работает криво! 2016 г.
//$msg=preg_replace("/(\[Quote\])(.+?)(\[\/Quote\])/is","<br><UL><B><U><small>Цитата:</small></U></B><table width=95% cellpadding=5 cellspacing=1 style=\"margin-left:18px;padding:5px;\"><tr><td class=quote>$2</td></tr></table></UL>",$msg);
//$msg=preg_replace("/(\[Code\])(.+?)(\[\/Code\])/is","<br><UL><B><U>Код:</U></B><table width=95% cellpadding=10 cellspacing=1 style=\"margin-left:18px;padding:5px;\"><tr><td class=code>$2</td></tr></table></UL>",$msg);

if ($antimat==TRUE) { // АНТИМАТ
$pattern="/\w{0,5}[хx]([хx\s\!@#\$%\^&*+-\|\/]{0,6})[уy]([уy\s\!@#\$%\^&*+-\|\/]{0,6})[ёiлeеюийя]\w{0,7}|\w{0,6}[пp]([пp\s\!@#\$%\^&*+-\|\/]{0,6})[iие]([iие\s\!@#\$%\^&*+-\|\/]{0,6})[3зс]([3зс\s\!@#\$%\^&*+-\|\/]{0,6})[дd]\w{0,10}|[сcs][уy]([уy\!@#\$%\^&*+-\|\/]{0,6})[4чkк]\w{1,3}|\w{0,4}[bб]([bб\s\!@#\$%\^&*+-\|\/]{0,6})[lл]([lл\s\!@#\$%\^&*+-\|\/]{0,6})[yя]\w{0,10}|\w{0,8}[её][bб][лске@eыиаa][наи@йвл]\w{0,8}|\w{0,4}[еe]([еe\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[uу]([uу\s\!@#\$%\^&*+-\|\/]{0,6})[н4ч]\w{0,4}|\w{0,4}[еeё]([еeё\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[нn]([нn\s\!@#\$%\^&*+-\|\/]{0,6})[уy]\w{0,4}|\w{0,4}[еe]([еe\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[оoаa@]([оoаa@\s\!@#\$%\^&*+-\|\/]{0,6})[тnнt]\w{0,4}|\w{0,10}[ё]([ё\!@#\$%\^&*+-\|\/]{0,6})[б]\w{0,6}|\w{0,4}[pп]([pп\s\!@#\$%\^&*+-\|\/]{0,6})[иeеi]([иeеi\s\!@#\$%\^&*+-\|\/]{0,6})[дd]([дd\s\!@#\$%\^&*+-\|\/]{0,6})[oоаa@еeиi]([oоаa@еeиi\s\!@#\$%\^&*+-\|\/]{0,6})[рr]\w{0,12}/i";
$msg=preg_replace("$pattern","<b><font color='red'>Цензура</font></b>",$msg); }

if ($showsmiles==TRUE) { // СМАЙЛИКИ
$i=count($smiles)-1; for($k=0; $k<$i; $k=$k+2)
{$j=$k+1; $msg=str_replace("$smiles[$j]","<img src='smile/$smiles[$k].gif' border=0>",$msg);}}

// Если разрешена публикация УРЛов
if ($liteurl==TRUE) {$msg=' '.$msg; $msg=preg_replace ("/([^\[img\]])((http|http|ftp)+(s)?:(\/\/)([\w]+(.[\w]+))([\w\-\.,@?^=%&:;\/~\+#]*[\w\-\@?^=%&:;\/~\+#])?)/i", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $msg); $msg=ltrim($msg);}

// После замены АДРЕСА URL преобразуем код [img]
$msg=preg_replace('#\[img\](.+?)(jpg|gif|jpeg|png|bmp)\[/img\]#','<img src="$1$2" border="0">',$msg);
//$msg=preg_replace('#\[img(.*?)\](.+?)\[/img\]#','<img src="$1$2" border="0">',$msg); // если нужно показывать картинки для всего что в контейнере


// Вставляем видео с ЮТУБ
$msg=preg_replace("/(\[Youtube\])(.+?)(\[\/Youtube\])/is","<center><object width=480px height=360px><param name=movie value=\"http://www.youtube.com/v/$2\"></param>
<param name=allowFullScreen value=true></param><param name=allowscriptaccess value=always></param>
<embed src=\"http://www.youtube.com/v/$2\" type=\"application/x-shockwave-flash\" allowscriptaccess=always allowfullscreen=true width=480px height=360px></embed></object></center>",$msg);

// считываем в память данные по пользователю
if ($dt[6]==TRUE) { $iu=$usercount; $predup="0";
do {$iu--; $du=explode("|", $userlines[$iu]); if ($du[0]==$dt[7]) { 
$reiting=$du[4]; $youavatar=$du[15]; $email=$du[5]; $sset=$du[10]; $site=$du[11]; $userpn=$iu;

if (isset($_COOKIE['wrfcookies']) or $podpis_pokaz==TRUE) { $youwr=' '.$du[14];
if (mb_strlen($youwr)>10) {
$youwr=preg_replace('#\[img\](.+?)(jpg|gif|jpeg|png|bmp)\[/img\]#','<img src="$1$2" border="0">',$youwr);
$image=stristr($youwr, '<img src="');
$image=mb_substr($image,10);
$pos=strpos($image,'" border="0">');
$image=mb_substr($image,0,$pos);

if (@GetImageSize($image)==TRUE) { $size=GetImageSize($image); // $width=$size[0]; $height=$size[1];
if ($size[0]>350 or $size[1]>20) {
do {$size[0]=round($size[0]/2); $size[1]=round($size[1]/2);}
while ($size[0]>350 or $size[1]>20); }
$youwr=str_replace('border="0"',"border=\"0\" width=\"$size[0]\" height=\"$size[1]\"",$youwr);}
if (stristr($youwr,"<img")==FALSE) $youwr=preg_replace("#(\[url=([^\]]+)\](.*?)\[/url\])|(http://(www.)?[0-9a-z\.-]+\.[a-z]{2,6}[0-9a-z/\?=&\._-]*)#","<a href=\"$4\" target='_blank'>$4</a> ",$youwr);
$youwr=ltrim($youwr);} else $youwr=$du[14];
} //if (mb_strlen($youwr)>10)
} // if (isset($_COOK
} while($iu > "0");
}

if ($tblstyle=="row1") $tblstyle="row2"; else $tblstyle="row1";

if ($flag==FALSE) { // БЛОК ПЕЧАТАЕМ ОДИН РАЗ

// С 2019 ХЛЕБНЫЕ КРОШКИ:  Главная  »  Форум  »  РУБРИКА  »  СТРАНИЦА 1/2/3..Х
$frname=str_replace(' »','',$frname); $frtname=str_replace(' »','',$frtname); //вырезаем лишние символы
// $pageur1 - Ссылка на главную [1 уровень]
$pageur2=$forum_url; // Ссылка на все статьи [2 уровень]
$pageur3="index.php?id=$fid"; // Ссылка название подфорума
$pageur4="index.php?id=$id"; // Ссылка стр 1 [4 уровень]
// текст № страницы без ссылки [4 уровень]

$linkus="<div class=pgbutt style='padding:10px;margin:0px;'>
<div itemscope='' itemtype='http://schema.org/BreadcrumbList' id='breadcrumbs'>";
if (mb_strlen($pageur1)>5) $linkus.="
	   <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
       <a itemprop='item' title='Главная страница сайта' href='$pageur1'>
          <span itemprop='name'>Главная сайта</span>
          <meta itemprop='position' content='1'></a>
   </span>&nbsp; » &nbsp;";
$linkus.="   <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
       <a itemprop='item' title='$forum_name' href='$pageur2'>
          <span itemprop='name'>$forum_name</span>
          <meta itemprop='position' content='2'></a>
   </span>&nbsp; » &nbsp;
   <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
       <a itemprop='item' title='Темы в рубрике: $frname' href='$pageur3'>
          <span itemprop='name'>$frname</span>
          <meta itemprop='position' content='3'></a>
   </span>&nbsp; » &nbsp;
      <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
       <a itemprop='item' title='Текущая тема: $frtname' href='$pageur4'>
		  <span itemprop='name'><strong>$frtname</strong></span>
          <meta itemprop='position' content='4'></a>
   </span>&nbsp; » &nbsp;
      <span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
		  <span itemprop='name'>Страница $page</span>
          <meta itemprop='position' content='5'>
   </span>   
</div>
</div>";
$flag=TRUE; print"<br>$pageinfo $linkus <h1>$frtname</h1>";

// РЕКЛАМНЫЙ БЛОК
if ($reklama==1) print"<table class=forumline align=center width=100% cellspacing=1 cellpadding=2><tr><th class=thHead height=20>$reklamatitle</th></tr><tr><td class=row1><br>$reklamatext<br></td></table>";





// 01-2019 модификация. БЛОК ГОЛОСОВАНИЯ: если есть, то выводим
if (is_file("$datadir/$id-vote.csv")) {
$vlines=file("$datadir/$id-vote.csv");
if (sizeof($vlines)>0) {
$vitogo=count($vlines); $vi=1; $vdt=explode("|",$vlines[0]);

// проверяем IP юзера. Если он уже голосовал, то выводим результат
$ip=$_SERVER['REMOTE_ADDR']; $iplines=file_get_contents("$datadir/$id-ip.csv");

print"<BR><table class=forumline width=100% cellspacing=1 cellpadding=1><tr class=forumline><th colspan=5 class=thLeft width=11% height=26>
Голосование: <strong>$vdt[0]</strong></th></tr>";

if (strstr($iplines,$ip)==TRUE) { // ПРОСМОТР РЕЗУЛЬТАТА голосования

// Считаем общее кол-во голосов
$vitogo=count($vlines); $z=1; $glmax=0;
do {$vdt=explode("|",$vlines[$z]); $glmax=$glmax+$vdt[1]; $z++; } while($z<$vitogo); $z=1; $all=$glmax;
$vdt=explode("|",$vlines[0]);

do {$vdt=explode("|",$vlines[$z]);
if ($glmax==0) $glmax=0.1; $glpercent=round(100*$vdt[1]/$glmax); $hcg=round($glpercent);
if ($glpercent<2) $hcg=2; if ($glpercent>100) $hcg=100;
if (($z/2)==round($z/2)) echo'<TR height=25 class=row1>'; else echo'<TR height=25 class=row2>';
print"<TD width=25 align=center> &nbsp; </TD><TD><B>$vdt[0]</B></TD><TD><B>$vdt[1]</B></TD><TD>(<B>$glpercent</B> %)</TD>
<TD><TABLE cellSpacing=0 cellPadding=5 width=$hcg height=11><TR bgcolor=#FF8000><TD><img src='$forum_skin/spacer.gif' border=0></TD></TR></TABLE></TD></TR>";
$z++;} while($z<$vitogo);
print"</TR><TR><Th colspan=5 class=row2><I>Итого проголосовало: <B>$all</B></I></Th></TR></TD></TR></TABLE><br>";

} else { // выводим форму для голосования

print"<FORM name=wrvote action='vote.php?id=$id' method=POST target='WRGolos'><TR class=$tblstyle><TD class=$tblstyle>";
do {$vdt=explode("|",$vlines[$vi]);
print"<UL><INPUT name='votec' type=radio value='$vi'> &nbsp; <B>$vdt[0]</B></UL>";
$vi++; } while($vi<$vitogo);
print "<center><INPUT name='id' type=hidden value='$id'>
<noindex><INPUT type=submit value='проголосовать' onclick=\"window.open('vote.php?id=$id','WRGolos','width=650,height=400,left=200,top=200,toolbar=0,status=0,border=0,scrollbars=1')\" border=0>
&nbsp; &nbsp; <A href='#' rel='nofollow' onclick=\"window.open('vote.php?rezultat&id=$id','WRGolos','width=650,height=400,left=200,top=200,toolbar=0,status=0,border=0,scrollbars=1')\">Результаты</A></noindex></center></FORM>
</TD></TR></TABLE><BR>"; }}} // КОНЕЦ БЛОКА ГОЛОСОВАНИЯ





print"<table class=forumline width=100% cellspacing=1 cellpadding=3><tr><th class=thLeft width=165 height=26 nowrap=nowrap>Автор</th><th class=thRight>Сообщение</th>";
} // if $flag==FALSE)

print"</tr><tr height=150><td class=$tblstyle valign=top><span class=name><BR><center>";

// Проверяем: это гость?

if (!isset($youwr)) {if (mb_strlen($dt[9])>5) print"$dt[8] "; else print"$dt[8] "; $kuda=$fm-1; print" <a href='javascript:%20x()' name='m$fm' onclick=\"DoSmilie('[b]$dt[8][/b], ');\" class=nav>&bull;</a><BR><br>";

// 01-2019 если нет аватара добавляем чудо через стиль css. если border-radius 100% - это круг
if ($dt[6]==TRUE) $print_name="Участник форума"; else $print_name=$guest_name;
$pb=mb_substr("$dt[8]",0,1,"utf8");
$pb=mb_convert_case($pb, MB_CASE_UPPER, "utf8");
print"<div class=guestavatar>$pb</div><small>$print_name</small><BR>"; 

if (mb_strlen($dt[9])>5) // если емайл указан, печатаем форму для отправки ЛС
print"
<form method='post' action='tools.php?event=mailto' target='email' onclick=\"window.open('tools.php?event=mailto','email','width=700,height=400,left=100,top=100');return true;\">
<input type=hidden name='email' value='$dt[9]'><input type=hidden name='name' value='$dt[8]'><input type=hidden name='id' value='$id'>
<input type=image src='$forum_skin/ico_pm.gif' alt='личное сообщение'></form>";


} else {


if (isset($youwr) and is_file("$datadir/userstat.csv")) { // ТОЛЬКО участники видят всю репутацию! ;-)
if (isset($ulines[$userpn])) {
if (mb_strlen($ulines[$userpn])>5) {
$ddu=explode("|",replacer($ulines[$userpn]));
$winop="window.open('tools.php?event=repa&name=$dt[8]&who=$userpn','repa','width=600,height=600,left=50,top=50,scrollbars=yes')";

if (mb_strlen($ddu[9])>1) $status=$ddu[9];
$si=0; $repuser=$ddu[7]; // Репутация пользователя
for ($si=0;$si<7;$si++) if ($repuser>=$userrepa[$si]) $stp=$si;
$si=$stp+1;

// БЛОК РЕПУТАЦИИ
if ($repuser>$userrepa[$si]) {$title="Набрано масксимальное количество рейтинга. Репутация более ".$userrepa[7]." единиц! Ваш статус: ".$userstatus[7]."! Поздравляем!!!"; $statpro=100;
} else {
$statd=$userrepa[$si]-$userrepa[$si-1];
$statt=$repuser-$userrepa[$si-1];
$statpro=intval(($statt*100)/$statd); if ($statpro=="0") $statpro=1;
$tekstatus=$userstatus[$si-1]; if (mb_strlen($status)<1) $status=$tekstatus;
$nextstatus=$userstatus[$si];

if (!stristr($status,"Администратор",false) and !stristr($status,"модератор",false)) $title="В текущем статусе &quot;$tekstatus&quot; набрано $statpro% рейтинга. Смена статуса на &quot;$nextstatus&quot; произойдет при репутации $userrepa[$si]";
else $title="$dt[8], статус администратор не зависит от репутации. В текущем диапазоне набрано $statpro% рейтинга";
} // repa
// КОНЕЦ БЛОКА



$codename=urlencode($dt[8]);
if (!isset($wrfname)) print"$dt[8]"; else print"<a name='m$fm' href='tools.php?event=profile&pname=$codename' rel='nofollow' class=nav>$dt[8]</a>";
print" <a href='javascript:%20x()' name='m$fm' onclick=\"DoSmilie('[b]$dt[8][/b], ');\" class=nav>&bull;</a><BR><BR><small>";
if (mb_strlen($status)>2 & $dt[6]==TRUE & isset($youwr)) print"$status"; else print"$user_name";
if (isset($reiting)) {if ($reiting>0) {echo'<BR>'; if (is_file("$forum_skin/star.gif")) {for ($ri=0;$ri<$reiting;$ri++) {print"<img src='$forum_skin/star.gif' title='Репутация в звёздах - $reiting шт.' border=0> ";} } }}

if (isset($youavatar)) { if (is_file("avatars/$youavatar")) $avpr="$youavatar"; else $avpr="noavatar.gif";
print "<BR><BR><img src='avatars/$avpr'><BR>
<details><summary>Данные профиля</summary>
<form action='tools.php?event=profile&pname=$dt[8]' method=POST><input type=submit class=mainoption value='П' title='Посмотреть профиль'></form>
<form action='pm.php?id=$dt[8]' method=POST name=citata><input type=submit class=mainoption value='ПС' title='Отправить ПЕРСОНАЛЬНОЕ СООБЩЕНИЕ'></form>
<form action='tools.php?event=mailto' method=POST target='email' onclick=\"window.open('tools.php?event=mailto','email','width=600,height=350,left=100,top=100');return true;\">
<input type=hidden name='email' value='$dt[9]'><input type=hidden name='name' value='$dt[8]'><input type=hidden name='id' value='$id'>
<input type=submit class=mainoption value='ЛС' title='Отправить личное сообщение на емайл'></form><BR>
<form action='$site' method=GET name=citata><input type=submit class=mainoption value='www' title='Перейти на сайт участника'></form>
<form action='$sset' method=GET name=citata><input type=submit class=mainoption value='Соцсеть' title='Перейти на страничку в Соцсети'></form>
</details>";}
} // isset($youwr)


// Статистика пользователя
print"</small></span>
<noindex><br><br><fieldset STYLE='color:#646464'>
<legend STYLE='font-weight:bold;'>Статистика:</legend>
<div style='PADDING:2px;' align=left class=gensmall>Тем создано: $ddu[5]<br>Сообщений: $ddu[6]<br>Репутация: $ddu[7] <A href='#m$fm' style='text-decoration:none' onclick=\"$winop\">&#177;</A><br>
<table align=left width=80% cellpadding=0 cellspacing=1 style='border-width:1;border-style:solid;border-color:#bbbbbb;cursor:help' title='$title'>
<tr><td width=\"$statpro%\" height=5 style='background-color:green;'></td><td></td></tr></table>
Нарушения: <a href=\"#\" onclick=\"window.open('tools.php?event=ban&znach=$ddu[8]','email','width=600,height=350,left=100,top=100');return true;\">$ddu[8]</a></div></fieldset>
</noindex>"; }}}
// Конец блока "статистика пользователя"


print "</td><td class=$tblstyle width=100% height=28 valign=top>
<table width=100% height=100%><tr valign=center><td><span class=postbody><P>$msg</P></span></td></tr><TR><TD>";


// Если ПРИКРЕПЛЁН ФАЙЛ к сообщению - то показываем значёк и ссылку на него или картинку изм. 12-2019
if (mb_strlen($dt[13])>2) { if (is_file("$filedir/$dt[13]")) {
$fsize=filesize("$filedir/$dt[13]"); $fsize=round($fsize/1048576,1); if ($fsize=="0") $fsize="0.1";
echo'<fieldset style="color:#008000"><legend>Прикреплён файл:</legend>';
if (preg_match("/.(jpg|jpeg|bmp|gif|png)+$/is",$dt[13]))
{ // Проверяем "габариты" фото и если одна из сторон больше допустимых - то меняем атрибуты
$size=getimagesize("$filedir/$dt[13]");
do {$size[0]=$size[0]/2; $size[1]=$size[1]/2;
} while ($size[0]>400 or $size[1]>400);
print"<img border=0 src='$filedir/$dt[13]' width=$size[0] height=$size[1]><br><a href='$filedir/$dt[13]' target=_blank>Посмотреть полноразмерное изображение</a>"; 
} else print"<img border=0 src='$forum_skin/ico_file.gif'> <a href='$filedir/$dt[13]'>$dt[13]</a> ($fsize Мб.)</fieldset>"; }}


// печатаем подпись участника
if (isset($youwr)) { if (mb_strlen($youwr)>3) print "<tr><td valign=bottom><span class=postbody>--------------------------------------------------<BR><small>$youwr</small>";}

// Если имя юзер участник и имя юзера=имени в сообщении и сообщение последнее, то вывести кнопку удаления
$codename=urlencode($dt[8]);
if (isset($wrfname)) { if ($fm==($maxi+1) and $maxi!="0" and strtolower($dt[8])==strtolower($wrfname)) print"<table><TR><td bgcolor=#FF2244><B><a href='tools.php?event=deletemsg&id=$id&username=$codename' rel='nofollow' title='УДАЛИТЬ' onclick=\"return confirm('Будет удалёно Ваше последнее сообщение! Удалить? Уверены?')\" >.УДАЛИТЬ СООБЩЕНИЕ.</a></B></td></tr></table>";}

if (date("d.m.Y",$dt[4])==$date)  $dt[4]="сегодня в ".date("H:i",$dt[4]); else $dt[4]=date("d.m.y - H:i:s",$dt[4]);

$addpage=""; if ($page>1) $addpage="&page=$page"; // нужно для цитирования
print"</table></td></tr><tr>
<td class=row3 valign=middle><span class=postdetails><I>Сообщение <a href=\"#m$fm\" title='Ссылка на сообщение' onClick=\"prompt('Ссылка на сообщение № $fm','$forum_url/index.php?id=$id$addpage#m$fm')\"># <B>$fm</B></a></I></span></td>

<td class=row3 width=100% height=28 align=right><span class=postdetails><b>$dt[4]
<form action='index.php?id=$id$addpage&quotemsg=$fm#add' method=POST name=citata><input type=submit class=mainoption value='Цитировать'></form></span></td></tr>";

print"<tr><td class=spaceRow colspan=2 height=1><img src=\"$forum_skin/spacer.gif\" width=1 height=1></td>";

} // если строчка потерялась
} while($fm < $lm);


// Предыдущая и следующая тема
if ($lasttema!=FALSE) {$lasttema--; $ldt=explode("|",$msglines[$lasttema]); $lasttema="<TD align=left>&#9668; <B><a href='index.php?id=$ldt[2]$ldt[3]'>$ldt[5]</a></B> :Предыдущая тема</TD>";} else $lasttema="";
if ($nexttema!=FALSE) {$ndt=explode("|",$msglines[$nexttema]); $nexttema="<TD align=right>Следующая тема: <B><a href='index.php?id=$ndt[2]$ndt[3]'>$ndt[5]</a></B> &#9658;";} else $nexttema="<TD>";

print"</tr></table> $linkus <table cellSpacing=0 cellPadding=0 width=100%><TR height=25>$lasttema$nexttema</TD></tr></table> $pageinfo<br>";

if ($g_add_msg==FALSE and !isset($wrfname)) print"<center>Администратор запретил отвечать гостям на сообщения! Для регистрации пройдите по ссылке: <B><a href='tools.php?event=reg' rel='nofollow'>зарегистрироваться</a></B></center><BR><BR>"; else {
if ($closed==FALSE) {

if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc,ENT_COMPAT,"utf-8"); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc); $wrfpass=replacer($wrfc[1]);} else {unset($wrfpass); $wrfpass="";}

if ($forum_lock==FALSE) {
print "
<form action=\"index.php?add_msg&id=$id\" method=post name=REPLIER enctype=\"multipart/form-data\">
<input type=hidden name=userpass value=\"$wrfpass\">
<input type=hidden name=page value='$page'>
<input type=hidden name=maxzd value='$maxzd'>
<table cellpadding=3 cellspacing=1 width=100% class=forumline>
<tr><th class=thHead colspan=2 height=25><b>Сообщение</b></th></tr>";

addmsg($qm);
} else echo'<center>Извините за неудобство, но администратор временно приостановил добавление тем и сообщений!';
} else echo'<center><BR><h3>Тема закрыта для обсуждения!</h3><BR><BR>';
}}
}
} // if isset($id)

?>
</td></tr></table>
<center><font size=-2><small>Powered by <a href="http://www.wr-script.ru" title="скрипт php форума" class="copyright">WR-Forum</a> Professional &copy; 2.3 UTF-8 beta версия</small></font></center>
</body>
</html>