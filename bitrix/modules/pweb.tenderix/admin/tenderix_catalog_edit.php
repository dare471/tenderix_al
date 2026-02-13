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
$CATALOG_ID = IntVal($CATALOG_ID);
$message = false;
$sUrl = "&CATALOG_ID=" . intval($CATALOG_ID);

$bInitVars = false;
if ((strlen($save) > 0 || strlen($apply) > 0) && $REQUEST_METHOD == "POST" && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    InitBVar($str_ACTIVE);
    $C_SORT = intval($C_SORT);
    global $DB;
    $arFields = array(
        "ACTIVE" => ($ACTIVE == "Y" ? "Y" : "N"),
        "C_SORT" => ($C_SORT <= 0 ? 100 : $C_SORT),
        "TITLE" => $TITLE,
        "CATALOG_ID" => $CATALOG_ID,
    );

    $res = 0;
    if ($ID > 0)
        $res = CTenderixSection::CatalogUpdate($ID, $arFields);
    else
        $res = CTenderixSection::CatalogAdd($arFields);
    if (intVal($res) <= 0 && $e = $GLOBALS["APPLICATION"]->GetException()) {
        $message = new CAdminMessage(($ID > 0 ? GetMessage("PW_TD_ERROR_UPDATE") : GetMessage("PW_TD_ERROR_ADD")), $e);
        $bInitVars = True;
    } elseif (strlen($save) > 0)
        LocalRedirect("tenderix_section.php?lang=" . LANG . GetFilterParams(array("CATALOG_ID"), false));
    else
        $ID = $res;
}

if ($ID > 0) {
    $db_section = CTenderixSection::GetCatalogList($by, $order, array("ID" => $ID));
    $db_section->ExtractFields("str_", False);
}
$catID = $ID > 0 ? $str_CATALOG_ID : $CATALOG_ID;

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_tx_section", "", "str_");
}

$rsCatalog = CTenderixSection::GetCatalogList($by = "id", $order = "asc", array());
while ($arCatalog = $rsCatalog->GetNext()) {
    $arCat[$arCatalog["CATALOG_ID"]][] = $arCatalog;
}
$arrCatalog = CTenderixSection::BuildTree($arCat, 0, 0, $str_ID);

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
        "LINK" => "/bitrix/admin/tenderix_section.php?lang=" . LANG . "&" . GetFilterParams(array("CATALOG_ID"), false),
        "ICON" => "btn_list",
    )
);

if ($ID > 0 && $TENDERIXRIGHT == "W") {
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_NEW_SECTION"),
        "LINK" => "/bitrix/admin/tenderix_catalog_edit.php?lang=" . LANG . GetFilterParams(array("CATALOG_ID"), false),
        "ICON" => "btn_new",
    );

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_DELETE_SECTION"),
        "LINK" => "javascript:if(confirm('" . GetMessage("PW_TD_DELETE_SECTION_CONFIRM") . "')) window.location='/bitrix/admin/tenderix_section.php?action=delete&ID[]=C" . $ID . "&lang=" . LANG. $sUrl . "&" . bitrix_sessid_get() . "#tb';",
        "ICON" => "btn_delete",
    );
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
    echo $message->Show();
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" name="catalog_edit">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_TAB_SECTION"), "ICON" => "tenderix_menu_icon_sections", "TITLE" => GetMessage("PW_TD_TAB_SECTION_DESCR")),
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
            <span class="required">*</span><?= GetMessage("PW_TD_CATALOG") ?>:
        </td>
        <td width="60%">
            <select name="CATALOG_ID">
                <option value="0"><?= GetMessage("PW_TD_CATALOG_0") ?></option>
                <? foreach ($arrCatalog as $cat_id => $cat_name): ?>
                    <option value="<?= $cat_id ?>"<?= $catID == $cat_id ? " selected" : "" ?>><?= $cat_name ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>


    <?
    $tabControl->EndTab();
    ?>

    <?
    $tabControl->Buttons(
            array(
                "disabled" => ($TENDERIXRIGHT < "W"),
                "back_url" => "/bitrix/admin/tenderix_section.php?lang=" . LANG . "&" . GetFilterParams("filter_", false)
            )
    );
    $tabControl->End();
    ?>
</form>
<?
$tabControl->ShowWarnings("catalog_edit", $message);
?>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>