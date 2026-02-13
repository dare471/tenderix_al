<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = "pweb.tenderix";
$sTableID = "tbl_tenderix_spr";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/prolog.php");
$TENDERIXRIGHT = $APPLICATION->GetGroupRight($module_id);
if($TENDERIXRIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");

IncludeModuleLangFile(__FILE__);
$err_mess = "File: ".__FILE__."<br>Line: ";

$arrNotDel = array(
    COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_UNIT"),
    COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_DELIVERY"),
    COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_PAYMENT")
);

/********************************************************************
				Actions
********************************************************************/

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
			"TIMESTAMP_X"	=> $DB->GetNowFunction(),
			"ACTIVE"		=> "'".$DB->ForSql($arFields["ACTIVE"])."'",
			"C_SORT"		=> "'".intval($arFields["C_SORT"])."'",
			"TITLE"		=> "'".$DB->ForSql($arFields["TITLE"])."'"
			);
		if (!$DB->Update("b_tx_spr",$arFieldsStore,"WHERE ID='$ID'",$err_mess.__LINE__))
		{
			$lAdmin->AddUpdateError(GetMessage("SAVE_ERROR").$ID.": ".GetMessage("PW_TD_SAVE_ERROR"), $ID);
			$DB->Rollback();
		}
		else
			$bupdate = true;

		$DB->Commit();
	}

	if ($bupdate)
		$CACHE_MANAGER->CleanDir("b_tx_spr");
}


if(($arID = $lAdmin->GroupAction()) && $TENDERIXRIGHT=="W" && check_bitrix_sessid())
{
        if($_REQUEST['action_target']=='selected')
        {
                $arID = Array();
                $rsData = CTenderixSpr::GetList($by, $order, "", $is_filtered);
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
                        if(!in_array($ID,$arrNotDel)) {
                            @set_time_limit(0);
                            $DB->StartTransaction();
                            if(!CTenderixSpr::Delete($ID))
                            {
                                    $DB->Rollback();
                                    $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                            }
                            $DB->Commit();
                        }
                        break;
                case "activate":
                case "deactivate":
                        $arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"'Y'":"'N'"));
						if (!$DB->Update("b_tx_spr",$arFields,"WHERE ID='$ID'",$err_mess.__LINE__))
                                $lAdmin->AddGroupError(GetMessage("PW_TD_SAVE_ERROR"), $ID);
						else
							$CACHE_MANAGER->CleanDir("b_tx_spr");
                        break;
                }
        }
}

$rsData = CTenderixSpr::GetList($by, $order, "", $is_filtered);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
		array("id"=>"ID", "content"=>"ID", "sort"=>"s_id", "default"=>true),
		array("id"=>"TITLE", "content"=>GetMessage("PW_TD_TITLE"), "sort"=>"s_title", "default"=>true),
		array("id"=>"ACTIVE", "content"=>GetMessage("PW_TD_ACTIVE"), "sort"=>"s_active", "default"=>true),
		array("id"=>"C_SORT", "content"=>GetMessage("PW_TD_C_SORT"), "sort"=>"s_c_sort", "default"=>true),
                array("id"=>"TIMESTAMP_X", "content"=>GetMessage("PW_TD_TIMESTAMP"), "sort"=>"s_timestamp", "default"=>true),
                array("id"=>"ELEMENTS", "content"=>GetMessage("PW_TD_ELEMENTS"), "sort"=>"s_elements", "default"=>true),
	)
);

while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes);
        
	if ($TENDERIXRIGHT=="W")
	{
                if(!in_array($f_ID,$arrNotDel)) {
                    $row->AddCheckField("ACTIVE");
                }
		$row->AddInputField("C_SORT", Array("size"=>"3"));
		$row->AddInputField("TITLE", Array("size"=>"35"));
                $row->AddViewField("TITLE", '<a href="tenderix_spr_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_ID.'" title="'.GetMessage("PW_TD_EDIT_TITLE").'">'.$f_TITLE.'</a>');
                $row->AddViewField("ELEMENTS", '<a href="tenderix_spr_details.php?lang='.LANGUAGE_ID.'&find_spr_id='.$f_ID.'&set_filter=Y" title="'.GetMessage("PW_TD_EDIT_SPR_VIEW").'">'.$f_ELEMENTS.'</a>&nbsp;[<a title="'.GetMessage("PW_TD_ADD_ELEMENTS").'" href="tenderix_spr_details_edit.php?find_spr_id='.$f_ID.'&lang='.LANGUAGE_ID.'">+</a>]');
	}
        
        $row->AddViewField("ACTIVE", ($f_ACTIVE=="Y"? GetMessage("MAIN_YES"):GetMessage("MAIN_NO")));

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y","ICON"=>"edit", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION"=>$lAdmin->ActionRedirect("tenderix_spr_edit.php?ID=".$f_ID));
	if(!in_array($f_ID,$arrNotDel) && $TENDERIXRIGHT=="W")
	{
		$arActions[] = array("SEPARATOR"=>true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage("PW_TD_CONFIRM_DEL_CHANNEL")."')) window.location='tenderix_spr.php?lang=".LANGUAGE_ID."&action=delete&ID=$f_ID&".bitrix_sessid_get()."'");
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
		"LINK"=>"tenderix_spr_edit.php?lang=".LANG,
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

<?
$lAdmin->DisplayList();

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>