<?

IncludeModuleLangFile(__FILE__);

if ($APPLICATION->GetGroupRight("pweb.tenderix") != "D") {

    /*$aMenu = array(
        "parent_menu" => "global_menu",
        "section" => "tender",
        "module_id" => "pweb.tenderix",
        "sort" => 900,
        "text" => GetMessage("PW_TD_TENDER"),
        "url" => "/bitrix/admin/tenderix_index.php?lang=" . LANG,
        "title" => GetMessage("PW_TD_CONTROL"),
        "icon" => "tenderix_menu_icon",
        "page_icon" => "tenderix_page_icon",
        "items_id" => "menu_tender",
        "items" => array(
            array(
                "text" => GetMessage("PW_TD_LOT"),
                "url" => "tenderix_lot.php?lang=" . LANG,
                "more_url" => array("tenderix_lot_edit.php"),
                "title" => GetMessage("PW_TD_LOT_ALT")
            ),
            array(
                "text" => GetMessage("PW_TD_COMPANY"),
                "url" => "tenderix_company.php?lang=" . LANG,
                "more_url" => array("tenderix_company_edit.php"),
                "title" => GetMessage("PW_TD_COMPANY_ALT")
            ),
            array(
                "text" => GetMessage("PW_TD_SECTION"),
                "url" => "tenderix_section.php?lang=" . LANG,
                "more_url" => array("tenderix_section_edit.php"),
                "title" => GetMessage("PW_TD_SECTION_ALT")
            ),
            array(
                "text" => GetMessage("PW_TD_SPR"),
                "url" => "tenderix_spr.php?lang=" . LANG,
                "more_url" => array("tenderix_spr_edit.php", "tenderix_spr_details.php", "tenderix_spr_details_edit.php"),
                "title" => GetMessage("PW_TD_SPR_ALT")
            ),
            array(
                "text" => GetMessage("PW_TD_PRODUCTS"),
                "url" => "tenderix_products.php?lang=" . LANG,
                "more_url" => array("tenderix_products_edit.php", "tenderix_products_property.php", "tenderix_products_property_edit.php"),
                "title" => GetMessage("PW_TD_PRODUCTS_ALT")
            ),
            array(
                "text" => GetMessage("PW_TD_USER"),
                "url" => "tenderix_user.php?lang=" . LANG,
                "more_url" => array(),
                "title" => GetMessage("PW_TD_USER_ALT"),
                "items_id" => "menu_tenderix_user",
                "page_icon" => "tenderix_page_icon",
                "items" => array(
                    array(
                        "text" => GetMessage("PW_TD_USER_SUPPLIER"),
                        "url" => "tenderix_users_supplier_index.php?lang=" . LANGUAGE_ID,
                        "more_url" => array(),
                        "title" => GetMessage("PW_TD_USER_SUPPLIER_ALT"),
                        "items_id" => "menu_tenderix_user_supplier",
                        "page_icon" => "tenderix_page_icon",
                        "items" => array(
                            array(
                                "text" => GetMessage("PW_TD_USER_SUPPLIER"),
                                "url" => "tenderix_users_supplier.php?lang=" . LANGUAGE_ID,
                                "more_url" => array("tenderix_users_supplier_edit.php"),
                                "title" => GetMessage("PW_TD_USER_SUPPLIER_ALT")
                            ),
                            array(
                                "text" => GetMessage("PW_TD_USER_SUPPLIER_SPR"),
                                "url" => "tenderix_users_supplier_spr.php?lang=" . LANGUAGE_ID,
                                "more_url" => array("tenderix_users_supplier_spr_edit.php"),
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
            )
        )
    );
    */
    return $aMenu;
}
return false;
?>
