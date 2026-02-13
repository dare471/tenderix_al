<?
$langs = CLanguage::GetList(($b=""), ($o=""));
while($lang = $langs->Fetch())
{
	$lid = $lang["LID"];
	IncludeModuleLangFile(__FILE__, $lid);

	$et = new CEventType;
	$et->Add(array(
		"LID" => $lid,
		"EVENT_NAME" => "TENDERIX_NEW_LOT",
		"NAME" => GetMessage("NEW_LOT_NAME"),
		"DESCRIPTION" => GetMessage("NEW_LOT_DESC"),
	));
        
    $et = new CEventType;
	$et->Add(array(
		"LID" => $lid,
		"EVENT_NAME" => "TENDERIX_NEW_PROPOSAL",
		"NAME" => GetMessage("NEW_PROPOSAL_NAME"),
		"DESCRIPTION" => GetMessage("NEW_PROPOSAL_DESC"),
	));
	
	$et = new CEventType;
    $et->Add(array(
        "LID" => $lid,
        "EVENT_NAME" => "TENDERIX_WIN_LOT",
        "NAME" => GetMessage("WIN_LOT_NAME"),
        "DESCRIPTION" => GetMessage("WIN_LOT_DESC"),
    ));

    $et = new CEventType;
    $et->Add(array(
        "LID" => $lid,
        "EVENT_NAME" => "TENDERIX_BEST_PROPOSAL",
        "NAME" => GetMessage("BEST_PROPOSAL_NAME"),
        "DESCRIPTION" => GetMessage("BEST_PROPOSAL_DESC"),
    ));
	
	$et = new CEventType;
    $et->Add(array(
        "LID" => $lid,
        "EVENT_NAME" => "TENDERIX_NEW_PARTY",
        "NAME" => GetMessage("NEW_PARTY_NAME"),
        "DESCRIPTION" => GetMessage("NEW_PARTY_DESC"),
    ));

    $et = new CEventType;
    $et->Add(array(
        "LID" => $lid,
        "EVENT_NAME" => "TENDERIX_STATUS_UPDATE",
        "NAME" => GetMessage("STATUS_UPDATE_NAME"),
        "DESCRIPTION" => GetMessage("STATUS_UPDATE_DESC"),
    ));

	$arSites = array();
	$sites = CSite::GetList(($b=""), ($o=""), Array("LANGUAGE_ID"=>$lid));
	while ($site = $sites->Fetch())
		$arSites[] = $site["LID"];

	if(count($arSites) > 0)
	{
		$emess = new CEventMessage;
		$emess->Add(array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => "TENDERIX_NEW_LOT",
			"LID" => $arSites,
			"EMAIL_FROM" => "#EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL_TO#",
			"BCC" => "",
			"SUBJECT" => GetMessage("NEW_LOT_SUBJECT"),
			"MESSAGE" => GetMessage("NEW_LOT_MESSAGE"),
			"BODY_TYPE" => "text",
		));
                
        $emess = new CEventMessage;
		$emess->Add(array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => "TENDERIX_NEW_PROPOSAL",
			"LID" => $arSites,
			"EMAIL_FROM" => "#EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL_TO#",
			"BCC" => "",
			"SUBJECT" => GetMessage("NEW_PROPOSAL_SUBJECT"),
			"MESSAGE" => GetMessage("NEW_PROPOSAL_MESSAGE"),
			"BODY_TYPE" => "text",
		));
		
		$emess = new CEventMessage;
        $emess->Add(array(
            "ACTIVE" => "Y",
            "EVENT_NAME" => "TENDERIX_WIN_LOT",
            "LID" => $arSites,
            "EMAIL_FROM" => "#EMAIL_FROM#",
            "EMAIL_TO" => "#EMAIL_TO#",
            "BCC" => "",
            "SUBJECT" => GetMessage("WIN_LOT_SUBJECT"),
            "MESSAGE" => GetMessage("WIN_LOT_MESSAGE"),
            "BODY_TYPE" => "text",
        ));

        $emess = new CEventMessage;
        $emess->Add(array(
            "ACTIVE" => "Y",
            "EVENT_NAME" => "TENDERIX_BEST_PROPOSAL",
            "LID" => $arSites,
            "EMAIL_FROM" => "#EMAIL_FROM#",
            "EMAIL_TO" => "#EMAIL_TO#",
            "BCC" => "",
            "SUBJECT" => GetMessage("BEST_PROPOSAL_SUBJECT"),
            "MESSAGE" => GetMessage("BEST_PROPOSAL_MESSAGE"),
            "BODY_TYPE" => "text",
        ));
		
		$emess = new CEventMessage;
        $emess->Add(array(
            "ACTIVE" => "Y",
            "EVENT_NAME" => "TENDERIX_NEW_PARTY",
            "LID" => $arSites,
            "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
            "EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
            "BCC" => "",
            "SUBJECT" => GetMessage("NEW_PARTY_SUBJECT"),
            "MESSAGE" => GetMessage("NEW_PARTY_MESSAGE"),
            "BODY_TYPE" => "text",
        ));

        $emess = new CEventMessage;
        $emess->Add(array(
            "ACTIVE" => "Y",
            "EVENT_NAME" => "TENDERIX_STATUS_UPDATE",
            "LID" => $arSites,
            "EMAIL_FROM" => "#EMAIL_FROM#",
            "EMAIL_TO" => "#EMAIL_TO#",
            "BCC" => "",
            "SUBJECT" => GetMessage("STATUS_UPDATE_SUBJECT"),
            "MESSAGE" => GetMessage("STATUS_UPDATE_MESSAGE"),
            "BODY_TYPE" => "text",
        ));
	}
}
?>