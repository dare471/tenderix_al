<?

include("include/pwebru/classes/tenderix/subscr.php");
include("include/pwebru/classes/tenderix/subsp.php");

class SubscribeHandlers
{
    function GetMailHash($email)
    {
		$MAIL_SALT = 'WFZlUwzVHLA0Bx';
        return md5(md5($email) . $MAIL_SALT);
    }
}
?> 