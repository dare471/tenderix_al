<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
if ($TENDERIXRIGHT < "W")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/include.php");
ClearVars();
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/prolog.php");

$ID = IntVal($ID);
$message = false;

$bInitVars = false;
if ((strlen($save) > 0 || strlen($apply) > 0) && $REQUEST_METHOD == "POST" && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    InitBVar($str_ACTIVE);
    $C_SORT = intval($C_SORT);
    global $DB;
    $arFields = array(
        "TIMESTAMP_X" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time()),
        "ACTIVE" => ($ACTIVE == "Y" ? "Y" : "N"),
        "C_SORT" => ($C_SORT <= 0 ? 100 : $C_SORT),
        "TITLE" => $TITLE,
        "URL" => $URL,
        "DESCRIPTION" => $DESCRIPTION,
        "CODE_INN" => $CODE_INN,
        "CODE_KPP" => $CODE_KPP,
        "CODE_OGRN" => $CODE_OGRN,
        "CODE_OKVED" => $CODE_OKVED,
        "CODE_OKPO" => $CODE_OKPO,
        "LEGALADDRESS_COUNTRY" => $LEGALADDRESS_COUNTRY,
        "LEGALADDRESS_REGION" => $LEGALADDRESS_REGION,
        "LEGALADDRESS_CITY" => $LEGALADDRESS_CITY,
        "LEGALADDRESS_BLOCK" => $LEGALADDRESS_BLOCK,
        "LEGALADDRESS_INDEX" => $LEGALADDRESS_INDEX,
        "LEGALADDRESS_STREET" => $LEGALADDRESS_STREET,
        "LEGALADDRESS_POST" => $LEGALADDRESS_POST,
        "POSTALADDRESS_COUNTRY" => $POSTALADDRESS_COUNTRY,
        "POSTALADDRESS_REGION" => $POSTALADDRESS_REGION,
        "POSTALADDRESS_CITY" => $POSTALADDRESS_CITY,
        "POSTALADDRESS_BLOCK" => $POSTALADDRESS_BLOCK,
        "POSTALADDRESS_INDEX" => $POSTALADDRESS_INDEX,
        "POSTALADDRESS_STREET" => $POSTALADDRESS_STREET,
        "POSTALADDRESS_POST" => $POSTALADDRESS_POST,
        "POSTALADDRESS_PHONE" => $POSTALADDRESS_PHONE,
    );

    $res = 0;
    if ($ID > 0)
        $res = CTenderixCompany::Update($ID, $arFields);
    else
        $res = CTenderixCompany::Add($arFields);
    if (intVal($res) <= 0 && $e = $GLOBALS["APPLICATION"]->GetException()) {
        $message = new CAdminMessage(($ID > 0 ? GetMessage("PW_TD_ERROR_UPDATE") : GetMessage("PW_TD_ERROR_ADD")), $e);
        $bInitVars = True;
    } elseif (strlen($save) > 0)
        LocalRedirect("tenderix_company.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
    else
        $ID = $res;
}

if ($ID > 0) {
    $db_enterprise = CTenderixCompany::GetList($by, $order, array("ID" => $ID), $is_filtered);
    $db_enterprise->ExtractFields("str_", False);
}

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_tx_company", "", "str_");
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
        "LINK" => "/bitrix/admin/tenderix_company.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_list",
    )
);

if ($ID > 0 && $TENDERIXRIGHT == "W") {
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_NEW_ENT"),
        "LINK" => "/bitrix/admin/tenderix_company_edit.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_new",
    );

    if ($ID != '1') {
        $aMenu[] = array(
            "TEXT" => GetMessage("PW_TD_DELETE_ENT"),
            "LINK" => "javascript:if(confirm('" . GetMessage("PW_TD_DELETE_ENT_CONFIRM") . "')) window.location='/bitrix/admin/tenderix_company.php?action=delete&ID[]=" . $ID . "&lang=" . LANG . "&" . bitrix_sessid_get() . "#tb';",
            "ICON" => "btn_delete",
        );
    }
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
    echo $message->Show();
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" name="ent_edit">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_TAB_ENT"), "ICON" => "tenderix_menu_icon_company", "TITLE" => GetMessage("PW_TD_TAB_ENT_DESCR")),
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
            <?= GetMessage("PW_TD_URL") ?>:
        </td>
        <td width="60%">
            <input type="text" name="URL" value="<?= htmlspecialcharsEx($str_URL) ?>" size="50" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_DESCRIPTION") ?>:
        </td>
        <td width="60%">
            <textarea name="DESCRIPTION" cols="30" rows="5"><?= htmlspecialcharsEx($str_DESCRIPTION) ?></textarea>
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_CODE") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_CODE_INN") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_INN" value="<?= htmlspecialcharsEx($str_CODE_INN) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_CODE_KPP") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_KPP" value="<?= htmlspecialcharsEx($str_CODE_KPP) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_CODE_OGRN") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_OGRN" value="<?= htmlspecialcharsEx($str_CODE_OGRN) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_CODE_OKVED") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_OKVED" value="<?= htmlspecialcharsEx($str_CODE_OKVED) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_CODE_OKPO") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_OKPO" value="<?= htmlspecialcharsEx($str_CODE_OKPO) ?>" size="30" />
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_LEGALADDRESS") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LEGALADDRESS_COUNTRY") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_COUNTRY" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_COUNTRY) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LEGALADDRESS_REGION") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_REGION" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_REGION) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LEGALADDRESS_CITY") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_CITY" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_CITY) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LEGALADDRESS_BLOCK") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_BLOCK" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_BLOCK) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LEGALADDRESS_INDEX") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_INDEX" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_INDEX) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LEGALADDRESS_STREET") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_STREET" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_STREET) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_LEGALADDRESS_POST") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_POST" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_POST) ?>" size="30" />
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_POSTALADDRESS") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_POSTALADDRESS_COUNTRY") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_COUNTRY" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_COUNTRY) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_POSTALADDRESS_REGION") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_REGION" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_REGION) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_POSTALADDRESS_CITY") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_CITY" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_CITY) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_POSTALADDRESS_BLOCK") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_BLOCK" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_BLOCK) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_POSTALADDRESS_INDEX") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_INDEX" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_INDEX) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_POSTALADDRESS_STREET") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_STREET" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_STREET) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_POSTALADDRESS_POST") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_POST" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_POST) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_POSTALADDRESS_PHONE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_PHONE" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_PHONE) ?>" size="30" />
        </td>
    </tr>


    <?
    $tabControl->EndTab();
    ?>

    <?
    $tabControl->Buttons(
            array(
                "disabled" => ($TENDERIXRIGHT < "W"),
                "back_url" => "/bitrix/admin/tenderix_company.php?lang=" . LANG . "&" . GetFilterParams("filter_", false)
            )
    );
    $tabControl->End();
    ?>
</form>
<?
$tabControl->ShowWarnings("ent_edit", $message);
?>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>