<?
/**
 * Created by vfilippov on 13.08.15.
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!CModule::IncludeModule("pweb.tenderix"))
return;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/pweb.tenderix/list.suppliers/class.php");

$lot_id = intval($_REQUEST["lotId"]);
$user_id = intval($_REQUEST["userId"]);

$res = CListSupplierClass::requestAddProposal($lot_id, $user_id);

if ($res) {
    echo 1;
} else {
    echo "Доступ к данному лоту уже был запрошен ранее";
}

?>