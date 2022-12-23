<?php

require_once('/opt/kwynn/kwutils.php');
require_once('dao.php');


class pquo {

    const defaultTDay  = 7;
    const defaultQuota = 15;
	const defaultQuota20 = 35;
    
    public function __construct($turnday = false, $quo = false, $time = false) {
	$this->turnday = $turnday ? $turnday : $this->getVal('turnday');
	$this->quo     = $quo     ? $quo     : $this->getVal('quota');
	$this->quo20   =					   $this->getVal('quota20');
	$this->now     = $time ? $time : time();
	$this->unmu    = $this->getVal('unmu');
	return;
    }
    
    private function getVal($pn) {

	if (isset($_REQUEST[$pn])) {
	    $pv = $_REQUEST[$pn];
	    if ($pn === 'quota' || $pn === 'quota20') {
		kwas(is_numeric($pv), 'bad quota(20) value');
		if ($pv == intval($pv)) return intval($pv);
		return floatval($pv);
	    }
	    else if ($pn === 'turnday') {
		$ts = strtotime($pv);
		if (!$ts) die('bad date');
		return intval(date('d', $ts));
	    } else if ($pn === 'unmu') return intval($pv);
	}
	
	if (!isset($this->dao)) $this->dao = new dao_pquo();
	
	if (!isset($this->dbdat)) $this->dbdat = $this->dao->get();
	
	if ($this->dbdat) {
	    if ($pn === 'quota')   return $this->dbdat['quota'];
	    if ($pn === 'quota20')   return $this->getQuota20I();
		if ($pn === 'turnday') return $this->dbdat['tday'];
	    if ($pn === 'unmu')    return isset($this->dbdat['unmu']) ? $this->dbdat['unmu'] : '';
	    kwas(0, 'should not be here in dao getVal()');
	}
	
	if ($pn === 'turnday') return self::defaultTDay;
	if ($pn === 'quota')   return self::defaultQuota;
	if ($pn === 'quota20')   return self::defaultQuota20;
	if ($pn === 'unmu' )   return '';
	kwas(0, 'should not be here at the end of getVal() dao');
    }
    
	private function getQuota20I() {
		$q20 = kwifs($this, 'dbdat', 'quota20');
		if (!$q20) return self::defaultQuota20;
		else return $q20;
	}
    
    public function getRange() {
	$now = $this->now;
	$today    = intval(date('j', $now));
	$eom      = intval(date('t', $now));
	
	$turnday = $this->turnday;
	
	$base = strtotime(date("Y-m-$turnday", $now));
	
	if ($today >= $turnday) {
	    $prev  = $base;
	    $next = strtotime('+1 month' , $base);
	}
	else {
	    $next = $base;
	    $prev = strtotime('-1 month' , $base);
	}
		
	return ['pts' => $prev, 'nts' => $next, 'ps' => date('Y-m-d', $prev), 'ns' => date('Y-m-d', $next), 'now' => $now,
		'turnday' => $turnday, 'eom' => $eom, 'today' => $today];
    }
    
    
    public function getNextTS() {
	$res = $this->getRange();
	return $res['ns'];
    }
    
    public function getUnmu() { return $this->unmu; }
    
    public function getQuota() { return $this->quo;     }

	public function getQuota20() { return $this->quo20;     }
	
    public function getActualUsage() {
	
	$this->isnewau = false;
	
	if (!isset($_REQUEST['ausage'])) {
	    if (isset($this->dbdat['ausage']) &&
		      is_numeric($this->dbdat['ausage'])) return $this->dbdat['ausage'];
	    return false;
	} else {
	    $u =  trim($_REQUEST['ausage']);
	    if (!is_numeric($u)) return false;
	    $this->isnewau = true;
	    return intval($u);
	}
	return false;
	
    }
    
    public function calcs() {
	$cs = $this->getCalcs();
	$ht = $this->getCalcsHT($cs);
	return $ht;
    }
    
    private function getCalcsHT($vin) {
	
	extract($vin);
	
	$ht = '';
	$ht .= self::getTables30($au, $ap, $qad, $ppd, $apd, $perday, $dintop, $dpinp);
	$ht .= '<p>';
	$ht .= 'as of ' . date('D, M j g:i A', $now) . "\n";
	$ht .= '</p>';

	return $ht;	
    }
    
    private function getCalcs() {
	
	$rres = $this->getRange();
	$now     = $rres['now'];
	$next    = $rres['nts'];
	$prev    = $rres['pts']; 
	$eom     = $rres['eom'];
	unset($rres);
	$quota   = $this->getQuota() * 1000;
	
	$dintop = ($next - $now) / 86400; unset($next);
	$dpinp  = ($now - $prev) / 86400;

	$fp  = $dintop / $eom;
	$pp  = (1 - $fp) * 100;
	$ppd = round($pp); unset($pp);

	$qa = $quota - ($quota * $fp); unset($fp);
	$qad = round($qa); unset($qa);
	
	$au = $this->getActualUsage();
	if ($au !== false) {
	    $this->save($au, $this->isnewau);
	    $ap = round($au * 100 / $quota);
	    $apdf = ($quota - $au) / $dintop;
	    $apd = round($apdf);
	} else $ap = $apdf = $apd = '';

	$perday = round($quota / $eom); unset($eom, $quota);
	
	$vars = get_defined_vars();
	return $vars;

    }

	private static function getTables30($au, $ap, $qad, $ppd, $apd, $perday, $dintop, $dpinp) {
		ob_start();
		require_once(__DIR__ . '/../templates/usage15.php');
		require_once(__DIR__ . '/../templates/period30.php');
		return ob_get_clean();
		
	}
    
    private function save($au, $isnew) {
	$dao = new dao_pquo();
	$dao->put($au, $this->turnday, $this->quo, $isnew, $this->unmu, $this->quo20);
    }
}
  
checkCLI();

function checkCLI() { 
    
    global $argv;
    
    if (PHP_SAPI !== 'cli' || pathinfo(__FILE__, PATHINFO_FILENAME) !== pathinfo($argv[0], PATHINFO_FILENAME)) return;
    
    $pqo = new pquo(7,2,time());
    $res = $pqo->getRange(); 
    var_dump($res); 
    $pqo->calcs();
}
