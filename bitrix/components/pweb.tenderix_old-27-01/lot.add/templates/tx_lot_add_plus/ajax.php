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
            <td align="center"><? echo $numProp ?></td>
            <td align="center">
	            <div class="form-group">
                	<input class="form-control input-sm" type="text" name="PROP_<?= $NEW_PROP_ID ?>_TITLE" value="" />
	            </div>
            </td>
            <td align="center">
                <div class="form-group">
                	<input class="form-control input-sm" type="text" name="PROP_<?= $NEW_PROP_ID ?>_ADD_INFO" value="" />
                </div>
            </td>
            <td align="center">
	            <div class="form-group">
                <select  class="form-control input-sm" name="PROP_<?= $NEW_PROP_ID ?>_UNIT_ID">
                    <? foreach ($unitArr as $unit_id => $unit_title): ?>
                        <option value="<?= $unit_id ?>"><?= $unit_title ?></option>
                    <? endforeach; ?>
                </select>
	            </div>
            </td>
            <td align="center">
	            <div class="form-group">
	                <input class="form-control input-sm" type="text" name="PROP_<?= $NEW_PROP_ID ?>_COUNT" value="" />
	            </div>
            </td>

            <td align="center">
	            <div class="form-group">
	                <input class="form-control input-sm" type="text" name="PROP_<?= $NEW_PROP_ID ?>_START_PRICE" value="" />
	            </div>
            </td>

		<?if ($_REQUEST["type"] == 'N' || $_REQUEST["type"] == 'P'):?>
            <td align="center">
	            <div class="form-group">
	                <input class="form-control input-sm" type="text" name="PROP_<?= $NEW_PROP_ID ?>_STEP_PRICE" value="" style="width: 98%" />
	            </div>
            </td>
		<?endif;?>
            <td align="center">
            </td>
        </tr>
        <?
        $result = ob_get_clean();
        break;

    case "getSupplier":
        $arrStatus = array();
        $rsStatus = CTenderixUserSupplierStatus::GetList($by = "", $order = "", array());
        while($arStatus = $rsStatus->Fetch()) {
            $arrStatus[$arStatus["ID"]] = $arStatus["TITLE"];
        }
        $arrSupplier = array();
        $rsSupplier = CTenderixUserSupplier::GetListUser(array("NAME_COMPANY" => "ASC"), array("NAME_COMPANY" => htmlspecialcharsEx($_REQUEST["nameCompany"])));
		
		$prop = array();
		if ($_REQUEST["prop1"] != "") {$prop["1"] = $_REQUEST["prop1"];}
		//if ($_REQUEST["prop3"] != "") {$prop["3"] = $_REQUEST["prop3"];}
		$status = $_REQUEST["status"];

		$user_itog = array();
		$sch = 0;
		foreach ($prop as $prop_key => $prop_val) {
			$user_mas = array();
			$user_mas2 = array(); // массив выбранных пользователей
			if ($prop_val != "" ) {$user_mas = CTenderixUserSupplier::GetPropertyStrValue($prop_key, $prop_val);}
			foreach ($user_mas[$prop_key] as $us_key => $us_val) {
				//if (!in_array($us_val["USER_ID"], $user_itog)) {$user_itog[] = $us_val["USER_ID"];}
				$user_mas2[] = $us_val["USER_ID"];
			}
			if ($sch > 0) {$user_itog = array_intersect($user_itog, $user_mas2);}
			else {$user_itog = $user_mas2;}
			$sch++;
		}

        while ($arSupplier = $rsSupplier->Fetch()) {
			// echo "<pre>";
			// print_r($arSupplier);
			// echo "</pre>";
			/*$asm = array();
			$rsProp2 = CTenderixUserSupplier::GetProperty($arSupplier["USER_ID"]);
			foreach ($rsProp2 as $idpr => $valpr) {
				$asm["props"][$idpr] = $valpr[0]["VALUE"];
			}*/
			$rsUser = CUser::GetByID($arSupplier['USER_ID']);
			$arSupplier['USER_INFO'] = $rsUser->Fetch();
			if (count($prop) > 0) {
				if ($status != "") {
					if ((in_array($arSupplier["USER_ID"], $user_itog)) && ($status == $arSupplier["STATUS"])) {
						$arrSupplier[] = array(
							"company" => $GLOBALS["APPLICATION"]->ConvertCharset("<b>".$arSupplier["NAME_COMPANY"]."</b> [".$arrStatus[$arSupplier["STATUS"]]."]", SITE_CHARSET, "UTF-8"),
							"company2" => $GLOBALS["APPLICATION"]->ConvertCharset("<b>".$arSupplier["NAME_COMPANY"]."</b> [".$arSupplier["USER_INFO"]["EMAIL"]."]", SITE_CHARSET, "UTF-8"),
							"id" => $arSupplier["USER_ID"],
							"email" => $arSupplier["USER_INFO"]["EMAIL"],
							//"props" => $asm["props"]
						);
					}
				} else {
					if (in_array($arSupplier["USER_ID"], $user_itog)) {
						$arrSupplier[] = array(
							"company" => $GLOBALS["APPLICATION"]->ConvertCharset("<b>".$arSupplier["NAME_COMPANY"]."</b> [".$arrStatus[$arSupplier["STATUS"]]."]", SITE_CHARSET, "UTF-8"),
							"company2" => $GLOBALS["APPLICATION"]->ConvertCharset("<b>".$arSupplier["NAME_COMPANY"]."</b> [".$arSupplier["USER_INFO"]["EMAIL"]."]", SITE_CHARSET, "UTF-8"),
							"id" => $arSupplier["USER_ID"],
							"email" => $arSupplier["USER_INFO"]["EMAIL"],
							//"props" => $asm["props"]
						);
					}
				}
			} else {
				if ($status != "") {
					if ($status == $arSupplier["STATUS"]) {
						$arrSupplier[] = array(
							"company" => $GLOBALS["APPLICATION"]->ConvertCharset("<b>".$arSupplier["NAME_COMPANY"]."</b> [".$arrStatus[$arSupplier["STATUS"]]."]", SITE_CHARSET, "UTF-8"),
							"company2" => $GLOBALS["APPLICATION"]->ConvertCharset("<b>".$arSupplier["NAME_COMPANY"]."</b> [".$arSupplier["USER_INFO"]["EMAIL"]."]", SITE_CHARSET, "UTF-8"),
							"id" => $arSupplier["USER_ID"],
							"email" => $arSupplier["USER_INFO"]["EMAIL"],
							//"props" => $asm["props"]
						);
					}
				} else {
					$arrSupplier[] = array(
						"company" => $GLOBALS["APPLICATION"]->ConvertCharset("<b>".$arSupplier["NAME_COMPANY"]."</b> [".$arrStatus[$arSupplier["STATUS"]]."]", SITE_CHARSET, "UTF-8"),
						"company2" => $GLOBALS["APPLICATION"]->ConvertCharset("<b>".$arSupplier["NAME_COMPANY"]."</b> [".$arSupplier["USER_INFO"]["EMAIL"]."]", SITE_CHARSET, "UTF-8"),
						"id" => $arSupplier["USER_ID"],
						"email" => $arSupplier["USER_INFO"]["EMAIL"],
						//"props" => $asm["props"]
					);
				}
			}
			//$arrSupplier[$supp]["props"] = $asm["props"];
			//$supp++;
        }

        $result = json_encode($arrSupplier);
}

echo $result;