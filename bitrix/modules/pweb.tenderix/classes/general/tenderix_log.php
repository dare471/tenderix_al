<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderLog {

    function Log($EVENT, $arParams = array()) {
        global $USER, $DB;
        if (strlen($EVENT) < 0)
            return;

        $USER_ID = $USER->GetID();
        $TIMESTAMP_X = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time() + CTimeZone::GetOffset());
        $DESCRIPTION = "";
        $OBJECT = 0;
        switch ($EVENT) {
            case "LOT_ADD":
                $DESCRIPTION = GetMessage("PW_TD_LOT_ADD_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
            case "LOT_UPDATE":
                $DESCRIPTION = GetMessage("PW_TD_LOT_UPDATE_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
            case "LOT_DEL":
                $DESCRIPTION = GetMessage("PW_TD_LOT_DEL_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
            case "LOT_WIN":
                $DESCRIPTION = GetMessage("PW_TD_LOT_WIN_DESCRIPTION") . " " . $arParams["LOT_ID"] . "; USER_ID: " . implode(",", $arParams["WIN"]);
                $OBJECT = $arParams["LOT_ID"];
                break;
            case "PROPOSAL_ADD":
                $DESCRIPTION = GetMessage("PW_TD_PROPOSAL_ADD_DESCRIPTION") . " " . $arParams["FIELDS"]["LOT_ID"];
                $OBJECT = $arParams["FIELDS"]["LOT_ID"];
                break;
            case "PROPOSAL_UPDATE":
                $DESCRIPTION = GetMessage("PW_TD_PROPOSAL_UPDATE_DESCRIPTION") . " " . $arParams["FIELDS"]["LOT_ID"];
                $OBJECT = $arParams["FIELDS"]["LOT_ID"];
                break;
            case "BUYER_ADD":
                $DESCRIPTION = GetMessage("PW_TD_BUYER_ADD_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
            case "BUYER_UPDATE":
                $DESCRIPTION = GetMessage("PW_TD_BUYER_UPDATE_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
            case "BUYER_DEL":
                $DESCRIPTION = GetMessage("PW_TD_BUYER_DEL_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
            case "SUPPLIER_ADD":
                $DESCRIPTION = GetMessage("PW_TD_SUPPLIER_ADD_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
            case "SUPPLIER_UPDATE":
                $DESCRIPTION = GetMessage("PW_TD_SUPPLIER_UPDATE_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
            case "SUPPLIER_DEL":
                $DESCRIPTION = GetMessage("PW_TD_SUPPLIER_DEL_DESCRIPTION") . " " . $arParams["ID"];
                $OBJECT = $arParams["ID"];
                break;
			case "TENDERIX_ADD_TIME_LOT":
				$DESCRIPTION = GetMessage("PW_TD_ADD_TIME_DESCRIPTION") . " " . $arParams["ID"]; 
				$OBJECT = $arParams["ID"];
			break;
			case "TENDERIX_BEST_PROPOSAL2":
			case "TENDERIX_BEST_PROPOSAL":
			case "TENDERIX_WIN_LOT":
			case "TENDERIX_NEW_PROPOSAL":
			case "TENDERIX_NEW_LOT":
			case "TENDERIX_USER_INVITE":
				$DESCRIPTION = GetMessage("PW_TD_EVENT_SEND_DESCRIPTION") . " " . $arParams["ID"]; 
				$OBJECT = $arParams["ID"];
				break;
		}
			
        $DESCRIPTION_EX = serialize($arParams);

        $arFields = array(
            "TIMESTAMP_X" => $TIMESTAMP_X,
            "EVENT" => $EVENT,
            "USER_ID" => $USER_ID,
            "OBJECT" => $OBJECT,
            "DESCRIPTION" => $DESCRIPTION,
            "DESCRIPTION_EX" => $DESCRIPTION_EX,
        );

        return CTenderixLog::Add($arFields);
    }

    function Event($EVENT = "") {
        $arEvents = array();
        $arEvents["LOT_ADD"] = GetMessage("PW_TD_LOT_ADD");
        $arEvents["LOT_UPDATE"] = GetMessage("PW_TD_LOT_UPDATE");
        $arEvents["LOT_DEL"] = GetMessage("PW_TD_LOT_DEL");
        $arEvents["LOT_WIN"] = GetMessage("PW_TD_LOT_WIN");
        $arEvents["PROPOSAL_ADD"] = GetMessage("PW_TD_PROPOSAL_ADD");
        $arEvents["PROPOSAL_UPDATE"] = GetMessage("PW_TD_PROPOSAL_UPDATE");
        $arEvents["BUYER_ADD"] = GetMessage("PW_TD_BUYER_ADD");
        $arEvents["BUYER_UPDATE"] = GetMessage("PW_TD_BUYER_UPDATE");
        $arEvents["BUYER_DEL"] = GetMessage("PW_TD_BUYER_DEL");
        $arEvents["SUPPLIER_ADD"] = GetMessage("PW_TD_SUPPLIER_ADD");
        $arEvents["SUPPLIER_UPDATE"] = GetMessage("PW_TD_SUPPLIER_UPDATE");
        $arEvents["SUPPLIER_DEL"] = GetMessage("PW_TD_SUPPLIER_DEL");
		$arEvents["EVENT_SEND"] = GetMessage("PW_TD_EVENT_SEND");

        if (strlen($EVENT) > 0)
            return $arEvents[$EVENT];
        else
            return $arEvents;
    }
}

?>
