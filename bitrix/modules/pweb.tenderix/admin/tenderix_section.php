<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_tenderix_section";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/prolog.php");
$TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
if ($TENDERIXRIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/include.php");

IncludeModuleLangFile(__FILE__);
$err_mess = "File: " . __FILE__ . "<br>Line: ";

$arFilterFields = Array(
    "find_id",
    "find_active",
    "find_title"
);
/* * ******************************************************************
  Actions
 * ****************************************************************** */
$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
    "ID" => $find_id,
    "ACTIVE" => $find_active,
    "TITLE" => $find_title,
    "CATALOG_ID" => intval($_REQUEST["CATALOG_ID"])
);

$sUrl = "&CATALOG_ID=" . intval($CATALOG_ID);

if ($lAdmin->EditAction() && $TENDERIXRIGHT >= "W" && check_bitrix_sessid()) {
    $bupdateS = false;
    $bupdateC = false;
    foreach ($FIELDS as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID))
            continue;
        $DB->StartTransaction();
        $TYPE = substr($ID, 0, 1);
        $ID = IntVal(substr($ID, 1));

        $arFieldsStore = Array(
            "ACTIVE" => "'" . $DB->ForSql($arFields["ACTIVE"]) . "'",
            "C_SORT" => "'" . intval($arFields["C_SORT"]) . "'",
            "TITLE" => "'" . $DB->ForSql($arFields["TITLE"]) . "'"
        );
        if ($TYPE == "S") {
            if (!$DB->Update("b_tx_section", $arFieldsStore, "WHERE ID='$ID'", $err_mess . __LINE__)) {
                $lAdmin->AddUpdateError(GetMessage("SAVE_ERROR") . $ID . ": " . GetMessage("PW_TD_SAVE_ERROR"), $ID);
                $DB->Rollback();
            }
            else
                $bupdateS = true;
        } elseif ($TYPE == "C") {
            if (!$DB->Update("b_tx_catalog", $arFieldsStore, "WHERE ID='$ID'", $err_mess . __LINE__)) {
                $lAdmin->AddUpdateError(GetMessage("SAVE_ERROR") . $ID . ": " . GetMessage("PW_TD_SAVE_ERROR"), $ID);
                $DB->Rollback();
            }
            else
                $bupdateC = true;
        }
        $DB->Commit();
    }

    if ($bupdateS)
        $CACHE_MANAGER->CleanDir("b_tx_section");
    if ($bupdateC)
        $CACHE_MANAGER->CleanDir("b_tx_catalog");
}


if (($arID = $lAdmin->GroupAction()) && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    if ($_REQUEST['action_target'] == 'selected') {
        $arID = Array();
        $rsData = CTenderixSection::GetMixedList($by, $order, $arFilter, $is_filtered);
        while ($arRes = $rsData->Fetch())
            $arID[] = $arRes['TYPE'] . $arRes['ID'];
    }

    foreach ($arID as $ID) {
        if (strlen($ID) <= 0)
            continue;
        $TYPE = substr($ID, 0, 1);
        $ID = IntVal(substr($ID, 1));
        switch ($_REQUEST['action']) {
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if ($TYPE == "S") {
                    if (!CTenderixSection::Delete($ID)) {
                        $DB->Rollback();
                        $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                    }
                } elseif ($TYPE == "C") {
                    if (!CTenderixSection::CatalogDelete($ID)) {
                        $DB->Rollback();
                        $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                    }
                }
                $DB->Commit();
                break;
            case "activate":
            case "deactivate":
                $arFields = Array("ACTIVE" => ($_REQUEST['action'] == "activate" ? "'Y'" : "'N'"));
				if ($TYPE == "S") {
					if (!$DB->Update("b_tx_section", $arFields, "WHERE ID='$ID'", $err_mess . __LINE__))
						$lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
					else
						$CACHE_MANAGER->CleanDir("b_tx_section");
                break;
				} elseif ($TYPE == "C") {
					if (!$DB->Update("b_tx_catalog", $arFields, "WHERE ID='$ID'", $err_mess . __LINE__))
						$lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
					else
						$CACHE_MANAGER->CleanDir("b_tx_catalog");
				}
        }
    }
}
$rsData = CTenderixSection::GetMixedList($by, $order, $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "sort" => "s_id", "default" => true),
    array("id" => "TITLE", "content" => GetMessage("PW_TD_TITLE"), "sort" => "s_title", "default" => true),
    array("id" => "ACTIVE", "content" => GetMessage("PW_TD_ACTIVE"), "sort" => "s_active", "default" => true),
    array("id" => "C_SORT", "content" => GetMessage("PW_TD_C_SORT"), "sort" => "s_c_sort", "default" => true)
        )
);

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row = & $lAdmin->AddRow($f_TYPE . $f_ID, $arRes);

    if ($TENDERIXRIGHT == "W") {
        $row->AddCheckField("ACTIVE");
        $row->AddInputField("C_SORT", Array("size" => "3"));
        $row->AddInputField("TITLE", Array("size" => "35"));
        if ($f_TYPE == "S")
            $row->AddViewField("TITLE", '<a href="tenderix_section_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_ID . '" title="' . GetMessage("PW_TD_EDIT_TITLE") . '">' . $f_TITLE . '</a>');
        else
            $row->AddViewField("TITLE", '<a class="adm-list-table-icon-link" href="tenderix_section.php?lang=' . LANGUAGE_ID . '&CATALOG_ID=' . $f_ID . '" title="' . GetMessage("PW_TD_EDIT_TITLE") . '"><span class="adm-submenu-item-link-icon adm-list-table-icon iblock-section-icon"></span><span class="adm-list-table-link">' . $f_TITLE . '</span></a>');
    }
    else {
        $row->AddViewField("ACTIVE", ($f_ACTIVE == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    }


    $arActions = Array();
    if ($f_TYPE == "S")
        $arActions[] = array("DEFAULT" => "Y", "ICON" => "edit", "TEXT" => GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION" => $lAdmin->ActionRedirect("tenderix_section_edit.php?ID=" . $f_ID));
    else
        $arActions[] = array("DEFAULT" => "Y", "ICON" => "edit", "TEXT" => GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION" => $lAdmin->ActionRedirect("tenderix_catalog_edit.php?ID=" . $f_ID));
    if ($TENDERIXRIGHT == "W") {
        $arActions[] = array("SEPARATOR" => true);
        if ($f_TYPE == "S")
            $arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION" => "if(confirm('" . GetMessage("PW_TD_CONFIRM_DEL_SECTION") . "'))" . $lAdmin->ActionDoGroup($f_TYPE . $f_ID, "delete", $sUrl));
        else
            $arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION" => "if(confirm('" . GetMessage("PW_TD_CONFIRM_DEL_CATALOG") . "'))" . $lAdmin->ActionDoGroup($f_TYPE . $f_ID, "delete", $sUrl));
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


if (intval($_REQUEST["CATALOG_ID"]) > 0) {
    $arCatalogUp = CTenderixSection::GetCatalogList($by = "id", $order = "asc", array("ID" => intval($_REQUEST["CATALOG_ID"])))->GetNext();

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_UP_CATALOG"),
        "TITLE" => GetMessage("PW_TD_UP_CATALOG_TITLE"),
        "LINK" => "tenderix_section.php?lang=" . LANG . "&CATALOG_ID=" . $arCatalogUp["CATALOG_ID"],
        "ICON" => "btn_up"
    );
}

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
        "LINK" => "tenderix_section_edit.php?lang=" . LANG . $sUrl,
        "ICON" => "btn_new"
    );
    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_CREATE_CATALOG"),
        "TITLE" => GetMessage("PW_TD_ADD_CATALOG_TITLE"),
        "LINK" => "tenderix_catalog_edit.php?lang=" . LANG . $sUrl,
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

    <tr>
        <td>ID:</td>
        <td><input type="text" name="find_id" size="47" value="<? echo htmlspecialchars($find_id) ?>"></td>
    </tr>
    <tr>
        <td nowrap><?= GetMessage("PW_TD_F_ACTIVE") ?></td>
        <td nowrap><?
    $arr = array("reference" => array(GetMessage("PW_TD_YES"), GetMessage("PW_TD_NO")), "reference_id" => array("Y", "N"));
    echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_active), GetMessage("PW_TD_ALL"));
    ?></td>
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
