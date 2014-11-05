<?
define('CRONJOB', true);
require_once("../public/index.php");

header("Content-Type", "text/html; charset=windows-1255");

$adapter = Zend_Db_Table::getDefaultAdapter();
$adapter->query("SET NAMES utf8");

$coop_coops = new Coop_Coops();
$coops = $coop_coops->getAllCoops();

$current_day = date("w");
$current_time = date("H:i:s");

foreach ($coops as $coop)
{
	debug("Checking coop " . $coop['coop_name'] . " id " . $coop['coop_id']);
	$is_open = checkEquals(false, $coop['coop_is_open_now'], "coop_is_open_now", 1, "static", "is open now");
	if ($is_open)
	{
		// do nothing...
	}
	else
	{
		$is_openning_day = checkEquals(false, $current_day, "current_day", $coop['coop_open_day'], "coop_open_day", "open day");
		if ($is_openning_day)
		{
			$is_openning_hour = checkEquals(false, substr($current_time, 0, 2), "current_time", substr($coop['coop_open_time'], 0, 2), "coop_open_time", "open hour");
			if ($is_openning_hour)
			{
				$is_after_openning_mins = checkEquals(true, substr($current_time, 3, 2), "current_time", substr($coop['coop_open_time'], 3, 2), "coop_open_time", "open minutes");
				if ($is_after_openning_mins)
				{
					changeCoopOpenStatus($coop['coop_id'], 1);
				}
			}
		}
	}
	debug("");
}

function changeCoopOpenStatus($coop_id, $new_status)
{
    $action = ($new_status == 0) ? "Closing" : "Openning";
    debug("$action the coop.");

    $coop_coops = new Coop_Coops();
    $coop_coops->editCoop($coop_id, array("coop_is_open_now" => $new_status));

    debug("Change successed.");
}


function checkEquals($alsoBigger, $a, $aName, $b, $bName, $descriptor)
{
	if ($alsoBigger)
		$attempt = ($a >= $b);
	else 
		$attempt = ($a == $b);
	
	if ($attempt)
		debug("It's $descriptor. ($aName $a vs. $bName $b)");
	else
		debug("It's not $descriptor ($aName $a vs. $bName $b)");
	
	return $attempt;
}

function debug($text)
{
	$debug = true;
	if ($debug)
	{
		echo $text . "\n";
	}
}
