<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
$module_id = "pweb.tenderix";
$sTableID = "tbl_tenderix_export_csv";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/prolog.php");
$TENDERIXRIGHT = $APPLICATION->GetGroupRight($module_id);
if ($TENDERIXRIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/include.php");
IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/csv_data.php");
print_r($_REQUEST);
set_time_limit(0);

$STEP = IntVal($STEP);
if ($STEP <= 0)
    $STEP = 1;
if ($REQUEST_METHOD == "POST" && strlen($backButton) > 0)
    $STEP = $STEP - 2;
if ($REQUEST_METHOD == "POST" && strlen($backButton2) > 0)
    $STEP = 1;

$strError = "";
$num_rows_writed = 0;
$arFieldsCompany = array("ID", "TITLE", "C_SORT", "ACTIVE", "URL", "DESCRIPTION");
$arFieldsTovar = array("ID", "SECTION_ID", "TITLE", "ACTIVE", "C_SORT", "UNIT", "ID_PROP", "SPR_ID", "TITLE_PROP", "VALUE", "REQUIRED", "EDIT", "ACTIVE_PROP", "C_SORT_PROP");

if ($REQUEST_METHOD == "POST" && $STEP > 1 && check_bitrix_sessid()) {
    if ($STEP > 1) {
        if (strlen($DATA_FILE_NAME) <= 0) {
            $strError .= GetMessage("PW_TD_NO_FILE_NAME") . "<br>";
        } elseif (preg_match('/[^a-zA-Z0-9\s!#\$%&\(\)\[\]\{\}+\.;=@\^_\~\/\\\\\-]/i', $DATA_FILE_NAME)) {
            $strError .= GetMessage("PW_TD_FILE_NAME_ERROR") . "<br>";
        } else {
            $DATA_FILE_NAME = Rel2Abs("/", $DATA_FILE_NAME);
            if (strtolower(substr($DATA_FILE_NAME, strlen($DATA_FILE_NAME) - 4)) != ".csv")
                $DATA_FILE_NAME .= ".csv";
        }

        if (strlen($strError) <= 0) {
            if (!($fp = fopen($_SERVER["DOCUMENT_ROOT"] . $DATA_FILE_NAME, "w")))
                $strError .= GetMessage("PW_TD_CANNOT_CREATE_FILE") . "<br>";
            @fclose($fp);
        }


        $csvFile = new CCSVData();

        $fields_type = "R";
        $csvFile->SetFieldsType($fields_type);

        $delimiter_r_char = "";
        switch ($delimiter_r) {
            case "TAB":
                $delimiter_r_char = "\t";
                break;
            case "ZPT":
                $delimiter_r_char = ",";
                break;
            case "SPS":
                $delimiter_r_char = " ";
                break;
            case "OTR":
                $delimiter_r_char = substr($delimiter_other_r, 0, 1);
                break;
            case "TZP":
                $delimiter_r_char = ";";
                break;
        }

        if (strlen($delimiter_r_char) != 1)
            $strError .= GetMessage("IBLOCK_ADM_EXP_NO_DELIMITER") . "<br>";

        if (strlen($strError) <= 0)
            $csvFile->SetDelimiter($delimiter_r_char);

        if (strlen($strError) > 0) {
            $STEP = 1;
        } else {
            if ($PLACE_IMPORT == 0) {
                $rsCompany = CTenderixCompany::GetList($by = "ID", $order = "asc", array());
                while ($arCompany = $rsCompany->Fetch()) {
                    $arrFields[] = array(
                        $arCompany["ID"],
                        $arCompany["TITLE"],
                        $arCompany["C_SORT"],
                        $arCompany["ACTIVE"],
                        $arCompany["URL"],
                        $arCompany["DESCRIPTION"]
                    );
                }
                $arFirstFields = $arFieldsCompany;
            } //print_r($arrFields); die;
            if ($PLACE_IMPORT == 1) {
                $rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_UNIT")), $is_filtered);
                while ($arUnit = $rsUnit->GetNext()) {
                    $arrUnit[$arUnit["ID"]] = $arUnit["TITLE"];
                }
                $rsTovar = CTenderixProducts::GetListProducts();
                while ($arTovar = $rsTovar->Fetch()) {
                    $rsTovarProp = CTenderixProductsProperty::GetList($by = "ID", $order = "asc", array("PRODUCTS_ID" => $arTovar["ID"]));
                    if (($arTovarProp = $rsTovarProp->Fetch())) {
                        do {
                            $arrFields[] = array(
                                $arTovar["ID"],
                                $arTovar["SECTION_ID"],
                                $arTovar["TITLE"],
                                $arTovar["ACTIVE"],
                                $arTovar["C_SORT"],
                                $arrUnit[$arTovar["UNIT_ID"]],
                                $arTovarProp["ID"],
                                $arTovarProp["SPR_ID"],
                                $arTovarProp["TITLE"],
                                $arTovarProp["VALUE"],
                                $arTovarProp["REQUIRED"],
                                $arTovarProp["EDIT"],
                                $arTovarProp["ACTIVE"],
                                $arTovarProp["C_SORT"]
                            );
                        } while ($arTovarProp = $rsTovarProp->Fetch());
                    } else {
                        $arrFields[] = array(
                            $arTovar["ID"],
                            $arTovar["SECTION_ID"],
                            $arTovar["TITLE"],
                            $arTovar["ACTIVE"],
                            $arTovar["C_SORT"],
                            $arrUnit[$arTovar["UNIT_ID"]]
                        );
                    }
                }
                $arFirstFields = $arFieldsTovar;
            }

            if ($first_line_names == "Y") {
                $csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"] . $DATA_FILE_NAME, $arFirstFields);
            }
            foreach ($arrFields as $arFields) {
                $csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"] . $DATA_FILE_NAME, $arFields);
                $num_rows_writed++;
            }
        }
    }
}


$APPLICATION->SetTitle(GetMessage("PW_TD_PAGE_TITLE"));
require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
/* * ****************************************************************** */
/* * ******************  BODY  **************************************** */
/* * ****************************************************************** */
CAdminMessage::ShowMessage($strError);
?>

<form method="POST" action="<? echo $sDocPath ?>?lang=<? echo LANG ?>" ENCTYPE="multipart/form-data" name="dataload">

    <input type="hidden" name="STEP" value="<? echo $STEP + 1; ?>">
    <?= bitrix_sessid_post() ?>

    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_EXPORT_TAB"), "ICON" => "tenderix_menu_icon_export", "TITLE" => GetMessage("PW_TD_EXPORT_TITLE")),
        array("DIV" => "edit2", "TAB" => GetMessage("PW_TD_RESULT_TAB"), "ICON" => "tenderix_menu_icon_export", "TITLE" => GetMessage("PW_TD_RESULT_TITLE")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
    $tabControl->Begin();

    $tabControl->BeginNextTab();
    if ($STEP < 2) {
        ?>
        <tr>
            <td><?= GetMessage("PW_TD_PLACE") ?>:</td>
            <td>
                <select name="PLACE_IMPORT">
                    <option<?= $PLACE_IMPORT == 0 ? " selected" : "" ?> value="0"><?= GetMessage("PW_TD_COMPANY") ?></option>
                    <option<?= $PLACE_IMPORT == 1 ? " selected" : "" ?> value="1"><?= GetMessage("PW_TD_TOVAR") ?></option>
                </select>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">
                <? echo GetMessage("PW_TD_CHOOSE_FORMAT") ?>
                <input type="hidden" name="fields_type" value="R">
            </td>
        </tr>
        <tr>
            <td valign="top"><? echo GetMessage("PW_TD_RAZDEL_TYPE") ?>:</td>
            <td valign="top">
                <input type="radio" name="delimiter_r" id="delimiter_TZP" value="TZP" <? if ($delimiter_r == "TZP" || strlen($delimiter_r) <= 0) echo "checked" ?>><label for="delimiter_TZP"><? echo GetMessage("PW_TD_RAZDEL_TZP") ?></label><br>
                <input type="radio" name="delimiter_r" id="delimiter_ZPT" value="ZPT" <? if ($delimiter_r == "ZPT") echo "checked" ?>><label for="delimiter_ZPT"><? echo GetMessage("PW_TD_RAZDEL_ZPT") ?></label><br>
                <input type="radio" name="delimiter_r" id="delimiter_TAB" value="TAB" <? if ($delimiter_r == "TAB") echo "checked" ?>><label for="delimiter_TAB"><? echo GetMessage("PW_TD_RAZDEL_TAB") ?></label><br>
                <input type="radio" name="delimiter_r" id="delimiter_SPS" value="SPS" <? if ($delimiter_r == "SPS") echo "checked" ?>><label for="delimiter_SPS"><? echo GetMessage("PW_TD_RAZDEL_SPS") ?></label><br>
                <input type="radio" name="delimiter_r" id="delimiter_OTR" value="OTR" <? if ($delimiter_r == "OTR") echo "checked" ?>><label for="delimiter_OTR"><? echo GetMessage("PW_TD_RAZDEL_OTR") ?></label>
                <input type="text" name="delimiter_other_r" size="3" value="<? echo htmlspecialchars($delimiter_other_r) ?>">
            </td>
        </tr>
        <tr>
            <td><? echo GetMessage("PW_TD_FIRST_NAMES") ?>:</td>
            <td>
                <input type="checkbox" name="first_line_names" value="Y" <? if ($first_line_names == "Y" || strlen($strError) <= 0) echo "checked" ?>>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2"><? echo GetMessage("PW_TD_FILE_NAME") ?></td>
        </tr>
        <tr>
            <td valign="top"><? echo GetMessage("PW_TD_ENTER_FILE_NAME") ?>:</td>
            <td valign="top">
                <input type="text" name="DATA_FILE_NAME" size="40" value="<? echo htmlspecialchars(strlen($DATA_FILE_NAME) > 0 ? $DATA_FILE_NAME : "/" . COption::GetOptionString("main", "upload_dir", "upload") . "/exportfile_tenderix_" . mt_rand(0, 999999) . ".csv") ?>"><br>
                <small><? echo GetMessage("PW_TD_FILE_WARNING") ?></small>
            </td>
        </tr>
        <?
    }

    $tabControl->BeginNextTab();
    if ($STEP == 2) {
        ?>
        <tr>
            <td valign="middle" colspan="2" nowrap>
                <b><?= GetMessage("PW_TD_FINISH"); ?></b>
            </td>
        </tr>
        <tr>
            <td valign="middle" colspan="2" nowrap>
                <?= GetMessage("PW_TD_ALL") ?>: <b><? echo $num_rows_writed ?></b><br />
            </td>
        </tr>
        <?
    }

    $tabControl->Buttons();
    if ($STEP < 2):
        ?>
        <input type="submit" value="<? echo GetMessage("PW_TD_BTN_NEXT_STEP_F") ?> &gt;&gt;" name="submit_btn">
        <?
    else:
        ?>
        <input type="submit" name="backButton2" value="&lt;&lt; <? echo GetMessage("PW_TD_2_1_STEP") ?>">
    <?
    endif;

    $tabControl->End();
    if (!$bPublicMode):
        ?>
        <script type="text/javaScript">
            <!--
            BX.ready(function() {
    <? if ($STEP < 2): ?>
                tabControl.SelectTab("edit1");
                tabControl.DisableTab("edit2");
    <? elseif ($STEP == 2): ?>
                tabControl.SelectTab("edit2");
                tabControl.DisableTab("edit1");
    <? endif; ?>
        });
        //-->
        </script>
        <?
    endif;
    ?>

</form>
<?
require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php");
?>