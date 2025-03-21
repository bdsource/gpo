<?php
/**
 * @version		$Id: icon.php 11213 2008-10-25 12:43:11Z pasamio $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Content Component HTML Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class JHTMLIcon
{
	function create($article, $params, $access, $attribs = array())
	{
		$uri =& JFactory::getURI();
		$ret = $uri->toString();	
	
		$url = 'index.php?task=new&ret='.base64_encode($ret).'&id=0&sectionid='.$article->sectionid;

		if ($params->get('show_icons')) {
			$text = JHTML::_('image.site', 'new.png', '/images/M_images/', NULL, NULL, JText::_('New') );
		} else {
			$text = JText::_('New').'&nbsp;';
		}

		$attribs	= array( 'title' => JText::_( 'New' ));
		return JHTML::_('link', JRoute::_($url), $text, $attribs);
	}

	function email($article, $params, $access, $attribs = array() )
	{
		$uri	=& JURI::getInstance();
		$base	= $uri->toString( array('scheme', 'host', 'port'));
		$link	= $base . 'test123';
		//$base.JRoute::_( ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid) , false );
		$url	= 'index.php?option=com_mailto&tmpl=component&link='.base64_encode( $link );

		$status = 'width=400,height=350,menubar=yes,resizable=yes';

		if ($params->get('show_icons')) 	{
			$text = JHTML::_('image.site', 'emailButton.png', '/images/M_images/', NULL, NULL, JText::_('Email'));
		} else {
			$text = '&nbsp;'.JText::_('Email');
		}

		$attribs['title']	= JText::_( 'Email' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

		$output = JHTML::_('link', JRoute::_($url), $text, $attribs);
		return $output;
	}

}
