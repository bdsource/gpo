<?php
/**
* @version		$Id: pagebreak.php 10906 2008-09-05 07:27:34Z willebil $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$mainframe = JFactory::getApplication();
$mainframe->registerEvent( 'onPrepareContent', 'plgGpoPagebreak' );

/**
* Page break plugin
*
* <b>Usage:</b>
* <code><hr class="system-pagebreak" /></code>
* <code><hr class="system-pagebreak" title="The page title" /></code>
* or
* <code><hr class="system-pagebreak" alt="The first page" /></code>
* or
* <code><hr class="system-pagebreak" title="The page title" alt="The first page" /></code>
* or
* <code><hr class="system-pagebreak" alt="The first page" title="The page title" /></code>
*
*/

	
function plgGpoPagebreak( &$row, &$params=0, $page=0 )
{
	// expression to search for
	$regex = '#<hr([^>]*?)class=(\"|\')system-pagebreak(\"|\')([^>]*?)\/*>#iU';
$jinput = JFactory::getApplication()->input;
	// Get Plugin info
	$plugin			= JPluginHelper::getPlugin('content', 'pagebreak');
	jimport( 'joomla.html.parameter' );
                  //$pluginParams	= new JParameter( $plugin->params );
                  $pluginParams	= new JRegistry( $plugin->params );
	$print   = $jinput->getBool('print');
	$showall = $jinput->getBool('showall');

	if (!$pluginParams->get('enabled', 1)) {
		$print = true;
	}

	if ($print) {
		$row->text = preg_replace( $regex, '<br />', $row->text );
		return true;
	}

	//simple performance check to determine whether bot should process further
    if ( strpos( $row->text, 'class="system-pagebreak' ) === false && strpos( $row->text, 'class=\'system-pagebreak' ) === false ) {
		return true;
	}

	$db		=& JFactory::getDBO();
    $view  = $jinput->getCmd('view');

	if(!$page) {
		$page = 0;
	}


	// check whether plugin has been unpublished
//	if (!JPluginHelper::isEnabled('content', 'pagebreak') || $params->get( 'intro_only' )|| $params->get( 'popup' ) || $view != 'article') {
////		$row->text = preg_replace( $regex, '', $row->text );
///		ftp_debug("here","here");		
//		return;
//	/}

	// find all instances of plugin and put in $matches
	$matches = array();
	preg_match_all( $regex, $row->text, $matches, PREG_SET_ORDER );

	if (($showall && $pluginParams->get('showall', 1) ))
	{
		$hasToc = $pluginParams->get( 'multipage_toc', 1 );
		if ( $hasToc ) {
			// display TOC
			$page = 1;
			plgGpoCreateTOC( $row, $matches, $page );
		} else {
			$row->toc = '';
		}
		$row->text = preg_replace( $regex, '<BR/>', $row->text );
		return true;
	}

	// split the text around the plugin
	$text = preg_split( $regex, $row->text );

	// count the number of pages
	$n = count( $text );

	// we have found at least one plugin, therefore at least 2 pages
	if ($n > 1)
	{
		// Get plugin parameters
		$pluginParams = new JParameter( $plugin->params );
		$title	= $pluginParams->get( 'title', 1 );
		$hasToc = $pluginParams->get( 'multipage_toc', 1 );

		// adds heading or title to <site> Title
		if ( $title )
		{
			if ( $page ) {
				$page_text = $page + 1;
				if ( $page && @$matches[$page-1][2] )
				{
					$attrs = JUtility::parseAttributes($matches[$page-1][1]);

					if ( @$attrs['title'] ) {
						$row->page_title = $attrs['title'];
					}
				}
			}
		}

		// reset the text, we already hold it in the $text array
		$row->text = '';

		// display TOC
		if ( $hasToc ) {
			plgGpoCreateTOC( $row, $matches, $page );
		} else {
			$row->toc = '';
		}

		// traditional mos page navigation
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $n, $page, 1 );

		// page counter
		$row->text .= '<div class="pagenavcounter">';
		$row->text .= $pageNav->getPagesCounter();
		$row->text .= '</div>';

		// page text
		$text[$page] = str_replace("<hr id=\"\"system-readmore\"\" />", "", $text[$page]);
		$row->text .= $text[$page];

		$row->text .= '<br />';
		$row->text .= '<div class="pagenavbar">';

		// adds navigation between pages to bottom of text
		if ( $hasToc ) {
			plgGpoCreateNavigation( $row, $page, $n );
		}

		// page links shown at bottom of page if TOC disabled
		if (!$hasToc) {
			$row->text .= $pageNav->getPagesLinks();
		}

		$row->text .= '</div><br />';
	}

	return true;
}

function plgGpoCreateTOC( &$row, &$matches, &$page )
{
	$heading = $row->title;

	// TOC Header
	$row->toc = '
	<table cellpadding="0" cellspacing="0" class="contenttoc">
	<tr>
		<th>'
		. JText::_( 'Article Index' ) .
		'</th>
	</tr>
	';

	$link = JRoute::_( '&showall=&limitstart=');
//	ftp_debug( $link,'link');
	// TOC First Page link
	$row->toc .= '
	<tr>
		<td>
		<a href="'. JRoute::_( '&showall=&limitstart=') .'" class="toclink">'
		. $heading .
		'</a>
		</td>
	</tr>
	';

	$i = 2;

	foreach ( $matches as $bot )
	{

		$link = JRoute::_( '&showall=&limitstart=' . ($i-1) );

		if ( @$bot[0] )
		{
			$attrs2 = JUtility::parseAttributes($bot[0]);

			if ( @$attrs2['alt'] )
			{
				$title	= stripslashes( $attrs2['alt'] );
			}
			elseif ( @$attrs2['title'] )
			{
				$title	= stripslashes( $attrs2['title'] );
			}
			else
			{
				$title	= JText::sprintf( 'Page #', $i );
			}
		}
		else
		{
			$title	= JText::sprintf( 'Page #', $i );
		}

		$row->toc .= '
			<tr>
				<td>
				<a href="'. $link .'" class="toclink">'
				. $title .
				'</a>
				</td>
			</tr>
			';
		$i++;
	}

	// Get Plugin info
	$plugin =& JPluginHelper::getPlugin('content', 'pagebreak');

	$params = new JParameter( $plugin->params );

	if ($params->get('showall') )
	{
		$link = JRoute::_( '&showall=1&limitstart=');
		$row->toc .= '
		<tr>
			<td>
				<a href="'. $link .'" class="toclink">'
				. JText::_( 'All Pages' ) .
				'</a>
			</td>
		</tr>
		';
	}
	$row->toc .= '</table>';
}

function plgGpoCreateNavigation( &$row, $page, $n )
{
	$pnSpace = "";
	if (JText::_( '&lt' ) || JText::_( '&gt' )) $pnSpace = " ";

	if ( $page < $n-1 )
	{
		$page_next = $page + 1;

		$link_next = JRoute::_( '&limitstart='. ( $page_next ) );
		// Next >>
		$next = '<a href="'. $link_next .'">' . JText::_( 'Next' ) . $pnSpace . JText::_( '&gt' ) . JText::_( '&gt' ) .'</a>';
	}
	else
	{
		$next = JText::_( 'Next' );
	}

	if ( $page > 0 )
	{
		$page_prev = $page - 1 == 0 ? "" : $page - 1;

		$link_prev = JRoute::_(  '&limitstart='. ( $page_prev) );
		// << Prev
		$prev = '<a href="'. $link_prev .'">'. JText::_( '&lt' ) . JText::_( '&lt' ) . $pnSpace . JText::_( 'Prev' ) .'</a>';
	}
	else
	{
		$prev = JText::_( 'Prev' );
	}

	$row->text .= '<div>' . $prev . ' - ' . $next .'</div>';
}
