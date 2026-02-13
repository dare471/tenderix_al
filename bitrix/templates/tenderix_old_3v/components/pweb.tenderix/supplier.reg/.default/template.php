<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?

//echo '<pre>'; print_r($arResult); echo '</pre>'; //["INFO"]["STATUS"];
$disabled = "";
if (($arResult["INFO"]["STATUS"] == 3) || ($arResult["INFO"]["STATUS"] == 4)) {
	$disabled = "";
}
?>
<div class="space-bottom"></div>
<div class="supplier-add">
	<? if (strlen($arResult["ERRORS"]) > 0): ?>
		<div class="errors-tender"><?= $arResult["ERRORS"] ?></div>
	<? endif; ?>
	<? if ($arResult["SEND_OK"] == "Y"): ?>
		<div class="send-ok-tender"><?= GetMessage("PW_TD_SEND_OK") ?></div>
	<? endif; ?>
	<form class="reg_form" name="reg_form" onSubmit="pre(); return false;" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
		<h3><?= GetMessage("PW_TD_STEP_1") ?></h3>
		<div class="alert alert-danger">
			Для получения возможности участвовать в конкурсных процедурах Вам необходимо заполнить обязательные поля (отмечены <span style="color:red;!important">*</span>) регистрационной формы «Профиль поставщика».<br />
Просим Вас корректно указывать все реквизиты (наименование, ИНН и пр.).

		</div>
		
		<table class="t_lot_table table table-striped table-hover table-condensed">
			<? if ($arResult["T_RIGHT"] == "P"): ?>
				<tr>
					<td class="reg-field" width="40%">Статус участника</td>
				<? if ($arResult["S_RIGHT"] == "A"): ?>
					<td class="" width="60%">
						<label class="t_status t_status_1">Новый участник</label>
					</td>
				<?else: ?>
					<td class="" width="60%">
						<label class="t_status t_status_<?=$arResult["INFO"]["STATUS"];?>"><?=$arResult["INFO"]["STATUS_NAME"];?></label>
					</td>					
				<?endif; ?>
				</tr>
			<? elseif ($arResult["T_RIGHT"] == "W"): ?>
			<tr>
				<td class="reg-field" width="40%">Статус</td>
				<td class="" width="60%">
					<label class="t_status t_status_admin">Организатор торгов</label>
				</td>
			</tr>
			<?endif; ?>
			<? if (in_array("LAST_NAME", $arParams["FIELDS"])): ?>
				<tr>
					<td class="reg-field" width="40%">
						<? if (in_array("LAST_NAME", $arParams["REG_FIELDS_REQUIRED"])): ?>
							<span class="required">*</span>
						<? endif; ?>
						<?= GetMessage("PW_TD_SUPPLIER_LAST_NAME") ?>:
					</td>
					<td width="60%">
						<div class="form-group">
						<input class="form-control input-sm" type="text" name="INFO[LAST_NAME]" value="<?= $arResult["INFO"]["LAST_NAME"] ?>" />
						</div>
					</td>
				</tr>
			<? endif; ?>
			<? if (in_array("NAME", $arParams["FIELDS"])): ?>
				<tr>
					<td class="reg-field" width="40%">
						<? if (in_array("NAME", $arParams["REG_FIELDS_REQUIRED"])): ?>
							<span class="required">*</span>
						<? endif; ?>
						<?= GetMessage("PW_TD_SUPPLIER_NAME") ?>:
					</td>
					<td width="60%">
						<div class="form-group">
						<input class="form-control input-sm" type="text" name="INFO[NAME]" value="<?= $arResult["INFO"]["NAME"] ?>" />
						</div>
					</td>
				</tr>
			<? endif; ?>
			<? if (in_array("SECOND_NAME", $arParams["FIELDS"])): ?>
				<tr>
					<td class="reg-field" width="40%">
						<? if (in_array("SECOND_NAME", $arParams["REG_FIELDS_REQUIRED"])): ?>
							<span class="required">*</span>
						<? endif; ?>
						<?= GetMessage("PW_TD_SUPPLIER_SECOND_NAME") ?>:
					</td>
					<td width="60%">
						<div class="form-group">
						<input class="form-control input-sm" type="text" name="INFO[SECOND_NAME]" value="<?= $arResult["INFO"]["SECOND_NAME"] ?>" />
						</div>
					</td>
				</tr>
			<? endif; ?>
			<tr>
				<td class="reg-field" width="40%"><span class="required">*</span><?= GetMessage("PW_TD_SUPPLIER_LOGIN") ?>:</td>
				<td width="60%"><?= $arResult["INFO"]["LOGIN"] ?></td>
			</tr>
			<tr>
				<td class="reg-field" width="40%"><span class="required">*</span><?= GetMessage("PW_TD_SUPPLIER_EMAIL") ?>:</td>
				<td width="60%">
					<div class="form-group">
					<input class="form-control input-sm" type="text" name="INFO[EMAIL]" value="<?= $arResult["INFO"]["EMAIL"] ?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<a class="" data-toggle="collapse" href="#changepassword" aria-expanded="false" aria-controls="#changepassword">
						Поменять пароль
					</a>
				</td>
			</tr>
			<div >
			<tr class="collapse" id="changepassword">
				<td class="reg-field" width="40%">
					<span class="required">*</span><?= GetMessage('PW_TD_SUPPLIER_PASSWORD') ?>: <br><br><br>
					<span class="required">*</span><?= GetMessage('PW_TD_SUPPLIER_PASSWORD_CONFIRM') ?>:
				</td>
				<td width="60%">
					<div class="form-group">
					<input class="form-control input-sm" type="password" name="INFO[PASSWORD]" value="" autocomplete="off" />
					</div>
					<br>
					<div class="form-group">
					<input class="form-control input-sm" type="password" name="INFO[PASSWORD_CONFIRM]" value="" autocomplete="off" />
					</div>
				</td>
			</tr>
			</div>
		</table>

		<h3><?= GetMessage("PW_TD_STEP_2") ?></h3>
		<table class="t_lot_table">
			<!--tr>
				<td class="reg-field" width="40%">
					<?= GetMessage("PW_TD_SUPPLIER_TYPE") ?>:
				</td>
				<td width="60%">
					<div class="form-group">
					<select class="form-control input-sm" onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;">
						<option<?= $arResult["TYPE"] == 0 ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE=0", array("TYPE")) ?>"><?= GetMessage("PW_TD_SUPPLIER_TYPE_VAL1") ?></option>
						<option<?= $arResult["TYPE"] == 1 ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE=1", array("TYPE")) ?>"><?= GetMessage("PW_TD_SUPPLIER_TYPE_VAL2") ?></option>
					</select>
					</div>
				</td>
			</tr-->
			<? if (in_array("NAME_COMPANY", $arParams["FIELDS"])): ?>
				<tr>
					<td class="reg-field" width="40%">
						<? if (in_array("NAME_COMPANY", $arParams["REG_FIELDS_REQUIRED"])): ?>
							<span class="required">*</span>
						<? endif; ?>
						<?= GetMessage("PW_TD_SUPPLIER_NAME_COMPANY") ?>:
					</td>
					<td width="60%">
						<div class="form-group">
						<input class="form-control input-sm" type="text" name="INFO[NAME_COMPANY]" value="<?= htmlspecialcharsEx($arResult["INFO"]["NAME_COMPANY"]) ?>" size="30" <?=$disabled;?> />
						</div>
					</td>
				</tr>
			<? endif; ?>
			<? if (in_array("NAME_DIRECTOR", $arParams["FIELDS"])): ?>
				<tr>
					<td class="reg-field" width="40%">
						<? if (in_array("NAME_DIRECTOR", $arParams["REG_FIELDS_REQUIRED"])): ?>
							<span class="required">*</span>
						<? endif; ?>
						<?= GetMessage("PW_TD_SUPPLIER_NAME_DIRECTOR") ?>:
					</td>
					<td width="60%">
						<div class="form-group">
						<input class="form-control input-sm" type="text" name="INFO[NAME_DIRECTOR]" value="<?= htmlspecialcharsEx($arResult["INFO"]["NAME_DIRECTOR"]) ?>" size="30" <?=$disabled;?> />
						</div>
					</td>
				</tr>
			<? endif; ?>
			<? if (in_array("NAME_ACCOUNTANT", $arParams["FIELDS"])): ?>
				<tr>
					<td class="reg-field" width="40%">
						<? if (in_array("NAME_ACCOUNTANT", $arParams["REG_FIELDS_REQUIRED"])): ?>
							<span class="required">*</span>
						<? endif; ?>
						<?= GetMessage("PW_TD_SUPPLIER_NAME_ACCOUNTANT") ?>:
					</td>
					<td width="60%">
						<div class="form-group">
						<input class="form-control input-sm" type="text" name="INFO[NAME_ACCOUNTANT]" value="<?= htmlspecialcharsEx($arResult["INFO"]["NAME_ACCOUNTANT"]) ?>" size="30" <?=$disabled;?> />
						</div>
					</td>
				</tr>
			<? endif; ?>

			<? if ($arParams["DOP_FIELDS_CODE_ACTIVE"] == "Y"): ?>    
				<tr class="heading">
					<td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_CODE") ?></b></td>
				</tr>

				<? if (in_array("CODE_INN", $arParams["FIELDS"])): ?>
					<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.3.1/jquery.maskedinput.min.js"></script>
					<tr>
						<td class="reg-field" width="40%">
							<? //if (in_array("CODE_INN", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? //endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_CODE_INN") ?>:
						</td>
						<td width="60%">
							<div class="form-group inn">
							<input class="form-control input-sm" type="text" name="INFO[CODE_INN]" value="<?= htmlspecialcharsEx($arResult["INFO"]["CODE_INN"]) ?>" size="30" <?=$disabled;?> />
							<div id="error_inn"></div>
							</div>
						</td>
					</tr>

<script>
$(document).ready(function(){
	$(".inn input.input-sm").keydown(function(event) {
		// Разрешаем: backspace, delete, tab и escape
		//alert(event.metaKey);
		var Value = $(this).val();
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || 
			 // Разрешаем: Ctrl+A Ctrl+V Ctrl+X
			(event.keyCode == 65 && (event.ctrlKey === true || event.metaKey === true)) ||
			(event.keyCode == 86 && (event.ctrlKey === true || event.metaKey === true)) ||
			(event.keyCode == 67 && (event.ctrlKey === true || event.metaKey === true)) ||
			(event.keyCode == 88 && (event.ctrlKey === true || event.metaKey === true)) ||
			 // Разрешаем: home, end, влево, вправо
			(event.keyCode >= 35 && event.keyCode <= 39)){
				 // Ничего не делаем
				 return;
		}
		else {
			// Обеждаемся, что это цифра, и останавливаем событие keypress
			if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )|| (Value.length > 11 )) {
				event.preventDefault();
			}
		}
	});
	$('.inn input.input-sm').keyup(function(){
		var Value = $(this).val();
		if(Value.length == 10 || Value.length == 12){
			$('.inn').removeClass('has-error');
			$('.inn').addClass('has-success');
			var inn = Value.split('');
			var sum = (inn[0]*2)+(inn[1]*4)+(inn[2]*10)+(inn[3]*3)+(inn[4]*5)+(inn[5]*9)+(inn[6]*4)+(inn[7]*6)+(inn[8]*8);
			var n = parseInt(sum/11);
			var valinn = 11*n + parseInt(inn[9]);
			if (valinn == sum) {
				$('#error_inn').empty().attr('style', 'color:green;');
				//$('#error_inn').text('ИНН верный');
			}else{
				$('#error_inn').empty().attr('style', 'color:red;');
				$('#error_inn').empty();
				//$('#error_inn').text('ИНН неверный');
			}
			if(Value.length === 0) {
				$('.inn').removeClass('has-error');
				$('#error_inn').empty();
			}
		}
		if(Value.length < 10 || Value.length == 11 || Value.length > 12){
			$('.inn').removeClass('has-success');
			$('.inn').addClass('has-error');
			$('#error_inn').empty();
			$('#error_inn').text('ИНН/БИК должен содержать 10 или 12 цифр ('+Value.length+')');
			if(Value.length > 12){
				Value = Value.substr(0,12);
				$(this).val(Value);
				$('.inn').removeClass('has-error');
				$('.inn').addClass('has-success');
				$('#error_inn').empty();
				$('#error_inn').text('Ваш ИНН -'+Value);
			}
		}


			// 							if(Value > 10){
			// 	$('#error_inn').attr('style', 'display:block;');
			// }else {
			// 	$('#error_inn').empty();
			// 	//$('#error_inn').text('Ваш ИНН - '+ Value);
			// }

		//$('#error_inn').empty();
		//$('#error_inn').text(Value);
	});


	// $(".inn input.input-sm").live(function(){
	// 	var inn = $(this).val();
	// 	//alert(this);
	// 	if (inn.length  < 10){
	// 		$(".inn").addClass('has-error');
	// 	}


	// 	// if (inn === '123'){
	// 	// 	inn_array = inn.split('');
	// 	// 	alert(inn.length);
	// 	// }else {
	// 	// 	alert(inn.length);
	// 	// }
		

	// });
	//$(".inn input.input-sm").mask("9999999999");
});
</script>
				<? endif; ?>
				<? if (in_array("CODE_KPP", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("CODE_KPP", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_CODE_KPP") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[CODE_KPP]" value="<?= htmlspecialcharsEx($arResult["INFO"]["CODE_KPP"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("CODE_OKVED", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("CODE_OKVED", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_CODE_OKVED") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[CODE_OKVED]" value="<?= htmlspecialcharsEx($arResult["INFO"]["CODE_OKVED"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("CODE_OKPO", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("CODE_OKPO", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_CODE_OKPO") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[CODE_OKPO]" value="<?= htmlspecialcharsEx($arResult["INFO"]["CODE_OKPO"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
			<? endif; ?>
			<? $ID = $arResult["INFO"]["USER_ID"] ?>
		<? foreach ($arResult["PROP"] as $k => $arPropList): ?>
				<tr> 
					<td>
						<? if ($arPropList["IS_REQUIRED"] == "Y"): ?>
							<span class="required">*</span>
						<? endif; ?>
						<b><?= $arPropList["TITLE"] ?></b>
					</td>
					<td>
						<? if (count($arPropList["FILE"]) > 0): ?>
							<table border="0" cellpadding="0" cellspacing="0" class="t_lot_table">
								<tr>
									<th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
									<th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
									<th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
								</tr>
								<? foreach ($arPropList["FILE"] as $arFile) : ?>
									<tr>
										<td><a href="/tx_files/supplier_file.php?USER_ID=<? echo $ID ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>&amp;PROPERTY=<? echo $arPropList["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
										<td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
										<td align="center">
											<input type="checkbox" name="FILE_ID_PROP[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
											<input type="hidden" name="PROP[<?= $arPropList["ID"] ?>][<?= $arFile["ID"] ?>]" />
										</td>
									</tr>
								<? endforeach; ?>
							</table>
						<? endif; ?>
						<?
						$result = "";
						if (strlen($arPropList["DEFAULT_VALUE"]) > 0 && $arPropList["MULTI"] == "Y") {
							$arPropList["MULTI_CNT"]++;
						}
						$cntProp = 0;
						if ($ID > 0 && $arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
							$cntProp = count($arResult["PROP_SUPPLIER"][$arPropList["ID"]]);
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

						$result .= '<div class="prop-elem" id="prop-' . $arPropList["ID"] . '">';
						for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
							$result .= '<div id="prop-' . $arPropList["ID"] . '-' . $i . '">';
							switch ($arPropList["PROPERTY_TYPE"]) {
								case "S":
								case "N":
									if ($i > 0 || $ID > 0) {
										$arPropList["DEFAULT_VALUE"] = "";
									}
									if ($ID > 0 && $i < $cntProp) {
										$propName = "PROP[" . $arPropList["ID"] . "][" . $arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"] . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["VALUE"]);
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
									if (count($arPropList["FILE"]) <= 0 || $arPropList["MULTI"] == "Y")
										$result .= '<input type="file" name="PROP[' . $arPropList["ID"] . '][n' . ($i - $cntProp) . ']" size="' . $arPropList["COL_COUNT"] . '" />';
									break;
								case "L":
									$arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
									if ($ID > 0) {
										foreach ($arResult["PROP_SUPPLIER"][$arPropList["ID"]] as $arrListSupplier) {
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
										$propName = "PROP[" . $arPropList["ID"] . "][" . $arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"] . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["VALUE"]);
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
										$propName = "PROP[" . $arPropList["ID"] . "][" . $arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"] . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) : (strlen($arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["VALUE"]), "FULL") : "");
									} else {
										$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : (strlen($arPropList["DEFAULT_VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropList["DEFAULT_VALUE"]), "FULL") : "");
									}
									$result .= '<input type="text" name="' . $propName . '" value="' . $propValue . '" size="20" />';
									ob_start();
									$APPLICATION->IncludeComponent(
											'bitrix:main.calendar', '', array(
										'SHOW_INPUT' => 'N',
										'FORM_NAME' => 'reg_form',
										'INPUT_NAME' => $propName,
										'INPUT_VALUE' => (strlen($propValue) > 0 ? $propValue : date("d.m.Y H:i:s")),
										'SHOW_TIME' => 'N',
										'HIDE_TIMEBAR' => 'N'
											), null, array('HIDE_ICONS' => 'Y')
									);
									$result .= ob_get_clean();
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
			<? endforeach; ?>



			<? if ($arParams["DOP_FIELDS_LEGALADDRESS_ACTIVE"] == "Y"): ?>   
				<tr class="heading">
					<td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_LEGALADDRESS") ?></b></td>
				</tr>
				<? if (in_array("LEGALADDRESS_REGION", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("LEGALADDRESS_REGION", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_REGION") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[LEGALADDRESS_REGION]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_REGION"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("LEGALADDRESS_CITY", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("LEGALADDRESS_CITY", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_CITY") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[LEGALADDRESS_CITY]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_CITY"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("LEGALADDRESS_INDEX", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("LEGALADDRESS_INDEX", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_INDEX") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[LEGALADDRESS_INDEX]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_INDEX"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("LEGALADDRESS_STREET", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("LEGALADDRESS_STREET", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_STREET") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[LEGALADDRESS_STREET]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_STREET"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("LEGALADDRESS_POST", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("LEGALADDRESS_POST", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_POST") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[LEGALADDRESS_POST]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_POST"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
			<? endif; ?>

			<? if ($arParams["DOP_FIELDS_POSTALADDRESS_ACTIVE"] == "Y"): ?>  
				<tr class="heading">
					<td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_POSTALADDRESS") ?></b></td>
				</tr>
				<? if (in_array("POSTALADDRESS_REGION", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("POSTALADDRESS_REGION", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_REGION") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[POSTALADDRESS_REGION]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_REGION"]) ?>" size="30" />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("POSTALADDRESS_CITY", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("POSTALADDRESS_CITY", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_CITY") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[POSTALADDRESS_CITY]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_CITY"]) ?>" size="30" />
							</div>
						</td>
					</tr> 
				<? endif; ?>
				<? if (in_array("POSTALADDRESS_INDEX", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("POSTALADDRESS_INDEX", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_INDEX") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[POSTALADDRESS_INDEX]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_INDEX"]) ?>" size="30" />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("POSTALADDRESS_STREET", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("POSTALADDRESS_STREET", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_STREET") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[POSTALADDRESS_STREET]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_STREET"]) ?>" size="30" />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("POSTALADDRESS_POST", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("POSTALADDRESS_POST", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_POST") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[POSTALADDRESS_POST]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_POST"]) ?>" size="30" />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("PHONE", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("PHONE", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_PHONE") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[PHONE]" value="<?= htmlspecialcharsEx($arResult["INFO"]["PHONE"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("FAX", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("FAX", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_FAX") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[FAX]" value="<?= htmlspecialcharsEx($arResult["INFO"]["FAX"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
			<? endif; ?>

			<? if ($arParams["DOP_FIELDS_STATEREG_ACTIVE"] == "Y"): ?>
				<tr class="heading">
					<td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_STATEREG") ?></b></td>
				</tr>
				<? if (in_array("STATEREG_PLACE", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("STATEREG_PLACE", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_STATEREG_PLACE") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[STATEREG_PLACE]" value="<?= htmlspecialcharsEx($arResult["INFO"]["STATEREG_PLACE"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("STATEREG_DATE", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("STATEREG_DATE", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_STATEREG_DATE") ?>:
						</td>
						<td width="60%">
							<div class="form-group form-date">
							<input class="form-control" type="text" name="INFO[STATEREG_DATE]" value="<?= $arResult["INFO"]["STATEREG_DATE"] ?>" class="valid" readonly size="20" <?=$disabled;?> />
							
							<?
							$APPLICATION->IncludeComponent(
									'bitrix:main.calendar', '', array(
								'SHOW_INPUT' => 'N',
								'FORM_NAME' => 'reg_form',
								'INPUT_NAME' => 'INFO[STATEREG_DATE]',
								'INPUT_VALUE' => $arResult["INFO"]["STATEREG_DATE"],
								'SHOW_TIME' => 'N',
								'HIDE_TIMEBAR' => 'Y'
									), null, array('HIDE_ICONS' => 'Y')
							);
							?>
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("STATEREG_NDS", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("STATEREG_NDS", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_STATEREG_NDS") ?>:
						</td>
						<td width="60%">
						<div class="form-group">
							<select class="form-control input-sm" name="INFO[STATEREG_NDS]" >
								<? $statereg_nds = htmlspecialcharsEx($arResult["INFO"]["STATEREG_NDS"]);?>
								<option<?= ($statereg_nds != 'Y') && ($statereg_nds != 'N')? " selected" : ""; ?> value=""></option>								
								<option<?= $statereg_nds == 'Y' ? " selected" : ""; ?> value="Y"><?= GetMessage("PW_TD_SUPPLIER_STATEREG_NDS_YES") ?></option>
								<option<?= $statereg_nds == 'N' ? " selected" : ""; ?> value="N"><?= GetMessage("PW_TD_SUPPLIER_STATEREG_NDS_NO") ?></option>
							</select>
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("STATEREG_OGRN", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("STATEREG_OGRN", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_STATEREG_OGRN") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[STATEREG_OGRN]" value="<?= htmlspecialcharsEx($arResult["INFO"]["STATEREG_OGRN"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
			<? endif; ?>

			<? if ($arParams["DOP_FIELDS_BANK_ACTIVE"] == "Y"): ?>
				<tr class="heading">
					<td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_BANK") ?></b></td>
				</tr>
				<? if (in_array("BANKING_NAME", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("v", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_BANKING_NAME") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[BANKING_NAME]" value="<?= htmlspecialcharsEx($arResult["INFO"]["BANKING_NAME"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("BANKING_ACCOUNT", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("BANKING_ACCOUNT", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNT") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[BANKING_ACCOUNT]" value="<?= htmlspecialcharsEx($arResult["INFO"]["BANKING_ACCOUNT"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("BANKING_ACCOUNTCORR", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("BANKING_ACCOUNTCORR", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNTCORR") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[BANKING_ACCOUNTCORR]" value="<?= htmlspecialcharsEx($arResult["INFO"]["BANKING_ACCOUNTCORR"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
				<? if (in_array("BANKING_BIK", $arParams["FIELDS"])): ?>
					<tr>
						<td class="reg-field" width="40%">
							<? if (in_array("BANKING_BIK", $arParams["REG_FIELDS_REQUIRED"])): ?>
								<span class="required">*</span>
							<? endif; ?>
							<?= GetMessage("PW_TD_SUPPLIER_BANKING_BIK") ?>:
						</td>
						<td width="60%">
							<div class="form-group">
							<input class="form-control input-sm" type="text" name="INFO[BANKING_BIK]" value="<?= htmlspecialcharsEx($arResult["INFO"]["BANKING_BIK"]) ?>" size="30" <?=$disabled;?> />
							</div>
						</td>
					</tr>
				<? endif; ?>
			<? endif; ?>

			<?
							// получение разделов, доступных пользователю - начало vvvvvvvvvv
							$sec_filtr = array();
							$rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", array("ACTIVE" => "Y"), $is_filtered = false);
							while ($arSection = $rsSection->Fetch()) {
								if ($arSection["GROUP_ARR"] != "") {$sec_filtr["SECTION_GROUP"][$arSection["ID"]] = explode(",", $arSection["GROUP_ARR"]);}
								else {$sec_filtr["SECTION_GROUP"][$arSection["ID"]] = array();}
							}
							$user_get_id = $USER->GetID();
							$group_users = CUser::GetUserGroup($user_get_id);
							$mas_sections = array();
							foreach ($group_users as $usgr_key => $usgr_val) {
								foreach ($sec_filtr["SECTION_GROUP"] as $sec_key => $sec_val) {
									if (!empty($sec_val)) {
										if (in_array($usgr_val, $sec_val)) $mas_sections[] = $sec_key;
									} else {
									$mas_sections[] = $sec_key;
									}
								}
							}
							$mas_sections = array_unique($mas_sections);

							/*echo "<pre>";
							print_r($arFilter);
							echo "</pre>";*/
							// получение разделов, доступных пользователю - конец ^^^^^^^^^^^^
			?>
			
			
			<? /*if ($arParams["DOP_FIELDS_DIRECTION_ACTIVE"] == "Y"): ?>
				<tr class="heading">
					<td colspan="2"><b><? echo GetMessage("PW_TD_DIRECTION_SUPPLIER") ?></b></td>
				</tr>
				<tr>
					<td class="reg-field" width="40%">&nbsp;</td>
					<td width="60%" class="input-checked">
						<?
						
						
						
						foreach ($arResult["SECTION"] as $arSectionID => $arSectionTITLE):
						  if (in_array($arSectionID, $mas_sections)) {
							$checked = in_array($arSectionID, $arResult["INFO"]["DIRECTION"]) ? " checked" : "";
							?>
							<input<?= $checked ?> type="checkbox" value="<?= $arSectionID ?>" name="INFO[DIRECTION][]" /> <?= $arSectionTITLE ?> <br />
						  <? } ?>
						<? endforeach; ?>
					</td>
				</tr>
			<? endif; */?>

			<? //property  ?>
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
			<? $ID = $arResult["INFO"]["USER_ID"] ?>
	
		</table>

		<? if ($arParams["DOP_FIELDS_SUBSCRIBE_ACTIVE"] == "Y" || $arParams["DOP_FIELDS_DOCUMENT_ACTIVE"] == "Y"): ?>
			<h3><?= GetMessage("PW_TD_STEP_3") ?></h3>
			<table class="t_lot_table work-checked">
				<? if ($arParams["DOP_FIELDS_SUBSCRIBE_ACTIVE"] == "Y"): ?>
					<tr class="heading">
						<td colspan="2"><b><? echo GetMessage("PW_TD_SUBSCRIBE_SUPPLIER") ?> <span style="color:red;">*</span></b></td>
					</tr>
					<tr>
						<td class="reg-field" width="40%">
							В направлениях работы необходимо указать только те направления деятельности, 
							в рамках которых Вы готовы принимать участие в конкурсных процедурах в качестве поставщика (подрядчика, исполнителя).
						</td>
						<td width="60%" class="input-checked">
							<?
							foreach ($arResult["SECTION"] as $arSectionID => $arSectionTITLE):
							  if (in_array($arSectionID, $mas_sections)) {
								$checked = in_array($arSectionID, $arResult["INFO"]["SUBSCRIBE"]) ? " checked" : "";
								?>
								<input<?= $checked ?> type="checkbox" value="<?= $arSectionID ?>" name="INFO[SUBSCRIBE][]" /> <?= $arSectionTITLE ?> <br />
							  <? } ?>
							<? endforeach; ?>

						</td>
					</tr>
				<? endif; ?>
				<? if ($arParams["DOP_FIELDS_DOCUMENT_ACTIVE"] == "Y"): ?>
					<tr class="heading">
						<td colspan="2"><b><? echo GetMessage("PW_TD_DOCUMENT") ?></b></td>
					</tr>
					<? if (count($arResult["INFO"]["FILE"]) > 0): ?>
						<tr>
							<td valign="top"><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:</td>
							<td>
								<table class="t_lot_table">
									<tr>
										<th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
										<th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
										<th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
									</tr>
									<? foreach ($arResult["INFO"]["FILE"] as $arFile) : ?>
										<tr>
											<td><a href="/tx_files/supplier_file.php?USER_ID=<? echo $ID ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
											<td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
											<td align="center">
												<input type="checkbox" name="FILE_ID[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
											</td>
										</tr>
									<? endforeach; ?>
								</table>
							</td>
						</tr>
					<? endif; ?>
					<tr>
						<td class="reg-field" width="40%">
						<p>
							В обязательном порядке необходимо прикрепить копии следующих документов: <br>
							<ul style="color:red;">
								<li>Свидетельства о государственной регистрации компании.</li>
								<li>Свидетельства о постановке на учет в налоговом органе</li>
								<li>Свидетельства о постановке на учет по НДС <br>(для плательщиков НДС)</li>
							</ul>
Вы также можете прикрепить краткую презентацию о деятельности компании. <br>
Более широкий перечень документов может быть запрошен в рамках каждой отдельной конкурсной процедуры.
						</p>
							

						</td>
						<td width="60%" class="file_upload">
							<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
							<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
							<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
							<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
							<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
						</td>
					</tr>
				<? endif; ?>
			</table>
		<? endif; ?>
		<input type="checkbox" id="kontur_agree" name="INFO[AGREE]" value="" <? echo ($arResult["INFO"]["AGREE"] == "Y" ? "checked" : "") ?> /> Я согласен(-на) на обработку моих персональных данных. <a href="/" tareget="_blank">Политика конфиденциальности</a> <br/>
		<script>
			$('#kontur_agree').click(function(){
				if($('#kontur_agree').prop("checked")) {
					$('#kontur_agree').val("Y");
					$('input[name="reg_submit"]').attr("disabled", false);
				}
				else {
					$('#kontur_agree').val("N");
					$('input[name="reg_submit"]').attr("disabled", true);
				}
			});
		</script>
		<? if(isset($arResult["INFO"]["STATUS_ID"]) && ($arResult["INFO"]["STATUS_ID"] == 6)) { ?>
			<button style="color:#fff!important;" class="btn_submit btn" name="reg_submit_yet"><?=GetMessage("PW_TD_FORM_REG_YET"); ?></button>
		<? } else {?>
			<input type="submit" style="color:#fff!important;" class="btn_submit btn" name="reg_submit" <? echo ($arResult["INFO"]["AGREE"] == "Y" ? "" : "disabled") ?> value="<?= ((in_array($arResult["INFO"]["STATUS_ID"], array(2,3,4))) ? GetMessage("PW_TD_FORM_SAVE") : GetMessage("PW_TD_FORM_REG")); ?>" />
		<? } ?>
	</form>
</div>
							<script>
							function pre(){
								var checked_set = new Array();
								$(":checkbox[name='INFO[SUBSCRIBE][]']:checked").each(function(){
									checked_set.push(this.id);
								});
								if (checked_set.length != 0) {
									$(".reg_form").submit();
									//return true;
								}else {
									alert('Вам необходимо выбрать направление работы!');
								}
							}
							</script>
