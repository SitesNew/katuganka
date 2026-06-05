<?php
error_reporting(0);
  $xfile = file("db.dat"); // файл базы

  $p = 5; // 
$news_limiter=5; // количество новостей на стрницу
  if (empty($npage)){$npage = "1";}
  $first = count($xfile) - ($p * ($npage - 1));
  $second = count($xfile) - ($p * $npage) + 1;
  if ($second < 1) {$second = 1;}
  $npages = (int)(count($xfile) / $p);
  if ($news_limiter>((int) ($npages*$p))) $npages = $npages+1;
  if ($npage <= $npages and $npage>1) $gline_rew = "<a href=\"allnews.php?npage=".($npage-1)."\">Назад</a>"; // ссылка назад
  if ($npages > 1 and $npage<$npages) $gline_next = "<a href=\"allnews.php?npage=".($npage+1)."\">Вперед</a>";  // ссылка вперед
  $line = " | "; // линия разделитель между навигационными указателями ввиде цифр на верху страницы
 
  for ($p = 1; $p <= $npages; $p++) {
   if ($p != $npage) { $line .= "<a href=\"allnews.php?npage=$p\"> $p </a>|";}
   if ($p == $npage) { $line .= "$p |"; }
  }

  print $line."<br>";

  for ($i = $first-1; $i >= $second-1; $i--) {
   $ii = $i;
   $ii++;
   $udata = explode("|",$xfile[$i]);
   print "<p><b>$udata[0]</b>:<br><b>$udata[1]</b><br>$udata[2]</p>"; // вывод всех новостей постранично, можно отредактировать html теги
  }


      print $gline_rew.' <a href="http://'.$_SERVER['HTTP_HOST'].'" target = _top >  Головна Сторінка  </a> '.$gline_next     // вывод ссылок на следующие страницы, можно отредактировать html теги
  // print $gline_rew.' <a href="http://'.$_SERVER['HTTP_HOST'].'">  Головна Сторінка  </a> '.$gline_next     // вывод ссылок на следующие страницы, можно отредактировать html теги
  //  create www.kinyabulatov.info
  ?><?php require_once("include_options.php");?>
