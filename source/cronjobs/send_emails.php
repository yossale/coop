<?
define('CRONJOB', true);
require_once("../public/index.php");
use Mailgun\Mailgun;
header("Content-Type", "text/html; charset=windows-1255");
 
$adapter = Zend_Db_Table::getDefaultAdapter();
$adapter->query("SET NAMES utf8");

$coop_deliveries = new Coop_EmailDeliveries();
$coop_users = new Coop_Users();

$users = $coop_users->getAllUsersFromAllCoops();

$emails = $coop_deliveries->getAllUnsentDeliveries(50);


if ($emails != false)
{
    $config = Zend_Registry::get('config');
    $mailgun = $config->mailgun;

    $mg = new Mailgun($mailgun->key);

	foreach ($emails as $email)
	{
		// get current user
		$current_user = array();
		foreach ($users as $key => $data)
		{
			if ($data['user_id'] == $email['user_id'])
			{
				$current_user = $data;
			}
		}
		

        $recipient_email = $email['email_delivery_address'];
        $recipient_name = stripslashes($email['email_delivery_name']);
        $recipients = array("$recipient_name <$recipient_email>");
		for ($i = 1; $i <= 3; $i++)
		{
			if (!empty($email['email_delivery_cc' . $i]))
			{
                $another_email = $email['email_delivery_cc' . $i];
                $another_name = stripslashes($email['email_delivery_name']);
                $recipients[] = "$another_name <$another_email>";
			}
		}
        $recipients = implode(', ', $recipients);

        $subject = stripslashes($coop_deliveries->replaceTags($email['email_subject'], $current_user));
        $html = stripslashes($coop_deliveries->replaceTags($email['email_body'], $current_user));

        $from_email = stripslashes($email['email_from_email']);
        $from_name = stripslashes($email['email_from_name']);
        $from = "$from_name <$from_email>";

       //$from = stripslashes($email['email_from_name']) . ' <' . $mailgun->from_email . '>';
        $from = 'מערכת הקואופ' . ' <' . $mailgun->from_email . '>';

        if (empty($html))
        {
            $html = ' ';
        }

        $mg->sendMessage($mailgun->domain, array('from' => $from,
            'to'      => $recipients,
            'subject' => $subject,
            'html'    => $html));

        $coop_deliveries->setDeliveryAsSent($email['email_delivery_id']);
        echo "Sent Delivery #" . $email['email_delivery_id'] . "<br />";

	}
}
else 
{
	echo "Nothing to send";
}

/*
$mail = new Zend_Mail('windows-1255');
$mail->setFrom("me");
$mail->addTo("eyal.regular@gmail.com");
$mail->setSubject(date("Y-m-d H:i:s") . " cron was working");
$mail->setBodyHtml("hello");
$mail->send();*/