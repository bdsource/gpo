<?php
defined('_JEXEC') or die();

class GpoControllerTopics extends GpoController
{
	function __construct()
	{
		parent::__construct();
		$this->oUser	   = & JFactory::getUser();
		        
        //7 and 8 is administrator and super administrator
        $groupsUserIsIn    = JAccess::getGroupsByUser($this->oUser->id);
        $this->can_publish = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        
        if ($this->can_publish === false) {
            $msg = 'Your account doesnt have high enough access';
            $link = JRoute::_('index.php?option=com_gpo', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        $this->cookie_name_topic = 'topic_id';
        $this->registerTask('', 'viewall');
    }

    /*
	 * 
	 */
	function edit()
	{
	//	$id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id' );
		   $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');

		$item = '';
		if( !empty( $id ) )
		{
			$model =& $this->getModel( 'Topics' );
			$item = $model->getById( $id );
		}
		if( $item === false )
		{
			$msg ="We are unable to find that topic.";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=topics', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}		
			
		if( !empty( $item->id ) )
		{
			require_once( JPATH_COMPONENT . DS . 'helper/spiderbait.php' );
			$input = array(
								'url' => $item->seo
							);
			$cSpiderbait = new GpoSpiderbait( $input[ 'url' ] );	
			$item->spiderbait = $cSpiderbait->get( 'text' );	
		}else{
			$item->spiderbait = '';
		}
			
			
		$view =& $this->getView( 'Topics', 'html' );
		//$view->assignRef( 'topic', $item );
		$view->topic = &$items;
		$view->edit();
	}
	
	
	
	function view()
	{
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id' );
		
		$model =& $this->getModel( 'Topics' );
		
		$item = $model->getById( $id );
		if( $item === false )
		{
			$msg ="We are unable to find that topic.";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=topics', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		
		$view =& $this->getView( 'Topics', 'html' );
		$view->topic=&$item;
		$view->view();
	}

	
	
	function viewall()
	{
		$cookie=array();
		$cookie[ 'name' ]= $this->cookie_name_topic;//Name of the cookie
		$cookie[ 'expiry_date' ]= $_SERVER['REQUEST_TIME'] - 86400;//Expiry date of the cookie ( 30 seconds )
		$cookie[ 'path' ]='/';//path allowed - linked to the domain	
		setcookie(
			$cookie[ 'name' ],
			$cookie[ 'data' ],
			$cookie[ 'expiry_date' ],
			$cookie[ 'path' ]
		);
		$model =& $this->getModel( 'Topics' );
		$items = $model->getAll();	
		$view =& $this->getView( 'Topics', 'html' );
		//$view->assignRef( 'all', $items );
		$view->all = &$items;
		$view->all();
	}
	
	

	function a_save()
	{
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
		{
			echo 'error';
			exit();
		}

		$search = $_POST['t']['search'];
		$search = json_decode( $search, true );
		unset( $search['notes'] );
		ksort( $search );
		$_POST['t']['search'] = json_encode( $search );

		$_POST['t']['search_hash'] = md5( $_POST['t']['search'] );
		
		$model =& $this->getModel( 'Topics' );
		$r = $model->save( $_POST['t'] );
		echo $model->response();

		GpoClearRssCache();
		GpoDeleteFromCache( 'topics.all' );
		exit();
	}
	

	
	function editsearch()
	{
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id' );
		$item = '';
		if( !empty( $id ) )
		{
			$model =& $this->getModel( 'Topics' );
			$item = $model->getById( $id );
		}
		if( empty( $item ) )
		{
			$msg ="We are unable to find that topic.";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=topics', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		$search = json_decode( $_GET['d'], true );
		
		$cookie_name_last_search = 'gpo_admin_news_last_search';
		$data = serialize( array( 'revise'=>'1', 'back'=>'1', 'news'=>$search ) );

		$cookie=array();
		$cookie[ 'name' ]= $cookie_name_last_search;//Name of the cookie
		$cookie[ 'data' ]=$data;//Data you want to store - 3 is the magic number
		$cookie[ 'expiry_date' ]= $_SERVER['REQUEST_TIME'] + 86400;//Expiry date of the cookie ( 30 seconds )
		$cookie[ 'path' ]='/';//path allowed - linked to the domain
		setcookie(
					$cookie[ 'name' ],
					$cookie[ 'data' ],
					$cookie[ 'expiry_date' ],
					$cookie[ 'path' ]
					);
					
//Set the topic_id cookie
		$data = $item->id;
		
		$cookie=array();
		$cookie[ 'name' ]= $this->cookie_name_topic;//Name of the cookie
		$cookie[ 'data' ]=$data;//Data you want to store - 3 is the magic number
		$cookie[ 'expiry_date' ]= $_SERVER['REQUEST_TIME'] + 86400;//Expiry date of the cookie ( 30 seconds )
		$cookie[ 'path' ]='/';//path allowed - linked to the domain
		setcookie(
					$cookie[ 'name' ],
					$cookie[ 'data' ],
					$cookie[ 'expiry_date' ],
					$cookie[ 'path' ]
					);
		
		$msg ="From Topic.";
	
		$link = JRoute::_( 'index.php?option=com_gpo&controller=news&task=search&back=1&revise=1&' . http_build_query( array( 'news'=>$search ) ) , false  );
		
		//$link = JRoute::_( 'index.php?option=com_gpo&controller=news&task=search&back=1&revise=1', false  );
		$this->setRedirect($link, $msg);
		$this->redirect();			
	}
	
	
	function create()
	{
		$model = $this->getModel( 'Topics' );
		
		$id = $_COOKIE[ $this->cookie_name_topic ];
		
		$item = '';
		if( !empty( $id ) )
		{
			$model =& $this->getModel( 'Topics' );
			$item = $model->getById( $id );
		}
		
		if( !empty( $item ) )
		{
			require_once( JPATH_COMPONENT . DS . 'helper/spiderbait.php' );
			$input = array(
								'url' => $item->seo
							);
			$cSpiderbait = new GpoSpiderbait( $input[ 'url' ] );	
			$item->spiderbait = $cSpiderbait->get( 'text' );
		}
		
		if( !empty( $_GET['d'] ) )
		{
			
			parse_str( rawurldecode( $_GET['d'] ), $data );
			$search = $data['news'];
			unset( $search['notes'] );
			
			
			if( !empty( $search[ 'fromdate' ] ) )
			{

				$search['published_range']['from'] = $search[ 'fromdate' ];	
				unset( $search[ 'fromdate' ] );
			}
			if( !empty( $search[ 'todate' ] ) )
			{
				$search['published_range']['to'] = $search[ 'todate' ];
				unset( $search[ 'todate' ] );	
			}
			$search['published_range']['from'] = trim( $search['published_range']['from'] );
			$search['published_range']['to'] = trim( $search['published_range']['to'] );			
				
			if( empty( $search['published_range']['from'] ) )
			{
				unset( $search['published_range']['from'] );
			}
			if( empty( $search['published_range']['to'] ) )
			{
				unset( $search['published_range']['to'] );
			}
			
			if( count(  $search['published_range'] ) == 0 )
			{
				unset( $search['published_range'] );
			}
				
				
			if( !empty( $search['l'] ) )
			{
				$search[ 'locations' ] = $search[ 'l' ];
				unset( $search[ 'l' ] );	
			}
			foreach( $search as $key => $value )
			{
				if( is_string( $value ) )
				{
					$value = trim( $value );
					if( strlen( $value ) !== 0 )
					{
						$search[ $key ] = $value;					
					}else{
						unset( $search[$key] );
					}
				}
			}

			if( isset( $search['published_range'] ) )
			{
				ksort( $search['published_range'] );	
			}
			ksort( $search );

			$search = json_encode( $search );
			$hash = md5( $search );
 			
			if( empty( $item->id ) )
			{
				$item = null;
//How will this change things
//				$item = $model->getBySearchHash( $hash );
			}
			$item->search = $search;
			$item->search_hash = $hash;		
		}
		$view =& $this->getView( 'Topics', 'html' );
		$view->topic=&$item;
		$view->edit();
	}
	
/*	
	function new_howto()
	{
		$view =& $this->getView( 'Topics', 'html' );
		$view->assignRef( 'topic', $item );
		$view->new_howto();
	}
*/

	
	function set_search()
	{
		$cookie_name_topic = 'topic_id';
		if( isset( $_COOKIE[ $cookie_name_topic ] ) )
		{
			$cookie=array();
			$cookie[ 'name' ]= $cookie_name_topic;//Name of the cookie
			$cookie[ 'expiry_date' ]= $_SERVER['REQUEST_TIME'] - 86400;//Expiry date of the cookie ( 30 seconds )
			$cookie[ 'path' ]='/';//path allowed - linked to the domain	
			setcookie(
			//cookie_name,
			$cookie[ 'name' ],
			//cookie_data,
			$cookie[ 'data' ],
			//cookie_expiry_date,
			$cookie[ 'expiry_date' ],
			//cookie_path,
			$cookie[ 'path' ]
			);
		}
		
		$msg = 'Set up the Search For the Topic then hit the goto Topic button.';
		$link = JRoute::_( 'index.php?option=com_gpo&controller=news&task=search', false  );
		$this->setRedirect($link, $msg);
		$this->redirect();
	}
	
	
	
	function delete()
	{
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id' );
		if( empty( $id ) || $id === 'all' )
		{
			if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST' )
			{
				if( $_POST['id'] === 'all' )
				{
					$model =& $this->getModel( 'Topics' );
					$model->deleteAll();	
					$msg ="Deleted All Topics.";
					$link = JRoute::_( 'index.php?option=com_gpo&controller=topics', false  );
					$this->setRedirect($link, $msg);
					$this->redirect();
				}
			}else{
				$view =& $this->getView( 'Topics', 'html' );
				$view->topic=&$item;
				$view->confirm_delete_all();
				return;
			}
		}
		
		
		$model =& $this->getModel( 'Topics' );
		
		$item = $model->getById( $id );
		if( $item === false )
		{
			$msg ="We are unable to find that topic.";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=topics', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
			
		if( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST' )
		{
			$model->deleteBy( $item );
			$msg = "Topic has been deleted.";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=topics', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}else{
			$view =& $this->getView( 'Topics', 'html' );
			$view->topic=&$item;
			$view->confirm_delete();
		}	
	}
	
//DELETE THIS 	
	function import()
	{
		return;
		$db =& JFactory::getDBO();
		
		$query = '
TRUNCATE `#__gpo_topic`;
';
		$db->setQuery( $query );
		$db->query();
			
		$query = '
DELETE FROM `#__gpo_spiderbait` WHERE `url` REGEXP "^firearms/topic.*";
';
		$db->setQuery( $query );
		$db->query();

		$query = "SELECT * FROM `gpo_topic` ORDER BY `topic_id` ASC;";
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		require_once( JPATH_COMPONENT . DS . 'helper/topic.php' );
		require_once( JPATH_COMPONENT . DS . 'helper/spiderbait.php' );

		$seos = array();
		foreach( $items as $item )
		{
//Search	
			$locations = array();
			if( !empty( $item->country ) && $item->country !== 'none' )
			{
				$locations[] = $item->country;
			}
			
			if( !empty( $item->region ) && $item->region !== 'none' )
			{
				$locations[] = $item->region;
			}
//No need to look up as this is now done.	
			sort( $locations );
			$locations = implode( ",", $locations );
			
			$search = array(
							'keywords' => $item->keywords,
							'locations' => $locations,
							'content' => $item->content,
							'many' => '1',
							'share' => '1'
							);
			foreach( $search as $k=>$v )
			{
				if( empty( $v ) )
				{
					unset( $search[ $k ] );
				}
			}
			
			ksort( $search );				
			$search = json_encode( $search );
			$hash = md5( $search );

//Meta
			$meta = array();
			$meta[ 'author' ] = $item->metadata_author;
			$meta[ 'keywords' ] = $item->metadata_keyword;
			$meta[ 'description' ] = ( empty( $item->metadata ) ) ? '' : $item->metadata;
			
//Build the seo
			$stub = strtolower( $item->topic );
			$stub = str_replace( " ", "_", $stub );			
			$seo = 'firearms/topic/' . $stub; 
			
			if( isset( $seos[ $seo] ) )
			{
				ftp_debug( array( 'seo' => $seo, 'topic_id' => $item->topic_id ), 'Problem', true, false );
			}else{
				$seos[ $seo ] = '';
			}
//Fake Input
			$input = array();
			$input[ 'id' ] = '';
			//->topic_id;
			$input[ 'topic_name'] = $item->topic;
			$input[ 'seo' ] = $seo;
			$input[ 'window_title' ] = $item->page_title;
			$input[ 'page_headline' ] = $item->page_headline;
			$input[ 'page_headline_sub' ] = $item->sub_headline;
			$input[ 'spiderbait' ] = $item->spider_bait;

			$input[ 'meta' ] = $meta; 
			$input[ 'search' ] = $search;
			$input['search_hash'] = md5( $input[ 'search' ] );

			//Spiderbait
			$cSpiderbait = new GpoSpiderbait( $input[ 'seo'] );					
			$cSpiderbait->save( array(
											'url' => $input[ 'seo'],
											'text' => $input[ 'spiderbait' ]
										)
								);			
			//Topic
			$cTopic = new GpoTopic();
			$cTopic->save( $input );
		}
		GpoClearRssCache();
		GpoDeleteFromCache( 'topics.all' );
	}
	
	
	function tidyup()
	{
		return;
		$db =& JFactory::getDBO();
		$query = "SELECT DISTINCT( `region` ) FROM `gpo_topic` ORDER BY `gpo_topic`.`region` ASC;";
		$db->setQuery( $query );
		$regions = $db->loadColumn();
		
		$query = "SELECT DISTINCT( `country` ) FROM `gpo_topic` ORDER BY `gpo_topic`.`country` ASC;";
		$db->setQuery( $query );
		$country = $db->loadColumn();
		
		$topic_locations = array();
		foreach( $regions as $item )
		{
			$topic_locations[] = $item;
		}
		foreach( $country as $item )
		{
			$topic_locations[] = $item;
		}
	
		$query = "SELECT `name` FROM `#__gpo_location`;";
		$db->setQuery( $query );
		$locations = $db->loadColumn();
		
		$locations = array_flip( $locations );
		foreach( $topic_locations as $lo )
		{
			if( !isset( $locations[ $lo ] ) )
			{
				echo $lo . '<br />';
			}
		}
	}
}
?>
