<?
define("STOP_STATISTICS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$LOT_ID = $_REQUEST["LOT_ID"];
$CURR = $_REQUEST["CURR"];
if ($LOT_ID <= 0)
    return;

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();

$timeZone = time() + CTimeZone::GetOffset();

//if ($T_RIGHT == "W" || ($S_RIGHT == "W" && $T_RIGHT == "P")) {
if ($T_RIGHT == "W" || $T_RIGHT == "P") {
    $rsLot = CTenderixLot::GetByIDa($LOT_ID);
    if ($arLot = $rsLot->Fetch()) {

        $date_start = strtotime($arLot["DATE_START"]);
        $date_tek = time();
        $date_st = $date_start - $date_tek;


        if ($S_RIGHT == "A" && $arLot["TYPE_ID"] != "P" && $T_RIGHT != "W") {
            return;
        }
        //time finish lot
        //$time_diff = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]) - $timeZone;
        $time_diff = strtotime($arLot["DATE_END"]) - $timeZone;
        //best proposal
        if ($arLot["OPEN_PRICE"] == "Y") {
            if (CModule::IncludeModule("currency")) {
                $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "asc", $arFilter = Array());
                while ($arCur = $rsCur->Fetch()) {
                    $arrCur[$arCur["CURRENCY"]] = $arCur["RATE"];
                }
            }

            $curr_user = floatval($arrCur[$CURR]) > 0 ? floatval($arrCur[$CURR]) : 1;
			
			$curr_user = 1;

            //best proposal N tovar -->>
            if ($arLot["TYPE_ID"] != "S") {
                $arProposalMin = array();
                $arProposalSpec = CTenderixProposal::GetListSpecPrice($LOT_ID);
                foreach ($arProposalSpec as $proposalBuyerId => $proposalValue) {
                    $arProposalMin[$proposalBuyerId] = floatval($proposalValue["MIN"]) / $curr_user;
                    $arProposalMax[$proposalBuyerId] = floatval($proposalValue["MAX"]) / $curr_user;
                }
                $arProposalMin = $arLot["TYPE_ID"] == "N" ? json_encode($arProposalMin) : json_encode($arProposalMax);
            }
            //best proposal N tovar <<--
            //best proposal S tovar -->>
            if ($arLot["TYPE_ID"] == "S") {
                $arProposalMin = "";
                $arProductsPrice = CTenderixProposal::GetListProductsPrice2($LOT_ID);
                foreach ($arProductsPrice as $proposalBuyerId => $proposalValue) {
                    $proposalMinArr = array();
                    foreach($proposalValue as $proposalPrice) {
                        if($proposalPrice!=0)
                            $proposalMinArr[] = $proposalPrice;
                    }
                    $arProposalMin[$proposalBuyerId] = min($proposalMinArr) / $curr_user;
                }
                $arProposalMin = json_encode($arProposalMin);
            }
            //best proposal S tovar <<--
        }

        $json_data = array(
            "time_diff" => $time_diff,
            "date_st" => $date_st,
            "proposal_min" => $arProposalMin,
            "proposal_curr" => $CURR,
            "type" => $arLot["TYPE_ID"],
        );
        echo json_encode($json_data);


    }
}
?>