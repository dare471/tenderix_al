<?php

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader,
	Bitrix\Main\Web\HttpClient,
	Bitrix\Currency;

class CAllTenderCurrency {

    function Add() {
        if (CModule::IncludeModule("currency")) {
            $strQueryText = QueryGetData("www.cbr.ru", 80, "/scripts/XML_daily.asp", "", $error_number, $error_text);
            if (strlen($strQueryText) > 0) {
                require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/xml.php");
                $strQueryText = eregi_replace("<!DOCTYPE[^>]{1,}>", "", $strQueryText);
                $objXML = new CDataXML();
                $objXML->LoadString($strQueryText);
                $arData = $objXML->GetArray();
                if (is_array($arData) && count($arData["ValCurs"]["#"]["Valute"]) > 0) {
                    $cdate = $arData["ValCurs"]["@"]["Date"] . "<br>";
                    $rsCurrency = CCurrency::GetList($by, $order);
                    $base_currency = CCurrency::GetBaseCurrency();
                    while ($arCurrency = $rsCurrency->GetNext()) {
                        $site_currency[] = $arCurrency['CURRENCY'];
                    }
                    for ($j1 = 0; $j1 < count($arData["ValCurs"]["#"]["Valute"]); $j1++) {
                        $arCurrValue = str_replace(",", ".", $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Value"][0]["#"]);
                        $curr = DoubleVal($arCurrValue);
                        $rate_cnt = $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Nominal"][0]["#"];
                        $currency = $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["CharCode"][0]["#"];
                        if ($currency != $base_currency && in_array($currency, $site_currency)) {
                            $arFields = array(
                                "RATE" => round($curr, 2),
                                "RATE_CNT" => $rate_cnt,
                                "CURRENCY" => $currency,
                                "DATE_RATE" => $cdate
                            );
                            CCurrencyRates::Add($arFields);
                        }
                    }
                }
            }
        }
        return "CTenderixCurrency::Add();";
    }
		
	function GetRatesFromKazakhstanNationalBank() { 

		global $DB, $APPLICATION;

		if (!Loader::includeModule('currency'))
		{
			return false;
		}

		$baseCurrency = 'KZT';
		$tenderixCurrencyList = array('RUB', 'UAH', 'EUR', 'USD');
		$currencyTenderixRateList = array();
		$date = date('d.m.Y');
		
		$url = 'http://www.nationalbank.kz/rss/get_rates.cfm?fdate=' . $date;
				
		$http = new HttpClient();
		$data = $http->get($url);
				
		$charset = 'windows-1251';
		$matches = array();
		
		if (preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $data, $matches))
		{
			$charset = trim($matches[1]);
		}
		
		$data = preg_replace("#<!DOCTYPE[^>]+?>#i", '', $data);
		$data = preg_replace("#<"."\\?XML[^>]+?\\?".">#i", '', $data);
		$data = $APPLICATION->ConvertCharset($data, $charset, SITE_CHARSET);

		$objXML = new CDataXML();
		$res = $objXML->LoadString($data);
		if ($res !== false)
			$data = $objXML->GetArray();
		else
			$data = false;
				
		if (is_array($data) && count($data["rates"]["#"]["item"])>0)
		{

			foreach($tenderixCurrencyList as $tenderixCurrency) {

				$currencyList = $data["rates"]["#"]["item"];

				foreach ($currencyList as &$currencyRate)
				{
					if ($currencyRate["#"]["title"][0]["#"] == $tenderixCurrency)
					{
						$currencyTenderixRateList[$tenderixCurrency]['STATUS'] = 'OK';
						$currencyTenderixRateList[$tenderixCurrency]['RATE_CNT'] = (int)$currencyRate["#"]["quant"][0]["#"];
						$currencyTenderixRateList[$tenderixCurrency]['RATE'] = (float)str_replace(",", ".", $currencyRate["#"]["description"][0]["#"]);
						$currencyTenderixRateList[$tenderixCurrency]['CURRENCY'] = $tenderixCurrency;
						$currencyTenderixRateList[$tenderixCurrency]['DATE_RATE'] = $date;
						break;
					}			
				}
			}
			
			unset($currencyRate, $currencyList, $tenderixCurrencyList, $tenderixCurrency);
			
			foreach($currencyTenderixRateList as $currency => $rate) {
			
				if ($rate['STATUS'] == 'OK')
				{
					$arFilter = array( 
					   "CURRENCY" => $rate['CURRENCY'], 
					   "DATE_RATE"=> $rate['DATE_RATE'] 
					); 
					$by = "date"; 
					$order = "desc"; 
					
					$rsRate = CCurrencyRates::GetList($by, $order, $arFilter); 
					
					if(!$arRate = $rsRate->Fetch()) 
						CCurrencyRates::Add($rate); 
				}
			}
		}
		
		return 'CTenderixCurrency::GetRatesFromKazakhstanNationalBank();'; 
	}

    function GetListProposal($DATE_RATE) {
        if (CModule::IncludeModule("currency")) {
            $day = date("w", $DATE_RATE);
            if($day == 1) $DATE_RATE -= 86400*2; 
            if($day == 0) $DATE_RATE -= 86400; 
            $DATE_RATE = date("d.m.Y", $DATE_RATE);
            $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("DATE_RATE" => $DATE_RATE));
            while ($arCur = $rsCur->Fetch()) {
                $arResult[$arCur["CURRENCY"]] = $arCur["RATE"] > 0 ? $arCur["RATE"] : 1;
            }
        }
        
        return $arResult;
    }

}

?>
