<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');


class GpoViewQuotes extends JViewLegacy
{

   function display($tpl = null)
	{ 
		GpoHelper::addSubmenu('quotes');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display($tpl);
	}

    function getLookupBox()
    {


        $id = (int)@$this->id;
        $state = Joomla\CMS\Factory::getApplication()->getInput()->get('state', '');
	$task = Joomla\CMS\Factory::getApplication()->getInput()->get('task');
        if (empty($state)) {
            $state = (Joomla\CMS\Factory::getApplication()->getInput()->get('task', '') == 'unpublished') ? 'unpublished' : 'published';
        }
        //echo $state;
	if(!($task=='edit' || $task=='create')){
        $lookupbox = '<small><small>
                    <form style="padding-left:40px;display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                        <input type="hidden" name="task" value="lookup" />
                        <input type="hidden" name="controller" value="quotes" />
                        <span>Lookup ID: Q<input type="text" name="id" value="" style="width:50px;"/>
                        <input type="hidden" name="state" value="' . $state . '" />
                        <input style="margin-left:-5px" type="submit" value="Go" /></span>
                    </form>
                    ';
        if (!empty($id)) {
            $lookupbox .= '<span><form style="padding-left:20px;display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                    <input type="hidden" name="task" value="lookup" />
                    <input type="hidden" name="controller" value="quotes" />
                    <input type="hidden" name="lookupdirection" value="prev" />
                    <input type="hidden" name="state" value="' . $state . '" />
                    <input type="hidden" name="id" value="' . $id . '"/>
                    <input type="submit" value="Prev" />
                </form>
                <form style="display:inline;" method="post" action="' . JRoute::_("index.php?option=com_gpo", false) . '" id="adminFormLookup" name="adminFormLookup">
                    <input type="hidden" name="task" value="lookup" />
                    <input type="hidden" name="controller" value="quotes" />
                    <input type="hidden" name="lookupdirection" value="next" />
                    <input type="hidden" name="id" value="' . $id . '"/>
                    <input type="hidden" name="state" value="' . $state . '" />
                    <input type="submit" value="Next" />
                </form></span>
            ';
        }
	}
        $lookupbox .= '</small></small>';
        return $lookupbox;
    }

    function edit()
    {
        $this->isNew = ($this->oQuote->id < 1);

        $text = $this->isNew ? JText::_('Create New') : JText::_('Edit');
        $title = JText::_('Quotes <small><small>[' . $text . ']</small></small>');
        if (isset($this->oQuote->live_id) && !empty($this->oQuote->live_id)) {
            $title .= ' <small><small>Live Id: ' . $this->oQuote->live_id . '</small></small>';
        }
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_(strip_tags($title)));

        $bar = & JToolBar::getInstance('toolbar');


        if ($this->isNew) {
            //Force content to be blank
            $this->oQuote->content = "";
        }
        //Clear Form
        $href = JRoute::_('#', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="clear_form" title="Clear all text from this form"><span class="icon-trash" ></span>Clear Form</a>', '');
        JToolBarHelper::spacer('10');

        //Save and Create Another
        $title = ($this->isNew) ? "Save this new quote, then create another" : "Save this quote, then create another";
        $href = JRoute::_('#', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="save_create_another" title="' . $title . '"><span class="icon-save"></span>Save + Clone</a>', '');
        JToolBarHelper::spacer('10');
        
        //Save and Publish
        $title = ($this->can_publish) ? "Save and Publish this Quote"
                : "Save this Quote and Goto Approve for Publishing";
        $href = JRoute::_('#save_publish');
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="item_publish" title="' . $title . '"><span class="icon-publish"></span>Save + Publish</a>', '');

        $this->tip_published();

        if (strpos($_SERVER['HTTP_REFERER'], 'task=unpublished') === false) {
            $href = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
        } else {
            $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=unpublished', false);
        }
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="item_close" title="Close Without Saving"><span class="icon-cancel"></span>Close Without Saving</a>', '');

        /* Moved it in here to try and seperate out the html + javascript a little more */
        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');
        $document->addScript(JURI::root(true) . '/administrator/templates/bluestork/js/quotes_location.js');
        $document->addStyleSheet(JURI::root(true) . '/media/system/css/calendar-jos.css', 'text/css', 'all', array('title' => 'green'));
        $document->addScript(JURI::root(true) . '/media/system/js/calendar.js');
        $document->addScript(JURI::root(true) . '/media/system/js/calendar-setup.js');
        $mootools = JURI::root(true) . '/media/system/js/mootools.js';
        if (isset($document->_scripts[$mootools])) {
            unset($document->_scripts[$mootools]);
        }
        $tpl = 'edit';
        $this->display($tpl);
    }


    function published()
    { 
        JToolBarHelper::title(JText::_('Quotes <small><small>[Published]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Published] '));

        $this->tip_reindex();
        $this->tip_search();
        $this->tip_poaimsearch();
        $this->tip_back_to_search();
        $this->tip_find_replace();
        $this->tip_create_new();
        $this->tip_unpublished();
        //$this->tip_export_csv();

        $tpl = 'published';
        $this->display($tpl);

    }


    function unpublished()
    {
        JToolBarHelper::title(JText::_('Quotes <small><small>[Unpublished]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Unpublished] '));

        $bar = & JToolBar::getInstance('toolbar');

        //$this->tip_back_to_search();
        $this->tip_reindex();
        //$this->tip_build();

        $this->tip_create_new();
        $this->tip_published();
        if ($this->can_publish) {
            //JToolbarHelper::custom('publish_green','list-view','','Publish Green',false);
            $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=publish_green', false);
            $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Publish items which already have a live id"><span class="icon-unblock"></span>Publish Green</a>', '');
            JToolBarHelper::spacer('10');
        }
        JToolbarHelper::custom('unpublished_empty','remove','','Empty Queue',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=unpublished_empty', false);
       //$bar->appendButton('Custom', '<a href="' . $href . '" id="unpublish_empty" title="Delete all the unpublished Quotes in this queue"><span class="icon-32-trash"></span>Empty Queue</a>', '');
        $tpl = 'unpublished';
        $this->display($tpl);
    }


    function empty_unpublished()
    {
        JToolBarHelper::title(JText::_('Quotes <small><small>[Empty Unpublished]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Empty Unpublished] '));

        $this->tip_close();

        $tpl = 'empty_unpublished';
        $this->display($tpl);
    }


    function publish()
    {
        JToolBarHelper::title(JText::_('Quotes <small><small>[Publish]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Publish] '));
        
        $bar = & JToolBar::getInstance('toolbar');

        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=edit&id=' . $this->oQuote->id, false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Edit"><span class="icon-edit"></span>Edit</a>', '');

        $this->tip_close();

        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');
        $mootools = JURI::root(true) . '/media/system/js/mootools.js';
        if (isset($document->_scripts[$mootools])) {
            unset($document->_scripts[$mootools]);
        }


        $tpl = 'publish';
        $this->display($tpl);
    }


    function publishGreen()
    {
        JToolBarHelper::title(JText::_('Quotes <small><small>[Publish Green]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Publish Green] '));
        
        $this->tip_close();

        viewHtmlAddPrototype();
        $tpl = 'publish_green';
        $this->display($tpl);
    }


    function delete()
    {
        JToolBarHelper::title(JText::_('Quotes: <small><small>[Delete]</small></small>' . $this->getLookupBox()), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Delete] '));

        $this->tip_close();

        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');

        $mootools = JURI::root(true) . '/media/system/js/mootools.js';
        if (isset($document->_scripts[$mootools])) {
            unset($document->_scripts[$mootools]);
        }

        $tpl = 'delete';
        $this->display($tpl);
    }


    function lookup()
    {

        $bar = & JToolBar::getInstance('toolbar');

        $this->tip_reindex();
        $this->tip_back_to_search();
        $this->tip_search();
        $this->tip_poaimsearch();

        if (!empty($this->id)) {
            JToolBarHelper::title(JText::_('Quotes <small><small>[Lookup]</small></small>' . $this->getLookupBox()), 'generic.png');
            
            $document = & JFactory::getDocument();
	    $document->setTitle(JText::_('Quotes [Lookup] '));
        
            $state = Joomla\CMS\Factory::getApplication()->getInput()->get('state');
            //echo $state;
            if ('unpublished' == $state) {
                $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=edit&state=' . $state . '&id=' . $this->id, false);
            } else {
                $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=edit&state=' . $state . '&live_id=' . $this->id, false);
            }

            if ('unpublished' == $state) {
                $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Open this Unpublished Quote in Edit mode"><span class="icon-edit"></span>Edit</a>', '');
            } else {
                $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Open this Published Quote in Edit mode"><span class="icon-edit"></span>Edit</a>', '');
            }
            
            if ('published' == $state) {
                $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=createcitation&id=' . $this->id);
                $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Create a separate Citation from this Quote, then open it for editing"><span class="icon-new"></span>Citation</a>', '');
            }
            
            if ($this->can_publish) {
                if ('unpublished' == $state) {
                    $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=unpublished_delete&id=' . $this->id, false);
                    $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Delete this unpublished Quote"><span class="icon-trash"></span>Delete</a>', '');
                } else {
                    $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=published_delete&id=' . $this->id, false);
                    $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Delete this live Quote"><span class="icon-trash"></span>Delete</a>', '');
                }
                JToolBarHelper::spacer('10');
            }

        } else {
            JToolBarHelper::title(JText::_('Quotes <small><small>[Lookup]</small></small>'), 'generic.png');
            
            $document = & JFactory::getDocument();
	    $document->setTitle(JText::_('Quotes [Lookup] '));
        }

        $href = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close without saving"><span class="icon-cancel"></span>Close</a>', '');

        if (!empty($this->oQuote->id)) {
            $tpl = 'edit_disabled';
            /* Moved it in here to try and seperate out the html + javascript a little more */
            $document = &JFactory::getDocument();
            $document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');
            $document->addScript(JURI::root(true) . '/administrator/templates/bluestork/js/quotes_location.js');
            $document->addStyleSheet(JURI::root(true) . '/media/system/css/calendar-jos.css', 'text/css', 'all', array('title' => 'green'));
            $document->addScript(JURI::root(true) . '/media/system/js/calendar.js');
            $document->addScript(JURI::root(true) . '/media/system/js/calendar-setup.js');
            $mootools = JURI::root(true) . '/media/system/js/mootools.js';
            if (isset($document->_scripts[$mootools])) {
                unset($document->_scripts[$mootools]);
            }
        }
        else
        {
            $tpl = 'lookup';
        }
        
        $this->display($tpl);
    }


    function search()
    {
        $title = JText::_('Quotes <small><small>[Search]</small></small>');
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Search] '));

        $bar = & JToolBar::getInstance('toolbar');

        $this->tip_reindex();
        //JToolbarHelper::custom('search','search','','Search',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=search', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="toolbar-Link" title="Search Published Quotes"><span class="icon-search"></span>Search</a>', '');

        if($this->isSuperAdmin) {
           $this->tip_export_template();
        }
        //$this->tip_published();
        $this->tip_close();

        $tpl = "searchform";
        $this->display($tpl);
    }

    function searchPoaim()
    {
        $title = JText::_('Quotes <small><small>[Search PoAIM]</small></small>');
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Search PoAIM] '));

        $bar = & JToolBar::getInstance('toolbar');

        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=search', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="toolbar-Link" title="Search PoAIM Quotes"><span class="icon-search"></span>Search</a>', '');

        $this->tip_close();

        $tpl = "poaimsearchform";
        $this->display($tpl);
    }

    function exportTemplate()
    {
        $title = JText::_('Export Template');
        //$bar = & JToolBar::getInstance('toolbar');

        //$href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=applytemplate');

        //$bar->appendButton('Custom','<');
        //$toolbar = JToolBarHelper::getInstance();
        JToolBarHelper::apply('exporttemplate', 'Save');
        JToolBarHelper::cancel('cancel', 'Close');

        $tpl = 'exporttemplate';
        $this->display($tpl);
    }

    function export($tpl = "export_search_form")
    {
        $title = JText::_('Quotes <small><small>[Export]</small></small>');
        JToolBarHelper::title(JText::_($title), 'generic.png');

        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Export] '));
        
        $bar = & JToolBar::getInstance('toolbar');


        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=search', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" id="toolbar-Link" title="Search Quotes"><span class="icon-search"></span>Search</a>', '');

        $this->tip_close();

        $this->display($tpl);
    }


    function searchResults()
    {
        if ($this->poaim) {
            $title = JText::_('Quotes <small><small>[Search PoAIM]</small></small>');
        } else {
            $title = JText::_('Quotes <small><small>[Search]</small></small>');
        }
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');

        $document = & JFactory::getDocument();
	    $document->setTitle(JText::_(strip_tags($title)));
        
        $bar = & JToolBar::getInstance('toolbar');

        $this->tip_reindex();

        if (isset($_GET['quotes'])) {
            $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" onclick="reviseSearch();" href="#" title="Revise Search"><span class="icon-search"></span>Revise Search</a>', '');
        }


        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=search&ignore=1', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Search Published Quotes"><span class="icon-search"></span>New Search</a>', '');

        //poaim search
        if (isset($this->poaim)) {
            $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=poaimsearch', false);
            $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Search Published Quotes"><span class="icon-search"></span>New PoAIM Search</a>', '');

        }
        
        ## Only Administrators and Super Admins should have the export buttons
        if($this->isSuperAdmin || $this->isAdministrator) {
            $this->tip_export_txt();
            $this->tip_export_csv();
            $this->tip_export_csv2();
        }
        ## Only Super Admin should have the export template change feature
        if($this->isSuperAdmin) {
            $this->tip_export_template('_blank');
        }
        
        $document = &JFactory::getDocument();
        //$document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');
        $mootools = JURI::root(true) . '/media/system/js/mootools.js';
        if (isset($document->_scripts[$mootools])) {
            unset($document->_scripts[$mootools]);
        }

        $tpl = "searchresults";
        $this->display($tpl);
    }


    function build()
    {

        JToolBarHelper::title(JText::_('Build Database: Quotes'), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Build Database: Quotes'));

        $this->tip_close();

        $tpl = 'build';
        $this->display($tpl);
    }


    function reindex()
    {
        $title = JText::_('Quotes <small><small>[Update]</small></small>');
        $title .= $this->getLookupBox();
        JToolBarHelper::title(JText::_($title), 'generic.png');
        
        $document = & JFactory::getDocument();
	$document->setTitle(JText::_('Quotes [Update]'));

        $this->tip_search();
        $this->tip_published();
        $this->tip_close();

        $tpl = 'reindex';
        $this->display($tpl);
    }


    /*
      *
      * Find & Rpelace tool
      * functions
      *
      */

    function frt_add()
    {
       
        JToolBarHelper::title('Quotes Find & Replace [Search]', 'generic.png');
        
        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('[Quotes] Quotes Find & Replace - Add New Search'));

        $title = "Search in a specific column";
        $title_do = 'Search';
        //$this->add_prototype();
        $this->add_jquery();

        $bar = & JToolBar::getInstance('toolbar');

        //search button
        JToolBarHelper::save('frt', 'Search');

        //last searched button
        $LastSearchedLink = 'index.php?option=com_gpo&controller=quotes&task=frt&action=add' .
                '&swap[from]=' . urlencode($this->lastSearchedQuery->from) .
                '&swap[to]=' . urlencode($this->lastSearchedQuery->to) .
                '&swap[column_name]=' . urlencode($this->lastSearchedQuery->column_name) .
                '&swap[case_sensitive]=' . urlencode($this->lastSearchedQuery->options);

        $href = JRoute::_($LastSearchedLink, false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Last Searhed Query"><span class="icon-search"></span>Last Searched</a>', '');

        //history button
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=frt&action=history', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="View Past Replaces History"><span class="icon-edit"></span>History</a>', '');

        JToolBarHelper::spacer(10);
        JToolBarHelper::divider();
        JToolBarHelper::spacer(10);

        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=published', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the Quotes [Published] Page"><span class="icon-cancel"></span>Close</a>', '');

        $tpl = 'frt_add';
        $this->display($tpl);
    }

    function frt_results()
    {
        
        JToolBarHelper::title('Quotes Find & Replace [Results]', 'generic.png');
        
        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('Results - Quotes Find & Replace'));
        
        $title = 'Replace Selected Records';
        //$this->add_prototype();
        $this->add_jquery();

        //JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=frt&action=add', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the Search Form of Quotes Find & Replace"><span class="icon-cancel"></span>Cancel</a>', '');

        JToolBarHelper::spacer(10);
        JToolBarHelper::divider();
        JToolBarHelper::spacer(10);

        JToolBarHelper::publishList('frt_replace', $title);

        $tpl = 'frt_results';
        $this->display($tpl);
    }


    function frt_history()
    {
        
        JToolBarHelper::title('Quotes Find & Replace [History]', 'generic.png');
        
        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('History - Quotes Find & Replace'));
        
        $bar = & JToolBar::getInstance('toolbar');

        //new search button
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=frt&action=add', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="New Search: Perform New Search on Quotes"><span class="icon-search"></span>New Search</a>', '');

        //Last searched button
        //var_dump( $this->lastSearchedQuery );
        $LastSearchedLink = 'index.php?option=com_gpo&controller=quotes&task=frt&action=add' .
                '&swap[from]=' . urlencode($this->lastSearchedQuery->from) .
                '&swap[to]=' . urlencode($this->lastSearchedQuery->to) .
                '&swap[column_name]=' . urlencode($this->lastSearchedQuery->column_name) .
                '&swap[case_sensitive]=' . urlencode($this->lastSearchedQuery->options);

        $href = JRoute::_($LastSearchedLink, false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Last Searhed Query"><span class="icon-search"></span>Last Searched</a>', '');


        //close button
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close: Back to the Quotes [Published] Page"><span class="icon-cancel"></span>Close</a>', '');

        $tpl = 'frt_history';
        $this->display($tpl);
    }


    function add_prototype()
    {
        /* Moved it in here to try and seperate out the html + javascript a little more */
        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');
    }
    
    function add_jquery() {
        
        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true).'/media/system/js/jquery1.6.2.js');
    }

    function tip_reindex()
    {
        if ($this->can_publish && $this->shouldReIndex) {
            $bar = & JToolBar::getInstance('toolbar');
            JToolbarHelper::custom('reindex','redo','','Update',false);
            $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=reindex', false);
            //$bar->appendButton('Custom', '<a href="' . $href . '" title="Update the Quotes index to show any recent changes"><span class="icon-32-upload"></span>Update</a>', '');
            JToolBarHelper::spacer('10');
        }
    }

    function tip_close()
    {
        $bar = & JToolBar::getInstance('toolbar');
        if (strpos($_SERVER['HTTP_REFERER'], 'task=unpublished') === false) {
            $href = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
        } else {
            $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=unpublished', false);
        }
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Close without saving"><span class="icon-cancel"></span>Close</a>', '');
    }


    function tip_search()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=search&ignore=1', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Search Published Quotes"><span class="icon-search"></span>Search</a>', '');
        JToolBarHelper::spacer('10');
    }

    function tip_poaimsearch()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=poaimsearch&ignore=1', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Search Published Quotes for PoAIM Data"><span class="icon-search"></span>PoAIM Search</a>', '');
        JToolBarHelper::spacer('10');
    }

    function tip_find_replace()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=frt', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Quotes: Find & Replace"><span class="icon-search"></span>Find & Replace</a>', '');
        JToolBarHelper::spacer('10');
    }


    function tip_back_to_search()
    {
        if (!isset($_COOKIE['gpo_admin_quotes_last_search'])) return;
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=search&back=1', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="' . $href . '" title="Back to Last Search Results"><span class="icon-search"></span>Last Searched</a>', '');
        JToolBarHelper::spacer('10');
    }


    function tip_published()
    {
        $bar = & JToolBar::getInstance('toolbar');
        JToolbarHelper::custom('published','list-view','','Published List',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=published', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="List of Published Quotes"><span class="icon-32-unpublish"></span>Published List</a>', '');
        JToolBarHelper::spacer('10');
    }


    function tip_build()
    {
        if ($this->can_publish) {
            $bar = & JToolBar::getInstance('toolbar');
            JToolbarHelper::custom('build','plus','','Build',false);
            $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=build', false);
            //$bar->appendButton('Custom', '<a href="' . $href . '" title="Rebuild the entire Quotes database from an imported text file"><span class="icon-32-new"></span>Build</a>', '');
        }
    }

    function tip_unpublished()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $html_total = ($this->unpublishedTotal > 0) ? '( ' . $this->unpublishedTotal . ' )' : '';
        $title = 'Unpublished Queue' . $html_total;
        JToolBarHelper::custom('unpublished', 'list-view','', $title, false);
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=unpublished&filter_order_Dir=asc', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="Go to the Unpublished Quotes queue"><span class="icon-32-unpublish"></span>Unpublished Queue' . $html_total . '</a>', '');
        JToolBarHelper::spacer('10');
    }

    function tip_export_txt()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=exporttxt', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="#" id="toolbar-Export" title="Export selected Quotes to Text format (.txt with charset=utf-8)"><span class="icon-export"></span>Export TXT</a>', '');
    }

    function tip_export_csv()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=exportcsv', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="#" id="toolbar-Export-Csv" title="Export selected Quotes to CSV format (.csv with charset=utf-16le)"><span class="icon-export"></span>Export CSV</a>', '');
    }

    function tip_export_csv2()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=exportcsv', false);
        $bar->appendButton('Custom', '<a class="btn btn-default toolbar-btn-padding" href="#" id="toolbar-Export-Csv2" title="Export selected Quotes to Excel format (.xls with charset=utf-8)"><span class="icon-export"></span>Export Excel</a>', '');
    }

    function tip_export_template($target = "_self")
    {
        $bar = & JToolBar::getInstance('toolbar');
        JToolbarHelper::custom('exporttemplate','download','','Export Template',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=exporttemplate', false);
        //$bar->appendButton('Custom', '<a target="' . $target . '"  href="' . $href . '" title="Determine export template"><span class="icon-32-export"></span>Export Template</a>', '');
    }


    function tip_create_new()
    {
        $bar = & JToolBar::getInstance('toolbar');
        JToolbarHelper::custom('create','new','','Create New',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=quotes&task=create', false);
        //$bar->appendButton('Custom', '<a href="' . $href . '" title="Create a new Quote record"><span class="icon-32-new"></span>Create New</a>', '');
        JToolBarHelper::spacer('10');
    }
}

