<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

echo '<div class="gpo_region_menu">';

if( !empty( $items ) )
{
	echo '<ul class="locations">';
	foreach( $items as $lo )
	{
		echo '<li><a href="' . JRoute::_("index.php?option=com_gpo&task=region&region=" . $lo->id, true ) .'">' . $lo->title . '</a></li>';
	}
	echo '</ul>';
}

//This is adapted from components/com_gpo/views/region/tmpl/default_articles.php
if( !empty( $articles ) )
{
	
	$user	= & JFactory::getUser();
	//$staff = ( $user->usertype === 'Super Administrator' || $user->usertype === 'Administrator' ) ? true : false ;
    
    $groupsUserIsIn = JAccess::getGroupsByUser($user->id);
    $staff = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;

	echo ( !$staff ) ? '<h3>Articles</h3>' : '<h3>Staff</h3>';
	
	echo '<ul class="articles">';
	foreach( $articles as $article )
	{
		echo '<li><a href="' . JRoute::_("index.php?option=com_gpo&task=region&region=" . $article->catid . "&id=" . $article->id, true ) .'">' . $article->title . '</a></li>';
	}
	echo '</ul>';			
}

echo '</div>';
?>