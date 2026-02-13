<?php
IncludeModuleLangFile(__FILE__);

class CAllTenderSprDetails {

    function CheckFields($ACTION, &$arFields, $ID = 0) {
        $aMsg = array();

        if ($ACTION == "ADD" || $ACTION == "UPDATE") {
            if (strlen(trim($arFields["TITLE"])) <= 0) {
                $aMsg[] = array(
                    "id" => 'TITLE',
                    "text" => GetMessage("PW_TD_ERROR_TITLE_EMPTY"));
            }
        }

        if (is_set($arFields, "C_SORT")) {
            $arFields["C_SORT"] = intVal($arFields["C_SORT"]);
        }

        if (!empty($aMsg)) {
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
        }

        return true;
    }

    function GetByIdSPR($ID, $SPR_ID) {
        $ID = intval($ID);
        $SPR_ID = intval($SPR_ID);
        $res = CTenderixSprDetails::GetList($by, $order, array("ID" => $ID, "SPR_ID" => $SPR_ID), $is_filtered);
        return $res;
    }

    function GetByID($ID) {
        $ID = intval($ID);
        $rsRes = CTenderixSprDetails::GetList($by, $order, array("ID" => $ID), $is_filtered);
        $arRes = $rsRes->Fetch();
        return $arRes;
    }

    function SelectBoxID($select_name, $val, $spr_id) {
        $module_id = "pweb.tenderix";

        $rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => $spr_id), $is_filtered);
        ?>
        <select name="<?= $select_name ?>" id="<?= $select_name ?>">
            <option value="0">--</option>
            <?
            while ($arUnit = $rsUnit->GetNext()) {
                $select = $val == $arUnit["ID"] ? " selected" : "";
                ?>
                <option<?= $select ?> value="<?= $arUnit["ID"] ?>"><?= $arUnit["TITLE"] ?></option>
                <?
            }
            ?>
        </select>
        <?
    }

    function SelectBoxUnit($select_name, $val) {
        $module_id = "pweb.tenderix";

        $rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_UNIT")), $is_filtered);
        ?>
        <select name="<?= $select_name ?>" id="<?= $select_name ?>">
            <option value="0">--</option>
            <?
            while ($arUnit = $rsUnit->GetNext()) {
                $select = $val == $arUnit["ID"] ? " selected" : "";
                ?>
                <option<?= $select ?> value="<?= $arUnit["ID"] ?>"><?= $arUnit["TITLE"] ?></option>
                <?
            }
            ?>
        </select>
        <?
    }

    function SelectBoxDelivery($select_name, $val) {
        $module_id = "pweb.tenderix";

        $rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_DELIVERY")), $is_filtered);
        ?>
        <select name="<?= $select_name ?>" id="<?= $select_name ?>">
            <option value="0">--</option>
            <?
            while ($arUnit = $rsUnit->GetNext()) {
                $select = $val == $arUnit["ID"] ? " selected" : "";
                ?>
                <option<?= $select ?> value="<?= $arUnit["ID"] ?>"><?= $arUnit["TITLE"] ?></option>
                <?
            }
            ?>
        </select>
        <?
    }

    function SelectBoxPayment($select_name, $val) {
        $module_id = "pweb.tenderix";

        $rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_PAYMENT")), $is_filtered);
        ?>
        <select name="<?= $select_name ?>" id="<?= $select_name ?>">
            <option value="0">--</option>
            <?
            while ($arUnit = $rsUnit->GetNext()) {
                $select = $val == $arUnit["ID"] ? " selected" : "";
                ?>
                <option<?= $select ?> value="<?= $arUnit["ID"] ?>"><?= $arUnit["TITLE"] ?></option>
                <?
            }
            ?>
        </select>
        <?
    }

}
?>
