<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = "pweb.tenderix";
$sTableID = "tbl_tenderix_users_supplier_status";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/prolog.php");
$TENDERIXRIGHT = $APPLICATION->GetGroupRight($module_id);
if ($TENDERIXRIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/include.php");

IncludeModuleLangFile(__FILE__);
$err_mess = "File: " . __FILE__ . "<br>Line: ";

$arFilterFields = Array(
    "find_title"
);
/* * ******************************************************************
  Actions
 * ****************************************************************** */
$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
    "TITLE" => $find_title
);

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
        if (!$DB->Update("b_tx_supplier_status", $arFieldsStore, "WHERE ID='$ID'", $err_mess . __LINE__)) {
            $lAdmin->AddUpdateError(GetMessage("SAVE_ERROR") . $ID . ": " . GetMessage("PW_TD_SAVE_ERROR"), $ID);
            $DB->Rollback();
        }
        else
            $bupdate = true;

        $DB->Commit();
    }

    if ($bupdate)
        $CACHE_MANAGER->CleanDir("b_tx_supplier_status");
}


if (($arID = $lAdmin->GroupAction()) && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    if ($_REQUEST['action_target'] == 'selected') {
        $arID = Array();
        $rsData = CTenderixUserSupplierStatus::GetList($by, $order, $arFilter);
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
                if (!CTenderixUserSupplierStatus::Delete($ID)) {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                }
                $DB->Commit();
                break;
            case "activate":
            case "deactivate":
                $arFields = Array("ACTIVE" => ($_REQUEST['action'] == "activate" ? "'Y'" : "'N'"));
                if (!$DB->Update("b_tx_supplier_status", $arFields, "WHERE ID='$ID'", $err_mess . __LINE__))
                    $lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
                else
                    $CACHE_MANAGER->CleanDir("b_tx_supplier_status");
                break;
        }
    }
}
$rsData = CTenderixUserSupplierStatus::GetList($by, $order, $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "sort" => "s_id", "default" => true),
    array("id" => "TITLE", "content" => GetMessage("PW_TD_TITLE"), "sort" => "TITLE", "default" => true),
    array("id" => "ACTIVE", "content" => GetMessage("PW_TD_ACTIVE"), "sort" => "ACTIVE", "default" => true),
    array("id" => "C_SORT", "content" => GetMessage("PW_TD_C_SORT"), "sort" => "SORT", "default" => true),
    array("id" => "AUTH", "content" => GetMessage("PW_TD_AUTH"), "sort" => "", "default" => true),
    array("id" => "PART", "content" => GetMessage("PW_TD_PART"), "sort" => "", "default" => true),
        )
);

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row = & $lAdmin->AddRow($f_ID, $arRes);

    if ($TENDERIXRIGHT == "W") {
        $row->AddCheckField("ACTIVE");
        $row->AddInputField("C_SORT", Array("size" => "3"));
        $row->AddInputField("TITLE", Array("size" => "35"));
        $row->AddViewField("TITLE", '<a href="tenderix_users_supplier_status_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_ID . '" title="' . GetMessage("PW_TD_EDIT_TITLE") . '">' . $f_TITLE . '</a>');
    } else {
        $row->AddViewField("ACTIVE", ($f_ACTIVE == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    }
 
    $row->AddViewField("AUTH", ($f_AUTH == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    $row->AddViewField("PART", ($f_PART == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));

    $arActions = Array();
    $arActions[] = array("DEFAULT" => "Y", "ICON" => "edit", "TEXT" => GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION" => $lAdmin->ActionRedirect("tenderix_users_supplier_status_edit.php?ID=" . $f_ID));
    if ($f_ID != '1' && $TENDERIXRIGHT == "W") {
        $arActions[] = array("SEPARATOR" => true);
        $arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION" => "if(confirm('" . GetMessage("PW_TD_CONFIRM_DEL_ENT") . "')) window.location='tenderix_users_supplier_status.php?lang=" . LANGUAGE_ID . "&action=delete&ID=$f_ID&" . bitrix_sessid_get() . "'");
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

if ($TENDERIXRIGHT == "W") {
    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_CREATE"),
        "TITLE" => GetMessage("PW_TD_ADD_TITLE"),
        "LINK" => "tenderix_users_supplier_status_edit.php?lang=" . LANG,
        "ICON" => "btn_new"
    );

    $aContext = $aMenu;
    $lAdmin->AddAdminContextMenu($aContext);
}


$lAdmin->CheckListMode();

/* * ******************************************************************
  Form
 * ****************************************************************** */
$APPLICATION->SetTitle(GetMessage("PW_TD_PAGE_TITLE"));
require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<a name="tb"></a>

<form name="find_form" method="GET" action="<?= $APPLICATION->GetCurPage() ?>?">

<?
$oFilter = new CAdminFilter(
                $sTableID . "_filter",
                array(
                    GetMessage("PW_TD_FLT_ID"),
                    GetMessage("PW_TD_FLT_ACTIVE")
                )
);

$oFilter->Begin();
?>

    <tr>
        <td nowrap><?= GetMessage("PW_TD_F_TITLE") ?></td>
        <td nowrap><input type="text" name="find_title" value="<? echo htmlspecialchars($find_title) ?>" size="47">&nbsp;<?= ShowFilterLogicHelp() ?></td>
    </tr>

    <?
    $oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
    $oFilter->End();
    ?>

</form>
    <?
    $lAdmin->DisplayList();

    require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
    ?>
