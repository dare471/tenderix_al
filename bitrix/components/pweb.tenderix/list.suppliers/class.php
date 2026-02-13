<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc as Loc;

if (!CModule::IncludeModule("pweb.tenderix")) {
	$this->AbortResultCache();
	ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
	return;
}

global $APPLICATION; 
$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/colorbox2/jquery.colorbox-min.js"></script>', true);
$APPLICATION->AddHeadString('<link href="/bitrix/js/pweb.tenderix/colorbox2/colorbox.css" type="text/css" rel="stylesheet">', true);

class CListSupplierClass extends CBitrixComponent
{

	/**
	 * кешируемые ключи arResult
	 * @var array()
	 */
	protected $cacheKeys = array();
	
	/**
	 * дополнительные параметры, от которых должен зависеть кеш
	 * @var array
	 */
	protected $cacheAddon = array();
	
	/**
	 * парамтеры постраничной навигации
	 * @var array
	 */
	protected $navParams = array();


	/**
	 * статус пользователя, просматривающего страницу поставщиков
	 */
	protected $T_RIGHT;

	protected $arFilter = array();


	public function getUserRight() {
		return $T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
	}

	/**
	 * подключает языковые файлы
	 */
	public function onIncludeComponentLang()
	{
		$this -> includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}
	
    /**
     * подготавливает входные параметры
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $result = array(
            'IBLOCK_TYPE' => trim($params['IBLOCK_TYPE']),
            'IBLOCK_ID' => intval($params['IBLOCK_ID']),
			'FILTER' => $params['FILTER'],
            'SHOW_NAV' => ($params['SHOW_NAV'] == 'Y' ? 'Y' : 'N'),
            'COUNT' => intval($params['COUNT']),
            'SORT_FIELD1' => strlen($params['SORT_FIELD1']) ? $params['SORT_FIELD1'] : 'ID',
            'SORT_DIRECTION1' => $params['SORT_DIRECTION1'] == 'ASC' ? 'ASC' : 'DESC',
            'SORT_FIELD2' => strlen($params['SORT_FIELD2']) ? $params['SORT_FIELD2'] : 'ID',
            'SORT_DIRECTION2' => $params['SORT_DIRECTION2'] == 'ASC' ? 'ASC' : 'DESC',
			'CACHE_TYPE' => $params['CACHE_TYPE'],
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600
        );
        return $result;
    }

	/**
	 * определяет читать данные из кеша или нет
	 * @return bool
	 */
	protected function readDataFromCache()
	{
		if ($this -> arParams['CACHE_TYPE'] == 'N')
			return false;
		return !($this -> StartResultCache(false, $this -> cacheAddon));
	}
	/**
	 * кеширует ключи массива arResult
	 */
	protected function putDataToCache()
	{
		if (is_array($this -> cacheKeys) && sizeof($this -> cacheKeys) > 0)
		{
			$this -> SetResultCacheKeys($this -> cacheKeys);
		}
	}
	/**
	 * прерывает кеширование
	 */
	protected function abortDataCache()
	{
		$this -> AbortResultCache();
	}
	
	/**
	 * проверяет подключение необходиимых модулей
	 * @throws LoaderException
	 */
	protected function checkModules()
	{
		if (!Main\Loader::includeModule('iblock'))
			throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));
	}
	
	/**
	 * проверяет заполнение обязательных параметров
	 * @throws SystemException
	 */
	protected function checkParams()
	{
		//if ($this -> arParams['IBLOCK_ID'] <= 0)
			//throw new Main\ArgumentNullException('IBLOCK_ID');
	}
	
	/**
	 * выполяет действия перед кешированием 
	 */
	protected function executeProlog()
	{
		if ($this -> arParams['COUNT'] > 0)
		{
			if ($this -> arParams['SHOW_NAV'] == 'Y')
			{
				\CPageOption::SetOptionString('main', 'nav_page_in_session', 'N');
				$this -> navParams = array(
					'nPageSize' => $this -> arParams['COUNT']
				);
	    		$arNavigation = \CDBResult::GetNavParams($this -> navParams);
				$this -> cacheAddon = array($arNavigation);
			}
			else
			{
				$this -> navParams = array(	
					'nTopCount' => $this -> arParams['COUNT']
				);
			}
		}
	}

	/**
	 * Проверка прав доступа к лоту
	 */
	public function selectRequestProposal($lot_id, $user_id, $typer) {
		global $DB;

		switch ($typer) {
			/*
			 * проверяем, есть ли запись в таблице о запрошенном доступе по лоту:
			 * 1. защита от хакеров, которые могут разблокировать кнопку "Запросить доступ" (через firebug, например) и несколько раз жмахать кнопку  :-);
			 * 2. при входе на страницу детального просмотра лота.
			*/
			case 'requestAddProposal':
				$where = "LOTID =".$lot_id." AND USERID =".$user_id;
				break;

			//Выборка всех поставщиков, которые запросили доступ к данному лоту
			case 'selectListSuppliersRequest':
				$where = "LOTID =".$lot_id." AND ACCESS = 'R'";
				break;
		}

		$strSql = "SELECT * FROM b_tx_supplier_lot_access WHERE ".$where;

		$res = $DB->Query($strSql, false, $err_mess . __LINE__);
		while ($res_lot_access = $res->Fetch()) {
			$lot_access_array[] = $res_lot_access;

		}

		return $lot_access_array;
	}

	/**
	 * получение результатов для списка поставщиков, запросивших доступ к лоту
	 */
	public function getResultForRequestAccess()
	{
		//выбираем поставщиков, которые имеют доступ к данному лоту
		if(isset($_REQUEST["LOT_ID"]) && ($_REQUEST["LOT_ID"] != '')) {
			$suppliers_requests = $this->selectRequestProposal($_REQUEST["LOT_ID"], null, 'selectListSuppliersRequest');
			//__($suppliers_requests);
		}

		if(!empty($suppliers_requests)) {
			foreach($suppliers_requests as $suppliers_access) {
				$arFilter = ARRAY("USER_ID" => $suppliers_access["USERID"]);
				$this -> getResult($arFilter, $suppliers_access["ACCESS"]);
			}
		}
	}

	/**
	 * получение результатов
	 */
	public function getResult($arFilter, $suppliers_access = null)
	{
		$resStatus = CTenderixUserSupplierStatus::GetList($by, $order = "asc", array("ACTIVE" => "Y"));
		while ($arStatus = $resStatus->GetNext()) {
			$arStat['STAT'][] = $arStatus;
		}


		$arFilter = (isset($_REQUEST['status']) ? (($_REQUEST['status'] == 0) ? array() : ARRAY("STATUS" => $_REQUEST['status'])) : $arFilter);

		$this->arResult['STATUS_ARRAY'] = $arStat['STAT'];
		
		$rsData = CTenderixUserSupplier::GetList($by = "s_id", $order = "desc", $arFilter, $is_filtered);

//		if ($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"]) {
//			$arNavParams = array(
//				"nPageSize" => $arParams["LOTS_COUNT"],
//				"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
//				"bShowAll" => $arParams["PAGER_SHOW_ALL"],
//			);
//			$arNavigation = CDBResult::GetNavParams($arNavParams);
//
//			if ($arNavigation["PAGEN"] == 0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] > 0)
//				$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
//		}
//		else {
			$arNavParams = array(
				"nTopCount" => 10,
				"nPageSize" => 10
			);
			//$arNavigation = false;
//		}


		//echo $rsData->SelectedRowsCount();

		$rsData->NavStart($arNavParams);
		$this->arResult["NAV_STRING"] = $rsData->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
		$this->arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
		//$this->arResult["NAV_RESULT"] = $rsData;

//		echo "<pre>";print_r($arResult);echo "</pre>";
//		echo "3"; die();

        while ($arRes = $rsData->GetNext()) {
        	//echo "<pre>";print_r($arRes);echo "</pre>";
			//echo "2"; die();
        	$arSubs = CTenderixUserSupplier::SubscribeList($arRes['ID']);
        	while ($arSub = $arSubs->GetNext()) {
        		$arSections = CTenderixSection::GetMixedList($by, $order, array("ID" => $arSub["SECTION_ID"], "ACTIVE" => "Y"))->GetNext();
        		$arRes['SUB'][] = $arSections['TITLE'];
        	}
        	$resFiles = CTenderixUserSupplier::GetFileList($arRes['ID']);
        	while ($arFiles = $resFiles->GetNext()) {
        		$arRes['FILES'][] = $arFiles;
        	}



        	$resProp = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc");
        	if ($arRes["ID"] > 0) {
				$arRes["PROP_S"] = CTenderixUserSupplier::GetProperty($arRes["ID"]);
			}
        	while ($arPropList = $resProp->GetNext()) {
				if ($arPropList["ACTIVE"] == "N")
					continue;

				$arRes["PROP"][$arPropList["ID"]] = $arPropList;
				if ($arPropList["PROPERTY_TYPE"] == "F" && $arRes['ID'] > 0) {
					$rsFiles = CTenderixUserSupplier::GetFileListProperty($arRes['ID'], $arPropList["ID"]);
					while ($arFile = $rsFiles->GetNext()) {
						$arRes["PROP"][$arPropList["ID"]]["FILE"][] = $arFile;

					}
				}
        	}


			//$rights = $this -> getUserRight();

        	$this->arResult["SUPPLIERS"][] = array(
        		'ALL' => $arRes,
        		'ID' => $arRes['ID'],
        		'NAME' => $arRes['FIO'],
        		'NAME_COMPANY' => $arRes['NAME_COMPANY'],
        		'EMAIL' => $arRes['EMAIL'],
        		'STATUS_NAME' => $arRes['STATUS_NAME'],
        		'STATUS_ID' => $arRes['STATUS_ID'],
				'T_RIGHT' => $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix"),
        		'AUTH' => $arRes['AUTH'],
        		'PART' => $arRes['PART'],
        		'SUBS' => $arRes['SUB'],
        		'FILES' => $arRes['FILES'],
        		'PROP' => $arRes['PROP'],
        		'PROP_S' => $arRes["PROP_S"],
				'LOT_ACCESS' => $suppliers_access
        		);
        }

		if ($this -> arParams['SHOW_NAV'] == 'Y' && $this -> arParams['COUNT'] > 0)
		{
			$this -> arResult['NAV_STRING'] = $rsElement -> GetPageNavString('');
		}

		return $this -> arResult;
	}
	
	/**
	 * выполняет действия после выполения компонента, например установка заголовков из кеша
	 */
	protected function executeEpilog()
	{
		
	}

	/**
	 * выполняет логику работы компонента
	 */
	public function executeComponent()
	{
		try
		{
			$this -> checkModules();
			$this -> checkParams();
			$this -> executeProlog();
			if (!$this -> readDataFromCache())
			{
				if(isset($_REQUEST["LOT_ID"]) && ($_REQUEST["LOT_ID"] != '')) {
					$this -> getResultForRequestAccess();
				} else {
					$this->getResult($this->arFilter);
				}
				$this -> putDataToCache();
				$this -> includeComponentTemplate();
			}
			$this -> executeEpilog();
		}
		catch (Exception $e)
		{	
			$this -> abortDataCache();
			ShowError($e -> getMessage());
		}
	}



	/**
	 * Поставщик запрашивает доступ к лоту
	 */
	public function requestAddProposal($lot_id, $user_id) {
		global $DB;


		$СLSElement = new CListSupplierClass;
		$req_in_base = $СLSElement->selectRequestProposal($lot_id, $user_id, 'requestAddProposal');

		if(count($req_in_base) ==  0) {
			$strSql = "INSERT INTO b_tx_supplier_lot_access(LOTID, USERID, ACCESS) VALUES(" . $lot_id . ", " . $user_id . ", 'R')";
			if ($DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * Разрешаем доступ к лоту
	 */
	public function confirmAccessLot($lot_id, $user_id) {
		global $DB;

		$strSql = "UPDATE b_tx_supplier_lot_access SET `ACCESS`='Y' WHERE `LOTID` = ".$lot_id." AND `USERID` = ".$user_id;

		if ($DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
			return true;
		} else {
			return false;
		}
	}
}

?>
