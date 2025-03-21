<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class GpoModelRss extends JModelLegacy
{
	var $id = null;

	
	
	function __construct()
	{
		parent::__construct();
		$this->path_to_rss = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'cache' . DS . 'rss' . DS;
	}
	
	
	function go2( $id )
	{
		$query="
SELECT `id`,`websource`,`share`
FROM `#__gpo_news`
WHERE `id` =" . $this->_db->quote( $id ) . "
LIMIT 0,1
";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObject();

		if( !$result )
		{
			$url = JRoute::_( 'index.php' );
			return $url;
		}


		$hasUrl = ( $result->websource !== 'No Web Source' && $result->websource !== 'NoWebSource') ? true : false;

		if( $this->logged_in === false )
		{
			if( $result->share === '0' )
			{
				$url = JRoute::_( 'index.php' );
				return $url;
			}

			if( $hasUrl )
			{
				$url = $result->websource;
			}else{
				$url = JRoute::_( 'index.php?option=com_gpo&task=news&id=' . $id );
			}

		}else{
			$url = JRoute::_( 'index.php?option=com_gpo&task=news&&id=' . $id );
		}
		return $url;
	}



	private function hash( $key='', $location='' )
	{
		$this->hash = md5( $key . '_' . $location );
	}



	private	function filename()
	{
		$this->filename = $this->path_to_rss . $this->hash;
	}



	function showFeed( $key='', $location='' )
	{
// not sure this is the greatest work around
		$key = utf8_encode( rawurldecode( $key ) );
$config = JFactory::getConfig();

		$this->hash( $key, $location );
		$this->filename();
		if( !file_exists( $this->filename ) )
		{
			ob_end_clean();			;
			ob_start();

			//jimport( 'joomla.application.component.view' );
			//$jView = new JView();
            $jView = new JViewLegacy();

			$model = $this->search;

			$data = $model->searchRss( $key, $location );

			if( empty( $data ) || count( $data ) == 0 )
			{
				$this->blank();
				return;
			}
		
			$title = 'Gun Policy News (';
			
			$add =array();
			if( !empty( $key ) )
			{
				$add[] = $key;
			}
			
			if( empty( $location ) )
			{
				$add[] = 'World';
			
			} else {
                            if(urldecode($location)=='-United States'){
                                $location = '-USA';
                            }
                            if( substr( $location,0,1 ) === '-' )
                            {
                                    $location = 'Non' . ltrim( $location, '-' );
                            }
                            $add[] = $location;
			}
			$title .= implode(" + ", $add );
			$title .=')';

                        
			jimport( 'feedwriter.feedwriter' );
			jimport( 'feedwriter.feeditem' );

	  //Creating an instance of FeedWriter class. 
			$feed = new FeedWriter(RSS2);
	
	//Setting the channel elements
	$liveSiteURL = JURI::base();
			$channel=array(
'title'=> $title,
'link'=>$liveSiteURL,
'description'=>'Daily gun news, small arms policy and firearm violence prevention news.',
'language'=>'en-au',
'copyright'=>'Copyright ' . date('Y') . ' ' . $config->get('sitename'),
'lastBuildDate'=> date( 'r', $_SERVER['REQUEST_TIME'] ),
'webMaster'=>'www.gunpolicy.org/contact',
'image'=>array(
'url'=> $liveSiteURL . 'templates/gunpolicy/images/logo.gif',
'title'=>'Daily gun news, small arms policy and firearm death and injury prevention news.',
'link'=> $liveSiteURL
),
'ttl'=>'60'
);
			$feed->setChannelElementsFromArray( $channel );
	
			foreach( $data as $item )
			{
	//Create an empty FeedItem
				$newItem = $feed->createNewItem();					
	//Add elements to the feed item    
				$link = JRoute::_( 'index.php?option=com_gpo&task=news&id=' . $item->id, true, -1 );
				
				$newItem->setTitle( $jView->escape( $item->gpnheader ) );
				$newItem->setLink( $link );
				
	//author
//				$str = html_entity_decode( $item->source,ENT_NOQUOTES, 'utf-8' );
				$str = $item->source;
//				echo $str . "<br />";				
				$author = GpoEndWith( ',', $str ) . " ";
				if( !empty( $item->category ) )
				{
					$author .= GpoEndWith( ',', $item->category ) . " ";
				}
				$author .= " via " . $config->get('sitename');
				
				$newItem->addElement( 'author', $author );
				
				$newItem->setDate( date( 'j F Y', strtotime( $item->entered ) ) );
				
//content
				$signature = "[" . GpoEndWith( ',', $item->source ) . " ";
				if( !empty( $item->category ) )
				{
					$signature .=  GpoEndWith( ',', $item->category ) . " ";
				}				
				$signature .="via " . $config->get('sitename') . "]";
				
				$newItem->setDescription( gpo_helper::short( $item->content, $signature ) );
				
	//pubDate
				$str = date('r', strtotime( $item->published ) );
				$newItem->addElement( 'pubDate', $str );
	//guid
				$str = $link;
				$newItem->addElement( 'guid', $str );
				
	//Now add the feed item
				$feed->addItem($newItem);
			}

	//OK. Everything is done. Now genarate the feed.
			$feed->genarateFeed();
			$data = ob_get_clean();
			file_put_contents( $this->filename, $data );
		}else{
			ob_end_clean();
		}

                header ( "Content-type:text/xml; charset=utf-8" );	
		echo file_get_contents( $this->filename );
		exit();	
	}
	
	
	
	function topic( $topic, $data=NULL )
	{
		$id = $topic->get( 'id' );
		$seo = $topic->get( 'seo' );

		$this->hash( $id, $seo );
		$this->filename();
		
		if( !file_exists( $this->filename ) )
		{
			ob_end_clean();	
			ob_start();
			
			$model = $this->search;
			$model->limit = 100;
			$model->cl->SetLimits( (int)$model->start, (int)$model->limit, $model->max_matches );
			$model->members( $_GET );
				
			$data = $model->articles;
			if( empty( $data ) || count( $data ) == 0 )
			{
				$this->blank();
				return;
			}
			
			//jimport( 'joomla.application.component.view' );			
			$jView = new JViewLegacy();
			
			//$title = 'Gun Policy News ( ' . $topic->get( 'topic_name' ) . ' )';
			$title = $topic->get( 'page_headline' );
			
			jimport( 'feedwriter.feedwriter' );
			jimport( 'feedwriter.feeditem' );		
	
	  //Creating an instance of FeedWriter class. 
			$feed = new FeedWriter(RSS2);
	
	//Setting the channel elements
	
			$channel=array(
'title'=> $title,
'link'=>JApplication::getCfg('live_site'),
'description'=>'Daily gun news, small arms policy and firearm violence prevention news.',
'language'=>'en-au',
'copyright'=>'Copyright ' . date('Y') . ' ' . $config->get('sitename'),
'lastBuildDate'=> date( 'r', $_SERVER['REQUEST_TIME'] ),
'webMaster'=>'www.gunpolicy.org/contact',
'image'=>array(
'url'=> JURI::base() . 'templates/gunpolicy/images/logo.gif',
'title'=>'Daily gun news, small arms policy and firearm death and injury prevention news.',
'link'=> JApplication::getCfg('live_site')
),
'ttl'=>'60'
);
			$feed->setChannelElementsFromArray( $channel );
	
			foreach( $data as $item )
			{
	//Create an empty FeedItem
				$newItem = $feed->createNewItem();					
	//Add elements to the feed item    
	
								
				$link = JRoute::_( 'index.php?option=com_gpo&task=rss&view=go&id=' . $item->id, true, -1 );
				
				
				$newItem->setTitle( $jView->escape( $item->gpnheader ) );
				$newItem->setLink( $link );
				
	//author
//				$str = html_entity_decode( $item->source,ENT_NOQUOTES, 'utf-8' );
				$str = $item->source;
//				echo $str . "<br />";				
				$author = GpoEndWith( ',', $str ) . " ";
				if( !empty( $item->category ) )
				{
					$author .= GpoEndWith( ',', $item->category ) . " ";
				}
				$author .= " via " . $config->get('sitename');
				
				$newItem->addElement( 'author', $author );
				
				$newItem->setDate( date( 'j F Y', strtotime( $item->entered ) ) );
				
//content
				$signature = "[" . GpoEndWith( ',', $item->source ) . " ";
				if( !empty( $item->category ) )
				{
					$signature .=  GpoEndWith( ',', $item->category ) . " ";
				}				
				$signature .="via " . $config->get('sitename') . "]";
				
				$newItem->setDescription( gpo_helper::short( $item->content, $signature ) );
				
	//pubDate
				$str = date('r', strtotime( $item->published ) );
				$newItem->addElement( 'pubDate', $str );
	//guid
				$str = $link;
				$newItem->addElement( 'guid', $str );
				
	//Now add the feed item
				$feed->addItem($newItem);
			}

	//OK. Everything is done. Now genarate the feed.
			$feed->genarateFeed();
			$data = ob_get_clean();
			file_put_contents( $this->filename, $data );
		}else{
			ob_end_clean();			
		}
                
                header("Content-type: text/xml");
		echo file_get_contents( $this->filename );
		exit();
	}
	
	function blank()
	{
		$this->hash( 'blank', 'blank' );
		$this->filename();
		
		if( !file_exists( $this->filename ) )
		{
			ob_end_clean();			
			ob_start();
			
			jimport( 'feedwriter.feedwriter' );
			jimport( 'feedwriter.feeditem' );				
	
//Creating an instance of FeedWriter class. 
			$feed = new FeedWriter(RSS2);

//Setting the channel elements
			
			$title = $config->get('sitename') . ' RSS has no content';
			$channel=array(
						'title'=> $title,
						'link'=>JApplication::getCfg('live_site'),
						'description'=>'Daily gun news, small arms policy and firearm violence prevention news.',
						'language'=>'en-au',
						'copyright'=>'Copyright ' . date('Y') . ' ' . $config->get('sitename'),
						'lastBuildDate'=> date( 'r', $_SERVER['REQUEST_TIME'] ),
						'webMaster'=>'www.gunpolicy.org/contact',
						'image'=>array(
										'url'=> JURI::base() . 'templates/gunpolicy/images/logo.gif',
										'title'=>'Daily gun news, small arms policy and firearm death and injury prevention news.',
										'link'=> JApplication::getCfg('live_site')
										),
						'ttl'=>'60'
					);
			$feed->setChannelElementsFromArray( $channel );
		
			$newItem = $feed->createNewItem();
							
			$newItem->setTitle( 'This RSS feed does not yet contain the term you searched for' );
			$newItem->setLink( JApplication::getCfg('live_site') );
			$newItem->addElement( 'author', $config->get('sitename') );
			$newItem->setDate( date( 'j F Y', strtotime( $item->entered ) ) );
		
			$newItem->setDescription( 'Please click the back button, or goto ' . JApplication::getCfg('live_site') . ' and try start again.' );
			
//pubDate
			$str = date('r', strtotime( $item->published ) );
			$newItem->addElement( 'pubDate', $str );
			
//Now add the feed item
			$feed->addItem($newItem);
		

//OK. Everything is done. Now genarate the feed.
			$feed->genarateFeed();
			$data = ob_get_clean();
			file_put_contents( $this->filename, $data );
		}else{
			ob_end_clean();
		}
		echo file_get_contents( $this->filename );
	}
}
?>
