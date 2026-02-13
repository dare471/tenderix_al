<?
define("STOP_STATISTICS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!CModule::IncludeModule("pweb.tenderix"))
    return;
IncludeModuleLangFile(__FILE__);
$result = "";

switch ($_REQUEST['action']) {
    case "addItem":
        $numProp = intval($_REQUEST["numProp"]);
        $newProp = intval($_REQUEST["newProp"]);
        $NEW_PROP_ID = "n" . $newProp;
        $unitArr = array();
        $rsUnit = CTenderixSprDetails::GetList($by = "s_c_sort", $order = "asc", $arFilter = Array("SPR_ID" => COption::GetOptionString("pweb.tenderix", "PW_TD_OPTIONS_SPR_UNIT")), $is_filtered);
        while ($arUnit = $rsUnit->GetNext()) {
            $unitArr[$arUnit["ID"]] = $arUnit["TITLE"];
        }
        ob_start();
        ?>
        <tr>
            <td align="center" width="5"><? echo $numProp ?></td>
            <td align="center" width="120">
                <input type="text" name="PROP_<?= $NEW_PROP_ID ?>_TITLE" value="" style="width: 98%" />
            </td>
            <td align="center" width="80">
                <input type="text" name="PROP_<?= $NEW_PROP_ID ?>_ADD_INFO" value="" style="width: 98%" />
            </td>
            <td align="center" width="20">
                <select name="PROP_<?= $NEW_PROP_ID ?>_UNIT_ID">
                    <? foreach ($unitArr as $unit_id => $unit_title): ?>
                        <option value="<?= $unit_id ?>"><?= $unit_title ?></option>
                    <? endforeach; ?>
                </select>
            </td>
            <td align="center" width="20">
                <input type="text" name="PROP_<?= $NEW_PROP_ID ?>_COUNT" value="" style="width: 98%" />
            </td>
            <td align="center" width="50">
                <input type="text" name="PROP_<?= $NEW_PROP_ID ?>_START_PRICE" value="" style="width: 98%" />
            </td>
            <td align="center" width="50">
                <input type="text" name="PROP_<?= $NEW_PROP_ID ?>_STEP_PRICE" value="" style="width: 98%" />
            </td>
            <td align="center" width="5">
            </td>
        </tr>
        <?
        $result = ob_get_clean();
        break;

    case "getSupplier":
        $arrSupplier = array();
        $rsSupplier = CTenderixUserSupplier::GetListUser(array("NAME_COMPANY" => "ASC"), array("NAME_COMPANY" => htmlspecialcharsEx($_REQUEST["nameCompany"])));
        while ($arSupplier = $rsSupplier->Fetch()) {
            $arrSupplier[] = array(
                "company" => $GLOBALS["APPLICATION"]->ConvertCharset($arSupplier["NAME_COMPANY"], SITE_CHARSET, "UTF-8"),
                "id" => $arSupplier["USER_ID"]
            );
        }
        $result = json_encode($arrSupplier);
}

echo $result;
