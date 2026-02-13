<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = "pweb.tenderix";
$sTableID = "tbl_tenderix_users_supplier";
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
    "find_id",
    "find_active",
    "find_name",
    "find_company",
    "find_login",
    "find_status",
);
/* * ******************************************************************
  Actions
 * ****************************************************************** */
$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
    "ID" => $find_id,
    "ACTIVE" => $find_active,
    "NAME" => $find_name,
    "NAME_COMPANY" => $find_company,
    "LOGIN" => $find_login,
    "STATUS" => $find_status,
);

if ($lAdmin->EditAction() && $TENDERIXRIGHT >= "W" && check_bitrix_sessid()) {
    $bupdate = false;
    foreach ($FIELDS as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID))
            continue;
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $arFieldsStore = Array(
            "ACTIVE" => "'" . $DB->ForSql($arFields["ACTIVE"]) . "'"
        );
        if (!$DB->Update("b_user", $arFieldsStore, "WHERE ID='$ID'", $err_mess . __LINE__)) {
            $lAdmin->AddUpdateError(GetMessage("SAVE_ERROR") . $ID . ": " . GetMessage("PW_TD_SAVE_ERROR"), $ID);
            $DB->Rollback();
        }
        else
            $bupdate = true;

        $DB->Commit();
    }

    if ($bupdate)
        $CACHE_MANAGER->CleanDir("b_tx_supplier");
}


if (($arID = $lAdmin->GroupAction()) && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    if ($_REQUEST['action_target'] == 'selected') {
        $arID = Array();
        $rsData = CTenderixUserSupplier::GetList($by, $order, $arFilter, $is_filtered);
        while ($arRes = $rsData->Fetch())
            $arID[] = $arRes['USER_ID'];
    }

    foreach ($arID as $ID) {
        if (strlen($ID) <= 0)
            continue;
        $ID = IntVal($ID);
        switch ($_REQUEST['action']) {
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if (!CTenderixUserSupplier::Delete($ID)) {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                }
                $DB->Commit();
                break;
			case "mail":
                // @set_time_limit(0);
					$rsUser = CTenderixUserSupplier::GetByID($ID);
					$arUser = $rsUser->Fetch();
					
					$arrSITE = CTenderixLot::GetSite();
					//CHECKWORD
					$salt = randString(8);
					$checkword = md5(CMain::GetServerUniqID().uniqid());
					$strSql = "UPDATE b_user SET ".
						"	CHECKWORD = '".$salt.md5($salt.$checkword)."', ".
						"	CHECKWORD_TIME = ".$DB->CurrentTimeFunction().", ".
						"	LID = '".$DB->ForSql($arrSITE, 2)."', ".
						"   TIMESTAMP_X = TIMESTAMP_X ".
						"WHERE ID = '".$ID."'".
						"	AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') ";

					$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

					$res = $DB->Query(
						"SELECT u.* ".
						"FROM b_user u ".
						"WHERE ID='".$ID."'".
						"	AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') "
					);
					
					if($res_array = $res->Fetch())
					{
							
						$arEventFields = array(
							"ID" => $ID,
							"LAST_NAME" => $arUser["LAST_NAME"],
							"NAME" => $arUser["NAME"],						
							"LOGIN" => $arUser["LOGIN"],
							"URL_LOGIN" => urlencode($arUser["LOGIN"]),
							"EMAIL" => $arUser["EMAIL"],
							"CHECKWORD" => $checkword,						
						);
						
						
						//
						CTenderixLog::Log("TENDERIX_USER_INVITE", array("ID" => $ID, "FIELDS" => $arEventFields));
						//Send e-mail
						CEvent::Send("USER_INVITE", $arrSITE, $arEventFields, "N");
					}
					//
					
                break;
            case "activate":
            case "deactivate":
                $arFields = Array("ACTIVE" => ($_REQUEST['action'] == "activate" ? "'Y'" : "'N'"));
                if (!$DB->Update("b_user", $arFields, "WHERE ID='$ID'", $err_mess . __LINE__))
                    $lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
                else
                    $CACHE_MANAGER->CleanDir("b_tx_supplier");
                break;
        }
    }
}
$rsData = CTenderixUserSupplier::GetList($by, $order, $arFilter, $is_filtered);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "sort" => "s_id", "default" => true),
    array("id" => "FIO", "content" => GetMessage("PW_TD_SUPPLIER_NAME"), "sort" => "s_name", "default" => true),
    array("id" => "STATUS_NAME", "content" => GetMessage("PW_TD_SUPPLIER_STATUS"), "sort" => "s_status", "default" => true),
    array("id" => "LOGIN", "content" => GetMessage("PW_TD_SUPPLIER_LOGIN"), "sort" => "s_login", "default" => true),
    array("id" => "EMAIL", "content" => GetMessage("PW_TD_SUPPLIER_EMAIL"), "sort" => "", "default" => true),
    array("id" => "NAME_COMPANY", "content" => GetMessage("PW_TD_SUPPLIER_NAME_COMPANY"), "sort" => "s_company", "default" => true),
    array("id" => "ACTIVE", "content" => GetMessage("PW_TD_SUPPLIER_ACTIVE"), "sort" => "s_active", "default" => true),
    array("id" => "DATE_REGISTER", "content" => GetMessage("PW_TD_SUPPLIER_DATE_REGISTER"), "sort" => "s_datereg", "default" => true)
        )
);

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row = &$lAdmin->AddRow($f_USER_ID, $arRes);

    if ($TENDERIXRIGHT == "W") {
        $row->AddCheckField("ACTIVE");
        $row->AddViewField("FIO", '<a href="tenderix_users_supplier_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_USER_ID . '" title="' . GetMessage("PW_TD_EDIT_NAME") . '">' . $f_FIO . '</a>');
    } else {
        $row->AddViewField("ACTIVE", ($f_ACTIVE == "Y" ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
    }

    $arActions = Array();
    $arActions[] = array("DEFAULT" => "Y", "ICON" => "edit", "TEXT" => GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION" => $lAdmin->ActionRedirect("tenderix_users_supplier_edit.php?ID=" . $f_USER_ID));
    // $arActions[] = array("DEFAULT" => "Y", "ICON" => "", "TEXT" => GetMessage("PW_MENU_MAIL"), "ACTION" => "if(confirm('" . GetMessage("PW_TD_CONFIRM_MAIL_SUPPLIER") . "')) window.location='tenderix_users_supplier.php?lang=" . LANGUAGE_ID . "&action=mail&ID=$f_USER_ID&" . bitrix_sessid_get() . "'");
    
	if ($TENDERIXRIGHT == "W") {
        $arActions[] = array("SEPARATOR" => true);
        $arActions[] = array("ICON" => "delete", "TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION" => "if(confirm('" . GetMessage("PW_TD_CONFIRM_DEL_SUPPLIER") . "')) window.location='tenderix_users_supplier.php?lang=" . LANGUAGE_ID . "&action=delete&ID=$f_USER_ID&" . bitrix_sessid_get() . "'");
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
		"mail" => GetMessage("PW_TD_MAIL"),
    ));

if ($TENDERIXRIGHT == "W") {
    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_CREATE"),
        "TITLE" => GetMessage("PW_TD_ADD_TITLE"),
        "LINK" => "tenderix_users_supplier_edit.php?lang=" . LANG,
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
                        GetMessage("PW_TD_F_ID"),
                        GetMessage("PW_TD_F_ACTIVE"),
                        GetMessage("PW_TD_F_NAME"),
                        GetMessage("PW_TD_F_NAME_COMPANY"),
                        GetMessage("PW_TD_F_LOGIN"),
                        GetMessage("PW_TD_F_STATUS"),
                    )
    );

    $oFilter->Begin();
    ?>

    <tr>
        <td nowrap><?= GetMessage("PW_TD_F_NAME") ?></td>
        <td nowrap><input type="text" name="find_name" value="<? echo htmlspecialchars($find_name) ?>" size="47">&nbsp;<?= ShowFilterLogicHelp() ?></td>
    </tr>

    <tr>
        <td>ID:</td>
        <td><input type="text" name="find_id" size="47" value="<? echo htmlspecialchars($find_id) ?>"></td>
    </tr>
    <tr>
        <td><?= GetMessage("PW_TD_F_LOGIN") ?></td>
        <td><input type="text" name="find_login" size="47" value="<? echo htmlspecialchars($find_login) ?>"></td>
    </tr>
    <tr>
        <td nowrap><?= GetMessage("PW_TD_F_NAME_COMPANY") ?></td>
        <td nowrap><input type="text" name="find_company" size="47" value="<? echo htmlspecialchars($find_company) ?>"></td></td>
    </tr>
    <tr>
        <td nowrap><?= GetMessage("PW_TD_F_ACTIVE") ?></td>
        <td nowrap><?
    $arr = array("reference" => array(GetMessage("PW_TD_YES"), GetMessage("PW_TD_NO")), "reference_id" => array("Y", "N"));
    echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_active), GetMessage("PW_TD_ALL"));
    ?></td>
    </tr>
    <tr>
        <td nowrap><?= GetMessage("PW_TD_F_STATUS") ?></td>
        <td nowrap><?
     echo SelectBox("find_status", CTenderixUserSupplierStatus::GetDropDownList(), "--", htmlspecialchars($find_status));
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
