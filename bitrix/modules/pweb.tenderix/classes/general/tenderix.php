<?php

IncludeModuleLangFile(__FILE__);

class CTenderix {

    function TenderGlobalMenu() {
        $T_RIGHT = $GLOBALS["APPLICATION"]->GetUserRight("pweb.tenderix");
        if ($T_RIGHT == "W") {
            return array(
                "global_menu_tender" => array(
                    "icon" => "tenderix_index_icon",
                    "page_icon" => "tenderix_page_icon",
                    "index_icon" => "tenderix_page_icon",
                    "text" => GetMessage("PW_TD_GLOBAL_MENU_TEXT"),
                    "title" => GetMessage("PW_TD_GLOBAL_MENU_TITLE"),
                    "url" => "tenderix_index.php?lang=" . LANG,
                    "sort" => 5000,
                    "items_id" => "global_menu_tender",
                    "items" => array(
                        array(
                            "text" => GetMessage("PW_TD_LOT"),
                            "url" => "tenderix_lot.php?lang=" . LANG,
                            "more_url" => array("tenderix_lot_edit.php", "tenderix_proposal.php", "tenderix_proposal_property.php", "tenderix_proposal_property_edit.php"),
                            "title" => GetMessage("PW_TD_LOT_ALT"),
                            "icon" => "tenderix_menu_icon_lot",
                            "page_icon" => "tenderix_page_icon_lot",
                            "sort" => 100,
                            "items_id" => "menu_tenderix_lot",
                            "items" => array(
                                array(
                                    "text" => GetMessage("PW_TD_LOT_LIST"),
                                    "url" => "tenderix_lot.php?lang=" . LANGUAGE_ID,
                                    "title" => GetMessage("PW_TD_LOT_LIST_ALT"),
                                    "items_id" => "menu_tenderix_lot_list",
                                ),
                                array(
                                    "text" => GetMessage("PW_TD_PROPOSAL_PROPERTY"),
                                    "url" => "tenderix_proposal_property.php?lang=" . LANGUAGE_ID,
                                    "title" => GetMessage("PW_TD_PROPOSAL_PROPERTY_ALT"),
                                    "items_id" => "menu_tenderix_proposal_property",
                                )
                            )
                        ),
                        array(
                            "text" => GetMessage("PW_TD_COMPANY"),
                            "url" => "tenderix_company.php?lang=" . LANG,
                            "more_url" => array("tenderix_company_edit.php"),
                            "title" => GetMessage("PW_TD_COMPANY_ALT"),
                            "icon" => "tenderix_menu_icon_company",
                            "page_icon" => "tenderix_page_icon_company",
                            "sort" => 200
                        ),
                        array(
                            "text" => GetMessage("PW_TD_SECTION"),
                            "url" => "tenderix_section.php?lang=" . LANG,
                            "more_url" => array("tenderix_section_edit.php"),
                            "title" => GetMessage("PW_TD_SECTION_ALT"),
                            "icon" => "tenderix_menu_icon_sections",
                            "page_icon" => "tenderix_page_icon_sections",
                            "sort" => 300
                        ),
                        array(
                            "text" => GetMessage("PW_TD_SPR"),
                            "url" => "tenderix_spr.php?lang=" . LANG,
                            "more_url" => array("tenderix_spr_edit.php", "tenderix_spr_details.php", "tenderix_spr_details_edit.php"),
                            "title" => GetMessage("PW_TD_SPR_ALT"),
                            "icon" => "tenderix_menu_icon_dictionaries",
                            "page_icon" => "tenderix_page_icon_dictionaries",
                            "sort" => 400
                        ),
                        array(
                            "text" => GetMessage("PW_TD_PRODUCTS"),
                            "url" => "tenderix_products.php?lang=" . LANG,
                            "more_url" => array("tenderix_products_edit.php", "tenderix_products_property.php", "tenderix_products_property_edit.php"),
                            "title" => GetMessage("PW_TD_PRODUCTS_ALT"),
                            "icon" => "tenderix_menu_icon_products",
                            "page_icon" => "tenderix_page_icon_products",
                            "sort" => 500
                        ),
                        array(
                            "text" => GetMessage("PW_TD_USER"),
                            "url" => "tenderix_user.php?lang=" . LANG,
                            "more_url" => array(),
                            "title" => GetMessage("PW_TD_USER_ALT"),
                            "items_id" => "menu_tenderix_user",
                            "icon" => "tenderix_menu_icon_users",
                            "page_icon" => "tenderix_page_icon_users",
                            "sort" => 600,
                            "items" => array(
                                array(
                                    "text" => GetMessage("PW_TD_USER_SUPPLIER"),
                                    "url" => "tenderix_users_supplier_index.php?lang=" . LANGUAGE_ID,
                                    "more_url" => array(),
                                    "title" => GetMessage("PW_TD_USER_SUPPLIER_ALT"),
                                    "items_id" => "menu_tenderix_user_supplier",
                                    "page_icon" => "tenderix_page_icon_users",
                                    "items" => array(
                                        array(
                                            "text" => GetMessage("PW_TD_USER_SUPPLIER"),
                                            "url" => "tenderix_users_supplier.php?lang=" . LANGUAGE_ID,
                                            "more_url" => array("tenderix_users_supplier_edit.php"),
                                            "title" => GetMessage("PW_TD_USER_SUPPLIER_ALT")
                                        ),
                                        array(
                                            "text" => GetMessage("PW_TD_USER_SUPPLIER_EX"),
                                            "url" => "tenderix_users_supplier_property.php?lang=" . LANGUAGE_ID,
                                            "more_url" => array("tenderix_users_supplier_property_edit.php"),
                                            "title" => GetMessage("PW_TD_USER_SUPPLIER_EX_ALT")
                                        ),
                                        array(
                                            "text" => GetMessage("PW_TD_USER_SUPPLIER_SPR"),
                                            "url" => "tenderix_users_supplier_status.php?lang=" . LANGUAGE_ID,
                                            "more_url" => array("tenderix_users_supplier_status_edit.php"),
                                            "title" => GetMessage("PW_TD_USER_SUPPLIER_SPR_ALT")
                                        )
                                    )
                                ),
                                array(
                                    "text" => GetMessage("PW_TD_USER_BUYER"),
                                    "url" => "tenderix_users_buyer.php?lang=" . LANGUAGE_ID,
                                    "more_url" => array("tenderix_users_buyer_edit.php"),
                                    "title" => GetMessage("PW_TD_USER_BUYER_ALT")
                                ),
                            )
                        ),
                        array(
                            "text" => GetMessage("PW_TD_LOG"),
                            "url" => "tenderix_log.php?lang=" . LANG,
                            "more_url" => array(),
                            "title" => GetMessage("PW_TD_LOG_ALT"),
                            "icon" => "tenderix_menu_icon_log",
                            "page_icon" => "tenderix_page_icon_log",
                            "sort" => 700
                        ),
                    /* array(
                      "text" => GetMessage("PW_TD_TOOLS"),
                      "url" => "tenderix_tools.php?lang=" . LANG,
                      "more_url" => array(),
                      "title" => GetMessage("PW_TD_TOOLS_ALT"),
                      "items_id" => "menu_tenderix_tools",
                      "icon" => "tenderix_menu_icon_tools",
                      "page_icon" => "tenderix_page_icon_tools",
                      "sort" => 800,
                      "items" => array(
                      array(
                      "text" => GetMessage("PW_TD_TOOLS_EXPORT"),
                      "url" => "tenderix_export_csv.php?lang=" . LANGUAGE_ID,
                      "title" => GetMessage("PW_TD_TOOLS_EXPORT_ALT"),
                      "items_id" => "menu_tenderix_tools_export",
                      "items" => array(
                      array(
                      "text" => GetMessage("PW_TD_TOOLS_EXPORT_CSV"),
                      "url" => "tenderix_export_csv.php?lang=" . LANGUAGE_ID,
                      "title" => GetMessage("PW_TD_TOOLS_EXPORT_ALT")
                      ),
                      array(
                      "text" => GetMessage("PW_TD_TOOLS_EXPORT_XML"),
                      "url" => "tenderix_export_xml.php?lang=" . LANGUAGE_ID,
                      "title" => GetMessage("PW_TD_TOOLS_EXPORT_XML_ALT")
                      ),
                      )
                      ),
                      array(
                      "text" => GetMessage("PW_TD_TOOLS_IMPORT"),
                      "url" => "tenderix_import_csv.php?lang=" . LANGUAGE_ID,
                      "title" => GetMessage("PW_TD_TOOLS_IMPORT_ALT"),
                      "items_id" => "menu_tenderix_tools_import",
                      "items" => array(
                      array(
                      "text" => GetMessage("PW_TD_TOOLS_IMPORT_CSV"),
                      "url" => "tenderix_import_csv.php?lang=" . LANGUAGE_ID,
                      "title" => GetMessage("PW_TD_TOOLS_IMPORT_ALT")
                      ),
                      array(
                      "text" => GetMessage("PW_TD_TOOLS_IMPORT_XML"),
                      "url" => "tenderix_import_xml.php?lang=" . LANGUAGE_ID,
                      "title" => GetMessage("PW_TD_TOOLS_IMPORT_XML_ALT")
                      ),
                      )
                      ),
                      )
                      ), */
                    )
                )
            );
        }
    }

    function AddPagerSettings(&$arComponentParameters, $pager_title, $bDescNumbering = true, $bShowAllParam = false) {
        $arComponentParameters["GROUPS"]["PAGER_SETTINGS"] = array(
            "NAME" => GetMessage("PW_TD_DESC_PAGER_SETTINGS"),
        );
        $arComponentParameters["PARAMETERS"]["DISPLAY_TOP_PAGER"] = Array(
            "PARENT" => "PAGER_SETTINGS",
            "NAME" => GetMessage("PW_TD_DESC_TOP_PAGER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        );
        $arComponentParameters["PARAMETERS"]["DISPLAY_BOTTOM_PAGER"] = Array(
            "PARENT" => "PAGER_SETTINGS",
            "NAME" => GetMessage("PW_TD_DESC_BOTTOM_PAGER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        );
        $arComponentParameters["PARAMETERS"]["PAGER_TITLE"] = Array(
            "PARENT" => "PAGER_SETTINGS",
            "NAME" => GetMessage("PW_TD_DESC_PAGER_TITLE"),
            "TYPE" => "STRING",
            "DEFAULT" => $pager_title,
        );
        $arComponentParameters["PARAMETERS"]["PAGER_SHOW_ALWAYS"] = Array(
            "PARENT" => "PAGER_SETTINGS",
            "NAME" => GetMessage("PW_TD_DESC_PAGER_SHOW_ALWAYS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        );
        $arComponentParameters["PARAMETERS"]["PAGER_TEMPLATE"] = Array(
            "PARENT" => "PAGER_SETTINGS",
            "NAME" => GetMessage("PW_TD_DESC_PAGER_TEMPLATE"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        );

        if ($bDescNumbering) {
            $arComponentParameters["PARAMETERS"]["PAGER_DESC_NUMBERING"] = Array(
                "PARENT" => "PAGER_SETTINGS",
                "NAME" => GetMessage("PW_TD_DESC_PAGER_DESC_NUMBERING"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "N",
            );
            $arComponentParameters["PARAMETERS"]["PAGER_DESC_NUMBERING_CACHE_TIME"] = Array(
                "PARENT" => "PAGER_SETTINGS",
                "NAME" => GetMessage("PW_TD_DESC_PAGER_DESC_NUMBERING_CACHE_TIME"),
                "TYPE" => "STRING",
                "DEFAULT" => "36000",
            );
        }

        if ($bShowAllParam) {
            $arComponentParameters["PARAMETERS"]["PAGER_SHOW_ALL"] = Array(
                "PARENT" => "PAGER_SETTINGS",
                "NAME" => GetMessage("PW_TD_DESC_SHOW_ALL"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "N",
            );
        }
    }

    function formatPrice($price, $type = 0) {
        switch ($type) {
            case 0:
                $res = number_format($price, 2, '.', ' ');
                break;
            case 1:
                $res = number_format($price, 2, '.', '');
                break;
            case 2:
                $res = number_format($price, 2, ',', ' ');
                break;
            case 3:
                $res = number_format($price, 2, ',', '');
                break;
        }
        return $res;
    }

    function TenderixUserDelete($USER_ID) {
        CTenderixUserSupplier::Delete($USER_ID);
        CTenderixUserBuyer::Delete($USER_ID);
    }

    //price with NDS
    function PriceNDSy($price, $nds) {
        return (($price * $nds) / 100) + $price;
    }

    //price without NDS
    function PriceNDSn($price, $nds) {
        return $price - (($price * $nds) / (100 + $nds));
    }

}

?>