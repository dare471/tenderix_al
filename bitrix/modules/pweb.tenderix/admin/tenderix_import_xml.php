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

$bPublicMode = defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1;
print_r($_REQUEST);
set_time_limit(0);
$STEP = IntVal($STEP);
if ($STEP <= 0)
    $STEP = 1;
if ($REQUEST_METHOD == "POST" && strlen($backButton) > 0)
    $STEP = $STEP - 2;
if ($REQUEST_METHOD == "POST" && strlen($backButton2) > 0)
    $STEP = 1;

$CUR_FILE_POS = IntVal($CUR_FILE_POS);
$strError = "";

$arFieldsCompany = array(
    array("ID" => "ID", "name" => "ID"),
    array("ID" => "TITLE", "name" => GetMessage("PW_TD_COMPANY_TITLE")),
    array("ID" => "ACTIVE", "name" => GetMessage("PW_TD_COMPANY_ACTIVE")),
    array("ID" => "URL", "name" => GetMessage("PW_TD_COMPANY_URL")),
    array("ID" => "DESCRIPTION", "name" => GetMessage("PW_TD_COMPANY_DESCRIPTION")),
    array("ID" => "C_SORT", "name" => GetMessage("PW_TD_COMPANY_SORT")),
);
$arFieldsTovar = array(
    array("ID" => "ID", "name" => "ID"),
    array("ID" => "SECTION_ID", "name" => GetMessage("PW_TD_TOVAR_SECTION_ID")),
    array("ID" => "TITLE", "name" => GetMessage("PW_TD_TOVAR_TITLE")),
    array("ID" => "ACTIVE", "name" => GetMessage("PW_TD_TOVAR_ACTIVE")),
    array("ID" => "C_SORT", "name" => GetMessage("PW_TD_TOVAR_C_SORT")),
    array("ID" => "UNIT", "name" => GetMessage("PW_TD_TOVAR_UNIT")),
    array("ID" => "ID_PROP", "name" => "ID_PROP"),
    array("ID" => "SPR_ID", "name" => GetMessage("PW_TD_TOVAR_SPR_ID")),
    array("ID" => "TITLE_PROP", "name" => GetMessage("PW_TD_TOVARPROP_TITLE")),
    array("ID" => "VALUE", "name" => GetMessage("PW_TD_TOVARPROP_VALUE")),
    array("ID" => "REQUIRED", "name" => GetMessage("PW_TD_TOVARPROP_REQUIRED")),
    array("ID" => "EDIT", "name" => GetMessage("PW_TD_TOVARPROP_EDIT")),
    array("ID" => "ACTIVE_PROP", "name" => GetMessage("PW_TD_TOVARPROP_ACTIVE")),
    array("ID" => "C_SORT_PROP", "name" => GetMessage("PW_TD_TOVARPROP_C_SORT")),
);

function text2utf($text = "") {
    return $GLOBALS["APPLICATION"]->ConvertCharset($text, SITE_CHARSET, "UTF-8");
}

function utf2text($text = "") {
    return $GLOBALS["APPLICATION"]->ConvertCharset($text, "UTF-8", SITE_CHARSET);
}

if (($REQUEST_METHOD == "POST" || $CUR_FILE_POS > 0) && $STEP > 1 && check_bitrix_sessid()) {
    //*****************************************************************//
    if ($STEP > 1) {
        //*****************************************************************//
        $DATA_FILE_NAME = "";

        if (is_uploaded_file($_FILES["DATA_FILE"]["tmp_name"])) {
            if (strtolower(GetFileExtension($_FILES["DATA_FILE"]["name"])) != "csv")
                $strError .= GetMessage("PW_TD_NOT_CSV") . "<br>";
            else {
                $DATA_FILE_NAME = "/" . COption::GetOptionString("main", "upload_dir", "upload") . "/" . basename($_FILES["DATA_FILE"]["name"]);
                if ($APPLICATION->GetFileAccessPermission($DATA_FILE_NAME) >= "W")
                    copy($_FILES["DATA_FILE"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $DATA_FILE_NAME);
                else
                    $DATA_FILE_NAME = "";
            }
        }

        if (strlen($strError) <= 0) {
            if (strlen($DATA_FILE_NAME) <= 0) {
                if (strlen($URL_DATA_FILE) > 0) {
                    $URL_DATA_FILE = trim(str_replace("\\", "/", trim($URL_DATA_FILE)), "/");
                    $FILE_NAME = rel2abs($_SERVER["DOCUMENT_ROOT"], "/" . $URL_DATA_FILE);
                    if (
                            (strlen($FILE_NAME) > 1) &&
                            ($FILE_NAME === "/" . $URL_DATA_FILE) &&
                            file_exists($_SERVER["DOCUMENT_ROOT"] . $FILE_NAME) &&
                            is_file($_SERVER["DOCUMENT_ROOT"] . $FILE_NAME) &&
                            ($APPLICATION->GetFileAccessPermission($FILE_NAME) >= "W")
                    ) {
                        $DATA_FILE_NAME = $FILE_NAME;
                    }
                }
            }

            if (strlen($DATA_FILE_NAME) <= 0)
                $strError .= GetMessage("PW_TD_NO_DATA_FILE") . "<br>";
        }


        if (strlen($strError) > 0) {
            $STEP = 1;
        } else {
            $xml = simplexml_load_file($_SERVER["DOCUMENT_ROOT"] . $DATA_FILE_NAME);

            $teg1 = text2utf(GetMessage("PW_TD_TEG1"));
            $teg2 = text2utf(GetMessage("PW_TD_TEG2"));
            $teg3 = text2utf(GetMessage("PW_TD_TEG3"));
            $teg4 = text2utf(GetMessage("PW_TD_TEG4"));
            $teg5 = text2utf(GetMessage("PW_TD_TEG5"));
            $teg6 = text2utf(GetMessage("PW_TD_TEG6"));
            $teg7 = text2utf(GetMessage("PW_TD_TEG7"));
            $teg8 = text2utf(GetMessage("PW_TD_TEG8"));
            $teg9 = text2utf(GetMessage("PW_TD_TEG9"));
            $teg10 = text2utf(GetMessage("PW_TD_TEG10"));
            $teg11 = text2utf(GetMessage("PW_TD_TEG11"));
            $teg12 = text2utf(GetMessage("PW_TD_TEG12"));
            $teg13 = text2utf(GetMessage("PW_TD_TEG13"));
            $teg14 = text2utf(GetMessage("PW_TD_TEG14"));
            $teg15 = text2utf(GetMessage("PW_TD_TEG15"));
            $teg16 = text2utf(GetMessage("PW_TD_TEG16"));

            $line_num = 0;
            $correct_lines = 0;
            $error_lines = 0;
            $tovar_lines = 0;
            $prop_lines = 0;

            if ($PLACE_IMPORT == 1) {
                $section_name = utf2text($xml->$teg1->$teg2->$teg3->$teg4);
//TODO: safsdf
                $tovar_obj = $xml->$teg5->$teg6->$teg7;
                foreach ($tovar_obj as $tovar) {
                    $idTovar = utf2text($tovar->$teg16);
                    $tovar_arr[$idTovar]["name"] = utf2text($tovar->$teg4);
                    $tovar_arr[$idTovar]["unit"] = utf2text($tovar->$teg8->$teg9->$teg10);
                    $tovar_data_obj = $tovar->$teg8->$teg9->$teg12->$teg13;
                    foreach ($tovar_data_obj as $data) {
                        $tovar_arr[$idTovar]["property"][] = array(
                            "name" => utf2text($data->$teg14),
                            "value" => utf2text($data->$teg15)
                        );
                    }
                }

                $rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_UNIT")), $is_filtered);
                while ($arUnit = $rsUnit->GetNext()) {
                    $arrUnit[$arUnit["ID"]] = $arUnit["TITLE"];
                }
                $rsSection = CTenderixSection::GetList();
                while ($arSection = $rsSection->Fetch()) {
                    $arrSection[$arSection["ID"]] = $arSection["TITLE"];
                }

                foreach ($tovar_arr as $tovarFields) {
                    $resUnit = 0;
                    $resSection = 0;
                    foreach ($arrUnit as $idUnit => $titleUnit) {
                        if (strstr($titleUnit, $tovarFields["unit"])) {
                            $resUnit = $idUnit;
                            break;
                        }
                    }
                    /* foreach ($arrSection as $idSection => $titleSection) {
                      if (strstr($titleSection, $section_name)) {
                      $resUnit = $idUnit;
                      break;
                      }
                      } */
                    $arFields = array(
                        "SECTION_ID" => 1,
                        "TITLE" => $tovarFields["name"],
                        "UNIT_ID" => $resUnit,
                    );
                    $rsLP = CTenderixProducts::GetListProducts(array(), array("TITLE" => $arFields["TITLE"]));
                    if (!$arLP = $rsLP->Fetch()) {
                        $id = CTenderixProducts::Add($arFields);
                        if ($id > 0) {
                            $tovar_lines++;
                            $correct_lines++;
                        } else {
                            $error_lines++;
                        }
                    } else {
                        $id = $arLP["ID"];
                    }

                    if (count($tovarFields["property"]) > 0) {
                        foreach ($tovarFields["property"] as $tovarProperty) {
                            $arFieldsProp = array(
                                "PRODUCTS_ID" => $id,
                                "TITLE" => $tovarProperty["name"],
                                "VALUE" => $tovarProperty["value"],
                            );
                            $rsLPP = CTenderixProductsProperty::GetList($by = "", $order = "", array("TITLE" => $arFieldsProp["TITLE"], "PRODUCTS_ID" => $id));
                            if (!$arLPP = $rsLPP->Fetch()) {
                                $idProp = CTenderixProductsProperty::Add($arFieldsProp);
                                if (intval($idProp) > 0) {
                                    $prop_lines++;
                                    $correct_lines++;
                                } else {
                                    $error_lines++;
                                }
                            } else {
                                $idProp = $arLPP["ID"];
                            }
                        }
                    }
                }
            }
        }
        //*****************************************************************//
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
        array("DIV" => "edit1", "TAB" => GetMessage("PW_TD_IMPORT_TAB1"), "ICON" => "tenderix_menu_icon_import", "TITLE" => GetMessage("PW_TD_IMPORT_TITLE1")),
        array("DIV" => "edit2", "TAB" => GetMessage("PW_TD_IMPORT_TAB2"), "ICON" => "tenderix_menu_icon_import", "TITLE" => GetMessage("PW_TD_IMPORT_TITLE2")),
        array("DIV" => "edit3", "TAB" => GetMessage("PW_TD_IMPORT_TAB3"), "ICON" => "tenderix_menu_icon_import", "TITLE" => GetMessage("PW_TD_IMPORT_TITLE3")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
    $tabControl->Begin();

    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td><? echo GetMessage("PW_TD_DATA_FILE") ?>:</td>
        <td>
            <input type="text" name="URL_DATA_FILE" value="<? echo htmlspecialchars($URL_DATA_FILE) ?>" size="30">
            <input type="button" value="<? echo GetMessage("PW_TD_OPEN_FILE") ?>" OnClick="BtnClick()">
            <?
            CAdminFileDialog::ShowScript
                    (
                    Array(
                        "event" => "BtnClick",
                        "arResultDest" => array("FORM_NAME" => "dataload", "FORM_ELEMENT_NAME" => "URL_DATA_FILE"),
                        "arPath" => array("SITE" => SITE_ID, "PATH" => "/" . COption::GetOptionString("main", "upload_dir", "upload")),
                        "select" => 'F', // F - file only, D - folder only
                        "operation" => 'O', // O - open, S - save
                        "showUploadTab" => true,
                        "showAddToMenuTab" => false,
                        "fileFilter" => 'csv',
                        "allowAllFiles" => true,
                        "SaveConfig" => true,
                    )
            );
            ?>
        </td>
    </tr>

    <tr>
        <td><?= GetMessage("PW_TD_PLACE") ?>:</td>
        <td>
            <select name="PLACE_IMPORT">
                <option<?= $PLACE_IMPORT == 0 ? " selected" : "" ?> value="0"><?= GetMessage("PW_TD_COMPANY") ?></option>
                <option<?= $PLACE_IMPORT == 1 ? " selected" : "" ?> value="1"><?= GetMessage("PW_TD_TOVAR") ?></option>
            </select>
        </td>
    </tr>

    <tr id="table_r1">
        <td valign="top" width="40%"><? echo GetMessage("PW_TD_RAZDEL_TYPE") ?>:</td>
        <td valign="top" width="60%">
            <input type="radio" name="delimiter_r" id="delimiter_r_TZP" value="TZP" <? if ($delimiter_r == "TZP" || strlen($delimiter_r) <= 0) echo "checked" ?>><label for="delimiter_r_TZP"><? echo GetMessage("PW_TD_RAZDEL_TZP") ?></label><br>
            <input type="radio" name="delimiter_r" id="delimiter_r_ZPT" value="ZPT" <? if ($delimiter_r == "ZPT") echo "checked" ?>><label for="delimiter_r_ZPT"><? echo GetMessage("PW_TD_RAZDEL_ZPT") ?></label><br>
            <input type="radio" name="delimiter_r" id="delimiter_r_TAB" value="TAB" <? if ($delimiter_r == "TAB") echo "checked" ?>><label for="delimiter_r_TAB"><? echo GetMessage("PW_TD_RAZDEL_TAB") ?></label><br>
            <input type="radio" name="delimiter_r" id="delimiter_r_SPS" value="SPS" <? if ($delimiter_r == "SPS") echo "checked" ?>><label for="delimiter_r_SPS"><? echo GetMessage("PW_TD_RAZDEL_SPS") ?></label><br>
            <input type="radio" name="delimiter_r" id="delimiter_r_OTR" value="OTR" <? if ($delimiter_r == "OTR") echo "checked" ?>><label for="delimiter_r_OTR"><? echo GetMessage("PW_TD_RAZDEL_OTR") ?></label>
            <input type="text" name="delimiter_other_r" size="3" value="<? echo htmlspecialchars($delimiter_other_r) ?>">
        </td>
    </tr>

    <tr id="table_r2">
        <td><? echo GetMessage("PW_TD_FIRST_NAMES") ?></td>
        <td>
            <input type="hidden" name="first_names_r" id="first_names_r_N" value="N">
            <input type="checkbox" name="first_names_r" id="first_names_r_Y" value="Y" <? if ($first_names_r != "N") echo "checked" ?>>
        </td>
    </tr>
    <?
    $tabControl->EndTab();

    $tabControl->BeginNextTab();
    if ($STEP == 2) {
        if ($PLACE_IMPORT == 0) {
            $arFieldsDescription = $arFieldsCompany;
        } elseif ($PLACE_IMPORT == 1) {
            $arFieldsDescription = $arFieldsTovar;
        }
        foreach ($arDataFileFields as $DATA_FIELDS_ID => $DATA_FIELDS) {
            ?>
            <tr>
                <td><b><?= GetMessage("PW_TD_POLE") ?> <?= $DATA_FIELDS_ID + 1 ?></b> (<?= $DATA_FIELDS ?>):</td>
                <td>
                    <select name="field_<?= $DATA_FIELDS_ID ?>">
                        <option value=""> - </option>
                        <? foreach ($arFieldsDescription as $FieldsDescription): ?>
                            <option<?= $DATA_FIELDS == $FieldsDescription["ID"] ? " selected" : "" ?> value="<?= $FieldsDescription["ID"] ?>"><?= $FieldsDescription["name"] ?></option>
                        <? endforeach; ?>
                    </select>
                </td>
            </tr>
            <?
        }
    }
    $tabControl->EndTab();

    $tabControl->BeginNextTab();
    if ($STEP == 3) {
        ?>
        <tr>
            <td valign="middle" colspan="2" nowrap>
                <b><?= GetMessage("PW_TD_FINISH"); ?></b>
            </td>
        </tr>
        <tr>
            <td valign="middle" colspan="2" nowrap>
                <?= GetMessage("PW_TD_SU_ALL") ?> :<b><? echo $line_num ?></b><br />
                <?= GetMessage("PW_TD_SU_CORR") ?> :<b><? echo $correct_lines ?></b><br />
                <? if ($PLACE_IMPORT == 1): ?>
                    <?= GetMessage("PW_TD_SU_TOVAR") ?> :<b><? echo $tovar_lines ?></b><br />
                    <?= GetMessage("PW_TD_SU_PROP") ?> :<b><? echo $prop_lines ?></b><br />
                <? endif; ?>
                <?= GetMessage("PW_TD_SU_ER") ?> :<b><? echo $error_lines ?></b><br />
            </td>
        </tr>
        <?
    }
    $tabControl->EndTab();

    $tabControl->Buttons();
    ?>

    <? if ($STEP < 3): ?>
        <input type="hidden" name="STEP" value="<? echo $STEP + 1; ?>">
        <?= bitrix_sessid_post() ?>
        <? if ($STEP > 1): ?>
            <input type="hidden" name="URL_DATA_FILE" value="<? echo htmlspecialchars($DATA_FILE_NAME) ?>">
            <input type="hidden" name="PLACE_IMPORT" value="<? echo $PLACE_IMPORT ?>">
            <input type="hidden" name="delimiter_r" value="<? echo htmlspecialchars($delimiter_r) ?>">
            <input type="hidden" name="delimiter_other_r" value="<? echo htmlspecialchars($delimiter_other_r) ?>">
            <input type="hidden" name="first_names_r" value="<? echo htmlspecialchars($first_names_r) ?>">
        <? endif; ?>

        <? if ($STEP <> 2): ?>
            <? foreach ($_POST as $name => $value): ?>
                <? if (preg_match("/^field_(\\d+)$/", $name)): ?>
                    <input type="hidden" name="<? echo $name ?>" value="<? echo htmlspecialchars($value) ?>">
                <? endif ?>
            <? endforeach ?>
        <? endif; ?>

        <? if ($STEP > 1): ?>
            <input type="submit" name="backButton" value="&lt;&lt; <? echo GetMessage("PW_TD_BTN_BACK") ?>">
        <? endif ?>
        <input type="submit" value="<? echo ($STEP == 3) ? GetMessage("PW_TD_BTN_NEXT_STEP_F") : GetMessage("PW_TD_BTN_NEXT_STEP") ?> &gt;&gt;" name="submit_btn">

        <?
        if ($STEP == 1) {
            ?>
            <SCRIPT LANGUAGE="JavaScript">
                DeactivateAllExtra();
                ChangeExtra();
            </SCRIPT>
            <?
        }
        ?>
    <? else: ?>
        <input type="submit" name="backButton2" value="&lt;&lt; <? echo GetMessage("PW_TD_2_1_STEP") ?>">
    <? endif; ?>
    <?
    $tabControl->End();
    ?>

</form>

<script language="JavaScript">
    <!--
<? if ($STEP < 2): ?>
        tabControl.SelectTab("edit1");
        tabControl.DisableTab("edit2");
        tabControl.DisableTab("edit3");
<? elseif ($STEP == 2): ?>
        tabControl.SelectTab("edit2");
        tabControl.DisableTab("edit1");
        tabControl.DisableTab("edit3");
<? elseif ($STEP == 3): ?>
        tabControl.SelectTab("edit3");
        tabControl.DisableTab("edit1");
        tabControl.DisableTab("edit2");
<? endif; ?>
    //-->
</script>


<?
require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php");
?>