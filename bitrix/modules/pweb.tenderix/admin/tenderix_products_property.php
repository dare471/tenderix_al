<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_tenderix_products_property";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/prolog.php");
$TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
if ($TENDERIXRIGHT<"W")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/include.php");

IncludeModuleLangFile(__FILE__);
$err_mess = "File: " . __FILE__ . "<br>Line: ";

/* * ******************************************************************
  Actions
 * ****************************************************************** */

$arFilter = Array(
    "PRODUCTS_ID" => $find_products_id
);

$arProductsTitle = "";
if ($find_products_id > 0) {
    $db_res = CTenderixProducts::GetByID($find_products_id);
    if ($db_res && $res = $db_res->Fetch()) {
        $arProducts = $res;
        $arProductsTitle = " [ " . $arProducts["TITLE"] . " ]";
    }
}

if ($lAdmin->EditAction() && $TENDERIXRIGHT >= "W" && check_bitrix_sessid()) {
    $bupdate = false;
    foreach ($FIELDS as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID))
            continue;
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $arFieldsStore = Array(
            "ACTIVE" => "'" . $DB->ForSql($arFields["ACTIVE"]) . "'",
            "C_SORT" => "'" . intval($arFields["C_SORT"]) . "'",
            "TITLE" => "'" . $DB->ForSql($arFields["TITLE"]) . "'"
        );
        if (!$DB->Update("b_tx_prod_property", $arFieldsStore, "WHERE ID='$ID'", $err_mess . __LINE__)) {
            $lAdmin->AddUpdateError(GetMessage("SAVE_ERROR") . $ID . ": " . GetMessage("PW_TD_SAVE_ERROR"), $ID);
            $DB->Rollback();
        }
        else
            $bupdate = true;

        $DB->Commit();
    }

    if ($bupdate)
        $CACHE_MANAGER->CleanDir("b_tx_prod_property");
}


if (($arID = $lAdmin->GroupAction()) && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    if ($_REQUEST['action_target'] == 'selected') {
        $arID = Array();
        $rsData = CTenderixProducts::GetList($by, $order, $arFilter, $is_filtered);
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
                if (!CTenderixProductsProperty::Delete($ID)) {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                }
                $DB->Commit();
                break;
            case "activate":
            case "deactivate":
                $arFields = Array("ACTIVE" => ($_REQUEST['action'] == "activate" ? "'Y'" : "'N'"));
                if (!$DB->Update("b_tx_prod_property", $arFields, "WHERE ID='$ID'", $err_mess . __LINE__))
                    $lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
                else
                    $CACHE_MANAGER->CleanDir("b_tx_prod_property");
                break;
        }
    }
}

$rsData = CTenderixProductsProperty::GetList($by, $order, $arFilter, $is_filtered);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "sort" => "s_id", "default" => false),
    array("id" => "TITLE", "content" => GetMessage("PW_TD_TITLE"), "sort" => "s_title", "default" => true),
    array("id" => "REQUIRED", "content" => GetMessage("PW_TD_REQUIRED"), "sort" => "", "default" => true),
    array("id" => "EDIT", "content" => GetMessage("PW_TD_EDIT"), "sort" => "", "default" => true),
    array("id" => "VALUE", "content" => GetMessage("PW_TD_VALUE"), "sort" => "", "default" => true),
    array("id" => "ACTIVE", "content" => GetMessage("PW_TD_ACTIVE"), "sort" => "s_active", "default" => true),
    array("id" => "C_SORT", "content" => GetMessage("PW_TD_C_SORT"), "sort" => "s_c_sort", "default" => true)
        )
);

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row = & $lAdmin->AddRow($f_ID, $arRes);

    if ($TENDERIXRIGHT == "W") {
        $row->AddCheckField("ACTIVE");
        $row->AddInputField("C_SORT", Array("size" => "3"));
        $row->AddInputField("TITLE", Array("size" => "35"));
        $row->AddViewField("TITLE", '<a href="tenderix_products_property_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_ID . '&find_products_id=' . $find_products_id . '" title="' . GetMessage("PW_TD_EDIT_TITLE") . '">' . $f_TITLE . '</a>');
    } else {
        $row->AddViewField("ACTIVE", ($f_ACTIVE == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    }

    $row->AddViewField("REQUIRED", ($f_REQUIRED == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    $row->AddViewField("EDIT", ($f_EDIT == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));

    if ($f_SPR_ID > 0) {
        $arSprDetails = CTenderixSprDetails::GetByIdSPR($f_VALUE,$f_SPR_ID);
        $arSprDetailsFields = $arSprDetails->GetNext();
        $f_VALUE = $arSprDetailsFields["TITLE"];
    }
    
    $row->AddViewField("VALUE", $f_VALUE);

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
$APPLICATION->SetTitle(GetMessage("PW_TD_PAGE_TITLE") . $arProductsTitle);
require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?

$lAdmin->DisplayList();

echo BeginNote();
echo GetMessage("PW_TD_NOTE_DELETE");
echo EndNote();

require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>