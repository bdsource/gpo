<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//ini_set('display_errors', true);
//error_reporting(E_ALL);

class GpoControllerFeaturedCharts extends GpoController
{
    var $image_path;

    ##Default Language
    var $currentLanguage = 'en';
    var $requestURI = '';
    var $currentURI = '';
    var $languages  = array();

    function __construct()
    {
        parent::__construct();
        $this->image_path = JPATH_SITE . DS . 'images' . DS . 'gpo/charts';
        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', '');
        
        include_once(JPATH_COMPONENT.DS.'helper'.DS.'language.php');
        $mainframe =& JFactory::getApplication();
        
        ##Initialize Language
        /* 
         retrieve the value of the state variable. First see if the variable has been passed
         in the request. Otherwise retrieve the stored value. If none of these are specified, 
         the specified default value will be returned
         function syntax is getUserStateFromRequest( $key, $request, $default );
         ref: http://docs.joomla.org/How_to_use_user_state_variables 
        */
        $this->languages = array('es','fr');
        $langSelection = $mainframe->getUserStateFromRequest( "lang", 'lang', 'en' );        
        $this->currentLanguage = $langSelection;
        
        ##Initiate Request URI
        $this->_initCurrentRequestURI();
        
        if (empty($task)) {
            $this->setRedirect('index.php?option=com_gpo&controller=featuredcharts&task=published');
            $this->redirect();
        }
        
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
    
    
    function updateOrdering()
    {
        $model = &$this->getModel('Featuredcharts',array(),array('currentLang' => $this->currentLanguage));
        $orderings = Joomla\CMS\Factory::getApplication()->getInput()->get('order');
        $new_order = array();
        foreach ($orderings AS $id => $order) {
            if ($order) {
                $new_order[$id] = $order;
            }
        }
        $model->updateOrdering($new_order);

        $this->setRedirect('index.php?option=com_gpo&controller=featuredcharts&task=published');
        $this->redirect();

    }

    function published()
    {
        //ini_set('display_errors',true);
        $model = &$this->getModel('Featuredcharts',array(),array('currentLang' => $this->currentLanguage));
        $view  = &$this->getView('FeaturedCharts', 'html');

        $publishedcharts = $model->getFeaturedCharts();
        /*$view->assignRef('featuredcharts', $publishedcharts);
        $view->assignRef('pagination', $model->_pagination);*/

        $view->featuredcharts = $publishedcharts;
        $view->pagination = $model->_pagination;
        //$view->assignRef('rows', count($publishedcharts));
       /* $view->assignRef('filter_order', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id'));
        $view->assignRef('filter_order_Dir', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc'));
        $view->assignRef('total', $model->_total);
        $view->assign('ordering', true);*/

        $view->filter_order = &Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
        $view->filter_order_Dir = &Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc');
        $view->total = &$model->_total;
        $view->ordering = true;


        /*$view->assign( 'currentLanguage', $this->currentLanguage);
        $view->assign( 'requestURI', $this->requestURI );
        $view->assign( 'currentURI', $this->currentURI );*/

        $view->currentLanguage = $this->currentLanguage;
        $view->requestURI = $this->requestURI;
        $view->currentURI = $this->currentURI;


        $view->published();
    }

    function edit()
    {
        $model = &$this->getModel('Featuredcharts',array(),array('currentLang' => $this->currentLanguage));
        $view = &$this->getView('FeaturedCharts', 'html');

        $chart = $model->getFeaturedChart(Joomla\CMS\Factory::getApplication()->getInput()->get('id'));
        //$view->assignRef('chart', $chart);
        $view->chart = $chart;
        $view->isNew = false;
        /*$view->assign( 'currentLanguage', $this->currentLanguage);
        $view->assign( 'requestURI', $this->requestURI );
        $view->assign( 'currentURI', $this->currentURI );*/

        $view->currentLanguage = $this->currentLanguage;
        $view->requestURI = $this->requestURI;
        $view->currentURI = $this->currentURI;
        
        $view->edit();
    }

    function create()
    {
        $model = &$this->getModel('Featuredcharts',array(),array('currentLang' => $this->currentLanguage));
        $view = &$this->getView('FeaturedCharts', 'html');

        $view->isNew = true;
       /* $view->assign( 'currentLanguage', $this->currentLanguage);
        $view->assign( 'requestURI', $this->requestURI );
        $view->assign( 'currentURI', $this->currentURI );*/

        $view->currentLanguage= &$this->currentLanguage;
        $view->requestURI= &$this->requestURI;
        $view->currentURI= &$this->currentURI;
        
        $view->edit();
    }

    function save() {
        $model = &$this->getModel('Featuredcharts', array(), array('currentLang' => $this->currentLanguage));
        jimport('joomla.filesystem.file');
        $file = &$this->getModel('Featuredcharts', array(), array('currentLang' => $this->currentLanguage));

        if ('POST' == JRequest::getMethod()) {
            $file = Joomla\CMS\Factory::getApplication()->getInput()->get('chart_image', '', 'FILES', 'array');
            $data = array(
                'location' => Joomla\CMS\Factory::getApplication()->getInput()->get('chart_location', '', 'POST'),
                'title' => Joomla\CMS\Factory::getApplication()->getInput()->get('chart_title', '', 'POST'),
                'image' => Joomla\CMS\Factory::getApplication()->getInput()->get('old_chart_image', '', 'POST'),
                'ordering' => trim(Joomla\CMS\Factory::getApplication()->getInput()->get('chart_order', '', 'POST', 'int')),
                'language' => $this->currentLanguage
            );

            $errors = array();
            $data['modified'] = time();
            if (empty($data['location'])) {
                $errors[] = 'Location must be selected!';
            }
            if (empty($data['title'])) {
                $errors[] = 'Title field is missing!';
            }
            if (empty($file['name']) && empty($data['image'])) {
                $errors[] = 'Chart Image is missing';
            } else if (!empty($file['name'])) {
                $filename = $data['modified'] . '_' . JFile::makeSafe($file['name']);
                $ext = strtolower(JFile::getExt($filename));
                if (!($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif')) {
                    $errors[] = 'Chart Image format is not jpg/gif/png';
                }
            }

            if (count($errors)) {
                $errors = implode('<br/>', $errors);
                $this->setRedirect('index.php?option=com_gpo&controller=featuredcharts&task=published', $errors);
                $this->redirect();
                return;
            }

            if (!empty($filename)) {
                $src = $file['tmp_name'];
                $dest = $this->image_path . DS . $filename;
                JFile::upload($src, $dest);

                //On Edit delete the old file
                if (!empty($data['image']))
                    unlink($this->image_path . DS . $data['image']);

                $data['image'] = $filename;
            }
            //no error found. proceed saving
            if (!(Joomla\CMS\Factory::getApplication()->getInput()->get('id', ''))) { //new record
                $result = $model->saveFeaturedCharts($data);
                $msg = "Chart added succesffuly!";
            } else { //update record
                $result = $model->saveFeaturedCharts($data, Joomla\CMS\Factory::getApplication()->getInput()->get('id', '', 'POST'));
                $msg = "Chart updated successfully";
            }


            if ($result) {
                $this->setRedirect('index.php?option=com_gpo&controller=featuredcharts&task=published', $msg);
                $this->redirect();
            } else {
                $this->setRedirect('index.php?option=com_gpo&controller=featuredcharts&task=published', 'Error while adding/updating chart! Addition failed!');
                $this->redirect();
            }
        } else {
            $this->setRedirect('index.php?option=com_gpo&controller=featuredcharts&task=published', 'Invalid request method!');
            $this->redirect();
        }
    }

    function delete()
    {
        $model_name = 'Featuredcharts';
        $model = &$this->getModel($model_name,array(),array('currentLang'=>$this->currentLanguage));
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '');

        if (empty($id)) {
            $msg = 'No featured chart found to delete';
        } else {
            //get the chart detail
            $chart_info = $model->getFeaturedChart($id);
            //remove the chart image
            if (unlink($this->image_path . DS . $chart_info->image) OR !file_exists($this->image_path . DS . $chart_info->image)) {
                if ($model->deleteFeaturedChart($id)) {
                    $msg = 'The featured chart is deleted successfully!';
                } else {
                    $msg = 'The featured chart is could not be deleted!';
                }
            } else {
                $msg = "The deletion is aborted as the respective image from filesystem could not be deleted!";
            }
        }
        $this->setRedirect('index.php?option=com_gpo&controller=featuredcharts&task=published', $msg);
        $this->redirect();
    }

    function log($txt)
    {
        $f = fopen(JPATH_BASE . '/log.txt', 'a');
        fwrite($f, $txt);
        fclose($f);
    }

    function handle_image_upload()
    {
        //import joomlas filesystem functions, we will do all the filewriting with joomlas functions,
        //so if the ftp layer is on, joomla will write with that, not the apache user, which might
        //not have the correct permissions
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        //this is the name of the field in the html form, filedata is the default name for swfupload
        //so we will leave it as that
        $fieldName = 'Filedata';

        //any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        //$this->log(print_r($_FILES, true));

        if ($fileError > 0) {
            switch ($fileError)
            {
                case 1:
                    echo JText::_('FILE TO LARGE THAN PHP INI ALLOWS');
                    return;

                case 2:
                    echo JText::_('FILE TO LARGE THAN HTML FORM ALLOWS');
                    return;

                case 3:
                    echo JText::_('ERROR PARTIAL UPLOAD');
                    return;

                case 4:
                    echo JText::_('ERROR NO FILE');
                    return;
            }
        }

        //check for filesize
        $fileSize = $_FILES[$fieldName]['size'];
        if ($fileSize > 2000000) {
            echo JText::_('FILE BIGGER THAN 2MB');
        }

        //check the file extension is ok
        $fileName = time() . $_FILES[$fieldName]['name'];
        $uploadedFileNameParts = explode('.', $fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        $validFileExts = explode(',', 'jpeg,jpg,png,gif');

        //assume the extension is false until we know its ok
        $extOk = false;

        //go through every ok extension, if the ok extension matches the file extension (case insensitive)
        //then the file extension is ok
        foreach ($validFileExts as $key => $value)
        {
            if (preg_match("/$value/i", $uploadedFileExtension)) {
                $extOk = true;
            }
        }

        if ($extOk == false) {
            echo JText::_('INVALID EXTENSION');
            return;
        }

        //the name of the file in PHP's temp directory that we are going to move to our folder
        $fileTemp = $_FILES[$fieldName]['tmp_name'];

        //for security purposes, we will also do a getimagesize on the temp file (before we have moved it
        //to the folder) to check the MIME type of the file, and whether it has a width and height
        $imageinfo = getimagesize($fileTemp);

        //we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
        //types, where we might miss one (whitelisting is always better than blacklisting)
        $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
        $validFileTypes = explode(",", $okMIMETypes);

        //if the temp file does not have a width or a height, or it has a non ok MIME, return
        if (!is_int($imageinfo[0]) || !is_int($imageinfo[1]) || !in_array($imageinfo['mime'], $validFileTypes)) {
            echo JText::_('INVALID FILETYPE');
            return;
        }

        //lose any special characters in the filename
        $fileName = preg_replace("/[^A-Za-z0-9.]/", "_", $fileName);

        //always use constants when making file paths, to avoid the possibilty of remote file inclusion
        if (!file_exists($this->image_path)) {
            @mkdir($this->image_path, 0775, true);
        }
        $uploadPath = $this->image_path . DS . $fileName;

        if (!JFile::upload($fileTemp, $uploadPath)) {
            echo JText::_('ERROR MOVING FILE');
            return;
        }
        else
        {
            // success, exit with code 0 for Mac users, otherwise they receive an IO Error
            echo $fileName;
            exit(0);
        }

    }


}
