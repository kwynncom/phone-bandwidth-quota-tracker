<?php $v = 'v0.0.5 2019/09/29 5:28pm EDT';
/* I have a 2GB (We'll just call it 2,000 MB) bandwidth / data quota that rolls over at midnight (the first moment) of the 7th of the month.  
 * This calculates the percentage of quota I can use at any given moment to stay within quota.  This assumes my usage is constant, of course.  */

/* 2020/01/23 - This was the original, simpler version.  The current version is more sophisticated.  I call this "no storage" because the new version 
		saves previous usage data. */		

$quota = 2000; // MB

header('Content-Type: text/plain');

$now      = time();
$turnday  = 7;
$today    = intval(date('j', $now));
$eom      = intval(date('t', $now));

if ($today >= 7) {
    $next = strtotime(date('Y-m-t' , $now)) + 86400 * 7;
    $prev = strtotime(date('Y-m-07', $now));
}
else {
    $next = strtotime(date('Y-m-07'));
    $prev = $next - 86400 * $eom;
}

echo($v . "\n");
$dintop = ($next - $now) / 86400;
echo ($today . ' = today, day of month') . "\n";
$qd = ((floatval($next - $prev)) / floatval($eom * 86400));
echo (round($dintop, 4) . ' = days left in period') . "\n";
$fp  = $dintop / $eom;
$pp  = (1 - $fp) * 100;
$ppd = round($pp);
echo ($pp  . '% = percent of period, raw') . "\n";
echo ($ppd . '% = percent of period, rounded') . "\n";
echo date('r', $now) . ' = as of' . "\n";
$qa = $quota - ($quota * $fp);
$qad = round($qa);
echo ($qa  . 'MB used now to stay within quota, raw') . "\n";
echo ($qad . 'MB used now to stay within quota, rounded') . "\n";
$perday = round($qa / $dintop);
echo($perday . 'MB per day can be used to stay within quota') . "\n";
