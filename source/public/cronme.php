<?
if (isset($_GET['job']))
{
	$job = $_GET['job'];

	switch ($_GET['job'])
	{
		case "open_coops":
			$script = "open_coops";
			break;
		
		case "send_emails":
			$script = "send_emails";
			break;
			
		case "reset_coop":
			$script = "reset_coop";
			break;
			
		case "move_reports_to_s3":
			$script = "move_reports_to_s3";
			break;

        case "close_coops":
            $script = "close_coops";
            break;

        case "special_script":
            $script = "special_script";
            break;
	}
	
	if (isset($script))
	{
		require_once("../cronjobs/" . $script . ".php");	
	}	
}
