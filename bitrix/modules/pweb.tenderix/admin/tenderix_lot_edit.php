<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/jquery.js"></script>', true);

$module_id = "pweb.tenderix";
$TENDERIXRIGHT = $APPLICATION->GetGroupRight($module_id);
if ($TENDERIXRIGHT < "W")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/include.php");
ClearVars();
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/prolog.php");

$ID = intval($ID);
$message = false;

$bInitVars = false;
if ((strlen($save) > 0 || strlen($apply) > 0) && $REQUEST_METHOD == "POST" && $TENDERIXRIGHT >= "S" && check_bitrix_sessid()) {
    global $DB, $USER;
    $arFields = array(
        "ACTIVE" => ($ACTIVE == "Y" ? "Y" : "N"),
        "SECTION_ID" => $SECTION_ID,
        "TYPE_ID" => $TYPE_ID,
        "COMPANY_ID" => $COMPANY_ID,
        "TIMESTAMP_X" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time()),
        "DATE_START" => $DATE_START,
        "DATE_END" => $DATE_END,
        "NOTE" => $NOTE,
        "OPEN_PRICE" => ($OPEN_PRICE == "Y" ? "Y" : "N"),
        "TIME_UPDATE" => (intval($TIME_UPDATE) > 0 ? $TIME_UPDATE : "600"),
        "DATE_DELIVERY" => $DATE_DELIVERY,
        "RESPONSIBLE_FIO" => $RESPONSIBLE_FIO,
        "TIME_EXTENSION" => $TIME_EXTENSION,
        "RESPONSIBLE_PHONE" => $RESPONSIBLE_PHONE,
        "TERM_PAYMENT_ID" => $TERM_PAYMENT_ID,
        "TERM_PAYMENT_VAL" => ($TERM_PAYMENT_ID == 0 ? "" : $TERM_PAYMENT_VAL),
        "TERM_PAYMENT_REQUIRED" => ($TERM_PAYMENT_ID == 0 ? "N" : ($TERM_PAYMENT_REQUIRED == "Y" ? "Y" : "N")),
        "TERM_PAYMENT_EDIT" => ($TERM_PAYMENT_ID == 0 ? "N" : ($TERM_PAYMENT_EDIT == "Y" ? "Y" : "N")),
        "TERM_DELIVERY_ID" => $TERM_DELIVERY_ID,
        "TERM_DELIVERY_VAL" => ($TERM_DELIVERY_ID == 0 ? "" : $TERM_DELIVERY_VAL),
        "TERM_DELIVERY_REQUIRED" => ($TERM_DELIVERY_ID == 0 ? "N" : ($TERM_DELIVERY_REQUIRED == "Y" ? "Y" : "N")),
        "TERM_DELIVERY_EDIT" => ($TERM_DELIVERY_ID == 0 ? "N" : ($TERM_DELIVERY_EDIT == "Y" ? "Y" : "N")),
        "PRIVATE" => $PRIVATE == "Y" ? "Y" : "N",
        "PRIVATE_LIST" => $PRIVATE_LIST,
        "WITH_NDS" => $WITH_NDS,
        "CURRENCY" => $CURRENCY,
    );

    if ($TYPE_ID != 'S' && $TYPE_ID != 'R') {
        $arFields["TITLE"] = $TITLE;
    } else {

        if ($PRODUCTS_ID > 0) {
            $rsTovar = CTenderixProducts::GetByID($PRODUCTS_ID);
            $arTovar = $rsTovar->Fetch();
            $arFields["TITLE"] = $arTovar["TITLE"];
            $arFields["SECTION_ID"] = $arTovar["SECTION_ID"];
        } else {
            $arFields["TITLE"] = "notitle";
        }
    }

    if ($TYPE_ID != "S" && $TYPE_ID != "R") {
        if ($ID > 0) {
            $rsSpecProp = CTenderixLotSpec::GetListProp($ID);
            while ($arSpecProp = $rsSpecProp->GetNext()) {
                if (${"PROP_" . $arSpecProp["ID"] . "_DEL"} == "Y") {
                    if (!CTenderixLotSpec::DeletePropID($arSpecProp["ID"])) {
                        $message = new CAdminMessage(Array("MESSAGE" => GetMessage("PROP_DELETE_ERROR")));
                        $bInitVars = true;
                    }
                }
                $arFieldsDop[] = array(
                    "TITLE" => ${"PROP_" . $arSpecProp["ID"] . "_TITLE"},
                    "ADD_INFO" => ${"PROP_" . $arSpecProp["ID"] . "_ADD_INFO"},
                    "COUNT" => ${"PROP_" . $arSpecProp["ID"] . "_COUNT"},
                    "UNIT_ID" => ${"PROP_" . $arSpecProp["ID"] . "_UNIT_ID"},
                    "START_PRICE" => ${"PROP_" . $arSpecProp["ID"] . "_START_PRICE"},
                    "STEP_PRICE" => ${"PROP_" . $arSpecProp["ID"] . "_STEP_PRICE"},
                );
                $arFieldsDopUpdate[$arSpecProp["ID"]] = array(
                    "TITLE" => ${"PROP_" . $arSpecProp["ID"] . "_TITLE"},
                    "ADD_INFO" => ${"PROP_" . $arSpecProp["ID"] . "_ADD_INFO"},
                    "COUNT" => ${"PROP_" . $arSpecProp["ID"] . "_COUNT"},
                    "UNIT_ID" => ${"PROP_" . $arSpecProp["ID"] . "_UNIT_ID"},
                    "START_PRICE" => ${"PROP_" . $arSpecProp["ID"] . "_START_PRICE"},
                    "STEP_PRICE" => ${"PROP_" . $arSpecProp["ID"] . "_STEP_PRICE"},
                );
            }
        }
        for ($i = 0; $i < 5; $i++) {
            if (strlen(${"PROP_n" . $i . "_TITLE"}) <= 0 ||
                    strlen(${"PROP_n" . $i . "_COUNT"}) <= 0 ||
                    strlen(${"PROP_n" . $i . "_UNIT_ID"}) <= 0)
                continue;
            $arFieldsDop[] = array(
                "TITLE" => ${"PROP_n" . $i . "_TITLE"},
                "ADD_INFO" => ${"PROP_n" . $i . "_ADD_INFO"},
                "COUNT" => ${"PROP_n" . $i . "_COUNT"},
                "UNIT_ID" => ${"PROP_n" . $i . "_UNIT_ID"},
                "START_PRICE" => ${"PROP_n" . $i . "_START_PRICE"},
                "STEP_PRICE" => ${"PROP_n" . $i . "_STEP_PRICE"},
            );
            $arFieldsDopNew[] = array(
                "TITLE" => ${"PROP_n" . $i . "_TITLE"},
                "ADD_INFO" => ${"PROP_n" . $i . "_ADD_INFO"},
                "COUNT" => ${"PROP_n" . $i . "_COUNT"},
                "UNIT_ID" => ${"PROP_n" . $i . "_UNIT_ID"},
                "START_PRICE" => ${"PROP_n" . $i . "_START_PRICE"},
                "STEP_PRICE" => ${"PROP_n" . $i . "_STEP_PRICE"},
            );
        }
    }

    if ($TYPE_ID == "S" || $TYPE_ID == "R") {
        $arFieldsDop["PRODUCTS"] = array(
            "PRODUCTS_ID" => $PRODUCTS_ID,
            "START_PRICE" => $START_PRICE,
            "STEP_PRICE" => $STEP_PRICE,
            "COUNT" => $COUNT,
            "COUNT_EDIT" => ($COUNT_EDIT == "Y" ? "Y" : "N")
        );
        if ($ID > 0) {
            $rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $PRODUCTS_ID), $is_filtered);
            $rsProdBuyer = CTenderixProducts::GetListBuyer($arFilter = Array("LOT_ID" => $ID));
            $arProdBuyer = $rsProdBuyer->Fetch();
            while ($arProps = $rsProps->Fetch()) {
                $rsPropsBuyer = CTenderixProductsProperty::GetListBuyer(array("PRODUCTS_ID" => $arProdBuyer["ID"], "PRODUCTS_PROPERTY_ID" => $arProps["ID"]));
                $arPropsBuyer = $rsPropsBuyer->Fetch();
                $arFieldsDop["PRODUCTS_PROPERTY"][] = array(
                    "PRODUCTS_PROPERTY_BUYER" => $arPropsBuyer["ID"],
                    "VALUE" => ${"PROP_PROD_" . $arPropsBuyer["ID"] . "_VALUE"},
                    "REQUIRED" => (${"PROP_PROD_" . $arPropsBuyer["ID"] . "_REQUIRED"} == "Y" ? "Y" : "N"),
                    "EDIT" => (${"PROP_PROD_" . $arPropsBuyer["ID"] . "_EDIT"} == "Y" ? "Y" : "N"),
                    "VISIBLE" => (${"PROP_PROD_" . $arPropsBuyer["ID"] . "_VISIBLE"} == "Y" ? "Y" : "N"),
                );
            }
        } else {
            $rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $PRODUCTS_ID), $is_filtered);
            while ($arProps = $rsProps->Fetch()) {
                $arFieldsDop["PRODUCTS_PROPERTY"][] = array(
                    "PRODUCTS_PROPERTY_ID" => $arProps["ID"],
                    "VALUE" => ${"PROP_PROD_" . $arProps["ID"] . "_VALUE"},
                    "REQUIRED" => (${"PROP_PROD_" . $arProps["ID"] . "_REQUIRED"} == "Y" ? "Y" : "N"),
                    "EDIT" => (${"PROP_PROD_" . $arProps["ID"] . "_EDIT"} == "Y" ? "Y" : "N"),
                    "VISIBLE" => (${"PROP_PROD_" . $arProps["ID"] . "_VISIBLE"} == "Y" ? "Y" : "N"),
                );
            }
        }
        //print_r($arFieldsDop); die;
    }

    $res_lot = 0;
    if ($ID > 0) {
        if (CTenderixLot::CheckFields("UPDATE", $arFields, $arFieldsDop)) {
            $res_lot = CTenderixLot::Update($ID, $arFields);
            if ($TYPE_ID != 'S' && $TYPE_ID != 'R' && intval($res_lot) > 0) {
                //Update NS lot
                $arFieldsSpec = array(
                    "FULL_SPEC" => ($FULL_SPEC == "Y" ? "Y" : "N"),
                    "NOT_ANALOG" => ($NOT_ANALOG == "Y" ? "Y" : "N")
                );
                $res = CTenderixLotSpec::Update($ID, $arFieldsSpec);
                if (!$res) {
                    $message = new CAdminMessage(Array("MESSAGE" => GetMessage("SPEC_SAVE_ERROR")));
                    $bInitVars = true;
                }
                $SPEC_ID = intval($res);
                if ($SPEC_ID > 0) {
                    foreach ($arFieldsDopUpdate as $arSpecPropId => $arFieldsProp) {
                        $res = CTenderixLotSpec::UpdateProp($arSpecPropId, $arFieldsProp);
                        if (!$res) {
                            $message = new CAdminMessage(Array("MESSAGE" => GetMessage("PROP_SAVE_ERROR") . $arSpecPropId));
                            $bInitVars = true;
                        }
                    }

                    foreach ($arFieldsDopNew as $fieldPropNew) {
                        $fieldPropNew["SPEC_ID"] = $SPEC_ID;
                        $res = CTenderixLotSpec::AddProp($fieldPropNew);
                        if (!$res) {
                            $message = new CAdminMessage(Array("MESSAGE" => GetMessage("PROP_SAVE_ERROR")));
                            $bInitVars = true;
                        }
                    }
                }
            } elseif ($TYPE_ID == 'S' || $TYPE_ID == 'R' && intval($res_lot) > 0) {
                //Update S lot
                $arFieldsProduct = $arFieldsDop["PRODUCTS"];
                $res = CTenderixLotProduct::Update($ID, $arFieldsProduct);
                if (!$res) {
                    $message = new CAdminMessage(Array("MESSAGE" => GetMessage("PRODUCT_SAVE_ERROR")));
                    $bInitVars = true;
                }
                $PRODUCT_ID_BUYER = intval($res);
                if ($PRODUCT_ID_BUYER > 0) {
                    foreach ($arFieldsDop["PRODUCTS_PROPERTY"] as $arFieldsProductProps) {
                        $arFieldsProductProps["PRODUCTS_ID"] = $PRODUCT_ID_BUYER;
                        $res = CTenderixLotProduct::UpdateProp($arFieldsProductProps["PRODUCTS_PROPERTY_BUYER"], $arFieldsProductProps);
                        if (!$res) {
                            $message = new CAdminMessage(Array("MESSAGE" => GetMessage("PROP_PRODUCT_SAVE_ERROR")));
                            $bInitVars = true;
                        }
                    }
                }
            }
        }
    } else {
        $arFields["BUYER_ID"] = $USER->GetID();
        if (CTenderixLot::CheckFields("ADD", $arFields, $arFieldsDop)) {
            $res_lot = CTenderixLot::Add($arFields);
            if ($TYPE_ID != 'S' && $TYPE_ID != 'R' && intval($res_lot) > 0) {
                $arFieldsSpec = array(
                    "FULL_SPEC" => ($FULL_SPEC == "Y" ? "Y" : "N"),
                    "NOT_ANALOG" => ($NOT_ANALOG == "Y" ? "Y" : "N"),
                    "LOT_ID" => intval($res_lot)
                );
                $res = CTenderixLotSpec::Add($arFieldsSpec);
                if (!$res) {
                    $message = new CAdminMessage(Array("MESSAGE" => GetMessage("SPEC_SAVE_ERROR")));
                    $bInitVars = true;
                }
                $SPEC_ID = intval($res);
                if ($SPEC_ID > 0) {
                    foreach ($arFieldsDop as $fieldPropNew) {
                        $fieldPropNew["SPEC_ID"] = $SPEC_ID;
                        $res = CTenderixLotSpec::AddProp($fieldPropNew);
                        if (!$res) {
                            $message = new CAdminMessage(Array("MESSAGE" => GetMessage("PROP_SAVE_ERROR")));
                            $bInitVars = true;
                        }
                    }
                }
            } elseif ($TYPE_ID == 'S' || $TYPE_ID == 'R' && intval($res_lot) > 0) {
                $arFieldsProduct = $arFieldsDop["PRODUCTS"];
                $arFieldsProduct["LOT_ID"] = intval($res_lot);

                $res = CTenderixLotProduct::Add($arFieldsProduct);
                if (!$res) {
                    $message = new CAdminMessage(Array("MESSAGE" => GetMessage("PRODUCT_SAVE_ERROR")));
                    $bInitVars = true;
                }
                $PRODUCT_ID_BUYER = intval($res);
                if ($PRODUCT_ID_BUYER > 0) {
                    foreach ($arFieldsDop["PRODUCTS_PROPERTY"] as $arFieldsProductProps) {
                        $arFieldsProductProps["PRODUCTS_ID"] = $PRODUCT_ID_BUYER;
                        $res = CTenderixLotProduct::AddProp($arFieldsProductProps);
                        if (!$res) {
                            $message = new CAdminMessage(Array("MESSAGE" => GetMessage("PROP_PRODUCT_SAVE_ERROR")));
                            $bInitVars = true;
                        }
                    }
                }
            }
        }
    }

    //Add Files
    if (intval($res_lot) > 0) {
        //Delete
        if (is_array($FILE_ID))
            foreach ($FILE_ID as $file)
                CTenderixLot::DeleteFile($ID, $file);

        //New files
        $arFiles = array();

        //Brandnew
        if (is_array($_FILES["NEW_FILE"]))
            foreach ($_FILES["NEW_FILE"] as $attribute => $files)
                if (is_array($files))
                    foreach ($files as $index => $value)
                        $arFiles[$index][$attribute] = $value;


        foreach ($arFiles as $file) {

            if (strlen($file["name"]) > 0 && intval($file["size"]) > 0) {
                $res = CTenderixLot::SaveFile($ID, $file);
                if (!$res)
                    break;
            }
        }
    }

    if (intval($res_lot) <= 0 && $e = $GLOBALS["APPLICATION"]->GetException()) {
        $message = new CAdminMessage(($ID > 0 ? GetMessage("PW_TD_ERROR_UPDATE") : GetMessage("PW_TD_ERROR_ADD")), $e);
        $bInitVars = True;
    } elseif (strlen($save) > 0)
        LocalRedirect("tenderix_lot.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
    else
        $ID = $res_lot;
}

//

if ($ID <= 0) {
    if (isset($TYPE_ID))
        $str_TYPE_ID = $TYPE_ID;
    else
        $str_TYPE_ID = 'N';
    if (isset($PRODUCTS_ID))
        $str_PROD_PRODUCTS_ID = $PRODUCTS_ID;
}

if ($ID > 0) {
    $arFilterLot["ID"] = $ID;
    if ($TENDERIXRIGHT != "W") {
        $arFilterLot["BUYER_ID"] = $USER->GetID();
    }
    $db_lot = CTenderixLot::GetList($by = "", $order = "", $arFilterLot, $is_filtered);
    if (!$db_lot->ExtractFields("str_", False))
        return;
    $ACTIVE_LOT = $str_ACTIVE;
    if ($str_TYPE_ID == 'N') {
        $db_lot_spec = CTenderixLotSpec::GetListSpec($by = "", $order = "", array("LOT_ID" => $ID), $is_filtered);
        $db_lot_spec->ExtractFields("str_", False);
    }
    if ($str_TYPE_ID == 'S') {
        $db_lot_prod = CTenderixProducts::GetListBuyer(array("LOT_ID" => $ID));
        $db_lot_prod->ExtractFields("str_PROD_", False);
    }
    if ($str_TYPE_ID == 'R') {
        $db_lot_prod = CTenderixProducts::GetListBuyer(array("LOT_ID" => $ID));
        $db_lot_prod->ExtractFields("str_PROD_", False);
    }    
    if ($str_PRIVATE == "Y") {
        $rs_lot_private = CTenderixLot::GetUserPrivateLot($ID);
        while ($ar_lot_private = $rs_lot_private->Fetch()) {
            $str_PRIVATE_USER_ID[] = $ar_lot_private["USER_ID"];
        }
    }
}
if ($str_TYPE_ID == 'S' || $str_TYPE_ID == 'R') {
    $db_lot_prod = CTenderixProducts::GetList($by = "", $order = "", array("ID" => $str_PRODUCTS_ID), $is_filtered);
    $db_lot_prod->ExtractFields("str_", False);
}

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_tx_lot", "", "str_");
    if ($str_TYPE_ID == 'N' || $str_TYPE_ID == 'NR') {
        $DB->InitTableVarsForEdit("b_tx_spec_buyer", "", "str_");
    }
    if ($str_TYPE_ID == 'S' && $ID <= 0) {
        die();
        $DB->InitTableVarsForEdit("b_tx_prod_buyer", "", "str_PROD_");
    }
    if ($str_TYPE_ID == 'R' && $ID <= 0) {
        die();
        $DB->InitTableVarsForEdit("b_tx_prod_buyer", "", "str_PROD_");
    }
    if ($str_PRIVATE == "Y") {
        $str_PRIVATE_USER_ID = $PRIVATE_LIST;
    }
}

$curr_name = array();
if (CModule::IncludeModule("currency")) {
    $lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
    while ($lcur_res = $lcur->Fetch()) {
        $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
        $arCur = $rsCur->Fetch();
        $curr_name[$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? CurrencyFormat($arCur["RATE"], $lcur_res["CURRENCY"]) : "";
    }
}


$sDocTitle = ($ID > 0) ? str_replace("#ID#", $ID, GetMessage("PW_TD_TITLE_UPDATE")) : GetMessage("PW_TD_TITLE_ADD");
$APPLICATION->SetTitle($sDocTitle);

$rsCatalog = CTenderixSection::GetCatalogList($by = "id", $order = "asc", array("ACTIVE"=>"Y"));
while ($arCatalog = $rsCatalog->GetNext()) {
    $arCat[$arCatalog["CATALOG_ID"]][] = $arCatalog;
} 
$arCat = CTenderixSection::BuildTree($arCat, 0, 0);

$rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", array("ACTIVE"=>"Y"), $is_filtered = false);
while ($arSection = $rsSection->Fetch()) {
    $arSec[$arSection["CATALOG_ID"]][] = $arSection;
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

/* * ****************************************************************** */
/* * ******************  BODY  **************************************** */
/* * ****************************************************************** */
?>

<?
$aMenu = array(
    array(
        "TEXT" => GetMessage("PW_TD_2FLIST"),
        "LINK" => "/bitrix/admin/tenderix_lot.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_list",
    )
);

if ($ID > 0 && $TENDERIXRIGHT >= "S") {
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_NEW_LOT"),
        "LINK" => "/bitrix/admin/tenderix_lot_edit.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_new",
    );

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_DELETE_LOT"),
        "LINK" => "javascript:if(confirm('" . GetMessage("PW_TD_DELETE_LOT_CONFIRM") . "')) window.location='/bitrix/admin/tenderix_lot.php?action=delete&ID[]=" . $ID . "&lang=" . LANG . "&" . bitrix_sessid_get() . "#tb';",
        "ICON" => "btn_delete",
    );
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
    echo $message->Show();
?>

<script type="text/javascript">
    $(function() {
        $("#private").click(function() {
            if($(this).is(":checked")) {
                $("#supplier-list").show(); 
            } else { 
                $("#supplier-list").hide();
            }
        });
    });
</script>

<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" name="lot_edit" enctype="multipart/form-data">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_TAB_LOT"), "ICON" => "tenderix_menu_icon", "TITLE" => GetMessage("PW_TD_TAB_LOT_DESCR")),
        array("DIV" => "edit2", "TAB" => GetMessage("PW_TD_TAB_DELIVERY_PAYMENT"), "ICON" => "tenderix_menu_icon", "TITLE" => GetMessage("PW_TD_TAB_DELIVERY_PAYMENT_DESCR")),
        array("DIV" => "edit3", "TAB" => ($str_TYPE_ID != 'S' && $str_TYPE_ID != 'R' ? GetMessage("PW_TD_TAB_SPEC") : GetMessage("PW_TD_TAB_PRODUCT")), "ICON" => "tenderix_menu_icon", "TITLE" => ($str_TYPE_ID != 'S' && $str_TYPE_ID != 'R' ? GetMessage("PW_TD_TAB_SPEC_DESCR") : GetMessage("PW_TD_TAB_PRODUCT_DESCR"))),
        array("DIV" => "edit4", "TAB" => GetMessage("PW_TD_TAB_ATTACH"), "ICON" => "tenderix_menu_icon", "TITLE" => GetMessage("PW_TD_TAB_ATTACH_DESCR")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    $tabControl->Begin();
    ?>

    <?
    $tabControl->BeginNextTab();

    if ($ID <= 0) {
        global $USER;
        $rsUser = CTenderixUserBuyer::GetByID($USER->GetID());
        $arUser = $rsUser->Fetch();
        $str_COMPANY_ID = $arUser["COMPANY_ID"];
        $str_RESPONSIBLE_FIO = $arUser["LAST_NAME"] . " " . $arUser["NAME"] . " " . $arUser["SECOND_NAME"];

        $ACTIVE_LOT = "Y";
        $str_PROD_COUNT = 1;
        $str_TIME_EXTENSION = 0;
        $str_TIME_UPDATE = 600;
    }
    ?>

    <? if ($ID > 0): ?>
        <tr>
            <td width="40%"><?= GetMessage("PW_TD_NUM_LOT") ?>:</td>
            <td width="60%"><? echo $ID ?></td>
        </tr>
    <? endif; ?>

    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_ACTIVE") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "ACTIVE", "Y", $ACTIVE_LOT, false) ?>
        </td>
    </tr>

    <? if ($ID <= 0): ?>
        <tr>
            <td width="40%"><span class="required">*</span><?= GetMessage("PW_TD_TYPE_PRODUCT") ?>:</td>
            <td width="60%">
                <select onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;" <?php echo (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "") ?>>
                    <option<?= $str_TYPE_ID == 'N' ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=N", array("TYPE_ID", "tabControl_active_tab")) ?>"><?= GetMessage("PW_TD_TYPE_PRODUCT_NST") ?></option>  
                    <option<?= $str_TYPE_ID == 'S' ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=S", array("TYPE_ID", "tabControl_active_tab")) ?>"><?= GetMessage("PW_TD_TYPE_PRODUCT_ST") ?></option>  
                    <option<?= $str_TYPE_ID == 'P' ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=P", array("TYPE_ID", "tabControl_active_tab")) ?>"><?= GetMessage("PW_TD_TYPE_SALE") ?></option>  
                    <option<?= $str_TYPE_ID == 'NR' ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=NR", array("TYPE_ID", "tabControl_active_tab")) ?>"><?= GetMessage("PW_TD_TYPE_PRODUCT_NR") ?></option>  
                    <option<?= $str_TYPE_ID == 'R' ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=R", array("TYPE_ID", "tabControl_active_tab")) ?>"><?= GetMessage("PW_TD_TYPE_PRODUCT_R") ?></option>  
                </select>
                <input type="hidden" value="<?= $str_TYPE_ID ?>" name="TYPE_ID" />
            </td>
        </tr>
    <? else: ?>
        <input type="hidden" value="<?= $str_TYPE_ID ?>" name="TYPE_ID" />
    <? endif; ?>

    <? if ($str_TYPE_ID != 'S' && $str_TYPE_ID != 'R'): ?>
        <tr>
            <td width="40%">
                <span class="required">*</span><?= GetMessage("PW_TD_TITLE") ?>:
            </td>
            <td width="60%">
                <input type="text" name="TITLE" value="<?= htmlspecialcharsEx($str_TITLE) ?>" size="50" />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <span class="required">*</span><?= GetMessage("PW_TD_SECTION_NAME") ?>:
            </td>
            <td width="60%">
                <?
                //echo SelectBox("SECTION_ID", CTenderixSection::GetDropDownList(), "--", htmlspecialchars($str_SECTION_ID));
                ?>
				<select name="SECTION_ID">
                            <option value="">--</option>
                            <? foreach ($arSec[0] as $sec): ?>
                                <option<?= $str_SECTION_ID == $sec["ID"] ? " selected" : ""; ?> value="<?= $sec["ID"] ?>"><?= $sec["TITLE"] ?></option>
                            <? endforeach; ?>
                            <? foreach ($arCat as $cat_id => $cat_name) : ?>
                                <optgroup label="<?= $cat_name ?>">
                                    <? foreach ($arSec[$cat_id] as $sec): ?>
                                        <option<?= $str_SECTION_ID == $sec["ID"] ? " selected" : ""; ?> value="<?= $sec["ID"] ?>"><?= $sec["TITLE"] ?></option>
                                    <? endforeach; ?>
                                </optgroup>
                            <? endforeach; ?>
                </section>
            </td>
        </tr>
        <? if ($str_TYPE_ID == 'P'): ?>
            <input type="hidden" name="NOT_ANALOG" value="Y" />
        <? endif; ?>
        <? if ($str_TYPE_ID == 'N' || $str_TYPE_ID == 'NR'): ?>
            <tr>
                <td width="40%">
                    <?= GetMessage("PW_TD_FULL_SPEC") ?>:
                </td>
                <td width="60%">
                    <?= InputType("checkbox", "FULL_SPEC", "Y", $str_FULL_SPEC, false) ?>
                </td>
            </tr>
            <tr>
                <td width="40%">
                    <?= GetMessage("PW_TD_NOT_ANALOG") ?>:
                </td>
                <td width="60%">
                    <?= InputType("checkbox", "NOT_ANALOG", "Y", $str_NOT_ANALOG, false) ?>
                </td>
            </tr>
        <? endif; ?>

    <? elseif ($str_TYPE_ID == 'S' || $str_TYPE_ID == 'R'): ?>
        <tr>
            <td width="40%"><span class="required">*</span><?= GetMessage("PW_TD_LIST_PRODUCT") ?>:</td>
            <td width="60%">
            
                <select<?= $ID > 0 ? " disabled" : ""; ?> onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;" <?php echo (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "") ?>>
                    <option value="<?= $APPLICATION->GetCurPageParam("PRODUCTS_ID=0", array("PRODUCTS_ID", "tabControl_active_tab")) ?>">--</option>
                    <?
                    $rsProducts = CTenderixProducts::GetList();
                    while ($arProducts = $rsProducts->Fetch()):
                        $select_section = $str_PROD_PRODUCTS_ID == $arProducts["ID"] ? " selected" : "";
                        ?>
                        <option<?= $select_section ?> value="<?= $APPLICATION->GetCurPageParam("PRODUCTS_ID=" . $arProducts["ID"], array("PRODUCTS_ID", "tabControl_active_tab")) ?>"><?= $arProducts["TITLE"] ?></option>  

                    <? endwhile; ?>
                </select>
                <input type="hidden" value="<?= $str_PROD_PRODUCTS_ID ?>" name="PRODUCTS_ID" />
            </td>
        </tr>
    <? endif; ?>


    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_OPEN_PRICE") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "OPEN_PRICE", "Y", $str_OPEN_PRICE, false) ?>
        </td>
    </tr>
    <? if ($str_TYPE_ID != 'P'): ?>
        <tr>
            <td width="40%" class="left-col">
                <?= GetMessage("PW_TD_PRIVATE") ?>:
            </td>
            <td width="60%" class="right-col">
                <input type="checkbox" id="private" name="PRIVATE" value="Y"<?= $str_PRIVATE == "Y" ? " checked" : ""; ?> />
            </td>
        </tr>
        <tr<?= $str_PRIVATE != "Y" ? ' style="display:none;"' : ''; ?> id="supplier-list">
            <td width="40%" class="left-col">
                <?= GetMessage("PW_TD_PRIVATE_USER") ?>:
            </td>
            <td width="60%" class="right-col">
                <select name="PRIVATE_LIST[]" multiple size="10">
                    <?
                    $rsSupplier = CTenderixUserSupplier::GetListUser(array("NAME_COMPANY" => "ASC"), array());
                    while ($arSupplier = $rsSupplier->Fetch()):
                        ?>
                        <option value="<?= $arSupplier["USER_ID"] ?>"<?= in_array($arSupplier["USER_ID"], $str_PRIVATE_USER_ID) ? " selected" : "" ?>><?= $arSupplier["NAME_COMPANY"] ?></option>
                    <? endwhile; ?>
                </select>
            </td>
        </tr>
    <? endif; ?>
    <tr>
        <td width="40%">
            <span class="required">*</span><?= GetMessage("PW_TD_COMPANY_LOT") ?>:
        </td>
        <td width="60%">
            <select name="COMPANY_ID">
                <?
                $rsCompany = CTenderixCompany::GetList($by = "s_title", $order = "desc", array(), $is_filtered);
                while ($rsCompany->ExtractFields("s_")) {
                    ?>
                    <option value="<? echo $s_ID ?>"<?= $s_ID == $str_COMPANY_ID ? " selected" : "" ?>><? echo $s_TITLE ?></option><?
            }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="required">*</span><?= GetMessage("PW_TD_RESPONSIBLE_FIO") ?>:
        </td>
        <td width="60%">
            <input type="text" name="RESPONSIBLE_FIO" value="<?= htmlspecialcharsEx($str_RESPONSIBLE_FIO) ?>" size="50" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="required">*</span><?= GetMessage("PW_TD_RESPONSIBLE_PHONE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="RESPONSIBLE_PHONE" value="<?= htmlspecialcharsEx($str_RESPONSIBLE_PHONE) ?>" size="50" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="required">*</span><?= GetMessage("PW_TD_LOT_DATE") . " (" . CLang::GetDateFormat() . ")" ?>:
        </td>
        <td width="60%">
            <?= CalendarPeriod("DATE_START", $str_DATE_START, "DATE_END", $str_DATE_END, "lot_edit", "N", false, false, "19") ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_TIME_UP") ?>:
        </td>
        <td width="60%">
            <input type="text" name="TIME_EXTENSION" value="<?= htmlspecialcharsEx($str_TIME_EXTENSION) ?>" size="10" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_TIME_UPDATE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="TIME_UPDATE" value="<?= htmlspecialcharsEx($str_TIME_UPDATE) ?>" size="10" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_TYPE_NDS") ?>:
        </td>
        <td width="60%">
            <select name="WITH_NDS">
                <option<?= $str_WITH_NDS == "Y" ? " selected" : ""; ?> value="Y"><?= GetMessage("PW_TD_PRICE_NDS") ?></option>
                <option<?= $str_WITH_NDS == "N" ? " selected" : ""; ?> value="N"><?= GetMessage("PW_TD_PRICE_NDS_N") ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_CURRENCY") ?>:
        </td>
        <td width="60%">
            <select name="CURRENCY">
                <? foreach ($curr_name as $nameCurrency => $arCurrency): ?>
                    <option<?
                if ($str_CURRENCY == $nameCurrency || $nameCurrency == $_REQUEST["CURRENCY"])
                    echo " selected";
                    ?> value="<?= $nameCurrency ?>"><?= $nameCurrency ?><?
                    if (strlen($arCurrency) > 0)
                        echo " [" . $arCurrency . "]";
                    ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_DATE_DELIVERY") ?>:
        </td>
        <td width="60%">
            <input type="text" name="DATE_DELIVERY" value="<?= htmlspecialcharsEx($str_DATE_DELIVERY) ?>" size="50" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_NOTE_LOT") ?>:
        </td>
        <td width="60%">
            <textarea name="NOTE" cols="50" rows="5"><?= htmlspecialcharsEx($str_NOTE) ?></textarea>
        </td>
    </tr>


    <?
    $tabControl->BeginNextTab();
    /*     * ************
      DELIVERY_SECTION
     * ************ */
    ?>
    <tr class="heading">
        <td colspan="2"><?= GetMessage("PW_TD_DELIVERY_SECTION") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_DELIVERY_SELECT") ?>:
        </td>
        <td width="60%">
            <? echo CTenderixSprDetails::SelectBoxDelivery("TERM_DELIVERY_ID", $str_TERM_DELIVERY_ID); ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_DELIVERY_VALUE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="TERM_DELIVERY_VAL" value="<?= htmlspecialcharsEx($str_TERM_DELIVERY_VAL) ?>" size="50" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_DELIVERY_REQUIRED") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "TERM_DELIVERY_REQUIRED", "Y", $str_TERM_DELIVERY_REQUIRED, false) ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_DELIVERY_EDIT") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "TERM_DELIVERY_EDIT", "Y", $str_TERM_DELIVERY_EDIT, false) ?>
        </td>
    </tr>
    <?
    /*     * ************
      PAYMENT_SECTION
     * ************ */
    ?>
    <tr class="heading">
        <td colspan="2"><?= GetMessage("PW_TD_PAYMENT_SECTION") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_PAYMENT_SELECT") ?>:
        </td>
        <td width="60%">
            <? echo CTenderixSprDetails::SelectBoxPayment("TERM_PAYMENT_ID", $str_TERM_PAYMENT_ID); ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_PAYMENT_VALUE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="TERM_PAYMENT_VAL" value="<?= htmlspecialcharsEx($str_TERM_PAYMENT_VAL) ?>" size="50" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_PAYMENT_REQUIRED") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "TERM_PAYMENT_REQUIRED", "Y", $str_TERM_PAYMENT_REQUIRED, false) ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_PAYMENT_EDIT") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "TERM_PAYMENT_EDIT", "Y", $str_TERM_PAYMENT_EDIT, false) ?>
        </td>
    </tr>

    <?
    /*     * ************
      NESTANDART LOT SECTION
     * ************ */
    $tabControl->BeginNextTab();
    if ($str_TYPE_ID != 'S' && $str_TYPE_ID != 'R') :
        ?>
        <tr>
            <td valign="top" colspan="2">
                <table cellpadding="0" cellspacing="0" width="100%" class="internal">
                    <tr class="heading">
                        <td align="center" width="5">¹</td>
                        <td align="center" width="40"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_NAME") ?></td>
                        <td align="center" width="40"><? echo GetMessage("PW_TD_SPEC_DOP") ?></td>
                        <td align="center" width="10"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_UNIT") ?></td>
                        <td align="center" width="20"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_COUNT") ?></td>
                        <td align="center" width="30"><? echo GetMessage("PW_TD_SPEC_START_PRICE") ?></td>
                        <td align="center" width="30"><? echo GetMessage("PW_TD_SPEC_STEP_PRICE") ?></td>
                        <td align="center" width="5"><? echo GetMessage("PW_TD_SPEC_DEL") ?></td>
                    </tr>


                    <?
                    $i = 0;
                    $numProp = 0;

                    function _GetOldAndNew($elements) {
                        global $i, $numProp;

                        $numProp++;
                        if ($i == 0 && ($tmp = $elements->ExtractFields("str_PROP_")))
                            return $tmp;

                        global $str_PROP_ID, $str_PROP_TITLE, $str_PROP_ADD_INFO,
                        $str_PROP_UNIT_ID, $str_PROP_COUNT, $str_PROP_START_PRICE, $str_PROP_STEP_PRICE;

                        if ($i > 3)
                            return false;

                        $str_PROP_ID = "n" . $i;
                        $str_PROP_TITLE = "";
                        $str_PROP_ADD_INFO = "";
                        $str_PROP_UNIT_ID = "";
                        $str_PROP_COUNT = "";
                        $str_PROP_START_PRICE = "";
                        $str_PROP_STEP_PRICE = "";

                        $i++;

                        return true;
                    }

                    $prop = CTenderixLotSpec::GetListProp($ID);
                    while ($r = _GetOldAndNew($prop)):

                        if ($bInitVars) {
                            $DB->InitTableVarsForEdit("b_tx_spec_property_b", "PROP_" . $str_PROP_ID . "_", "str_PROP_");
                        }
                        ?>
                        <tr>
                            <td align="center" width="5"><? echo $numProp ?></td>
                            <td align="center" width="120">
                                <input type="text" name="PROP_<?= $str_PROP_ID ?>_TITLE" value="<?= $str_PROP_TITLE ?>" style="width: 98%">
                            </td>
                            <td align="center" width="80">
                                <input type="text" name="PROP_<?= $str_PROP_ID ?>_ADD_INFO" value="<?= $str_PROP_ADD_INFO ?>" style="width: 98%">
                            </td>
                            <td align="center" width="20">
                                <? echo CTenderixSprDetails::SelectBoxUnit("PROP_" . $str_PROP_ID . "_UNIT_ID", $str_PROP_UNIT_ID); ?>
                            </td>
                            <td align="center" width="20">
                                <input type="text" name="PROP_<?= $str_PROP_ID ?>_COUNT" value="<?= $str_PROP_COUNT ?>" style="width: 98%">
                            </td>
                            <td align="center" width="50">
                                <input type="text" name="PROP_<?= $str_PROP_ID ?>_START_PRICE" value="<?= $str_PROP_START_PRICE ?>" style="width: 98%">
                            </td>
                            <td align="center" width="50">
                                <input type="text" name="PROP_<?= $str_PROP_ID ?>_STEP_PRICE" value="<?= $str_PROP_STEP_PRICE ?>" style="width: 98%">
                            </td>


                            <td align="center" width="5">
                                <? if (intval($str_PROP_ID) > 0): ?>
                                    <input type="checkbox" name="PROP_<?= $str_PROP_ID ?>_DEL" value="Y">
                                <? endif ?>
                                <input type="hidden" name="PROP_HIDDEN_ID[]" value="<?= $str_PROP_ID ?>">
                            </td>
                        </tr>
                    <? endwhile; ?>

                </table>

            </td>
        </tr>
    <? elseif ($str_TYPE_ID == 'S' || $str_TYPE_ID == 'R'): ?>
        <? if ($str_PROD_PRODUCTS_ID > 0): ?>
            <?
            /*             * ************
              STANDART LOT SECTION
             * ************ */
            ?>
            <?
            if ($ID <= 0) {
                $str_PROP_PROD_VISIBLE = "Y";
            }
            ?>
            <tr>
                <td valign="top" colspan="2">
                    <table cellpadding="0" cellspacing="0" width="100%" class="internal">
                        <tr class="heading">
                            <td align="center" width="5"><? echo GetMessage("PW_TD_PRODUCT_PROP_VISIBLE") ?></td>
                            <td align="center" width="40"><? echo GetMessage("PW_TD_PRODUCT_NAME") ?></td>
                            <td align="center" width="40"><? echo GetMessage("PW_TD_PRODUCT_VALUE") ?></td>
                            <td align="center" width="10"><? echo GetMessage("PW_TD_PRODUCT_REQUIRED") ?></td>
                            <td align="center" width="20"><? echo GetMessage("PW_TD_PRODUCT_EDIT") ?></td>
                        </tr>


                        <?
                        $rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $str_PROD_PRODUCTS_ID), $is_filtered);
                        while ($rsProps->ExtractFields("str_PROP_PROD_")):
                            if ($ID > 0) {
                                $str_PROD_ID2 = $str_PROD_ID;
                                $rsProps2 = CTenderixProductsProperty::GetListBuyer(Array("PRODUCTS_ID" => $str_PROD_ID, "PRODUCTS_PROPERTY_ID" => $str_PROP_PROD_ID));
                                $rsProps2->ExtractFields("str_PROP_PROD_");
                                $DB->InitTableVarsForEdit("b_tx_prod_buyer", "", "str_PROD_");
                                $str_PROD_ID = $str_PROD_ID2;
                            }
                            if ($bInitVars) {
                                $str_PROP_PROD_REQUIRED = "";
                                $str_PROP_PROD_EDIT = "";
                                $str_PROP_PROD_VISIBLE = ${"PROP_PROD_" . $str_PROP_PROD_ID . "_VISIBLE"};
                                $DB->InitTableVarsForEdit("b_tx_prod_property", "PROP_PROD_" . $str_PROP_PROD_ID . "_", "str_PROP_PROD_");
                            }
                            ?>
                            <tr>
                                <td align="center" width="5">
                                    <?= InputType("checkbox", "PROP_PROD_" . $str_PROP_PROD_ID . "_VISIBLE", "Y", $str_PROP_PROD_VISIBLE, false) ?>
                                </td>
                                <td align="center" width="120">
                                    <? echo $str_PROP_PROD_TITLE ?>
                                </td>
                                <td align="left" width="80">
                                    <? if ($str_PROP_PROD_SPR_ID > 0): ?>
                                        <?= CTenderixSprDetails::SelectBoxID("PROP_PROD_" . $str_PROP_PROD_ID . "_VALUE", $str_PROP_PROD_VALUE, $str_PROP_PROD_SPR_ID); ?>
                                    <? else: ?>
                                        <input type="text" name="PROP_PROD_<?= $str_PROP_PROD_ID ?>_VALUE" value="<?= $str_PROP_PROD_VALUE ?>" style="width: 98%">
                                    <? endif; ?>
                                </td>
                                <td align="center" width="20">
                                    <?= InputType("checkbox", "PROP_PROD_" . $str_PROP_PROD_ID . "_REQUIRED", "Y", $str_PROP_PROD_REQUIRED, false) ?>
                                </td>
                                <td align="center" width="20">
                                    <?= InputType("checkbox", "PROP_PROD_" . $str_PROP_PROD_ID . "_EDIT", "Y", $str_PROP_PROD_EDIT, false) ?>
                                </td>
                            </tr>
                        <? endwhile; ?>
                        <tr>
                            <td align="center" width="5"></td>
                            <td align="center" width="120"><?= GetMessage("PW_TD_STANDART_COUNT_NAME") ?></td>
                            <td align="left" width="80">
                                <input type="text" name="COUNT" value="<?= $str_PROD_COUNT ?>" style="width: 98%">
                            </td>
                            <td align="center" width="20"></td>
                            <td align="center" width="20">
                                <?= InputType("checkbox", "COUNT_EDIT", "Y", $str_PROD_COUNT_EDIT, false) ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" width="5"></td>
                            <td align="center" width="120"><?= GetMessage("PW_TD_STANDART_UNIT_NAME") ?></td>
                            <td align="left" width="80"><input type="text" value="<?= $str_UNIT_NAME ?>" disabled /></td>
                            <td align="center" width="20"></td>
                            <td align="center" width="20"></td>
                        </tr>
                    </table>

                </td>
            </tr>

            <tr class="heading">
                <td colspan="2"><?= GetMessage("PW_TD_STANDART_PRICE_SECTION") ?></td>
            </tr>
            <tr>
                <td width="40%">
                    <?= GetMessage("PW_TD_STANDART_START_PRICE") ?>:
                </td>
                <td width="60%">
                    <input type="text" name="START_PRICE" value="<?= htmlspecialcharsEx($str_PROD_START_PRICE) ?>" size="50" />
                </td>
            </tr>
            <tr>
                <td width="40%">
                    <?= GetMessage("PW_TD_STANDART_STEP_PRICE") ?>:
                </td>
                <td width="60%">
                    <input type="text" name="STEP_PRICE" value="<?= htmlspecialcharsEx($str_PROD_STEP_PRICE) ?>" size="50" />
                </td>
            </tr>
        <? else: ?>
            <tr><td>
                    <? echo GetMessage("PW_TD_PRODUCTS_NOSELECT") ?>
                </td></tr>
        <? endif; ?>
    <? endif; ?>

    <?
    /*     * ***************
     * ATTACH
     * *************** */
    $tabControl->BeginNextTab();
    ?>
    <? if ($ID > 0 && ($rsFiles = CTenderixLot::GetFileList($ID)) && ($arFile = $rsFiles->GetNext())): ?>
        <tr>
            <td valign="top"><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:</td>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" class="internal">
                    <tr class="heading">
                        <td align="center"><? echo GetMessage("PW_TD_FILE_NAME") ?></td>
                        <td align="center"><? echo GetMessage("PW_TD_FILE_SIZE") ?></td>
                        <td align="center"><? echo GetMessage("PW_TD_FILE_DELETE") ?></td>
                    </tr>
                    <?
                    do {
                        ?>
                        <tr>
                            <td><a href="tenderix_lot_file.php?LOT_ID=<? echo $ID ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
                            <td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
                            <td align="center">
                                <input type="checkbox" name="FILE_ID[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
                            </td>
                        </tr>
                        <?
                    } while ($arFile = $rsFiles->GetNext());
                    ?>
                </table></td>
        </tr>
    <? endif; ?>
    <tr>
        <td valign="top"><?= GetMessage("PW_TD_ATTACH_LOAD") ?>:</td>
        <td>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr><td><? echo CFile::InputFile("NEW_FILE[n0]", 40, 0) ?></td></tr>
                <tr><td><? echo CFile::InputFile("NEW_FILE[n1]", 40, 0) ?></td></tr>
                <tr><td><? echo CFile::InputFile("NEW_FILE[n2]", 40, 0) ?></td></tr>
            </table>
        </td>
    </tr>

    <?
    $tabControl->EndTab();
    ?>

    <?
    $tabControl->Buttons(
            array(
                "disabled" => ($TENDERIXRIGHT < "S"),
                "back_url" => "/bitrix/admin/tenderix_lot.php?lang=" . LANG . "&" . GetFilterParams("filter_", false)
            )
    );
    $tabControl->End();
    ?>
</form>
<? echo BeginNote(); ?>
<span class="required">*</span> - <? echo GetMessage("REQUIRED_FIELDS") ?>
<? echo EndNote(); ?>
<?
$tabControl->ShowWarnings("lot_edit", $message);
?>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>