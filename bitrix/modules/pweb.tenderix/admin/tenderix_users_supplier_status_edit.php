<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

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

    $arLOGO_BIG = $_FILES["LOGO_BIG"];
    $arLOGO_BIG["del"] = ${"LOGO_BIG_del"};
    $arLOGO_BIG["MODULE_ID"] = "pweb.tenderix";
    
    $arLOGO_SMALL = $_FILES["LOGO_SMALL"];
    $arLOGO_SMALL["del"] = ${"LOGO_SMALL_del"};
    $arLOGO_SMALL["MODULE_ID"] = "pweb.tenderix";

    $C_SORT = intval($C_SORT);
    global $DB;
    $arFields = array(
        "ACTIVE" => ($ACTIVE == "Y" ? "Y" : "N"),
        "AUTH" => ($AUTH == "Y" ? "Y" : "N"),
        "PART" => ($PART == "Y" ? "Y" : "N"),
        "C_SORT" => ($C_SORT <= 0 ? 100 : $C_SORT),
        "TITLE" => $TITLE,
        "COLOR" => $COLOR,
        "LOGO_BIG" => $arLOGO_BIG,
        "LOGO_SMALL" => $arLOGO_SMALL,
    );

    $res = 0;
    if ($ID > 0)
        $res = CTenderixUserSupplierStatus::Update($ID, $arFields);
    else
        $res = CTenderixUserSupplierStatus::Add($arFields);
    if (intVal($res) <= 0 && $e = $GLOBALS["APPLICATION"]->GetException()) {
        $message = new CAdminMessage(($ID > 0 ? GetMessage("PW_TD_ERROR_UPDATE") : GetMessage("PW_TD_ERROR_ADD")), $e);
        $bInitVars = True;
    } elseif (strlen($save) > 0)
        LocalRedirect("tenderix_users_supplier_status.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
    else
        $ID = $res;
}

if ($ID > 0) {
    $db_enterprise = CTenderixUserSupplierStatus::GetList($by, $order, array("ID" => $ID));
    $db_enterprise->ExtractFields("str_", False);
}

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_tx_supplier_status", "", "str_");
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
        "LINK" => "/bitrix/admin/tenderix_users_supplier_status.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_list",
    )
);

if ($ID > 0 && $TENDERIXRIGHT == "W") {
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_NEW_ENT"),
        "LINK" => "/bitrix/admin/tenderix_users_supplier_status_edit.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_new",
    );

    if ($ID != '1') {
        $aMenu[] = array(
            "TEXT" => GetMessage("PW_TD_DELETE_ENT"),
            "LINK" => "javascript:if(confirm('" . GetMessage("PW_TD_DELETE_ENT_CONFIRM") . "')) window.location='/bitrix/admin/tenderix_users_supplier_status.php?action=delete&ID[]=" . $ID . "&lang=" . LANG . "&" . bitrix_sessid_get() . "#tb';",
            "ICON" => "btn_delete",
        );
    }
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
    echo $message->Show();
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" name="status_edit" enctype="multipart/form-data">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_TAB_ENT"), "ICON" => "tenderix_menu_icon_users", "TITLE" => GetMessage("PW_TD_TAB_ENT_DESCR")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    $tabControl->Begin();
    ?>

    <?
    $tabControl->BeginNextTab();

    if ($ID <= 0) {
        $str_ACTIVE = "Y";
        $str_C_SORT = "100";
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
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_C_SORT") ?>:
        </td>
        <td width="60%">
            <input type="text" name="C_SORT" value="<?= htmlspecialcharsEx($str_C_SORT) ?>" size="10" />
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
            <?= GetMessage("PW_TD_AUTH") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "AUTH", "Y", $str_AUTH, false) ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_PART") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "PART", "Y", $str_PART, false) ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_COLOR") ?>:
        </td>
        <td width="60%">
            <input type="text" name="COLOR" value="<?= htmlspecialcharsEx($str_COLOR) ?>" size="50" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LOGO_BIG") ?>:
        </td>
        <td width="60%">
            <? echo CFile::InputFile("LOGO_BIG", 20, $str_LOGO_BIG, false, 0, "IMAGE", "", 0); ?> 
            <? echo CFile::ShowImage($str_LOGO_BIG, 200, 200, "border=0", "", true) ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LOGO_SMALL") ?>:
        </td>
        <td width="60%">
            <? echo CFile::InputFile("LOGO_SMALL", 20, $str_LOGO_SMALL, false, 0, "IMAGE", "", 0); ?> 
            <? echo CFile::ShowImage($str_LOGO_SMALL, 200, 200, "border=0", "", true) ?>
        </td>
    </tr>


    <?
    $tabControl->EndTab();
    ?>

    <?
    $tabControl->Buttons(
            array(
                "disabled" => ($TENDERIXRIGHT < "W"),
                "back_url" => "/bitrix/admin/tenderix_users_supplier_status.php?lang=" . LANG . "&" . GetFilterParams("filter_", false)
            )
    );
    $tabControl->End();
    ?>
</form>
<?
$tabControl->ShowWarnings("status_edit", $message);

echo BeginNote();
echo GetMessage("PW_TD_NOTE");
echo EndNote();
?>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>