<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
	$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/colorbox2/jquery.colorbox-min.js"></script>', true);
	$APPLICATION->AddHeadString('<link href="/bitrix/js/pweb.tenderix/colorbox2/colorbox.css" type="text/css" rel="stylesheet">', true);

	//echo "<pre>";print_r($arResult);echo "</pre>";
	$date_start = strtotime($arResult["LOT"]["DATE_START"]);
	$date_end = strtotime($arResult["LOT"]["DATE_END"]);
	$date_tek = time();
	$dis_arch = "";
?>

<?if(($arResult["LOT"]["ACTIVE"] == "Y") && ($arResult["LOT"]["NOEDIT"] == "Y") && ($arResult["COPY"] == "N") && ($date_tek > $date_end) && ($date_end != 0)) :?>
	<br />
	<br />
	<div class="well">
	<h4 style="color:red">Этот лот запретили редактировать!</h4>
	</div>
	<?$dis_arch = " disabled";?>
<?elseif(($arResult["LOT"]["ACTIVE"] == "Y") && ($arResult["COPY"] == "N") && ($date_tek < $date_end) && ($date_tek > $date_start) && ($date_end != 0) && ($date_start != 0)): ?>
	<br />
	<br />
	<div class="well">
	<h4 style="color:red">Этот лот еще не завершен и его нельзя редактировать!</h4>
	</div>
	<?$dis_arch = " disabled";?>
<?else: ?>
<script type="text/javascript">
$(function () {
	arrItem();
	$("#addItem").click(function () {
		arrItem();
	});
	$("#supplier-count").html(" (" + $("select[id=select-private-supplier] option").size() + ")");

	$("#supplier-view").colorbox({
		inline: true,
		href: "#supplier-block",
		opacity: 0.5,
		width: "900px",
		maxHheight: "80%",
		top: "10%",
		onClosed: function () {
			$("#select-private-supplier option").each(function () {
				this.selected = true;
			});
			$("#supplier-count").html(" (" + $("select[id=select-private-supplier] option").size() + ")");
		},
		onOpen: function () {
			$('#select-private-supplier option:selected').each(function () {
				this.selected = false;
			});
		}
	});
	$('#button-select-add-supplier').click(function() {
		var select_val = '';
		var select_html = '';
		$('#select-all-supplier').find('option').each(function() {
			select_val = $(this).val();
			select_html = $(this).html();
			var select_true = false;
			if ($(this).prop('selected') ) {
				$('#select-private-supplier').find('option').each(function () {
					if ($(this).val() == select_val) select_true = true;
				});
				if (!select_true) {
					$('#select-private-supplier').append('<option value="' + select_val + '">' + select_html + '</option>');
				}
			}
		});
	});
	$("#button-select-del-supplier").click(function () {
		$('#select-private-supplier option:selected').each(function () {
			$(this).remove();
		});
	});

	$("#private").click(function () {
		var supplier_cnt = $("select[id=select-all-supplier] option").size();
		if ($(this).is(":checked") && supplier_cnt <= 0) {
			$.ajax({
				url: "<?= $templateFolder ?>/ajax.php",
				type: "POST",
				data: "action=getSupplier",
				dataType: "json",
				beforeSend: function () {
					$("#supplier-view-load").show();
				},
				success: function (data) {
					for (var i = 0; i < data.length; i++) {
						$("#select-all-supplier").append('<option value="' + data[i].id + '">' + data[i].company + '</option>');
					}
					$("#supplier-view-load").hide();
					$("#supplier-view").show();
				}
			});
		}
		if ($(this).is(":checked") && supplier_cnt > 0) {
			$("#supplier-view").show();
		} else {
			$("#supplier-view").hide();
		}
	});


	$('#searchSupplier').keyup(function () {

		var n = "0";

		if ($('#searchSupplier').val().length >= 0) {
			$('#select-all-supplier').empty();
			var search22 = $('#searchSupplier').val();
			var search44 = Number(search22);

			var status = $('#searchStatus').val();

			var props = "";
			<?
			$svvProp = array();
			$rsvProp = CTenderixUserSupplierProperty::GetList($by = "", $order = "", array());
			while ($svProp = $rsvProp->Fetch()) { ?>
			var prop<?=$svProp["ID"];?> = $('#searchProp<?=$svProp["ID"];?>').val();
			//props .= "&prop<?=$svProp["ID"];?>= "+$('#searchProp<?=$svProp["ID"];?>').val();
			<?
			}
			?>

			if ((search44 != search22) && ($('#searchSupplier').val().length >= 3)) {
				cifr22 = '0';
			}
			else {
				cifr22 = '1';
			}


			$.ajax({
				url: "<?= $templateFolder ?>/ajax.php",
				type: "POST",
				data: "action=getSupplier&nameCompany=" + search22 + "&status=" + status,
				dataType: "json",
				success: function (data) {
					window.n = '0';
					window.n = data.length;
					//alert(window.n);
					$("#supplier-view-load").hide();
					$("#supplier-view").show();
					if (window.n == 0) {
						$("span.results").empty();
						$("span.results").fadeIn().append("Ничего не найдено");
					} else {
						$("span.results").empty();
						$("span.results").fadeIn().append("<b>Результатов:</b> " + window.n + ".");
					}
					$('#select-all-supplier').empty();
					for (var i = 0; i < data.length; i++) {
						$("#select-all-supplier").append('<option value="' + data[i].id + '" selected>' + data[i].company + '</option>');
					}
				}
			});

			return false;
		}
		if ($('#searchSupplier').val().length == 0) {
			$("span.results").empty();
		}

	});
	
});

function arrItem() {
	var numProp = $("#numProp").val();
	var newProp = $("#newProp").val();
	$.ajax({
		url: "<?= $templateFolder ?>/ajax.php",
		type: "POST",
		data: "action=addItem&numProp=" + numProp + "&newProp=" + newProp,
		beforeSend: function () {
			$("#addItem").attr("disabled", true);
		},
		success: function (data) {
			$("#numProp").val(parseInt(numProp) + 1);
			$("#newProp").val(parseInt(newProp) + 1);
			$("#table_spec").append(data);
			$("#addItem").attr("disabled", false);
		}
	});
}
</script>

<div class="lot-add">

	<? if (strlen($arResult["ERRORS"]) > 0): ?>
		<div class="errors-tender"><?= $arResult["ERRORS"] ?></div>
	<? endif; ?>
	
	<form name="lotadd_form" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
		<h3><?= GetMessage("PW_TD_BASE_TITLE") ?></h3>
		<div class="container">
			<div class="row">
				<input class="btn btn-primary" type="submit" name="lotadd_submit" value="<?= GetMessage("PW_TD_ADD_LOT") ?>"<?= $dis_arch; ?> />&nbsp;
				<? if ($T_RIGHT == 'W'): ?>
					<?if($arResult['LOT']['ACTIVE'] != 'Y'):?>
					<input class="btn btn-primary" type="submit" name="lotopen_submit" value="<?= GetMessage("PW_TD_OPEN_LOT") ?>"<?= $dis_arch; ?> />
					<?endif;?>
					<?if(isset($arResult['LOT']['ID'])):?>
						<input class="btn btn-primary" type="submit" name="lotarch_submit" value="Перенести в архив"<?= $dis_arch; ?> />
					<?endif;?>
				<?endif;?>
			</div>
		</div>
		<br/>
		
		<table class="table table-striped table-bordered table-hover table-condensed">
			<? if ($arResult["LOT"]["ID"] > 0 && $arResult["COPY"] == "N"): ?>
				<tr>
					<td width="40%" class="left-col"><?= GetMessage("PW_TD_NUM_LOT") ?></td>
					<td width="60%" class="right-col"><? echo $arResult["LOT"]["ID"] ?></td>
				</tr>
			<? endif; ?>
			<? if ($arResult["LOT"]["ACTIVE"] == "Y" && $arResult["LOT"]["ID"] > 0 && $arResult["COPY"] == "N"): ?>
				<tr>
					<td width="40%" class="left-col">
						<?= GetMessage("PW_TD_ACTIVE") ?>:
					</td>
					<td width="60%" class="right-col">
						<input type="checkbox" name="ACTIVE" value="Y" <?if ($arResult["LOT"]["ACTIVE"] == "Y") echo " checked"; ?> <?= $dis_arch; ?> />
					</td>
				</tr>
			<? endif; ?>
<!-- 	                ТИП ЛОТА -->
			<? if ($arResult["LOT"]["ID"] <= 0 && $arResult["COPY"] == "N"): ?>
				<tr>
					<td width="40%" class="left-col">
						<span class="required">*</span><?= GetMessage("PW_TD_TYPE_PRODUCT") ?>:
					</td>
					<td width="60%" class="right-col">
						<div class="form-group">
							<select class="form-control input-sm" 
								onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;" <?= (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "") ?> <?= $dis_arch; ?>>
								<option<?= $arResult["TYPE_ID"] == "N" ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=N", array("TYPE_ID", "SECTION_ID", "PRODUCTS_ID")) ?>"><?= GetMessage("PW_TD_TYPE_PRODUCT_NST") ?></option>
-								<option<?= $arResult["TYPE_ID"] == "S" ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=S", array("TYPE_ID", "SECTION_ID", "PRODUCTS_ID")) ?>"><?= GetMessage("PW_TD_TYPE_PRODUCT_ST") ?></option>
-								<option<?= $arResult["TYPE_ID"] == "P" ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=P", array("TYPE_ID", "SECTION_ID", "PRODUCTS_ID")) ?>"><?= GetMessage("PW_TD_TYPE_SALE") ?></option>
 								<option<?= $arResult["TYPE_ID"] == "T" ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=T", array("TYPE_ID", "SECTION_ID", "PRODUCTS_ID")) ?>"><?= GetMessage("PW_TD_TYPE_REQUEST_NST") ?></option>
-								<option<?= $arResult["TYPE_ID"] == "R" ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=R", array("TYPE_ID", "SECTION_ID", "PRODUCTS_ID")) ?>"><?= GetMessage("PW_TD_TYPE_REQUEST_ST") ?></option>
							</select>
						</div>
						<input type="hidden" name="TYPE_ID" value="<?= $arResult["TYPE_ID"] ?>"/>
					</td>
				</tr>
			<? else: ?>
				<input type="hidden" name="TYPE_ID" value="<?= $arResult["TYPE_ID"] ?>"/>
			<? endif; ?>
			<? if ($arResult["TYPE_ID"] == 'T' || $arResult["TYPE_ID"] == 'R'): ?>
				<tr>
					<td width="40%" class="left-col">Запрос котировок
					</td>
					<td width="60%" class="right-col">
						<input type="checkbox" name="QUOTES" value="Y"<?= $arResult["LOT"]["QUOTES"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
					</td>
				</tr>
			<?endif;?>
<!-- 	                РАЗДЕЛ ЛОТА -->
			<tr>
				<td width="40%" class="left-col">
					<span class="required">*</span><?= GetMessage("PW_TD_SECTION_NAME") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
					<?if(isset($arResult['LOT']['ID'])):?>
						<?=$arResult['LOT']['SECTION'] ?>
						<input type="hidden" name="SECTION_ID" value="<?=$arResult['LOT']['SECTION_ID'] ?>"/>
					<?else:?>
						<? if ($arResult["TYPE_ID"] == 'S' || $arResult["TYPE_ID"] == 'R'): ?>
							<select class="form-control input-sm" name="SECTION_ID" <?= $dis_arch; ?> onchange="if(this[this.selectedIndex].value!='') window.location='<?= $APPLICATION->GetCurPageParam("", array("SECTION_ID", "PRODUCTS_ID")) ?>&SECTION_ID='+ this[this.selectedIndex].value " <?= (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "") ?>>
						<?else:?>
							<select class="form-control input-sm" name="SECTION_ID" <?= $dis_arch; ?>>
						<?endif;?>
							<option value="">--</option>
								<? foreach ($arResult["SECTION_ARR"][0] as $sec): ?>
								<option<?= $_GET["SECTION_ID"] == $sec["ID"] ? " selected" : ""; ?> value="<?= $sec["ID"] ?>"><?= $sec["TITLE"] ?></option>
								<? endforeach; ?>
							<? foreach ($arResult["CATALOG"] as $cat_id => $cat_name) : ?>
								<optgroup label="<?= $cat_name ?>">
									<? foreach ($arResult["SECTION_ARR"][$cat_id] as $sec): ?>
										<option<?= $arResult["LOT"]["SECTION_ID"] == $sec["ID"] ? " selected" : ""; ?> value="<?= $sec["ID"] ?>"><?= $sec["TITLE"] ?></option>
									<? endforeach; ?>
								</optgroup>
							<? endforeach; ?>
						</select>
					<?endif;?>
					</div>
				</td>
			</tr>
<!-- НАИМЕНОВАНИЕ ТОВАРА -->            
			<? if ($arResult["TYPE_ID"] == 'S' || $arResult["TYPE_ID"] == 'R'): ?>
				<?if ($_GET['SECTION_ID']):?>
				<tr>
					<td width="40%" class="left-col">
						<span class="required">*</span><?= GetMessage("PW_TD_LIST_PRODUCT") ?>:
					</td>
					<td width="60%" class="right-col">
						<?
						$arProd = isset($arResult["PRODUCTS_ID"]) ? $arResult["PRODUCTS_ID"] : array();
						$arProd = isset($arResult["PRODUCTS_ID2"]) ? $arResult["PRODUCTS_ID2"] : $arProd;
						$arProdCnt2 = count($arProd);
						$arProdCnt = 0;
						?>
						<? while ($arProdCnt2 >= $arProdCnt) { ?>
							<div class="form-group">
								<select class="form-control input-sm" <?= $arResult["LOT"]["ID"] > 0 || $arResult["COPY"] == "Y" ? " disabled" : ""; ?> onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;" <?= (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "") ?>>
									<? foreach ($arResult["PRODUCTS"] as $products_id => $products_title): ?>
										<option<?= $products_id == $arProd[$arProdCnt] ? " selected" : ""; ?> value="<?= nfGetCurPageParam("PRODUCTS_ID[" . $arProdCnt . "]=" . $products_id, array(array("PRODUCTS_ID", $arProdCnt))) ?>"><?= $products_title ?></option>
									<? endforeach; ?>
								</select>
								</div>
							<input type="hidden" value="<?= $arProd[$arProdCnt] ?>" name="PRODUCTS_ID[<?= $arProdCnt ?>]"/>
							<? $arProdCnt++; ?>
						<? } ?>
					</td>
				</tr>
				<?endif;?>
			<? endif; ?>

 <!--                   Название ЛОТА -->           
			<tr>
				<td width="40%" class="left-col">
					<span class="required">*</span><?= GetMessage("PW_TD_TITLE") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="TITLE" value="<?= htmlspecialcharsEx($arResult["LOT"]["TITLE"]) ?>" size="50" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>
<!-- НАИМЕНОВАНИЕ ТОВАРА СПЕЦИФИКАЦИЯ -->
			<?if (($arResult["TYPE_ID"] != 'S') && ($arResult["TYPE_ID"] != 'R')): ?>
				<? if ($arResult["TYPE_ID"] == 'P'): ?>
					<input type="hidden" name="NOT_ANALOG" value="Y"/>
				<? endif; ?>
				<? if ($arResult["TYPE_ID"] == 'N' || $arResult["TYPE_ID"] == 'T'): ?>
					<tr>
						<td width="40%" class="left-col">
							<?= GetMessage("PW_TD_FULL_SPEC") ?>:
						</td>
						<td width="60%" class="right-col">
							<input type="checkbox" name="FULL_SPEC" value="Y"<?= $arResult["LOT"]["FULL_SPEC"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
						</td>
					</tr>
					<tr>
						<td width="40%" class="left-col">
							<?= GetMessage("PW_TD_NOT_ANALOG") ?>:
						</td>
						<td width="60%" class="right-col">
							<input type="checkbox" name="NOT_ANALOG" value="Y"<?= $arResult["LOT"]["NOT_ANALOG"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
						</td>
					</tr>
				<? endif; ?>
			<? endif; ?>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_OPEN_PRICE") ?>:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="OPEN_PRICE" value="Y"<?= $arResult["LOT"]["OPEN_PRICE"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
				</td>
			</tr>
			
			<?if($arResult['TYPE_ID'] != 'R' && $arResult['TYPE_ID'] != 'T' ):?>
			<tr>
				<td width="40%" class="left-col">
					Запрещать поставщикам устанавливать равные ставки:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="NOSAME" value="Y"<?= $arResult["LOT"]["NOSAME"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
				</td>
			</tr>
			
			<tr>
				<td width="40%" class="left-col">
					Запрещать поставщикам устанавливать ставки хуже лидирующей:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="NOBAD" value="Y"<?= $arResult["LOT"]["NOBAD"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
				</td>
			</tr>
			
			<tr>
				<td width="40%" class="left-col">
					Запрещать поставщикам устанавливать ставки хуже начальной:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="ONLY_BEST" value="Y"<?= $arResult["LOT"]["ONLY_BEST"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
				</td>
			</tr>
			<?endif;?>
			
			<tr>
				<td width="40%" class="left-col">
					Рассылка со спецификацией по окончанию торгов:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="SEND_SPEC" value="Y"<?= $arResult["LOT"]["SEND_SPEC"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
				</td>
			</tr>
			
			<tr>
				<td width="40%" class="left-col">
					Показывать контрагентам историю торгов после закрытия лота:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="VIZ_HIST" value="Y"<?= $arResult["LOT"]["VIZ_HIST"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
				</td>
			</tr>
			
			<tr>
				<td width="40%" class="left-col">
					Возможность делать ставки до открытия торгов:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="PRE_PROPOSAL" value="Y"<?= $arResult["LOT"]["PRE_PROPOSAL"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
				</td>
			</tr>
			
			<tr>
				<td width="40%" class="left-col">
					Запретить редактирование и удаление лота после завершения:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="NOEDIT" value="Y"<?= $arResult["LOT"]["NOEDIT"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
				</td>
			</tr>

			<? if ($arResult["TYPE_ID"] != 'P'): ?>
				<? if ($arResult["T_RIGHT"] == "W" || ($arResult["T_RIGHT"] == "S" && ($arResult["LOT"]["ID"] == 0 && $arResult["COPY"] == "N")) || $arResult["LOT"]["NOTVISIBLE_PROPOSAL"] == "N"): ?>
					<tr>
						<td width="40%" class="left-col">
							<?= GetMessage("PW_TD_NOTVISIBLE_PROPOSAL") ?>:
						</td>
						<td width="60%" class="right-col">
							<input type="checkbox" name="NOTVISIBLE_PROPOSAL" value="Y"<?= $arResult["LOT"]["NOTVISIBLE_PROPOSAL"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />
						</td>
					</tr>
				<? endif; ?>
				<tr>
					<td width="40%" class="left-col">
						<?= GetMessage("PW_TD_PRIVATE") ?>:
					</td>
					<td width="60%" class="right-col">
						<input type="checkbox" id="private" name="PRIVATE" value="Y"<?= $arResult["LOT"]["PRIVATE"] == "Y" ? " checked" : ""; ?> <?= $dis_arch; ?> />&nbsp;&nbsp;
						<a id="supplier-view" href="#"<?= $arResult["LOT"]["PRIVATE"] == "Y" ? '' : ' style="display:none;"'; ?>><?= GetMessage("PW_TD_PRIVATE_USER_SELECTED") ?>
							<span id="supplier-count"></span>
						</a>
						<span id="supplier-view-load" style="display:none;"><?= GetMessage("PW_TD_PRIVATE_USER_LOAD") ?></span>

						<div style="display:none;">
							<div id="supplier-block">
								<h3><?= GetMessage("PW_TD_PRIVATE_USER") ?></h3>
								<br/>
								<table class="table search_sup" style="border:none;" >
									<tr>
										<td>
											<label>Поиск: </label>
										</td>
										<td>
											<div class="form-group">
												<input class="form-control input-sm" type="text" id="searchSupplier" value="" name="search">
												<span class="results"></span>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label>Статус: </label>
										</td>
										<td>
											<div class='form-group'>
												<select class="form-control" name="searchStatus" id="searchStatus">
													<option value="">Выбрать статус</option>
														<?
														$arrStatus = array();
														$rsStatus = CTenderixUserSupplierStatus::GetList($by = "", $order = "asc", array());
														while ($arStatus = $rsStatus->Fetch()) {?>
															<option value='<?=$arStatus[ID];?>'><?=$arStatus[TITLE];?></option>
														<?}?>
												</select>
											</div>
										</td>
									</tr>
								</table>
								
								<table class="table">
									<tr>
										<td><h4><?= GetMessage("PW_TD_PRIVATE_USER_ALL") ?>:</h4></td>
										<td></td>
										<td><h4><?= GetMessage("PW_TD_PRIVATE_USER_SELECT") ?>:</h4></td>
									</tr>
									<tr>
										<td>
											<select id="select-all-supplier" class="supplier-select" multiple size="20">
												<? if ($arResult["LOT"]["PRIVATE"] == "Y"): ?>
													<? foreach ($arResult["LOT"]["PRIVATE_USER"] as $arSupplier) : ?>
														<option value="<?= $arSupplier["id"] ?>"><?= $arSupplier["company"] ?></option>
													<? endforeach; ?>
												<? endif; ?>
											</select>
										</td>
										<td valign="middle" style="padding-top:160px;">
											<input class="btn btn-primary" type="button" id="button-select-add-supplier" value=" > "/><br/>
											<input class="btn btn-danger" type="button" id="button-select-del-supplier" value=" < "/>
										</td>
										<td>
											<select name="PRIVATE_LIST[]" id="select-private-supplier" class="supplier-select" multiple size="20">
												<? if ($arResult["LOT"]["PRIVATE"] == "Y"): ?>
													<? foreach ($arResult["LOT"]["PRIVATE_LIST"] as $arSupplier) : ?>
														<option selected value="<?= $arSupplier["id"] ?>"><?= $arSupplier["company"] ?></option>
													<? endforeach; ?>
												<? endif; ?>
											</select>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</td>
				</tr>
			<? endif; ?>

			<? if ($arParams["COMPANY_ONLY"] == "N"): ?>
				<tr>
					<td width="40%" class="left-col">
						<span class="required">*</span><?= GetMessage("PW_TD_COMPANY_LOT") ?>:
					</td>
					<td width="60%" class="right-col">
						<div class="form-group">
							<select class="form-control input-sm" name="COMPANY_ID"<?= $dis_arch; ?>>
								<? foreach ($arResult["COMPANY"] as $comapny_id => $company_title): ?>
									<option<?= $arResult["LOT"]["COMPANY_ID"] == $comapny_id ? " selected" : ""; ?> value="<? echo $comapny_id ?>"><? echo $company_title ?></option>
								<? endforeach; ?>
							</select>
						</div>
					</td>
				</tr>
			<? endif; ?>

			<tr>
				<td width="40%" class="left-col">
					<span class="required">*</span><?= GetMessage("PW_TD_RESPONSIBLE_FIO") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="RESPONSIBLE_FIO" value="<?= trim(htmlspecialcharsEx($arResult["LOT"]["RESPONSIBLE_FIO"])) ?>" size="50" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<span class="required">*</span><?= GetMessage("PW_TD_RESPONSIBLE_PHONE") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="RESPONSIBLE_PHONE" value="<?= trim(htmlspecialcharsEx($arResult["LOT"]["RESPONSIBLE_PHONE"])) ?>" size="50" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<span class="required">*</span><?= GetMessage("PW_TD_LOT_DATE") . " (" . CLang::GetDateFormat() . ")" ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-inline">
						<div class="form-group">
							<div class="input-group">
								<input class="form-control input-date" type="text" name="DATE_START" value="<?= $arResult["LOT"]["DATE_START"] ?>" class="valid" placeholder="Начало" size="19" <?= $dis_arch; ?> />
								<div class="input-group-addon">
									<?
									$APPLICATION->IncludeComponent(
										'bitrix:main.calendar', '', array(
											'SHOW_INPUT' => 'N',
											'FORM_NAME' => 'lotadd_form',
											'INPUT_NAME' => 'DATE_START',
											'INPUT_VALUE' => $arResult["LOT"]["DATE_START"],
											'SHOW_TIME' => 'Y',
											'HIDE_TIMEBAR' => 'N'
										), null, array('HIDE_ICONS' => 'Y')
									);
									?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<input class="form-control input-date" type="text" name="DATE_END" value="<?= $arResult["LOT"]["DATE_END"] ?>" class="valid" placeholder="Конец" size="19" <?= $dis_arch; ?> />
								<div class="input-group-addon">
									<?
									$APPLICATION->IncludeComponent(
										'bitrix:main.calendar', '', array(
											'SHOW_INPUT' => 'N',
											'FORM_NAME' => 'lotadd_form',
											'INPUT_NAME' => 'DATE_END',
											'INPUT_VALUE' => $arResult["LOT"]["DATE_END"],
											'SHOW_TIME' => 'Y',
											'HIDE_TIMEBAR' => 'N'
										), null, array('HIDE_ICONS' => 'Y')
									);
									?>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>

			<?if($arResult['TYPE_ID'] != 'R' && $arResult['TYPE_ID'] != 'T' ):?>
			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_TIME_UP") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="TIME_EXTENSION" value="<?= htmlspecialcharsEx($arResult["LOT"]["TIME_EXTENSION"]) ?>" size="10" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_TIME_UPDATE") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="TIME_UPDATE" value="<?= htmlspecialcharsEx($arResult["LOT"]["TIME_UPDATE"]) ?>" size="10" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>

			<?endif;?>
			
			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_TYPE_NDS") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<select class="form-control input-sm" name="WITH_NDS"<?= $dis_arch; ?>>
							<option<?= $arResult["LOT"]["WITH_NDS"] == "N" ? " selected" : ""; ?> value="Y"><?= GetMessage("PW_TD_PRICE_NDS_N") ?></option>
							<option<?= $arResult["LOT"]["WITH_NDS"] == "Y" ? " selected" : ""; ?> value="N"><?= GetMessage("PW_TD_PRICE_NDS") ?></option>
						</select>
					</div>
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_CURRENCY") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<select class="form-control input-sm" name="CURRENCY"<?= $dis_arch; ?>>
							<? foreach ($arResult["LOT"]["CURRENCY_ARRAY"] as $nameCurrency => $arCurrency): ?>
								<option<?
									if ($arResult["LOT"]["CURRENCY"] == $nameCurrency)
										echo " selected";
									?> value="<?= $nameCurrency ?>"><?=
									$nameCurrency ?><?
										if (strlen($arCurrency) > 0)
											echo " [" . $arCurrency . "]";
									?>
								</option>
							<? endforeach; ?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_DATE_DELIVERY") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<input class="form-control input-sm"  type="text" name="DATE_DELIVERY" value="<?= htmlspecialcharsEx($arResult["LOT"]["DATE_DELIVERY"]) ?>" size="50" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>
			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_NOTE_LOT") ?>:
				</td>
				<td width="60%" class="right-col">
					<? if ($arParams["VISUAL_EDITOR"] == "Y"): ?>
						<?
						$APPLICATION->IncludeComponent(
							"bitrix:fileman.light_editor", ".default", Array(
								"CONTENT" => $arResult["LOT"]["NOTE"],
								"INPUT_NAME" => "NOTE",
								"WIDTH" => "100%",
								"HEIGHT" => "200px",
								"USE_FILE_DIALOGS" => "N",
								"FLOATING_TOOLBAR" => "Y",
								"ARISING_TOOLBAR" => "Y",
							)
						);
						?>
					<? else: ?>
					<textarea class="form-control" name="NOTE" cols="50" rows="5" <?= $dis_arch; ?>><?= htmlspecialcharsEx($arResult["LOT"]["NOTE"]) ?></textarea>
					<? endif; ?>
				</td>
			</tr>

			<? /* ************ DELIVERY_SECTION ************* */ ?>
			<tr class="heading">
				<td colspan="2"><?= GetMessage("PW_TD_DELIVERY_SECTION") ?></td>
			</tr>
			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_DELIVERY_SELECT") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<select class="form-control input-sm" name="TERM_DELIVERY_ID"<?= $dis_arch; ?>>
							<option value="0">--</option>
							<? foreach ($arResult["DELIVERY"] as $delivery_id => $delivery_title) : ?>
								<option<?
									if ($arResult["LOT"]["TERM_DELIVERY_ID"] == $delivery_id)
										echo " selected";
									?> value="<?= $delivery_id ?>"><?= $delivery_title ?></option>
							<? endforeach; ?>
						</select>
					</div>
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_DELIVERY_VALUE") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="TERM_DELIVERY_VAL" value="<?= htmlspecialcharsEx($arResult["LOT"]["TERM_DELIVERY_VAL"]) ?>" size="50" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_DELIVERY_REQUIRED") ?>:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="TERM_DELIVERY_REQUIRED" value="Y" <?
						if ($arResult["LOT"]["TERM_DELIVERY_REQUIRED"] == "Y")
							echo " checked";
						?> <?= $dis_arch; ?> />
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_DELIVERY_EDIT") ?>:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="TERM_DELIVERY_EDIT" value="Y" <?
						if ($arResult["LOT"]["TERM_DELIVERY_EDIT"] == "Y")
							echo " checked";
						?> <?= $dis_arch; ?> />
				</td>
			</tr>

			<?/* ************ PAYMENT_SECTION ************ */ ?>
			<tr class="heading">
				<td colspan="2"><?= GetMessage("PW_TD_PAYMENT_SECTION") ?></td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_PAYMENT_SELECT") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<select class="form-control input-sm"  name="TERM_PAYMENT_ID"<?= $dis_arch; ?>>
							<option value="0">--</option>
							<? foreach ($arResult["PAYMENT"] as $payment_id => $payment_title) : ?>
								<option<?
									if ($arResult["LOT"]["TERM_PAYMENT_ID"] == $payment_id)
										echo " selected";
									?> value="<?= $payment_id ?>"><?= $payment_title ?></option>
							<? endforeach; ?>
						</select>
					</div>
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_PAYMENT_VALUE") ?>:
				</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="TERM_PAYMENT_VAL" value="<?= htmlspecialcharsEx($arResult["LOT"]["TERM_PAYMENT_VAL"]) ?>" size="50" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>

			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_PAYMENT_REQUIRED") ?>:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="TERM_PAYMENT_REQUIRED" value="Y" <?
						if ($arResult["LOT"]["TERM_PAYMENT_REQUIRED"] == "Y")
							echo " checked";
						?> <?= $dis_arch; ?> />
				</td>
			</tr>
			<tr>
				<td width="40%" class="left-col">
					<?= GetMessage("PW_TD_PAYMENT_EDIT") ?>:
				</td>
				<td width="60%" class="right-col">
					<input type="checkbox" name="TERM_PAYMENT_EDIT" value="Y" <?
						if ($arResult["LOT"]["TERM_PAYMENT_EDIT"] == "Y")
							echo " checked";
						?> <?= $dis_arch; ?> />
				</td>
			</tr>

			<tr class="heading">
				<td colspan="2"><? echo GetMessage("PW_TD_DOCUMENT") ?></td>
			</tr>
			
			<? if (count($arResult["LOT"]["FILE"]) > 0): ?>
				<tr>
					<td valign="top">
						<?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:
					</td>
					<td>
						<table class="t_lot_table">
							<tr>
								<th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
								<th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
								<th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
							</tr>
							<? foreach ($arResult["LOT"]["FILE"] as $arFile) : ?>
								<tr>
									<td>
										<a href="/tx_files/lot_file.php?LOT_ID=<? echo $arResult["LOT"]["ID"] ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a>
									</td>
									<td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
									<td align="center">
										<input type="checkbox" name="FILE_ID[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>" <?= $dis_arch; ?>>
									</td>
								</tr>
							<? endforeach; ?>
						</table>
					</td>
				</tr>
			<? endif; ?>

			<tr>
				<td class="reg-field" width="40%" class="left-col">&nbsp;</td>
				<td width="60%" class="right-col">
					<div class="form-group">
						<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br/>
						<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br/>
						<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br/>
					</div>
				</td>
			</tr>

			<tr class="heading">
				<td colspan="2">Рассылка уведомлений о торгах</td>
			</tr>

			<tr>
				<td valign="top">Сразу после публикации:</td>
				<td>
					<input type="checkbox" name="SUBSCR_NOW" value="Y" <?
						if ($arResult["LOT"]["SUBSCR_NOW"] == "N" && $arResult["LOT"]["ID"] > 0)
							echo "";
						else
							echo " checked";
						?> <?= $dis_arch; ?> />
				</td>
			</tr>

			<tr>
				<td valign="top">Перед началом за NN минут:</td>
				<td>
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="SUBSCR_START" value="<?= intval($arResult["LOT"]["SUBSCR_START"]) ?>" size="10" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>

			<tr>
				<td valign="top">Перед завершением за NN минут:</td>
				<td>
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="SUBSCR_END" value="<?= intval($arResult["LOT"]["SUBSCR_END"]) ?>" size="10" <?= $dis_arch; ?> />
					</div>
				</td>
			</tr>
		</table>
		
		<? /* ************ NESTANDART LOT SECTION ************ */ ?>
		<?if ($arResult["TYPE_ID"] != 'S' && $arResult['TYPE_ID'] != 'R') : ?>
			<h3><?= GetMessage("PW_TD_SPEC_TITLE") ?></h3>
			<table id="table_spec" cellpadding="0" cellspacing="0" width="100%" class="t_lot_table table table-striped table-hover table-condensed">
				<tr>
					<td align="center" width="3%"><? echo GetMessage("PW_TD_NUM") ?></td>
					<td align="center" width="30%"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_NAME") ?></td>
					<td align="center" width="38%"><? echo GetMessage("PW_TD_SPEC_DOP") ?></td>
					<td align="center" width="10%"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_UNIT") ?></td>
					<td align="center" width="7%"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_COUNT") ?></td>
					<td align="center" width="15%"><? echo GetMessage("PW_TD_SPEC_START_PRICE") ?></td>
					<!--td align="center" width="10%"><!--<? echo GetMessage("PW_TD_SPEC_STEP_PRICE") ?>--></td-->
					<td align="center" width="2%"><? echo GetMessage("PW_TD_SPEC_DEL") ?></td>
				</tr>
				<? $numProp = 1; ?>
				<? foreach ($arResult["LOT"]["SPEC"] as $kkey => $PROP): ?>
					<? if (is_numeric($kkey)) { ?>
						<tr>
							<td align="center"><? echo $numProp ?></td>
							<td align="center">
								<div class="form-group">
									<input class="form-control input-sm" type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_TITLE" value="<?= htmlspecialcharsEx($PROP["TITLE"]) ?>" style="width: 98%" <?= $dis_arch; ?> />
								</div>
							</td>
							<td align="center">
								<div class="form-group">
									<input class="form-control input-sm" type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_ADD_INFO" value="<?= htmlspecialcharsEx($PROP["ADD_INFO"]) ?>" style="width: 98%" <?= $dis_arch; ?> />
								</div>
							</td>
							<td align="center">
								<div class="form-group">
									<select class="form-control input-sm" name="PROP_<?= $PROP["PROP_ID"] ?>_UNIT_ID"<?= $dis_arch; ?>>
										<? foreach ($arResult["UNIT"] as $unit_id => $unit_title): ?>
											<option<?
												if ($PROP["UNIT_ID"] == $unit_id)
													echo " selected";
												?> value="<?= $unit_id ?>"><?= $unit_title ?></option>
										<? endforeach; ?>
									</select>
								</div>
							</td>
							<td align="center">
								<div class="form-group">
									<input class="form-control input-sm" type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_COUNT" value="<?= $PROP["COUNT"] ?>" <?= $dis_arch; ?> />
								</div>
							</td>
							<td align="center">
								<div class="form-group">
									<input class="form-control input-sm" type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_START_PRICE" value="<?= $PROP["START_PRICE"] ?>" <?= $dis_arch; ?> />
								</div>
							</td>
							<!--td align="center" width="50">
								<div class="form-group">
									<input class="form-control input-sm" type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_STEP_PRICE" value="<?= $PROP["STEP_PRICE"] ?>" style="width: 98%" <?= $dis_arch; ?> />
								</div>
							</td-->
							<td align="center">
								<? if (intval($PROP["PROP_ID"]) > 0): ?>
									<input type="checkbox" name="PROP_<?= $PROP["PROP_ID"] ?>_DEL" value="Y" <?= $dis_arch; ?> />
								<? endif ?>
								<input type="hidden" name="PROP_HIDDEN_ID[]" value="<?= $PROP["PROP_ID"] ?>"/>
							</td>
						</tr>
						<? $numProp++; ?>
					<? } ?>
				<? endforeach; ?>
			</table>
			
			<input type="hidden" id="numProp" value="<?= $numProp ?>"/>
			<input type="hidden" id="newProp" name="newProp" value="<?= isset($arResult["SPEC_NEW_PROP"]) ? $arResult["SPEC_NEW_PROP"] : 0; ?>"/>
		<? endif; ?>
		
		<? if ($arResult['TYPE_ID'] == 'S' || $arResult['TYPE_ID'] == 'R'): ?>
			<? if ($arResult["PRODUCTS_ID"] > 0): ?>
			
				<? /* ************ STANDART LOT SECTION ************ */ ?>
				<? foreach ($arResult["PRODUCTS_ID"] as $id_mass => $tov_id): ?>
					<? $tov_arr = $arResult["LOT"]["TOVAR"][$tov_id]; ?>
					<h3><?= GetMessage("PW_TD_TOVAR_TITLE") ?> <?= $tov_arr["TITLE"] ?></h3>
					<table cellpadding="0" cellspacing="0" width="100%" class="t_lot_table table table-striped table-hover table-condensed">
						<? if ($arResult["ID"] <= 0) {
							$str_PROP_PROD_VISIBLE = "Y";
						}?>
						<tr class="heading">
							<td align="center" width="5%"><? echo GetMessage("PW_TD_PRODUCT_PROP_VISIBLE") ?></td>
							<td align="center" width="40%"><? echo GetMessage("PW_TD_PRODUCT_NAME") ?></td>
							<td align="center" width="40%"><? echo GetMessage("PW_TD_PRODUCT_VALUE") ?></td>
							<td align="center" width="10%"><? echo GetMessage("PW_TD_PRODUCT_REQUIRED") ?></td>
							<td align="center" width="20%"><? echo GetMessage("PW_TD_PRODUCT_EDIT") ?></td>
						</tr>
						<? foreach ($tov_arr["PROP"] as $prop_arr): ?>
							<tr>
								<td align="center" width="5">
									<input type="checkbox" name="PROP_PROD_<?= $prop_arr["ID"][$id_mass] ?>_VISIBLE[<?= $id_mass ?>][<?= $tov_id ?>]" <?
										if ($prop_arr["VISIBLE"][$id_mass] == "Y")
											echo "checked";
										?> value="Y" <?= $dis_arch; ?> />
								</td>
								<td align="center" width="120">
									<? echo $prop_arr["TITLE"][$id_mass] ?>
								</td>
								<td align="left" width="80">
									<? if (is_array($prop_arr["SPR_ID"][$id_mass])): ?>
										<div class="form-group">
											<select class="form-control input-sm" name="PROP_PROD_<?= $prop_arr["ID"][$id_mass] ?>_VALUE[<?= $id_mass ?>][<?= $tov_id ?>]"<?= $dis_arch; ?>>
												<? foreach ($prop_arr["SPR_ID"] as $sprId => $sprTitle): ?>
													<option<?
														if ($sprId == $prop_arr["VALUE"][$id_mass])
															echo " selected";
														?> value="<?= $sprId ?>"><?= $sprTitle ?></option>
												<? endforeach; ?>
											</select>
										</div>
									<? else: ?>
									<div class="form-group">
										<input class="form-control input-sm" type="text"
											name="PROP_PROD_<?= $prop_arr["ID"][$id_mass] ?>_VALUE[<?= $id_mass ?>][<?= $tov_id ?>]"
											value="<?= htmlspecialcharsEx($prop_arr["VALUE"][$id_mass]) ?>"
											style="" <?= $dis_arch; ?> />
									</div>
									<? endif; ?>
								</td>
								<td align="center" width="20">
									<input type="checkbox"
										name="PROP_PROD_<?= $prop_arr["ID"][$id_mass] ?>_REQUIRED[<?= $id_mass ?>][<?= $tov_id ?>]" <?
										if ($prop_arr["REQUIRED"][$id_mass] == "Y")
											echo "checked";
										?> value="Y" <?= $dis_arch; ?> />
								</td>
								<td align="center" width="20">
									<input type="checkbox"
										name="PROP_PROD_<?= $prop_arr["ID"][$id_mass] ?>_EDIT[<?= $id_mass ?>][<?= $tov_id ?>]" <?
										if ($prop_arr["EDIT"][$id_mass] == "Y")
											echo "checked";
										?> value="Y" <?= $dis_arch; ?> />
								</td>
							</tr>
						<? endforeach; ?>
						<tr>
							<td align="center" width="5"></td>
							<td align="center" width="120"><?= GetMessage("PW_TD_STANDART_COUNT_NAME") ?></td>
							<td align="left" width="80">
								<div class="form-group">
									<input class="form-control input-sm" type="text" name="COUNT[<?= $id_mass ?>][<?= $tov_id ?>]"
										value="<?= $tov_arr["COUNT"][$id_mass] ?>"
										style="" <?= $dis_arch; ?> />
								</div>
							</td>
							<td align="center" width="20"></td>
							<td align="center" width="20">
								<input type="checkbox" name="COUNT_EDIT[<?= $id_mass ?>][<?= $tov_id ?>]" <?
									if ($tov_arr["COUNT_EDIT"][$id_mass] == "Y")
										echo "checked";
									?> value="Y" <?= $dis_arch; ?> />
							</td>
						</tr>
						<tr>
							<td align="center" width="5"></td>
							<td align="center" width="120"><?= GetMessage("PW_TD_STANDART_UNIT_NAME") ?></td>
							<td align="left" width="80">
								<div class="form-group">
									<input class="form-control input-sm" type="text" value="<?= $tov_arr["UNIT_NAME"] ?>" disabled/>
								</div>
							</td>
							<td align="center" width="20"></td>
							<td align="center" width="20"></td>
						</tr>

						<tr class="heading">
							<td colspan="5"><?= GetMessage("PW_TD_STANDART_PRICE_SECTION") ?></td>
						</tr>

						<tr>
							<td colspan="2">
								<?= GetMessage("PW_TD_STANDART_START_PRICE") ?>:
							</td>
							<td>
								<div class="form-group">
									<input class="form-control input-sm" type="text" name="START_PRICE[<?= $id_mass ?>][<?= $tov_id ?>]"
										value="<?= htmlspecialcharsEx($tov_arr["START_PRICE"][$id_mass]) ?>"/>
								</div>
							</td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td colspan="2">
								<?= GetMessage("PW_TD_STANDART_STEP_PRICE") ?>:
							</td>
							<td>
								<div class="form-group">
									<input class="form-control input-sm" type="text" name="STEP_PRICE[<?= $id_mass ?>][<?= $tov_id ?>]"
										value="<?= htmlspecialcharsEx($tov_arr["STEP_PRICE"][$id_mass]) ?>"/>
								</div>
							</td>
							<td></td>
							<td></td>
						</tr>
					</table>
				<? endforeach; ?>
			<? else: ?>
				<table cellpadding="0" cellspacing="0" width="100%" class="t_lot_table">
					<tr>
						<td>
							<? echo GetMessage("PW_TD_PRODUCTS_NOSELECT") ?>
						</td>
					</tr>
				</table>
			<? endif; ?>
		<? endif; ?>

		<? if ($arResult["TYPE_ID"] != 'S' && $arResult["TYPE_ID"] != 'R'): ?>
			<div class="container">
				<div class="row">
					<input class="btn btn-primary btn-sm" type="button" id="addItem" value="<?= GetMessage("PW_TD_BUTTON_ADD_ITEM") ?>"<?= $dis_arch; ?> />       
				</div>
			</div>
		<? endif; ?>
		<br/>
		<div class="container">
			<div class="row">
				<input class="btn btn-primary" type="submit" name="lotadd_submit" value="<?= GetMessage("PW_TD_ADD_LOT") ?>" <?= $dis_arch; ?> />&nbsp;
				<? if (!$T_RIGHT == 'P'): ?><input class="btn btn-primary" type="submit" name="lotopen_submit" value="<?= GetMessage("PW_TD_OPEN_LOT") ?>" <?= $dis_arch; ?> /><?endif;?>
			</div>
		</div>
	</form>
</div>
<?endif;?>