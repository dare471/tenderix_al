<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
// CRM server conection data
define('CRM_HOST', 'tenderix.bitrix24.ru'); // your CRM domain name
define('CRM_PORT', '443'); // CRM server port
define('CRM_PATH', '/crm/configs/import/lead.php'); // CRM server REST service path

// CRM server authorization data
define('CRM_LOGIN', 's.kustov@tenderix.ru'); // login of a CRM user able to manage leads
define('CRM_PASSWORD', 'Cnfc160172'); // password of a CRM user
// OR you can send special authorization hash which is sent by server after first successful connection with login and password
//define('CRM_AUTH', 'e54ec19f0c5f092ea11145b80f465e1a'); // authorization hash

/********************************************************************************************/
// POST processing

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$leadData = $_POST['DATA'];

	// get lead data from the form
	$postData = array(
		'TITLE' => 'Заявка с сайта',
		'NAME' => $leadData['name'],
		'SOURCE_ID' => 'WEB',
		'PHONE_WORK' => $leadData['phone'],
		'EMAIL_WORK' => $leadData['email'],
	);

	$arEventFields = array(
		"NAME" => $leadData['name'],
		"EMAIL" => $leadData['email'],
		"OPERATOR_LOGIN" => 'operator',
		"SUPPLIER_LOGIN" => 'supplier',
		"SUPPLIER2_LOGIN" => 'supplier2',
		"MANAGER_LOGIN" => 'manager',
			"PASSWORD" => '*kpkgG7wKp'
	);

	$arrSITE = 's1';
	if(CEvent::Send("NEW_DEMO_REQUEST", $arrSITE, $arEventFields, "N")) {
		$postData['COMMENTS'] = 'Email - '.$leadData['email'].', телефон -'.$leadData['phone'].', Узнали - '.$leadData['info'].'<br/> Письмо с доступом клиенту отправлено.';
		$result_script = 'success';
	} else {
		$postData['COMMENTS'] = 'Email - '.$leadData['email'].', телефон -'.$leadData['phone'].', Узнали - '.$leadData['info'].'<br/> Письмо с доступом клиенту не отправлено.';
		$result_script = 'email not send';
	}

	// append authorization data
	if (defined('CRM_AUTH'))
	{
		$postData['AUTH'] = CRM_AUTH;
	}
	else
	{
		$postData['LOGIN'] = CRM_LOGIN;
		$postData['PASSWORD'] = CRM_PASSWORD;
	}

	// open socket to CRM
	$fp = fsockopen("ssl://".CRM_HOST, CRM_PORT, $errno, $errstr, 30);
	if ($fp)
	{
		// prepare POST data
		$strPostData = '';
		foreach ($postData as $key => $value)
			$strPostData .= ($strPostData == '' ? '' : '&').$key.'='.urlencode($value);

		// prepare POST headers
		$str = "POST ".CRM_PATH." HTTP/1.0\r\n";
		$str .= "Host: ".CRM_HOST."\r\n";
		$str .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$str .= "Content-Length: ".strlen($strPostData)."\r\n";
		$str .= "Connection: close\r\n\r\n";

		$str .= $strPostData;

		// send POST to CRM
		fwrite($fp, $str);

		// get CRM headers
		$result = '';
		while (!feof($fp))
		{
			$result .= fgets($fp, 128);
		}
		fclose($fp);

	}
	else
	{
		$result_script = 'error';
	}
}
else
{
	$result_script = 'error_module';
}

echo $result_script;
?>