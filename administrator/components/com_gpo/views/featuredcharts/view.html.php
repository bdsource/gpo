<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');


class GpoViewFeaturedCharts extends JViewLegacy
{
		function display($tpl = null)
	{
		GpoHelper::addSubmenu('featuredcharts');
        if (JVERSION >= '3.0')
        {
            //$this->sidebar = JHtmlSidebar::render();
        }
		parent::display( $tpl );
	}

    function edit($tpl = 'edit')
    {

        if ($this->isNew) {
            $title = JText::_('Featured Charts: <small><small>[Create New]</small></small>');
            JToolBarHelper::title($title, 'generic.png');
            
            $document = &JFactory::getDocument();
            $document->setTitle(JText::_( 'Featured Charts: [Create New]' ));
            
        } else {
            $title = JText::_('Featured Charts: <small><small>[Edit]</small></small>');
            JToolBarHelper::title($title, 'generic.png');
            
            $document = &JFactory::getDocument();
            $document->setTitle(JText::_( 'Featured Charts: [Edit]' ));
        }
        jimport('joomla.environment.uri' );
        $host = JURI::root() ;
        $document = &JFactory::getDocument();
        $document->addScript($host . '/media/system/swfupload/swfupload.js');
        $document->addScript($host . '/media/system/swfupload/swfupload.queue.js');
        $document->addScript($host . '/media/system/swfupload/fileprogress.js');
        $document->addScript($host . '/media/system/swfupload/handlers.js');
        $document->addStyleSheet($host. '/media/system/swfupload/default.css');

        //when we send the files for upload, we have to tell Joomla our session, or we will get logged out
        $session = & JFactory::getSession();

        $swfUploadHeadJs = '
var swfu;

window.onload = function()
{

var settings =
{
        //this is the path to the flash file, you need to put your components name into it
        flash_url : "' . $host  . 'media/system/swfupload/swfupload.swf",

        //we can not put any vars into the url for complicated reasons, but we can put them into the post...
        upload_url: "'.$host.'administrator/index.php",
        post_params:
        {
                "option" : "com_gpo",
                "controller" : "featuredcharts",
                "task" : "handle_image_upload",
                "id" : "' . @$this->chart->id . '",
                "' . $session->getName() . '" : "' . $session->getId() . '",
                "format" : "raw"
        },
        //you need to put the session and the "format raw" in there, the other ones are what you would normally put in the url
        file_size_limit : "5 MB",
        //client side file chacking is for usability only, you need to check server side for security
        file_types : "*.jpg;*.jpeg;*.gif;*.png",
        file_types_description : "Image Files",
        file_upload_limit : 1,
        file_queue_limit : 1,
        custom_settings :
        {
                progressTarget : "fsUploadProgress",
                cancelButtonId : "btnCancel"
        },
        debug: false,

        // Button settings
        button_image_url: "",
        button_width: "85",
        button_height: "29",
        button_placeholder_id: "spanButtonPlaceHolder",
        button_text: \'<span class="theFont">Upload File</span>\',
        button_text_style: ".theFont { font-size: 13; }",
        button_text_left_padding: 5,
        button_text_top_padding: 5,

        // The event handler functions are defined in handlers.js
        file_queued_handler : fileQueued,
        file_queue_error_handler : fileQueueError,
        file_dialog_complete_handler : fileDialogComplete,
        upload_start_handler : uploadStart,
        upload_progress_handler : uploadProgress,
        upload_error_handler : uploadError,
        upload_success_handler : uploadSuccess1,
        upload_complete_handler : uploadComplete,
        queue_complete_handler : queueComplete     // Queue plugin event
};
swfu = new SWFUpload(settings);
};

function uploadSuccess1(file, serverData){
    try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus("Complete.");
		progress.toggleCancel(false);
        document.getElementById("chart_image").value = serverData;
		document.getElementById("fsUploadProgress").style.display  = "none";
		document.getElementById("uploadBox").style.display = "none";
        alert("Uploaded successfully! You may now fill up the fields and hit Save button");
	} catch (ex) {
		this.debug(ex);
	}
}

';

        //$document->addScriptDeclaration($swfUploadHeadJs);

        JToolBarHelper::save();
        $this->tip_close();
        $tpl = 'edit';
        $this->display($tpl);
    }

    function published()
    {
       // JHTML::_('behavior.modal');

        $title = JText::_('Featured Charts');
        JToolBarHelper::title($title, 'generic.png');
        
        $document = &JFactory::getDocument();
        $document->setTitle($title);

        $this->tip_create();
        $this->tip_update_ordering();

        $tpl = 'published';
        $this->display($tpl);
    }


    function tip_create()
    {
        $bar = &JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=featuredcharts' . '&task=create', false);
        $bar->appendButton('Custom', '<a href="' . $href . '" title="Create new featured chart"><span class="icon-32-new"></span>Create New</a>', '');
    }

    function tip_update_ordering()
    {
        $bar = &JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=featuredcharts' . '&task=updateOrdering', false);
        $bar->appendButton('Custom', '<a href="#" onclick="javascript:document.getElementById(\'task\').value=\'updateordering\';document.getElementById(\'adminForm\').submit();" title="Update Ordering"><span class="icon-32-save"></span>Update Ordering</a>', '');
    }

    function tip_close()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $href = JRoute::_('index.php?option=com_gpo&controller=featuredcharts', false);
        $bar->appendButton('Custom', '<a href="' . $href . '" title="Close without saving"><span class="icon-32-cancel"></span>Close</a>', '');
    }
}

