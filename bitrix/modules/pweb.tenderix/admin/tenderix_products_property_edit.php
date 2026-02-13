<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
if ($TENDERIXRIGHT<"W")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/include.php");
ClearVars();
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/prolog.php");

$ID = IntVal($ID);
$message = false;

$find_products_id = intval($_REQUEST["find_products_id"]);

$arProductsTitle = "";
if ($find_products_id > 0) {
    $db_res = CTenderixProducts::GetByID($find_products_id);
    if ($db_res && $res = $db_res->Fetch()) {
        $arProducts = $res;
        $arProductsTitle = " [ " . $arProducts["TITLE"] . " ]";
    }
}

$bInitVars = false;
if ((strlen($save) > 0 || strlen($apply) > 0) && $REQUEST_METHOD == "POST" && $TENDERIXRIGHT == "W" && check_bitrix_sessid()) {
    InitBVar($str_ACTIVE);
    $C_SORT = intval($C_SORT);
    global $DB;
    $arFields = array(
        "ACTIVE" => ($ACTIVE == "Y" ? "Y" : "N"),
        "C_SORT" => ($C_SORT <= 0 ? 100 : $C_SORT),
        "TITLE" => $TITLE,
        "PRODUCTS_ID" => $find_products_id,
        "SPR_ID" => $SPR_ID,
        "VALUE" => $VALUE,
        "EDIT" => ($EDIT == "Y" ? "Y" : "N"),
        "REQUIRED" => ($REQUIRED == "Y" ? "Y" : "N"),
    );

    $res = 0;
    if ($ID > 0)
        $res = CTenderixProductsProperty::Update($ID, $arFields);
    else
        $res = CTenderixProductsProperty::Add($arFields);
    if (intVal($res) <= 0 && $e = $GLOBALS["APPLICATION"]->GetException()) {
        $message = new CAdminMessage(($ID > 0 ? GetMessage("PW_TD_ERROR_UPDATE") : GetMessage("PW_TD_ERROR_ADD")), $e);
        $bInitVars = True;
    } elseif (strlen($save) > 0)
        LocalRedirect('tenderix_products_property.php?lang=' . LANG . '&find_products_id=' . $find_products_id . '&set_filter=Y');
    else
        $ID = $res;
}

if ($ID > 0) {
    $db_products_property = CTenderixProductsProperty::GetList($by, $order, array("ID" => $ID), $is_filtered);
    $db_products_property->ExtractFields("str_", False);
}

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_tx_prod_property", "", "str_");
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
        "LINK" => "/bitrix/admin/tenderix_products_property.php?lang=" . LANG . '&find_products_id=' . $find_products_id . "&" . GetFilterParams("filter_", false),
        "ICON" => "btn_list",
    )
);

if ($ID > 0 && $TENDERIXRIGHT == "W") {
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => GetMessage("PW_TD_NEW_PRODUCTS_PROPERTY"),
        "LINK" => "/bitrix/admin/tenderix_products_property_edit.php?lang=" . LANG . '&find_products_id=' . $find_products_id . "&set_filter=Y",
        "ICON" => "btn_new",
    );

    if ($ID != '1') {
        $aMenu[] = array(
            "TEXT" => GetMessage("PW_TD_DELETE_PRODUCTS_PROPERTY"),
            "LINK" => "javascript:if(confirm('" . GetMessage("PW_TD_DELETE_PRODUCTS_PROPERTY_CONFIRM") . "')) window.location='/bitrix/admin/tenderix_products_property.php?action=delete&ID[]=" . $ID . "&lang=" . LANG . "&" . bitrix_sessid_get() . "#tb';",
            "ICON" => "btn_delete",
        );
    }
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
    echo $message->Show();
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>" name="products_property_edit">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <input type="hidden" name="find_products_id" value="<? echo $find_products_id ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_TAB_PRODUCTS_PROPERTY"), "ICON" => "tenderix_menu_icon_products", "TITLE" => GetMessage("PW_TD_TAB_PRODUCTS_PROPERTY_DESCR").$arProductsTitle),
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
    
    if (isset($SPR_ID))
	$str_SPR_ID = $SPR_ID;
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
            <?= GetMessage("PW_TD_SPR_PRODUCTS_PROPERTY") ?>:
        </td>
        <td width="60%">
            <select onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;" <?php echo (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "")?>>
                <option value="<?=$APPLICATION->GetCurPageParam("SPR_ID=0", array("SPR_ID","tabControl_active_tab"))?>">--</option>
                <?
                $arSpr = CTenderixSpr::GetList($by, $order, $arFilter=array("ACTIVE"=>"Y"),$is_filtered);
                while ($ar_fields = $arSpr->GetNext()) {
                    $select_section = $str_SPR_ID == $ar_fields["ID"] ? " selected" : "";
                    echo "<option value='" . $APPLICATION->GetCurPageParam("SPR_ID=".$ar_fields["ID"]."", array("SPR_ID","tabControl_active_tab")) . "'" . $select_section . ">" . $ar_fields["TITLE"] . "</option>";
                }
                ?>
            </select>
            <input type="hidden" name="SPR_ID" value="<?=$str_SPR_ID?>">
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
            <?= GetMessage("PW_TD_VALUE_PRODUCTS_PROPERTY") ?>:
        </td>
        <td width="60%">
            <?if($str_SPR_ID > 0):?>
                <select name="VALUE">
                    <?
                    $arSprDetails = CTenderixSprDetails::GetList($by = "s_c_sort", $order = "desc", $arFilter=Array("SPR_ID" => $str_SPR_ID, "ACTIVE"=>"Y"), $is_filtered = "");
                    while ($ar_fields = $arSprDetails->GetNext()) {
                        $select_section = $str_VALUE == $ar_fields["ID"] ? " selected" : "";
                        echo "<option value='" . $ar_fields["ID"] . "'" . $select_section . ">" . $ar_fields["TITLE"] . "</option>";
                    }
                    ?>
                </select>
            <?else:?>
                <input type="text" name="VALUE" value="<?= htmlspecialcharsEx($str_VALUE) ?>" size="25" />
            <?endif;?>
        </td>
    </tr>
    
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_REQUIRED") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "REQUIRED", "Y", $str_REQUIRED, false) ?>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <?= GetMessage("PW_TD_EDIT") ?>:
        </td>
        <td width="60%">
            <?= InputType("checkbox", "EDIT", "Y", $str_EDIT, false) ?>
        </td>
    </tr>

    <?
    $tabControl->EndTab();
    ?>

    <?
    $tabControl->Buttons(
            array(
                "disabled" => ($TENDERIXRIGHT < "W"),
                "back_url" => "/bitrix/admin/tenderix_products_property.php?lang=" . LANG . '&find_products_id=' . $find_products_id . "&set_filter=Y"
            )
    );
    $tabControl->End();
    ?>
</form>
<?
$tabControl->ShowWarnings("products_property_edit", $message);
?>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>