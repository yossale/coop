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
    
    $endDate = new DateTime();
    $endDate->setISODate($params['year'], $params['week'], 0);
    $endDate->add(new DateInterval('P6D'));
    $endDate_unixTime = $endDate->getTimestamp();

    $smarty->assign('weekStartDate', $startDate_unixTime);
    $smarty->assign('weekEndDate', $endDate_unixTime);
}
?>