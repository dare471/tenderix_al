<?
/**
 * Автоматическая выгрузка отчетов и протоколов Tenderix
 * Квартальные и полугодовые отчеты
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("pweb.tenderix")) {
    return false;
}

/**
 * Генерация отчета за период
 * @param string $periodType - 'quarter' (квартал) или 'halfyear' (полугодие)
 * @param int $year - год
 * @param int $periodNumber - номер периода (1-4 для кварталов, 1-2 для полугодий)
 * @return string|false - путь к файлу или false при ошибке
 */
function GeneratePeriodReport($periodType, $year, $periodNumber) {
    global $DB;
    
    // Определяем даты периода
    if ($periodType == 'quarter') {
        $startMonth = ($periodNumber - 1) * 3 + 1;
        $endMonth = $periodNumber * 3;
        $dateStart = sprintf('%04d-%02d-01 00:00:00', $year, $startMonth);
        $dateEnd = sprintf('%04d-%02d-%02d 23:59:59', $year, $endMonth, date('t', mktime(0, 0, 0, $endMonth, 1, $year)));
        $periodName = sprintf('Q%d_%d', $periodNumber, $year);
    } else { // halfyear
        $startMonth = ($periodNumber - 1) * 6 + 1;
        $endMonth = $periodNumber * 6;
        $dateStart = sprintf('%04d-%02d-01 00:00:00', $year, $startMonth);
        $dateEnd = sprintf('%04d-%02d-%02d 23:59:59', $year, $endMonth, date('t', mktime(0, 0, 0, $endMonth, 1, $year)));
        $periodName = sprintf('H%d_%d', $periodNumber, $year);
    }
    
    // Получаем все завершенные лоты за период
    $arFilter = array(
        ">=DATE_END" => $dateStart,
        "<=DATE_END" => $dateEnd
    );
    
    $rsLots = CTenderixLot::GetList(array("DATE_END" => "DESC"), $arFilter);
    $arLots = array();
    while ($arLot = $rsLots->Fetch()) {
        $time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
        if ($time_end < time()) {
            $arLots[] = $arLot;
        }
    }
    
    if (empty($arLots)) {
        return false; // Нет данных за период
    }
    
    // Создаем директорию для отчетов
    $reportsDir = $_SERVER["DOCUMENT_ROOT"] . "/upload/tenderix_reports/";
    if (!is_dir($reportsDir)) {
        mkdir($reportsDir, 0755, true);
    }
    
    $filename = sprintf('report_%s_%s.xls', $periodType, $periodName);
    $filepath = $reportsDir . $filename;
    
    // Генерируем Excel отчет
    $file = fopen($filepath, 'w');
    if (!$file) {
        return false;
    }
    
    // Заголовок Excel (BOM для UTF-8)
    fwrite($file, "\xEF\xBB\xBF");
    
    // HTML заголовок для Excel
    fwrite($file, "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">\n");
    fwrite($file, "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /></head>\n");
    fwrite($file, "<body>\n");
    fwrite($file, "<table border=\"1\">\n");
    
    // Заголовок отчета
    $periodLabel = $periodType == 'quarter' 
        ? sprintf('Квартал %d, %d год', $periodNumber, $year)
        : sprintf('Полугодие %d, %d год', $periodNumber, $year);
    
    fwrite($file, "<tr><td colspan=\"6\" style=\"font-weight:bold;text-align:center;\">Отчет по тендерам за " . $periodLabel . "</td></tr>\n");
    fwrite($file, "<tr><td colspan=\"6\">&nbsp;</td></tr>\n");
    
    // Заголовки таблицы
    fwrite($file, "<tr style=\"font-weight:bold;background-color:#E0E0E0;\">\n");
    fwrite($file, "<td>№</td>\n");
    fwrite($file, "<td>ID лота</td>\n");
    fwrite($file, "<td>Название лота</td>\n");
    fwrite($file, "<td>Дата завершения</td>\n");
    fwrite($file, "<td>Количество предложений</td>\n");
    fwrite($file, "<td>Победитель</td>\n");
    fwrite($file, "</tr>\n");
    
    $i = 1;
    foreach ($arLots as $arLot) {
        // Получаем количество предложений
        $rsProposals = CTenderixProposal::GetList(array(), array("LOT_ID" => $arLot["ID"]));
        $proposalCount = 0;
        while ($rsProposals->Fetch()) {
            $proposalCount++;
        }
        
        // Получаем победителя
        $rsWin = CTenderixLot::GetListWinLot(array(), array("LOT_ID" => $arLot["ID"]));
        $winnerName = "Не определен";
        if ($arWin = $rsWin->Fetch()) {
            $rsUser = CTenderixUserSupplier::GetByID($arWin["USER_ID"]);
            if ($arUser = $rsUser->Fetch()) {
                $winnerName = !empty($arUser["NAME_COMPANY"]) ? $arUser["NAME_COMPANY"] : $arUser["FIO"];
            }
        }
        
        fwrite($file, "<tr>\n");
        fwrite($file, "<td>" . $i . "</td>\n");
        fwrite($file, "<td>" . htmlspecialchars($arLot["ID"]) . "</td>\n");
        fwrite($file, "<td>" . htmlspecialchars($arLot["NAME"]) . "</td>\n");
        fwrite($file, "<td>" . htmlspecialchars($arLot["DATE_END"]) . "</td>\n");
        fwrite($file, "<td>" . $proposalCount . "</td>\n");
        fwrite($file, "<td>" . htmlspecialchars($winnerName) . "</td>\n");
        fwrite($file, "</tr>\n");
        $i++;
    }
    
    fwrite($file, "</table>\n");
    fwrite($file, "</body>\n");
    fwrite($file, "</html>\n");
    fclose($file);
    
    return $filepath;
}

/**
 * Агент для автоматической выгрузки квартальных отчетов
 */
function TenderixAutoExportQuarterly() {
    $currentYear = date('Y');
    $currentMonth = (int)date('m');
    
    // Определяем текущий квартал
    $currentQuarter = ceil($currentMonth / 3);
    
    // Генерируем отчет за предыдущий квартал
    $prevQuarter = $currentQuarter - 1;
    $reportYear = $currentYear;
    
    if ($prevQuarter <= 0) {
        $prevQuarter = 4;
        $reportYear = $currentYear - 1;
    }
    
    $filepath = GeneratePeriodReport('quarter', $reportYear, $prevQuarter);
    
    if ($filepath) {
        // Логируем успешную генерацию
        $logFile = $_SERVER["DOCUMENT_ROOT"] . "/upload/tenderix_reports/export_log.txt";
        $logMessage = date('Y-m-d H:i:s') . " - Квартальный отчет Q{$prevQuarter}_{$reportYear} сгенерирован: {$filepath}\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    // Возвращаем строку для следующего запуска агента (через 1 день)
    return "TenderixAutoExportQuarterly();";
}

/**
 * Агент для автоматической выгрузки полугодовых отчетов
 */
function TenderixAutoExportHalfYearly() {
    $currentYear = date('Y');
    $currentMonth = (int)date('m');
    
    // Определяем текущее полугодие
    $currentHalfYear = ceil($currentMonth / 6);
    
    // Генерируем отчет за предыдущее полугодие
    $prevHalfYear = $currentHalfYear - 1;
    $reportYear = $currentYear;
    
    if ($prevHalfYear <= 0) {
        $prevHalfYear = 2;
        $reportYear = $currentYear - 1;
    }
    
    $filepath = GeneratePeriodReport('halfyear', $reportYear, $prevHalfYear);
    
    if ($filepath) {
        // Логируем успешную генерацию
        $logFile = $_SERVER["DOCUMENT_ROOT"] . "/upload/tenderix_reports/export_log.txt";
        $logMessage = date('Y-m-d H:i:s') . " - Полугодовой отчет H{$prevHalfYear}_{$reportYear} сгенерирован: {$filepath}\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    // Возвращаем строку для следующего запуска агента (через 1 день)
    return "TenderixAutoExportHalfYearly();";
}

// Если файл вызван напрямую (для тестирования или ручной генерации)
if (isset($_REQUEST["action"])) {
    if ($_REQUEST["action"] == "generate_quarter" && isset($_REQUEST["year"]) && isset($_REQUEST["quarter"])) {
        $filepath = GeneratePeriodReport('quarter', (int)$_REQUEST["year"], (int)$_REQUEST["quarter"]);
        if ($filepath) {
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=" . basename($filepath));
            readfile($filepath);
        } else {
            echo "Ошибка генерации отчета";
        }
        die();
    }
    
    if ($_REQUEST["action"] == "generate_halfyear" && isset($_REQUEST["year"]) && isset($_REQUEST["halfyear"])) {
        $filepath = GeneratePeriodReport('halfyear', (int)$_REQUEST["year"], (int)$_REQUEST["halfyear"]);
        if ($filepath) {
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=" . basename($filepath));
            readfile($filepath);
        } else {
            echo "Ошибка генерации отчета";
        }
        die();
    }
}
?>
