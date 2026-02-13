<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_tenderix_proposal";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/prolog.php");
$TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
if ($TENDERIXRIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/include.php");

IncludeModuleLangFile(__FILE__);
$err_mess = "File: " . __FILE__ . "<br>Line: ";

/* * ******************************************************************
  Actions
 * ****************************************************************** */

$arFilter = Array(
    "LOT_ID" => $find_lot_id
);

$arLotTitle = "";
if ($find_lot_id > 0) {
    $arLot = CTenderixLot::GetByID($find_lot_id);
    $arLotTitle = $arLot["ID"]." ".$arLot["TITLE"];
}

if ($lAdmin->EditAction() && $TENDERIXRIGHT >= "W" && check_bitrix_sessid()) {
    $bupdate = false;
    foreach ($FIELDS as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID))
            continue;
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $arFieldsStore = Array();
        if (!$DB->Update("b_tx_proposal", $arFieldsStore, "WHERE ID='$ID'", $err_mess . __LINE__)) {
            $lAdmin->AddUpdateError(GetMessage("SAVE_ERROR") . $ID . ": " . GetMessage("PW_TD_SAVE_ERROR"), $ID);
            $DB->Rollback();
        }
        else
            $bupdate = true;

        $DB->Commit();
    }

    if ($bupdate)
        $CACHE_MANAGER->CleanDir("b_tx_proposal");
}


if (($arID = $lAdmin->GroupAction()) && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    if ($_REQUEST['action_target'] == 'selected') {
        $arID = Array();
        $rsData = CTenderixProposal::GetList($arFilter);
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
                if (!CTenderixProposal::Delete($ID)) {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                }
                $DB->Commit();
                break;
            case "activate":
            case "deactivate":
                $arFields = Array("ACTIVE" => ($_REQUEST['action'] == "activate" ? "'Y'" : "'N'"));
                if (!$DB->Update("b_tx_proposal", $arFields, "WHERE ID='$ID'", $err_mess . __LINE__))
                    $lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
                else
                    $CACHE_MANAGER->CleanDir("b_tx_proposal");
                break;
        }
    }
}

$rsData = CTenderixProposal::GetList($arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "sort" => "s_id", "default" => false),
    array("id" => "NAME", "content" => GetMessage("PW_TD_USER"), "sort" => "", "default" => true),
    array("id" => "TERM_PAYMENT_VAL", "content" => GetMessage("PW_TD_PAYMENT"), "sort" => "", "default" => true),
    array("id" => "TERM_DELIVERY_VAL", "content" => GetMessage("PW_TD_DELIVERY"), "sort" => "", "default" => true),

        )
);

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row = & $lAdmin->AddRow($f_ID, $arRes);

    if ($TENDERIXRIGHT == "W") {
        
    }

    $arActions = Array();
    $arActions[] = array("DEFAULT" => "Y", "ICON" => "edit", "TEXT" => GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION" => $lAdmin->ActionRedirect("tenderix_products_property_edit.php?ID=" . $f_ID . '&find_products_id=' . $find_products_id));
    if ($f_ID != '1' && $TENDERIXRIGHT == "W") {
        $arActions[] = array("SEPARATOR" => true);
        $arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION" => "if(confirm('" . GetMessage("PW_TD_CONFIRM_PRODUCTS_PROPERTY_CHANNEL") . "')) window.location='tenderix_products_property.php?lang=" . LANGUAGE_ID . '&find_products_id=' . $find_products_id . "&action=delete&ID=$f_ID&" . bitrix_sessid_get() . "'");
    }

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


$aMenu[] = array(
    "TEXT" => GetMessage("PRODUCTS_PROPERTY_LIST"),
    "TITLE" => GetMessage("PRODUCTS_PROPERTY_TITLE_LIST"),
    "LINK" => "/bitrix/admin/tenderix_products.php?lang=" . LANGUAGE_ID,
    "ICON" => "btn_list");

if ($TENDERIXRIGHT == "W") {
    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_CREATE"),
        "TITLE" => GetMessage("PW_TD_ADD_TITLE"),
        "LINK" => "tenderix_products_property_edit.php?lang=" . LANG . '&find_products_id=' . $find_products_id,
        "ICON" => "btn_new"
    );
}

$aContext = $aMenu;
$lAdmin->AddAdminContextMenu($aContext);


$lAdmin->CheckListMode();

/* * ******************************************************************
  Form
 * ****************************************************************** */
$APPLICATION->SetTitle(GetMessage("PW_TD_PAGE_TITLE") . $arLotTitle);
require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?

$lAdmin->DisplayList();

require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>