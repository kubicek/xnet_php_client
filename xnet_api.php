<?php


// For all the fetchin'
require_once 'HTTP/Request.php';
require_once 'XML/Serializer.php';
require_once 'XML/Unserializer.php';

class ApiXnet {
	
	var $user;
	var $pass;
	var $subject_slug;
	var $api_key;
	
	var $base;

	function ApiXnet($user, $pass, $api_key, $subject_slug, $base) {
		$this->user = $user;
		$this->pass = $pass;
		$this->api_key = $api_key;
		$this->subject_slug = $subject_slug;
		$this->base = $base;
	}

	/*======================/
	 * 	General
	/*=====================*/
	
	// Vraci seznam domen, ktere muzete administrovat pod timto uctem
	function domains() {
	  	return $this->hook("/{$this->subject_slug}/v1/domains.xml?api_key={$this->api_key}","domain");
	}
	
	// Vraci seznam objednavek, ktere jsou vystaveny pro dany subjekt
	function orders() {
	  	return $this->hook("/{$this->subject_slug}/v1/orders.xml?api_key={$this->api_key}","order");
	}

	function order_detail($order_id) {
		return $this->hook("/{$this->subject_slug}/v1/orders/{$order_id}.xml?api_key={$this->api_key}","order");
	}
	
	function take_order($xml_order) {
		return $this->hook("/{$this->subject_slug}/v1/orders.xml?api_key={$this->api_key}","order",$xml_order);
	}
	
	/*===================
	 * 	The Worker Bees  
	/*===================*/
	
	function hook($url,$expected,$send = false) {
		$returned = $this->unserialize($this->request($url,$send));
		$placement = $expected;
		if (isset($returned->{$expected})) {
			$this->{$placement} = $returned->{$expected};	
			return $returned->{$expected};
		} else {
			$this->{$placement} = $returned;
			return $returned;
		}
	}
	
	function request($url, $params = false) {
		//do the connect to a server thing
		$req =& new HTTP_Request($this->base . $url);
		//authorize
		$req->setBasicAuth($this->user, $this->pass);
		//set the headers
		$req->addHeader("Accept", "application/xml");
		$req->addHeader("Content-Type", "application/xml");
		//if were sending stuff
		if ($params) {
			//serialize the data
			//$xml = $this->serialize($params);
			//($xml)?$req->setBody($xml):false;
			$req->setBody($params);
			$req->setMethod(HTTP_REQUEST_METHOD_POST);
		}
		$response = $req->sendRequest();
		// print_r($req->getResponseHeader());
		// echo $req->getResponseCode() .	"\n";
		
		if (PEAR::isError($response)) {
		    return $response->getMessage();
		} else {
		    print_r($req->getResponseBody());
		    return $req->getResponseBody();
		}
	}
	
	function serialize($data) {
		$options = array(	XML_SERIALIZER_OPTION_MODE => XML_SERIALIZER_MODE_SIMPLEXML,
						 	XML_SERIALIZER_OPTION_ROOT_NAME   => 'request',
							XML_SERIALIZER_OPTION_INDENT => '  ');
		$serializer = new XML_Serializer($options);
		$result = $serializer->serialize($data);
		return ($result)?$serializer->getSerializedData():false;
	}
	
	function unserialize($xml) {
		$options = array (XML_UNSERIALIZER_OPTION_COMPLEXTYPE => 'object');
		$unserializer = &new XML_Unserializer($options);
		$status = $unserializer->unserialize($xml);    
	    $data = (PEAR::isError($status))?$status->getMessage():$unserializer->getUnserializedData();
		return $data;
	}
}

// match comments
// \/\/ .+


?>
