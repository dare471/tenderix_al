<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/jquery.js"></script>', true);

$TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
if ($TENDERIXRIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/include.php");
ClearVars();
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/prolog.php");

$ID = IntVal($ID);
$message = false;

$bInitVars = false;
if ((strlen($save) > 0 || strlen($apply) > 0) && $REQUEST_METHOD == "POST" && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {

    $SORT = intval($SORT);
    global $DB;
    if ($PROPERTY_TYPE == "D") {
        $DEFAULT_VALUE = ConvertDateTime($DEFAULT_VALUE, "YYYY-MM-DD HH:MI:SS");
    }
    if ($PROPERTY_TYPE == "L") {
        foreach ($DEFAULT_VALUE as $listVal) {
            $listVal = trim($listVal);
            if (strlen($listVal) > 0) {
                $listValArr[] = $listVal;
            }
        }
        $arrList = array(
            "DEFAULT_VALUE_SELECT" => $DEFAULT_VALUE_SELECT,
            "DEFAULT_VALUE" => $listValArr
        );
        $DEFAULT_VALUE = base64_encode(serialize($arrList));
    }
    $arFields = array(
        "ACTIVE" => ($ACTIVE == "Y" ? "Y" : "N"),
        "PROPERTY_TYPE" => $PROPERTY_TYPE,
        "IS_REQUIRED" => ($IS_REQUIRED == "Y" ? "Y" : "N"),
        "MULTI" => ($MULTI == "Y" ? "Y" : "N"),
        "T_RIGHT" => ($_POST['T_RIGHT']),
        "S_RIGHT" => ($_POST['S_RIGHT']),
        "START_LOT" => ($_POST['WHEN_QUERY'] == "Y"? "Y" : "N"),
        "END_LOT" => ($_POST['WHEN_QUERY'] == "N"? "Y" : "N"),
        "SORT" => ($SORT <= 0 ? 100 : $SORT),
        "TITLE" => $TITLE,
        "DESCRIPTION" => $DESCRIPTION,
        "CODE" => $CODE,
        "FILE_TYPE" => $FILE_TYPE,
        "MULTI_CNT" => intval($MULTI_CNT) > 0 ? intval($MULTI_CNT) : 1,
        "ROW_COUNT" => $ROW_COUNT,
        "COL_COUNT" => $COL_COUNT,
        "DEFAULT_VALUE" => $DEFAULT_VALUE,
    );

    $res = 0;
    if ($ID > 0)
        $res = CTenderixProposalProperty::Update($ID, $arFields);
    else
        $res = CTenderixProposalProperty::Add($arFields);
    if (intVal($res) <= 0 && $e = $GLOBALS["APPLICATION"]->GetException()) {
        $message = new CAdminMessage(($ID > 0 ? GetMessage("PW_TD_ERROR_UPDATE") : GetMessage("PW_TD_ERROR_ADD")), $e);
        $bInitVars = True;
    } elseif (strlen($save) > 0)
        LocalRedirect("tenderix_proposal_property.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
    else
        $ID = $res;
}

if ($ID > 0) {
    $db_enterprise = CTenderixProposalProperty::GetList($by, $order, array("ID" => $ID));
    $db_enterprise->ExtractFields("str_", False);
}

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_tx_proposal_property", "", "str_");
}

$sDocTitle = ($ID > 0) ? str_replace("#ID#", $ID, GetMessage("PW_TD_TITLE_UPDATE")) : GetMessage("PW_TD_TITLE_ADD");
$APPLICATION->SetTitle($sDocTitle);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

/* * ****************************************************************** */
/* * ******************  BODY  **************************************** */
/* * ****************************************************************** */
?>

<?
$aMenu = array(
    array(
        "TEXT" => GetMessage("PW_TD_2FLIST"),
        "LINK" => "/bitrix/admin/tenderix_proposal_property.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_list",
    )
);

if ($ID > 0 && $TENDERIXRIGHT == "W") {
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_NEW_ENT"),
        "LINK" => "/bitrix/admin/tenderix_proposal_property_edit.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_new",
    );

    if ($ID != '1') {
        $aMenu[] = array(
            "TEXT" => GetMessage("PW_TD_DELETE_ENT"),
            "LINK" => "javascript:if(confirm('" . GetMessage("PW_TD_DELETE_ENT_CONFIRM") . "')) window.location='/bitrix/admin/tenderix_proposal_property.php?action=delete&ID[]=" . $ID . "&lang=" . LANG . "&" . bitrix_sessid_get() . "#tb';",
            "ICON" => "btn_delete",
        );
    }
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
    echo $message->Show();
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" name="property_edit" enctype="multipart/form-data">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_TAB_ENT"), "ICON" => "tenderix_menu_icon_lot", "TITLE" => GetMessage("PW_TD_TAB_ENT_DESCR")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    $tabControl->Begin();
    ?>

    <?
    $tabControl->BeginNextTab();

    if ($ID <= 0) {
        $str_ACTIVE = "Y";
        $str_SORT = "100";
        $str_MULTI_CNT = "1";
        $str_ROW_COUNT = "1";
        $str_COL_COUNT = "30";
        $str_PROPERTY_TYPE = "S";
    } else {
        if ($str_PROPERTY_TYPE != $_REQUEST["PROPERTY_TYPE"] && isset($_REQUEST["PROPERTY_TYPE"])) {
            $str_DEFAULT_VALUE = "";
        }
    }
    if (isset($_REQUEST["PROPERTY_TYPE"]))
        $str_PROPERTY_TYPE = $_REQUEST["PROPERTY_TYPE"];
    ?>

    <? if ($ID > 0): ?>
        <tr>
            <td width="40%">ID:</td>
            <td width="60%"><? echo $ID ?></td>
        </tr>
    <? endif; ?>

    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_PROPERTY_TYPE") ?>:
        </td>
        <td width="60%">
            <select onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;">
                <option value="<?= $APPLICATION->GetCurPageParam("PROPERTY_TYPE=S&ID=" . $ID, array("PROPERTY_TYPE", "ID", "tabControl_active_tab")) ?>" <?= $str_PROPERTY_TYPE == "S" ? "selected" : ""; ?>><?= GetMessage("PW_TD_PROPERTY_S") ?></option>
                <option value="<?= $APPLICATION->GetCurPageParam("PROPERTY_TYPE=N&ID=" . $ID, array("PROPERTY_TYPE", "ID", "tabControl_active_tab")) ?>" <?= $str_PROPERTY_TYPE == "N" ? "selected" : ""; ?>><?= GetMessage("PW_TD_PROPERTY_N") ?></option>
                <option value="<?= $APPLICATION->GetCurPageParam("PROPERTY_TYPE=F&ID=" . $ID, array("PROPERTY_TYPE", "ID", "tabControl_active_tab")) ?>" <?= $str_PROPERTY_TYPE == "F" ? "selected" : ""; ?>><?= GetMessage("PW_TD_PROPERTY_F") ?></option>
                <option value="<?= $APPLICATION->GetCurPageParam("PROPERTY_TYPE=L&ID=" . $ID, array("PROPERTY_TYPE", "ID", "tabControl_active_tab")) ?>" <?= $str_PROPERTY_TYPE == "L" ? "selected" : ""; ?>><?= GetMessage("PW_TD_PROPERTY_L") ?></option>
                <option value="<?= $APPLICATION->GetCurPageParam("PROPERTY_TYPE=T&ID=" . $ID, array("PROPERTY_TYPE", "ID", "tabControl_active_tab")) ?>" <?= $str_PROPERTY_TYPE == "T" ? "selected" : ""; ?>><?= GetMessage("PW_TD_PROPERTY_T") ?></option>
                <option value="<?= $APPLICATION->GetCurPageParam("PROPERTY_TYPE=D&ID=" . $ID, array("PROPERTY_TYPE", "ID", "tabControl_active_tab")) ?>" <?= $str_PROPERTY_TYPE == "D" ? "selected" : ""; ?>><?= GetMessage("PW_TD_PROPERTY_D") ?></option>
            </select> 
            <input type="hidden" name="PROPERTY_TYPE" value="<?= $str_PROPERTY_TYPE ?>" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_ACTIVE") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "ACTIVE", "Y", $str_ACTIVE, false) ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SORT") ?>:
        </td>
        <td width="60%">
            <input type="text" name="SORT" value="<?= htmlspecialcharsEx($str_SORT) ?>" size="10" />
        </td>
    </tr>
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
            <?= GetMessage("PW_TD_DESCRIPTION") ?>:
        </td>
        <td width="60%">
        <textarea name="DESCRIPTION" rows="5" cols="48" ><?= htmlspecialcharsEx($str_DESCRIPTION) ?></textarea>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_CODE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE" value="<?= htmlspecialcharsEx($str_CODE) ?>" size="50" />
        </td>
    </tr>
    <? if ($str_PROPERTY_TYPE == "F"): ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_FILE_TYPE") ?>:
            </td>
            <td width="60%">
                <input type="text" size="30" maxlength="255" name="FILE_TYPE" value="<?= htmlspecialcharsEx($str_FILE_TYPE) ?>" id="CURRENT_PROPERTY_FILE_TYPE">
                <select onchange="if(this.selectedIndex!=0) document.getElementById('CURRENT_PROPERTY_FILE_TYPE').value=this[this.selectedIndex].value">
                    <option value="-"></option>
                    <option value=""><?= GetMessage("PW_TD_FILE_ALL") ?></option>
                    <option value="jpg, gif, bmp, png, jpeg" <?= $str_FILE_TYPE == "jpg, gif, bmp, png, jpeg" ? "selected" : ""; ?>><?= GetMessage("PW_TD_FILE_IMG") ?></option>
                    <option value="mp3, wav, midi, snd, au, wma" <?= $str_FILE_TYPE == "mp3, wav, midi, snd, au, wma" ? "selected" : ""; ?>><?= GetMessage("PW_TD_FILE_SOUND") ?></option>
                    <option value="mpg, avi, wmv, mpeg, mpe" <?= $str_FILE_TYPE == "mpg, avi, wmv, mpeg, mpe" ? "selected" : ""; ?>><?= GetMessage("PW_TD_FILE_VIDEO") ?></option>
                    <option value="doc, txt, rtf" <?= $str_FILE_TYPE == "doc, txt, rtf" ? "selected" : ""; ?>><?= GetMessage("PW_TD_FILE_DOC") ?></option>
                </select>
            </td>
        </tr>
    <? endif; ?>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_IS_REQUIRED") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "IS_REQUIRED", "Y", $str_IS_REQUIRED, false) ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_MULTI") ?>:
        </td>
        <td width="60%">
            <input type="checkbox" name="MULTI" id="multi" value="Y" <?= $str_MULTI == "Y" ? "checked" : ""; ?> onclick="multiCheck();" />
        </td>
    </tr>
    <? if ($str_PROPERTY_TYPE != "L"): ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_MULTI_CNT") ?>:
            </td>
            <td width="60%">
                <input id="multi-cnt" <?= $str_MULTI == "Y" ? '' : 'disabled'; ?> type="text" name="MULTI_CNT" value="<?= htmlspecialcharsEx($str_MULTI_CNT) ?>" size="5" />
            </td>
        </tr>
    <? endif; ?>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_WHEN_QUERY") ?>:
        </td>
        <td width="60%">
            <input type="radio" name="WHEN_QUERY" value="Y" <?= $str_START_LOT == "Y" ? "checked" : ""; ?>><?= GetMessage("PW_TD_START_LOT") ?><br />
            <input type="radio" name="WHEN_QUERY" value="N" <?= $str_END_LOT == "Y" ? "checked" : ""; ?>><?= GetMessage("PW_TD_END_LOT") ?><br />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_T_RIGHT") ?>:
        </td>
        <td width="60%">
            <input type="radio" name="T_RIGHT" value="W" <?= $str_T_RIGHT == "W" ? "checked" : ""; ?>><?= GetMessage("PW_TD_T_RIGHT_W") ?><br />
            <input type="radio" name="T_RIGHT" value="R" <?= $str_T_RIGHT == "R" ? "checked" : ""; ?>><?= GetMessage("PW_TD_T_RIGHT_R") ?><br />
            <input type="radio" name="T_RIGHT" value="D" <?= $str_T_RIGHT == "D" ? "checked" : ""; ?>><?= GetMessage("PW_TD_T_RIGHT_D") ?><br />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_S_RIGHT") ?>:
        </td>
        <td width="60%">
            <input type="radio" name="S_RIGHT" value="W" <?= $str_S_RIGHT == "W" ? "checked" : ""; ?>><?= GetMessage("PW_TD_S_RIGHT_W") ?><br />
            <input type="radio" name="S_RIGHT" value="R" <?= $str_S_RIGHT == "R" ? "checked" : ""; ?>><?= GetMessage("PW_TD_S_RIGHT_R") ?><br />
            <input type="radio" name="S_RIGHT" value="D" <?= $str_S_RIGHT == "D" ? "checked" : ""; ?>><?= GetMessage("PW_TD_S_RIGHT_D") ?><br />
        </td>
    </tr>
    <? if ($str_PROPERTY_TYPE == "F"): ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_COL_COUNT") ?>:
            </td>
            <td width="60%">
                <input type="text" name="COL_COUNT" value="<?= htmlspecialcharsEx($str_COL_COUNT) ?>" size="5" />
            </td>
        </tr>
    <? elseif ($str_PROPERTY_TYPE == "L"): ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_ROW_LIST_COUNT") ?>:
            </td>
            <td width="60%">
                <input type="text" name="ROW_COUNT" value="<?= htmlspecialcharsEx($str_ROW_COUNT) ?>" size="5" />
            </td>
        </tr>
    <? elseif ($str_PROPERTY_TYPE != "L" && $str_PROPERTY_TYPE != "D"): ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_ROW_COL_COUNT") ?>:
            </td>
            <td width="60%">
                <input type="text" name="ROW_COUNT" value="<?= htmlspecialcharsEx($str_ROW_COUNT) ?>" size="5" /> x 
                <input type="text" name="COL_COUNT" value="<?= htmlspecialcharsEx($str_COL_COUNT) ?>" size="5" />
            </td>
        </tr>
    <? endif; ?>
    <? if ($str_PROPERTY_TYPE == "S" || $str_PROPERTY_TYPE == "N"): ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_DEFAULT_VALUE") ?>:
            </td>
            <td width="60%">
                <input type="text" name="DEFAULT_VALUE" value="<?= htmlspecialcharsEx($str_DEFAULT_VALUE) ?>" size="50" />
            </td>
        </tr>
    <? endif; ?>
    <? if ($str_PROPERTY_TYPE == "T"): ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_DEFAULT_VALUE") ?>:
            </td>
            <td width="60%">
                <textarea rows="10" cols="50" name="DEFAULT_VALUE"><?= htmlspecialcharsEx($str_DEFAULT_VALUE) ?></textarea>
            </td>
        </tr>
    <? endif; ?>
    <? if ($str_PROPERTY_TYPE == "D"): ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_DEFAULT_VALUE") ?>:
            </td>
            <td width="60%">
                <input type="text" name="DEFAULT_VALUE" value="<?//= ConvertTimeStamp(strtotime($str_DEFAULT_VALUE), "FULL") ?>" size="20" />
                <?= Calendar("DEFAULT_VALUE", "property_edit") ?>
            </td>
        </tr>
    <? endif; ?>
    <? if ($str_PROPERTY_TYPE == "L"): ?>
        <tr>
            <td width="40%" valign="top">
                <?= GetMessage("PW_TD_DEFAULT_VALUE_LIST") ?>:
            </td>
            <td id="value-list">
                <?
                $idRow = 0;
                $colRowDef = 3;
                $arrList = unserialize(base64_decode($str_DEFAULT_VALUE));
                ?>
                <? foreach ($arrList["DEFAULT_VALUE"] as $listVal): ?>
                    <input type="radio" <?= $idRow == $arrList["DEFAULT_VALUE_SELECT"] ? "checked" : "" ?> name="DEFAULT_VALUE_SELECT" value="<?= $idRow ?>" /> <input type="text" name="DEFAULT_VALUE[]" value="<?= htmlspecialcharsEx($listVal) ?>" size="30" /> <br />
                    <? $idRow++ ?>        
                <? endforeach; ?>
                <? for ($i = $idRow; $i < $colRowDef + $idRow; $i++): ?>
                    <input type="radio" <?= $i == 0 ? "checked" : "" ?> name="DEFAULT_VALUE_SELECT" value="<?= $i; ?>" /> <input type="text" name="DEFAULT_VALUE[]" value="" size="30" /> <br />
                <? endfor; ?>

            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="hidden" name="LIST_ROWS" id="list-rows" value="<?= $i ?>" />
                <input type="button" id="button-list-add" value="<?= GetMessage("PW_TD_LIST_ELEMENT_ADD") ?>" onclick="add_list_row();" />
            </td>
        </tr>
    <? endif; ?>

    <? $tabControl->EndTab(); ?>

    <?
    $tabControl->Buttons(
            array(
                "disabled" => ($TENDERIXRIGHT < "W"),
                "back_url" => "/bitrix/admin/tenderix_proposal_property.php?lang=" . LANG . "&" . GetFilterParams("filter_", false)
            )
    );
    $tabControl->End();
    ?>
</form>
<script type="text/javascript">
    function multiCheck() {
        if($("#multi-cnt").attr("disabled") == "disabled") {
            $("#multi-cnt").removeAttr("disabled");
        } else {
            $("#multi-cnt").attr("disabled","disabled");
        }
    }
    function add_list_row() {
        var idRow = $("#list-rows").val();
        $("#value-list").append('<input type="radio" name="DEFAULT_VALUE_SELECT" value="'+idRow+'" /> <input type="text" name="DEFAULT_VALUE[]" value="" size="30" /> <br />');
        $("#list-rows").val(parseInt(idRow) + 1);
    }
</script>  
<?
$tabControl->ShowWarnings("property_edit", $message);
?>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>