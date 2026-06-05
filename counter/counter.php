<? // WR-Counter v 1.5 UTF-8  //  25.11.18 г.  //  Miha-ingener@yandex.ru

#error_reporting(E_ALL);

include "data/config.php";

$times=time(); $ldate="0"; // Блок МЫЛИТ СТАТИСТИКУ АДМИНУ
if (is_file("$coundir/last.csv")) {$lline=file("$coundir/last.csv"); $li=count($lline); if ($li>0) $ldate=$lline[0];}
$datescribe=$ldate+7*86400; // расчитываем дату отправки
if ($times>$datescribe) include("infomail.php");

function browser($agent) { // Функция определяет БРАУЗЕР
preg_match("/(MSIE|Opera|Firefox|Chrome|Chromium|Version)(?:\/| )([0-9.]+)/", $agent, $browser_info);
list(,$browser,$version) = $browser_info;
if (strlen($version)>3) {$version=substr($version,0,1); $version.="0+";} // Для версий 30-99 будет показываться 30+ или 40+ и т.д.
if ($browser == 'Opera' && $version == '9.80') return 'Opera '.substr($agent,-5);
if ($browser == 'Version') return 'Safari '.$version;
if (!$browser && strpos($agent, 'Gecko')) return 'Браузер на движке Gecko';
return $browser.' '.$version; }

function user_os($user_agent) { // Функция определяет ОПЕРАЦИОННУЮ СИСТЕМУ. C 2016 добавлены Android, iPod, iPhone, iPad
$os=array(
'Windows' => 'Win',  
'Open BSD'=>'OpenBSD',  
'Sun OS'=>'SunOS',  
'Linux'=>'(Linux)|(X11)',  
'Mac OS'=>'(Mac_PowerPC)|(Macintosh)',  
'QNX'=>'QNX',  
'BeOS'=>'BeOS',  
'OS/2'=>'OS/2',
'Android' => '(Android)',
'iPod' => '(iPod)',
'iPhone' => '(iPhone)',
'iPad' => '(iPad)');
foreach($os as $key=>$value) {if (preg_match('#'.$value.'#i', $user_agent)) return $key;}
return 'Неизвестна'; }

function addSpace($num) {$strlen=17-strlen($num); $space=null; while($strlen) {$space.=" "; $strlen--;} return $space.$num;}

function read_file($path)
{if(!is_file($path))return false;
elseif(!filesize($path))return array();
elseif($array=file($path))return $array;
else while(!$array=file($path))sleep(1);
return $array;}

function normal_numeric($number)
{if(!isset($number))return false;
else{$strlen=strlen($number);
$new=null;
for ($i=$strlen-1;$i>-1;$i--)
{$n = $i;$n++;if(strstr($n/3,"."))$new.=$number[$strlen-1-$i];
else if($n!=$strlen)$new.=" ".$number[$strlen-1-$i];
else$new.=$number[$strlen-1-$i];}
return $new;}}


if(!extension_loaded("gd")) exit("У Вашего хостера моуль GD не загружен - скрипт работать НЕ будет");

$browser=browser($_SERVER['HTTP_USER_AGENT']); // Определяем БРАУЗЕР
$os=user_os($_SERVER['HTTP_USER_AGENT']); // Определяем ОС
$ip=(isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:0; // Определяем IP
$a=$b=$c=null;
$today=date('d.m.Y',time());
if (isset($_SERVER["HTTP_REFERER"])) $from=$_SERVER["HTTP_REFERER"]; else $from=""; // Определяем СТРАНИЦУ
if (!is_file("$coundir/$today.csv")) {$OpenToday=fopen("$coundir/$today.csv","a"); fclose($OpenToday);}

$itogo=read_file("$coundir/all.csv"); if(!isset($itogo[0])) $itogo[0]=0;
$fp=fopen("$coundir/all.csv","a+");
flock ($fp,LOCK_EX);
$a=fread($fp,100);
@$a=$a+1;
ftruncate($fp, 0);
fwrite($fp,$a);
fflush($fp);
flock($fp,LOCK_UN);
fclose($fp);

$ft=fopen("$coundir/$today.csv","a"); flock ($ft,LOCK_EX); fwrite($ft,"$ip;$times;$browser;$os;$from;\r\n"); flock ($ft,LOCK_UN); fclose($ft);
$newlines=read_file("$coundir/$today.csv"); 
for ($i=0;$i < count($newlines); $i++) {$dt=explode(";", $newlines[$i]); $lines[$i]=$dt[0];}
$b=count($lines); $c=count(array_unique($lines));

if(strlen($a)>9||!isset($a)) $a="?"; if(strlen($b)>9||!isset($b)) $b="?"; if(strlen($c)>9||!isset($c)) $c="?";

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: image/png".chr(10).chr(10));

$image=ImageCreateFromPNG("./images/".$image);

$color1=ImageColorAllocate($image,$s1r,$s1g,$s1b);
$color2=ImageColorAllocate($image,$s2r,$s2g,$s2b);
$color3=ImageColorAllocate($image,$s3r,$s3g,$s3b);

ImageString($image,1,0,2, addSpace(normal_numeric("$a")),$color1);
ImageString($image,1,0,12, addSpace(normal_numeric("$b")),$color2);
ImageString($image,1,0,21, addSpace(normal_numeric("$c")),$color3);
ImagePNG($image);

?>