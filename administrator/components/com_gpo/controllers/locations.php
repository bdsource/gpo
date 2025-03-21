<?php defined('_JEXEC') or die();

function gpoMakeAlias( $string )
{
	$a = 'Ã€Ã�Ã‚ÃƒÃ„Ã…Ã†Ã‡ÃˆÃ‰ÃŠÃ‹ÃŒÃ�ÃŽÃ�Ã�Ã‘Ã’Ã“Ã�?Ã•Ã–Ã˜Ã™ÃšÃ›ÃœÃ�Ãž
ÃŸÃ Ã¡Ã¢Ã£Ã¤Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã°Ã±Ã²Ã³Ã´ÃµÃ¶Ã¸Ã¹ÃºÃ»Ã½Ã½Ã¾Ã¿Å�?Å•';
    $b = 'aaaaaaaceeeeiiiidnoooooouuuuy
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $string = utf8_decode($string);
    $string = strtr($string, utf8_decode($a), $b);
    $string = strtolower($string);

$trans = array(
"("=>"",
")"=>"",
" "=>"-",
"&"=>"and",
"&amp;"=>"and"
);
	$string = strtr( $string, $trans );
	$s = str_split( $string );

	foreach( $s as $k=>$v )
	{
		if( ctype_alnum($v) || ( $v ==="-" ) )
		{
			$c.=$v;
		}
	}
    return utf8_encode($string); 
}



class GpoControllerLocations extends GpoController
{
    
    ##Default Language
    var $currentLanguage = 'en';
    var $requestURI = '';
    var $currentURI = '';
    var $languages  = array();
    
	function __construct()
	{
		parent::__construct();
		$this->registerTask( '','cpanel');
		$this->oUser = & JFactory::getUser();
        include_once(JPATH_COMPONENT.DS.'helper'.DS.'language.php');
        $mainframe =& JFactory::getApplication();
        
		//if( $this->oUser->usertype !== 'Super Administrator' ){
        if(!$this->oUser->get('isRoot')) {
			$msg = 'Access to Locations requires higher permissions.';
			$link = JRoute::_( 'index.php?option=com_gpo', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
        
        ##Initialize Language
        $this->languages = array('es','fr');
        $langSelection = $mainframe->getUserStateFromRequest( "lang", 'lang', 'en' );        
        $this->currentLanguage = $langSelection;
        
        ##Initiate Request URI
        $this->_initCurrentRequestURI();
        
	}
    
    
    /*
     * ##Make Request URI 
     * For language versions
     * 
     */
    function _initCurrentRequestURI() {
        
        $u =& JURI::getInstance();
        $requestURI = $u->toString();
        $langCodeToReplace = '&lang='.$this->currentLanguage;
        $currentURI = str_ireplace($langCodeToReplace,'',$requestURI);
        
        $this->currentURI = $currentURI;
        $this->requestURI = $requestURI;

        return true;
    }
    

	
	function cpanel()
	{
		$view =& $this->getView( 'Locations', 'html' );
		$view->cpanel();
	}
	
	
	function location_list()
	{
		$model =& $this->getModel( 'Locations' );
		$view  =& $this->getView( 'Locations', 'html' );
		
		$oItems = $model->getAllLocationData();
		$view->rows=&$oItems;
		$view->location_list();
	}
    
    function location_translate() {
        $model =& $this->getModel( 'Locations' );
        
        if( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && !empty($_POST) )
        {
            $locationIdString  =  Joomla\CMS\Factory::getApplication()->getInput()->get('locationId',false);
            $locationNewName   =  Joomla\CMS\Factory::getApplication()->getInput()->get('locationNewName', false,null, 'STRING');
            $locationIdString = explode('_',$locationIdString);
            $locationId   = trim($locationIdString[0]);
            $locationLang = trim($locationIdString[1]);
            $updateField       = trim($locationIdString[2]);

            
            $model->translateLocationName($locationId,$locationNewName,$locationLang,$updateField);
            echo $locationNewName;
            exit();
        }

        $view  =& $this->getView( 'Locations', 'html' );
	$oItems = $model->getAllLocationData();
	$view->rows=&$oItems;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

	$view->location_translate();
    }
    	
	function location_new()
	{
		$view =& $this->getView( 'Locations', 'html' );
		$view->admin_location_new();		
	}

	function admin_location_links()
	{
		$model =& $this->getModel( 'Locations' );
		$view =& $this->getView( 'Locations', 'html' );
		
		$locations = $model->getAllLocationData();
		$location_links = $model->getAllLocationLinks();
		$view->locations=&$locations;
		$view->location_links=&$location_links;
		$view->admin_location_links();
	}
	
	
	function admin_region_list()
	{		
		$model =& $this->getModel( 'Locations' );
		$view =& $this->getView( 'Locations', 'html' );
		
		$regions = $model->getRegions();
		$subregions = $model->getSubRegions();
		
		$location_links = $model->getRegionToSubRegionLinks();
		$location_names = $model->getAllLocationNames();
		
		$cacheData = explode( "\n", GpoGetTypeFromCache( 'admin_region' ) );
		
		$new_order =array();
		foreach( $cacheData as $location )
		{
			$location = str_replace("&nbsp;",'', $location );
			if( empty( $location ) )
			{
				$new_order[]=$location;
			}else if( in_array( $location, $regions ) )
			{
				$new_order[]=$location;
			}else if( in_array( $location, $subregions ) )
			{
				$new_order[]='---' . $location;
			}
		}


		
		$view->current_order=&$cacheData;
		$view->new_order=&$new_order;
		
		$view->location_links=&$location_links;
		$view->location_names=&$location_names;
		$view->admin_region_list();
	}
	
	
	
	function admin_country_list()
	{
		$model =& $this->getModel( 'Locations' );
		$view =& $this->getView( 'Locations', 'html' );
		
		$countries_on_record = $model->getCountries();
		
//Get saved data		
		$cacheData = explode( "\n", GpoGetTypeFromCache( 'admin_country' ) );

		$new_order =array();		
		foreach( $cacheData as $location )
		{
			//quick fix to deal with the missing &
			if( empty( $location ) )
			{
				$new_order[]=$location;
			}
			else if( in_array( $location, $countries_on_record ) )
			{
				$new_order[]=$location;
			}
		}

		$view->countries_on_record=&$countries_on_record;
		$view->new_order=&$new_order;
		$view->admin_country_list();
	}
	
	
	function public_region_list()
	{
		$model =& $this->getModel( 'Locations' );
		$view =& $this->getView( 'Locations', 'html' );
		
		$regions = $model->getRegions( 'public' );
		$subregions = $model->getSubRegions( 'public' );
		
		$location_links = $model->getRegionToSubRegionLinks( 'public' );
		$location_names = $model->getAllLocationNames();
		
		$cacheData = explode( "\n", GpoGetTypeFromCache( 'public_region' ) );
		
		$new_order =array();
		foreach( $cacheData as $location )
		{
			$location = str_replace("&nbsp;",'', $location );
			if( empty( $location ) )
			{
				$new_order[]=$location;
			}else if( in_array( $location, $regions ) )
			{
				$new_order[]=$location;
			}else if( in_array( $location, $subregions ) )
			{
				$new_order[]='---' . $location;
			}
		}

		$view->current_order=&$cacheData;
		$view->new_order=&$new_order;
		
		$view->location_links=&$location_links;
		$view->location_names=&$location_names;
		$view->public_region_list();
	}
	
	
	
	function public_country_list()
	{
		$model =& $this->getModel( 'Locations' );
		$view =& $this->getView( 'Locations', 'html' );
		
		$countries_on_record = $model->getCountries( 'public' );
		
//Get saved data		
		$cacheData = explode( "\n", GpoGetTypeFromCache( 'public_country' ) );

		$new_order =array();		
		foreach( $cacheData as $location )
		{
			//quick fix to deal with the missing &
			if( empty( $location ) )
			{
				$new_order[]=$location;
			}
			else if( in_array( $location, $countries_on_record ) )
			{
				$new_order[]=$location;
			}
		}

		$view->countries_on_record=&$countries_on_record;
		$view->new_order=&$new_order;
		$view->public_country_list();
	}
	
	

	function cancel()
	{
		$msg = '';
		$link = 'index.php?option=com_gpo&controller=locations';
		$this->setRedirect($link, $msg);
	}
	
	
		
	function a_save_admin_locations()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		$i = json_decode( stripslashes( $_POST['links'] ) );
		$model =& $this->getModel( 'Locations' );
		$model->save_links( $i );
		exit();
	}
	

	function a_save_admin_region()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		$location= str_replace("&amp;",'&', $_POST['order'] );
		$i = json_decode( stripslashes( $location ) );
		if( !is_array( $i ) || (int) count( $i ) === (int)'0' )
		{
			echo 'Click reset, to get the latest locations. Then alter accordingly.';
			exit();
		}		
		$model =& $this->getModel( 'Locations' );
		$regions = $model->getRegions();
		$subregions = $model->getSubRegions();
		
		foreach( $i as $location )
		{
			if( substr($location,0,1) === '-' )
			{
				$location = str_replace("-",'', $location );
			}
			if( empty( $location ) )
			{
				$order[]='';
			}else if( in_array( $location, $regions) )
			{
				$order[]=$location;
			}else if( in_array( $location, $subregions) )
			{
				$order[]="&nbsp;&nbsp;&nbsp;" . $location;
			}
		}
		$data = implode( "\n", $order );
		GpoSaveTypeToCache( 'admin_region', $data );
		echo 'Saved';
		exit();
	}
	
	
	function a_save_admin_country()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		$location= str_replace("&amp;",'&', $_POST['order'] );
		$i = json_decode( stripslashes( $location ) );
		if( !is_array( $i ) || (int) count( $i ) === (int)'0' )
		{
			echo 'Click reset, to get the latest locations. Then alter accordingly.';
			exit();
		}

		$data = implode( "\n", $i );
		GpoSaveTypeToCache( 'admin_country', $data );
		echo 'Saved';
		exit();
	}
	
	
	function a_save_public_region()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}

		$location= str_replace("&amp;",'&', $_POST['order'] );
		$i = json_decode( stripslashes( $location ) );
		if( !is_array( $i ) || (int) count( $i ) === (int)'0' )
		{
			echo 'Click reset, to get the latest locations. Then alter accordingly.';
			exit();
		}		
		
		$model =& $this->getModel( 'Locations' );
		$regions = $model->getRegions();
		$subregions = $model->getSubRegions();

		foreach( $i as $location )
		{
			if( substr($location,0,1) === '-' )
			{
				$location = str_replace("-",'', $location );
			}
			if( empty( $location ) )
			{
				$order[]='';
			}else if( in_array( $location, $regions) )
			{
				$order[]=$location;
			}else if( in_array( $location, $subregions) )
			{
				$order[]="&nbsp;&nbsp;&nbsp;" . $location;
			}
		}

		$data = implode( "\n", $order );
		GpoSaveTypeToCache( 'public_region', $data );
		
		echo 'saved';
		exit();
	}
	
	
	function a_save_public_country()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		
		$location= str_replace("&amp;",'&', $_POST['order'] );
		$i = json_decode( stripslashes( $location ) );
		if( !is_array( $i ) || (int) count( $i ) === (int)'0' )
		{
			echo 'Click reset, to get the latest locations. Then alter accordingly.';
			exit();
		}

		$data = implode( "\n", $i );
		GpoSaveTypeToCache( 'public_country', $data );

		echo 'Saved';
		exit();
	}

	
	
	function a_save()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		$model =& $this->getModel( 'Locations' );
		$model->save( $_POST['locations'] );
		exit();
	}
	
	
	function a_edit()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		$this->_db = & JFactory::getDBO();		

		$data = (array)json_decode(  stripslashes( $_POST['data'] ) );

		$location = trim($data['name']);
		$type = $data['type'];
		$display = ( $data['display'] === '1') ? '1': '0';

		$alias = gpoMakeAlias( $location );
		$id = $data['id'];

		$query1 = '
UPDATE `#__content` as `c`
INNER JOIN `#__categories` as `cat` ON `cat`.`id`=`c`.`catid`
INNER JOIN `#__gpo_location` AS `lo`  ON lower( `lo`.`name` )=lower(`cat`.`title`)
SET `c`.`alias` = REPLACE( `c`.`alias`,`cat`.`alias`,' . $this->_db->quote( $alias ) . ' ),
`c`.`title` = REPLACE( `c`.`title`,`lo`.`name`,' . $this->_db->quote( $location ) . ' ),
`c`.`metakey` = REPLACE( `c`.`metakey`,`lo`.`name`,' . $this->_db->quote( $location ) . ' )
WHERE `lo`.`id`=' . $this->_db->quote( $id ) . '
AND `c`.`alias`=CONCAT( `cat`.`alias`,"-index" )';

$this->_db->setQuery( $query1 );
$r1 = $this->_db->execute();
                
                
$query2 = 'UPDATE `#__content` as `c`
INNER JOIN `#__categories` as `cat` ON `cat`.`id`=`c`.`catid`
INNER JOIN `#__gpo_location` AS `lo`  ON lower( `lo`.`name` )=lower(`cat`.`title`)
SET `c`.`alias` = REPLACE( `c`.`alias`,`cat`.`alias`,' . $this->_db->quote( $alias ) . ' ),
`c`.`title` = REPLACE( `c`.`title`,`lo`.`name`,' . $this->_db->quote( $location ) . ' ),
`c`.`introtext` = REPLACE( `c`.`introtext`,`lo`.`name`,' . $this->_db->quote( $location ) . ' ),
`c`.`metakey` = REPLACE( `c`.`metakey`,`lo`.`name`,' . $this->_db->quote( $location ) . ' )
WHERE `lo`.`id`=' . $this->_db->quote( $id ) . '
AND `c`.`alias`=CONCAT( "staff-notes-", `cat`.`alias` )';

$this->_db->setQuery( $query2 );
$r2 = $this->_db->execute();


$query3 = 'UPDATE `#__gpo_spiderbait` as `s`, `#__gpo_location` as `lo`
INNER JOIN `#__categories` AS `cat`  ON lower( `lo`.`name` )=lower(`cat`.`title`)
SET `s`.`url_hash`=md5( CONCAT( "firearms/region/", ' . $this->_db->quote( $alias ) . ' ) ),
`s`.`url`=CONCAT( "firearms/region/", ' . $this->_db->quote( $alias ) . ' ),
`s`.`text`=REPLACE( `s`.`text`,`lo`.`name`,' . $this->_db->quote( $location ) . ' )
WHERE `s`.`url_hash`=md5( CONCAT( "firearms/region/", `cat`.`alias`) )
AND `lo`.`id`=' . $this->_db->quote( $id ) . '';

$this->_db->setQuery( $query3 );
$r3 = $this->_db->execute();

$query4 = 'UPDATE `#__categories` as `cat`
INNER JOIN `#__gpo_location` AS `lo`  ON lower( `lo`.`name` )=lower(`cat`.`title`)
SET `cat`.`alias` = ' . $this->_db->quote( $alias ) . ',
`cat`.`title` = ' . $this->_db->quote( $location ) . ',
`cat`.`description` = REPLACE( `cat`.`description`,lower(`lo`.`name`),lower( ' . $this->_db->quote( $location ) . ' ) )
WHERE `lo`.`id`=' . $this->_db->quote( $id ) . '';

$this->_db->setQuery( $query4 );
$r4 = $this->_db->execute();

$query5 = 'UPDATE `#__gpo_location` as `lo`
SET `lo`.`name`=' . $this->_db->quote( $location ) . ',
`lo`.`type`=' . $this->_db->quote( $type ) . ',
`lo`.`display`=' . $this->_db->quote( $display ) . '
WHERE `lo`.`id`=' . $this->_db->quote( $id ) . '';

$this->_db->setQuery( $query5 );
$r5 = $this->_db->execute();

		//$this->_db->setQuery( $query );
		//$r = $this->_db->queryBatch();
		echo '
<p>Changed ' . $_POST['c'] . ' to ' . $_POST['n'] . '</p>
<script>
//<![CDATA[	
	var id = EditLocation.getId();

	locations[id].name = $("location-edit").down("input[type=\'text\']").getValue();
	locations[id].type = $("location-edit").down("select").getValue();
	locations[id].display = $("location-edit").down("select","1").getValue();
//reset	
	form.reset();

//hide
	$("location-edit").hide();
	edit_status=\'hide\';
//show result - with new changes
	var e = $("location-edit").previous(); 
	e.show();
	e.down("td",0).update( locations[id].name );
	e.down("td",1).update( options_type.get( locations[id].type ) );
	e.down("td",2).update( options_display.get( locations[id].display ) );
//]]>
</script>
		';
		exit();
	}
	
	
	
	function a_new()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		
		$this->_db = & JFactory::getDBO();		
		
		$location = stripslashes( $_POST['n'] );
		$type = stripslashes( $_POST['t'] );
                
                echo "$type";

		if( empty( $location ) )
		{
			echo 'Please enter a Location';
			exit();
		}
		
		$this->_db = & JFactory::getDBO();		
		$query = '
                 SELECT COUNT( `name` )
                 FROM `#__gpo_location`
                 WHERE lower( `name`  )=lower( ' . $this->_db->quote( $location ) .'  )
                 LIMIT 0,1';

		$this->_db->setQuery( $query );
		$r = $this->_db->loadResult();
		
		if( !empty( $r ) )
		{
			echo 'That Location already exists, we have not created another';
			exit();
		}
	
        $locationStartWith = substr(trim($location), 0, 1);
        //$locationSearchString = $this->_db->getEscaped($locationStartWith,true) . '%';
        $locationSearchString = $this->_db->escape($locationStartWith) . '%';

        ##Find the category parent ID
        $categoryParentIdQuery = "
                 SELECT id  
                 FROM `#__categories`
                 WHERE lower( `title` ) = 'regions'  
                 LIMIT 0,1
                 ";
        $this->_db->setQuery($categoryParentIdQuery);
        $categoryParentId = $this->_db->loadResult();
        if (empty($categoryParentId)) {
            $categoryParentId = 0;
        }
        
        ##Find the right order in the Category tree
        $orderedCategories = "SELECT `id`, `parent_id`, `lft`, `rgt`, `title` 
                              FROM  `#__categories` 
                              WHERE `title` LIKE " . $this->_db->quote($locationSearchString,false);
        if( $categoryParentId != 0 ) {
            $orderedCategories .= " AND parent_id = '$categoryParentId' "; 
        }
        $orderedCategories .= "UNION (
                                SELECT NULL, NULL, NULL, NULL, ".$this->_db->quote($location)." AS `title`
                              ) ORDER BY `title`";
        $this->_db->setQuery($orderedCategories);
        $orderedCatResult = $this->_db->loadObjectList();
        foreach($orderedCatResult as $key => $val) {
            if($location == $val->title) {
               break;
            }
            $leftNode  = $val->lft;
            $rightNode = $val->rgt;
        }
        //new order of the newly created category, +2 is for joomla's default increment
        $leftNode  += 2;
        $rightNode += 2;

		$alias = gpoMakeAlias( $location );

// INSERT NEW location\n
$query = "insert into `#__gpo_location` (`id`, `name`,`type`,`display`) VALUES (NULL , " . $this->_db->quote( $location ) . ", " . $this->_db->quote( $type ) . ", '1')";
$this->_db->setQuery( $query );
//echo $query; exit();
$r = $this->_db->execute();

// INSERT NEW category
$query = "insert into `#__categories` (`id`, `parent_id`, `title`, `alias`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `hits`, `params`,`level`, `lft`, `rgt`, `extension`, `language`, `created_time`, `modified_time`) 
VALUES( NULL, " . $this->_db->quote($categoryParentId) . ", " . $this->_db->quote( $location ) . ", " . $this->_db->quote( $alias ) . 
", " . $this->_db->quote( "This is the Category for " . $location ) . ", 1, NULL, NULL, 0, 0, '',
'2',".$this->_db->quote($leftNode).",".$this->_db->quote($rightNode).",'com_content','*',now(),now())";
$this->_db->setQuery( $query );
$r = $this->_db->execute();

$query = "INSERT INTO `#__gpo_spiderbait` (`id`, `url_hash`,`url`,`text`)  SELECT null AS `id`, MD5( CONCAT( 'firearms/region/', `cat`.`alias` ) ) AS `url_hash`, CONCAT( 'firearms/region/', `cat`.`alias` ) AS `url`, CONCAT( 'Small arms in ', `lo`.`name`, ', firearm injury prevention, firearm regulation, gun law and gun control.' ) AS `text` FROM `#__categories` as `cat` INNER JOIN  `#__gpo_location` as `lo` ON `lo`.`name` = `cat`.`title` WHERE `lo`.`name`=" . $this->_db->quote( $location ) . ";";
$this->_db->setQuery( $query );
$r = $this->_db->execute();

$query = "INSERT INTO `#__content` (`language`,`id`, `title`, `alias`, `introtext`, `fulltext`, `state`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`) SELECT '*' AS `language`, null AS `id`, CONCAT( 'Guns in ', `lo`.`name`, ': Small arms policy, firearm injury and gun law' ) AS `title`, CONCAT( `cat`.`alias`,'-index') AS `alias`, CONCAT('<p>This is a place holder for the intro to ', `lo`.`name`, ' test intro.</p>' ) AS `introtest`, '' AS `fulltext`, 0 AS `state`, `cat`.`id` AS `catid`, '" . date("Y/m/d H:i:s", $_SERVER['REQUEST_TIME'] ) . "' AS `created`, 62 AS `created_by`, '' AS `created_by_alias`, now() AS `modified`, 0 AS `modified_by`, 0 AS `checked_out`, null AS `checked_out_time`, null AS `publish_up`, null AS `publish_down`, '' AS `images`, '' AS `urls`, 'show_title=link_titles=show_intro=show_section=link_section=show_category=link_category=show_vote=show_author=show_create_date=show_modify_date=show_pdf_icon=show_print_icon=show_email_icon=language=keyref=readmore=' AS `attribs`, 1 AS `version`, 1 AS `ordering`, CONCAT( `lo`.`name`,', firearm homicide, gun law, small arms policy, firearm injury prevention, armed violence, gun crime, public health, international development, human security, gun control' ) AS `metakey`, CONCAT( 'Gun law, gun control statistics, number of guns in ', `lo`.`name`, ' gun deaths, firearm facts and policy, armed violence, public health and development', '.' ) AS `metadesc`, 0 AS `access`, 0 AS `hits`, 'robots=author=GunPolicy.org' AS `metadata` FROM `#__categories` as `cat` INNER JOIN  `#__gpo_location` as `lo` ON `lo`.`name` = `cat`.`title` WHERE `lo`.`name`=" . $this->_db->quote( $location ) . " AND `lo`.`display`='1'";
$this->_db->setQuery( $query );
$r = $this->_db->execute();
$query = "INSERT INTO `#__content` (`language`,`id`, `title`, `alias`, `introtext`, `fulltext`, `state`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`) SELECT '*' AS `language`, null AS `id`,CONCAT( `lo`.`name`, ' notes' ) AS `title`, CONCAT( 'staff-notes-', `cat`.`alias` ) AS `alias`,  CONCAT( '<p>', `lo`.`name`, ' notes</p>' ) AS `introtest`, '' AS `fulltext`, 0 AS `state`, `cat`.`id` AS `catid`, '" . date("Y/m/d H:i:s", $_SERVER['REQUEST_TIME'] ) . "' AS `created`, 62 AS `created_by`, '' AS `created_by_alias`, now() AS `modified`, 0 AS `modified_by`, 0 AS `checked_out`, null AS `checked_out_time`, null AS `publish_up`, null AS `publish_down`, '' AS `images`, '' AS `urls`, 'show_title=link_titles=show_intro=show_section=link_section=show_category=link_category=show_vote=show_author=show_create_date=show_modify_date=show_pdf_icon=show_print_icon=show_email_icon=language=keyref=readmore=' AS `attribs`, 1 AS `version`, 1 AS `ordering`, CONCAT( `lo`.`name`,', firearm homicide, gun law, small arms policy, firearm injury prevention, armed violence, gun crime, public health, international development, human security, gun control' ) AS `metakey`, CONCAT( 'Gun law, gun control statistics, number of guns in ', `lo`.`name`, ' gun deaths, firearm facts and policy, armed violence, public health and development', '.' ) AS `metadesc`, 0 AS `access`, 0 AS `hits`, 'robots=author=GunPolicy.org' AS `metadata` FROM `#__categories` as `cat` INNER JOIN  `#__gpo_location` as `lo` ON `lo`.`name` = `cat`.`title` WHERE `lo`.`name`=" . $this->_db->quote( $location ) . " AND `lo`.`display`='1'";
//echo $query;exit();
$this->_db->setQuery( $query );
$r = $this->_db->execute();
if( !$r ) {
           echo 'control-----------------';
           echo $this->_db->getError();
        }

        
		echo '
<p>
' . $location . ' has been added.<br />
<a href="' . JRoute::_( 'index.php?option=com_gpo&controller=locations&task=admin_location_links' , false ) . '">Dont forget to assign ' . $location . ' to one of the Locations links</a>.
</p>
<script>
//<![CDATA[	
//reset	
	form.reset();
//]]>
</script>
';
		exit();
	}
	
	
	function location_delete()
	{		
		$view =& $this->getView( 'Locations', 'html' );
		$view->admin_location_delete();
	}

	
	function a_delete()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}
		$this->_db = & JFactory::getDBO();		
		
		$location = stripslashes( $_POST['n'] );

		if( empty( $location ) )
		{
			echo 'Please enter a Location';
			exit();
		}
		
		$this->_db = & JFactory::getDBO();		
		$query = '
SELECT COUNT( `name` )
FROM `#__gpo_location`
WHERE lower( `name`  )=lower( ' . $this->_db->quote( $location ) .'  )
LIMIT 0,1';

		$this->_db->setQuery( $query );
		$r = $this->_db->loadResult();
		
		if( empty( $r ) )
		{
			echo 'That Location cannot be found';
			exit();
		}
		
		$query ="DELETE `c` FROM `#__content` as `c` INNER JOIN `#__categories` as `cat` ON `cat`.`id`=`c`.`catid` WHERE `cat`.`title`=" . $this->_db->quote( $location );
		$this->_db->setQuery( $query );
		$r = $this->_db->execute();

		$query ="DELETE `s` FROM `#__gpo_spiderbait` as `s` INNER JOIN `#__categories` as `cat` ON `cat`.`alias`=REPLACE(`s`.`url`,'firearms/region/','' ) WHERE `cat`.`title`=" . $this->_db->quote( $location );
		$this->_db->setQuery( $query );
		$r = $this->_db->execute();

		$query ="DELETE FROM `#__categories` WHERE `title`=" . $this->_db->quote( $location );
		$this->_db->setQuery( $query );
		$r = $this->_db->execute();

		$query ="DELETE FROM `#__gpo_location` WHERE `name`=" . $this->_db->quote( $location );
		$this->_db->setQuery( $query );
		$r = $this->_db->execute();
		
		echo '
<p>
' . $location . ' has been Deleted.
</p>
<script>
//<![CDATA[	
//reset	
	form.reset();
//]]>
</script>
';
		exit();
	}
    
    function group_list() {
        $model = & $this->getModel('Locations');
        $view = & $this->getView('Locations', 'html');

        $oItems = $model->getAllGroupNames();
       
        $view->rows=&$oItems;
        $view->locModel=&$model;
        
        $view->group_list();
    }
    
    function group_edit() {
        $model    = & $this->getModel('Locations');
        $datapage = & $this->getModel('Datapages');
        $view     = & $this->getView('Locations', 'html');
        $groupId  = Joomla\CMS\Factory::getApplication()->getInput()->get('groupid', false);
        
        if (!empty($_POST)) {
            $selectedLocationsId = Joomla\CMS\Factory::getApplication()->getInput()->get('selectedLocations', false);
            $groupName = trim(Joomla\CMS\Factory::getApplication()->getInput()->get('group_name', false));
            $groupDetails = $model->getGroupById($groupId);
            
            $status = $model->updateGroupLocations($groupId,$selectedLocationsId);
            
            if( !empty($groupName) && $groupDetails['name'] != $groupName ) {
                $result = $model->updateGroupName($groupId, $groupName);
            }
            
            $responseLink = JRoute::_( "index.php?option=com_gpo&controller=locations&task=group_edit&groupid=$groupId", false);
            $responseMsg = "Group Locations successfully updated";
		    $this->setRedirect( $responseLink, $responseMsg );
		    $this->redirect(); 
            
        }else {
        //   $groupId  = Joomla\CMS\Factory::getApplication()->getInput()->get('groupid', false);
           $groupDetails = $model->getGroupById($groupId);
           $allLocations = $datapage->getAllLocationData('name');
           $groupLocations = $model->getAllLocationsByGroupId($groupId);

           $view->groupDetails=&$groupDetails;
           $view->allLocations=&$allLocations;
           $view->groupLocations=&$groupLocations;
           $view->groupId=&$groupId;
           
           $view->group_edit();
        }
    }
    
    function group_new() {
        
      $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', 'create');
      $model  = & $this->getModel('Locations');

      if( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && !empty($_POST) )
      {
          
          $this->_db = & JFactory::getDBO();
          $groupId = Joomla\CMS\Factory::getApplication()->getInput()->get('groupid', false);
          $groupName = trim(Joomla\CMS\Factory::getApplication()->getInput()->get('group_name', false));
          
          if( empty( $groupName ) )
          {
              echo 'Please enter a Group Name';
              exit();
          }
          
          if( 'delete' == $action ) {        
             
              $result = $model->deleteGroup($groupId);
              $responseLink = JRoute::_( "index.php?option=com_gpo&controller=locations&task=group_list", false);
              $addLink = "<a href='" . $responseLink . "'> Cick here to view all groups</a>" ;
              echo "The Group - $groupName has been successfully deleted ... $addLink";
              exit();
              
          }else {
                $query = '
                   SELECT COUNT( `name` )
                   FROM `#__gpo_groups`
                   WHERE lower( `name`  )=lower( ' . $this->_db->quote($groupName) . '  )
                   LIMIT 0,1';

                $this->_db->setQuery($query);
                $r = $this->_db->loadResult();

                if (!empty($r)) {
                    echo 'That Group already exists, we have not created another';
                    exit();
                }

                $query = "
                         insert into `#__gpo_groups` (`id`, `name`, `display`)
                         VALUES (
                                NULL , " . $this->_db->quote($groupName) . ", '1'
                         );
                         ";

                $this->_db->setQuery($query);
                $r = $this->_db->execute();
                $insert_id = $this->_db->insertid();
                $responseLink = JRoute::_( "index.php?option=com_gpo&controller=locations&task=group_edit&groupid=$insert_id", false);
                $addLink = "<a href='" . $responseLink . "'> Cick here to add locations under this group</a>" ;
                echo "New Group has been successfully created... $addLink";
                exit();
          }
        }else {
            $view = & $this->getView('Locations', 'html');
            
            if( 'delete' == $action ) {
               $groupId = Joomla\CMS\Factory::getApplication()->getInput()->get('groupid', false);
               $groupDetails = $model->getGroupById($groupId);
               $view->groupDetails=&$groupDetails;
            }
            
            $view->action=$action;
            $view->admin_group_new();
        }
    }
    
    function state_or_province_new()
    {
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', 'create');
        $model  = & $this->getModel('Locations');
        $jinput = JFactory::getApplication()->input;
        
        if( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && !empty($_POST) )
        {
            $stateOrProvinceName = trim($jinput->get('location_name', false, 'string'));
            $countryName = trim($jinput->get('select_country_name', false, 'string'));
            
            if( empty( $stateOrProvinceName ) )
            {
                echo 'Please enter a State/Province Name.';
                exit();
            }
            
            $countryID = $model->getLocationIdByName($countryName);
            if(empty($countryID)) {
                echo "Country ID error, please check the country name";
                exit();
            }
            
            //Insert the new State/Province Name
            $locationArray = array('name'=>$stateOrProvinceName, 'type'=>'state_province', 'display'=>1);
            $stateOrProvinceID = $model->insertLocation($locationArray);
            
            if(empty($stateOrProvinceID)) {
                echo "State/Province creation error. Probably duplicate name, try again";
                exit();
            }
            
            //Now link with the country name
            $model = $model->insertLocationLinks(array('location_id'=>$countryID, 'link_id'=>$stateOrProvinceID));
            echo "New State/Province has been created successfully and linked with the country $countryName";
            exit();
        }
        else
        {
            $view = & $this->getView('Locations', 'html');

            /*
            if( 'delete' == $action )
            {
               $locationId = Joomla\CMS\Factory::getApplication()->getInput()->get('locationid', false);
               $locationDetails = $model->getLocationById($locationId);
               $view->assignRef('locationDetails', $locationDetails);
            }
            */
            
            $view->action=&$action;
            $view->admin_state_or_province_new();
        }
    }
    
    function states_list() {
        $model =  $this->getModel('Locations');
        $view  = $this->getView('Locations', 'html');
        $view->locModel=&$model;
        
        $view->states_list();
    }
}
?>
