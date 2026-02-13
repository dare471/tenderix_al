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

$ID = IntVal($ID);
$message = false;

$bInitVars = false;
if ((strlen($save) > 0 || strlen($apply) > 0) && $REQUEST_METHOD == "POST" && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    global $DB, $USER;

    $userGroup = CUser::GetUserGroup($ID);
    $userGroup[] = COption::GetOptionString("pweb.tenderix", "PW_TD_SUPPLIER_GROUPS_DEFAULT");

    $arFields = array(
        "NAME_COMPANY" => $NAME_COMPANY,
        "NAME_DIRECTOR" => $NAME_DIRECTOR,
        "NAME_ACCOUNTANT" => $NAME_ACCOUNTANT,
        "CODE_INN" => $CODE_INN,
        "CODE_KPP" => $CODE_KPP,
        "CODE_OKVED" => $CODE_OKVED,
        "CODE_OKPO" => $CODE_OKPO,
        "LEGALADDRESS_REGION" => $LEGALADDRESS_REGION,
        "LEGALADDRESS_CITY" => $LEGALADDRESS_CITY,
        "LEGALADDRESS_INDEX" => $LEGALADDRESS_INDEX,
        "LEGALADDRESS_STREET" => $LEGALADDRESS_STREET,
        "LEGALADDRESS_POST" => $LEGALADDRESS_POST,
        "POSTALADDRESS_REGION" => $POSTALADDRESS_REGION,
        "POSTALADDRESS_CITY" => $POSTALADDRESS_CITY,
        "POSTALADDRESS_INDEX" => $POSTALADDRESS_INDEX,
        "POSTALADDRESS_STREET" => $POSTALADDRESS_STREET,
        "POSTALADDRESS_POST" => $POSTALADDRESS_POST,
        "PHONE" => $PHONE,
        "FAX" => $FAX,
        "STATEREG_PLACE" => $STATEREG_PLACE,
        "STATEREG_DATE" => $STATEREG_DATE,
        "STATEREG_OGRN" => $STATEREG_OGRN,
        "BANKING_NAME" => $BANKING_NAME,
        "BANKING_ACCOUNT" => $BANKING_ACCOUNT,
        "BANKING_ACCOUNTCORR" => $BANKING_ACCOUNTCORR,
        "BANKING_BIK" => $BANKING_BIK,
        "STATUS" => $STATUS,
        "TIMESTAMP_X" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time()),
        "ACTIVE" => ($ACTIVE == "Y" ? "Y" : "N"),
        "NAME" => $NAME,
        "LAST_NAME" => $LAST_NAME,
        "SECOND_NAME" => $SECOND_NAME,
        "EMAIL" => $EMAIL,
        "LOGIN" => $LOGIN,
        "GROUP_ID" => $userGroup,
        "PROPERTY" => array("PROPERTY" => $_POST["PROP"], "FILES" => $_FILES["PROP"])
    );
	
	$arRequired = array(
		"NAME",
		"LAST_NAME",
		"NAME_COMPANY",		
	);
	
    if (strlen($NEW_PASSWORD) > 0) {
        $arFields["PASSWORD"] = $NEW_PASSWORD;
        $arFields["CONFIRM_PASSWORD"] = $NEW_PASSWORD_CONFIRM;
    }

    $res = 0;
    if ($ID > 0)
        $res = CTenderixUserSupplier::Update($ID, $arFields, $arRequired);
    else
        $res = CTenderixUserSupplier::Add($arFields);

    if (intVal($res) > 0) {
        $ID = $res;
        CTenderixUserSupplier::SubscribeAdd($ID, $subscribe);
        CTenderixUserSupplier::DirectionAdd($ID, $direction);

        //Add Files
        //Delete
        if (is_array($FILE_ID))
            foreach ($FILE_ID as $file)
                CTenderixUserSupplier::DeleteFile($ID, $file);
        if (is_array($FILE_ID_PROP))
            foreach ($FILE_ID_PROP as $file)
                CTenderixUserSupplier::DeleteFileProperty($ID, $file);

        //New files
        $arFiles = array();

        //Brandnew
        if (is_array($_FILES["NEW_FILE"]))
            foreach ($_FILES["NEW_FILE"] as $attribute => $files)
                if (is_array($files))
                    foreach ($files as $index => $value)
                        $arFiles[$index][$attribute] = $value;

        foreach ($arFiles as $file) {

            if (strlen($file["name"]) > 0 && intval($file["size"]) > 0) {
                $res_file = CTenderixUserSupplier::SaveFile($ID, $file);
                if (!$res_file)
                    break;
            }
        }
    }

    if (intVal($res) <= 0 && $e = $GLOBALS["APPLICATION"]->GetException()) {
        $message = new CAdminMessage(($ID > 0 ? GetMessage("PW_TD_ERROR_UPDATE") : GetMessage("PW_TD_ERROR_ADD")), $e);
        $bInitVars = True;
    } elseif (strlen($save) > 0) {
        LocalRedirect("tenderix_users_supplier.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
    } else {
        $ID = $res;
        unset($_REQUEST["PROP"]);
        unset($_REQUEST["PROP_ID_MULTI"]);
    }
}

if ($ID > 0) {
    $db_users_supplier = CTenderixUserSupplier::GetList($by, $order, array("ID" => $ID), $is_filtered);
    $db_users_supplier->ExtractFields("str_", False);
}

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_tx_supplier", "", "str_");
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
        "LINK" => "/bitrix/admin/tenderix_users_supplier.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_list",
    )
);

if ($ID > 0 && $TENDERIXRIGHT == "W") {
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_NEW_SUPPLIER"),
        "LINK" => "/bitrix/admin/tenderix_users_supplier_edit.php?lang=" . LANG . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_new",
    );

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_DELETE_SUPPLIER"),
        "LINK" => "javascript:if(confirm('" . GetMessage("PW_TD_DELETE_SUPPLIER_CONFIRM") . "')) window.location='/bitrix/admin/tenderix_users_supplier.php?action=delete&ID[]=" . $ID . "&lang=" . LANG . "&" . bitrix_sessid_get() . "#tb';",
        "ICON" => "btn_delete",
    );
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
    echo $message->Show();
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" name="users_supplier_edit" enctype="multipart/form-data">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_TAB_SUPPLIER"), "ICON" => "tenderix_menu_icon_users", "TITLE" => GetMessage("PW_TD_TAB_SUPPLIER_DESCR")),
        array("DIV" => "edit2", "TAB" => GetMessage("PW_TD_TAB_SUPPLIER2"), "ICON" => "tenderix_menu_icon_users", "TITLE" => GetMessage("PW_TD_TAB_SUPPLIER_DESCR2")),
        array("DIV" => "edit3", "TAB" => GetMessage("PW_TD_TAB_SUPPLIER_SUBSCRIBE"), "ICON" => "tenderix_menu_icon_users", "TITLE" => GetMessage("PW_TD_TAB_SUPPLIER_SUBSCRIBE_DESCR")),
        array("DIV" => "edit4", "TAB" => GetMessage("PW_TD_TAB_SUPPLIER_DOCS"), "ICON" => "tenderix_menu_icon_users", "TITLE" => GetMessage("PW_TD_TAB_SUPPLIER_DOCS_DESCR")),
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
    <tr>
        <td width="40%">
            <span class="required">*</span> <?= GetMessage("PW_TD_SUPPLIER_LAST_NAME") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LAST_NAME" value="<?= htmlspecialcharsEx($str_LAST_NAME) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="required">*</span> <?= GetMessage("PW_TD_SUPPLIER_NAME") ?>:
        </td>
        <td width="60%">
            <input type="text" name="NAME" value="<?= htmlspecialcharsEx($str_NAME) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_SECOND_NAME") ?>:
        </td>
        <td width="60%">
            <input type="text" name="SECOND_NAME" value="<?= htmlspecialcharsEx($str_SECOND_NAME) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="required">*</span> <?= GetMessage("PW_TD_SUPPLIER_LOGIN") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LOGIN" value="<?= htmlspecialcharsEx($str_LOGIN) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="required">*</span> <?= GetMessage("PW_TD_SUPPLIER_EMAIL") ?>:
        </td>
        <td width="60%">
            <input type="text" name="EMAIL" value="<?= htmlspecialcharsEx($str_EMAIL) ?>" size="30" />
        </td>
    </tr>

    <tr id="bx_pass_row">
        <td><? if ($ID <= 0 || $COPY_ID > 0): ?><span class="required">*</span><? endif ?><? echo GetMessage('PW_TD_SUPPLIER_PASSWORD') ?>:</td>
        <td><input type="password" name="NEW_PASSWORD" size="30" maxlength="50" value="" autocomplete="off"></td>
    </tr>
    <tr id="bx_pass_confirm_row">
        <td><? if ($ID <= 0 || $COPY_ID > 0): ?><span class="required">*</span><? endif ?><? echo GetMessage('PW_TD_SUPPLIER_PASSWORD_CONFIRM') ?></td>
        <td><input type="password" name="NEW_PASSWORD_CONFIRM" size="30" maxlength="50" value="" autocomplete="off"></td>
    </tr>

    <tr>
        <td width="40%">
            <span class="required">*</span> <?= GetMessage("PW_TD_SUPPLIER_STATUS") ?>:
        </td>
        <td width="60%">
            <?
            echo SelectBox("STATUS", CTenderixUserSupplierStatus::GetDropDownList(), "--", htmlspecialchars($str_STATUS));
            ?>
        </td>
    </tr>

    <?
    $tabControl->BeginNextTab();
    ?>

    <tr>
        <td width="40%">
            <span class="required">*</span> <?= GetMessage("PW_TD_SUPPLIER_NAME_COMPANY") ?>:
        </td>
        <td width="60%">
            <input type="text" name="NAME_COMPANY" value="<?= htmlspecialcharsEx($str_NAME_COMPANY) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_NAME_DIRECTOR") ?>:
        </td>
        <td width="60%">
            <input type="text" name="NAME_DIRECTOR" value="<?= htmlspecialcharsEx($str_NAME_DIRECTOR) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_NAME_ACCOUNTANT") ?>:
        </td>
        <td width="60%">
            <input type="text" name="NAME_ACCOUNTANT" value="<?= htmlspecialcharsEx($str_NAME_ACCOUNTANT) ?>" size="30" />
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_SUPPLIER_CODE") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_CODE_INN") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_INN" value="<?= htmlspecialcharsEx($str_CODE_INN) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_CODE_KPP") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_KPP" value="<?= htmlspecialcharsEx($str_CODE_KPP) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_CODE_OKVED") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_OKVED" value="<?= htmlspecialcharsEx($str_CODE_OKVED) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_CODE_OKPO") ?>:
        </td>
        <td width="60%">
            <input type="text" name="CODE_OKPO" value="<?= htmlspecialcharsEx($str_CODE_OKPO) ?>" size="30" />
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_SUPPLIER_LEGALADDRESS") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_REGION") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_REGION" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_REGION) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_CITY") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_CITY" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_CITY) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_INDEX") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_INDEX" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_INDEX) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_STREET") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_STREET" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_STREET) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_POST") ?>:
        </td>
        <td width="60%">
            <input type="text" name="LEGALADDRESS_POST" value="<?= htmlspecialcharsEx($str_LEGALADDRESS_POST) ?>" size="30" />
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_SUPPLIER_POSTALADDRESS") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_REGION") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_REGION" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_REGION) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_CITY") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_CITY" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_CITY) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_INDEX") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_INDEX" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_INDEX) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_STREET") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_STREET" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_STREET) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_POST") ?>:
        </td>
        <td width="60%">
            <input type="text" name="POSTALADDRESS_POST" value="<?= htmlspecialcharsEx($str_POSTALADDRESS_POST) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_PHONE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="PHONE" value="<?= htmlspecialcharsEx($str_PHONE) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_FAX") ?>:
        </td>
        <td width="60%">
            <input type="text" name="FAX" value="<?= htmlspecialcharsEx($str_FAX) ?>" size="30" />
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_SUPPLIER_STATEREG") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_STATEREG_PLACE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="STATEREG_PLACE" value="<?= htmlspecialcharsEx($str_STATEREG_PLACE) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_STATEREG_DATE") ?>:
        </td>
        <td width="60%">
            <input type="text" name="STATEREG_DATE" value="<?= $str_STATEREG_DATE ?>" size="20" readonly />
            <?
            $APPLICATION->IncludeComponent(
                    'bitrix:main.calendar', '', array(
                'SHOW_INPUT' => 'N',
                'FORM_NAME' => 'users_supplier_edit',
                'INPUT_NAME' => 'STATEREG_DATE',
                'INPUT_VALUE' => $str_STATEREG_DATE,
                'SHOW_TIME' => 'N',
                'HIDE_TIMEBAR' => 'Y'
                    ), null, array('HIDE_ICONS' => 'Y')
            );
            ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_STATEREG_OGRN") ?>:
        </td>
        <td width="60%">
            <input type="text" name="STATEREG_OGRN" value="<?= htmlspecialcharsEx($str_STATEREG_OGRN) ?>" size="30" />
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_SUPPLIER_BANK") ?></td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_BANKING_NAME") ?>:
        </td>
        <td width="60%">
            <input type="text" name="BANKING_NAME" value="<?= htmlspecialcharsEx($str_BANKING_NAME) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNT") ?>:
        </td>
        <td width="60%">
            <input type="text" name="BANKING_ACCOUNT" value="<?= htmlspecialcharsEx($str_BANKING_ACCOUNT) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNTCORR") ?>:
        </td>
        <td width="60%">
            <input type="text" name="BANKING_ACCOUNTCORR" value="<?= htmlspecialcharsEx($str_BANKING_ACCOUNTCORR) ?>" size="30" />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_SUPPLIER_BANKING_BIK") ?>:
        </td>
        <td width="60%">
            <input type="text" name="BANKING_BIK" value="<?= htmlspecialcharsEx($str_BANKING_BIK) ?>" size="30" />
        </td>
    </tr>

    <tr class="heading">
        <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_DIRECTION_SUPPLIER") ?></td>
    </tr>
    <tr>
        <td width="40%">&nbsp;</td>
        <td width="60%">
            <?
            $rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", $arFilter = Array(), $is_filtered);
            $arrDirection = CTenderixUserSupplier::DirectionListArr($ID);
            while ($arSection = $rsSection->GetNext()):
                $checked = in_array($arSection["ID"], $arrDirection) ? " checked" : "";
                ?>
                <input<?= $checked ?> type="checkbox" value="<?= $arSection["ID"] ?>" name="direction[]" /> <?= $arSection["TITLE"] ?> <br />
            <? endwhile; ?>
        </td>
    </tr>

    <? //PROPERTY ?>
    <script type="text/javascript">
        function addNewElem(id, cnt) {
            var idProp = parseInt($("#id-prop-"+id).val());
            var str = $("#prop-"+id+"-"+(idProp-1)).html();
            var nidProp = idProp-parseInt(cnt);
            str = str.replace(/\[n\d+\]/g,"[n"+nidProp+"]");
            $("#prop-"+id).append('<div id="prop-'+id+'-'+idProp+'">'+str+'</div>');
            idProp += 1;
            $("#id-prop-"+id).val(idProp);
        }
    </script>
    <?
    $cnt = CTenderixUserSupplierProperty::GetCountActive();
    if ($cnt > 0) :
        ?>
        <tr class="heading">
            <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_PROPERTY") ?></td>
        </tr> 
    <? endif; ?>
    <?
    $rsPropList = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc", $arFilter = Array());
    if ($ID > 0) {
        $arPropSupplier = CTenderixUserSupplier::GetProperty($ID);
    }
    while ($arPropList = $rsPropList->GetNext()) :
        if ($arPropList["ACTIVE"] == "N")
            continue;
        ?>
        <tr valign="top">
            <td width="40%">
                <? if ($arPropList["IS_REQUIRED"] == "Y"): ?>
                    <span class="required">*</span>
                <? endif; ?>
                <?= $arPropList["TITLE"] ?>:
            </td>
            <td width="60%">
                <? $is_file_prop = false; ?>
                <? if ($ID > 0 && $arPropList["PROPERTY_TYPE"] == "F" && ($rsFiles = CTenderixUserSupplier::GetFileListProperty($ID, $arPropList["ID"])) && ($arFile = $rsFiles->GetNext())) { ?>
                    <? $is_file_prop = true; ?>
                    <table>
                        <tr>
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0" class="internal">
                                    <tr class="heading">
                                        <td align="center"><? echo GetMessage("PW_TD_FILE_NAME") ?></td>
                                        <td align="center"><? echo GetMessage("PW_TD_FILE_SIZE") ?></td>
                                        <td align="center"><? echo GetMessage("PW_TD_FILE_DELETE") ?></td>
                                    </tr>
                                    <?
                                    do {
                                        ?>
                                        <tr>
                                            <td><a href="tenderix_supplier_file.php?USER_ID=<? echo $ID ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>&amp;PROPERTY=<? echo $arPropList["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
                                            <td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
                                            <td align="center">
                                                <input type="checkbox" name="FILE_ID_PROP[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
                                                <input type="hidden" name="PROP[<?= $arPropList["ID"] ?>][<?= $arFile["ID"] ?>]" />
                                            </td>
                                        </tr>
                                    <? } while ($arFile = $rsFiles->GetNext()); ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                <? } ?>
                <?
                $result = "";
                if (strlen($arPropList["DEFAULT_VALUE"]) > 0 && $arPropList["MULTI"] == "Y") {
                    $arPropList["MULTI_CNT"]++;
                }
                $cntProp = 0;
                if ($ID > 0 && $arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
                    $cntProp = count($arPropSupplier[$arPropList["ID"]]);
                    $arPropList["MULTI_CNT"] += $cntProp;
                }
                if (isset($_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]]) &&
                        $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] >= $arPropList["MULTI_CNT"] &&
                        $arPropList["PROPERTY_TYPE"] != "L" &&
                        $arPropList["PROPERTY_TYPE"] != "F") {
                    if (strlen($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] - $cntProp - 1)]) > 0) {
                        $arPropList["MULTI_CNT"] = $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] + 1;
                    } else {
                        $arPropList["MULTI_CNT"] = $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]];
                    }
                }
                if ($arPropList["PROPERTY_TYPE"] == "L" || $arPropList["MULTI"] == "N") {
                    $arPropList["MULTI_CNT"] = 1;
                }
                $result .= '<div id="prop-' . $arPropList["ID"] . '">';
                for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
                    $result .= '<div id="prop-' . $arPropList["ID"] . '-' . $i . '">';
                    switch ($arPropList["PROPERTY_TYPE"]) {
                        case "S":
                        case "N":
                            if ($i > 0 || $ID > 0) {
                                $arPropList["DEFAULT_VALUE"] = "";
                            }
                            if ($ID > 0 && $i < $cntProp) {
                                $propName = "PROP[" . $arPropList["ID"] . "][" . $arPropSupplier[$arPropList["ID"]][$i]["ID"] . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropSupplier[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropSupplier[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropSupplier[$arPropList["ID"]][$i]["VALUE"]);
                            } else {
                                $propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
                            }
                            if ($arPropList["ROW_COUNT"] <= 1) {
                                $result .= '<input name="' . $propName . '" type="text" value="' . $propValue . '" size="' . $arPropList["COL_COUNT"] . '" />';
                            } else {
                                $result .= '<textarea name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
                            }
                            break;
                        case "F":
                            if (!$is_file_prop || $arPropList["MULTI"] == "Y")
                                $result .= '<input type="file" name="PROP[' . $arPropList["ID"] . '][n' . ($i - $cntProp) . ']" size="' . $arPropList["COL_COUNT"] . '" />';
                            break;
                        case "L":
                            $arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
                            if ($ID > 0) {
                                foreach ($arPropSupplier[$arPropList["ID"]] as $arrListSupplier) {
                                    $arrListValue[] = $arrListSupplier["VALUE"];
                                }
                            } else {
                                $arrListValue[] = $arrList["DEFAULT_VALUE_SELECT"];
                            }
                            if (isset($_REQUEST["PROP"][$arPropList["ID"]])) {
                                unset($arrListValue);
                                $arrListValue = $_REQUEST["PROP"][$arPropList["ID"]];
                            }
                            $result .= '<select name="PROP[' . $arPropList["ID"] . '][]"' . ($arPropList["MULTI"] == "Y" ? " multiple" : "") . ' size="' . $arPropList["ROW_COUNT"] . '">';
                            foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
                                $result .= '<option' . (in_array($idRow, $arrListValue) ? " selected" : "") . ' value="' . $idRow . '">' . $listVal . '</option>';
                            }
                            $result .= '</select>';
                            break;
                        case "T":
                            if ($i > 0 || $ID > 0) {
                                $arPropList["DEFAULT_VALUE"] = "";
                            }
                            if ($ID > 0 && $i < $cntProp) {
                                $propName = "PROP[" . $arPropList["ID"] . "][" . $arPropSupplier[$arPropList["ID"]][$i]["ID"] . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropSupplier[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropSupplier[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropSupplier[$arPropList["ID"]][$i]["VALUE"]);
                            } else {
                                $propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
                            }
                            $result .= '<textarea name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
                            break;
                        case "D":
                            if ($i > 0 || $ID > 0) {
                                $arPropList["DEFAULT_VALUE"] = "";
                            }
                            if ($ID > 0 && $i < $cntProp) {
                                $propName = "PROP[" . $arPropList["ID"] . "][" . $arPropSupplier[$arPropList["ID"]][$i]["ID"] . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropSupplier[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropSupplier[$arPropList["ID"]][$i]["ID"]]) : (strlen($arPropSupplier[$arPropList["ID"]][$i]["VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropSupplier[$arPropList["ID"]][$i]["VALUE"]), "FULL") : "");
                            } else {
                                $propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : (strlen($arPropList["DEFAULT_VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropList["DEFAULT_VALUE"]), "FULL") : "");
                            }
                            $result .= '<input type="text" name="' . $propName . '" value="' . $propValue . '" size="20" />';
                            $result .= Calendar($propName, "users_supplier_edit");
                            break;
                    }
                    $result .= '</div>';
                }
                $result .= '</div>';
                $result .= '<input type="hidden" name="PROP_ID_MULTI[' . $arPropList["ID"] . ']" id="id-prop-' . $arPropList["ID"] . '" value="' . $i . '" />';
                if ($arPropList["MULTI"] == "Y" && $arPropList["PROPERTY_TYPE"] != "L") {
                    $result .= '<input type="button" value="' . GetMessage("PW_TD_PROP_ADD") . '" onclick="addNewElem(' . $arPropList["ID"] . ', ' . $cntProp . ');" />';
                }
                echo $result;
                ?>
            </td>
        </tr>
    <? endwhile; ?>

    <?
    $tabControl->BeginNextTab();
    $rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", $arFilter = Array(), $is_filtered);

    $arrSubscribe = CTenderixUserSupplier::SubscribeListArr($ID);
    ?>
    <tr>
        <td width="10%">&nbsp;</td>
        <td width="90%">
            <?
            while ($arSection = $rsSection->GetNext()):
                $checked = in_array($arSection["ID"], $arrSubscribe) ? " checked" : "";
                ?>
                <input<?= $checked ?> type="checkbox" value="<?= $arSection["ID"] ?>" name="subscribe[]" /> <?= $arSection["TITLE"] ?> <br />
            <? endwhile; ?>
        </td>
    </tr>

    <?
    /*     * ***************
     * ATTACH
     * *************** */
    $tabControl->BeginNextTab();
    ?>
    <? if ($ID > 0 && ($rsFiles = CTenderixUserSupplier::GetFileList($ID)) && ($arFile = $rsFiles->GetNext())): ?>
        <tr>
            <td valign="top"><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:</td>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" class="internal">
                    <tr class="heading">
                        <td align="center"><? echo GetMessage("PW_TD_FILE_NAME") ?></td>
                        <td align="center"><? echo GetMessage("PW_TD_FILE_SIZE") ?></td>
                        <td align="center"><? echo GetMessage("PW_TD_FILE_DELETE") ?></td>
                    </tr>
                    <?
                    do {
                        ?>
                        <tr>
                            <td><a href="tenderix_supplier_file.php?USER_ID=<? echo $ID ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
                            <td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
                            <td align="center">
                                <input type="checkbox" name="FILE_ID[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
                            </td>
                        </tr>
                        <?
                    } while ($arFile = $rsFiles->GetNext());
                    ?>
                </table>
            </td>
        </tr>
    <? endif; ?>
    <tr>
        <td valign="top"><?= GetMessage("PW_TD_ATTACH_LOAD") ?>:</td>
        <td>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr><td><? echo CFile::InputFile("NEW_FILE[n0]", 40, 0) ?></td></tr>
                <tr><td><? echo CFile::InputFile("NEW_FILE[n1]", 40, 0) ?></td></tr>
                <tr><td><? echo CFile::InputFile("NEW_FILE[n2]", 40, 0) ?></td></tr>
            </table>
        </td>
    </tr>

    <?
    $tabControl->EndTab();
    ?>

    <?
    $tabControl->Buttons(
            array(
                "disabled" => ($TENDERIXRIGHT < "W"),
                "back_url" => "/bitrix/admin/tenderix_users_supplier.php?lang=" . LANG . "&" . GetFilterParams("filter_", false)
            )
    );
    $tabControl->End();
    ?>
</form>
<?
$tabControl->ShowWarnings("users_supplier_edit", $message);
?>
<? if (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1): ?>
    <? echo BeginNote(); ?>
    <? $GROUP_POLICY = CUser::GetGroupPolicy($ID);
    echo $GROUP_POLICY["PASSWORD_REQUIREMENTS"]; ?><br /><br />
    <span class="required">*</span> <? echo GetMessage("REQUIRED_FIELDS") ?>
    <? echo EndNote(); ?>
<? endif; ?>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>