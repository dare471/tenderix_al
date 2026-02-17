<?
/**
 * Установка агентов для автоматической выгрузки отчетов Tenderix
 * Запустить один раз для установки агентов в систему Bitrix
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("main")) {
    die("Модуль main не установлен");
}

// Проверка прав администратора
global $USER;
if (!$USER->IsAdmin()) {
    die("Доступ запрещен. Требуются права администратора.");
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/tx_files/reports_auto_export.php");

// Удаляем старые агенты (если есть)
$rsAgents = CAgent::GetList(
    array("ID" => "DESC"),
    array("NAME" => "%TenderixAutoExport%")
);
while ($arAgent = $rsAgents->Fetch()) {
    CAgent::Delete($arAgent["ID"]);
}

// Устанавливаем агент для квартальных отчетов
// Запускается каждый день в 02:00
$nextExecQuarterly = ConvertTimeStamp(
    strtotime(date('Y-m-d 02:00:00', strtotime('+1 day'))),
    'FULL'
);

CAgent::AddAgent(
    "TenderixAutoExportQuarterly();",
    "",
    "N", // не периодический
    86400, // интервал 1 день (в секундах)
    "",
    "Y", // активен
    $nextExecQuarterly,
    100
);

// Устанавливаем агент для полугодовых отчетов
// Запускается каждый день в 02:30
$nextExecHalfYearly = ConvertTimeStamp(
    strtotime(date('Y-m-d 02:30:00', strtotime('+1 day'))),
    'FULL'
);

CAgent::AddAgent(
    "TenderixAutoExportHalfYearly();",
    "",
    "N",
    86400,
    "",
    "Y",
    $nextExecHalfYearly,
    100
);

echo "<h2>Агенты успешно установлены!</h2>";
echo "<p>Квартальные отчеты будут генерироваться автоматически каждый день в 02:00</p>";
echo "<p>Полугодовые отчеты будут генерироваться автоматически каждый день в 02:30</p>";
echo "<p>Отчеты сохраняются в: /upload/tenderix_reports/</p>";
echo "<p><a href='/bitrix/admin/agent_list.php'>Просмотр агентов в админ-панели</a></p>";
?>
