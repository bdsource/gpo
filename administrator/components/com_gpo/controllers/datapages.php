<?php

/**
 * DataPages Controller for GPO Component
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:components/
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

$frontBase = str_replace( "administrator",'',JPATH_BASE);
include_once $frontBase . 'libraries/phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Datapages Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class GpoControllerDatapages extends GpoController {
    ##Default Language

    var $currentLanguage = 'en';
    var $requestURI = '';
    var $currentURI = '';
    var $languages = array();

    function __construct() {
        parent::__construct();
        require_once(JPATH_COMPONENT . DS . 'helper' . DS . 'datapage.php');
        require_once(JPATH_COMPONENT . DS . 'helper' . DS . 'language.php');
        $mainframe = JFactory::getApplication();
        $this->oUser = JFactory::getUser();
        $this->isRoot = $this->oUser->get('isRoot');
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        ##groupusers: 7 and 8 is administrator and super administrator
        $this->isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;

        if ($this->isAdministrator !== true) {
            $link = JRoute::_('index.php');
            $this->setRedirect($link);
            $this->redirect();
        }

        ##Initialize Language
        /*
          retrieve the value of the state variable. First see if the variable has been passed
          in the request. Otherwise retrieve the stored value. If none of these are specified,
          the specified default value will be returned
          function syntax is getUserStateFromRequest( $key, $request, $default );
          ref: http://docs.joomla.org/How_to_use_user_state_variables
         */
        $this->languages = array('es', 'fr');
        $langSelection = $mainframe->getUserStateFromRequest("lang", 'lang', 'en');
        $this->currentLanguage = $langSelection;
        if( empty($this->currentLanguage) ) {
            $this->currentLanguage = 'en';
        }
        
        ##Initiate Request URI
        $this->_initCurrentRequestURI();

        $this->registerTask('', 'cpanel');
    }

    /*
     * ##Make Request URI 
     * For language versions
     * 
     */

    function _initCurrentRequestURI() {

        $u =  JURI::getInstance();
        $requestURI = $u->toString();
        $langCodeToReplace = '&lang=' . $this->currentLanguage;
        $currentURI = str_ireplace($langCodeToReplace, '', $requestURI);

        $this->currentURI = $currentURI;
        $this->requestURI = $requestURI;

        return true;
    }

    function cpanel() {
        $this->location_list();
    }

    function location_translate() {
        $model = & $this->getModel('Locations');

        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && !empty($_POST)) {
            $locationIdString = Joomla\CMS\Factory::getApplication()->getInput()->get('locationId', false);
            $locationNewName = trim(strip_tags(Joomla\CMS\Factory::getApplication()->getInput()->get('locationNewName', false,null, 'STRING')));
            //echo 'test-';echo $locationNewName; exit();
            $locationIdString = explode('_', $locationIdString);
            $locationId = trim($locationIdString[0]);
            $locationLang = trim($locationIdString[1]);
            $updateField = trim($locationIdString[2]);
            //var_dump($locationIdString);
            $model->translateLocationName($locationId, $locationNewName, $locationLang, $updateField);
            echo $locationNewName;
            exit();
        }

        $view = & $this->getView('Datapages', 'html');
        $oItems = $model->getAllLocationData();
       /* $view->assignRef('rows', $oItems);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->rows=$oItems;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

        $view->location_translate();
    }

    function view_dp() {
        $model = & $this->getModel('Datapages', '', array());

        $country_id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location_name = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        $location = $model->getLocationById($country_id);
        $display_location = ($location->name) ? $location->name : $location_name;
        $dp_metadata = $model->getDPMetaDataInfo();
        $dp_data = $model->getDpDataByLocationId($country_id);
        $dp_total_fields = count($dp_metadata);

        $view = & $this->getView('Datapages', 'html');
    /*    $view->assign('dp_data', $dp_data);
        $view->assign('dp_metadata', $dp_metadata);
        $view->assign('dp_total_fields', $dp_total_fields);
        $view->assign('location', $location);
        $view->assign('display_location', $display_location);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI); */

        $view->dp_data=$dp_data;
        $view->dp_metadata=$dp_metadata;
        $view->dp_total_fields=$dp_total_fields;
        $view->location=$location;
        $view->display_location=$display_location;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

        $view->view_dp();
    }

    function view_preambles() {
        $model = & $this->getModel('Datapages', '', array());
        $country_id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location_name = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        $location = $model->getLocationById($country_id);
        $display_location = ($location->name) ? $location->name : $location_name;
        $dp_metadata = $model->getDPPreamblesMetaData();
        $dp_data = $model->getDpPreamblesByLocationId($country_id);
        $dp_total_fields = count($dp_metadata);

        $view = & $this->getView('Datapages', 'html');

      /*  $view->assign('dp_data', $dp_data);
        $view->assign('dp_metadata', $dp_metadata);
        $view->assign('dp_total_fields', $dp_total_fields);
        $view->assign('location', $location);
        $view->assign('display_location', $display_location);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->dp_data=$dp_data;
        $view->dp_metadata=$dp_metadata;
        $view->dp_total_fields=$dp_total_fields;
        $view->location=$location;
        $view->display_location=$display_location;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

        $view->view_dp_preambles();
    }

    /*
     * Inline edit category methods 
     * 
     */

    function edit_category_data() {
        $model = & $this->getModel('Datapages', '', array());
        $country_id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location_name = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        $location = $model->getLocationById($country_id);
        $display_location = ($location->name) ? $location->name : $location_name;
        $dp_metadata = $model->getDPMetaDataInfo();
        $dp_data = $model->getDpDataByLocationAndLang($country_id, 'en');
        $dp_data_es = $model->getDpDataByLocationAndLang($country_id, 'es');
        $dp_data_fr = $model->getDpDataByLocationAndLang($country_id, 'fr');
        $dp_total_fields = count($dp_metadata);

        $liveSite = JURI::root();
        $previewDPURL = JRoute::_($liveSite . 'index.php?option=com_gpo&task=preview&' . 'location=') . urlencode($location_name);
        $previewMultiDPURL = JRoute::_($liveSite . 'preview_dp.php') . '?location=' . urlencode($location_name);

        $view = & $this->getView('Datapages', 'html');
      /*  $view->assign('dp_data', $dp_data);
        $view->assign('dp_data_es', $dp_data_es);
        $view->assign('dp_data_fr', $dp_data_fr);
        $view->assign('dp_metadata', $dp_metadata);
        $view->assign('dp_total_fields', $dp_total_fields);
        $view->assign('location', $location);
        $view->assign('liveSite', urlencode($liveSite));
        $view->assign('display_location', $display_location);
        $view->assign('previewDPURL', $previewDPURL);
        $view->assign('previewMultiDPURL', $previewMultiDPURL);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->dp_data=$dp_data;
        $view->dp_data_es=$dp_data_es;
        $view->dp_data_fr=$dp_data_fr;
        $view->dp_metadata=$dp_metadata;
        $view->dp_total_fields=$dp_total_fields;
        $view->location=$location;
        $view->liveSite=urlencode($liveSite);
        $view->display_location=$display_location;
        $view->previewDPURL=$previewDPURL;
        $view->previewMultiDPURL=$previewMultiDPURL;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

        $view->edit_category_data();
    }

    function updateColumnData() {
        $datapageModel = & $this->getModel('Datapages', '', array());

        $locationId = Joomla\CMS\Factory::getApplication()->getInput()->get('locationId', false);
        $columnName = Joomla\CMS\Factory::getApplication()->getInput()->get('columnName', false);
        $saveAll = Joomla\CMS\Factory::getApplication()->getInput()->get('saveAll', false);

        $langOptionEn = Joomla\CMS\Factory::getApplication()->getInput()->get('language_en', false);
        $langOptionEs = Joomla\CMS\Factory::getApplication()->getInput()->get('language_es', false);
        $langOptionFr = Joomla\CMS\Factory::getApplication()->getInput()->get('language_fr', false);

        if ($saveAll) {
            $langString = 'En  Es  Fr ';
        } else {
            $langString = !empty($langOptionEn) ? ucfirst($langOptionEn) . '  ' : '';
            $langString .= !empty($langOptionEs) ? ucfirst($langOptionEs) . '  ' : '';
            $langString .= !empty($langOptionFr) ? ucfirst($langOptionFr) . '  ' : '';
        }

        $dataEn = $_POST['data_en_' . $columnName]; //Joomla\CMS\Factory::getApplication()->getInput()->get('data_en_'.$columnName, false, $_POST, 'none', JREQUEST_ALLOWHTML);
        $dataEs = $_POST['data_es_' . $columnName]; //Joomla\CMS\Factory::getApplication()->getInput()->get('data_es_'.$columnName, false, 'post', 'none', JREQUEST_ALLOWHTML);
        $dataFr = $_POST['data_fr_' . $columnName]; //Joomla\CMS\Factory::getApplication()->getInput()->get('data_fr_'.$columnName, false, $_POST, 'none', JREQUEST_ALLOWHTML);


        if (!empty($langOptionEn) || !empty($saveAll)) {
            $resultEn = $datapageModel->_updateColumnData($columnName, $dataEn, $locationId, 'en');
        }

        if (!empty($langOptionEs) || !empty($saveAll)) {
            $resultEs = $datapageModel->_updateColumnData($columnName, $dataEs, $locationId, 'es');
        }

        if (!empty($langOptionFr) || !empty($saveAll)) {
            $resultFr = $datapageModel->_updateColumnData($columnName, $dataFr, $locationId, 'fr');
        }

        if ($resultEn || $resultEs || $resultFr) {
            echo "Success: <b>$columnName</b> Data updated in $langString at " . date('Y-m-d H:i:s');
        } else {
            echo "ERROR|| Update failed, Please try again";
        }

        exit();
    }

    function edit_category_preambles() {
        $model = & $this->getModel('Datapages', '', array());
        $country_id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location_name = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        $location = $model->getLocationById($country_id);
        $display_location = ($location->name) ? $location->name : $location_name;
        $dp_metadata = $model->getDPPreamblesMetaData();
        $dp_data = $model->getDpPreamblesByLocationAndLang($country_id, 'en');
        $dp_data_es = $model->getDpPreamblesByLocationAndLang($country_id, 'es');
        $dp_data_fr = $model->getDpPreamblesByLocationAndLang($country_id, 'fr');
        $dp_total_fields = count($dp_metadata);

        $liveSite = JURI::root();
        $previewDPURL = JRoute::_($liveSite . 'index.php?option=com_gpo&task=preview&' . 'location=' . $location_name);

        $view = & $this->getView('Datapages', 'html');

        /*$view->assign('dp_data', $dp_data);
        $view->assign('dp_data_es', $dp_data_es);
        $view->assign('dp_data_fr', $dp_data_fr);
        $view->assign('dp_metadata', $dp_metadata);
        $view->assign('dp_total_fields', $dp_total_fields);
        $view->assign('previewDPURL', $previewDPURL);
        $view->assign('location', $location);
        $view->assign('display_location', $display_location);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->dp_data=&$dp_data;
        $view->dp_data_es=&$dp_data_es;
        $view->dp_data_fr=&$dp_data_fr;
        $view->dp_metadata=&$dp_metadata;
        $view->dp_total_fields=&$dp_total_fields;
        $view->previewDPURL=&$previewDPURL;
        $view->location=&$location;
        $view->display_location=&$display_location;
        $view->currentLanguage=&$this->currentLanguage;
        $view->requestURI=&$this->requestURI;
        $view->currentURI=&$this->currentURI;


        $view->edit_category_preambles();
    }

    function updateColumnPreamble() {
        $datapageModel = & $this->getModel('Datapages', '', array());

        $locationId = Joomla\CMS\Factory::getApplication()->getInput()->get('locationId', false);
        $columnName = Joomla\CMS\Factory::getApplication()->getInput()->get('columnName', false);
        $saveAll = Joomla\CMS\Factory::getApplication()->getInput()->get('saveAll', false);

        $langOptionEn = Joomla\CMS\Factory::getApplication()->getInput()->get('language_en', false);
        $langOptionEs = Joomla\CMS\Factory::getApplication()->getInput()->get('language_es', false);
        $langOptionFr = Joomla\CMS\Factory::getApplication()->getInput()->get('language_fr', false);

        if ($saveAll) {
            $langString = 'En  Es  Fr ';
        } else {
            $langString = !empty($langOptionEn) ? ucfirst($langOptionEn) . '  ' : '';
            $langString .= !empty($langOptionEs) ? ucfirst($langOptionEs) . '  ' : '';
            $langString .= !empty($langOptionFr) ? ucfirst($langOptionFr) . '  ' : '';
        }

        $dataEn = $_POST['data_en_' . $columnName]; //Joomla\CMS\Factory::getApplication()->getInput()->get('data_en_'.$columnName, false);
        $dataEs = $_POST['data_es_' . $columnName]; //Joomla\CMS\Factory::getApplication()->getInput()->get('data_es_'.$columnName, false);
        $dataFr = $_POST['data_fr_' . $columnName]; //Joomla\CMS\Factory::getApplication()->getInput()->get('data_fr_'.$columnName, false);

        if (!empty($langOptionEn) || !empty($saveAll)) {
            $resultEn = $datapageModel->_updateColumnPreamble($columnName, $dataEn, $locationId, 'en');
        }

        if (!empty($langOptionEs) || !empty($saveAll)) {
            $resultEs = $datapageModel->_updateColumnPreamble($columnName, $dataEs, $locationId, 'es');
        }

        if (!empty($langOptionFr) || !empty($saveAll)) {
            $resultFr = $datapageModel->_updateColumnPreamble($columnName, $dataFr, $locationId, 'fr');
        }

        if ($resultEn || $resultEs || $resultFr) {
            echo "Success: <b>$columnName</b> Preamble updated in $langString at " . date('Y-m-d H:i:s');
        } else {
            echo "ERROR|| Update failed, Please try again";
        }

        exit();
    }

    function preambles_switches_list() {
        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', false);

        $model = & $this->getModel('Datapages', '', array());
        if ('save_master_list' == $task || 'update_master_list' == $task) {
            $retVal = $model->updatePreamblesMasterList();
            $redirectTask = ($task == 'save_master_list') ? 'location_list' : 'preambles_switches_list';
            $responseMsg = ($retVal) ? 'Preambles Master List has been updated successfully' : 'Sorry an error has occurred, please try again';

            $responseLink = JRoute::_("index.php?option=com_gpo&controller=datapages&task=$redirectTask"
                            . '&lang=' . $this->currentLanguage, false);
            $this->setRedirect($responseLink, $responseMsg);
            $this->redirect();
        } else {
            $dp_hierarchy = getDPHierarchy(3);
            $dp_tree = processDPHierarchy($dp_hierarchy);
            $preambles_master_list = $model->getPreamblesMasterList();
            $columnTitles = getDPColumnTitles($this->currentLanguage);
            $view = & $this->getView('Datapages', 'html');

            $view->dp_hierarchy=&$dp_hierarchy;
            $view->dp_tree=&$dp_tree;
            $view->columnTitles=&$columnTitles;
            $view->preambles_master_list=&$preambles_master_list;
            $view->currentLanguage=&$this->currentLanguage;
            $view->requestURI=$this->requestURI;
            $view->currentURI=$this->currentURI;

/*
            $view->assignRef('dp_hierarchy', $dp_hierarchy);
            $view->assignRef('dp_tree', $dp_tree);
            $view->assignRef('columnTitles', $columnTitles);
            $view->assignRef('preambles_master_list', $preambles_master_list);
            $view->assignRef('currentLanguage', $this->currentLanguage);
            $view->assign('requestURI', $this->requestURI);
            $view->assign('currentURI', $this->currentURI);
*/
            $view->preambles_switches_list();
        }
    }

    function save_master_list() {
        //delegate the task to the main handler method for this
        $this->preambles_switches_list();
    }

    function update_master_list() {
        //delegate the task to the main handler method for this
        $this->preambles_switches_list();
    }

    function add_new_dp() {
        $model = & $this->getModel('Datapages');
        $view = & $this->getView('Datapages', 'html');
        $view->admin_view_dp();
    }

    function location_list() {
        $model = & $this->getModel('Datapages', '', array());
        $view = & $this->getView('Datapages', 'html');

        $countries_on_record = $model->getAllLocationData();
        $allDPWithLocations = $model->getAllDPWithLocations(Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'name'), Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc'), Joomla\CMS\Factory::getApplication()->getInput()->get('search', '')
        );
        $view->countries_on_record = &$countries_on_record;
        $view->allDPWithLocations = &$allDPWithLocations;
        $view->filter_key = &Joomla\CMS\Factory::getApplication()->getInput()->get('search', '');
        $view->filter_order = &Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'name');
        $view->filter_order_Dir = &Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc');
        $view->currentLanguage = &$this->currentLanguage;
        $view->requestURI = $this->requestURI;
        $view->currentURI = $this->currentURI;
        $view->isRoot = $this->isRoot;
        $view->location_list();
    }

    function editColumnTitle() {
        //handle updating the column title only
        if ('POST' == Joomla\CMS\Factory::getApplication()->getInput()->getMethod()) {
            $model = & $this->getModel('Datapages', '', array());
            $column_name = trim(Joomla\CMS\Factory::getApplication()->getInput()->get('columnAlias','', 'string'));
            $column_title = trim(strip_tags(Joomla\CMS\Factory::getApplication()->getInput()->get('columnNewTitle', '', 'string')));
            $retVal = $model->updateDPHierarchyColumnTitle($column_name, $column_title);
            echo $column_title;
            exit();
        }

        echo "Error: Please try again...";
        exit();
    }

    function edit_columns() {

        error_reporting(0);
        $db = JFactory::getDBO();
        //handle delete column
        if (Joomla\CMS\Factory::getApplication()->getInput()->get('delete')) {
            /* By Amlana for delete all sub categories [Start] */
            $qlev2 = "SELECT `column_name` FROM `#__gpo_datapage_hierarchy` WHERE parent_id IN(SELECT id FROM `#__gpo_datapage_hierarchy` WHERE `column_name` = '" . Joomla\CMS\Factory::getApplication()->getInput()->get('delete') . "')";
            $db->setQuery($qlev2);
            $dlev2 = $db->loadObjectList();

            foreach ($dlev2 as $key2 => $val2) {
                $qlev3 = "SELECT `column_name` FROM `#__gpo_datapage_hierarchy` WHERE parent_id IN(SELECT id FROM `#__gpo_datapage_hierarchy` WHERE `column_name` = '" . $val2->column_name . "')";
                $db->setQuery($qlev3);
                $dlev3 = $db->loadObjectList();
                foreach ($dlev3 as $key3 => $val3) {
                    //echo "<br>VAL3==".$val3->column_name;
                    $this->_edit_column_handle_delete($val3->column_name);
                }
                $this->_edit_column_handle_delete($val2->column_name);
            }
            /* By Amlana for delete all sub categories [Start] */

            $this->_edit_column_handle_delete(Joomla\CMS\Factory::getApplication()->getInput()->get('delete'));
        }
        //handle updating the column title
        if ('edit_columns' == Joomla\CMS\Factory::getApplication()->getInput()->get('task') AND 'POST' == Joomla\CMS\Factory::getApplication()->getInput()->getMethod()) {
            $this->_edit_columns_handle_update();
        }

        //showing current column info
        $model = & $this->getModel('Datapages', '', array());
        $view = & $this->getView('Datapages', 'html');

        //get the table info from db.
        $columns_info = $model->getDPColumnsInfo();
        $regionAggregationOptions = getRegionAggregationOptions();
        $verticalChartLabels = getYChartLabelOptions();

        //assign variable to view
     

       $view->columns_info=&$columns_info;
       $view->regionAggregationOptions=&$regionAggregationOptions;
       $view->verticalChartLabels=&$verticalChartLabels;
       $view->languages=$this->languages;
       $view->currentLanguage=&$this->currentLanguage;
       $view->requestURI=$this->requestURI;
       $view->currentURI=$this->currentURI;

        $view->edit_columns();
    }
    
    
    function view_columns() {

        error_reporting(0);
        $db = JFactory::getDBO();
        
        //showing current column info
        $model = & $this->getModel('Datapages', '', array());
        $view = & $this->getView('Datapages', 'html');

        //get the table info from db.
        $columns_info = $model->getDPColumnsInfo();
        $regionAggregationOptions = getRegionAggregationOptions();
        $verticalChartLabels = getYChartLabelOptions();

        //assign variable to view
       /* $view->assignRef('columns_info', $columns_info);
        $view->assignRef('regionAggregationOptions', $regionAggregationOptions);
        $view->assignRef('verticalChartLabels', $verticalChartLabels);
        $view->assign('languages', $this->languages);
        $view->assignRef('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->columns_info = &$columns_info;
        $view->regionAggregationOptions=&$regionAggregationOptions;
        $view->verticalChartLabels=&$verticalChartLabels;
        $view->languages = $this->languages;
        $view->currentLanguage = &$this->currentLanguage;
        $view->requestURI = $this->requestURI;
        $view->currentURI = $this->currentURI;

        $view->view_columns();
    }

    protected function _edit_columns_handle_update() {
        $model = & $this->getModel('Datapages');
        $column_name = Joomla\CMS\Factory::getApplication()->getInput()->get('column_name');
        $column_title = Joomla\CMS\Factory::getApplication()->getInput()->get('column_title');
        $display_type = Joomla\CMS\Factory::getApplication()->getInput()->get('display_type');
        $region_aggregation = Joomla\CMS\Factory::getApplication()->getInput()->get('aggregation');
        $y_axis_label = Joomla\CMS\Factory::getApplication()->getInput()->get('y_axis_label');
        $sort_order = Joomla\CMS\Factory::getApplication()->getInput()->get('sort_order');
        $gcite_id = Joomla\CMS\Factory::getApplication()->getInput()->get('gcite_id');
        $parent_id = Joomla\CMS\Factory::getApplication()->getInput()->get('parent_id');



        foreach ($column_name as $key => $col_alias) {
            //check if the column title is not empty
            //display type is added on 31.03.2011
            $model->updateDPHierarchyColumnInfo($col_alias, $display_type[$key], $gcite_id[$key], $region_aggregation[$key], $y_axis_label[$key], $sort_order[$key], $parent_id[$key]);
        }

        $link = JRoute::_('index.php?option=com_gpo&controller=datapages&task=edit_columns&lang=' . $this->currentLanguage, false);
        $msg = 'DP Column options & settings are updated!';
        $this->setRedirect($link, $msg);
        $this->redirect();
    }

    /**
     * Delete column info from tables. Here is how it will work:
     * 1. Delete a record from gpo_datapage_hierarchy table
     * 2. delete a record from gpo_preambles_switches_master_list
     * 3. a field with same name exists in gpo_datapages table. we need to drop it
     * 4. a field with same name followed by _p exists in gpo_datapage_preamble_values table. we need to drop it
     * @param <string> $column_name
     */
    protected function _edit_column_handle_delete($column_name) {
        /** How will it work?
         *
         * This delete task will
         *
         */
        if (FALSE == canDeleteColumn()) {
            $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=edit_columns', false);
            $this->setRedirect($responseLink, "Sorry, You don't have sufficient privilege to perform this operation.");
            $this->redirect();
            return false;
        }

        $model = & $this->getModel('Datapages');
        //delete record from gpo_datapage_hierarchy table
        $step1 = $model->deleteDPColumnInfo('gpo_datapage_hierarchy', $column_name);

        //delete record from gpo_preambles_switches_master_list
        $step2 = $model->deleteDPColumnInfo('gpo_preambles_switches_master_list', $column_name);

        //drop field from gpo_datapage_preamble_values table
        $step3 = $model->dropDPDropTableField('gpo_datapage_preamble_values', $column_name . '_p');
        $step31 = $model->dropDPDropTableField('gpo_datapage_preamble_values_es', $column_name . '_p');
        $step32 = $model->dropDPDropTableField('gpo_datapage_preamble_values_fr', $column_name . '_p');


        //drop field from gpo_datapages table
        $step4 = $model->dropDPDropTableField('gpo_datapages', $column_name);

        $this->setRedirect('?option=com_gpo&controller=datapages&task=edit_columns', "The Category '$column_name' has been deleted successfully");
        $this->redirect();
        exit();
    }

    function savePreambles() {
        $model = & $this->getModel('Datapages', '', array());
        if ($this->isAdministrator) {
            $result = $model->saveDPPreambles($_POST);
            if ($result) {
                $responseMsg = 'The Data Page has been Successfully Updated on ' . date('d/m/Y \a\t h:i:sA');
            } else {
                $db = JFactory::getDBO();
                $responseMsg = $db->getErrorMsg();
            }
            $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list', false);
            $this->setRedirect($responseLink, $responseMsg);
            $this->redirect();
        } else {
            die("Restricted Access, you don't have permission to edit this content");
        }
    }

    function applyPreambles() {
        $model = & $this->getModel('Datapages', '', array());
        if ($this->isAdministrator) {
            $result = $model->saveDPPreambles($_POST);
            if ($result) {
                $responseMsg = 'The Data Page has been Successfully Updated  on ' . date('d/m/Y \a\t h:i:sA');
            } else {
                $db = JFactory::getDBO();
                $responseMsg = $db->getErrorMsg();
            }
            $loc_id = ($_POST['passed_location_id']) ? $_POST['passed_location_id'] : $_POST['location_id'];
            $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=view_preambles&id='
                            . $loc_id . '&location=' . $_POST['location'] . '&lang=' . $this->currentLanguage, false);
            $this->setRedirect($responseLink, $responseMsg);
            $this->redirect();
        } else {
            die("Restricted Access, you don't have permission to edit this content");
        }
    }

    function publish() {
        $model = & $this->getModel('Datapages', '', array());
        $dp_id = Joomla\CMS\Factory::getApplication()->getInput()->get('dp_id', false);
        $location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('location_id', false);
        $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list', false);

        if (empty($dp_id) || empty($location_id)) {
            $responseMsg = "Sorry, EMPTY DP ID or Location ID. Please try again.";
            $msgType = 'error';
        } else {
            $result = $model->publishDP($dp_id, $location_id, 'publish');
            $data = $model->getLocationById($location_id);
            $responseMsg = ( $result ) ? "The DP <i>$data->name</i> has been successfully published" : "Sorry, an error has occured. Please try again.";
            $msgType = ( $result ) ? 'message' : 'error';
        }
        $this->setRedirect($responseLink, $responseMsg, $msgType);
    }

    function unpublish() {
        $model = & $this->getModel('Datapages');
        $dp_id = Joomla\CMS\Factory::getApplication()->getInput()->get('dp_id', false);
        $location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('location_id', false);
        $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list', false);

        if (empty($dp_id) || empty($location_id)) {
            $responseMsg = "Sorry, EMPTY DP ID or Location ID. Please try again.";
            $msgType = 'error';
        } else {
            $result = $model->publishDP($dp_id, $location_id, 'unpublish');
            $data = $model->getLocationById($location_id);
            $responseMsg = ( $result ) ? "The DP <i>$data->name</i> has been successfully unpublished" : "Sorry, an error has occured. Please try again.";
            $msgType = ( $result ) ? 'message' : 'error';
        }
        $this->setRedirect($responseLink, $responseMsg, $msgType);
    }

    function save() {
        $model = & $this->getModel('Datapages', '', array());

        if ($this->isAdministrator) {
            $result = $model->saveDP($_POST);
            if ($result) {
                $responseMsg = 'The Data Page has been Successfully Updated on ' . date('d/m/Y \a\t h:i:sA');
            } else {
                $db = JFactory::getDBO();
                $responseMsg = $db->getErrorMsg();
            }
            $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list', false);
            $this->setRedirect($responseLink, $responseMsg);
            $this->redirect();
        } else {
            die("Restricted Access, you don't have permission to edit this content");
        }
    }

    function apply() {
        $model = & $this->getModel('Datapages', '', array());

        if ($this->isAdministrator) {
            $result = $model->saveDP($_POST);
            if ($result) {
                $responseMsg = 'The Data Page has been Successfully Updated on ' . date('d/m/Y \a\t h:i:sA');
            } else {
                $db = JFactory::getDBO();
                $responseMsg = $db->getErrorMsg();
            }
            $loc_id = ($_POST['passed_location_id']) ? $_POST['passed_location_id'] : $_POST['location_id'];
            $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=view_dp&id='
                            . $loc_id . '&location=' . $_POST['location'] . '&lang=' . $this->currentLanguage, false);
            $this->setRedirect($responseLink, $responseMsg);
            $this->redirect();
        } else {
            die("Restricted Access, you don't have permission to edit this content");
        }
    }

    /* add new column */

    function add_new_column() {

        $model = & $this->getModel('Datapages');
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $submit = Joomla\CMS\Factory::getApplication()->getInput()->get('submit', false);
        $columnName = Joomla\CMS\Factory::getApplication()->getInput()->get('column_name', false);

        $h1Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h1column', false);
        $h2Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h2column', false);
        $h3Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h3column', false);
        $tableName = Joomla\CMS\Factory::getApplication()->getInput()->get('table', false);
        $beforeafter = Joomla\CMS\Factory::getApplication()->getInput()->get('insert_position', false);

        $SelectionValue = Joomla\CMS\Factory::getApplication()->getInput()->get('after', false);

        // $swapColumnId = !empty($h3Column) ? $h3Column : $h2Column;
        //$defaultValue = Joomla\CMS\Factory::getApplication()->getInput()->get( 'default_val', false );
        //$defaultPreamble = Joomla\CMS\Factory::getApplication()->getInput()->get( 'default_preamble', false );
        //$id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id', false );
        //$location = Joomla\CMS\Factory::getApplication()->getInput()->get( 'location', false );
        if ($submit && empty($columnName)) {
            $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=add_new_column', false);
            $this->setRedirect($responseLink, 'Sorry, You must give a category name');
            $this->redirect();
        } else if ($submit && empty($h1Column) && empty($h2Column) && empty($h3Column)) {
            $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=add_new_column', false);
            $this->setRedirect($responseLink, 'Please select a position where to insert the new category');
            $this->redirect();
        } else if ($submit && !empty($columnName) && (!empty($h1Column) || !empty($h2Column) || !empty($h3Column))) {

            if ($this->isAdministrator) {
                $result = $model->addNewColumn();
            } else {
                die("Restricted Access. You don't have permission to perform this task.");
            }
            //die();
            if (TRUE === $result) {
                $responseMsg = 'The New Category has been successfully added';
                $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=location_list', false);
            } else {
                $responseMsg = $result;
                $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=add_new_column', false);
            }

            if (empty($h3Column) && empty($h2Column) && !empty($h1Column)) {
                if (!$this->oUser->get('isRoot')) {
                    $responseMsg = 'Sorry! Only a Super Administrator can add a top level column';
                }
            }


            $this->setRedirect($responseLink, $responseMsg);
            $this->redirect();
        } else {
            $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
            $location = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
            $topLevelHeaders = getTopLevelHeaders();
            $allLocations = $model->getAllLocationData('name');
            $view = & $this->getView('Datapages', 'html');
           /* $view->assign('id', $id);
            $view->assign('location', $location);
            $view->assignRef('topLevelHeaders', $topLevelHeaders);
            $view->assignRef('allLocations', $allLocations);
            $view->assign('languages', $this->languages);
            $view->assignRef('currentLanguage', $this->currentLanguage);
            $view->assign('requestURI', $this->requestURI);
            $view->assign('currentURI', $this->currentURI);*/

            $view->id=$id;
            $view->location=$location;
            $view->topLevelHeaders=&$topLevelHeaders;
            $view->allLocations=&$allLocations;
            $view->languages=$this->languages;
            $view->currentLanguage=&$this->currentLanguage;
            $view->requestURI=$this->requestURI;
            $view->currentURI=$this->currentURI;

            $view->add_new_column();
        }
    }

    /*
     * add_new_column page ajax response 
     * 
     */

    function make_column_alias() {
        $columnTitle = Joomla\CMS\Factory::getApplication()->getInput()->get('column_title');
        $columnAlias = strtolower(str_replace(array('"', "'"), '', $columnTitle)); //strip quotes if any
        $columnAlias = field_title($columnAlias); // glue the words by underscore
        if (strlen($columnAlias) > 61) {
            $columnAlias = substr($columnAlias, 0, 61); // cut the name to get 61 chars
        }
        echo trim($columnAlias);
        exit();
    }

    /*
     *
     * preview DP 
     * 
     * 
     * */

    function preview_dp() {
        $model = & $this->getModel('Datapages');
        $location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
        $location_name = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false);
        $location = $model->getLocationById($location_id);

        $dp_data = $model->getDPByLocationId($location_id);
        $dp_metadata = $model->getDPMetaDataInfo();

        $view = & $this->getView('Datapages', 'html');
        /*$view->assign('dp_data', $dp_data);
        $view->assign('dp_metadata', $dp_metadata);
        $view->assign('location', $location);
        $view->assign('location_name', $location_name);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->dp_data=$dp_data;
        $view->dp_metadata=$dp_metadata;
        $view->location=$location;
        $view->location_name=$location_name;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

        $view->preview_dp();
    }

    /* Groups DP Tabular */

    function groups_dp_tabular() {
        $model = & $this->getModel('Datapages');
        $country = Joomla\CMS\Factory::getApplication()->getInput()->get('country', false, $_REQUEST);
        $region = Joomla\CMS\Factory::getApplication()->getInput()->get('region', false, $_REQUEST);
        $group = Joomla\CMS\Factory::getApplication()->getInput()->get('group', false, $_REQUEST);
        $isSubmitted = Joomla\CMS\Factory::getApplication()->getInput()->get('isSubmitted', false, $_REQUEST);

        $h1Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h1column', false, $_REQUEST);
        $h2Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h2column', false, $_REQUEST);
        $h3Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h3column', false, $_REQUEST);

        //get selected location
        if (!empty($country)) {
            $selectedLocationId = $country;
            $selectedLocationType = 'country';
        } else if (!empty($region)) {
            $selectedLocationId = $region;
            $selectedLocationType = 'region';
        } else if (!empty($group)) {
            $selectedLocationId = $group;
            $selectedLocationType = 'group';
        }

        //get selected column
        if (!empty($h3Column)) {
            $selectedColumn = $h3Column;
            $selectedColumnType = 'h3column';
        } else if (!empty($h2Column)) {
            $selectedColumn = $h2Column;
            $selectedColumnType = 'h2column';
        } else if (!empty($h1Column)) {
            $selectedColumn = $h1Column;
            $selectedColumnType = 'h1column';
        }
        $selectedColumn = getDPColumnInfoById($selectedColumn);
        $aggregationOptions = getRegionAggregationOptions();
        $verticalAxisLabel = getYChartLabelOptions();

        if (!empty($selectedLocationId) && !empty($selectedColumn)) {
            $view = & $this->getView('Datapages', 'html');
            $view->selectedLocationId=$selectedLocationId;
            $view->selectedLocationType=$selectedLocationType;
            $view->selectedColumn=$selectedColumn;
            $view->selectedColumnType=$selectedColumnType;
            $view->aggregationOptions=$aggregationOptions;
            $view->verticalAxisLabel=$verticalAxisLabel;

            $view->selectedLocationId=$selectedLocationId;
            $view->selectedLocationType=$selectedLocationType;
            $view->selectedColumn=$selectedColumn;
            $view->selectedColumnType=$selectedColumnType;
            $view->aggregationOptions=$aggregationOptions;
            $view->verticalAxisLabel=$verticalAxisLabel;

            $view->groups_dp_tabular_stats();
        } else {
            if ($isSubmitted) {
                $errMsg = empty($selectedLocationId) ? 'Please select a Group or Region' :
                          (empty($selectedColumn) ? 'Please select a category' : '' );
            }
            $topLevelHeaders = getTopLevelHeaders();
            $view = & $this->getView('Datapages', 'html');
            $view->topLevelHeaders=&$topLevelHeaders;
            $view->selectedColumnType=&$selectedColumnType;
            $view->errorMessage=&$errMsg;
            $view->groups_dp_tabular();
        }
    }

    /* DP Yearly Data Update Script */

    function dpdata_update_automation() {
        error_reporting(E_ERROR);
        ini_set('display_errors', 1);
        $model = $this->getModel('Datapages');
        $dataSourceName = Joomla\CMS\Factory::getApplication()->getInput()->get('data_source', false, $_REQUEST);
        $isSubmitted = Joomla\CMS\Factory::getApplication()->getInput()->get('isSubmitted', false, $_REQUEST);

        $h1Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h1column', false, $_REQUEST);
        $h2Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h2column', false, $_REQUEST);
        $h3Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h3column', false, $_REQUEST);

        $importType           = Joomla\CMS\Factory::getApplication()->getInput()->get('importType', false, $_REQUEST);
        $selectedLocation     = Joomla\CMS\Factory::getApplication()->getInput()->get('location', false, $_REQUEST);
        $importOnlyBlankYears = Joomla\CMS\Factory::getApplication()->getInput()->get('importOnlyBlankYears', false, $_REQUEST);
        
        //get selected column
        if (!empty($h3Column)) {
            $selectedColumn = $h3Column;
            $selectedColumnType = 'h3column';
        } else if (!empty($h2Column)) {
            $selectedColumn = $h2Column;
            $selectedColumnType = 'h2column';
        } else if (!empty($h1Column)) {
            $selectedColumn = $h1Column;
            $selectedColumnType = 'h1column';
        }

        if ( !empty($dataSourceName) && (!empty($selectedColumn) || !empty($selectedLocation)) ) {
            
            jimport('joomla.filesystem.file');
            jimport('joomla.filesystem.folder');
             
            //this is the name of the field in the html form, filedata is the default name for swfupload
            //so we will leave it as that
            $fieldName = 'sourceFile';
            $fileError = $_FILES[$fieldName]['error'];
            $fileSize  = $_FILES[$fieldName]['size'];
            if ($fileSize > 2000000) {
                echo JText::_('FILE BIGGER THAN 2MB');
            }
            
            //the name of the file in PHP's temp directory that we are going to move to our folder
            $fileTemp      = $_FILES[$fieldName]['tmp_name'];    
            $inputFileName = $_FILES[$fieldName]['name'];
            $inputFileExt  = JFile::getExt($inputFileName);
            
            $searchOptions = array('dataSourceName'  => $dataSourceName,
                                   'importType'      => $importType,
                                   'sourceFile'      => $inputFileName,
                                   'importOnlyBlankYears' => $importOnlyBlankYears
                             );
           
            /*
            $helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');
            $reader = IOFactory::createReader(ucfirst($inputFileExt));
            $spreadsheet = $reader->load($fileTemp);
            */
            
            $spreadsheet = IOFactory::load($fileTemp);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, false, true);
            $view = $this->getView('Datapages', 'html');
            $selectedColumnInfo  = getDPColumnInfoById($selectedColumn);
            //$newSourceData     = $model->getAllDPSourceDataByCategory($selectedColumnInfo->column_name, $dataSourceName);
            $newSourceData       = $model->getAllDPSourceDataByYear($sheetData);
            
            if( empty($newSourceData) ) {
                echo "Source Data table not found or empty! Nothing to update!";
                exit();
            }
            
            if( 'by_category' == $importType ) {
                $existingDPDataEn = $model->getDPDataByCategoryAndLang($selectedColumnInfo->column_name, 'en'); 
                $existingDPDataEs = $model->getDPDataByCategoryAndLang($selectedColumnInfo->column_name, 'es');
                $existingDPDataFr = $model->getDPDataByCategoryAndLang($selectedColumnInfo->column_name, 'fr');
                
                $searchOptions['selectedColumn']     = $selectedColumn;
                $searchOptions['selectedColumnType'] = $selectedColumnType;
            } else if ( 'by_location' == $importType ) {
                $existingDPDataEn = $model->getDPDataByLocationAndLang($selectedLocation, 'en');
                //$existingDPDataEs = $model->getDPDataByLocationAndLang($selectedLocation, 'es');
                //$existingDPDataFr = $model->getDPDataByLocationAndLang($selectedLocation, 'fr');
                
                $searchOptions['selectedLocation'] = $selectedLocation; 
            }

            $view->dataSourceName=$dataSourceName;
            $view->selectedColumnInfo=$selectedColumnInfo;
            $view->selectedColumnType=$selectedColumnType;
            $view->selectedLocation=$selectedLocation;
            $view->importType=$importType;
            
            $view->existingDPDataEn=$existingDPDataEn;
            $view->existingDPDataEs=$existingDPDataEs;
            $view->existingDPDataFr=$existingDPDataFr;
            $view->newSourceData=$newSourceData;
            $view->action=dpdata_update_frmsrc_write();
            $view->currentLanguage=$this->currentLanguage;

            
            $view->searchOptions=&$searchOptions;
            $view->importOnlyBlankYears=&$importOnlyBlankYears;
            
            $view->dpdata_update_frmsrc_result();
            
        } else {
            if ($isSubmitted) {
                $errMsg  = empty($dataSourceName)  ? 'Please select a data source <br>' : '';
                if( 'by_location' == $importType ) {
                    $errMsg .= empty($selectedLocation) ? 'Please select a location to proceed' : '';
                }
                if( 'by_category' == $importType ) {
                    $errMsg .= empty($selectedColumn) ? 'Please select a category to proceed' : '';
                }
            }
            
            $topLevelHeaders = getTopLevelHeaders();
            $allLocations    = $model->getAllLocationData('name');

            $view = & $this->getView('Datapages', 'html');
           /* $view->assignRef('topLevelHeaders', $topLevelHeaders);
            $view->assignRef('selectedColumnType', $selectedColumnType);
            $view->assignRef('errorMessage', $errMsg);
            $view->assignRef('allLocations', $allLocations);
            $view->assignRef('searchOptions', $searchOptions);
            $view->assignRef('importOnlyBlankYears', $importOnlyBlankYears);
            */
            $view->topLevelHeaders= &$topLevelHeaders;
            $view->selectedColumnType=&$selectedColumnType;
            $view->errorMessage=&$errMsg;
            $view->allLocations=&$allLocations;
            $view->searchOptions=&$searchOptions;
            $view->importOnlyBlankYears=&$importOnlyBlankYears;
            
            $view->dpdata_update_automation();
        }
    }
    
    function dpdata_update_frmsrc_result() {
        error_reporting(E_ERROR);
        ini_set('display_errors', 1);
        
        $model = $this->getModel('Datapages');
        $dataSourceName = Joomla\CMS\Factory::getApplication()->getInput()->get('data_source', false, $_REQUEST);
        $isSubmitted = Joomla\CMS\Factory::getApplication()->getInput()->get('isSubmitted', false, $_REQUEST);

        $h1Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h1column', false, $_REQUEST);
        $h2Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h2column', false, $_REQUEST);
        $h3Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h3column', false, $_REQUEST);
                
        $view = & $this->getView('Datapages', 'html');
        /*$view->assignRef('topLevelHeaders', $topLevelHeaders);
        $view->assignRef('selectedColumnType', $selectedColumnType);
        $view->assignRef('errorMessage', $errMsg);*/

        $view->topLevelHeaders=&$topLevelHeaders;
        $view->selectedColumnType=&$selectedColumnType;
        $view->errorMessage=&$errMsg;

        $view->dpdata_update_frmsrc_result();
    }
    
    function dpdata_update_frmsrc_write() {
        if ( empty($_POST['cid']) ) {
             return false;
        }

        $cids           = Joomla\CMS\Factory::getApplication()->getInput()->get('cid', false);
        $locationIDs    = Joomla\CMS\Factory::getApplication()->getInput()->get('location_id', false);
        $search_options = Joomla\CMS\Factory::getApplication()->getInput()->get('search_options', false);
        $search_options = unserialize(urldecode($search_options));

        $search_options['currentLang']        = $this->currentLanguage;
        $search_options['selectedColumn']     = Joomla\CMS\Factory::getApplication()->getInput()->get('selectedColumn', false);
        $search_options['selectedLocationID'] = Joomla\CMS\Factory::getApplication()->getInput()->get('selectedLocationID', false);
        $search_options['column_name']        = $search_options['selectedColumn'];

        $frtModel        = $this->getModel('updateDPFromSourceFile', '', $search_options);
        $DPModel         = $this->getModel('Datapages');
        $locModel        = $this->getModel('locations');
        
        $replaceResult   = $frtModel->frtUpdateRowsDatapage($search_options);
        
        /*
        $replaceCount    = $replaceResult['total'];
        $replacedLocIDs  = $replaceResult['updatedLocationIDs'];
        $replacedLangs   = $replaceResult['affectedLanguages'];
        $replacedCategories = $replaceResult['updatedCategories'];
        */
        //$allGpoLocations = $locModel->getAllLocationNames();
        //$updatedLocationNames = '';
        
        /*
        $counter = 1;
        foreach( $replacedLocIDs as $key => $val ) {
            $updatedLocationNames .= $allGpoLocations[$val]['name'] . ', ';
            $pURL = JURI::root() . 'preview_dp.php?location='.urlencode($allGpoLocations[$val]['name']).'&category=' . $search_options['selectedColumn'];
            $previewURLs[] = $pURL;
            $previewLinks .= $counter++ . '. <b>' . $allGpoLocations[$val]['name'] . 
                             '</b> <a target="_blank" href="' . $pURL . '">' . $pURL . '</a>' . '<br>';
        }
        
        if ($replaceCount) {
            $previewSampleURL     = JURI::root() . 'preview_dp.php?location=Afghanistan&category=' . $search_options['selectedColumn'];
            $previewSampleURLLink = '<a target="_blank" href="' . $previewSampleURL . '">' . $previewSampleURL . '</a>';
            
            $responseMsg = "Total <i>$replaceCount</i> rows successfully updated in the column: <i>"
                            . $search_options['selectedColumn'] . '</i>; Updated location IDs: <i>'
                            . implode(',',$replacedLocIDs) . ' - Location Names: ' . substr($updatedLocationNames, 0, -2) . '</i> <br>'
                            . ' Affected Languages:  <i>' . implode(', ', array_unique($replacedLangs)) . '</i> <br>';
            
            $search_options['affected_locations'] = substr($updatedLocationNames, 0, -2);
            if( $replaceResult['importType'] == 'by_category' ) {
                  $search_options['affected_columns']   = implode(', ', $search_options['selectedColumn']);
            } else {
                  $search_options['affected_columns']   = implode( ', ', array_unique($replacedCategories) );
                  $dpColumnsInfo = $DPModel->getDPColumnsInfo();
            }
            
            $frtModel->insertSearchHistory($search_options, $replaceCount);
        } else {
            
        }
        */
        $db = JFactory::getDBO();
        $errorMsg = $db->getErrorMsg();
        
        $view = $this->getView('Datapages', 'html');
        
       /* $view->assignRef('replaceResult', $replaceResult);
        $view->assignRef('search_options', $search_options);
        $view->assignRef('errorMsg', $errorMsg);
        $view->assignRef('locModel', $locModel);
        $view->assignRef('DPModel', $DPModel);
        $view->assignRef('frtModel', $frtModel);*/

        $view->replaceResult=&$replaceResult;
        $view->search_options=&$search_options;
        $view->errorMsg=&$errorMsg;
        $view->locModel=&$locModel;
        $view->DPModel=&$DPModel;
        $view->frtModel=&$frtModel;
        
        $view->dpdata_update_frmsrc_write();
    }
    
    /*
     * Shows the past history DP Excel File Import
     */
    function dataimport_history() {
        $task   = Joomla\CMS\Factory::getApplication()->getInput()->get('task', false);

        $modelOptions  = array('currentLang' => $this->currentLanguage);
        $dpImportModel = $this->getModel('updateDPFromSourceFile', array(), $modelOptions);
        $items = $dpImportModel->frtGetHistory();
        $columnTitles = getDPColumnTitles();

        $view = & $this->getView('Datapages', 'html');
        /*$view->assign('action', $action);
        $view->assign('task', $task);
        $view->assignRef('items', $items);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);
        $view->assignRef('columnTitles', $columnTitles);
        $view->assignRef( 'pagination', $dpImportModel->pagination );*/

        $view->action=$action;
        $view->task=$task;
        $view->items=&$items;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;
        $view->columnTitles=&$columnTitles;
        $view->pagination=&$dpImportModel->pagination;

        $view->dataimport_history();
    }
    
    /** 
     ** Get details of DP Data Import
     ** History
     */
    function dataimport_getdetails() {
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '0');

        $modelOptions = array('currentLang' => $this->currentLanguage);
        $dpImportModel = $this->getModel('updateDPFromSourceFile', array(), $modelOptions);
        $items = $dpImportModel->frtGetRawUpdateHistory($id);
        $columnTitles = getDPColumnTitles();

        $view = & $this->getView('Datapages', 'html');
      /*  $view->assignRef('items', $items);
        $view->assignRef('id', $id);
        $view->assignRef('columnTitles', $columnTitles);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->items=&$items;
        $view->id=&$id;
        $view->columnTitles=&$columnTitles;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

        $view = & $this->getView('Datapages', 'html');
        $view->dataimport_getdetails();
    }

    /* LCPGV Updates */
    function lcpgv_updates() {

        $model = & $this->getModel('Datapages');
        $jinput = JFactory::getApplication()->input;
        $USJurisdictions = $model->getUSJurisdictionNames();
        $country = Joomla\CMS\Factory::getApplication()->getInput()->get('country', false, $_REQUEST);
        $region = Joomla\CMS\Factory::getApplication()->getInput()->get('region', false, $_REQUEST);
        $websource = Joomla\CMS\Factory::getApplication()->getInput()->get('websource', false, $_REQUEST);
        $isSubmitted = Joomla\CMS\Factory::getApplication()->getInput()->get('isSubmitted', false, $_REQUEST);
        $h1Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h1column', false, $_REQUEST);
        $h2Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h2column', false, $_REQUEST);
        $h3Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h3column', false, $_REQUEST);
        $showall = "";
        if ($_POST['id']) {
            $message = "";
            $ret = updatelastchecked($_POST['id']);

            if ($ret) {
                $message = "<span style='color:blue'>Last checked date successfully updated as of today's date</span>";
            } else {
                $message = "<span style='color:red'>Last checked date is not successfully updated as of today's date</span>";
            }
        }

        if ($_POST['crawlsource']) {
            $message = "";
            $datelastmodified = getLastModified($_POST['crawlsource']);
            $ret = updategpotable($_POST['crawlsource'], $datelastmodified);

            if ($ret) {
                $message = "<span style='color:blue'>The websource is successfully crawled</span>";
            } else {
                $message = "<span style='color:red'>The websource is not crawled successfully</span>";
            }
        }

        //get selected location
        if (!empty($country)) {
            $selectedLocationName = $country;
            $selectedLocationType = 'country';
        } else if (!empty($region)) {
            $selectedLocationName = $region;
            $selectedLocationType = 'region';
        } else if (!empty($websource)) {
            $selectedwebsource = $websource;
            $selectedLocationType = 'websource';
        }
        //get selected column
        if (!empty($h3Column)) {
            $selectedColumn = $h3Column;
            $selectedColumnType = 'h3column';
        } else if (!empty($h2Column)) {
            $selectedColumn = $h2Column;
            $selectedColumnType = 'h2column';
        } else if (!empty($h1Column)) {
            $selectedColumn = $h1Column;
            $selectedColumnType = 'h1column';
        }

        if ((!empty($selectedLocationName) && !empty($websource)) || $jinput->get('ispost') === '1') {

            $selectedrows = SelectfromgpoWebsource($jinput->get('filter_order', 'lastmodified', 'string'), $jinput->get('filter_order_Dir', 'DESC', 'string'), $selectedLocationName, $websource);
        }

        if ($jinput->get('update') === '1') {

            $selectedColumn = getAllWebsourcefromCitations();
            //DeleteTableWebsource();
            ini_set("max_execution_time", 0);
            ini_set("memory_limit", "512M");

            foreach ($selectedColumn as $key => $val) {
                $datemodified[$val->websource] = getLastModified($val->websource);
                if (!is_string($datemodified[$val->websource])) {
                    $datemodified[$val->websource] = $datemodified[$val->websource];
                }

                // $datemodified[$selectedColumn[10]->websource] = getLastModified($selectedColumn[10]->websource);
                $rows = array(
                            'lastmodified' => NULL,
                            'websource' => NULL,
                            'qcitesid' => NULL,
                            'lastcrawled' => NULL
                );

                $dt = new DateTime();
                $date = $dt->format('Y-m-d H:i:s');
                $rows['lastmodified'] = $datemodified[$val->websource];
                $rows['websource'] = $val->websource;
                $rows['qcitesid'] = $val->id;
                $rows['lastcrawled'] = date("Y/m/d");
                InsertTogpoWebsource($rows);
            }
            echo "<h4>Crawling is done</h4>";
        }

        $aggregationOptions = getRegionAggregationOptions();
        $verticalAxisLabel = getYChartLabelOptions();

        //if (!empty($selectedLocationId) & !empty($selectedColumn))
        if (!empty($selectedLocationName) || $jinput->get('ispost') === '1' || $jinput->get('update') === '1') {
            if ($selectedLocationName === 'showall') {
                $showall = 'Show All';
            }

            $view = & $this->getView('Datapages', 'html');
            /*$view->assign('selectedLocationType', $selectedLocationType);
            $view->assignRef('websource', $websource);
            $view->assignRef('SelectedLocationName', $selectedLocationName);
            $view->assign('aggregationOptions', $aggregationOptions);
            $view->assign('verticalAxisLabel', $verticalAxisLabel);
            $view->assignRef('message', $message);

            $view->assignRef('showall', $showall);
            $view->assignRef('selectedrows', $selectedrows);
            $view->assignRef('filter_order', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'lastmodified'));
            $view->assignRef('filter_order_Dir', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'DESC'));*/

            $view->selectedLocationType= $selectedLocationType;
            $view->websource=&$websource;
            $view->SelectedLocationName=&$selectedLocationName;
            $view->aggregationOptions=$aggregationOptions;
            $view->verticalAxisLabel=$verticalAxisLabel;
            $view->message=&$message;

            $view->showall=&$showall;
            $view->selectedrows=&$selectedrows;
            $view->filter_order=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'lastmodified');
            $view->filter_order_Dir=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'DESC');

            $view->lcpgv_updates_stats();
        } else {
            if ($isSubmitted) {
                $errMsg = empty($selectedLocationName) ? 'Please select a Group or Region' : (empty($selectedColumn) ? 'Please select a category' : '' );
            }
            $topLevelHeaders = getTopLevelHeaders();
            $view = & $this->getView('Datapages', 'html');
            /*$view->assignRef('topLevelHeaders', $topLevelHeaders);
            $view->assignRef('selectedColumnType', $selectedColumnType);
            $view->assignRef('errorMessage', $errMsg);
            $view->assign('USJurisdictions', $USJurisdictions);*/

            $view->topLevelHeaders=&$topLevelHeaders;
            $view->selectedColumnType=&$selectedColumnType;
            $view->errorMessage=&$errMsg;
            $view->USJurisdictions=$USJurisdictions;

            $view->lcpgv_updates();
        }
    }

    /*
     * 
     * this portion will be used to clean data 
     * restricted use only
     * 
     * 
     */

    function clean_data() {
        $model = & $this->getModel('Datapages');
        $location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('location_id', false);
        $type = Joomla\CMS\Factory::getApplication()->getInput()->get('type', false);
        $location = $model->getLocationById($location_id);

        if ('run' == $type || 'dry_run' == $type) {
            $clean_result = $model->cleanDPData($location_id, $type);
        }

        $view = & $this->getView('Datapages', 'html');
        /*$view->assign('dp_data', $dp_data);
        $view->assign('location', $location);
        $view->assign('type', $type);
        $view->assignRef('clean_result', $clean_result);*/

        $view->dp_data=$dp_data;
        $view->location=$location;
        $view->type=$type;
        $view->clean_result=&$clean_result;

        $view->clean_dp_data();
    }

    function check_missing_cite() {
        $model = & $this->getModel('Datapages');
        $location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('location_id', false);
        $location = $model->getLocationById($location_id);
        $result = $model->checkDPForMissingSources($location_id);
        $view = & $this->getView('Datapages', 'html');
        /*$view->assign('dp_data', $dp_data);
        $view->assign('location', $location);
        $view->assignRef('result', $result);*/

        $view->dp_data=$dp_data;
        $view->location=$location;
        $view->result=&$result;

        $view->missing_cite_data();
    }

    /*
     * 
     * FIND & REPLACE GUI INTERFACE 
     * FOR DP AND DP PREAMBLES
     * 
     */

    function frt() {
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $allowedList = array('add', 'getcol');
        $methodName = 'frt_' . $action . '()';
        ini_set('memory_limit', -1);
        switch ($action) {
            case 'add':
                $this->frt_add();
                break;
            case 'getcol':
                $this->frt_getcol();
                break;
            case 'replace':
                $this->frt_replace();
                break;
            case 'history':
                $this->frt_history();
                break;
            default:
                $this->frt_add();
                break;
        }
    }

    /*
     * 
     * SHOW FIND & REPLACE 
     * SEARCH FORM
     * 
     */

    function frt_add() {

        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $task = 'frt';
        $USJurisdictionIDs = NULL;

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' && $_POST['action'] === 'add') {
            $h1Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h1column', false);
            $h2Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h2column', false);
            $h3Column = Joomla\CMS\Factory::getApplication()->getInput()->get('h3column', false);
            $tableName = Joomla\CMS\Factory::getApplication()->getInput()->get('table', false);
            $swapColumnId = !empty($h3Column) ? $h3Column : $h2Column;

            $model = & $this->getModel('Datapages', '', array());
            $columnInfo = $model->getDPColumnsInfo();

            /* This code for location subheader */
            if ($h1Column == 'location_subheader') {
                $_POST['swap']['column_name'] = $h1Column;
            } else {
                $_POST['swap']['column_name'] = $model->getColumnNameByHierarchyId($swapColumnId);
            }
            $_POST['swap']['created_at'] = date('Y-m-d H:i:s');
            $_POST['swap']['updated_at'] = date('Y-m-d H:i:s');

            $modelOptions = $_POST['swap'];
            $modelOptions['currentLang'] = $this->currentLanguage;

            //search only in US Jurisdictions
            if (isset($_POST['swap']['us_jurisdictions'])) {
                $USJurisdictionIDs = implode(',', $model->getAllUSJurisdictions());
            }

            $frtModel = & $this->getModel('Findreplace', '', $modelOptions);

            //For other languages
            $modelOptionsES = $modelOptionsFR = $modelOptions;
            $modelOptionsES['currentLang'] = 'es';
            $modelOptionsFR['currentLang'] = 'fr';

            // data correction functionality ----------------------
            if (isset($_POST['swap']['correction'])) {

                if (isset($_POST['swap']['correction']['manual'])) {
                    $searchResult = $frtModel->frtPerformCorrectionManualSearch();
                    $replacedResult = $frtModel->frtSearchCorrectionManualReplace($searchResult, $_POST['swap']);
                } else if (isset($_POST['swap']['correction']['auto'])) {

                    $app = JFactory::getApplication();
                    $app->redirect('index.php?option=com_gpo&controller=datapages&task=clean_data&type=dry_run');
                }
            } else {

                $searchResult = $frtModel->frtPerformSearch($USJurisdictionIDs);
                $replacedResult = $frtModel->frtSearchReplace($searchResult, $_POST['swap']);

                /*
                  if('en' == $this->currentLanguage) {
                  $frtModelES =& $this->getModel( 'Findreplace', '', $modelOptionsES );
                  $frtModelFR =& $this->getModel( 'Findreplace', '', $modelOptionsFR );

                  $searchResultES   = $frtModelES->frtPerformSearch($USJurisdictionIDs);
                  $replacedResultES = $frtModelES->frtSearchReplace($searchResultES, $_POST['swap']);

                  $searchResultFR   = $frtModelFR->frtPerformSearch($USJurisdictionIDs);
                  $replacedResultFR = $frtModelFR->frtSearchReplace($searchResultFR, $_POST['swap']);

                  }
                 */
            }

            /* view results */
            $view = & $this->getView('Datapages', 'html');
           /* $view->assignRef('total', $frtModel->total);
            $view->assignRef('options', $_POST['swap']);
            $view->assignRef('items', $searchResult);
            $view->assignRef('replacedItems', $replacedResult);
            $view->assignRef('USJurisdictionIDs', $USJurisdictionIDs);

            $view->assignRef('itemsES', $searchResultES);
            $view->assignRef('replacedItemsES', $replacedResultES);

            $view->assignRef('itemsFR', $searchResultFR);
            $view->assignRef('replacedItemsFR', $replacedResultFR);

            $view->assign('action', $action);
            $view->assign('task', $task);
            $view->assign('currentLanguage', $this->currentLanguage);
            $view->assign('requestURI', $this->requestURI);
            $view->assign('currentURI', $this->currentURI . '&controller=datapages&task=frt&action=add');
            $view->assign('columnInfo', $columnInfo);*/

            $view->total=&$frtModel->total;
            $view->options=&$_POST['swap'];
            $view->items=&$searchResult;
            $view->replacedItems=&$replacedResult;
            $view->USJurisdictionIDs=&$USJurisdictionIDs;

            $view->itemsES=&$searchResultES;
            $view->replacedItemsES=&$replacedResultES;

            $view->itemsFR=&$searchResultFR;
            $view->replacedItemsFR=&$replacedResultFR;

            $view->action=$action;
            $view->task=$task;
            $view->currentLanguage=$this->currentLanguage;
            $view->requestURI=$this->requestURI;
            $view->currentURI=$this->currentURI . '&controller=datapages&task=frt&action=add';
            $view->columnInfo=$columnInfo;

            $view->frt_results();
            return true;
        }

        $action = empty($action) ? 'add' : $action; //by default it will show search form & will search

        $topLevelHeaders = getTopLevelHeaders();
        $from = Joomla\CMS\Factory::getApplication()->getInput()->get('from');
        $view = & $this->getView('Datapages', 'html');
    /*  $view->assign('action', $action);
        $view->assign('task', $task);
        $view->assignRef('topLevelHeaders', $topLevelHeaders);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);
        $view->assign('action', $action);
        $view->assign('task', $task);
        $view->assignRef('topLevelHeaders', $topLevelHeaders);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->action=$action;
        $view->task=$task;
        $view->topLevelHeaders=&$topLevelHeaders;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;
        $view->action=$action;
        $view->task=$task;
        $view->topLevelHeaders=&$topLevelHeaders;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

        $view->frt_add();
    }

    /*
     * 
     * The replace portion of the search tool
     * updates the table column according to 
     * the posted replaced values.
     * 
     */

    function frt_replace() {
        if (empty($_POST['cid'])) {
            return false;
        }

        $cids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid', false);
        $search_options = Joomla\CMS\Factory::getApplication()->getInput()->get('search_options', false);
        $search_options = unserialize(urldecode($search_options));
        $search_options['currentLang'] = $this->currentLanguage;

        $frtModel = & $this->getModel('Findreplace', '', $search_options);
        $replaceCount = $frtModel->frtUpdateRowsDatapage($search_options);

        if ($replaceCount) {
            $responseMsg = "Total <i>$replaceCount</i> rows successfully updated in the column: <i>"
                    . $search_options['column_name'] . '</i>; table: <i>'
                    . $search_options['table_name'] . '</i>';
            $frtModel->frtInsertSearchHistory($search_options, $replaceCount);
        } else {
            $db = JFactory::getDBO();
            $responseMsg = $db->getErrorMsg();
        }

        $responseLink = JRoute::_('index.php?option=com_gpo&controller=datapages&task=frt&action=add', false);
        $this->setRedirect($responseLink, $responseMsg);
        $this->redirect();

        return false;
    }

    /*
     * Shows the past history of find & replace
     */

    function frt_history() {
        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', false);
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);

        $modelOptions = array('currentLang' => $this->currentLanguage);
        $frtModel = & $this->getModel('Findreplace', array(), $modelOptions);
        $items = $frtModel->frtGetHistory();
        $columnTitles = getDPColumnTitles();

        $view = & $this->getView('Datapages', 'html');
    /*  $view->assign('action', $action);
        $view->assign('task', $task);
        $view->assignRef('items', $items);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);
        $view->assignRef('columnTitles', $columnTitles);*/
        
        $view->action=$action;
        $view->task=$task;
        $view->items=&$items;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;
        $view->columnTitles=&$columnTitles;

        $view->frt_history();
    }

    /*
     * 
     * the ajax response for getting the child nodes 
     * of a top level data column 
     * 
     */

    function frt_getcol() {
        $pid = Joomla\CMS\Factory::getApplication()->getInput()->get('pid');
        $level = Joomla\CMS\Factory::getApplication()->getInput()->get('level', '');
        $html = '<option value="0">No Category found</option>';
        if (empty($pid)) {
            echo $html;
            exit();
        }
        $data = getColumnsByParentId($pid);
        $columnTitles = getDPColumnTitles();

        if ($data) {
            $html = "<option value='0'>-- Select level-$level Category --</option>";
            $data = getColumnsByParentId($pid);

            foreach ($data as $key => $val) {
                $camelizedVal = $columnTitles[$val->column_name]; //camelize($val->column_name);
                $html .= '<option title="' . $camelizedVal . '" value="' . $val->id . '">' . $camelizedVal . '</option>';
            }
        }

        echo $html;
        exit();
    }

    function parent_getcol() {
        $type = Joomla\CMS\Factory::getApplication()->getInput()->get('typ', '0');
        $html = '<option value="0">No Category found</option>';

        ##We need to show all columns irrespective of the type of the columns
        $data = getTopLevelHeaders();
        $columnTitles = getDPColumnTitles();

        if ($data) {
            $html = "<option value='0'>-- Select top header --</option>";
            foreach ($data as $key => $val) {
                $html .= '<option title="' . $columnTitles[$val] . '" value="' . $key . '">' . $columnTitles[$val] . '</option>';
            }
        }

        echo $html;
        exit();
    }

    function propagate_masterlist() {
        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', false);
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $model = & $this->getModel('Datapages', '', array());
        $redirectTask = 'propagate_masterlist';

        if ('update_preambles' == $action) {
            if ('en' == $this->currentLanguage) {
                $responseMsg = 'Sorry! English DPs is not supported for this feature';
            } else if (!$this->isRoot) {
                $responseMsg = 'Sorry! Only a Super Administrator can run this command';
            } else {
                $retVal = $model->propagatePreamblesFromMasterList($this->currentLanguage);
                $responseMsg = ($retVal) ? 'Preambles Master List has been propagated successfully' : 'Sorry an error has occurred, please try again';
            }

            $responseLink = JRoute::_("index.php?option=com_gpo&controller=datapages&task=$redirectTask"
                            . '&lang=' . $this->currentLanguage, false);
            $this->setRedirect($responseLink, $responseMsg);
            $this->redirect();
        } else {
            $preambles_master_list = $model->getPreamblesMasterList();
            $isRoot = $this->oUser->get('isRoot');

            $view = & $this->getView('Datapages', 'html');
           /* $view->assignRef('preambles_master_list', $preambles_master_list);
            $view->assignRef('currentLanguage', $this->currentLanguage);
            $view->assign('requestURI', $this->requestURI);
            $view->assign('currentURI', $this->currentURI);
            $view->assign('isRoot', $isRoot);

            $view->assignRef('preambles_master_list', $preambles_master_list);
            $view->assignRef('currentLanguage', $this->currentLanguage);
            $view->assign('requestURI', $this->requestURI);
            $view->assign('currentURI', $this->currentURI);
            $view->assign('isRoot', $isRoot);*/

            $view->preambles_master_list=&$preambles_master_list;
            $view->currentLanguage=&$this->currentLanguage;
            $view->requestURI=$this->requestURI;
            $view->currentURI=$this->currentURI;
            $view->isRoot=$isRoot;

            $view->preambles_master_list=&$preambles_master_list;
            $view->currentLanguage=&$this->currentLanguage;
            $view->requestURI=$this->requestURI;
            $view->currentURI=$this->currentURI;
            $view->isRoot=$isRoot;

            $view->propagate_masterlist();
        }
    }

    function get_frtdetails() {
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '0');

        $modelOptions = array('currentLang' => $this->currentLanguage);
        $frtModel = & $this->getModel('Findreplace', array(), $modelOptions);
        $items = $frtModel->frtGetRawUpdateHistory($id);
        $columnTitles = getDPColumnTitles();

        $view = & $this->getView('Datapages', 'html');
       /* $view->assignRef('items', $items);
        $view->assignRef('id', $id);
        $view->assignRef('columnTitles', $columnTitles);
        $view->assign('currentLanguage', $this->currentLanguage);
        $view->assign('requestURI', $this->requestURI);
        $view->assign('currentURI', $this->currentURI);*/

        $view->items= $items;
        $view->id=$id;
        $view->columnTitles=$columnTitles;
        $view->currentLanguage=$this->currentLanguage;
        $view->requestURI=$this->requestURI;
        $view->currentURI=$this->currentURI;

        $view = & $this->getView('Datapages', 'html');
        $view->frt_getdetails();
    }

    /*
     * Update UCDP old country profiles 
     * with the new URLs after the upgrade
     * of their site
     */

    function updateUCDPSwitchForAll() {
        /*
         * SELECT id, location_id, location, `conflict_profile`  FROM `j25_gpo_datapages` WHERE `conflict_profile` like '%gpdatabase%' or conflict_profile = ''
         */
        $ucdpCountryProfileBaseURL = 'http://ucdp.uu.se/#country/';
        $conflictProfileColumnName = 'conflict_profile';
        $i = 0;
        $k = 0;
        $updatedLocs = array();

        $dpModel = $this->getModel('Datapages', '', array());
        $model = $this->getModel('Locations');
        $locations = $model->getAllLocationArray();
        $isoArray = array_flip($locations[1]);
        $locationsArray = array_flip($locations[0]);
        $locationsArray2 = $locations[0];
        $isoArray2 = $locations[1];
        $gwardCodes = $model->getGleditschWardCountryCodes();
        $gwardCodesISO = $model->gleditschWardCountryCodesISO3();
        $originalGPOIds = array_keys($locationsArray2);
        //loop through the ISO names 
        foreach ($gwardCodesISO as $iso3 => $id) {
            $countryCodeToUse = $id;
            $gpoCountryCode = $isoArray[$iso3];
            $countryProfileURL = $ucdpCountryProfileBaseURL . $countryCodeToUse;
            if ($gpoCountryCode) {
                $dpModel->_updateColumnData($conflictProfileColumnName, $countryProfileURL, $gpoCountryCode, 'all');
                $i++;
                $updatedLocs[] = $gpoCountryCode;
            } else {
                echo "Not Updated: $iso3 => $id => <b> $gpoCountryCode  - " . $isoArray2[$gpoCountryCode] . "</b> => $countryProfileURL <br>";
            }
        }
        echo "Total ISO3 profile updated: $i <br>";

        //loop through the country names 
        foreach ($gwardCodes as $key => $val) {
            $countryCodeToUse = $key;
            $gpoCountryCode = $locationsArray[$val];
            $countryProfileURL = $ucdpCountryProfileBaseURL . $countryCodeToUse;
            if ($gpoCountryCode) {
                $dpModel->_updateColumnData($conflictProfileColumnName, $countryProfileURL, $gpoCountryCode, 'all');
                $k++;
                $updatedLocs[] = $gpoCountryCode;
            } else {
                echo "Not Updated: $key => $val => <b> $gpoCountryCode  - " . $locationsArray2[$gpoCountryCode] . "</b> => $countryProfileURL <br>";
            }
        }

        echo "Total country name profile updated: $k <br>";

        $notUpdatedList = array_diff($originalGPOIds, $updatedLocs);
        //var_dump($notUpdatedList);
    }

}

?>