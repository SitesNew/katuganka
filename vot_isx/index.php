 <? // WR-Golos v 1.5  // 05.12.2018 г. // Пример вывода голосований № 1 и 2

function golos($id,$logo) { // функция golos(1,имя файла jpg с картинкой без расширения);
	$golosdir="./data/"; // путь до папки с данными
	if (!ctype_digit($id)) exit(' <b>Ошибочно указан номер голосования.</b>');
	// в функции проверку на текст лого
	if (!preg_match("/^[a-z0-9\.\-_]+$/is", $logo) or $logo=="" or strlen($logo)>20) exit("Ошибочно указано фото для фона");	
	$lines = file("$golosdir/vote$id.csv"); // Здесь путь до папки data указан если уровень одинаков, если вы ставите голосование на уровень ниже - скорректируйте - добавьте  ../
	$itogo=count($lines); $i=1;
	$vdt=explode(";",$lines[0]);	

print"<script language=JavaScript><!--
function gosub() {WRSub=window.open('vote.php?id=$id','WRGolos','width=450,height=350,left=250,top=100'); WRSub.focus();}
function gorez() {WRSub=window.open('rezult.php?id=$id','WRRezultGolos','width=450,height=350,left=250,top=100'); WRSub.focus();}//--></script>
<link href='images/style.css' rel='stylesheet' type='text/css' />
		<FORM name=wrvote action='vote.php?id=2' method=post target='WRGolos'>
		<div id='container'>
			<div class='bubble' style=\"BACKGROUND-IMAGE: url(images/$logo.jpg); background-size: cover; BACKGROUND-REPEAT: no-repeat\">
				<div class='rectangle'><h2>Голосование</h2></div>
				<div class='triangle-l'></div>
				<div class='triangle-r'></div>
				<div class='info'>
					<h2>$vdt[0]</h2>
					<p>";
do {$dt=explode(";",$lines[$i]); print"<INPUT name='votec' type=radio value='$i'><B>$dt[0]</B></BR>"; $i++;} while($i<$itogo);
print"				</p>
				<p><a href='#' onClick='gosub();'><button class='knopka knopka-red'>Проголосовать</button></a></p>
				</div>
			</div>
		</div>
</FORM>
<a href='rezult.php?id=$id' onClick='gorez();' target='WRRezultGolos'><button class='knopka knopka-red'>Результаты</button></a>";
}
// Пример 1
golos("1","fongolos-1");  // выводим голосование № 1
// Пример 2
golos("2","fongolos-2");  // выводим голосование № 2

?>
