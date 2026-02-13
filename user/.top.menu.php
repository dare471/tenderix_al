<?
$aMenuLinks = Array(
	Array(
		"Торги", 
		"/user/index.php", 
		Array(), 
		Array(),  
		"\$GLOBALS['USER']->IsAuthorized()" 
	),
	Array(
		"Создать лот", 
		"/user/lot.php?TYPE_ID=N", 
		Array(), 
		Array(),  
		"\CSite::InGroup(array(8))" 
	),
	Array(
		"Статистика", 
		"/user/stat.php",
		Array(), 
		Array(), 
		"\CSite::InGroup(array(8))" 
	),
	Array(
		"Поставщики", 
		"/user/list_suppliers.php",
		Array(), 
		Array(), 
		"\CSite::InGroup(array(8))" 
	),
	Array(
		"Управление", 
		"/bitrix",
		Array(), 
		Array("target"=>"target=\"_blank\""), 
		"\CSite::InGroup(array(8))" 
	),
	Array(
		"Профиль", 
		"/user/profile.php",
		Array(), 
		Array(), 
		"!\CSite::InGroup(array(8)) && \$GLOBALS['USER']->IsAuthorized()" 
	),
	Array(
		"FAQ", 
		"/user/faq.php",
		Array(), 
		Array(), 
		"!\CSite::InGroup(array(8)) && \$GLOBALS['USER']->IsAuthorized()" 
	),
	
);
?>