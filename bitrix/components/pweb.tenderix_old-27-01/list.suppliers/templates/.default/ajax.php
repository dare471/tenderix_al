<?php
/**
 * Created by PhpStorm.
 * User: vfilippov
 * Date: 10.08.15
 * Time: 18:13
 */
//use \Bitrix\Main;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!CModule::IncludeModule("pweb.tenderix"))
    return;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/pweb.tenderix/list.suppliers/class.php");

if(isset($_REQUEST)) {
    if(isset($_REQUEST["statusId"])) {
        $status_id = intval($_REQUEST["statusId"]);
    }

    if($_REQUEST['type'] == 'update_status') {
        $supplier_id = intval($_REQUEST["supplierId"]);


        $arFields = array(
            "STATUS" => $status_id,
        );

        $res = CTenderixUserSupplier::Update($supplier_id, $arFields);

        if ($res) {
            echo 'Статус изменён';
        } else {
            echo 'Ошибка изменения статуса';
        }
    }
    elseif($_REQUEST['type'] == 'filter_status') {

        if($status_id == 0) {
            $arFilter = array();
        }
        else {
            $arFilter = array(
                "STATUS" => $status_id,
            );
        }

        //__($status_id);

        $CElementSupplier = new CListSupplierClass;
        $res = $CElementSupplier -> getResult($arFilter);

        echo $res;
    }
    elseif($_REQUEST['type'] == 'confirm_access') {
        $supplier_id = intval($_REQUEST["supplId"]);
        $lot_id = intval($_REQUEST["lotId"]);

        $CElementSupplier = new CListSupplierClass;
        $res = $CElementSupplier -> confirmAccessLot($lot_id, $supplier_id);

        if ($res) {
            echo 1;
        } else {
            echo "Ошибка изменения прав на доступ к лоту";
        }
    }
}