<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog.php");
ini_set("soap.wsdl_cache_enabled", 0);

CModule::IncludeModule('webservice');



$client = new CSOAPClient( "smev-mvf.test.gosuslugi.ru", '/gateway/services/SID0003218/wsdl', '7777' );

//$client->setLogin("tenderix1c");
//$client->setPassword("Qweasdzxcrfv123");

$request = new CSOAPRequest( "SmevUnifoServiceSOAP", 'http://roskazna.ru/SmevUnifoService/UnifoTransferMsg' );
//$request->addParameter("CODE_INN", "77187032422");
//$request->addParameter("TITLE", "Рога и копыта");

print_r($request);

//$client = new CSOAPClient( "tenderix.wls.su", '/tenderix/web_services2.php' );
/*
$client->setLogin("tenderix1c");
$client->setPassword("Qweasdzxcrfv123");
*/
//$request = new CSOAPRequest( "CheckCompany", 'http://tenderix.wls.su/' );
//$request->addParameter("CODE_INN", "77187032422");
//$request->addParameter("PARAMS", array(
//				"XML_ID" => "351",
//				"TITLE" => "Hjuf"
//
//));


//$response = $client->send( $request );

//if ($response->FaultString)
//	echo $response->FaultString;
//else
//	echo "<pre>".mydump($response->Value)."</pre>";

?>