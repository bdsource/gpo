<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');
//JHTML::_('behavior.modal', 'a.popup');

class GpoViewDatapages extends JViewLegacy {

    var $subComponentName = 'Data Pages ';

    function display($tpl = null) {
        $document = JFactory::getDocument();
        $document->addScript('/media/vendor/mootools/MooTools-Core-1.6.0.js');
        GpoHelper::addSubmenu('datapages');
        if (JVERSION >= '3.0') {
           // $this->sidebar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }

    /*
      function display($tpl = null)
      {
      parent::display($tpl);
      }
     */

    function cpanel() {
        JToolBarHelper::title($this->subComponentName . '<small><small>[Start]</small></small>', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('[Start] '));

        $tpl = 'cpanel';
        $this->display($tpl);
    }

    function location_list() {
        JToolBarHelper::title($this->subComponentName . '<small>[Locations]</small>', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('[Locations] '));

        $document = & JFactory::getDocument();
        $bar = & JToolBar::getInstance('toolbar');

        if ($this->isRoot) {
            //Propagate Master List
            JToolBarHelper::custom('propagate_masterlist', 'forward', '', 'Propagate MasterList (Es/Fr)', false);
            $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=propagate_masterlist', false);
            //$bar->appendButton( 'Custom', '<a  href="' . $href . '" title="Propagate preambles from master list"><span class="icon-32-forward"></span>Propagate MasterList (Es/Fr)</a>', '' );
        }

        //Translate Locations
        //JToolBarHelper::custom('location_translate', 'pencil', '', 'Translate Locations', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_translate', false);
        $bar->appendButton('Custom', '<a  class="btn btn-default" href="' . $href . '" title="Translate Location Names"><span class="icon-pencil-2"></span>Translate Locations</a>', '');

        //Preambles Switches Master List
        JToolBarHelper::custom('preambles_switches_list', 'list-2', '', 'Preambles: Master List', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=preambles_switches_list', false);
        //$bar->appendButton( 'Custom', '<a  href="' . $href . '" title="Preambles: Master List"><span class="icon-32-forward"></span>Preambles: Master List</a>', '' );
        //Add new Column
        JToolBarHelper::custom('add_new_column', 'new', '', 'Add Category', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=add_new_column', false);
        //$bar->appendButton( 'Custom', '<a  href="' . $href . '" title="Add a New Category"><span class="icon-32-new"></span>Add Category</a>', '' );
        //Edit Columns
        //JToolBarHelper::custom('edit_columns', 'edit', '', 'Edit Categories', false);      
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=edit_columns', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Edit Categories"><span class="icon-edit"></span>Edit Categories</a>', '');

        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=view_columns', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="View Categories"><span class="icon-list"></span>View Categories</a>', '');

        //Find & Replace
        JToolBarHelper::custom('frt', 'search', '', 'Find & Replace', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=frt&action=add', false);
        //$bar->appendButton( 'Custom', '<a  href="' . $href . '" title="Find & Replace Tool"><span class="icon-32-search"></span>Find & Replace</a>', '' );
        //Missing Citations
        JToolBarHelper::custom('check_missing_cite', 'question', '', 'Missing Citations', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=check_missing_cite', false);
        //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Search for missing Q or N cite"><span class="icon-32-preview"></span>Missing Citations</a>', '' );
        //Group DP Analyzer
        JToolBarHelper::custom('groups_dp_tabular', 'chart', '', 'Groups DP Analyzer', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=groups_dp_tabular', false);
        //$bar->appendButton( 'Custom', '<a  href="' . $href . '" title="Groups DP Analyzer"><span class="icon-32-stats"></span>Groups DP Analyzer</a>', '' );
        //LCPGV Updates
        JToolBarHelper::custom('lcpgv_updates', 'chart', '', 'LCPGV Updates', false);

        //DPData Automation Script
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dpdata_update_automation', false);
        $bar->appendButton('Custom', '<a  class="btn btn-default" href="' . $href . '" title="DP Yearly Data Update Automation Script"><span class="icon-bars"></span>DP Yearly Data Update Automation Script</a>', '');

        /*
          //$href = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=clean_data&type=show_options',false );
          //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Clean Spaces in DP Data"><span class="icon-32-forward"></span>Clean Spaces</a>', '' );
          // $bar->appendButton('Custom','<a target="_blank" href="http://gunpolicy.org/testbed/synctestbed.php" title="Update DP in Testbed"><span class="icon-32-apply"></span>Update Testbed DP</a>');
         */

       // JHTML::_('behavior.tooltip');
        $document->addStyleSheet("templates/$template/css/general.css");

        $tpl = 'location_list';
        $this->display($tpl);
    }

    function location_translate() {
        JToolBarHelper::title('Location <small><small>[Translate Location Names]</small></small>', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('Location [Translate Location Names]'));

        //Close page
        $this->tip_close();

        $tpl = 'location_translate';
        $this->display($tpl);
    }

    function propagate_masterlist() {
        JToolBarHelper::title('Propagate MasterList (Es/Fr)', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('Propagate MasterList (Es/Fr)'));

        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=propagate_masterlist&action=update_preambles&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Propagate masterlist"><span class="icon-forward"></span>Propagate</a>', '');

        //Close page
        $this->tip_close();

        $tpl = 'propagate_masterlist';
        $this->display($tpl);
    }

    function edit_columns() {
        $countryId = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        JToolBarHelper::title($this->subComponentName . '<small>[Edit Categories]</small></small>', 'edit.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('[Edit Categories]'));
        $bar = & JToolBar::getInstance('toolbar');
        
        JToolBarHelper::apply('edit_columns', 'Save Changes');
        JToolBarHelper::back();
        $this->tip_close();
        
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=view_columns', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="View Categories"><span class="icon-list"></span>View Categories</a>', '');

        $tpl = 'edit_columns';
        $this->display($tpl);
    }
    
    function view_columns() {
        JToolBarHelper::title($this->subComponentName . '<small>[View Categories - Readonly]</small></small>', 'list.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('[View Categories]'));
        $bar = & JToolBar::getInstance('toolbar');
        
        //JToolBarHelper::apply('edit_columns', 'Edit Categories');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=edit_columns', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Edit Categories"><span class="icon-edit"></span>Edit Categories</a>', '');

        JToolBarHelper::back();
        $this->tip_close();

        $tpl = 'view_columns';
        $this->display($tpl);
    }

    function view_dp() {
        $countryId = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        JToolBarHelper::title($this->subComponentName . "<small>[Edit DP for " . $this->display_location . "]</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('[Edit DP for ' . $this->display_location . ']'));

        JToolBarHelper::back();
        JToolBarHelper::apply();
        JToolBarHelper::save();

        //View Preamble
        $this->tip_view_preambles();
        $bar = & JToolBar::getInstance('toolbar');

        //Preview DP
        $this->tip_preview_dp();

        //New Column
        $this->tip_create_new_column();
        $bar = & JToolBar::getInstance('toolbar');

        //Close page
        $this->tip_close();

        $tpl = 'view_dp';
        $this->display($tpl);
    }

    function edit_category_data() {
        JToolBarHelper::title($this->subComponentName . "<small>[Edit Category Data (inline) for " . $this->display_location . "]</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Edit Category Data (inline) for ' . $this->display_location . ']'));

        JToolBarHelper::back();
        $this->tip_edit_category_preambles();
        $this->tip_preview_dp();
        $this->tip_close();

        $tpl = 'edit_category_data';
        $this->display($tpl);
    }

    function edit_category_preambles() {
        JToolBarHelper::title($this->subComponentName . "<small>[Edit Category Preambles (inline) for $this->display_location]</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Edit Category Preambles (inline) for ' . $this->display_location . ']'));

        JToolBarHelper::back();
        $this->tip_edit_category_data();
        $this->tip_preview_dp();
        $this->tip_close();

        $tpl = 'edit_category_preambles';
        $this->display($tpl);
    }

    function preambles_switches_list() {
        $countryId = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        JToolBarHelper::title($this->subComponentName . "<small>[Preambles And Switches - Master List]</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Preambles And Switches - Master List]'));

        JToolBarHelper::save('save_master_list', 'Save');
        JToolBarHelper::apply('update_master_list', 'Apply');

        JToolBarHelper::divider();
        JToolBarHelper::spacer(10);

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');
        $this->tip_close(); //Close page

        $tpl = 'preambles_switches_list';
        $this->display($tpl);
    }

    function view_dp_preambles() {
        $countryId = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        JToolBarHelper::title($this->subComponentName . "<small>[Edit DP Preambles for $this->display_location]</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Edit DP Preambles for ' . $this->display_location . ']'));

        JToolBarHelper::back();
        JToolBarHelper::apply('applyPreambles');
        JToolBarHelper::save('savePreambles');

        //View Preamble
        $this->tip_view_dp();
        $bar = & JToolBar::getInstance('toolbar');

        //Preview DP
        $this->tip_preview_dp();

        //New Column
        $this->tip_create_new_column();
        $bar = & JToolBar::getInstance('toolbar');

        //Close page
        $this->tip_close();

        $tpl = 'view_dp_preambles';
        $this->display($tpl);
    }

    function add_new_column() {

        JToolBarHelper::title($this->subComponentName . "<small>[Add New Category " . $this->display_location . "]</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Add New Category ' . $this->display_location . ']'));

        JToolBarHelper::back();
        //New Column
        //$this->tip_create_new_column();                
        $bar = & JToolBar::getInstance('toolbar');

        //add prototype js
        $this->add_prototype();

        //Close page
        //$this->tip_close();
        JToolBarHelper::back('Cancel');

        $tpl = 'add_new_column';
        $this->display($tpl);
    }

    function groups_dp_tabular() {
        JToolBarHelper::title($this->subComponentName . "[Last Modified date analyzer from the source] <small>Select page</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Last Modified date analyzer from the source] Select page'));

        $bar = & JToolBar::getInstance('toolbar');
        /*
          $href = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=groups_dp_tabular',false );
          $bar->appendButton( 'Custom', '<a  href="' . $href . '" title="Groups DP Tabular"><span class="icon-32-stats"></span>Show Stats</a>', '' );
         */
        JToolBarHelper::custom('groups_dp_tabular', 'chart', 'Show DP Stats', 'Show DP Stats', false);
        JToolBarHelper::back();

        //Close page          
        JToolBarHelper::custom('location_list', 'chevron-left', '', 'Datapages', false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list', false);
        //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close"><span class="icon-32-forward"></span>Datapages</a>', '' );
        $this->tip_close();

        $this->add_prototype();
        $tpl = 'groups_dp_tabular';
        $this->display($tpl);
    }

    function groups_dp_tabular_stats() {
        JToolBarHelper::title($this->subComponentName . " [Groups DP Aggregation Analyzer] <small>Stats page</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Groups DP Aggregation Analyzer] Stats page'));

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');

        //add prototype js
        $this->add_prototype();

        //Close page
        //JToolbarHelper::custom('groups_dp_tabular','remove','','Close',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=groups_dp_tabular', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');

        $tpl = 'groups_dp_tabular_stats';
        $this->display($tpl);
    }

    function dpdata_update_automation() {
        JToolBarHelper::title($this->subComponentName . "[DP Yearly Data Update Automation Script] <small>Select Data Source & Category</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[DP Yearly Data Update Automation Script] - Select Data Source & Category'));
        $bar = & JToolBar::getInstance('toolbar');

        JToolBarHelper::back();
        JToolBarHelper::custom('location_list', 'list', '', 'Datapages', false);
        
        //DPData Automation Script
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dpdata_update_automation', false);
        $bar->appendButton('Custom', '<button  onclick="Joomla.submitbutton(\'dpdata_update_automation\');" class="btn btn-default" ' . '" title="Preview Probable DP Changes"><span class="icon-stack"></span>Preview Probable DP Changes</button>', '');

        /*
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dpdata_update_frmsrc_result', false);
        $bar->appendButton('Custom', '<button  disabled="disabled" onclick="Joomla.submitbutton(\'dpdata_update_frmsrc_result\');" class="btn btn-default" ' . '" title="Update DP Data From Source">'
                           . '<span class="icon-apply"></span>Update DP Data</button>', '');
        */
        
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dataimport_history&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="View Past Data Import History"><span class="icon-list-2"></span>History</a>', '');        
        $this->tip_close();

        $tpl = 'dpdata_update_automation';
        $this->display($tpl);
    }

    function dpdata_update_frmsrc_result() {
        JToolBarHelper::title($this->subComponentName . " [DP Yearly Data Update Automation Results] <small>View Result</small>", 'generic.png');
        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[DP Yearly Data Update Autoation Results] View Result'));

        JToolBarHelper::back();
        $bar = JToolBar::getInstance('toolbar');
        //DPData Go Back to upload page
        //$bar->appendButton('Custom', '<button  onclick="Joomla.submitbutton(\'dpdata_update_automation\');" class="btn btn-default" ' . '" title="DPData Update Automation upload page"><span class="icon-edit"></span>DPData Update Automation Upload</button>', '');
        //DPData Wrtie the changes button
        $bar->appendButton('Custom', '<button  onclick="Joomla.submitbutton(\'dpdata_update_frmsrc_write\');" class="btn btn-default" ' . '" title="Write the DP changes"><span class="icon-edit"></span>Write the DP Changes</button>', '');

        //Close page
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dpdata_update_automation', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');
        
        $tpl = ('by_category' == $this->importType) ? 'dpdata_update_frmsrc_result' : 'dpdata_update_frmsrc_bylocation_result';
        //$tpl = (1 == $this->importOnlyBlankYears)   ? ($templateFile . '_blankyears') : $templateFile;

        $this->display($tpl);
    }
    
    function dpdata_update_frmsrc_bylocation_result() {
        JToolBarHelper::title($this->subComponentName . " [DP Yearly Data Update By Location Automation Results] <small>View Result</small>", 'generic.png');
        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[DP Yearly Data Update Autoation Results By Location] View Result'));

        JToolBarHelper::back();
        $bar = JToolBar::getInstance('toolbar');
        
        //DPData Wrtie the changes button
        $bar->appendButton('Custom', '<button  onclick="Joomla.submitbutton(\'dpdata_update_frmsrc_write\');" class="btn btn-default" ' . '" title="Write the DP changes"><span class="icon-edit"></span>Write the DP Changes</button>', '');

        //Close page
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dpdata_update_automation', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');

        $tpl = 'dpdata_update_frmsrc_bylocation_result';
        $this->display($tpl);
    }
    
    function dpdata_update_frmsrc_write() {
        JToolBarHelper::title($this->subComponentName . " [DP Yearly Data Update Write to DB] <small>View Status </small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[DP Yearly Data Update Write to DB] View Status '));

        JToolBarHelper::back();

        $bar = & JToolBar::getInstance('toolbar');
        //DPData Wrtie the changes button
        $bar->appendButton('Custom', '<button  onclick="Joomla.submitbutton(\'dpdata_update_automation\');" class="btn btn-default" ' . '" title="Go Back to DPData Upload Page"><span class="icon-edit"></span>Go Back to DPData Upload Page</button>', '');

        //History
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dataimport_history&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="View Past Data Import History"><span class="icon-list-2"></span>History</a>', '');        
        
        //Close page
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dpdata_update_automation', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');

        $tpl = 'dpdata_update_frmsrc_write';
        $this->display($tpl);
    }
    
     function dataimport_history() {

        JToolBarHelper::title('DP Data Import through Excel file History, (Only writes)', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('List all of the dataimport update history - DP Yearly Data Upload Script'));

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');
        JToolBarHelper::cancel('dpdata_update_automation', 'Close');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=frt&action=add&lang=' . $this->currentLanguage, false);
        $tpl = 'dataimport_history';
        $this->display($tpl);
    }

    function dataimport_getdetails() {
        JToolBarHelper::title('DP Import Data Raw Replace/Write History (By Column & Location)', 'generic.png');
        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('List of DP raw update history - DP Yearly Data Upload Script'));

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=dataimport_history', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');

        $tpl = 'dataimport_getdetails';
        $this->display($tpl);
    }

    function lcpgv_updates() {
        JToolBarHelper::title("DP LCPGV Updates  [Last Modified date analyzer from the source] Select page", 'generic.png');
        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('DP LCPGV Updates  [Last Modified date analyzer from the source] Select page'));

        $bar = & JToolBar::getInstance('toolbar');
        
        /*
          $href = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=lcpgv_updates',false );
          $bar->appendButton( 'Custom', '<a  href="' . $href . '" title="Groups DP Tabular"><span class="icon-32-stats"></span>Show Stats</a>', '' );
        */
        JToolBarHelper::custom('lcpgv_updates', 'chart', 'Search', 'Search', false);
        JToolBarHelper::back();

        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list&lang=en', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');

        //Close page          
        //JToolBarHelper::custom('location_list','chevron-left','','Datapages',false);
        //$href = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=location_list',false );
        //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close"><span class="icon-32-forward"></span>Datapages</a>', '' );
        //$this->tip_close();
        $this->add_prototype();
        $tpl = 'lcpgv_updates';
        $this->display($tpl);
    }

    function lcpgv_updates_stats() {
        JToolBarHelper::title("DP LCPGV Updates [Last Modified date analyzer from the source] Stats page", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('DP LCPGV Updates [Last Modified date analyzer from the source] Stats page'));

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');

        //add prototype js
        $this->add_prototype();
        //Close page
        //JToolbarHelper::custom('groups_dp_tabular','remove','','Close',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=lcpgv_updates', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');

        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=lcpgv_updates&update=1', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Update last modified date for all LCPGV records.(This may take 1 hour to finish)">Crawl & Update Last Modified Date again</a>', '');


        $tpl = 'lcpgv_updates_stats';
        $this->display($tpl);
    }

    function lcpgv_showall() {
        JToolBarHelper::title($this->subComponentName . " [Groups DP Aggregation Analyzer] <small>Stats page</small>", 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Groups DP Aggregation Analyzer] Stats page'));

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');
        //add prototype js
        $this->add_prototype();

        //Close page
        //JToolbarHelper::custom('groups_dp_tabular','remove','','Close',false);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=lcpgv_showall', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');

        $tpl = 'lcpgv_showall';
        $this->display($tpl);
    }

    function preview_dp() {
        $tpl = 'preview_dp';
        $this->display($tpl);
    }

    function clean_dp_data() {
        JToolBarHelper::title($this->subComponentName . '<small>[Clean DP Data, add space or remove spaces between the curly braces]</small>', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[Clean DP Data, add space or remove spaces between the curly braces]'));

        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages', false);
        $href1 = JRoute::_('index.php?option=com_gpo&controller=datapages&task=frt&action=add', false);

        if (count($this->clean_result) > 0 && Joomla\CMS\Factory::getApplication()->getInput()->get('type', false) == 'dry_run') {
            $href2 = JRoute::_('index.php?option=com_gpo&controller=datapages&task=clean_data&type=run', false);
            $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href2 . '" title="Close"><span class="icon-publish"></span>Make Corrections</a>', '');
        }

        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href1 . '" title="Exit"><span class="icon-back"></span>Exit</a>', '');
        //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close"><span class="icon-32-cancel"></span>Close</a>', '' );

        $tpl = 'clean_dp_data';
        $this->display($tpl);
    }

    function missing_cite_data() {
        JToolBarHelper::title($this->subComponentName . '<small>[ Missing Citations ]</small>', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_($this->subComponentName . '[ Missing Citations ]'));

        $bar = & JToolBar::getInstance('toolbar');
        $this->tip_close();
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages', false);
        //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close"><span class="icon-32-cancel"></span>Close</a>', '' );

        $tpl = 'missing_cite_data';
        $this->display($tpl);
    }

    /*
     * 
     * Find & Rpelace tool
     * functions
     * 
     */

    function frt_add() {

        $title = 'Add Search';
        JToolBarHelper::title('DP Find & Replace Tool <small><small>[' . $title . ']</small></small>', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('[DP] DP Find & Replace Tool - Add New Search'));

        $title = "Search in a specific column";
        $title_do = 'Search';
        $this->add_prototype();

        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list&lang=' . $this->currentLanguage, false);
        JToolBarHelper::cancel('location_list', 'Close');
        //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close"><span class="icon-32-cancel"></span>Close</a>', '' );

        JToolBarHelper::divider();
        JToolBarHelper::spacer(10);

        JToolBarHelper::save('frt', $title_do);
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=frt&action=history&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="View Past Replaces History"><span class="icon-edit"></span>History</a>', '');

        $tpl = 'frt_add';
        header('Content-Type: text/html; charset=utf-8');
        $this->display($tpl);
    }

    function frt_results() {

        JToolBarHelper::title('Find And Replace', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('Preview Search & Replace Results - DP Find & Replace Tool'));

        $title = 'Replace Selected Records';
        $this->add_prototype();

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=frt&action=add&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Cancel</a>', '');

        JToolBarHelper::divider();
        JToolBarHelper::spacer(10);

        JToolBarHelper::publishList('frt', $title);

        $tpl = ('en' == $this->currentLanguage) ? 'frt_results_en' : 'frt_results';
        $this->display($tpl);
    }

    function frt_history() {

        JToolBarHelper::title('Find & Replace History, (Only Replaces)', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('List of all the previous replace queries - DP Find & Replace Tool'));

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');
        JToolBarHelper::cancel('frt', 'Close');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=frt&action=add&lang=' . $this->currentLanguage, false);
        //$bar->appendButton( 'Custom', '<a href="' . $href . '" title="Close"><span class="icon-32-cancel"></span>Close</a>', '' );

        $tpl = 'frt_history';
        $this->display($tpl);
    }

    function frt_getdetails() {

        JToolBarHelper::title('DP Raw Replace History (By Column & Location)', 'generic.png');

        $document = & JFactory::getDocument();
        $document->setTitle(JText::_('List of DP raw update history - DP Find & Replace Tool'));

        JToolBarHelper::back();
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=frt&action=add&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');

        $tpl = 'frt_getdetails';
        $this->display($tpl);
    }

    function add_prototype() {
        /* Moved it in here to try and seperate out the html + javascript a little more */
        $document = &JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');
        $mootools = JURI::root(true) . '/media/system/js/mootools.js';
        if (isset($document->_scripts[$mootools])) {
            unset($document->_scripts[$mootools]);
        }
    }

    function tip_create_new_column() {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=add_new_column', false);
        $bar->appendButton('Custom', '<a  class="btn btn-default" href="' . $href . '" title="Add a New Category"><span class="icon-new"></span>Add Category</a>', '');
    }

    function tip_view_preambles() {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=view_preambles&id='
                        . Joomla\CMS\Factory::getApplication()->getInput()->get('id', false) . '&location=' . Joomla\CMS\Factory::getApplication()->getInput()->get('location', false)
                        . '&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Edit Preamble Values"><span class="icon-edit"></span>Preambles</a>', '');
    }

    function tip_view_dp() {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=view_dp&id='
                        . Joomla\CMS\Factory::getApplication()->getInput()->get('id', false) . '&location=' . Joomla\CMS\Factory::getApplication()->getInput()->get('location', false)
                        . '&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Edit Data Values"><span class="icon-edit"></span>Edit Data</a>', '');
    }

    function tip_edit_category_preambles() {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=edit_category_preambles&id='
                        . Joomla\CMS\Factory::getApplication()->getInput()->get('id', false) . '&location=' . Joomla\CMS\Factory::getApplication()->getInput()->get('location', false)
                        . '&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Edit Category Preamble Values"><span class="icon-edit"></span>Edit Category Preambles</a>', '');
    }

    function tip_edit_category_data() {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=edit_category_data&id='
                        . Joomla\CMS\Factory::getApplication()->getInput()->get('id', false) . '&location=' . Joomla\CMS\Factory::getApplication()->getInput()->get('location', false)
                        . '&lang=' . $this->currentLanguage, false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Edit Category Data Values"><span class="icon-edit"></span>Edit Category Data</a>', '');
    }

    function tip_preview_dp() {
        $bar = & JToolBar::getInstance('toolbar');
        $liveSite = JURI::root();
        $location = urlencode(Joomla\CMS\Factory::getApplication()->getInput()->get('location', false));
        if (isset($this->currentLanguage)) {
            $langURI = '&lang=' . $this->currentLanguage;
        } else {
            $langURI = '&lang=en';
        }

        $url = JRoute::_($liveSite . 'index.php?option=com_gpo&task=preview' . $langURI . '&location=') . $location;
        $href = "javascript:popup=window.open('" . $url . "','GunPolicy.org Data Page - Preview','toolbar=no,
                location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600'); popup.focus();";

        $bar->appendButton('Custom', '<a  class="btn btn-default" href="' . $href . '" title="Preview DP"><span class="icon-preview"></span>Preview DP</a>', '');
    }

    function tip_close() {
        $bar = & JToolBar::getInstance('toolbar');
        //JToolbarHelper::cancel('location_list','Close');
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list', false);
        $bar->appendButton('Custom', '<a class="btn btn-default" href="' . $href . '" title="Close"><span class="icon-cancel"></span>Close</a>', '');
    }

}
