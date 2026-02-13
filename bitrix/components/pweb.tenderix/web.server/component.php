<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("webservice"))
{
	return;
}

class Tenderix extends IWebService
{
//	function CheckAuthorization($user, $password)
//	{
//		$UserAuthTry = new CUser();
//		$authTry = $UserAuthTry->Login($user, $password);
//		if (!is_array($authTry))
//		{
//			$unode = $UserAuthTry->GetByLogin($user);
//			$uinfo = $unode->Fetch();
//			return "OK";
//		}
//
//		return new CSOAPFault( 'Server Error', 'Unable to authorize user.' );
//	}

	function CheckCompany($CODE_INN, $PARAMS){


		if(!CSite::InGroup(array(1,6,8))) {
			$GLOBALS["USER"]->RequiredHTTPAuthBasic();
			return new CSOAPFault('Server Error', 'Unable to authorize user.');
		}

		$id = 0;
		if(!empty($CODE_INN)) {
			$rsCompany = CTenderixCompany::GetList($by, $order, array("CODE_INN" => $CODE_INN), $is_filtered);
			//return new CSOAPFault('Server Error', $companyList->selectedRowsCount());

			global $DB;
			$arFields = array(
				"CODE_INN" => $CODE_INN,
				"ACTIVE" => "Y",
				"TIMESTAMP_X" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time()),
				"TITLE" => $PARAMS['TITLE'],
				"XML_ID" => $PARAMS['XML_ID'],
				"C_SORT" => ($PARAMS['C_SORT'] <= 0 ? 100 : $PARAMS['C_SORT']),
				"URL" => $PARAMS['$URL'],
				"DESCRIPTION" => $PARAMS['DESCRIPTION'],
				"CODE_KPP" => $PARAMS['CODE_KPP'],
				"CODE_OGRN" => $PARAMS['CODE_OGRN'],
				"CODE_OKVED" => $PARAMS['CODE_OKVED'],
				"CODE_OKPO" => $PARAMS['CODE_OKPO'],
				"LEGALADDRESS_COUNTRY" => $PARAMS['LEGALADDRESS_COUNTRY'],
				"LEGALADDRESS_REGION" => $PARAMS['LEGALADDRESS_REGION'],
				"LEGALADDRESS_CITY" => $PARAMS['LEGALADDRESS_CITY'],
				"LEGALADDRESS_BLOCK" => $PARAMS['LEGALADDRESS_BLOCK'],
				"LEGALADDRESS_INDEX" => $PARAMS['LEGALADDRESS_INDEX'],
				"LEGALADDRESS_LOCALITY" => $PARAMS['LEGALADDRESS_LOCALITY'],
				"LEGALADDRESS_STREET" => $PARAMS['LEGALADDRESS_STREET'],
				"LEGALADDRESS_POST" => $PARAMS['LEGALADDRESS_POST'],
				"LEGALADDRESS_PHONE" => $PARAMS['LEGALADDRESS_PHONE'],
				"POSTALADDRESS_COUNTRY" => $PARAMS['POSTALADDRESS_COUNTRY'],
				"POSTALADDRESS_REGION" => $PARAMS['POSTALADDRESS_REGION'],
				"POSTALADDRESS_CITY" => $PARAMS['POSTALADDRESS_CITY'],
				"POSTALADDRESS_BLOCK" => $PARAMS['POSTALADDRESS_BLOCK'],
				"POSTALADDRESS_INDEX" => $PARAMS['POSTALADDRESS_INDEX'],
				"POSTALADDRESS_LOCALITY" => $PARAMS['POSTALADDRESS_LOCALITY'],
				"POSTALADDRESS_STREET" => $PARAMS['POSTALADDRESS_STREET'],
				"POSTALADDRESS_POST" => $PARAMS['POSTALADDRESS_POST'],
				"POSTALADDRESS_PHONE" => $PARAMS['POSTALADDRESS_PHONE'],
        	);

			if ($rsCompany->selectedRowsCount()) {
				while($arCompany = $rsCompany->Fetch()) {
					$ID = CTenderixCompany::Update($arCompany['ID'], $arFields);

					return array('CODE_INN' => $arCompany['CODE_INN'], 'TITLE' => $arCompany['TITLE']);
				}
			}

        	//return new CSOAPFault('Server Error', urlencode(print_r($arFields, true)));
        	//return new CSOAPFault('Server Error', htmlentities(print_r($arFields, true)));

			$ID = CTenderixCompany::Add($arFields);
			if ($ID>0){
				$rsCompany = CTenderixCompany::GetList($by, $order, array("ID" => $ID), $is_filtered);
				while ($arCompany = $rsCompany->Fetch())
					return array('CODE_INN' => $arCompany['CODE_INN'], 'TITLE' => 'ADDED');
			}
		}
		return new CSOAPFault( 'Server Error', 'none' );
	}
	
	function CheckSections($XML_ID, $TITLE) {

// Убрали пока авторизацию
		if(!CSite::InGroup(array(1,6,8))) {
			$GLOBALS["USER"]->RequiredHTTPAuthBasic();
			return new CSOAPFault('Server Error', 'Unable to authorize user.');
		}

		if(!empty($XML_ID) && !empty($TITLE)) {

			$rsSection = CTenderixSection::GetList($by, $order, array("XML_ID" => $XML_ID));

			$arFields = array("ACTIVE" => "Y", "C_SORT" => 100, "TITLE" => $TITLE, "XML_ID" => $XML_ID);

			if ($rsSection->selectedRowsCount()) {
				while ($arSection = $rsSection->Fetch()) {
					$ID = CTenderixSection::Update($arSection['ID'], $arFields);
					//return new CSOAPFault('Server Error', htmlentities(print_r($res, true)));

            		return array('XML_ID' => $arSection['XML_ID'], 'TITLE' => $arSection['TITLE']);
        		}
			}

			$ID = CTenderixSection::Add($arFields);
			if ($ID>0){
				$rsSection = CTenderixSection::GetList($by, $order, array("ID" => $ID));
				while ($arSection = $rsSection->Fetch())
					return array('XML_ID' => $arSection['XML_ID'], 'TITLE' => $arSection['TITLE']);
			}

		}
		return new CSOAPFault( 'Server Error Sections', 'not work' );
	}

	function CheckLot($LOT, $TITLE, $SPEC, $SETTINGS) {
		//return new CSOAPFault('Server Error', htmlentities(print_r($SPEC, true)));

//		if(!CSite::InGroup(array(1,6,8))) {
//			$GLOBALS["USER"]->RequiredHTTPAuthBasic();
//			return new CSOAPFault('Server Error', 'Unable to authorize user.');
//		}

		//return new CSOAPFault('Server Error', htmlentities(print_r($LOT, true)));
		if(!empty($LOT['XML_ID']) ){
			
			//return new CSOAPFault('Server Error', htmlentities(var_export($LOT, true)));
			//return new CSOAPFault( 'Server Error Sections', htmlentities(print_r($TITLE, true)) );

			global $DB;
			$rsLot = CTenderixLot::GetList($by, $order, array("XML_ID" => $LOT['XML_ID']));
			//return new CSOAPFault('Server Error', htmlentities(print_r($rsLot, true)));
			//if($rsLot){

			//}
			//while ($arLot = $rsLot->Fetch())
			//	return new CSOAPFault('Server Error', htmlentities(print_r($arLot, true)));
			
			//return new CSOAPFault('Server Error', htmlentities(print_r($LOT, true)));
			//return new CSOAPFault('Server Error', htmlentities(print_r($rsLot->selectedRowsCount(), true)));
			
			$rsSection = CTenderixSection::GetList($by, $order, array("XML_ID" => $LOT['SECTION_XML_ID']));
			while ($arSection = $rsSection->Fetch()){
				//return new CSOAPFault('Server Error', htmlentities(print_r($LOT, true)));
				$LOT['SECTION_ID'] = $arSection['ID'];
			}

			
			$rsCompany = CTenderixCompany::GetList($by, $order, array("XML_ID" => $LOT['COMPANY_XML_ID']), $is_filtered);
			while ($arCompany = $rsCompany->Fetch())
				$LOT['COMPANY_ID'] = $arCompany['ID'];

			//return new CSOAPFault( 'Server Error Sections', htmlentities(print_r($SPEC, true)) );


			$arFields = array(
				"ACTIVE" => $LOT['ACTIVE'],
				"BUYER_ID" => $LOT['BUYER_ID'], // нужен id организатора торгов
				"TYPE_ID" => $LOT['TYPE_ID'], // Это медицинский лот,  TYPE_ID = T
				"TIMESTAMP_X" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time()),
				"XML_ID" => $LOT['XML_ID'], // required
				"DATE_START" => $LOT['DATE_START'], // requared
				"DATE_END" => $LOT['DATE_END'], // requared
				"TITLE" => $TITLE, // requared
				"SECTION_ID" => $LOT['SECTION_ID'], // requared
				"COMPANY_ID" => $LOT['COMPANY_ID'], // requared
				"RESPONSIBLE_FIO" => $LOT['RESPONSIBLE_FIO'], // requared
				"RESPONSIBLE_PHONE" => $LOT['RESPONSIBLE_PHONE'], // requared
				"CURRENCY" => ($LOT['CURRENCY'] == "") ? "RUB" : $LOT['CURRENCY'], // RUB USB EUR
				"NOTE" => $LOT['NOTE'],

				//"TERM_PAYMENT_EDIT" => ($SETTINGS["TERM_PAYMENT_EDIT"] == 'false') ? "N" : "Y",
				//"TERM_PAYMENT_ID" => ($SETTINGS["TERM_PAYMENT_EDIT"] == 'true') ? '16': "",
//				"OPEN_PRICE" => ($SETTINGS['OPEN_PRICE'] == 'false') ? "N" : "N",
//				"NOSAME" => ($SETTINGS["NOSAME"] == 'false') ? "N" : "N",
//				"NOBAD" => ($SETTINGS["NOBAD"] == 'false') ? "N" : "N",
//				"ONLY_BEST" => ($SETTINGS["ONLY_BEST"] == 'false') ? "N" : "N",
//				"SEND_SPEC" => ($SETTINGS["SEND_SPEC"] == 'false') ? "N" : "N",
//				"VIZ_HIST" => ($SETTINGS["VIZ_HIST"] == 'false') ? "N" : "N",
//				"PRE_PROPOSAL" => ($SETTINGS["PRE_PROPOSAL"] == 'false') ? "N" : "Y",
//				"NOEDIT" => ($SETTINGS["NOEDIT"] == 'false') ? "N" : "Y",
//				"NOTVISIBLE_PROPOSAL" => ($SETTINGS["NOTVISIBLE_PROPOSAL"] == 'false') ? "N" : "Y",
//				"PRIVATE" => ($SETTINGS["PRIVATE"] == "") ? "N" : "Y",
				"TIME_EXTENSION" => $SETTINGS["TIME_EXTENSION"],
				"TIME_UPDATE" => (intval($SETTINGS["TIME_UPDATE"]) > 0 ? $SETTINGS["TIME_UPDATE"] : "600"),
				"WITH_NDS" => ($SETTINGS["WITH_NDS"] == "") ? "N" : "Y",

	);
	
		//return new CSOAPFault( 'Server Error Sections', htmlentities(print_r($arFields, true)) );
			
		$arFieldsDop = array(); 
		
		foreach($SPEC as $value){
			
			//return new CSOAPFault( 'Server Error Sections', htmlentities(print_r($SPEC, true)) );
			//return new CSOAPFault('Server Error', htmlentities(print_r($value, true)));
			
			if(strlen($value["TITLE"]) <= 0 ||
				strlen($value["COUNT"]) <= 0 ||
				strlen($value["UNIT_XML_ID"]) <= 0
			)
				continue;

			$rsUnit = CTenderixSprDetails::GetList($by, $order, array("XML_ID" => $value["UNIT_XML_ID"]), $is_filtered);
			while ($arUnit = $rsUnit->Fetch()) {
				$UnitId = $arUnit['ID'];
			}
			
			if ($value["ID_POS"]){
				$xmlID = $value["XML_ID"].'-'.$value["ID_POS"];	
			}else {
				$xmlID = $value["XML_ID"];
			}

			$arFieldsDop[$xmlID] = array(
				"XML_ID" => $xmlID,
				"UNIT_XML_ID" => $value["UNIT_XML_ID"],
				"TITLE" => $value["TITLE"],
				"ADD_INFO" => $value["ADD_INFO"],
				"COUNT" => $value["COUNT"],
				"UNIT_ID" => $UnitId,
				"START_PRICE" => $value["START_PRICE"],
				"STEP_PRICE" => $value["STEP_PRICE"],
			);
		}

		//return new CSOAPFault('Server Error', htmlentities(print_r($arFieldsDop, true)));
		// Обновление лота
		
		if($rsLot->selectedRowsCount()){

			while($arLot = $rsLot->Fetch()){

				$ID = CTenderixLot::Update($arLot['ID'], $arFields);

				if ($ID > 0) {
				$arFieldsSpec = array(
					"FULL_SPEC" => "N", //($SETTINGS["FULL_SPEC"] == "Y" ? "Y" : "N"),
					"NOT_ANALOG" => "N", //($SETTINGS["NOT_ANALOG"] == "Y" ? "Y" : "N"),
					"LOT_ID" => $ID
				);

				$SPEC_ID = intval(CTenderixLotSpec::Update($ID, $arFieldsSpec));
				
				if($SPEC_ID > 0){

					// товары в базе
					$rsSpec = CTenderixLotSpec::GetListProp($ID);
					while($arSpec = $rsSpec->Fetch())
						$exists[($arSpec['XML_ID'])] = $arSpec['ID'];
					unset($arSpec);

					// Добавление и обновление
					foreach($arFieldsDop as $arFieldDop){

						// Обновление
						if(isset($exists[($arFieldDop['XML_ID'])]))
							$data[] = CTenderixLotSpec::UpdateProp($exists[($arFieldDop['XML_ID'])], $arFieldDop);

						// Добавление
						if(!isset($exists[($arFieldDop['XML_ID'])])){
							$arFieldDop["SPEC_ID"] = $SPEC_ID;
							$data[] = CTenderixLotSpec::AddProp($arFieldDop);
						}

					}

					// Удаление
					foreach($exists as $exist => $value){
						if(!isset($arFieldsDop[$exist])){
							$data[] = CTenderixLotSpec::DeletePropID($value);
							$deleteSpec[] = CTenderixProposal::DeleteSpec($value);
							//return new CSOAPFault('Server Error', htmlentities(print_r($hello, true)));
						}
					}

				}
				return array('XML_ID' => $LOT['XML_ID'], 'TITLE' => $LOT['TITLE']);

			}
				return array('XML_ID' => $arLot['XML_ID'], 'TITLE' => $arLot['TITLE']);
        	}			
		}

			//Добавление нового
			
			$ID = intval(CTenderixLot::Add($arFields));
			
			if ($ID > 0) {
				
				$arFieldsSpec = array(
					"FULL_SPEC" => "N", //($SETTINGS["FULL_SPEC"] == "Y" ? "Y" : "N"),
					"NOT_ANALOG" => "N", //($SETTINGS["NOT_ANALOG"] == "Y" ? "Y" : "N"),
					"LOT_ID" => $ID
				);

				//return new CSOAPFault('Server Error', htmlentities(print_r($ID, true)));
				
				$SPEC_ID = intval(CTenderixLotSpec::Add($arFieldsSpec));
				
				
				
				if ($SPEC_ID > 0) {
					foreach ($arFieldsDop as $fieldPropNew) {
						$fieldPropNew["SPEC_ID"] = $SPEC_ID;
						$res = CTenderixLotSpec::AddProp($fieldPropNew);
					}
					
					
				}
				return array('XML_ID' => $LOT['XML_ID'], 'TITLE' => $LOT['TITLE']);	
				
			}
		}
		return new CSOAPFault( 'Server Error Sections', 'not work' );
	}

	function GetWebServiceDesc(){
		$wsdesc = new CWebServiceDesc();
		$wsdesc->wsname = "tenderix.web.server";
		$wsdesc->wsclassname = "Tenderix";
		$wsdesc->wsdlauto = true;
		$wsdesc->wsendpoint = CWebService::GetDefaultEndpoint();
		$wsdesc->wstargetns = CWebService::GetDefaultTargetNS();

		$wsdesc->classTypes = array();
		$wsdesc->structTypes["CUser"] = array(
				"id" => array("varType" => "string"),
			);
		$wsdesc->structTypes['companyParams'] = array(
				"XML_ID" => array("varType" => "string", "strict" => "no"),
				"TITLE" => array("varType" => "string", "strict" => "no"),
				"C_SORT" => array("varType" => "string", "strict" => "no"),
				"URL" => array("varType" => "string", "strict" => "no"),
				"DESCRIPTION" => array("varType" => "string", "strict" => "no"),
				"CODE_KPP" => array("varType" => "string", "strict" => "no"),
				"CODE_OGRN" => array("varType" => "string", "strict" => "no"),
				"CODE_OKVED" => array("varType" => "string", "strict" => "no"),
				"CODE_OKPO" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_COUNTRY" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_REGION" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_CITY" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_BLOCK" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_INDEX" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_LOCALITY" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_STREET" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_POST" => array("varType" => "string", "strict" => "no"),
				"LEGALADDRESS_PHONE" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_COUNTRY" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_REGION" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_CITY" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_BLOCK" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_INDEX" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_LOCALITY" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_STREET" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_POST" => array("varType" => "string", "strict" => "no"),
				"POSTALADDRESS_PHONE" => array("varType" => "string", "strict" => "no"),
			);
		$wsdesc->structTypes['Company'] = array(
			"CODE_INN" => array("varType" => "string"),
			"TITLE" => array("varType" => "string"),
			);
		$wsdesc->structTypes['Sections'] = array(
			"XML_ID" => array("varType" => "string"),
			"TITLE" => array("varType" => "string"),
			);
		$wsdesc->structTypes['Lotout'] = array(
			"XML_ID" => array("varType" => "string"),
			"TITLE" => array("varType" => "string"),
			);
		$wsdesc->structTypes['lot'] = array(
			"XML_ID" => array("varType" => "string"),
			"ACTIVE" => array("varType" => "string"),
			"TYPE_ID" => array("varType" => "string"),
			"BUYER_ID" => array("varType" => "string"),
			"DATE_START" => array("varType" => "string", "strict" => "no"),
			"DATE_END" => array("varType" => "string", "strict" => "no"),
			"SECTION_XML_ID" => array("varType" => "string", "strict" => "no"),
			"COMPANY_XML_ID" => array("varType" => "string", "strict" => "no"),
			"RESPONSIBLE_FIO" => array("varType" => "string", "strict" => "no"),
			"RESPONSIBLE_PHONE" => array("varType" => "string", "strict" => "no"),
			"DATE_DELIVERY" => array("varType" => "string", "strict" => "no"),
			"CURRENCY" => array("varType" => "string", "strict" => "no"),
			"NOTE" => array("varType" => "string", "strict" => "no"),
			);
		$wsdesc->structTypes['specProd'] = array(
			"XML_ID" => array("varType" => "string", "strict" => "no"),
			"ID_POS" => array("varType" => "string", "strict" => "no"),
			"TITLE" => array("varType" => "string", "strict" => "no"),
			"ADD_INFO" => array("varType" => "string", "strict" => "no"),
			"COUNT" => array("varType" => "string", "strict" => "no"),
			"UNIT_XML_ID" => array("varType" => "string", "strict" => "no"),
			"START_PRICE" => array("varType" => "string", "strict" => "no"),
			"STEP_PRICE" => array("varType" => "string", "strict" => "no"),
			);
		$wsdesc->structTypes['lotSettings'] = array(
			"OPEN_PRICE" => array("varType" => "string", "strict" => "no"),
			"TIME_EXTENSION" => array("varType" => "string", "strict" => "no"),
			"TIME_UPDATE" => array("varType" => "string", "strict" => "no"),
			"WITH_NDS" => array("varType" => "string", "strict" => "no"),
			"NOT_ANALOG" => array("varType" => "string", "strict" => "no"),
			);

		$wsdesc->classes = array(
			"Tenderix" => array(
//				"CheckAuthorization" => array(
//					"type"		=> "public",
//					"name"		=> "CheckAuthorization",
//					"input"		=> array(
//						"user" =>array("varType" => "string"),
//						"password" =>array("varType" => "string")),
//				),
				"CheckCompany" => array(
					"type" => "public",
					"name" => "CheckCompany",
					"input" => array(
						"CODE_INN" => array("varType" => "string"),
						"PARAMS" => array("varType" => "companyParams", "strict" => "no"),
					),
					"output" => array(
						"company" => array("varType" => "Company")
					),
					"httpauth" => "Y"
				),
				"CheckSections" => array(
					"type" => "public",
					"name" => "CheckSections",
					"input" => array(
						"XML_ID" => array("varType" => "string"),
						"TITLE" => array("varType" => "string")
					),
					"output" => array(
						"section" => array("varType" => "Sections")
					),
					"httpauth" => "Y"
				),
				"CheckLot" => array(
					"type" => "public",
					"name" => "CheckLot",
					"input" => array(
						"LOT" => array("varType" => "lot"),
						"TITLE" => array("varType" => "string"),
						"SPEC" => array(
							"varType" => "ArrayOfSpecProd",
							"arrType" => "specProd"
							),
						"SETTINGS" => array("varType" => "lotSettings", "strict" => "no"),
						
					),
					"output" => array(
						"lot" => array("varType" => "Lotout")
					),
					"httpauth" => "N"
				)
			)
		);

		return $wsdesc;
	}

	function TestComponent(){
		global $APPLICATION;
		$client = new CSOAPClient( "demo.tenderix.ru", $APPLICATION->GetCurPage() );
		$client->setLogin("admin");
		$client->setPassword("qweasdzxcrfvq");



		$request = new CSOAPRequest("CheckLot", CWebService::GetDefaultTargetNS());

		$request->addParameter("TITLE", 'Новый лот из 1С');
		$request->addParameter("LOT", array(
			"XML_ID" => "12345092123",
			"DATE_START" => "11.10.2015 10:32:27",
			"DATE_END" => "12.10.2015 20:32:27",
			"SECTION_XML_ID" => "81e0a9ce",
			"COMPANY_XML_ID" => "e77c72ef",
			"RESPONSIBLE_FIO" => "Vasya Pupkin sm",
			"RESPONSIBLE_PHONE" => "78912345678",
			"ACTIVE" => 'N',
			"TYPE_ID" => 'T',
			"BUYER_ID" => 122,

		));

		$request->addParameter("SETTINGS", array(
			"OPEN_PRICE" => 'true',
			"NOSAME" => 'false',
			"NOBAD" => 'true',
			"ONLY_BEST" => 'false',
			"SEND_SPEC" => 'false',
			"VIZ_HIST" => 'false',
			"PRE_PROPOSAL" => 'false',
			"NOEDIT" => 'false',
			"NOTVISIBLE_PROPOSAL" => 'false',
			"TIME_EXTENSION" => '0',
			"TIME_UPDATE" => '600',
			"WITH_NDS" => 'false',
			));

		$request->addParameter("SPEC", array(
			"1:ArrayOfsSpecProdEl" => array(
				"TITLE" => "new tovar 11",
				"XML_ID" => "a33b6a03-917c-11de-a428-005056c00001",
				"ID_POS" => "123",
				"ADD_INFO" => "infa k tovaru 1",
				"COUNT" => "10",
				"UNIT_XML_ID" => "a33b6a04",
				"START_PRICE" => "1000",
				"STEP_PRICE" => "100",
			),			
			"2:ArrayOfsSpecProdEl" => array(
				"TITLE" => "new tovar 33",
				"XML_ID" => "a33b6a03-917c-11de-a428-005056c00002",
				"ID_POS" => "124",
				"ADD_INFO" => "infa k tovaru 3",
				"COUNT" => "30",
				"UNIT_XML_ID" => "a33b6a04",
				"START_PRICE" => "3000",
				"STEP_PRICE" => "300",
			),
			"3:ArrayOfsSpecProdEl" => array(
				"TITLE" => "new tovar 77",
				"XML_ID" => "a33b6a03-917c-11de-a428-005056c00003",
				"ID_POS" => "125",
				"ADD_INFO" => "infa k tovaru 7",
				"COUNT" => "70",
				"UNIT_XML_ID" => "a33b6a04",
				"START_PRICE" => "7000",
				"STEP_PRICE" => "700",
			),
			"4:ArrayOfsSpecProdEl" => array(
				"TITLE" => "new tovar44 Измененный",
				"XML_ID" => "a33b6a03-917c-11de-a428-005056c00004",
				"ID_POS" => "126",
				"ADD_INFO" => "infa k tovaru 4",
				"COUNT" => "40",
				"UNIT_XML_ID" => "a33b6a04",
				"START_PRICE" => "4000",
				"STEP_PRICE" => "400",
			),
			"5:ArrayOfsSpecProdEl" => array(
				"TITLE" => "new tovar55",
				"XML_ID" => "a33b6a03-917c-11de-a428-005056c00005",
				"ID_POS" => "127",
				"ADD_INFO" => "infa k tovaru 5",
				"COUNT" => "50",
				"UNIT_XML_ID" => "a33b6a04",
				"START_PRICE" => "5000",
				"STEP_PRICE" => "500",
			),
			"6:ArrayOfsSpecProdEl" => array(
				"TITLE" => "new tovar99",
				"XML_ID" => "a33b6a03-917c-11de-a428-005056c00007",
				"ADD_INFO" => "infa k tovaru 9",
				"COUNT" => "90",
				"UNIT_XML_ID" => "a33b6a03",
				"START_PRICE" => "9000",
				"STEP_PRICE" => "900",
			)

			));


		$response = $client->send($request);

		if ($response->FaultString)
			echo $response->FaultString;
		else
			echo "<pre>".mydump($response->Value)."</pre>";

	}
}

$arParams["WEBSERVICE_NAME"] = "tenderix.web.server";
$arParams["WEBSERVICE_CLASS"] = "Tenderix";
$arParams["WEBSERVICE_MODULE"] = "";

$APPLICATION->IncludeComponent(
	"bitrix:webservice.server",
	"",
	$arParams
	);

die();



/*
				"PRIVATE_LIST" => $SETTING["PRIVATE_LIST"],
				"SUBSCR_NOW" => $SETTING["SUBSCR_NOW"] == "Y" ? "Y" : "N",
				"SUBSCR_START" => intval($SETTING["SUBSCR_START"]),
				"SUBSCR_END" => intval($SETTING["SUBSCR_END"]),	

				"TERM_PAYMENT_ID" => $_REQUEST["TERM_PAYMENT_ID"],
				"TERM_PAYMENT_VAL" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "" : $_REQUEST["TERM_PAYMENT_VAL"]),
				"TERM_PAYMENT_REQUIRED" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "N" : ($_REQUEST["TERM_PAYMENT_REQUIRED"] == "Y" ? "Y" : "N")),
				"TERM_PAYMENT_EDIT" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "N" : ($_REQUEST["TERM_PAYMENT_EDIT"] == "Y" ? "Y" : "N")),

				"TERM_DELIVERY_ID" => $_REQUEST["TERM_DELIVERY_ID"],
				"TERM_DELIVERY_VAL" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "" : $_REQUEST["TERM_DELIVERY_VAL"]),
				"TERM_DELIVERY_REQUIRED" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "N" : ($_REQUEST["TERM_DELIVERY_REQUIRED"] == "Y" ? "Y" : "N")),
				"TERM_DELIVERY_EDIT" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "N" : ($_REQUEST["TERM_DELIVERY_EDIT"] == "Y" ? "Y" : "N")),
*/

?>