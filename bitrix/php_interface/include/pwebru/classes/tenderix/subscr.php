<?
function TenderixDistrLot($action, $id)
{
    //$id = intval($_GET['ID']);
    //$action = intval($_GET['ACTION']);
    if ($action > 0 && $id > 0) {
        CModule::IncludeModule("pweb.tenderix");
        
        $rsLot = CTenderixLot::GetByIDa($id);
        $arLot = $rsLot->GetNext();

        if ($rsPrivate = CTenderixLot::GetUserPrivateLot($id)) {
            while ($arPrivate = $rsPrivate->GetNext()) {
                $arLot["PRIVATE_LIST"][] = $arPrivate["USER_ID"];
            }
        }

        if($arLot["PRIVATE_LIST"]){
            $emailSubscrSupplier = CTenderixUserSupplier::GetEmailSubscribeListLot($arLot["PRIVATE_LIST"]);
        }else {
            $emailSubscrSupplier = CTenderixUserSupplier::GetEmailSubscribeListSection($arLot["SECTION_ID"], $arLot["PRIVATE_LIST"]);
        }

        $COMPANY = CTenderixCompany::GetByIdName($arLot["COMPANY_ID"]);
        $rsSupplier = CUser::GetByID($arLot["BUYER_ID"]);
        $arSupplier = $rsSupplier->Fetch();
        foreach ($emailSubscrSupplier as $idSupplier => $infoSupplier) {
            $arEventFields = array(
                "LOT_NUM" => $id,
                "LOT_NAME" => $arLot["TITLE"],
                "SUPPLIER" => $infoSupplier["FIO"],
                "COMPANY" => $COMPANY,
                "RESPONSIBLE_FIO" => $arLot["RESPONSIBLE_FIO"],
                "RESPONSIBLE_PHONE" => $arLot["RESPONSIBLE_PHONE"],
                "DATE_START" => $arLot["DATE_START"],
                "DATE_END" => $arLot["DATE_END"],
                "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                "EMAIL_TO" => $infoSupplier["EMAIL"],
                "NOTE" => strlen($arLot["NOTE"]) > 0 ? $arLot["NOTE"] : "-",
                "RESPONSIBLE_EMAIL" => $arSupplier["EMAIL"],
            );
            $arrSITE = CTenderixLot::GetSite();
            if ($action == 1) {
                CEvent::Send("TENDERIX_START_LOT", $arrSITE, $arEventFields, "N");
                //file_put_contents($file, " EVENT_START");
            }
            if ($action == 2)
                CEvent::Send("TENDERIX_FINISH_LOT", $arrSITE, $arEventFields, "N");
        }

    }
}
function TenderixSequrityLot($action, $id)
{
        CModule::IncludeModule("pweb.tenderix");

        $rsLot = CTenderixLot::GetByIDa($id);
        $arLot = $rsLot->GetNext();
        // $filter = array(
        //     "ACTIVE" => "Y",
        //     "GROUPS_ID" => array(9)
        //     );
        // $rsUsers = CUser::GetList(($by=""), ($order="desc"), $filter);
        // while ($arUser = $rsUsers->Fetch()) {
        //     $reUser[] = $arUser;
        // }

        $COMPANY = CTenderixCompany::GetByIdName($arLot["COMPANY_ID"]);
        // $rsSupplier = CUser::GetByID($arLot["BUYER_ID"]);
        // $arSupplier = $rsSupplier->Fetch();

        //foreach ($reUser as $user) {
            $arEventFields = array(
                "LOT_NUM" => $id,
                "LOT_NAME" => $arLot["TITLE"],
                "COMPANY" => $COMPANY,
                "RESPONSIBLE_FIO" => $arLot["RESPONSIBLE_FIO"],
                "RESPONSIBLE_PHONE" => $arLot["RESPONSIBLE_PHONE"],
                "DATE_START" => $arLot["DATE_START"],
                "DATE_END" => $arLot["DATE_END"],
                "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                "EMAIL_TO" => 'MVasileva@mtt.ru',
                //"EMAIL_TO" => $user['EMAIL'],
                //"NOTE" => strlen($arLot["NOTE"]) > 0 ? $arLot["NOTE"] : "-",
                "COMMENT_MAIL" => "Лот завершен! Требуется проверить поставщиков.",
                //"RESPONSIBLE_EMAIL" => $arSupplier["EMAIL"],
            );
            $arrSITE = CTenderixLot::GetSite();
            $res = CEvent::Send("TENDERIX_LOT_DONE", $arrSITE, $arEventFields, "N");
        file_put_contents('/home/bitrix/_res.txt', print_r($res,true));
        //}
        CEvent::CheckEvents();

}

?>