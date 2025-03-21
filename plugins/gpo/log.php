<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe = JFactory::getApplication();
$mainframe->registerEvent( 'onAfterRender', 'plgGpoLog' );

function plgGpoLog()
{
	if( defined( 'GpoLogAlreadyCalled' ) )
	{
		return;	
	}
	define( 'GpoLogAlreadyCalled', true );		

	$document = JFactory::getDocument();
	$user	= & JFactory::getUser();
	
	if( empty( $user->id ) )
	{
		return;
	}

    // igonor super administrator
    if($user->get('isRoot')) {
        return;
    }
    $groupsUserIsIn = JAccess::getGroupsByUser($user->id);
 
	$a = array();
	$a['title']  = $document->getTitle();
	$a['when']   = date( 'Y/m/d H:i:s', $_SERVER['REQUEST_TIME'] );
	$a['request_uri'] = $_SERVER['REQUEST_URI'];
	$a['remote_addr'] = $_SERVER['REMOTE_ADDR'];
	$a['user_id']   = $user->id;
	$a['user_type'] = $groupsUserIsIn; //$user->usertype;
	$a['user_username'] = $user->username;
	$a['user_name'] = $user->name;	
	$a['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	$a['request_method'] = $_SERVER['REQUEST_METHOD'];

//we could save a copy of the page?
	$b = (object)$a;
	$db = & JFactory::getDBO();	
	$db->insertObject( '#__gpo_site_logger', $b, 'id' );
//	echo '<!--',ftp_debug( $db, 'db', true, false ), '-->';

}
?>