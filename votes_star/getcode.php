<?php
# (c) 2005, Programming by InfMag
# Author's email: infmag@yandex.ru
# Author's ICQ: 320-215-083

header("Content-Type: text/html; charset=UTF-8");

include("cfg.php");

$votearray = file_exists($votefile) ? file($votefile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

?>
<html>
<head>
<!--
	(c) 2005, Programming by InfMag
	Author's email: infmag@yandex.ru
	Author's ICQ: 320-215-083
-->
<title>Код для размещения голосования</title>
<style>
body, textarea {
	background-color: #ffffff;
	color: #000000;
	font-family: Tahoma, Verdana, Geneva, Arial, Helvetica, sans-serif;
}
body, p, div, td, textarea {
	font-size: 12px;
}
a {
	text-decoration: none;
	color: #666666;
}
a:hover {
	color: #0066cc;
}
</style>
</head>
<body>
<table border="0" cellpadding="50" cellspacing="0" width="100%" height="100%">
<tr>
<td align="center" valign="middle">
<p><font size="5">Код для размещения голосования</font></p>
<p><font size="4"><a href="<?php
	echo file_exists("homepage.txt") ? htmlspecialchars(file_get_contents("homepage.txt")) : "#";
?>">Вернуться на сайт</a></font></p>
<p>
<textarea style="width: 400px; height: 300px;" onClick="this.select();" readonly><!-- Система голосований -->
<form action="votes/vote.php" method="post">
<div align="center"><font size="2"><?php echo isset($votearray[0]) ? htmlspecialchars($votearray[0]) : "Голосование не найдено"; ?></font></div>
<table border="0" cellpadding="2" cellspacing="0" align="center">
<?php for ($i = 1; $i < count($votearray); $i++): ?>
    <?php $explode = explode("|", $votearray[$i]); ?>
<tr>
<td align="center" valign="middle">
    <input type="radio" name="votenum" value="<?php echo $i; ?>"<?php echo ($i == 1) ? " checked" : ""; ?>>
</td>
<td align="left" valign="middle">
    <font size="2"><?php echo htmlspecialchars($explode[0]); ?></font>
</td>
</tr>
<?php endfor; ?>
</table>
<div align="center"><input type="submit" name="vote" value="Проголосовать"></div>
<div align="center"><font size="1"><a href="votes/index.php"><b>Результаты</b></a></font></div>
</form>
<!-- Система голосований --></textarea>
</p>
<p><font size="1">&copy; 2005, Programming by <a href="mailto:infmag@yandex.ru">InfMag</a></font></p>
</td>
</tr>
</table>
</body>
</html>
