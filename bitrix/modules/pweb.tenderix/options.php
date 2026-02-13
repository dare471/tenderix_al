<?
global $MESS;
$module_id = "pweb.tenderix";

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/options.php");

CModule::IncludeModule($module_id);

$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($MOD_RIGHT >= "R") :

    if ($REQUEST_METHOD == "GET" && strlen($RestoreDefaults) > 0) {
        COption::RemoveOption($module_id);
        $arGROUPS = array();
        reset($arGROUPS);
        while (list(, $value) = each($arGROUPS))
            $APPLICATION->DelGroupRight($module_id, array($value["ID"]));
    }

    if ($REQUEST_METHOD == "POST" && strlen($Update) > 0 && $MOD_RIGHT == "W" && check_bitrix_sessid()) {
        if (!is_array($arBUYER_GROUPS))
            $arBUYER_GROUPS = array();
        if (count($arBUYER_GROUPS) < 1)
            COption::SetOptionString($module_id, "PW_TD_BUYER_GROUPS", "1");
        else
            COption::SetOptionString($module_id, "PW_TD_BUYER_GROUPS", implode(",", $arBUYER_GROUPS));

        if (!is_array($arSUPPLIER_GROUPS))
            $arSUPPLIER_GROUPS = array();
        if (count($arSUPPLIER_GROUPS) < 1)
            COption::SetOptionString($module_id, "PW_TD_SUPPLIER_GROUPS", "3");
        else
            COption::SetOptionString($module_id, "PW_TD_SUPPLIER_GROUPS", implode(",", $arSUPPLIER_GROUPS));

        if (!is_array($arADMIN_GROUPS))
            $arADMIN_GROUPS = array();
        if (count($arADMIN_GROUPS) < 1)
            COption::SetOptionString($module_id, "PW_TD_ADMIN_GROUPS", "1");
        else
            COption::SetOptionString($module_id, "PW_TD_ADMIN_GROUPS", implode(",", $arADMIN_GROUPS));
        
        COption::SetOptionString($module_id, "PW_TD_SITE", $arSITE);

        COption::SetOptionString($module_id, "PW_TD_BUYER_GROUPS_DEFAULT", $arBUYER_GROUPS_DEFAULT);
        COption::SetOptionString($module_id, "PW_TD_SUPPLIER_GROUPS_DEFAULT", $arSUPPLIER_GROUPS_DEFAULT);
        COption::SetOptionString($module_id, "PW_TD_ADMIN_GROUPS_DEFAULT", $arADMIN_GROUPS_DEFAULT);

        /*COption::SetOptionString($module_id, "PW_TD_OPTIONS_SPR_UNIT", $PW_TD_OPTIONS_SPR_UNIT);
        COption::SetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_DELIVERY", $PW_TD_OPTIONS_SPR_TERM_DELIVERY);
        COption::SetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_PAYMENT", $PW_TD_OPTIONS_SPR_TERM_PAYMENT);*/
    }
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "tenderix_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
        array("DIV" => "edit2", "TAB" => GetMessage("PW_TD_TAB_SPR"), "ICON" => "tenderix_settings", "TITLE" => GetMessage("PW_TD_TAB_TITLE_SPR")),
        array("DIV" => "edit3", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "tenderix_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    ?>
    <?
    $tabControl->Begin();
    ?><form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialchars($mid) ?>&lang=<?= LANGUAGE_ID ?>"><?= bitrix_sessid_post() ?><?
    $tabControl->BeginNextTab();
    ?>
        <?
        $rUserGroups = CGroup::GetList($by = "c_sort", $order = "asc");
        while ($arUserGroups = $rUserGroups->Fetch()) {
            $ug_id[] = $arUserGroups["ID"];
            $ug[] = "[" . $arUserGroups["ID"] . "] " . $arUserGroups["NAME"];
        }

        $arrSites = array();
        $rs = CSite::GetList(($by = "sort"), ($order = "asc"));
        $arrSites_id[] = "";
        $arrSites[] = "--";
        while ($ar = $rs->Fetch()) {
            $arrSites_id[] = $ar["ID"];
            $arrSites[] = "[" . $ar["ID"] . "] " . $ar["NAME"];
        }
        ?>
        <tr class="heading">
            <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_SITE") ?></td>
        </tr>
        <?
        $arSITE = COption::GetOptionString($module_id, "PW_TD_SITE");
        ?>
        <tr>
            <td valign="top" width="50%"><?= GetMessage("PW_TD_SITE_SELECT") ?>:</td>
            <td valign="middle" nowrap width="50%">
                <?= SelectBoxFromArray("arSITE", array("REFERENCE" => $arrSites, "REFERENCE_ID" => $arrSites_id), $arSITE); ?>
            </td>
        </tr>

        <tr class="heading">
            <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_DEFAULT") ?></td>
        </tr>
        <?
        $arBUYER_GROUPS_DEFAULT = COption::GetOptionString($module_id, "PW_TD_BUYER_GROUPS_DEFAULT");
        ?>
        <tr>
            <td valign="top" width="50%"><?= GetMessage("PW_TD_BUYER_GROUPS_DEFAULT_LABEL") ?>:</td>
            <td valign="middle" nowrap width="50%"><?= SelectBoxFromArray("arBUYER_GROUPS_DEFAULT", array("REFERENCE" => $ug, "REFERENCE_ID" => $ug_id), $arBUYER_GROUPS_DEFAULT); ?></td>
        </tr>
        <?
        $arSUPPLIER_GROUPS_DEFAULT = COption::GetOptionString($module_id, "PW_TD_SUPPLIER_GROUPS_DEFAULT");
        ?>
        <tr>
            <td valign="top" width="50%"><?= GetMessage("PW_TD_SUPPLIER_GROUPS_DEFAULT_LABEL") ?>:</td>
            <td valign="middle" nowrap width="50%"><?= SelectBoxFromArray("arSUPPLIER_GROUPS_DEFAULT", array("REFERENCE" => $ug, "REFERENCE_ID" => $ug_id), $arSUPPLIER_GROUPS_DEFAULT); ?></td>
        </tr>
        <?
        $arADMIN_GROUPS_DEFAULT = COption::GetOptionString($module_id, "PW_TD_ADMIN_GROUPS_DEFAULT");
        ?>
        <tr>
            <td valign="top" width="50%"><?= GetMessage("PW_TD_ADMIN_GROUPS_DEFAULT_LABEL") ?>:</td>
            <td valign="middle" nowrap width="50%"><?= SelectBoxFromArray("arADMIN_GROUPS_DEFAULT", array("REFERENCE" => $ug, "REFERENCE_ID" => $ug_id), $arADMIN_GROUPS_DEFAULT); ?></td>
        </tr>


        <tr class="heading">
            <td align="center" colspan="2" nowrap><? echo GetMessage("PW_TD_GROUP_OTHER") ?></td>
        </tr>
        <?
        $arBUYER_GROUPS = explode(",", COption::GetOptionString($module_id, "PW_TD_BUYER_GROUPS"));
        ?>
        <tr>
            <td valign="top" width="50%"><?= GetMessage("PW_TD_BUYER_GROUPS_LABEL") ?>:<br><img src="/bitrix/images/statistic/mouse.gif" width="44" height="21" border=0 alt=""></td>
            <td valign="middle" nowrap width="50%"><?= SelectBoxMFromArray("arBUYER_GROUPS[]", array("REFERENCE" => $ug, "REFERENCE_ID" => $ug_id), $arBUYER_GROUPS, "", false, 10); ?></td>
        </tr>

        <?
        $arSUPPLIER_GROUPS = explode(",", COption::GetOptionString($module_id, "PW_TD_SUPPLIER_GROUPS"));
        ?>
        <tr>
            <td valign="top" width="50%"><?= GetMessage("PW_TD_SUPPLIER_GROUPS_LABEL") ?>:<br><img src="/bitrix/images/statistic/mouse.gif" width="44" height="21" border=0 alt=""></td>
            <td valign="middle" nowrap width="50%"><?= SelectBoxMFromArray("arSUPPLIER_GROUPS[]", array("REFERENCE" => $ug, "REFERENCE_ID" => $ug_id), $arSUPPLIER_GROUPS, "", false, 10); ?></td>
        </tr>

        <?
        $arADMIN_GROUPS = explode(",", COption::GetOptionString($module_id, "PW_TD_ADMIN_GROUPS"));
        ?>
        <tr>
            <td valign="top" width="50%"><?= GetMessage("PW_TD_ADMIN_GROUPS_LABEL") ?>:<br><img src="/bitrix/images/statistic/mouse.gif" width="44" height="21" border=0 alt=""></td>
            <td valign="middle" nowrap width="50%"><?= SelectBoxMFromArray("arADMIN_GROUPS[]", array("REFERENCE" => $ug, "REFERENCE_ID" => $ug_id), $arADMIN_GROUPS, "", false, 10); ?></td>
        </tr>


        <? $tabControl->BeginNextTab(); ?>
        <?
        $arSpr = CTenderixSpr::GetList();
        while ($ar_fields = $arSpr->GetNext()) {
            $arrSpr[$ar_fields["ID"]] = $ar_fields["TITLE"];
        }
        ?>
        <tr>
            <td valign="top"  width="50%"><? echo GetMessage("PW_TD_OPTIONS_SPR_UNIT") ?>:</td>
            <td valign="middle" width="50%">
                <? $val = COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_UNIT"); ?>
                <select name="PW_TD_OPTIONS_SPR_UNIT">
                    <option value="">--</option>
                    <?
                    foreach ($arrSpr as $sprId => $sprTitle) {
                        $select_section = $val == $sprId ? " selected" : "";
                        echo "<option value='" . $sprId . "'" . $select_section . ">" . $sprTitle . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td valign="top"  width="50%"><? echo GetMessage("PW_TD_OPTIONS_SPR_TERM_DELIVERY") ?>:</td>
            <td valign="middle" width="50%">
                <? $val = COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_DELIVERY"); ?>
                <select name="PW_TD_OPTIONS_SPR_TERM_DELIVERY">
                    <option value="">--</option>
                    <?
                    foreach ($arrSpr as $sprId => $sprTitle) {
                        $select_section = $val == $sprId ? " selected" : "";
                        echo "<option value='" . $sprId . "'" . $select_section . ">" . $sprTitle . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td valign="top"  width="50%"><? echo GetMessage("PW_TD_OPTIONS_SPR_TERM_PAYMENT") ?>:</td>
            <td valign="middle" width="50%">
                <? $val = COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_PAYMENT"); ?>
                <select name="PW_TD_OPTIONS_SPR_TERM_PAYMENT">
                    <option value="">--</option>
                    <?
                    foreach ($arrSpr as $sprId => $sprTitle) {
                        $select_section = $val == $sprId ? " selected" : "";
                        echo "<option value='" . $sprId . "'" . $select_section . ">" . $sprTitle . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>


        <? $tabControl->BeginNextTab(); ?>
        <? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php"); ?>
        <? $tabControl->Buttons(); ?>
        <script language="JavaScript">
            function RestoreDefaults()
            {
                if(confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>'))
                window.location = "<? echo $APPLICATION->GetCurPage() ?>?RestoreDefaults=Y&lang=<?= LANGUAGE_ID ?>&mid=<? echo urlencode($mid) ?>";
            }
        </script>
        <input <? if ($MOD_RIGHT < "W")
        echo "disabled" ?> type="submit" name="Update" value="<?= GetMessage("PW_TD_OPTIONS_SAVE") ?>">
        <input type="hidden" name="Update" value="Y">
        <input type="reset" name="reset" value="<?= GetMessage("PW_TD_OPTIONS_RESET") ?>">
        <input <? if ($MOD_RIGHT < "W")
            echo "disabled" ?> type="button" title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>" OnClick="RestoreDefaults();" value="<? echo GetMessage("PW_TD_OPTIONS_DEFAULTS") ?>">
            <? $tabControl->End(); ?>
    </form>
<? endif; ?>
