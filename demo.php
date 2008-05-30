<?php

require_once 'xnet.php';

$session = new Xnet('zacatecnik','heslo','https://admin.xnet.cz');

$domains = $session->domains();

print "Expirace\tDomena\n";
print "=========================================================================================\n";
for ($p = 0; $p < count($domains); ++$p){
	echo ($domains[$p]->expiry."\t");
	echo ($domains[$p]->name."\n");
}

#$domain = $session->domain($domains[0]->name);
#print_r($domain);

?>