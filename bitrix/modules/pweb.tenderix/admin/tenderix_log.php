<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = "pweb.tenderix";
$sTableID = "tbl_tenderix_log";
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
    "find_user_id",
    "find_event",
    "find_object"
);
/* * ******************************************************************
  Actions
 * ****************************************************************** */
$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
    "USER_ID" => $find_user_id,
    "EVENT" => $find_event,
    "OBJECT" => $find_object
);

$rsData = CTenderixLog::GetList(array($by => $order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PW_TD_PAGES")));


$lAdmin->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "sort" => "ID", "default" => true),
    array("id" => "TIMESTAMP_X", "content" => GetMessage("PW_TD_TIMESTAMP_X"), "sort" => "TIMESTAMP_X", "default" => true),
    array("id" => "OBJECT", "content" => GetMessage("PW_TD_OBJECT"), "sort" => "OBJECT", "default" => true),
    array("id" => "EVENT", "content" => GetMessage("PW_TD_EVENT"), "sort" => "", "default" => true),
    array("id" => "USER_ID", "content" => GetMessage("PW_TD_USER_ID"), "sort" => "USER_ID", "default" => true),
    array("id" => "DESCRIPTION", "content" => GetMessage("PW_TD_DESCRIPTION"), "sort" => "", "default" => true),
        )
);

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row = & $lAdmin->AddRow($f_ID, $arRes);

    $EVENT = CTenderixLog::Event($f_EVENT);
    $row->AddViewField("EVENT", $EVENT);
    $row->AddViewField("USER_ID", $f_FIO . ' [<a href="user_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_USER_ID . '">' . $f_USER_ID . '</a>]');
    switch ($f_EVENT) {
        case "LOT_ADD":
        case "LOT_UPDATE":
        case "LOT_WIN":
        case "PROPOSAL_ADD":
        case "PROPOSAL_UPDATE":
            $OBJECT = GetMessage("PW_TD_LOT") . ' [<a href="tenderix_lot_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_OBJECT . '">' . $f_OBJECT . '</a>]';
            break;
        case "LOT_DEL":
            $OBJECT = GetMessage("PW_TD_LOT") . ' [' . $f_OBJECT . ']';
            break;
        case "BUYER_ADD":
        case "BUYER_UPDATE":
            $OBJECT = GetMessage("PW_TD_BUYER") . ' [<a href="tenderix_users_buyer_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_OBJECT . '">' . $f_OBJECT . '</a>]';
            break;
        case "BUYER_DEL":
            $OBJECT = GetMessage("PW_TD_BUYER") . ' [' . $f_OBJECT . ']';
            break;
        case "SUPPLIER_ADD":
        case "SUPPLIER_UPDATE":
            $OBJECT = GetMessage("PW_TD_SUPPLIER") . ' [<a href="tenderix_users_supplier_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $f_OBJECT . '">' . $f_OBJECT . '</a>]';
            break;
        case "SUPPLIER_DEL":
            $OBJECT = GetMessage("PW_TD_SUPPLIER") . ' [' . $f_OBJECT . ']';
            break;
        default:
            $OBJECT = $f_OBJECT;
            break;
    }
    $row->AddViewField("OBJECT", $OBJECT);
}

$lAdmin->AddFooter(
        array(
            array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount())
        )
);

$aContext = $aMenu;
$lAdmin->AddAdminContextMenu($aContext);

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
                        GetMessage("PW_TD_FLT_EVENT"),
                        GetMessage("PW_TD_FLT_OBJECT"),
                    )
    );

    $oFilter->Begin();
    ?>

    <tr>
        <td nowrap><?= GetMessage("PW_TD_F_USER_ID") ?></td>
        <td nowrap><input type="text" name="find_user_id" value="<? echo htmlspecialchars($find_user_id) ?>" size="47" /></td>
    </tr>
    <tr>
        <td nowrap><?= GetMessage("PW_TD_F_EVENT") ?></td>
        <td nowrap>
            <?
            $arrEvents = CTenderixLog::Event();
            foreach ($arrEvents as $kEvent => $vEvent) {
                $arr["reference"][] = $vEvent;
                $arr["reference_id"][] = $kEvent;
            }
            echo SelectBoxFromArray("find_event", $arr, htmlspecialchars($find_event), GetMessage("PW_TD_EVENT_ALL"));
            ?>
        </td>
    </tr>
    <tr>
        <td><?= GetMessage("PW_TD_F_OBJECT") ?></td>
        <td><input type="text" name="find_object" size="47" value="<? echo htmlspecialchars($find_object) ?>"></td>
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
