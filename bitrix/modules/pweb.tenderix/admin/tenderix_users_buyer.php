<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_tenderix_users_buyer";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/prolog.php");
$TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
if($TENDERIXRIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/include.php");

IncludeModuleLangFile(__FILE__);
$err_mess = "File: ".__FILE__."<br>Line: ";

$arFilterFields = Array(
	"find_id",
	"find_active",
	"find_name",
	"find_company",
	"find_login",
	);
/********************************************************************
				Actions
********************************************************************/
$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
	"ID"				=> $find_id,
	"ACTIVE"			=> $find_active,
	"NAME"				=> $find_name,
	"COMPANY"			=> $find_company,
	"LOGIN"			=> $find_login,
	);

if ($lAdmin->EditAction() && $TENDERIXRIGHT>="W" && check_bitrix_sessid())
{
	$bupdate = false;
	foreach($FIELDS as $ID=>$arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;
		$DB->StartTransaction();
		$ID = IntVal($ID);
		$arFieldsStore = Array(
			"ACTIVE"		=> "'".$DB->ForSql($arFields["ACTIVE"])."'"
			);
		if (!$DB->Update("b_user",$arFieldsStore,"WHERE ID='$ID'",$err_mess.__LINE__))
		{
			$lAdmin->AddUpdateError(GetMessage("SAVE_ERROR").$ID.": ".GetMessage("PW_TD_SAVE_ERROR"), $ID);
			$DB->Rollback();
		}
		else
			$bupdate = true;

		$DB->Commit();
	}

	if ($bupdate)
		$CACHE_MANAGER->CleanDir("b_tx_buyer");
}


if(($arID = $lAdmin->GroupAction()) && $TENDERIXRIGHT=="W" && check_bitrix_sessid())
{
        if($_REQUEST['action_target']=='selected')
        {
                $arID = Array();
                $rsData = CTenderixUserBuyer::GetList($by, $order, $arFilter, $is_filtered);
                while($arRes = $rsData->Fetch())
                        $arID[] = $arRes['ID'];
        }

        foreach($arID as $ID)
        {
                if(strlen($ID)<=0)
                        continue;
                $ID = IntVal($ID);
                switch($_REQUEST['action'])
                {
                case "delete":
                        @set_time_limit(0);
                        $DB->StartTransaction();
                        if(!CTenderixUserBuyer::Delete($ID))
                        {
                                $DB->Rollback();
                                $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                        }
                        $DB->Commit();
                        break;
                case "activate":
                case "deactivate":
                        $arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"'Y'":"'N'"));
						if (!$DB->Update("b_user",$arFields,"WHERE ID='$ID'",$err_mess.__LINE__))
                                $lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
						else
							$CACHE_MANAGER->CleanDir("b_tx_buyer");
                        break;
                }
        }
}
$rsData = CTenderixUserBuyer::GetList($by, $order, $arFilter, $is_filtered);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
		array("id"=>"ID", "content"=>"ID", "sort"=>"s_id", "default"=>true),
		array("id"=>"FIO", "content"=>GetMessage("PW_TD_BUYER_NAME"), "sort"=>"s_name", "default"=>true),
		array("id"=>"LOGIN", "content"=>GetMessage("PW_TD_BUYER_LOGIN"), "sort"=>"s_login", "default"=>true),
		array("id"=>"EMAIL", "content"=>GetMessage("PW_TD_BUYER_EMAIL"), "sort"=>"", "default"=>true),
		array("id"=>"COMPANY", "content"=>GetMessage("PW_TD_BUYER_COMPANY"), "sort"=>"s_company", "default"=>true),
		array("id"=>"ACTIVE", "content"=>GetMessage("PW_TD_BUYER_ACTIVE"), "sort"=>"s_active", "default"=>true)
	)
);
 
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_USER_ID, $arRes);

	if ($TENDERIXRIGHT=="W")
	{
		$row->AddCheckField("ACTIVE");
                $row->AddViewField("FIO", '<a href="tenderix_users_buyer_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_USER_ID.'" title="'.GetMessage("PW_TD_EDIT_NAME").'">'.$f_FIO.'</a>');
	}
	else
	{
		$row->AddViewField("ACTIVE", ($f_ACTIVE=="Y"? GetMessage("MAIN_YES"):GetMessage("MAIN_NO")));
	}
        
	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y","ICON"=>"edit", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION"=>$lAdmin->ActionRedirect("tenderix_users_buyer_edit.php?ID=".$f_USER_ID));
	if($TENDERIXRIGHT=="W")
	{
		$arActions[] = array("SEPARATOR"=>true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage("PW_TD_CONFIRM_DEL_BUYER")."')) window.location='tenderix_users_buyer.php?lang=".LANGUAGE_ID."&action=delete&ID=$f_USER_ID&".bitrix_sessid_get()."'");
	}

	if ($TENDERIXRIGHT=="W")
		$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

if ($TENDERIXRIGHT=="W")
	$lAdmin->AddGroupActionTable(Array(
		"delete"=>GetMessage("PW_TD_DELETE"),
		"activate"=>GetMessage("PW_TD_ACTIVATE"),
		"deactivate"=>GetMessage("PW_TD_DEACTIVATE"),
		));

if ($TENDERIXRIGHT=="W")
{
	$aMenu[] = array(
		"TEXT"	=> GetMessage("PW_TD_CREATE"),
		"TITLE"=>GetMessage("PW_TD_ADD_TITLE"),
		"LINK"=>"tenderix_users_buyer_edit.php?lang=".LANG,
		"ICON" => "btn_new"
	);

	$aContext = $aMenu;
	$lAdmin->AddAdminContextMenu($aContext);
}


$lAdmin->CheckListMode();

/********************************************************************
				Form
********************************************************************/
$APPLICATION->SetTitle(GetMessage("PW_TD_PAGE_TITLE"));
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<a name="tb"></a>

<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
    
<?

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("PW_TD_F_ID"),
		GetMessage("PW_TD_F_ACTIVE"),
		GetMessage("PW_TD_F_NAME"),
		GetMessage("PW_TD_F_COMPANY"),
		GetMessage("PW_TD_F_LOGIN"),
	)
);

$oFilter->Begin();

?>

<tr>
	<td nowrap><?=GetMessage("PW_TD_F_NAME")?></td>
	<td nowrap><input type="text" name="find_name" value="<?echo htmlspecialchars($find_name)?>" size="47">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>

<tr>
	<td>ID:</td>
	<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("PW_TD_F_LOGIN")?></td>
	<td><input type="text" name="find_login" size="47" value="<?echo htmlspecialchars($find_login)?>"></td>
</tr>
<tr>
	<td nowrap><?=GetMessage("PW_TD_F_COMPANY")?></td>
	<td nowrap><?
		echo SelectBox("find_company", CTenderixCompany::GetDropDownList(), GetMessage("PW_TD_ALL"), htmlspecialchars($find_company));
                ?></td>
</tr>
<tr>
	<td nowrap><?=GetMessage("PW_TD_F_ACTIVE")?></td>
	<td nowrap><?
		$arr = array("reference"=>array(GetMessage("PW_TD_YES"), GetMessage("PW_TD_NO")), "reference_id"=>array("Y","N"));
		echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_active), GetMessage("PW_TD_ALL"));
		?></td>
</tr>

<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>

</form>
<?
$lAdmin->DisplayList();

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
