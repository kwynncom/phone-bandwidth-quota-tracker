<?php

require_once('/opt/kwynn/kwutils.php');

class dao_pquo extends dao_generic {
    
    const dbName = 'pquo';
    
    public function __construct() {
	parent::__construct(self::dbName);
	$this->rcoll = $this->client->selectCollection(self::dbName, 'running');
	$this->acoll = $this->client->selectCollection(self::dbName, 'arch'   );
	$sid = startSSLSession();
	$this->q = ['sid' => $sid];
    }
    
    public function put($au, $date, $quo, $isnewau, $unmu) {
	$dat           = $this->q;
	$dat['tday' ]  = $date;
	$dat['quota']  = $quo;
	$dat['ausage'] = $au;
	$dat['unmu']   = $unmu;
	$dat['agent']  = self::getua();
	$this->rcoll->upsert($this->q, $dat);
	
	if (!$isnewau || !$au) return;
	
	unset($dat['agent']);
	
	$dat['ts']     = time();
	$dat['r']      = date('r', $dat['ts']);
	$dat['ip']     = $_SERVER['REMOTE_ADDR'];
	$this->acoll->insertOne($dat);
	
    }
    
    private function getua() {
	if (isset( $_SERVER['HTTP_USER_AGENT'])) 
	    return $_SERVER['HTTP_USER_AGENT'];
	
	if (PHP_SAPI === 'cli') return 'cli';
	return 'unk';
    }
    
    public function get() { return $this->rcoll->findOne($this->q);  }
}


