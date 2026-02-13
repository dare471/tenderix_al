<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix")) {
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");


if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($T_RIGHT == 'S' || $T_RIGHT == 'W')) {
	
	$action = $_REQUEST['action'];
	
	$lot_id = isset($_GET['LOT_ID']) ? intval($_GET['LOT_ID']) : 0;
	
	$rsLot = CTenderixLot::GetByIDa($lot_id);
	$arLot = $rsLot->Fetch();
	
	switch($action) {
		case 'add_time':
			//Теперь еще надо проверку завершен ли лот.
			//А также логировать

			$time = intval($_REQUEST['time']);
			$ID = intval($_REQUEST['LOT_ID']);
			$res = CTenderixLot::AddTime($ID, $time);
			if ($res)	
				print_r('<font color="green">Лот продлен на ' . $time . ' секунд</font>');
			else
				print_r('<font color="red">Произошла ошибка</font>');
			die();
		break;
		default:
		
		// Получаем информацию по LOT_ID
		if (isset ($_POST['email']))
			foreach ($_POST['email'] as $k => $v) {		
				$email = substr($v, 0, strpos($v, ':'));
				$fio = substr($v, strpos($v, ':')+1);
				$company = CTenderixCompany::GetByIdName($arLot["COMPANY_ID"]);
				//
				$arEventFields = array(
					"LOT_NUM" => $lot_id,
					"LOT_NAME" => $arLot["TITLE"],
					"SUPPLIER" => $fio,
					"COMPANY" => $company,
					"RESPONSIBLE_FIO" => $arLot["RESPONSIBLE_FIO"],
					"RESPONSIBLE_PHONE" => $arLot["RESPONSIBLE_PHONE"],
					"DATE_START" => $arLot["DATE_START"],
					"DATE_END" => $arLot["DATE_END"],
					"EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
					"EMAIL_TO" => $email,
					"NOTE" => strlen($arLot["NOTE"]) > 0 ? $arLot["NOTE"] : "-",
					"RESPONSIBLE_EMAIL" =>'',
				);
				$arrSITE = CTenderixLot::GetSite();
				// print_r($arEventFields);
				CEvent::Send("TENDERIX_NEW_LOT", $arrSITE, $arEventFields, "N");
				CTenderixLog::Log("TENDERIX_NEW_LOT", array("ID" => $lot_id, "FIELDS" => $arEventFields));
			}
			
	}

}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && ($T_RIGHT == 'S' || $T_RIGHT == 'W')) {
 
$lot_id = isset($_GET['LOT_ID']) ? intval($_GET['LOT_ID']) : 0;

$action = $_REQUEST['action'];

switch($action) {
		case 'proposal_list':
			if ($lot_id > 0 && $T_RIGHT == 'W') { ?>
				<div class="row">
					<?$APPLICATION->IncludeComponent(
						"pweb.tenderix:proposal.list", 
						"tx_proposal_history", 
						array(
							"LOT_ID" => $lot_id,
							"JQUERY" => "N",
							"CACHE_TYPE" => "N",
							"CACHE_TIME" => "3600000",
							"SORT_ITOGO" => "asc"
						),
						false
					);?>
				</div>
			<?}
			
		break;
		default:


			if ($lot_id > 0) {
				$rsLot = CTenderixLot::GetByIDa($lot_id);
				$arLot = $rsLot->Fetch();
				$SECTION_ID = $arLot['SECTION_ID'];
				
				//Пока без "PRIVATE_LIST", но в будущем нужно будет добавить эту функцию
				$arList = CTenderixUserSupplier::GetEmailSubscribeListSectionEx($arLot['SECTION_ID']);
				$arEventSendList = CTenderixLog::GetListNewLotEvent($lot_id);
				?>

				<form id="email-send" action="/event_send.php?LOT_ID=<?php echo $lot_id;?>" method="POST" name="email-send">
					<table class="table">	
						<tr>
							<th><input id="check-all" type="checkbox" onchange="var elements = document.getElementsByClassName('check-email-send'); for (var i = 0; i < elements.length; i++) { elements[i].checked = this.checked;}" value="1" name="" /></th>
							<th>Наименование компании</th>
						</tr>		
						<?
						foreach ($arList as $k => $value) {
							?>
								<tr>
									<td><input class="check-email-send"  type="checkbox"  value="<?echo $value['EMAIL'] . ':' . $value['FIO']?>" name="email[]" /></td>
									<td><?echo $value['NAME_COMPANY']?><?if (isset($arEventSendList[$value['EMAIL']])) echo '<span class="label-email-send">(' . $arEventSendList[$value['EMAIL']] . ')</span>'?></td>
								</tr>
							<?
						}
						?>			
					</table>
				</form>
				<?
			}
		break;
	}
}

?>