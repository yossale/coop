<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File: function.weeknumber_formatter.php
 * Type: function
 * Name: weeknumber_formatter
 * Purpose: format weeknumber date to specific date range
 * -------------------------------------------------------------
 */
function smarty_function_weeknumber_formatter ($params, &$smarty)
{
    $startDate = new DateTime();
    $startDate->setISODate($params['year'], $params['week'], 0);
    $startDate_unixTime = $startDate->getTimestamp();
    $startDate_str = strftime("%F", $startDate_unixTime);
    
    $endDate = new DateTime();
    $endDate->setISODate($params['year'], $params['week'], 0);
    $endDate->add(new DateInterval('P6D'));
    $endDate_unixTime = $endDate->getTimestamp();
    $endDate_str = strftime("%F", $endDate_unixTime);
    
    $result = "בין התאריכים:" . $startDate_str . " - " . $endDate_str;
    
    return $result;
}
?>