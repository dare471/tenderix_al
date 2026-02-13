<?

global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang . "/lang/", "/install/index.php"));

Class pweb_tenderix extends CModule {

    var $MODULE_ID = "pweb.tenderix";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $errors;

    function pweb_tenderix() {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("PW_TD_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("PW_TD_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("PW_TD_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("PW_TD_PARTNER_URI");
    }

    function InstallEvents() {
        global $DB;
        $sIn = "'TENDERIX_NEW_LOT','TENDERIX_NEW_PROPOSAL','TENDERIX_WIN_LOT','TENDERIX_BEST_PROPOSAL','TENDERIX_NEW_PARTY','TENDERIX_STATUS_UPDATE'";
        $rs = $DB->Query("SELECT count(*) C FROM b_event_type WHERE EVENT_NAME IN (" . $sIn . ") ", false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ar = $rs->Fetch();
        if ($ar["C"] <= 0) {
            include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/events/set_events.php");
        }
        return true;
    }

    function UnInstallEvents() {
        global $DB;
        include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/events/del_events.php");
        return true;
    }

    function InstallFiles() {
        global $DB;

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/images/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/themes/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/public/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/pweb.tenderix", true, true);

        //copy public scripts
        $arSITE_ID = "";
        $sites = CLang::GetList($by, $order, Array("ACTIVE" => "Y"));
        while ($site = $sites->Fetch()) {
            if ($_REQUEST["site_lid"] == $site["LID"]) {
                $arSITE_ID = $site["LID"];
                $DOC_ROOT = (strlen($site["DOC_ROOT"]) <= 0) ? $_SERVER["DOCUMENT_ROOT"] : $site["DOC_ROOT"];
                $ldir = $site['LANGUAGE_ID'] == 'ru' ? 'ru' : 'en';
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/public/site/$ldir/", $DOC_ROOT . $_REQUEST["path_site"], true, true);
            }
        }

        if (!empty($arSITE_ID)) {
            if (strlen($_REQUEST["template_id"]) <= 0)
                $_REQUEST["template_id"] = "tenderix";

            //Copy Template
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/public/template/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . $_REQUEST["template_id"] . "/", true, true);

            $path = $_REQUEST["path_site"];
            if (strlen($path) <= 0)
                return false;

            if (substr($path, -1, 1) != "/")
                $path .= "/";

            $cond = "CSite::InDir('" . $path . "')";

            $DB->Query(
                    "INSERT INTO b_site_template(SITE_ID, " . CMain::__GetConditionFName() . ", SORT, TEMPLATE) " .
                    "VALUES('" . $DB->ForSQL($arSITE_ID) . "', '" . $DB->ForSQL($cond, 255) . "', '100', '" . $DB->ForSQL(trim($_REQUEST["template_id"]), 255) . "')", true);
        }

        return true;
    }

    function UnInstallFiles() {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/", true, true);
        DeleteDirFilesEx("/bitrix/images/pweb.tenderix");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/themes/.default", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default");
        DeleteDirFilesEx("/bitrix/themes/.default/icons/pweb.tenderix");
        DeleteDirFilesEx("/bitrix/js/pweb.tenderix/");
        return true;
    }

    function InstallDB($arParams = array()) {
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        // Database tables creation
        if (!$DB->Query("SELECT 'x' FROM b_tx_company WHERE 1=0", true)) {
            $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/db/" . $DBType . "/install.sql");

            //demo
            $DB->Add("b_tx_supplier_status", array("C_SORT"=>100,"ACTIVE"=>"Y","TITLE"=>GetMessage("DATA_STATUS1"),"AUTH"=>"Y","PART"=>"N"), array());
            $DB->Add("b_tx_supplier_status", array("C_SORT"=>200,"ACTIVE"=>"Y","TITLE"=>GetMessage("DATA_STATUS2"),"AUTH"=>"Y","PART"=>"Y"), array());
            $DB->Add("b_tx_supplier_status", array("C_SORT"=>300,"ACTIVE"=>"Y","TITLE"=>GetMessage("DATA_STATUS3"),"AUTH"=>"Y","PART"=>"Y"), array());
            $DB->Add("b_tx_supplier_status", array("C_SORT"=>400,"ACTIVE"=>"Y","TITLE"=>GetMessage("DATA_STATUS4"),"AUTH"=>"Y","PART"=>"Y"), array());
            $DB->Add("b_tx_supplier_status", array("C_SORT"=>500,"ACTIVE"=>"Y","TITLE"=>GetMessage("DATA_STATUS5"),"AUTH"=>"N","PART"=>"N"), array());

            $SPR1_ID = $DB->Add("b_tx_spr", array("C_SORT"=>100,"ACTIVE"=>"N","TITLE"=>GetMessage("DATA_SPR1")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR1_ID,"C_SORT"=>100,"TITLE"=>GetMessage("DATA_SPR_DETAILS1_1")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR1_ID,"C_SORT"=>200,"TITLE"=>GetMessage("DATA_SPR_DETAILS1_2")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR1_ID,"C_SORT"=>300,"TITLE"=>GetMessage("DATA_SPR_DETAILS1_3")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR1_ID,"C_SORT"=>400,"TITLE"=>GetMessage("DATA_SPR_DETAILS1_4")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR1_ID,"C_SORT"=>500,"TITLE"=>GetMessage("DATA_SPR_DETAILS1_5")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR1_ID,"C_SORT"=>600,"TITLE"=>GetMessage("DATA_SPR_DETAILS1_6")), array());

            $SPR2_ID = $DB->Add("b_tx_spr", array("C_SORT"=>200,"ACTIVE"=>"N","TITLE"=>GetMessage("DATA_SPR2")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR2_ID,"C_SORT"=>100,"TITLE"=>GetMessage("DATA_SPR_DETAILS2_1")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR2_ID,"C_SORT"=>200,"TITLE"=>GetMessage("DATA_SPR_DETAILS2_2")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR2_ID,"C_SORT"=>300,"TITLE"=>GetMessage("DATA_SPR_DETAILS2_3")), array());

            $SPR3_ID = $DB->Add("b_tx_spr", array("C_SORT"=>300,"ACTIVE"=>"N","TITLE"=>GetMessage("DATA_SPR3")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR3_ID,"C_SORT"=>100,"TITLE"=>GetMessage("DATA_SPR_DETAILS3_1")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR3_ID,"C_SORT"=>200,"TITLE"=>GetMessage("DATA_SPR_DETAILS3_2")), array());
            $DB->Add("b_tx_spr_details", array("SPR_ID"=>$SPR3_ID,"C_SORT"=>300,"TITLE"=>GetMessage("DATA_SPR_DETAILS3_3")), array());

            $DB->Add("b_tx_section", array("C_SORT"=>100,"TITLE"=>GetMessage("DATA_SECTION1"),"ACTIVE"=>"Y"), array());
            $DB->Add("b_tx_section", array("C_SORT"=>200,"TITLE"=>GetMessage("DATA_SECTION2"),"ACTIVE"=>"Y"), array());

            //demo

            COption::SetOptionString("pweb.tenderix", "PW_TD_OPTIONS_SPR_UNIT", $SPR1_ID);
            COption::SetOptionString("pweb.tenderix", "PW_TD_OPTIONS_SPR_TERM_DELIVERY", $SPR2_ID);
            COption::SetOptionString("pweb.tenderix", "PW_TD_OPTIONS_SPR_TERM_PAYMENT", $SPR3_ID);

            $group = new CGroup;

            $arGroups = Array(
                Array(
                    "ACTIVE" => "Y",
                    "C_SORT" => 10,
                    "NAME" => GetMessage("PW_TD_BUYER_GROUP_NAME"),
                    "DESCRIPTION" => GetMessage("PW_TD_BUYER_GROUP_DESC"),
                    "STRING_ID" => "USER_TENDER_BUYER"
                ),
                Array(
                    "ACTIVE" => "Y",
                    "C_SORT" => 20,
                    "NAME" => GetMessage("PW_TD_SUPPLIER_GROUP_NAME"),
                    "DESCRIPTION" => GetMessage("PW_TD_SUPPLIER_GROUP_DESC"),
                    "STRING_ID" => "USER_TENDER_SUPPLIER"
                ),
                Array(
                    "ACTIVE" => "Y",
                    "C_SORT" => 30,
                    "NAME" => GetMessage("PW_TD_ADMIN_GROUP_NAME"),
                    "DESCRIPTION" => GetMessage("PW_TD_ADMIN_GROUP_DESC"),
                    "STRING_ID" => "USER_TENDER_ADMIN"
                )
            );

            foreach ($arGroups as $arKey => $arGroup) {
                $rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", Array("STRING_ID" => $arGroup["STRING_ID"]));
                if (!$rsGroup->Fetch()) {
                    $idGroup = $group->Add($arGroup);
                    if (!$idGroup) {
                        $APPLICATION->ThrowException($group->LAST_ERROR);
                        return false;
                    } else {
                        if ($arKey == 0) {
                            COption::SetOptionString("pweb.tenderix", "PW_TD_BUYER_GROUPS", "1");
                            COption::SetOptionString("pweb.tenderix", "PW_TD_BUYER_GROUPS_DEFAULT", $idGroup);
                            $APPLICATION->SetGroupRight("pweb.tenderix", $idGroup, "S");
                        }
                        if ($arKey == 1) {
                            COption::SetOptionString("pweb.tenderix", "PW_TD_SUPPLIER_GROUPS", "1");
                            COption::SetOptionString("pweb.tenderix", "PW_TD_SUPPLIER_GROUPS_DEFAULT", $idGroup);
                            $APPLICATION->SetGroupRight("pweb.tenderix", $idGroup, "P");
                        }
                        if ($arKey == 2) {
                            COption::SetOptionString("pweb.tenderix", "PW_TD_ADMIN_GROUPS", "1");
                            COption::SetOptionString("pweb.tenderix", "PW_TD_ADMIN_GROUPS_DEFAULT", $idGroup);
                            $APPLICATION->SetGroupRight("pweb.tenderix", $idGroup, "W");
                        }
                    }
                }
            }
        }

        if ($this->errors !== false) {
            $APPLICATION->ThrowException(implode("<br>", $this->errors)); 
            return false;
        } else {
            RegisterModuleDependences("main", "OnBuildGlobalMenu", "pweb.tenderix", "CTenderix", "TenderGlobalMenu");
            RegisterModuleDependences("main", "OnUserDelete", "pweb.tenderix", "CTenderix", "TenderixUserDelete");
            RegisterModule("pweb.tenderix");
            CModule::IncludeModule("pweb.tenderix");

            CAgent::Add(array(
                "NAME" => "CTenderixCurrency::Add();",
                "MODULE_ID" => "pweb.tenderix",
                "ACTIVE" => "Y",
                "NEXT_EXEC" => date("d.m.Y H:i:s", mktime(0, 0, 0, date("m"), date("j"), date("Y"))),
                "AGENT_INTERVAL" => 86400,
                "IS_PERIOD" => "Y"
            ));

            return true;
        }
    }

    function UnInstallDB($arParams = array()) {
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;
        if (!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y") {
            $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/db/" . $DBType . "/uninstall.sql");
            if ($this->errors !== false) {
                $APPLICATION->ThrowException(implode("", $this->errors));
                return false;
            }
        }

        CAgent::RemoveModuleAgents("pweb.tenderix");
        UnRegisterModule("pweb.tenderix");

        if (!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y") {
            $group = new CGroup;

            $arGroups = Array(
                Array(
                    "STRING_ID" => "USER_TENDER_BUYER"
                ),
                Array(
                    "STRING_ID" => "USER_TENDER_SUPPLIER"
                ),
                Array(
                    "STRING_ID" => "USER_TENDER_ADMIN"
                )
            );

            foreach ($arGroups as $arGroup) {
                $rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", Array("STRING_ID" => $arGroup["STRING_ID"]));
                if ($arrGroup = $rsGroup->Fetch()) {
                    $success = (bool) $group->Delete($arrGroup["ID"]);
                    if (!$success) {
                        $APPLICATION->ThrowException($group->LAST_ERROR);
                        return false;
                    }
                }
            }

            COption::RemoveOption("pweb.tenderix", "PW_TD_BUYER_GROUPS");
            COption::RemoveOption("pweb.tenderix", "PW_TD_SUPPLIER_GROUPS");
            COption::RemoveOption("pweb.tenderix", "PW_TD_ADMIN_GROUPS");

            COption::RemoveOption("pweb.tenderix", "PW_TD_BUYER_GROUPS_DEFAULT");
            COption::RemoveOption("pweb.tenderix", "PW_TD_SUPPLIER_GROUPS_DEFAULT");
            COption::RemoveOption("pweb.tenderix", "PW_TD_ADMIN_GROUPS_DEFAULT");
        }

        UnRegisterModuleDependences("main", "OnBuildGlobalMenu", "pweb.tenderix", "CTenderix", "TenderGlobalMenu");
        UnRegisterModuleDependences("main", "OnUserDelete", "pweb.tenderix", "CTenderix", "TenderixUserDelete");

        return true;
    }

    function DoInstall() { 
        global $DB, $APPLICATION, $step;
        $TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
        $your_company = "";
        $GLOBALS["new_company"] = false;

        $your_company = $DB->ForSql(trim($_REQUEST["your_company"]));  
        if (!$DB->Query("SELECT 'x' FROM b_tx_company WHERE 1=0", true)) { 
            $GLOBALS["new_company"] = true;
            $error_req = $your_company != "" ? false : true;
        } else {
            $error_req = false;
        }

        if ($TENDERIXRIGHT == "W") {
            $step = IntVal($step); 
            if ($step < 2) { 
                $GLOBALS["install_step"] = 1;
                $APPLICATION->IncludeAdminFile(GetMessage("PW_TD_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/step.php");
            } elseif ($step == 2 && !$error_req) { 

                $this->InstallDB();
                $this->InstallEvents();
                $this->InstallFiles();

                if (strlen($your_company) > 0) { 
                    $DB->Add("b_tx_company", array("TITLE"=>$your_company), array());
                }

                $GLOBALS["errors"] = $this->errors;
                $GLOBALS["install_step"] = 2;
                $APPLICATION->IncludeAdminFile(GetMessage("PW_TD_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/step.php");
            } else {
                $GLOBALS["install_step"] = 2;
                $APPLICATION->IncludeAdminFile(GetMessage("PW_TD_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/step.php");
            }
        }
    }

    function DoUninstall() {
        global $DB, $APPLICATION, $step;
        $TENDERIXRIGHT = $APPLICATION->GetGroupRight("pweb.tenderix");
        if ($TENDERIXRIGHT == "W") {
            if ($step < 2) {
                $GLOBALS["uninstall_step"] = 1;
                $APPLICATION->IncludeAdminFile(GetMessage("PW_TD_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/unstep.php");
            } elseif ($step == 2) {
                $this->UnInstallDB(array(
                    "savedata" => $_REQUEST["savedata"],
                ));
                if ($_REQUEST["save_templates"] != "Y") {
                    $this->UnInstallEvents();
                }

                $this->UnInstallFiles();
                $GLOBALS["errors"] = $this->errors;
                $GLOBALS["uninstall_step"] = 2;
                $APPLICATION->IncludeAdminFile(GetMessage("PW_TD_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/install/unstep.php");
            }
        }
    }

    function GetModuleRightList() {
        $arr = array(
            "reference_id" => array("D", "P", "S", "W"),
            "reference" => array(
                "[D] " . GetMessage("PW_TD_PERM_D"),
                "[P] " . GetMessage("PW_TD_PERM_P"),
                "[S] " . GetMessage("PW_TD_PERM_S"),
                "[W] " . GetMessage("PW_TD_PERM_W")
            )
        );
        return $arr;
    }

}

?>