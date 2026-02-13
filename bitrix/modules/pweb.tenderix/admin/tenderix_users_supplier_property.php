<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = "pweb.tenderix";
$sTableID = "tbl_tenderix_users_supplier_property";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/prolog.php");
$TENDERIXRIGHT = $APPLICATION->GetGroupRight($module_id);
if ($TENDERIXRIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/include.php");

IncludeModuleLangFile(__FILE__);
$err_mess = "File: " . __FILE__ . "<br>Line: ";

$arFilterFields = Array();
/* * ******************************************************************
  Actions
 * ****************************************************************** */
$lAdmin->InitFilter($arFilterFields);

$arFilter = Array();

if ($lAdmin->EditAction() && $TENDERIXRIGHT >= "W" && check_bitrix_sessid()) {
    $bupdate = false;
    foreach ($FIELDS as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID))
            continue;
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $arFieldsStore = Array(
            "ACTIVE" => "'" . $DB->ForSql($arFields["ACTIVE"]) . "'",
            "SORT" => "'" . intval($arFields["SORT"]) . "'",
            "TITLE" => "'" . $DB->ForSql($arFields["TITLE"]) . "'",
            "CODE" => "'" . $DB->ForSql($arFields["CODE"]) . "'",
            "IS_REQUIRED" => "'" . $DB->ForSql($arFields["IS_REQUIRED"]) . "'",
            "MULTI" => "'" . $DB->ForSql($arFields["MULTI"]) . "'"
        );
        if (!$DB->Update("b_tx_supplier_property", $arFieldsStore, "WHERE ID='$ID'", $err_mess . __LINE__)) {
            $lAdmin->AddUpdateError(GetMessage("SAVE_ERROR") . $ID . ": " . GetMessage("PW_TD_SAVE_ERROR"), $ID);
            $DB->Rollback();
        }
        else
            $bupdate = true;

        $DB->Commit();
    }

    if ($bupdate)
        $CACHE_MANAGER->CleanDir("b_tx_supplier_property");
}


if (($arID = $lAdmin->GroupAction()) && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    if ($_REQUEST['action_target'] == 'selected') {
        $arID = Array();
        $rsData = CTenderixUserSupplierProperty::GetList($by, $order, $arFilter);
        while ($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }

    foreach ($arID as $ID) {
        if (strlen($ID) <= 0)
            continue;
        $ID = IntVal($ID);
        switch ($_REQUEST['action']) {
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if (!CTenderixUserSupplierProperty::Delete($ID)) {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                }
                $DB->Commit();
                break;
            case "activate":
            case "deactivate":
                $arFields = Array("ACTIVE" => ($_REQUEST['action'] == "activate" ? "'Y'" : "'N'"));
                if (!$DB->Update("b_tx_supplier_property", $arFields, "WHERE ID='$ID'", $err_mess . __LINE__))
                    $lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
                else
                    $CACHE_MANAGER->CleanDir("b_tx_supplier_property");
                break;
        }
    }
}
$rsData = CTenderixUserSupplierProperty::GetList($by, $order, $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "sort" => "ID", "default" => true),
    array("id" => "TITLE", "content" => GetMessage("PW_TD_TITLE"), "sort" => "TITLE", "default" => true),
    array("id" => "ACTIVE", "content" => GetMessage("PW_TD_ACTIVE"), "sort" => "ACTIVE", "default" => true),
    array("id" => "SORT", "content" => GetMessage("PW_TD_SORT"), "sort" => "SORT", "default" => true),
    array("id" => "CODE", "content" => GetMessage("PW_TD_CODE"), "sort" => "CODE", "default" => true),
    array("id" => "PROPERTY_TYPE", "content" => GetMessage("PW_TD_PROPERTY_TYPE"), "sort" => "PROPERTY_TYPE", "default" => true),
    array("id" => "IS_REQUIRED", "content" => GetMessage("PW_TD_IS_REQUIRED"), "sort" => "", "default" => true),
    array("id" => "MULTI", "content" => GetMessage("PW_TD_MULTI"), "sort" => "", "default" => true),
        )
);

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row = & $lAdmin->AddRow($f_ID, $arRes);

    if ($TENDERIXRIGHT == "W") {
        $row->AddCheckField("ACTIVE");
        $row->AddCheckField("IS_REQUIRED");
        $row->AddCheckField("MULTI");
        $row->AddInputField("SORT", Array("size" => "3"));
        $row->AddInputField("TITLE", Array("size" => "35"));
        $row->AddInputField("CODE", Array("size" => "35"));
        $row->AddViewField("TITLE", '<a href="tenderix_users_supplier_property_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_ID . '" title="' . GetMessage("PW_TD_EDIT_TITLE") . '">' . $f_TITLE . '</a>');
    } else {
        $row->AddViewField("ACTIVE", ($f_ACTIVE == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    }

    $row->AddViewField("IS_REQUIRED", ($f_IS_REQUIRED == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    $row->AddViewField("MULTI", ($f_MULTI == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    switch ($f_PROPERTY_TYPE) {
        case "S":
            $descr_PROPERTY_TYPE = GetMessage("PW_TD_PROPERTY_S");
            break;
        case "N":
            $descr_PROPERTY_TYPE = GetMessage("PW_TD_PROPERTY_N");
            break;
        case "F":
            $descr_PROPERTY_TYPE = GetMessage("PW_TD_PROPERTY_F");
            break;
        case "L":
            $descr_PROPERTY_TYPE = GetMessage("PW_TD_PROPERTY_L");
            break;
        case "T":
            $descr_PROPERTY_TYPE = GetMessage("PW_TD_PROPERTY_T");
            break;
        case "D":
            $descr_PROPERTY_TYPE = GetMessage("PW_TD_PROPERTY_D");
            break;
    }
    $row->AddViewField("PROPERTY_TYPE", ($descr_PROPERTY_TYPE));

    $arActions = Array();
    $arActions[] = array("DEFAULT" => "Y", "ICON" => "edit", "TEXT" => GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION" => $lAdmin->ActionRedirect("tenderix_users_supplier_property_edit.php?ID=" . $f_ID));
    $arActions[] = array("SEPARATOR" => true);
    $arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION" => "if(confirm('" . GetMessage("PW_TD_CONFIRM_DEL_ENT") . "')) window.location='tenderix_users_supplier_property.php?lang=" . LANGUAGE_ID . "&action=delete&ID=$f_ID&" . bitrix_sessid_get() . "'");

    if ($TENDERIXRIGHT == "W")
        $row->AddActions($arActions);
}

$lAdmin->AddFooter(
        array(
            array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
            array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
        )
);

if ($TENDERIXRIGHT == "W")
    $lAdmin->AddGroupActionTable(Array(
        "delete" => GetMessage("PW_TD_DELETE"),
        "activate" => GetMessage("PW_TD_ACTIVATE"),
        "deactivate" => GetMessage("PW_TD_DEACTIVATE"),
    ));

if ($TENDERIXRIGHT == "W") {
    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_CREATE"),
        "TITLE" => GetMessage("PW_TD_ADD_TITLE"),
        "LINK" => "tenderix_users_supplier_property_edit.php?lang=" . LANG,
        "ICON" => "btn_new"
    );

    $aContext = $aMenu;
    $lAdmin->AddAdminContextMenu($aContext);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("PW_TD_PAGE_TITLE"));
require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<a name="tb"></a>

<?
$lAdmin->DisplayList();

require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
