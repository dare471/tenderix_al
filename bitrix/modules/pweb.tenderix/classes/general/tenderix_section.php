<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderSection {

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

    function BuildTree($cats, $parent_id, $level, $curr_id = -1) {
        global $arCatTree;
        if (isset($cats[$parent_id])) {
            foreach ($cats[$parent_id] as $cat) {
                if ($curr_id != $cat['ID'] || $curr_id < 0)
                    $arCatTree[$cat['ID']] = str_repeat(" . ", $level) . $cat['TITLE'];
                $level = $level + 1;
                if ($curr_id != $cat['ID'] || $curr_id < 0)
                    self::BuildTree($cats, $cat['ID'], $level, $curr_id);
                $level = $level - 1;
            }
        }else
            return null;
        return $arCatTree;
    }

}

?>
