<?php

require_once 'xnet_api.php';

$session = new ApiXnet('reseller','api','reseller_api_key','reseller_api_slug','http://demo.xnet.cz');
/*
$domains = $session->domains();
print_r($domains);

$orders = $session->orders();
print_r($orders);

$order_detail = $session->order_detail(344941681);
print_r($order_detail);
*/

$xml_order = '<?xml version="1.0" encoding="UTF-8"?>
<order>
<domain>
<name>kubicek.cz</name>
<owner>
<client_type>person</client_type>
<first_name>Jiri</first_name>
<last_name>Kubicek</last_name>
<street>Kamenicka 26</street>
<city>Praha 7</city>
<postal_code>170 00</postal_code>
<country_code>CZ</country_code>
<email>email@email.tld</email>
<birth_date>18.2.1980</birth_date>
</owner>
<admin_handle>XNET</admin_handle>
<nsset_handle>NSS:KRAXNET:1</nsset_handle>
<period>1</period>
</domain>
</order>';
$order = $session->take_order($xml_order);
print_r($order);

?>
