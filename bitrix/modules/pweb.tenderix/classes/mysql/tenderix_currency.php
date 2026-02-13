<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_currency.php");

class CTenderixCurrency extends CAllTenderCurrency {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixCurrency<br>File: " . __FILE__;
    }

}

?>
