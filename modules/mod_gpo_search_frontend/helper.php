<?php

class modgposearchfrontendhelper
{
    /**
     * Retrieves the hello message
     *
     * @param array $params An object containing the module parameters
     * @access public
     */
    function getCountryOptions( $params )
    {
    	$selected = JRequest::getVar( 'country', '','','string');
    	$db =& JFactory::getDBO();
		$query = "SELECT `name` as `value`, `name` as `text` FROM `#__gpo_countries` ORDER BY `name` ASC;";
		$db->setQuery( $query );
		$options = $db->loadAssocList();
		$html = JHTMLSelect::options( $options, 'value', 'text', $selected );
        return $html;
    }



    function getRegionOptions()
    {
    	$selected = JRequest::getVar( 'region', '','','string');
		$options = array(
						'0'=>array('value'=>'Africa','text'=>'Africa'),
						'1'=>array('value'=>'North Africa','text'=>'--North Africa'),
						'2'=>array('value'=>'West Africa','text'=>'--West Africa'),
						'3'=>array('value'=>'Central Africa','text'=>'--Central Africa'),
						'4'=>array('value'=>'East Africa','text'=>'--East Africa'),
						'5'=>array('value'=>'Southern Africa','text'=>'--Southern Africa'),
						'6'=>array('value'=>'Americas','text'=>'Americas'),
						'7'=>array('value'=>'North America','text'=>'--North America'),
						'8'=>array('value'=>'Central America','text'=>'--Central America'),
						'9'=>array('value'=>'Caribbean','text'=>'--Caribbean'),
						'10'=>array('value'=>'South America','text'=>'--South America'),
						'11'=>array('value'=>'Asia','text'=>'Asia'),
						'12'=>array('value'=>'Central Asia','text'=>'--Central Asia'),
						'13'=>array('value'=>'South Asia','text'=>'--South Asia'),
						'14'=>array('value'=>'East Asia','text'=>'--East Asia'),
						'15'=>array('value'=>'South East Asia','text'=>'--South East Asia'),
						'16'=>array('value'=>'Europe','text'=>'Europe'),
						'17'=>array('value'=>'Northern Europe','text'=>'--Northern Europe'),
						'18'=>array('value'=>'Western Europe','text'=>'--Western Europe'),
						'19'=>array('value'=>'Eastern Europe','text'=>'--Eastern Europe'),
						'20'=>array('value'=>'Southern Europe','text'=>'--Southern Europe'),
						'21'=>array('value'=>'Middle East','text'=>'Middle East'),
						'22'=>array('value'=>'Oceania','text'=>'Oceania'),
//						'23'=>array('value'=>'United Nations','text'=>'United Nations'),
						);
		$html = JHTMLSelect::options( $options, 'value', 'text', $selected );
        return $html;
    }
}
?>