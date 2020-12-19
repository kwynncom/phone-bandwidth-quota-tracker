<?php

require_once('/opt/kwynn/kwutils.php');
require_once('dao.php');


class pquo {

    const defaultTDay  = 7;
    const defaultQuota = 15;
    
    public function __construct($turnday = false, $quo = false, $time = false) {
	$this->turnday = $turnday ? $turnday : $this->getVal('turnday');
	$this->quo     = $quo     ? $quo     : $this->getVal('quota');
	$this->now     = $time ? $time : time();
	$this->unmu    = $this->getVal('unmu');
	return;
    }
    
    private function getVal($pn) {

	if (isset($_REQUEST[$pn])) {
	    $pv = $_REQUEST[$pn];
	    if ($pn === 'quota') {
		kwas(is_numeric($pv), 'bad quota value');
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
	    if ($pn === 'turnday') return $this->dbdat['tday'];
	    if ($pn === 'unmu')    return isset($this->dbdat['unmu']) ? $this->dbdat['unmu'] : '';
	    kwas(0, 'should not be here in dao getVal()');
	}
	
	if ($pn === 'turnday') return self::defaultTDay;
	if ($pn === 'quota')   return self::defaultQuota;
	if ($pn === 'unmu' )   return '';
	kwas(0, 'should not be here at the end of getVal() dao');
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
	
	$ht .= self::getTab1($au, $ap, $qad, $ppd);
	$ht .= self::getTab2($apd, $perday);
	$ht .= self::getTab3($dintop, $dpinp);

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
    
    private static function getTab2($apd, $perday) { 
	$ht  = '<table>' . "\n";	
	$ht .= '<tr><th>MB</th><th></th></tr>' . "\n";
        $ht .= "<tr><td>$apd</td>   <td class='tdlab'>per day until turnover can be used, on avg., given actual</td></tr>\n";
	$ht .= "<tr><td>$perday</td><td class='tdlab'>per day can be used assuming linear usage</td></tr>\n\n";	
	$ht .= '</table>' . "\n";
	
	return $ht;
    }
    
    private static function getTab3($dintop, $dpinp) { 
	$ht = '<table>' . "\n";	
	$ht .= '<tr><td>' . round($dintop) . '</td><td class="tdlab">days left in period</td>  <td>' . round($dintop, 5) . '</td></tr>' . "\n";	
	$ht .= '<tr><td>' . round($dpinp)  . '</td><td class="tdlab">days passed in period</td><td>' . round($dpinp , 5) . '</td></tr>' . "\n";	
	$ht .= '</table>' . "\n";
	
	return $ht;
    }
    
    private static function getTab1($au, $ap, $qad, $ppd) {

	$ht = '<table>' . "\n";

	$ht		       .= "<tr><th>MB</th> <th>%</th><th></th></tr>   ";
	if ($au !== false) $ht .= "<tr><td>$au</td> <td class='peru'>$ap</td><td  class='tdlab'>actual usage</td></tr>\n";
	$ht .=		          "<tr><td>$qad</td><td class='peru'>$ppd</td><td class='tdlab'>can be used by now assuming linear usage</td></tr>\n";    	
	$ht .= '</table>' . "\n";
	return $ht;
    }
    
    private function save($au, $isnew) {
	$dao = new dao_pquo();
	$dao->put($au, $this->turnday, $this->quo, $isnew, $this->unmu);
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
