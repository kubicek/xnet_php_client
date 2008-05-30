<?php

require_once 'xnet.php';

$session = new Xnet('zacatecnik','heslo','https://admin.xnet.cz');

$domains = $session->domains();

print "Expirace\tDomena\n";
print "=========================================================================================\n";
foreach ($domains as $domain) {
	echo ($domain->expiry."\t");
	echo ($domain->name."\n");
}

$domain = $session->domain($domains[0]->name);
print_r($domain);

?>