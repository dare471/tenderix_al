<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix")) {
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/pweb.tenderix/list.suppliers/class.php");
// require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/currency/lib/currencymanager.php");
// use Bitrix\Currency;

$module_id = 'pweb.tenderix';

function PriceFormat($price)
{
    $price = str_replace(",", ".", $price);
	$price = str_replace(" ", "", $price);
    $price = floatval($price);
    if ($price < 0)
        return 0;
    return $price;
}

//Валюты 08.11.2017
function convert_to_user_curr(&$fields) {
	
	$base_cur = $fields['CURRENCY_PROPOSAL'];

	foreach($fields['CURR'] as $curr => $rate) {
		$fields['CURR'][$curr]= $rate / $fields['CURR'][$base_cur];
	}

	$currency = $fields['CURR_USER'];

	$rate = $fields['CURR'][$currency]; 
	
	$prop_keys = preg_grep("/^PROP_\d+_.+$/", array_keys($fields));
	
	foreach($prop_keys as $key) {
		$fields[$key] = PriceFormat($fields[$key])*$rate;
	}	
	
	
	//echo '<pre>'; print_r($fields); echo '</pre>';
	//die();
}

function convert_to_lot_curr(&$curr_name, $base_cur) {
	foreach ($curr_name as $curr => $rate) {
		$curr_name[$curr] = $rate / $curr_name[$base_cur];
	}
}
//
/* function PriceFormat2($price)
{
    $price = str_replace(",", "", $price);
    $price = floatval($price);
    if ($price < 0)
        return 0;
    return $price;
} */

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();
$arResult['T_RIGHT'] = $T_RIGHT;
$arResult['S_RIGHT'] = $S_RIGHT;

if ($T_RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

if (($S_RIGHT == "D" && $T_RIGHT == "P") || $S_RIGHT == "A") {
    ShowError(GetMessage("ACCESS_DENIED"));
    header('Location: /');
    return;
}

$timeZone = time() + CTimeZone::GetOffset();

$arResult["PROPOSAL_ID"] = $_REQUEST["PROPOSAL_ID"] ? $_REQUEST["PROPOSAL_ID"] : 0;
//print_r($arResult);

if (CModule::IncludeModule("currency")) {
    $lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
    while ($lcur_res = $lcur->Fetch()) {
        $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
        $arCur = $rsCur->Fetch();
        $arResult["CURRENCY"][$lcur_res["CURRENCY"]] = array(
            "RATE" => CurrencyFormat($arCur["RATE"], $lcur_res["CURRENCY"]),
            "RATE_NUM" => $arCur["RATE"],
            "DATE_RATE" => $arCur["DATE_RATE"],
            "ID" => $arCur["ID"]
        );
        $curr_name[$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? $arCur["RATE"] : 1;
    }
}



//if ($T_RIGHT == "W" || ($S_RIGHT == "W" && $T_RIGHT == "P")) {
if ($T_RIGHT == "W" || $T_RIGHT == "P") {
    $new_proposal = false;
    $LOT_ID = intval($_REQUEST["LOT_ID"]);
    if ($LOT_ID <= 0) {
        ShowError(GetMessage("PW_TD_LOT_NOTFOUND"));
        return;
    }

    $rsLot = CTenderixLot::GetByIDa($LOT_ID);
    $arLot = $rsLot->Fetch();
    $arParams["NDS_TYPE"] = $arLot["WITH_NDS"];

    $date_start = strtotime($arLot["DATE_START"]);
    $date_tek = time();

//редактируем
    if (isset($_REQUEST["proposal_submit"])) {
		
		global $USER;
		
		$time_end = strtotime($arLot["DATE_END"]);
		$timeZone = time() + CTimeZone::GetOffset();
		
		if ($time_end < $timeZone) {
            ShowError(GetMessage("PW_TD_DATE_END_LOT"));
            return;
        }
		
		if ($_REQUEST['CURRENCY_PROPOSAL'] != $_REQUEST['CURR_USER']) {
			convert_to_user_curr($_REQUEST);
		}
		
		convert_to_lot_curr($curr_name, $_REQUEST['CURRENCY_PROPOSAL']);
		
        $arFields["TERM_PAYMENT_VAL"] = $_REQUEST["TERM_PAYMENT_VAL"];
        $arFields["TERM_DELIVERY_VAL"] = $_REQUEST["TERM_DELIVERY_VAL"];
		
        $arFields["LOT_ID"] = $LOT_ID;
		
        if (!isset($_REQUEST["PROPOSAL_ID"])) {
            $arFields["USER_ID"] = $USER->GetID();
        }
        $arFields["CURRENCY"] = $_REQUEST["CURRENCY_PROPOSAL"];
        $arFields["MESSAGE"] = $_REQUEST["MESSAGE"];
        $arFields["DATE_START"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), $timeZone);
		
		
		
		
        //property
        if (is_array($_REQUEST["FILE_ID_PROP"])) {
            foreach ($_REQUEST["FILE_ID_PROP"] as $file)
                CTenderixProposal::DeleteFileProperty($arResult["PROPOSAL_ID"], $file);
        }

        $arPropDop = array("PROPERTY" => $_REQUEST["PROP"], "FILES" => $_FILES["PROP"]);
        $rsPropS = CTenderixProposalProperty::GetList($by="", $order="", $arFilter=Array());
        $arParams["PROPERTY"] = $arLot["TYPE_ID"] == "P" ? $arParams["PROPERTY2"] : $arParams["PROPERTY"];
        $arParams["PROPERTY_REQUIRED"] = $arLot["TYPE_ID"] == "P" ? $arParams["PROPERTY_REQUIRED2"] : $arParams["PROPERTY_REQUIRED"];
        while ($arPropS = $rsPropS->Fetch()) {
	        // print_r($arProps);
            if (in_array("PROP_" . $arPropS["ID"], $arParams["PROPERTY"]) || !isset($arParams["PROPERTY"])) {
                if (isset($arParams["PROPERTY"])) {
                    $arPropS["IS_REQUIRED"] = in_array("PROP_" . $arPropS["ID"], $arParams["PROPERTY_REQUIRED"]) ? "Y" : "N";
                }
                $arrPropS[$arPropS["ID"]] = $arPropS;
            }
        }
        $arPropDop["PROPERTY_S"] = $arrPropS;
        $arFields["PROPERTY"] = $arPropDop;
		

		
		
        // проверка лучшей ставки
        //__($arLot);
        if (($arLot["OPEN_PRICE"] == "Y") && (($arLot["NOSAME"] == "Y") || ($arLot["NOBAD"] == "Y"))) {
            if (CModule::IncludeModule("currency")) {
                $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "asc", $arFilter = Array());
                while ($arCur = $rsCur->Fetch()) {
                    $arrCur[$arCur["CURRENCY"]] = $arCur["RATE"];
                }
            }
			
            $curr_user = floatval($arrCur[$CURR]) > 0 ? floatval($arrCur[$CURR]) : 1;
            //best proposal N tovar -->>
            if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {
                $arProposalMin = array();
                $arProposalSpec = CTenderixProposal::GetListSpecPrice($LOT_ID);

                foreach ($arProposalSpec as $proposalBuyerId => $proposalValue) {
                    $arProposalMin[$proposalBuyerId] = floatval($proposalValue["MIN"]) / $curr_user;
                    $arProposalMax[$proposalBuyerId] = floatval($proposalValue["MAX"]) / $curr_user;
                }				
				
                //$arProposalMin = $arLot["TYPE_ID"] == "N" ? json_encode($arProposalMin) : json_encode($arProposalMax);
                $arProposalMin = ($arLot["TYPE_ID"] == "N" ? $arProposalMin : $arProposalMax);

                //__($arProposalMin);
                /////////////если есть предложения
                $ths_prop = array();
                $rsProposal33 = CTenderixProposal::GetList(array("LOT_ID" => $LOT_ID, "USER_ID" => $USER->GetID()));
                if ($arProposal33 = $rsProposal33->Fetch()) {
                    /*$arResult["PROPOSAL_ID"] = $arProposal["ID"];
                    $arResult["CURRENCY_PROPOSAL"] = $arProposal["CURRENCY"];
                    $arResult["MESSAGE"] = $arProposal["MESSAGE"];*/
                    $curr_list33 = CTenderixCurrency::GetListProposal(strtotime($arProposal33["DATE_START"]));

                    $arFieldsSpec33 = array();
                    $rsProposalSpec33 = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal33["ID"]));
                    while ($arProposalSpec33 = $rsProposalSpec33->Fetch()) {
                        $arFieldsSpec33[$arProposalSpec33["PROPERTY_BUYER_ID"]]["PROPERTY_BUYER_ID"] = $arProposalSpec33["PROPERTY_BUYER_ID"];
                        $arFieldsSpec33[$arProposalSpec33["PROPERTY_BUYER_ID"]]["NDS"] = $arProposalSpec33["NDS"];
                        $arFieldsSpec33[$arProposalSpec33["PROPERTY_BUYER_ID"]] = floatval($arProposalSpec33["PRICE_NDS"]) / (floatval($curr_list33[$arProposal33["CURRENCY"]]) <= 0 ? 1 : floatval($curr_list33[$arProposal33["CURRENCY"]]));
                    }
                    $ths_prop = $arFieldsSpec33;
                }

                $arrProposalSpecCurUser = array();
                $arrProposalSpecCurUser = CTenderixProposal::GetListPriceCurUser($LOT_ID);

                //__("arrProposalSpecCurUser");
                //__($arrProposalSpecCurUser);

                ////////////
            }

            //best proposal N tovar <<--
            //best proposal S tovar -->>
            if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
                $arProposalMin = "";
                $arProductsPrice = CTenderixProposal::GetListProductsPrice($LOT_ID);
                $arProposalMin = $arProductsPrice["MIN"] / $curr_user;
            }
            //best proposal S tovar <<--
        }
        // проверка лучшей ставки - конец

        // сюда вставляем определение начальных цен в лоте
        if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
            $rsProdBuyer3 = CTenderixProducts::GetListBuyer(array("LOT_ID" => $arLot["ID"]));
            $arProdBuyer3 = $rsProdBuyer3->Fetch();

            $rsProd3 = CTenderixProducts::GetList($by, $order, array("ID" => $arProdBuyer3["PRODUCTS_ID"]), $is_filtered);
            $arProd3 = $rsProd3->Fetch();

            $rsProdProps3 = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $arProdBuyer3["PRODUCTS_ID"]), $is_filtered);
            while ($arProdProps3 = $rsProdProps3->GetNext()) {
                $rsProps2 = CTenderixProductsProperty::GetListBuyer(Array("PRODUCTS_ID" => $arProdBuyer3["ID"], "PRODUCTS_PROPERTY_ID" => $arProdProps3["ID"]));
                $arProps2 = $rsProps2->Fetch();
                $arrPropProduct[$arProdProps3["ID"]] = $arProdProps3;
                $arrPropProductBuyer[$arProps2["PRODUCTS_PROPERTY_ID"]] = $arProps2;
            }
            $arProdBuyer3["START_PRICE"] = floatval($arProdBuyer3["START_PRICE"]);
            $arResult3["PRODUCT_BUYER"] = $arProdBuyer3;
            $startpr = $arResult3["PRODUCT_BUYER"]["START_PRICE"];
        }
        $titles = array();
        if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {
            $arResult3["SPEC"] = CTenderixLotSpec::GetByLotId($arLot["ID"]);
            $rsProp3 = CTenderixLotSpec::GetListProp($arLot["ID"]);
            while ($arProp3 = $rsProp3->Fetch()) {
                $arProp3["START_PRICE"] = floatval($arProp3["START_PRICE"]);
                $arResult3["PROPERTY_SPEC"][$arProp3["ID"]] = $arProp3;
                $startpr[$arProp3["ID"]] = $arResult3["PROPERTY_SPEC"][$arProp3["ID"]]["START_PRICE"];
                $titles[$arProp3["ID"]] = $arResult3["PROPERTY_SPEC"][$arProp3["ID"]]["TITLE"];
            }
        }

        //__($_REQUEST);

        // начальные цены лота - конец
        $ex2 = "";
        //property
        if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
            $arFieldsPropertyProducts = array();
            //print_r($_REQUEST["PROPS"]);
            foreach ($_REQUEST["PROPS"] as $idProp => $valueProp) {
                $arFieldsPropertyProducts[$idProp]["PRODUCTS_PROPERTY_BUYER_ID"] = $idProp;
                $arFieldsPropertyProducts[$idProp]["VALUE"] = $valueProp;
            }
            if (($arProposalMin == $_REQUEST["PRICE_NDS"]) && ($arLot["NOSAME"] == "Y") && ($date_tek > $date_start)) {
                $ex2 .= "Цена не может быть равна текущей лучшей ставке " . $arProposalMin . " рублей.<br />";
            }
            if (($arProposalMin < $_REQUEST["PRICE_NDS"]) && ($arLot["NOBAD"] == "Y") && ($arProposalMin != "") && ($date_tek > $date_start)) {
                $ex2 .= "Цена не может быть больше текущей лучшей ставки " . $arProposalMin . " рублей.<br />";
            }
            if (($startpr < $_REQUEST["PRICE_NDS"]) && ($arLot["ONLY_BEST"] == "Y") && ($startpr != "")) {
                $ex2 .= "Цена не может быть больше начальной ставки " . $startpr . " рублей.<br />";
            }
            $check_fields = CTenderixProposal::CheckFields($arFields, $arFieldsPropertyProducts);
        }

        if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {
            //echo "<pre>";print_r($arProposalMin);echo "</pre>";
            $arFilter = array(
                "LOT_ID" => $arParams["LOT_ID"]
            );
            $rsProposal = CTenderixProposal::GetList($arFilter); //предложения

            $arFieldsSpec = array();
            $rsProp = CTenderixLotSpec::GetListProp($LOT_ID); //спец

            $rsProp1 = CTenderixLotSpec::GetListProp($LOT_ID);


            //__("arrProp1"); //предложения
//                $arrProp1 = array();
//                while ($arrProp = $rsProposal->GetNext()) {
//                    $arrProp1[] = $arrProp;
//                }
            //__($arrProp1);

                while ($arProp = $rsProp->GetNext()) {

                    $arFieldsBuyer["FULL_SPEC"] = $arProp["FULL_SPEC"];
                    $arFieldsSpec[$arProp["ID"]]["PROPERTY_BUYER_ID"] = $arProp["ID"];
                    $arFieldsSpec[$arProp["ID"]]["NDS"] = $_REQUEST["PROP_" . $arProp["ID"] . "_NDS"];
                    $arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] = PriceFormat($_REQUEST["PROP_" . $arProp["ID"] . "_PRICE_NDS"]) * floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]);
                    // $arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] /= 100; // Новая фишка (из-за маски ввода)
					$arFieldsSpec[$arProp["ID"]]["ANALOG"] = trim($_REQUEST["PROP_" . $arProp["ID"] . "_ANALOG"]);
                    $arFieldsSpec[$arProp["ID"]]["DATE_START"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), $timeZone);

//                    __("arProposalSpec");
//                    __($arProposalSpec);
//                    __("arProp");
//                    __($arProp);
//                    __("arFieldsSpec");
//                    __($arFieldsSpec);
//                    __($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] / floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]));


                    foreach($arrProposalSpecCurUser as $user) {
                        foreach($user as $spec_id) {
                            if($spec_id["PROPERTY_BUYER_ID"] == $arProp["ID"]) {
                                if(($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] / floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]])) == $spec_id["PRICE_NDS"] && ($spec_id["USER_ID"] != $USER->GetID())) {
                                    $ex2 .= "Цена " . $titles[$arProp["ID"]] . " не может быть равна текущей лучшей ставке " . $arProposalMin[$arProp["ID"]] . " " . $_REQUEST["CURRENCY_PROPOSAL"] . ".<br />";
                                }
                            }
                        }
                    }


//                    foreach($arrProp1 as $arrProp_key => $arrProp_val) {
//                        if($arrProp_val["ID"] == $arrProposalSpecCurUser[$arrProp_val["USER_ID"]][$arProp["ID"]]["PROPOSAL_ID"]) {
//                            __("arProposalMin");
//                            __($arProposalMin);
//                            __($arProposalMin[$arProp["ID"]]);
//                            __("arFieldsSpec");
//                            __($arFieldsSpec);
//                            __($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] / floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]));
//                            __("arPropUsID");
//                            __($arrProp_val["USER_ID"]);
//                            __("USER_ID");
//                            __($USER->GetID());
//
//
//                            if (($arProposalMin[$arProp["ID"]] == ($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] / floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]))) && ($arLot["NOSAME"] == "Y") && ($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] != 0) && ($date_tek > $date_start) && ($arrProp_val["USER_ID"] != $USER->GetID())) {
//                                $ex2 .= "Цена " . $titles[$arProp["ID"]] . " не может быть равна текущей лучшей ставке " . $arProposalMin[$arProp["ID"]] . " " . $_REQUEST["CURRENCY_PROPOSAL"] . ".<br />";
//                            }
//                        }
//                    }

                    if (($startpr[$arProp["ID"]] < ($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] / floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]))) /*&& ($arLot["ONLY_BEST"] == "Y")*/ && ($arLot["TYPE_ID"] == "N") && ($startpr[$arProp["ID"]] != "")) {
                        $ex2 .= "Цена " . $titles[$arProp["ID"]] . " не может быть больше начальной ставки " . $startpr[$arProp["ID"]] . " " . $_REQUEST["CURRENCY_PROPOSAL"] . ".<br />";
                    }
                    if (($startpr[$arProp["ID"]] > ($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] / floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]))) /*&& ($arLot["ONLY_BEST"] == "Y")*/ && ($arLot["TYPE_ID"] == "P") && ($startpr[$arProp["ID"]] != "") && ($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] != 0)) {
                        $ex2 .= "Цена " . $titles[$arProp["ID"]] . " не может быть меньше начальной ставки " . $startpr[$arProp["ID"]] . " " . $_REQUEST["CURRENCY_PROPOSAL"] . ".<br />";
                    }

                   /* if (($ex2 != "") && count($ths_prop) > 0) {
                        $arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] = $ths_prop[$arProp["ID"]];
                    } elseif (($ex2 != "") && count($ths_prop) == 0) {
                        $arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] = 0;
                    }*/

                    /*if (($arProposalMin[$arProp["ID"]] < $arFieldsSpec[$arProp["ID"]]["PRICE_NDS"]) && ($arLot["NOBAD"] == "Y") && ($arLot["TYPE_ID"] == "N") && ($arProposalMin[$arProp["ID"]] != "") && ($date_tek > $date_start)) {
                        $ex2 .= "Цена " . $titles[$arProp["ID"]] . " не может быть больше текущей лучшей ставки " . $arProposalMin[$arProp["ID"]] . " рублей.<br />";
                    }
                    if (($arProposalMin[$arProp["ID"]] > $arFieldsSpec[$arProp["ID"]]["PRICE_NDS"]) && ($arLot["NOBAD"] == "Y") && ($arLot["TYPE_ID"] == "P") && ($arProposalMin[$arProp["ID"]] != "") && ($arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] != 0) && ($date_tek > $date_start)) {
                        $ex2 .= "Цена " . $titles[$arProp["ID"]] . " не может быть меньше текущей лучшей ставки " . $arProposalMin[$arProp["ID"]] . " рублей.<br />";
                    }*/

                }

            //__($ex2);
            //die();

            //__("arFieldsSpec");
            //__($arFieldsSpec);

            $check_fields = CTenderixProposal::CheckFields($arFields, $arFieldsSpec, $arFieldsBuyer);
			
            //echo "<pre>";print_r($arFieldsSpec);echo "</pre>";
            
        }


        if (($check_fields) && $ex2 == "") {

        //if ($check_fields) {
            //__($_REQUEST);
            //__($arFields);
            //die();

            if (isset($_REQUEST["PROPOSAL_ID"]) && $_REQUEST["PROPOSAL_ID"] > 0) {
                $ID = $_REQUEST["PROPOSAL_ID"];

                    $ID = CTenderixProposal::Update($ID, $arFields);

            } else {
                $rsLotAdd = CTenderixProposal::GetList(array("LOT_ID" => $LOT_ID, "USER_ID" => $USER->GetID()));
                if(!$rsLotAdd->Fetch()) {
                    $ID = CTenderixProposal::Add($arFields);
                    if (intval($ID) > 0)
                        $new_proposal = true;
                }
            }

            if (intval($ID) > 0) {
				
				/* Поведение, которое должно быть при подаче предложения */
				if (intval($arLot["TIME_EXTENSION"]) > 0) {
					$rsProposalExtension = CTenderixProposal::GetList(array("LOT_ID" => $LOT_ID));
					while ($arProposalExtension = $rsProposalExtension->GetNext()) {
						//echo '<pre>'; print_r($arProposalExtension); echo '</pre>';
						$rsSpecHistoryExtension = CTenderixProposal::GetSpecHistory(array("PROPOSAL_ID" => $arProposalExtension["ID"]));
						while($arHistoryExtension = $rsSpecHistoryExtension->Fetch()) {
							//echo '<pre>'; print_r($arHistory);
							// $arResult["PROPOSAL"][$arHistory['PROPOSAL_ID']]["HISTORY"][$arHistory['DATE_START']][$arHistory['PROPERTY_BUYER_ID']] = $arHistory;
							//$arHistoryExtension
							$arHistoryResultExtension[$arHistoryExtension['DATE_START']][$arHistoryExtension['PROPOSAL_ID']][$arHistoryExtension['PROPERTY_BUYER_ID']] = $arHistoryExtension;
							
						}
					}
					krsort($arHistoryResultExtension);
					if (is_array($arHistoryResultExtension) && count($arHistoryResultExtension) > 0) {
						$specLastArrayExtension = array_shift($arHistoryResultExtension);
						if (is_array($specLastArrayExtension) && count($specLastArrayExtension) > 0) {
							reset($specLastArrayExtension);
							$proposalIDExtension = key($specLastArrayExtension);
						}
					}
					if ($proposalIDExtension == null || $proposalIDExtension != $ID ) {
						//Нужно проверить кто последний подал предложение
						$time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
						$time_end = date("d.m.Y H:i:s", $time_end);
						$result = CTenderixLot::Update($arLot['ID'], array('DATE_END' => $time_end), true);
						/* Добавлено 04.10.2017 */
					}
				}
				
                $arResult["PROPOSAL_ID"] = $ID;
                CTenderixProposal::SetProperty($ID, $arPropDop);

                //File add
                if (is_array($_REQUEST["FILE_ID"]))
                    foreach ($_REQUEST["FILE_ID"] as $file)
                        CTenderixProposal::DeleteFile($ID, $file);
                if (is_array($_FILES["NEW_FILE"]))
                    foreach ($_FILES["NEW_FILE"] as $attribute => $files)
                        if (is_array($files))
                            foreach ($files as $index => $value)
                                $arFiles[$index][$attribute] = $value;

                foreach ($arFiles as $file) {
                    if (strlen($file["name"]) > 0 && intval($file["size"]) > 0) {
                        $res_file = CTenderixProposal::SaveFile($ID, $file);
                        if (!$res_file)
                            break;
                    }
                }

                if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
                    $arFieldsPropertyProducts = array();
                    foreach ($_REQUEST["PROPS"] as $idProp => $valueProp) {
                        $arFieldsPropertyProducts[$idProp]["PRODUCTS_PROPERTY_BUYER_ID"] = $idProp;
                        $arFieldsPropertyProducts[$idProp]["PROPOSAL_ID"] = $ID;
                        $arFieldsPropertyProducts[$idProp]["VALUE"] = $valueProp;
                    }
                    if (!isset($_REQUEST["PROPOSAL_ID"])) {
                        CTenderixProposal::AddPropertyProducts($arFieldsPropertyProducts);
                    } else {
                        CTenderixProposal::UpdatePropertyProducts($arFieldsPropertyProducts);
                    }

                    foreach ($_REQUEST["PRICE_NDS"] as $k => $price) {
                        $arFieldsProducts = array();
                        $arFieldsProducts["PROPOSAL_ID"] = $ID;
                        $arFieldsProducts["PROD_BUYER_ID"] = $k;
                        $arFieldsProducts["NDS"] = $_REQUEST["NDS"][$k];
                        $arFieldsProducts["PRICE_NDS"] = PriceFormat($_REQUEST["PRICE_NDS"][$k]) * floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]);
                        $arFieldsProducts["COUNT"] = $_REQUEST["COUNT"][$k];
                        $arFieldsProducts["DATE_START"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), $timeZone);

                        CTenderixProposal::AddProductsHistory($arFieldsProducts, $LOT_ID);

                        if (!isset($_REQUEST["PROPOSAL_ID"])) {
                            CTenderixProposal::AddProducts($arFieldsProducts);
                            CTenderixLog::Log("PROPOSAL_ADD", array("ID" => $ID, "FIELDS" => $arFields, "PRODUCTS" => $arFieldsProducts));
                        } else {
                            CTenderixProposal::UpdateProducts2($arFieldsProducts);
                            CTenderixLog::Log("PROPOSAL_UPDATE", array("ID" => $ID, "FIELDS" => $arFields, "PRODUCTS" => $arFieldsProducts));
                        }
                        CTenderixStatistic::UpdatePriceForS($LOT_ID);
                    }

                    //event TENDERIX_BEST_PROPOSAL -->
                    if ($arLot["OPEN_PRICE"] == "Y") {
                        $rsBestProposal = CTenderixProposal::GetListProductsPriceAll($LOT_ID);
                        while ($arBestProposal = $rsBestProposal->Fetch()) {
                            $arrBestProposal[$arBestProposal["PROPOSAL_ID"]] += $arBestProposal["PRICE_NDS"];
                        }
                    }
                    //event TENDERIX_BEST_PROPOSAL <--
                }
                if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {

                    foreach ($arFieldsSpec as $key => $val) {
                        $arFieldsSpec[$key]["PROPOSAL_ID"] = $ID;
                    }

                    CTenderixProposal::AddSpecHistory($arFieldsSpec, $arParams);

                    if (!isset($_REQUEST["PROPOSAL_ID"])) {
                        if (!CTenderixProposal::AddSpec($arFieldsSpec)) {
                            ShowError(GetMessage("PW_TD_EDIT_PROPOSAL"));
                            return;
                        }
                        CTenderixLog::Log("PROPOSAL_ADD", array("ID" => $ID, "FIELDS" => $arFields, "SPEC" => $arFieldsSpec));
                    } else {
                        if (!CTenderixProposal::UpdateSpec($arFieldsSpec)) {
                            ShowError(GetMessage("PW_TD_EDIT_PROPOSAL"));
                            return;
                        }
                        CTenderixLog::Log("PROPOSAL_UPDATE", array("ID" => $ID, "FIELDS" => $arFields, "SPEC" => $arFieldsSpec));
                    }
                    CTenderixStatistic::UpdatePriceForN($LOT_ID);

                    //event TENDERIX_BEST_PROPOSAL -->
                    if ($arLot["OPEN_PRICE"] == "Y") {
                        $rsBestProposal = CTenderixProposal::GetListSpecPriceAll($LOT_ID);
                        while ($arBestProposal = $rsBestProposal->Fetch()) {
                            $arrBestProposal[$arBestProposal["PROPOSAL_ID"]] += $arBestProposal["PRICE_NDS"];
                            echo $arBestProposal["PROPOSAL_ID"] . " " . $arBestProposal["PRICE_NDS"] . "<br />";
                        }
                    }
                    //event TENDERIX_BEST_PROPOSAL <--
                }

                //event TENDERIX_BEST_PROPOSAL -->
                if (($arLot["OPEN_PRICE"] == "Y") && ($date_tek > $date_start)) {
				
                    ////// получаем полную спецификацию - начало
                    if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {
                        /////////////если есть предложения
                        $this_user = $USER->GetID();
                        $ths_prop77 = array();
                        $tovar_title = array();
                        $rsProposal77 = CTenderixProposal::GetList(array("LOT_ID" => $LOT_ID));
                        while ($arProposal77 = $rsProposal77->Fetch()) {
                            /*echo "<pre>";
                            print_r($arProposal77);
                            echo "</pre>";*/
                            $ths_prop77[$arProposal77["USER_ID"]]["INFO"]["LAST_NAME"] = $arProposal77["LAST_NAME"];
                            $ths_prop77[$arProposal77["USER_ID"]]["INFO"]["NAME"] = $arProposal77["NAME"];
                            $ths_prop77[$arProposal77["USER_ID"]]["INFO"]["SECOND_NAME"] = $arProposal77["SECOND_NAME"];
                            $ths_prop77[$arProposal77["USER_ID"]]["INFO"]["EMAIL"] = $arProposal77["EMAIL"];
                            $curr_list77 = CTenderixCurrency::GetListProposal(strtotime($arProposal77["DATE_START"]));

                            $arFieldsSpec77 = array();
                            $rsProposalSpec77 = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal77["ID"]));
                            while ($arProposalSpec77 = $rsProposalSpec77->Fetch()) {
                                $tovar_title[$arProposalSpec77["PROPERTY_BUYER_ID"]] = $arProposalSpec77["TITLE"];
                                $arFieldsSpec77[$arProposalSpec77["PROPERTY_BUYER_ID"]] = floatval($arProposalSpec77["PRICE_NDS"]) / (floatval($curr_list77[$arProposal77["CURRENCY"]]) <= 0 ? 1 : floatval($curr_list77[$arProposal77["CURRENCY"]]));
                            }
                            $ths_prop77[$arProposal77["USER_ID"]]["SPEC"] = $arFieldsSpec77;
                        }
                        ////////////
                        /*echo "<pre>";
                        print_r($tovar_title);
                        echo "</pre>";*/
                        $arrSITE = CTenderixLot::GetSite();
                        $COMPANY = CTenderixCompany::GetByIdName($arLot["COMPANY_ID"]);
                        foreach ($ths_prop77 as $us_id => $us_info) {
                            /*echo "<pre>";
                            print_r($us_info);
                            echo "</pre>";*/
                            foreach ($us_info["SPEC"] as $id_prop => $price) {
                                if ((($price > $ths_prop77[$this_user]["SPEC"][$id_prop]) && $arLot["TYPE_ID"] == "N" && $price != 0 && ($ths_prop77[$this_user]["SPEC"][$id_prop] != 0)) || (($price < $ths_prop77[$this_user]["SPEC"][$id_prop]) && $arLot["TYPE_ID"] == "P" && $price != 0 && ($ths_prop77[$this_user]["SPEC"][$id_prop] != 0))) {
                                    echo "Ваше предложение ".$price." руб по товару ".$tovar_title[$id_prop]." улучшено другим поставщиком - ".$ths_prop77[$this_user]["SPEC"][$id_prop]."руб.<br />";
                                    $arEventFields = array(
                                        "LOT_NUM" => $arLot["ID"],
                                        "LOT_NAME" => $arLot["TITLE"],
                                        "SUPPLIER" => $us_info["INFO"]["LAST_NAME"] . " " . $us_info["INFO"]["NAME"] . " " . $us_info["INFO"]["SECOND_NAME"],
                                        "COMPANY" => $COMPANY,
                                        "RESPONSIBLE_FIO" => $arLot["RESPONSIBLE_FIO"],
                                        "RESPONSIBLE_PHONE" => $arLot["RESPONSIBLE_PHONE"],
                                        "DATE_START" => $arLot["DATE_START"],
                                        "DATE_END" => $arLot["DATE_END"],
                                        "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                                        "EMAIL_TO" => $us_info["INFO"]["EMAIL"],
                                        "TOVAR" => $tovar_title[$id_prop],
                                        "PRICEP" => $price,
                                        "PRICEBEST" => $ths_prop77[$this_user]["SPEC"][$id_prop],
                                    );
                                    CEvent::Send("TENDERIX_BEST_PROPOSAL2", $arrSITE, $arEventFields, "N"); //Че за хрень!!!!
									CTenderixLog::Log("TENDERIX_BEST_PROPOSAL2", array("ID" => $arLot["ID"], "FIELDS" => $arEventFields));
                                }
                            }
                        }
                    }

                    ////// получаем полную спецификацию - конец
                    if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
                        $arrBestProposalCurr = $arrBestProposal[$ID];
                        unset($arrBestProposal[$ID]);
                        $arrSITE = CTenderixLot::GetSite();
                        $COMPANY = CTenderixCompany::GetByIdName($arLot["COMPANY_ID"]);
                        foreach ($arrBestProposal as $arrBestProposalID => $arrBestProposalPrice) {
                            if ($arrBestProposalPrice > $arrBestProposalCurr) {
                                $rsProposalID = CTenderixProposal::GetList(array("ID" => $arrBestProposalID));
                                if ($arProposalID = $rsProposalID->Fetch()) {
                                    $arEventFields = array(
                                        "LOT_NUM" => $arLot["ID"],
                                        "LOT_NAME" => $arLot["TITLE"],
                                        "SUPPLIER" => $arProposalID["LAST_NAME"] . " " . $arProposalID["NAME"] . " " . $arProposalID["SECOND_NAME"],
                                        "COMPANY" => $COMPANY,
                                        "RESPONSIBLE_FIO" => $arLot["RESPONSIBLE_FIO"],
                                        "RESPONSIBLE_PHONE" => $arLot["RESPONSIBLE_PHONE"],
                                        "DATE_START" => $arLot["DATE_START"],
                                        "DATE_END" => $arLot["DATE_END"],
                                        "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                                        "EMAIL_TO" => $arProposalID["EMAIL"],
                                    );
                                    CEvent::Send("TENDERIX_BEST_PROPOSAL", $arrSITE, $arEventFields, "N");
									CTenderixLog::Log("TENDERIX_BEST_PROPOSAL", array("ID" => $arLot["ID"], "FIELDS" => $arEventFields));
                                }
                            }
                        }
                    }
                }
                //event TENDERIX_BEST_PROPOSAL <--
                //event TENDERIX_NEW_PROPOSAL -->
                if ($new_proposal) {
                    $rsBuyer = CTenderixUserBuyer::GetByID($arLot["BUYER_ID"]);
                    $arBuyer = $rsBuyer->Fetch();

                    $rsSupplier = CTenderixUserSupplier::GetByID($USER->GetID());
                    if ($arSupplier = $rsSupplier->Fetch()) {
                        $arEventFields = array(
                            "LOT_NUM" => $arLot["ID"],
                            "LOT_NAME" => $arLot["TITLE"],
                            "BUYER" => $arBuyer["FIO"],
                            "COMPANY" => $arSupplier["NAME_COMPANY"],
                            "DATE_START" => $arFields["DATE_START"],
                            "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                            "EMAIL_TO" => $arBuyer["EMAIL"],
                        );
                        $arrSITE = CTenderixLot::GetSite();
                        CEvent::Send("TENDERIX_NEW_PROPOSAL", $arrSITE, $arEventFields, "N");
						CTenderixLog::Log("TENDERIX_NEW_PROPOSAL", array("ID" => $arLot["ID"], "FIELDS" => $arEventFields));
                    }
                }
                //event TENDERIX_NEW_PROPOSAL <--
            }

            $sRedirectUrl = $APPLICATION->GetCurPage() . "?LOT_ID=" . $LOT_ID;

            if ((!$ex = $APPLICATION->GetException()) && ($ex2 == "")) {
                $_SESSION["SEND_OK"] = "Y";
                LocalRedirect($sRedirectUrl);
                exit();
            }
        }
    }


    if ($ex = $APPLICATION->GetException()) {
        $e = new CAdminException();
        $arResult["ERRORS_ARRAY"] = $ex->GetMessages();
        $arResult["ERRORS"] = $ex->GetString();
    } elseif ($ex2 != "") {
        $arResult["ERRORS2"] = $ex2;
    } elseif ($_SESSION["SEND_OK"] == "Y") {
        unset($_SESSION["SEND_OK"]);
        $arResult["SEND_OK"] = "Y";
    }


    $arParams["LOT_ID"] = intval($arParams["~LOT_ID"]);

    $arFilter = array(
        "ID" => $arParams["LOT_ID"]
    );
    $res = CTenderixLot::GetList($by = "", $order = "", $arFilter);

    if ($arLots = $res->GetNext()) {
        if ($S_RIGHT == "A" && $arLots["TYPE_ID"] != "P" && $T_RIGHT != "W") {
	        echo "<div class='alert alert-danger'>";
            ShowError(GetMessage("ACCESS_DENIED"));
            echo "</div>";
            header("Location: /");
            return;
        }

        $arResult["TIME"] = "";

        $time_start = strtotime($arLots["DATE_START"]);
        if (($time_start > $timeZone) && ($arLots["PRE_PROPOSAL"] != "Y")) {
			$arResult["TIME_S_MESSAGE"] = GetMessage("PW_TD_DATE_START_LOT") . ": " . $arLots["DATE_START"];
            $arResult["TIME"] = "S";
            //return;
        }

        //$time_end = strtotime($arLots["DATE_END"]) + intval($arLots["TIME_EXTENSION"]);
        $time_end = strtotime($arLots["DATE_END"]);
        $time_diff = $time_end - $timeZone; //echo $time_diff." ".$timeZone." ".CTimeZone::GetOffset();
		
        //$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/components/pweb.tenderix/proposal.add/ajax.php"></script>', true);
        //$APPLICATION->AddHeadScript(substr(dirname(__FILE__), strpos(__FILE__, "/bitrix/")) . "/ajax.php");

        echo '<input type="hidden" id="lot_id" value="' . $arLots["ID"] . '" />';
        echo '<input type="hidden" id="time_diff" value="' . $time_diff . '" />';

        $arResult["LOT"] = $arLots;
		
		 if ($time_end < $timeZone) {
            //ShowError(GetMessage("PW_TD_DATE_END_LOT"));
			$arResult["LOT"]["END_LOT"] = "Y";
            $arResult["TIME"] = "E";
            //return;
        }

        $db_dopprop = CTenderixProposal::GetPropertyLot($arLots["ID"]);
        foreach($db_dopprop as $dopprop => $value){
            foreach($value as $data){
                $arResult["LOT"]['DOPPROP'][$data['PROPERTY_ID']] = $data;
            }
        }

        $rsFile = CTenderixLot::GetFileList($arLots["ID"]);
        $arrFile = array();
        while ($arFile = $rsFile->Fetch()) {
            $arrFile[] = $arFile;
        }
        $arResult["LOT"]["FILE"] = $arrFile;

        $rsPayment = CTenderixSprDetails::GetList($by = "", $order = "", $arFilter = Array("ID" => $arLots["TERM_PAYMENT_ID"]), $is_filtered);
        $arPayment = $rsPayment->Fetch();
        $arResult["PAYMENT"] = $arPayment["TITLE"];

        $rsDelivery = CTenderixSprDetails::GetList($by = "", $order = "", $arFilter = Array("ID" => $arLots["TERM_DELIVERY_ID"]), $is_filtered);
        $arDelivery = $rsDelivery->Fetch();
        $arResult["DELIVERY"] = $arDelivery["TITLE"];
		
		
		//09.11.2017 Ярослав. Способ доставки.
		$rsDelivery = CTenderixSprDetails::GetList($by = "", $order = "", $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_DELIVERY")), $is_filtered);
		while ($arDelivery = $rsDelivery->GetNext()) {
			$arResult["DELIVERY_ARRAY"][$arDelivery["ID"]] = $arDelivery["TITLE"];
		}
		//

        //если есть предложения
		//А тут надо найти минимальное предложение мне кажется, только среди всех пользователей, а  из тендерикса ищут только по своему айди. :P
        $proposalFlag = false;
        $rsProposal = CTenderixProposal::GetList(array("LOT_ID" => $arParams["LOT_ID"], "USER_ID" => $USER->GetID()));
        if ($arProposal = $rsProposal->Fetch()) {
            $proposalFlag = true;
            $arResult["PROPOSAL_ID"] = $arProposal["ID"];
            $arResult["CURRENCY_PROPOSAL"] = $arProposal["CURRENCY"];
            $arResult["MESSAGE"] = $arProposal["MESSAGE"];
            $curr_list = CTenderixCurrency::GetListProposal(strtotime($arProposal["DATE_START"]));

            if (strlen($arProposal["TERM_PAYMENT_VAL"]) > 0) {
                $arResult["LOT"]["TERM_PAYMENT_VAL"] = $arProposal["TERM_PAYMENT_VAL"];
            }
            if (strlen($arProposal["TERM_DELIVERY_VAL"]) > 0) {
                $arResult["LOT"]["TERM_DELIVERY_VAL"] = $arProposal["TERM_DELIVERY_VAL"];
            }

            if ($arLots["TYPE_ID"] == "S" || $arLots["TYPE_ID"] == "R") {
                $arFieldsPropertyProducts = array();
                $rsPropertyProducts = CTenderixProposal::GetListPropertyProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                while ($arPropertyProducts = $rsPropertyProducts->Fetch()) {
                    $arFieldsPropertyProducts[$arPropertyProducts["PRODUCTS_PROPERTY_BUYER_ID"]] = $arPropertyProducts["VALUE"];
                }
                $arResult["PROPOSAL_PROPERTY_PRODUCTS"] = $arFieldsPropertyProducts;

                $arFieldsProducts = array();
                $rsProducts = CTenderixProposal::GetListProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                while($arProducts = $rsProducts->Fetch()) {
                    $arProducts["PRICE_NDS"] = floatval($arProducts["PRICE_NDS"]) / (floatval($curr_list[$arProposal["CURRENCY"]]) <= 0 ? 1 : floatval($curr_list[$arProposal["CURRENCY"]]));
                    $arResult["PROPOSAL_PRODUCTS"][$arProducts["PROD_BUYER_ID"]] = $arProducts;
					//echo "type S";
                }
				//echo $arProposal["ID"];
				//print_r($arResult["PROPOSAL_PRODUCTS"]);
            }
            if ($arLots["TYPE_ID"] != "S" && $arLots["TYPE_ID"] != "R") {
                $arFieldsSpec = array();
                $rsProposalSpec = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
                while ($arProposalSpec = $rsProposalSpec->Fetch()) {
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PROPERTY_BUYER_ID"] = $arProposalSpec["PROPERTY_BUYER_ID"];
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["NDS"] = $arProposalSpec["NDS"];
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PRICE_NDS"] = floatval($arProposalSpec["PRICE_NDS"]);// 18.06.2021 / (floatval($curr_list[$arProposal["CURRENCY"]]) <= 0 ? 1 : floatval($curr_list[$arProposal["CURRENCY"]]));
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["ANALOG"] = $arProposalSpec["ANALOG"];
                }
                $arResult["PROPOSAL_SPEC"] = $arFieldsSpec;
            }
        } else {
            $arResult["CURRENCY_PROPOSAL"] = $arLots["CURRENCY"];
            //$arResult["CURRENCY_PROPOSAL"] = $arParams["CURR"];
        }
		//
		convert_to_lot_curr($curr_name, $arLots["CURRENCY"]);
        //File result
        $rsFiles = CTenderixProposal::GetFileList($arProposal["ID"]);
        while ($arFile = $rsFiles->GetNext()) {
            $arResult["INFO"]["FILE"][] = $arFile;
        }

        if ($arLots["TYPE_ID"] == "S" || $arLots["TYPE_ID"] == "R") {
            $rsProdBuyer = CTenderixProducts::GetListBuyer(array("LOT_ID" => $arLots["ID"]));
            $k = 0;
            while ($arProdBuyer = $rsProdBuyer->Fetch()) {
                $k = $arProdBuyer["ID"];
                $rsProd = CTenderixProducts::GetList($by, $order, array("ID" => $arProdBuyer["PRODUCTS_ID"]), $is_filtered);
                $arProd = $rsProd->Fetch();

                $arrPropProduct = array();
                $arrPropProductBuyer = array();
                $rsProdProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $arProdBuyer["PRODUCTS_ID"]), $is_filtered);
                while ($arProdProps = $rsProdProps->GetNext()) {
                    $rsProps2 = CTenderixProductsProperty::GetListBuyer(Array("PRODUCTS_ID" => $arProdBuyer["ID"], "PRODUCTS_PROPERTY_ID" => $arProdProps["ID"]));
                    $arProps2 = $rsProps2->Fetch();
                    $arrPropProduct[$arProdProps["ID"]] = $arProdProps;
                    $arrPropProductBuyer[$arProps2["PRODUCTS_PROPERTY_ID"]] = $arProps2;
                }
                $arResult["PROPERTY_PRODUCT"][$k] = $arrPropProduct;
                $arResult["PROPERTY_PRODUCT_BUYER"][$k] = $arrPropProductBuyer;
                if ($proposalFlag)
                    $arProdBuyer["START_PRICE"] = floatval($arProdBuyer["START_PRICE"]) / floatval($curr_name[$arResult["CURRENCY_PROPOSAL"]]);
                else
                    $arProdBuyer["START_PRICE"] = floatval($arProdBuyer["START_PRICE"]);
                $arProdBuyer["STEP_PRICE"] = floatval($arProdBuyer["STEP_PRICE"]) * floatval($curr_name[$arResult["LOT"]["CURRENCY"]]);
                $arResult["PRODUCT_BUYER"][$k] = $arProdBuyer;
                $arResult["PRODUCT"][$k] = $arProd;
                //$k++;
            } //print_r($arResult); die;
        }
        if ($arLots["TYPE_ID"] != "S" && $arLots["TYPE_ID"] != "R") {
            $arResult["SPEC"] = CTenderixLotSpec::GetByLotId($arLots["ID"]);

            $rsProp = CTenderixLotSpec::GetListProp($arLots["ID"]);
            while ($arProp = $rsProp->Fetch()) {
                if ($proposalFlag) {
                    $arProp["START_PRICE"] = floatval($arProp["START_PRICE"]) / floatval($curr_name[$arResult["CURRENCY_PROPOSAL"]]);
                } else {
                    $arProp["START_PRICE"] = floatval($arProp["START_PRICE"]);
                }
                $arProp["STEP_PRICE"] = floatval($arProp["STEP_PRICE"]) * floatval($curr_name[$arResult["LOT"]["CURRENCY"]]);
                $arResult["PROPERTY_SPEC"][] = $arProp;
            }
        }

        //dop property
        $rsPropList = CTenderixProposalProperty::GetList($by = "SORT", $order = "desc");
        if ($arResult["PROPOSAL_ID"] > 0) {
            $arResult["PROP_PROPOSAL"] = CTenderixProposal::GetProperty($arResult["PROPOSAL_ID"]);
        }
        $arParams["PROPERTY"] = $arLots["TYPE_ID"] == "P" ? $arParams["PROPERTY2"] : $arParams["PROPERTY"];
        $arParams["PROPERTY_REQUIRED"] = $arLots["TYPE_ID"] == "P" ? $arParams["PROPERTY_REQUIRED2"] : $arParams["PROPERTY_REQUIRED"];
        while ($arPropList = $rsPropList->GetNext()) {
            //if ($arPropList["ACTIVE"] == "N")
            //   continue;
            if (in_array("PROP_" . $arPropList["ID"], $arParams["PROPERTY"]) || !isset($arParams["PROPERTY"])) {
                if (isset($arParams["PROPERTY"])) {
                    $arPropList["IS_REQUIRED"] = in_array("PROP_" . $arPropList["ID"], $arParams["PROPERTY_REQUIRED"]) ? "Y" : "N";
                }
                $arResult["PROP_LIST"][] = $arPropList;
            }
        }
		
		/* Минимальная цена */
		////////
		$arPrice = CTenderixProposal::GetPrice(array("LOT_ID" => $arParams["LOT_ID"]));
		//
		$arResult['MIN_PRICE'] = $arPrice['min'] ;
		
		$arResult['START_PRICE'] = $arPrice['start'] ;
		$arResult['PRODUCT_MIN_SUM'] = $arPrice['product'] ;
		//
		/* Стартовая цена */
        ////////
        $arFilter = array(
            "LOT_ID" => $arParams["LOT_ID"]
        );
        if (count($arLot) > 0) {
            $arResult["TYPE_ID"] = $arLot["TYPE_ID"];
            $arResult["OWNER"] = ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W") ? "Y" : "N";
            // $arResult["RIGHT"] = $T_RIGHT;
            // $time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
            //  $arResult["LOT_END"] = "N";

            //dop property
            $rsPropList = CTenderixProposalProperty::GetList($by = "SORT", $order = "desc");
            while ($arPropList = $rsPropList->GetNext()) {
                if ($arPropList["ACTIVE"] == "N")
                    continue;
                if ($arPropList["START_LOT"] == "Y" && ($arPropList["S_RIGHT"] == "W" || $arPropList["S_RIGHT"] == "R"))
                    $arResult["PROP_LIST"][] = $arPropList;
                else
                    continue;
            }

            if ($arLot["NOTVISIBLE_PROPOSAL"] == "N") {

                //dop property
                $rsProposal = CTenderixProposal::GetList($arFilter);
                while ($arProposal = $rsProposal->GetNext()) {
                    $arResult["PROPOSAL"][$arProposal["ID"]] = $arProposal;

                    //dop property
                    $arResult["PROP_PROPOSAL"][$arProposal["ID"]] = CTenderixProposal::GetProperty($arProposal["ID"]);
                    //dop property

                    $rsFile = CTenderixProposal::GetFileList($arProposal["ID"]);
                    $arrFile = array();
                    while ($arFile = $rsFile->Fetch()) {
                        $arrFile[] = $arFile;
                    }

                    $rsUser = CTenderixUserSupplier::GetByID($arProposal["USER_ID"]);
                    $arUser = $rsUser->Fetch();
                    $arUser["LOGO_SMALL"] = CFile::GetPath($arUser["LOGO_SMALL"]);
                    $arUser["LOGO_BIG"] = CFile::GetPath($arUser["LOGO_BIG"]);
                    $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"] = $arUser;

                    $rsPropList = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc");
                    if ($arProposal["USER_ID"] > 0) {
                        $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP_SUPPLIER"] = CTenderixUserSupplier::GetProperty($arProposal["USER_ID"]);
                    }
                    while ($arPropList = $rsPropList->GetNext()) {
                        if ($arPropList["ACTIVE"] == "N")
                            continue;

                        $arPropList["IS_REQUIRED"] = in_array("PROP_" . $arPropList["ID"], $arParams["REG_FIELDS_REQUIRED"]) ? "Y" : "N";
                        $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
                        $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
                        if ($arPropList["PROPERTY_TYPE"] == "F" && $arProposal["USER_ID"] > 0) {
                            $rsFiles = CTenderixUserSupplier::GetFileListProperty($arProposal["USER_ID"], $arPropList["ID"]);
                            while ($arFile = $rsFiles->GetNext()) {
                                $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]]["FILE"][] = $arFile;
                            }
                        }
                    }


                    $arResult["PROPOSAL"][$arProposal["ID"]]["FILE"] = $arrFile;
                    if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {
                        $rsProposalSpec = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
                        while ($arProposalSpec = $rsProposalSpec->GetNext()) {
                            $arResult["PROPOSAL"][$arProposal["ID"]]["SPEC"][$arProposalSpec["PROPERTY_BUYER_ID"]] = $arProposalSpec;
                        }
                        //natsort($arResult["PROPOSAL"][$arProposal["ID"]]["SPEC"]);
                    }
                    if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
                        $rsProduct = CTenderixProposal::GetListProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                        $arProduct = $rsProduct->Fetch();
                        $arResult["PROPOSAL"][$arProposal["ID"]]["PRODUCT"] = $arProduct;

                        $rsProductProp = CTenderixProposal::GetListPropertyProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                        while ($arProductProp = $rsProductProp->Fetch()) {
                            $arResult["PROPOSAL"][$arProductProp["PROPOSAL_ID"]]["PROP"][$arProductProp["PRODUCTS_PROPERTY_BUYER_ID"]] = $arProductProp;
                        }
                    }
                }
                //print_r($arResult["PROPOSAL"]);
                $arParams["NDS_TYPE"] = $arLot["WITH_NDS"];

                $res->arResult = Array();
                //unset($arLot);

                $arCurr = array();
                if (CModule::IncludeModule("currency")) {
                    $lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
                    while ($lcur_res = $lcur->Fetch()) {
                        $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
                        $arCur = $rsCur->Fetch();
                        $arCurr[$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? $arCur["RATE"] : 1;
                    }
                }

                foreach ($arResult["PROPOSAL"] as $idProp => $vProp) {
                    $itogo = 0;
                    $itogo_n = 0;
                    $hasPrices = false;
                    if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R") {
                        foreach ($vProp["SPEC"] as $idPropBuyer => $proposals) {
                            if (floatval($proposals["PRICE_NDS"]) > 0 && floatval($proposals["COUNT"]) > 0) {
                                $hasPrices = true;
                                $proposals["PRICE_NDS"] = $proposals["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
                                $itogo += $proposals["PRICE_NDS"] * $proposals["COUNT"];
                                if ($arParams["NDS_TYPE"] == "N") {
                                    $itogo_n += CTenderix::PriceNDSy($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
                                } else {
                                    $itogo_n += CTenderix::PriceNDSn($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
                                }
                            }
                            $history[$idPropBuyer] = $proposals;
                        }
                        $arResult["PROPOSAL"][$idProp]["HISTORY"] = $history;
                    }
                    if ($arResult["TYPE_ID"] == "S" || $arResult["TYPE_ID"] == "R") {
                        if (floatval($vProp["PRODUCT"]["PRICE_NDS"]) > 0 && floatval($vProp["PRODUCT"]["COUNT"]) > 0) {
                            $hasPrices = true;
                            $vProp["PRODUCT"]["PRICE_NDS"] = $vProp["PRODUCT"]["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
                            $itogo = $vProp["PRODUCT"]["PRICE_NDS"] * $vProp["PRODUCT"]["COUNT"];
                            if ($arParams["NDS_TYPE"] == "N") {
                                $itogo_n = CTenderix::PriceNDSy($vProp["PRODUCT"]["PRICE_NDS"], $vProp["PRODUCT"]["NDS"]) * $vProp["PRODUCT"]["COUNT"];
                            } else {
                                $itogo_n = CTenderix::PriceNDSn($vProp["PRODUCT"]["PRICE_NDS"], $vProp["PRODUCT"]["NDS"]) * $vProp["PRODUCT"]["COUNT"];
                            }
                        }
                    }
                    // Устанавливаем итоги только если есть заполненные цены
                    if ($hasPrices && $itogo > 0) {
                        $arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
                        $itogg[$idProp] = $itogo;
                        $itogg_n[$idProp] = $itogo_n;
                    } else {
                        $arResult["PROPOSAL"][$idProp]["ITOGO"] = 0;
                        $itogg[$idProp] = 0;
                        $itogg_n[$idProp] = 0;
                    }
                }
                $arr_proposal = $arResult["PROPOSAL"];
                unset($arResult["PROPOSAL"]);
                unset($itogo);
                unset($itogo_n);
                if ($arParams["SORT_ITOGO"] == "asc") {
                    asort($itogg, SORT_NUMERIC);
                    asort($itogg_n, SORT_NUMERIC);
                } elseif ($arParams["SORT_ITOGO"] == "desc") {
                    arsort($itogg, SORT_NUMERIC);
                    arsort($itogg_n, SORT_NUMERIC);
                }
                foreach ($itogg as $idProp => $itogo) {
                    $arResult["PROPOSAL"][$idProp] = $arr_proposal[$idProp];
                    $arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
                }
                foreach ($itogg_n as $idProp => $itogo_n) {
                    $arResult["PROPOSAL"][$idProp]["ITOGO_N"] = $itogo_n;
                }
            }

            /*$r = array();
            $t = array();
                    $p = array();
            foreach($arResult["PROPOSAL"] as $id_prop => $prop) {
                foreach($prop["HISTORY"] as $id_spec => $spec) {
                    $r[$id_spec][$spec["PROPOSAL_ID"]] = $spec;
                    $t[$id_spec] = array(
                                                     "TITLE" => $spec["TITLE"],
                                                     "START_PRICE" => $spec["START_PRICE"],
                                                     "ADD_INFO" => $spec["ADD_INFO"],
                                                     "COUNT" => $spec["COUNT"],
                                                   );
                                    $p[$id_spec][] = $spec["PRICE_NDS"];
                }
            }
            $r["TOVAR"] = $t;
            $r["PRICE"] = $p;
            $arResult["SPEC2"] = $r;*/

        }

        ////////

        $lotAccess = CListSupplierClass::selectRequestProposal($arParams["LOT_ID"], $USER->GetID(), 'requestAddProposal');

        $arResult["LOT"]["ACCESS"] = $lotAccess[0]["ACCESS"];

    } else {
        ShowError(GetMessage("PW_TD_LOT_NOTFOUND"));
        return;
    }

    $res->arResult = Array();
    unset($arLots);
	

	
    $this->IncludeComponentTemplate();
} else {
	echo "<div class='alert alert-danger' style='text-align:center;font-size:18px;font-weight:bold;'>";
    echo "<p><font class='errortext'>Доступ запрещён</font></p>";
    echo "</div>";
    return;
}
?>