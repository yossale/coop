<?php
set_include_path(
        PROJECT_PATH . '/source/application/smarty/plugins' . PATH_SEPARATOR .
                 get_include_path());

require_once 'function.weeknumber_daterange.php';

class smarty_function_weeknumber_daterange_Test extends PHPUnit_Framework_TestCase
{

    public function testObjectsProvider ()
    {
        return array(
                array(
                        2014,
                        1,
                        new DateTime('2013-12-29'),
                        new DateTime('2014-1-4')
                ),
                array(
                        2014,
                        5,
                        new DateTime('2014-1-26'),
                        new DateTime('2014-2-1')
                )
        );
    }

    /**
     * @dataProvider testObjectsProvider
     */
    public function testFunction ($year, 
            $week, 
            $expected_StartDate, 
            $expected_EndDate)
    {
        // Build
        $params = array();
        $params['year'] = $year;
        $params['week'] = $week;
        $smarty = new Smarty();
        $smarty = $this->getMockBuilder('Smarty_Internal_Template')
            ->disableOriginalConstructor()
            ->getMock();
        
        // Mock
        $smarty->expects($this->exactly(2))
            ->method('assign')
            ->withConsecutive(
                array(
                        $this->equalTo('weekStartDate'),
                        $this->equalTo($expected_StartDate->getTimestamp())
                ), 
                array(
                        $this->equalTo('weekEndDate'),
                        $this->equalTo($expected_EndDate->getTimestamp())
                ));
        
        // Test
        smarty_function_weeknumber_daterange($params, $smarty);
        
        // Assert
    }
}
?>