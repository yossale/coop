<?
define('CRONJOB', true);
require_once("../public/index.php");

header("Content-Type", "text/html; charset=windows-1255");

$adapter = Zend_Db_Table::getDefaultAdapter();
$adapter->query("SET NAMES utf8");

$coop_coops = new Coop_Coops();
$coops = $coop_coops->getAllCoops();

foreach ($coops as $coop)
{
	echo "Checking coop " . $coop['coop_name'] . " id " . $coop['coop_id'];
	
	$its_reset_day = $coop['coop_reset_day'] == date('w');
	$got_reset_today = $coop['coop_last_reset_day'] == date('Y-m-d');
	
	if ($its_reset_day && !$got_reset_today)
	{
		$success = $coop_coops->resetCoop($coop['coop_id']);
		
		if ($success)
		{
			$reset_day = $coop_coops->getLastResetDay($coop['coop_id']);
			echo " - Success - Saved Report for $reset_day";
		}
		else
		{
			echo " - Failed\n";
		}
		return;
	}	
	else if (!$its_reset_day)
	{
		echo " - It's not reset day";
	}
    else if ($got_reset_today)
    {
        echo "- Already got reset today";
    }
    echo "\n";
}