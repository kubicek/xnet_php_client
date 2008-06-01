<?php


// For all the fetchin'
require_once 'HTTP/Request.php';
require_once 'XML/Serializer.php';
require_once 'XML/Unserializer.php';

class Xnet {
	
	var $user;
	var $pass;
	
	var $base;

	function Xnet($user, $pass, $base) {
		$this->user = $user;
		$this->pass = $pass;
		$this->base = $base;
	}

	/*======================/
	 * 	General
	/*=====================*/
	
	// Vraci seznam domen, ktere muzete administrovat pod timto uctem
	function domains() {
	  	return $this->hook("/person/domains","domain");
	}
	
	// Vraci informace o domene
	function domain($fqdn) {
		$fqdn = preg_replace('/\./', '_', $fqdn);
		return $this->hook("/domains/{$fqdn}","domain");
	}

	// Vraci seznam subjektu, ktere muzete administrovat pod timto uctem
	function subjects() {
	  	return $this->hook("/person/subjects","subject");
	}
	
	// Vraci seznam objednavek, ktere jsou vystaveny pro dany subjekt
	function subject_orders($subject_id) {
	  	return $this->hook("/subjects/{$subject_id}/orders","order");
	}

	// Vraci seznam domen, ktere jsou zarazeny pod dany subjekt
	function subject_domains($subject_id) {
	  	return $this->hook("/subjects/{$subject_id}/domains","domain");
	}
		
	/*===================/
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
			$xml = $this->serialize($params);
			//print_r($xml);
			($xml)?$req->setBody($xml):false;
			$req->setMethod(HTTP_REQUEST_METHOD_POST);
		}
		$response = $req->sendRequest();
		// print_r($req->getResponseHeader());
		// echo $req->getResponseCode() .	"\n";
		
		if (PEAR::isError($response)) {
		    return $response->getMessage();
		} else {
			  // print_r($req->getResponseBody());
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