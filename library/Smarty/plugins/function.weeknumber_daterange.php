<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File: function.weeknumber_daterange.php
 * Type: function
 * Name: weeknumber_daterange
 * Purpose: Calculate weeknumber date range. Start and end.
 * -------------------------------------------------------------
 */
function smarty_function_weeknumber_daterange ($params, &$smarty)
{
    $startDate = new DateTime();
    $startDate->setISODate($params['year'], $params['week'], 0);
    $startDate_unixTime = $startDate->getTimestamp();
    //$startDate_str = strftime("%F", $startDate_unixTime);
    
    $endDate = new DateTime();
    $endDate->setISODate($params['year'], $params['week'], 0);
    $endDate->add(new DateInterval('P6D'));
    $endDate_unixTime = $endDate->getTimestamp();
    //$endDate_str = strftime("%F", $endDate_unixTime);

    $smarty->assign('weekStartDate', $startDate_unixTime);
    $smarty->assign('weekEndDate', $endDate_unixTime);
}
?>