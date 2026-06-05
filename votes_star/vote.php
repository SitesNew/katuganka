<?php
header("Content-Type: text/html; charset=UTF-8");

include("cfg.php");

function getip(): ?string
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'] ?? null;
}

function addme(): void
{
    global $timenewuser, $userfile;

    $fp = fopen($userfile, "a");
    if ($fp) {
        fwrite($fp, getip() . "|" . (time() + $timenewuser) . "|\n");
        fclose($fp);
    }
}

function findme(): bool
{
    global $userfile;

    if (!file_exists($userfile)) {
        return false;
    }

    $userarray = file($userfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($userarray === false) {
        return false;
    }

    foreach ($userarray as $line) {
        $explode = explode("|", $line);
        if ($explode[0] === getip()) {
            return true;
        }
    }

    return false;
}

function errortohtml(string $message): void
{
?>
<html>
<head>
<title>Помилка !</title>
<style>
body {
    background-color: #ffffff;
    color: #000000;
    font-family: Tahoma, Verdana, Geneva, Arial, Helvetica, sans-serif;
}
body, p, div, td {
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
<p><font size="5" color="#FF0000">Помилка !!!</font></p>
<p><font size="4">
<a href="<?php
    if (file_exists("homepage.txt")) {
        echo htmlspecialchars(file_get_contents("homepage.txt"));
    }
?>">Поверніться на сайт.</a>
</font></p>
<p><font size="4" color="#FF0000"><?php echo htmlspecialchars($message); ?></font></p>
</td>
</tr>
</table>
</body>
</html>
<?php
}

if (isset($_POST['vote'], $_POST['votenum'])) {
    if (!file_exists($votefile)) {
        errortohtml("Файл голосования не найден!");
        exit();
    }

    $votearray = file($votefile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($votearray === false) {
        errortohtml("Ошибка чтения файла голосования!");
        exit();
    }

    $voteIndex = (int) $_POST['votenum'];
    if ($voteIndex < 1 || $voteIndex >= count($votearray)) {
        errortohtml("Задано невірний параметр!");
        exit();
    }

    $fp = fopen($votefile, "w");
    if (!$fp) {
        errortohtml("Ошибка записи в файл голосования!");
        exit();
    }

    foreach ($votearray as $i => $line) {
        if ($i === $voteIndex) {
            $explode = explode("|", $line);
            $explode[1] = isset($explode[1]) ? (int) $explode[1] + 1 : 1;
            fwrite($fp, "{$explode[0]}|{$explode[1]}|\n");
        } else {
            fwrite($fp, $line . "\n");
        }
    }

    fclose($fp);
    addme();
}

header("Location: index.php");
exit();
?>
