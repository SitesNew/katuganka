<? // WR-forum mini v 2.2 UTF-8 //  08.02.2019 г.  //  WR-Script.ru

error_reporting (E_ALL); //error_reporting(0);
ini_set('register_globals','off');// Все скрипты написаны для этой настройки php

include "data/config.php";

// wrfminicookies - мини куки для мини форума

// оставить только:
/*
- поиск
- простая регистрация  - запомнить логин и емайл - как loginza в галлере!
- отправка сообщения на емайл - тет перемешано с регистрированными - почистить!

*/









// Определяем URL форума 11-11-2018 поддержка http / http
$url="http".(($_SERVER['SERVER_PORT']==443)?"s":"")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; $fu=explode('tools.php', $url);$forum_url=$fu[0];

// Функция содержит ПРОДОЛЖЕНИЕ ШАПКИ. Вызывается: addtop();
function addtop() { global $wrfname,$forum_skin,$date,$time;
$wrfname=null; $wrfpass=null;
echo'<TD align=right>';
if (is_file("$forum_skin/tiptop.html")) include("$forum_skin/tiptop.html"); // подключаем дополнение к ВЕРХУШКе

print"</span></td></tr></table></td></tr></table><span class=gensmall>Сегодня: $date - $time";
return true;}


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


function unreplacer($text) { // ФУНКЦИЯ замены спецсимволов конца строки на обычные
$text=str_replace("&lt;br&gt;","<br>",$text); return $text;}


function nospam() { global $max_key,$rand_key,$antispam2012,$antispam2012v; // Функция АНТИСПАМ 2011+2012
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
echo'<noindex><table cellspacing=0 cellpadding=0><tr height=30><TD>Защитный код:</TD>';
$nummax=0; for ($i=0; $i<=$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
$secret=mt_rand(0,1); $styles='bgcolor=#FFFF00';
if ($nummax<3) { if ($secret==1 or $i==0) {$styles='bgcolor=#77C9FF'; $xkey=$xkey.$snum[$i]; $nummax++;}}
echo "<td width=20 $styles><img src=antispam.php?image=$psnum border=0 alt=''>\n<img src=antispam.php?image=$psnum height=1 width=1 border=0></td>\r\n";}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из data/config.php + код меняющийся кажые 24 часа
print"<td><input name='usernum' class=post type='text' maxlength=$nummax size=6> (введите цифры, которые на <font style='font-weight:bold'> голубом фоне</font>)
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>
</td></tr></table></noindex>";
if ($antispam2012==TRUE) print"Ответ на вопрос: <input name='antispam2012o' class=post type='text' maxlength=20 size=10>($antispam2012v)";
return; }









// ВСЁ, что делается при наличии переменной $_GET['event']
if(isset($_GET['event'])) {













// БЛОК ЛОГИНЗА - сохранение логина,емайла и пароля антиспама для быстрой работы с фотоальбомом
if ($_GET['event']=="loginza") {


if (isset($_GET['save'])) {
if (isset($_POST['name'])) $name=replacer($_POST['name']); else exit();
if (isset($_POST['email'])) $email=replacer($_POST['email']); else exit();
if (isset($_POST['usernum'])) $usernum=replacer($_POST['usernum']); else exit();
setcookie("wrfminicookies", "$name|$email|$usernum|", time()+1728000);
Header("Location: index.php"); exit; }

$frname=""; $frtname=""; include("$forum_skin/top.html"); addtop(); // подключаем ШАПКУ форума

print"<center><h1>Логинимся</h1>
<table border=0 width=450 align=center><form action='tools.php?event=loginza&save' method=POST>
<tr><td class=row1 width=100 height=25><span class=gen><b>Имя</b></span></td><td class=row1><input type=text name=name maxlength=$maxname size=35></td></tr>
<tr><td class=row2 height=25><span class=gen>E-mail</span></td><td class=row2><input type=text name=email size=35></td></tr> 
<tr><td class=row2 height=25><span class=gen><B>Ответьте на вопрос:</B></span></td><td class=row2><input name='usernum' type='text' maxlength=20 size=25> ($antispam2012v)</td></tr>
</tr><tr><td class=row1 colspan=4 align=center height=28><input type=submit class=post value='  Сохранить данные  '></td></form>";
exit;}




// ОТПРАВКА СООБЩЕНИЯ юзеру
if ($_GET['event']=="mailto") {

if ($sendmail!=TRUE) exit("$back. <center><B>Извините, но функция отправки писем ЗАБЛОКИРОВАНА администратором!<BR><BR><BR><a href='' onClick='self.close()'>Закрыть окно</b></a></center>");

if (!isset($_POST['email'])) exit("Нет данных переменной email.");
if (!isset($_POST['name'])) exit("Нет данных переменной name.");
$uemail=replacer($_POST['email']); $uname=replacer($_POST['name']);
$id=""; $fid=""; if (isset($_POST['id'])) {$id=replacer($_POST['id']); if (strlen($id)>0) $fid=substr($id,0,3);}

print "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><meta http-equiv='Content-Language' content='ru'>
<meta name=\"Robots\" content=\"noindex,nofollow\">
<title>Отправление сообщения автору объявления</title></head><body topMargin=5>
<center><TABLE bgColor=#aaaaaa cellPadding=2 cellSpacing=1 width=98%>
<FORM action='tools.php?event=mailtogo' method=post>
<TBODY><TR><TD align=middle bgColor=#cccccc colSpan=2>Получатель сообщения: <B>$uname</B></TD></TR>

<TR bgColor=#ffffff><TD>&nbsp; Ваше Имя:<FONT color=#ff0000>*</FONT> <INPUT name=name style='FONT-SIZE: 14px; WIDTH: 150px'>

и E-mail:<FONT color=#ff0000>*</FONT> <INPUT name=email style='FONT-SIZE: 14px; WIDTH: 180px'></TD></TR>

<TR bgColor=#ffffff><TD>&nbsp; Сообщение:<FONT color=#ff0000>*</FONT><br>
<TEXTAREA name=msg style='FONT-SIZE: 14px; HEIGHT: 150px; WIDTH: 494px'></TEXTAREA></TD></TR>
<INPUT type=hidden name=uemail value=$uemail><INPUT type=hidden name=uname value=$uname>
<TR bgColor=#ffffff><TD>";

if ($antispam==TRUE and !isset($wrfname)) nospam(); // АНТИСПАМ !

if ($id!="") print"<INPUT type=hidden name=id value=$id><INPUT type=hidden name=fid value=$fid>";

echo'<TR><TD bgColor=#FFFFFF colspan=2><center><INPUT type=submit value=Отправить></TD></TR></TBODY></TABLE></FORM>'; 
exit; }


// ШАГ 2 отправки сообщения пользователю
if ($_GET['event']=="mailtogo") {
$name=replacer($_POST['name']);
$email=replacer($_POST['email']);
$msg=replacer($_POST['msg']);
if (isset($_POST['fid'])) $fid=replacer($_POST['fid']);
if (isset($_POST['id'])) $id=replacer($_POST['id']);
$uname=replacer($_POST['uname']);
$uemail=replacer($_POST['uemail']);

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("данные из формы не поступили!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("введён ОШИБОЧНЫЙ код!");}

if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$email) and strlen($email)>30 and $email!="") exit("$back и введите корректный E-mail адрес!</B></center>");
if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$uemail) and strlen($uemail)>30 and $uemail!="") exit("$back у пользователя задан несуществующий адрес!</B></center>");
if ($name=="") exit("$back Вы не ввели своё имя!</B></center>");
if ($msg=="") exit("$back Вы не ввели сообщение!</B></center>");

$text="$name|$msg|$uname|$email|";
$text=str_replace("\r\n","<br>",$text);
$exd=explode("|",$text); $name=$exd[0]; $msg=$exd[1]; $uname=$exd[2]; $email=$exd[3];

$headers=null; // Настройки для отправки писем
$headers.="From: $name $email\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=UTF-8";

// Собираем всю информацию в теле письма

$allmsg="<html><head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><meta http-equiv='Content-Language' content='ru'>
</head><body>
<BR><BR><center>$uname, это сообщение отправлено вам от посетителя форума <BR><B>$forum_name</B><BR><BR>
<table cellspacing=0 width=700 bgcolor=navy><tr><td><table cellpadding=6 cellspacing=1 width='100%'>
<tr bgcolor=#F7F7F7><td width=130 height=24>Имя</td><td>$name</td></tr>
<tr bgcolor=#F7F7F7><td>E-mail:</td><td><font size='-1'>$email</td></tr>
<tr bgcolor=#F7F7F7><td> Сообщение:</td><td><BR>$msg<BR></td></tr>
<tr bgcolor=#F7F7F7><td>Дата отправки сообщения:</td><td>$time - <B>$date г.</B></td></tr>
<tr bgcolor=#F7F7F7><td>Перейти на главную страницу:</td><td><a href='$forum_url'>$forum_url</a></td></tr>
</table></td></tr></table></center><BR><BR>* Данное письмо сгенерировано и отправлено роботом, отвечать на него не нужно.
</body></html>";

mail("$uemail", "Сообщение от посетителя форума ($forum_name) от $name ", $allmsg, $headers);
exit('<div align=center><BR><BR><BR>Ваше сообщение <B>успешно</B> отправлено.<BR><BR><BR><a href="#" onClick="self.close()"><b>Закрыть окно</b></a></div>'); }




// проверка имени/пароля и вход на форум
if ($_GET['event']=="regenter") {
if (!isset($_POST['name']) & !isset($_POST['pass'])) exit("$back введите имя и пароль!");
$name=str_replace("|","I",$_POST['name']); $pass=replacer($_POST['pass']);
$name=replacer($name); $name=strtolower($name);
if (strlen($name)<1 or strlen($pass)<1) exit("$back Вы не ввели имя или пароль!");

// проходим по всем пользователям и сверяем данные
$lines=file("$datadir/user.php"); $i=count($lines); $regenter=FALSE;
$pass=md5("$pass");
do {$i--; $rdt=explode("|",$lines[$i]);
if (isset($rdt[1])) { // Если строчка НЕ ПУСТА
if ($name===strtolower($rdt[2]) & $pass===$rdt[3]) {
if ($rdt[16]==FALSE) exit("$back. Ваша учётная запись не <a href='tools.php?event=reg3'>активирована</a>. Для активации Вам необходимо перейти по ссылке, которая должна прийти Вам на емайл.");
$regenter=TRUE;
$tektime=time();
$wrfcookies="$rdt[2]|$rdt[3]|$tektime|$tektime|";
setcookie("wrfcookies", $wrfcookies, time()+1728000);
}} // if-ы

} while($i > "1");

if ($regenter==FALSE) exit("$back Ваш данные <B>НЕ верны</B>!</center>");
Header("Location: index.php");
}



if ($_GET['event']=="moresmiles") { // ПОКАЗАТЬ ВСЕ смайлы из директории SMILE
$lines=null; unset($lines); if (!is_dir("smile/")) exit("папка smile не существует.");
$i=0; if ($handle=opendir("smile/")) {while (($file=readdir($handle)) !== false) if (!is_dir($file)) {$lines[$i]=$file; $i++;} closedir($handle);}
if (!isset($lines)) exit("В папке smile НЕТ смайлов! Обратитесь к админу - пусть скинет.");
$itogo=count($lines); $k=0; $text=null;
print"<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'></head><body>
<script language=\"JavaScript\">function setSmile(symbol) { obj=opener.document.REPLIER.msg; obj.value=obj.value + symbol; }</script>
<center><b>Дополнительные смайлы</b><br>";
do {
$rdt=explode(".",$lines[$k]);
if ($rdt[1]=="jpg" or $rdt[1]=="gif") {print"<a href=\"javascript:setSmile('[img]$forum_url/smile/$lines[$k][/img] ')\"><img src='smile/$lines[$k]' border=0></a>&nbsp; ";}
$k++;
} while ($k<$itogo);
exit("<BR><a href='' onClick='self.close()'><b>Закрыть окно</b></a></center><small>P.S. Администратор! Чтобы здесь появились новые смайлы - просто закачай любые смайлы в папку".$forum_url."smile/</small></body></html>");}




// ----- Шапка для всех страниц форума

if (isset($_COOKIE['wrfcookies'])) {
$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc,ENT_COMPAT,"UTF-8"); $wrfc=stripslashes($wrfc);
$wrfc=explode("|", $wrfc);
$wrfname=$wrfc[0];$wrfpass=$wrfc[1];$wrftime1=$wrfc[2];$wrftime2=$wrfc[3];
if (time()>($wrftime1+50)) {$tektime=time();$wrfcookies="$wrfc[0]|$wrfc[1]|$tektime|$wrftime1";setcookie("wrfcookies", $wrfcookies, time()+1728000);}}
 else {unset($wrfname); unset($wrfpass);}

// -----

$frname=""; $frtname=""; include("$forum_skin/top.html"); addtop(); // подключаем ШАПКУ форума









if ($_GET['event']=="find") { // ПОИСК
$minfindme="3"; //минимальное кол-во символов в слове для поиска
echo'<BR><form action="tools.php?event=go&find" method=POST>
<center><table class=forumline align=center width=700>
<tr><th class=thHead colspan=4 height=25>Поиск</th></tr>
<tr class=row2>
<td class=row1>Запрос: <input type="text" style="width: 250px" class=post name=findme size=30></TD>
<TD class=row1>Тип: <select style="FONT-SIZE: 12px; WIDTH: 120px" name=ftype>
<option value="0">&quotИ&quot
<option value="1" selected>&quotИЛИ&quot
<option value="2">Вся фраза целиком
</select></td>
<td class=row1><INPUT type=checkbox name=withregistr><B>С учётом РЕГИСТРА</B></TD>
<input type=hidden name=gdefinder value="1">
</tr>

<input type=hidden name=user value="0">

<tr class=row1>
<td class=row1 colspan=4 width="100%">
Язык запросов:<br><UL>
<LI><B>&quotИ&quot</B> - должны присутствовать оба слова;</LI><br>
<LI><B>&quotИЛИ&quot</B> - есть ХОТЯ БЫ одно из слов;</LI><br>
<LI><B>&quotВся фраза целиком&quot</B> - в искомом документе ищите фразу на 100% соответствующую вашему запросу;</LI><BR><BR>
<LI><B>&quotС учётом РЕГИСТРА&quot</B> - поиск ведётся с учётом введённого ВАМИ РЕГИСТРА;</LI><BR><BR>
</UL>Скрипт ищет все данные, которые начинаются с введенной вами строки. Например, при запросе &quotфорум&quot будут найдены слова &quotфорум&quot, &quotфорумы&quot, &quotфорумом&quot и многие другие.
</td>
</tr><tr><td class=row1 colspan=4 align=center height=28><input type=submit class=post value="  Поиск  "></td></form>
</tr></table><BR><BR>';

print "Ограничение на поиск: <BR> - минимальное кол-во символов: <B>$minfindme</B>";
}





if (isset($_GET['find'])) {

//exit("Поиск временно не работает!");
$minfindme="2"; //минимальное кол-во символов в слове для поиска
$time=explode(' ', microtime()); $start_time=$time[1]+$time[0]; // считываем начальное время запуска поиска

$gdefinder="1"; $ftype=$_POST['ftype']; 
if (!ctype_digit($ftype) or strlen($ftype)>2) exit("<B>$back. Попытка взлома. Хакерам здесь не место.</B>");
if (!isset($_POST['withregistr'])) $withregistr="0"; else $withregistr="1";

if ($_POST['user']!="0") {$findme=$_POST['user']; $gdefinder="3"; $ftype="2"; $withregistr="1";} //  Если выбран поиск по имени юзера
else $findme=$_POST['findme']; 

$findme=replacer($findme); // Защита от взлома
$findmeword=explode(" ",$findme); // Разбиваем $findme на слова
$wordsitogo=count($findmeword);
$findme=trim($findme); // Вырезает ПРОБЕЛьные символы 
if ($findme == "" || strlen($findme) < $minfindme) exit("$back Ваш запрос пуст, или менее $minfindme символов!</B>");

// Открываем файл с темами формума и запоминаем имена файлов с сообщениями

setlocale(LC_ALL,'ru_RU.UTF-8'); // ! РАЗРЕШАЕМ РАБОТУ ФУНКЦИЙ, работающих с регистором и с РУССКИМИ БУКВАМИ


// ПЕРВЫЙ цикл - считаем кол-во форумов (записываем в переменную $itogofid)
$mainlines=file("$datadir/wrforum.dat");$i=count($mainlines); $itogofid="0";$number="0"; $oldid="0"; $nump="0";
do {$i--; $dt=explode("|",$mainlines[$i]);
if ($dt[3]==FALSE) { $maxzd=$dt[9];
if (!ctype_digit($maxzd)) $maxzd=0;  // считываем ЗВЁЗДы раздела из файла
if ($maxzd<1) {$itogofid++; $fids[$itogofid]=$dt[2]; }} // $itogofid - общее кол-во форумов
} while($i > "0");


// ВТОРОЙ цикл - открываем файл с топиком (если он существует) и сохраняем в переменную $topicsid все имена тем
do { $fid=$fids[$itogofid];
if (is_file("$datadir/$fid.dat")) {
$msglines=file("$datadir/$fid.dat");

unset($topicsid); if (count($msglines)>0) { $lines=file("$datadir/$fid.dat"); $i=count($lines);
do {$i--; $dt=explode("|",$lines[$i]); $topicsid[$i]="$dt[2]$dt[3]";} while($i > "0"); }


// ТРЕТИЙ цикл - последовательно открываем каждую тему
if (isset($topicsid)) {
$ii=count($topicsid);
do {$ii--;
$id=str_replace("\r\n","",$topicsid[$ii]);

if (is_file("$datadir/$id.dat")) { // Если файл есть? Бывает, что файлы с сообщениями бьются, тогда при пересчёте они удаляются.
$file=file("$datadir/$id.dat"); $iii=count($file);

// ЧЕТВЁРТЫЙ цикл - последовательно ищем в каждой теме искомое сообщение
if ($iii>0) { // если файл с сообщениями НЕ ПУСТОЙ
do {$iii--; 
$lines=file("$datadir/$id.dat");
$dt=explode("|", $lines[$iii]); if (!isset($dt[4])) $dt[4]=" ";

if ($gdefinder=="0") {$msgmass=array($dt[2],$dt[3],$dt[4]); $gi="3"; $add="ях <B>Автор, Текст, Заголовок</B> ";}
if ($gdefinder=="1") {$msgmass=array($dt[14]); $gi="1"; $add="е <strong>Текст</strong> ";}
if ($gdefinder=="2") {$msgmass=array($dt[3],$dt[4]); $gi="2"; $add="ях <B>Текст и Заголовок</B> ";}
if ($gdefinder=="3") {$msgmass=array($dt[8]); $gi="1"; $add="е <B>Автор</B> ";}
if ($gdefinder=="4") {$msgmass=array($dt[3]); $gi="1"; $add="е <B>Заголовок</B> ";}

// Цикл по местам поиска (0,1,2,3,4)
do {$gi--;

$msg=$dt[14];
$msdat=$msgmass[$gi];
$stroka="0"; $wi=$wordsitogo;

// ЦИКЛ по КАЖДОМУ слову запроса !
do {$wi--;


// БЛОК УСЛОВИЙ ПОИСКА
if ($withregistr!="1") // регистронезависимый поиск - cимвол "i" после закрывающего ограничителя шаблона - /
   {
    if ($ftype=="2") 
        { if (stristr($msdat,$findme)) // ПОИСК по "ВСЕЙ ФРАЗЕ ЦЕЛИКОМ" БЕЗ учёта регистра
          { $stroka++; $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg); }
        } else {
           $str1=strtolower($msdat);  
           $str2=strtolower($findmeword[$wi]); 
           if ($str2!="" and strlen($str2) >= $minfindme)
              { if (stristr($str1,$str2)) // ПОИСК БЕЗ учёта регистра при равных прочих условиях
                { $stroka++; $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg); }
              }
          }
        }

else  // if ($withregistr!="1")
   {
    if ($ftype=="2")
       {
        if (strstr($msdat,$findme)) // ПОИСК по "ВСЕЙ ФРАЗЕ ЦЕЛИКОМ" C учёта РЕГИСТРА
           {
            $stroka++;
            $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg);
           }
       }
     else {
           if ($msdat!="" and strlen($findmeword[$wi]) >= $minfindme)
              {
               if (strstr($msdat,$findmeword[$wi])) // ПОИСК С учётом РЕГИСТРА при равных прочих условиях
                  {
                   $stroka++;
                   $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg);
                  }
              }
          }

   }   // if ($withregistr!="1")

} while($wi > "0"); // конец ЦИКЛа по КАЖДОМУ слову запроса


// Подготавливаем результирующее сообщение, и если результат соответствует условиям - выводим его
if ($ftype=="0") { if ($stroka==$wordsitogo) $printflag="1"; }
if ($ftype=="1") { if ($stroka>"0") $printflag="1"; }
if ($ftype=="2") { if ($stroka==$wordsitogo) $printflag="1"; }

if (!isset($printflag)) $printflag="0";
    if ($printflag=="1")
       { $msg=str_replace("<br>", " &nbsp;&nbsp;", $msg); // заменяем в сообщении <br> на пару пробелов

if (strlen($msg)>150)
{
 $ma=strpos($msg,"<b>"); if ($ma > 50) $ma=$ma-50; else $ma=0;
 $mb=strrpos($msg,">b/<"); if (($mb+50) > strlen($msg)) $mb=strlen($msg); else $mb=$mb+50;
 $msgtowrite="..."; $msgtowrite.=substr($msg,$ma,$mb); $msgtowrite.="...";
 $msgtowrite=substr($msg,0,400);
}
else $msgtowrite=$msg;




if (!isset($m)) {
print"
<small><BR>По запросу '<U><B>$findme</B></U>' в пол$add найдено: <HR size=+2 width=99% color=navy>
<BR><form action='tools.php?event=go&find' method=POST>
<table class=forumline align=center width=700>
<tr><th class=thHead colspan=4 height=25>Повторить поиск по сообщению</th></tr>
<tr class=row2>
<td class=row1>Запрос: <input type='text' value='$findme' style='width: 250px' class=post name=findme size=30>
<INPUT type=hidden value='1' name=ftype>
<INPUT type=hidden value='0' name=user>
<input type=hidden name=gdefinder value='1'>
<input type=submit class=post value='  Поиск  '></td></table></form><br>
<table width=100% class=forumline><TR align=center class=small><TH class=thCornerL><B>№</B></TH><TH class=thCornerL width=35%><B>Заголовок</B></TH><TH class=thCornerL width=70%><B>часть сообщения</B></TH><TH class=thCornerL><B>Совпадений<BR> в теме</B></TH></TR>"; $m="1"; }

$in=$iii+1; if ($in>$msg_onpage) {$page=ceil($in/$msg_onpage);} else $page="1"; // расчитываем верную страницу и номер сообщения

if ($oldid!=$id and $number<100) { $number++; $msgnumber=$iii;

if ($nump>1) $anp="$nump"; else $anp="1";
if ($number>1) print"<TD class=row1 align=center>$anp</TD></TR><TR height=25>";

$msg=$msgtowrite; // Убираем спец код из строки поиска
$msg=str_replace("&","&amp;",$msg); $msg=str_replace('\"','"',$msg);
$msg=str_replace("[b]","<p>",$msg); $msg=str_replace("[/b]","</p>",$msg);
$msg=str_replace("[RB]","<p>",$msg); $msg=str_replace("[/RB]","</p>",$msg);
$msg=str_replace("[Code]","<p>",$msg); $msg=str_replace("[/Code]","</p>",$msg);
$msg=str_replace("[Quote]","<p>",$msg); $msg=str_replace("[/Quote]","</p>",$msg);
$msg=str_replace("<br>","\r\n", $msg);
$msg=str_replace("&amp;lt;br&amp;gt;","<p></p>", $msg);

print "<TD class=row1 align=center><B>$number</B></TD>
<TD class=row1><A class=listlink href='index.php?id=$id&page=$page#m$iii' target=_blank>$dt[5]</A></TD>
<TD class=row1>$msg</TD>";
$printflag="0"; $nump="0";

} else $nump++;

if ($number>=100) { print"</TR></TABLE> * поиск останавливается, при нахождении более 100 вхождений!"; $gi=0; $iii=0; $ii=0; $itogofid=0;}

$oldid=$id;
} // if $printflag==1

} while($gi > "0"); // конец ЦИКЛа по МЕСТУ поиска

} while($iii > "0");
} // если файл с сообщениями НЕПУСТОЙ

} // if is_file("$datadir/$id.dat")
} while($ii > "0");

} // if isset($topicsid)

} // if файл $fid.dat НЕ пуст

$itogofid--;
} while($itogofid > "0");

if (!isset($m)) echo'<table width=80% align=center><TR><TD>По вашему запросу ничего не найдено.</TD></TR></table>';

$time=explode(' ',microtime());
$seconds=($time[1]+$time[0]-$start_time);
echo "</TR></table><HR size=+2 width=99% color=navy><BR><p align=center><small>".str_replace("%1", sprintf("%01.3f", $seconds), "Время поиска: <b>%1</b> секунд.")."</small></p>";

}

} // if isset($_GET['event']) - всё, что делается при наличии переменной $event

?>

</td></tr></table>
<center><small>Powered by <a href="http://www.wr-script.ru" title="Скрипт форума" class="copyright">WR-Forum mini</a> &copy; 2.2 UTF-8<br></small></center>
</body>
</html>