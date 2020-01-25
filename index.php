<?php

require_once('utils/calcs.php');

doAJAX();
doInitDisplay(getHTOutputVars());

function doAJAX() {
    if (!isset($_REQUEST) || !isset($_REQUEST['quota'])) return;
    header('Content-Type: text/html');
    $o = new pquo();
    $res = $o->calcs();
    echo $res;
    exit(0);
}

function getHTOutputVars() {
    $pqo = new pquo();
    $ddate = $pqo->getNextTS();
    $dquo  = $pqo->getQuota();
    $au    = $pqo->getActualUsage();
    if (!$au) $au = '';
    $umax  = $dquo * 1000;
    $calcs = $pqo->calcs();
    unset($pqo);

    $vars = get_defined_vars(); 
    return $vars;
}

function doInitDisplay($vin) {
    foreach($vin as $k => $v) $r['htout_' . $k] = $v;
    unset($vin);
    extract($r);
    unset($r);
    require_once('template.php');
}
