<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/jquery.js"></script>', true);

$module_id = "pweb.tenderix";
$TENDERIXRIGHT = $APPLICATION->GetGroupRight($module_id);
if ($TENDERIXRIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/include.php");
ClearVars();
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/prolog.php");

$ID = intval($ID);
$message = false;


$bInitVars = false;
if ((strlen($save) > 0 || strlen($apply) > 0) && $REQUEST_METHOD == "POST" && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {

    global $DB, $USER;

    $userGroup = CUser::GetUserGroup($ID);
    $userGroup[] = COption::GetOptionString("pweb.tenderix", "PW_TD_BUYER_GROUPS_DEFAULT");

    if (isset($USER_ID) && $USER_ID > 0) {
        $rsUser = CUser::GetByID($USER_ID);
        $arUser = $rsUser->Fetch();
        $NAME = $arUser["NAME"];
        $LAST_NAME = $arUser["LAST_NAME"];
        $SECOND_NAME = $arUser["SECOND_NAME"];
        $EMAIL = $arUser["EMAIL"];
        $LOGIN = $arUser["LOGIN"];
    }

    $arFields = array(
        "COMPANY_ID" => $COMPANY_ID,
        "SUBSCR_OWN_LOT" => ($SUBSCR_OWN_LOT == "Y" ? "Y" : "N"),
        "TIMESTAMP_X" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time()),
        "ACTIVE" => ($ACTIVE == "Y" ? "Y" : "N"),
        "NAME" => $NAME,
        "LAST_NAME" => $LAST_NAME,
        "SECOND_NAME" => $SECOND_NAME,
        "EMAIL" => $EMAIL,
        "LOGIN" => $LOGIN,
        "GROUP_ID" => $userGroup,
        "USER_BIND" => serialize($USER_BIND)
    );

    if (strlen($NEW_PASSWORD) > 0) {
        $arFields["PASSWORD"] = $NEW_PASSWORD;
        $arFields["CONFIRM_PASSWORD"] = $NEW_PASSWORD_CONFIRM;
    }

    $res = 0;
    if ($ID > 0) {
        $res = CTenderixUserBuyer::Update($ID, $arFields);
    } elseif (isset($USER_ID) && $USER_ID > 0) {
        $res = CTenderixUserBuyer::Add2($USER_ID, $arFields);
    } else {
        $res = CTenderixUserBuyer::Add($arFields);
    }
    if (intVal($res) <= 0 && $e = $GLOBALS["APPLICATION"]->GetException()) {
        $message = new CAdminMessage(($ID > 0 ? GetMessage("PW_TD_ERROR_UPDATE") : GetMessage("PW_TD_ERROR_ADD")), $e);
        $bInitVars = True;
    } elseif (strlen($save) > 0)
        LocalRedirect("tenderix_users_buyer.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
    else
        $ID = $res;
}

if ($ID > 0) {
    $db_users_buyer = CTenderixUserBuyer::GetList($by, $order, array("ID" => $ID), $is_filtered);
    $db_users_buyer->ExtractFields("str_", False);
}

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_tx_buyer", "", "str_");
    $DB->InitTableVarsForEdit("b_user", "", "str_");
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
        "LINK" => "/bitrix/admin/tenderix_users_buyer.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_list",
    )
);

if ($ID > 0 && $TENDERIXRIGHT == "W") {
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_NEW_BUYER"),
        "LINK" => "/bitrix/admin/tenderix_users_buyer_edit.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_new",
    );

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_DELETE_BUYER"),
        "LINK" => "javascript:if(confirm('" . GetMessage("PW_TD_DELETE_BUYER_CONFIRM") . "')) window.location='/bitrix/admin/tenderix_users_buyer.php?action=delete&ID[]=" . $ID . "&lang=" . LANG . "&" . bitrix_sessid_get() . "#tb';",
        "ICON" => "btn_delete",
    );
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
    echo $message->Show();
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" name="users_buyer_edit">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_TAB_BUYER"), "ICON" => "tenderix_menu_icon_users", "TITLE" => GetMessage("PW_TD_TAB_BUYER_DESCR")),
        array("DIV" => "edit2", "TAB" => GetMessage("PW_TD_TAB_BUYER_SETTINGS"), "ICON" => "tenderix_menu_icon_users", "TITLE" => GetMessage("PW_TD_TAB_BUYER_SETTINGS_DESCR")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    $tabControl->Begin();
    ?>

    <?
    $tabControl->BeginNextTab();

    if ($ID <= 0) {
        $str_ACTIVE = "Y";
    }
    ?>

    <? if ($ID > 0): ?>
        <tr>
            <td width="40%">ID:</td>
            <td width="60%"><? echo $ID ?></td>
        </tr>
    <? endif; ?>

    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_ACTIVE") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "ACTIVE", "Y", $str_ACTIVE, false) ?>
        </td>
    </tr>
    <? if (IntVal($str_USER_ID) <= 0): ?>
        <? $USER_LINK = $_REQUEST["USER"] == "Y" ? "Y" : "N" ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_USER_LINK") ?>:
            </td>
            <td width="60%">
                <select onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;" <?php echo (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "") ?>>
                    <option value="<?= $APPLICATION->GetCurPageParam("USER=N", array("USER", "tabControl_active_tab")) ?>"><?= GetMessage("PW_TD_USER_N") ?></option>  
                    <option<?
            if ($USER_LINK == "Y")
                echo " selected";
                ?> value="<?= $APPLICATION->GetCurPageParam("USER=Y", array("USER", "tabControl_active_tab")) ?>"><?= GetMessage("PW_TD_USER_Y") ?></option>  
                </select>
                <input type="hidden" name="USER" value="<?= $USER_LINK ?>" />
            </td>
        </tr>
    <? endif; ?>
    <? if ($USER_LINK == "Y"): ?>  
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_FIND_USER") ?>:
            </td>
            <td width="60%">
                <?= FindUserID("USER_ID", IntVal($str_USER_ID), "", "users_buyer_edit"); ?>
            </td>
        </tr>
    <? else: ?>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_BUYER_LAST_NAME") ?>:
            </td>
            <td width="60%">
                <input type="text" name="LAST_NAME" value="<?= htmlspecialcharsEx($str_LAST_NAME) ?>" size="30" />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_BUYER_NAME") ?>:
            </td>
            <td width="60%">
                <input type="text" name="NAME" value="<?= htmlspecialcharsEx($str_NAME) ?>" size="30" />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <?= GetMessage("PW_TD_BUYER_SECOND_NAME") ?>:
            </td>
            <td width="60%">
                <input type="text" name="SECOND_NAME" value="<?= htmlspecialcharsEx($str_SECOND_NAME) ?>" size="30" />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <span class="required">*</span> <?= GetMessage("PW_TD_BUYER_LOGIN") ?>:
            </td>
            <td width="60%">
                <input type="text" name="LOGIN" value="<?= htmlspecialcharsEx($str_LOGIN) ?>" size="30" />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <span class="required">*</span> <?= GetMessage("PW_TD_BUYER_EMAIL") ?>:
            </td>
            <td width="60%">
                <input type="text" name="EMAIL" value="<?= htmlspecialcharsEx($str_EMAIL) ?>" size="30" />
            </td>
        </tr>

        <tr id="bx_pass_row">
            <td><? if ($ID <= 0 || $COPY_ID > 0): ?><span class="required">*</span><? endif ?><? echo GetMessage('PW_TD_BUYER_PASSWORD') ?>:</td>
            <td><input type="password" name="NEW_PASSWORD" size="30" maxlength="50" value="" autocomplete="off"></td>
        </tr>
        <tr id="bx_pass_confirm_row">
            <td><? if ($ID <= 0 || $COPY_ID > 0): ?><span class="required">*</span><? endif ?><? echo GetMessage('PW_TD_BUYER_PASSWORD_CONFIRM') ?></td>
            <td><input type="password" name="NEW_PASSWORD_CONFIRM" size="30" maxlength="50" value="" autocomplete="off"></td>
        </tr>
    <? endif; ?>
    <tr>
        <td width="40%">
            <span class="required">*</span> <?= GetMessage("PW_TD_COMPANY") ?>:
        </td>
        <td width="60%">
            <select name="COMPANY_ID"> 
                <option value="">--</option>
                <?
                $bsections = CTenderixCompany::GetList($by = "s_title", $order = "desc", $arFilter, $is_filtered);
                while ($bsections->ExtractFields("s_")):
                    ?><option value="<? echo $s_ID ?>"<?
                if ($s_ID == $str_COMPANY_ID)
                    echo " selected"
                        ?>><? echo $s_TITLE ?></option><?
                    endwhile;
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><?= GetMessage("PW_TD_USER_BIND") ?>:</td>
        <td width="60%">
            <select name="USER_BIND[]" size="10" multiple>
                <?
                $userBind = unserialize($str_USER_BIND);
                $rsUserBuyer = CTenderixUserBuyer::GetList($by = "", $order = "", $arFilter = Array(),  $is_filtered);
                while ($arUserBuyer = $rsUserBuyer->Fetch()):
                    if($arUserBuyer["ID"] == $str_USER_ID) continue;
                    ?>
                    <option<?= in_array($arUserBuyer["ID"], $userBind) ? " selected" : ""; ?> value="<?= $arUserBuyer["ID"] ?>">[<?= $arUserBuyer["LOGIN"] ?>] <?= $arUserBuyer["FIO"] ?></option>
                <? endwhile; ?>
            </select>
        </td>
    </tr>
    <?
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUBSCR_OWN_LOT") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "SUBSCR_OWN_LOT", "Y", $str_SUBSCR_OWN_LOT, false) ?>
        </td>
    </tr>
    <?
    $tabControl->EndTab();
    ?>

    <?
    $tabControl->Buttons(
            array(
                "disabled" => ($TENDERIXRIGHT < "W"),
                "back_url" => "/bitrix/admin/tenderix_users_buyer.php?lang=" . LANG . "&" . GetFilterParams("filter_", false)
            )
    );
    $tabControl->End();
    ?>
</form>
<?
$tabControl->ShowWarnings("users_buyer_edit", $message);
?>
<? if (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1): ?>
    <? echo BeginNote(); ?>
    <?
    $GROUP_POLICY = CUser::GetGroupPolicy($ID);
    echo $GROUP_POLICY["PASSWORD_REQUIREMENTS"];
    ?><br /><br />
    <span class="required">*</span> <? echo GetMessage("REQUIRED_FIELDS") ?>
    <? echo EndNote(); ?>
<? endif; ?>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>