<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
//ini_set('display_errors',true);
//error_reporting(E_ALL);
class GpoControllerGlossary extends GpoController
{
    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit');
        $this->oUser = & JFactory::getUser();

        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', '');
        if (empty($task)) {
            $this->setRedirect('index.php?option=com_gpo&controller=glossary&task=published');
            $this->redirect();
        }

    }

    function lookup()
    {
        //global $mainframe;
        $mainframe =& JFactory::getApplication();
        //ini_set('display_errors',true);
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $published = Joomla\CMS\Factory::getApplication()->getInput()->get('published', 1);
        $model =&$this->getModel('Glossary');

        //check if next/prev button is pressed or not!
        $lookupdirection = Joomla\CMS\Factory::getApplication()->getInput()->get('lookupdirection');
        if ($lookupdirection == 'next') {
            $id = $model->getNextById($id, $published);
        }
        if ($lookupdirection == 'prev') {
            $id = $model->getPrevById($id, $published);
        }
        if(!$model->isGlossaryExists($id)){
            $mainframe->redirect('index.php?option=com_gpo&controller=glossary&task=published','The glossary does not exist!');
            return;
        }
        if($id){
            $mainframe->redirect('index.php?option=com_gpo&controller=glossary&task=edit&id='.$id);
            return;
        }

    }


    function published()
    {
        $model_name = 'Glossary';
        $model = &$this->getModel($model_name);
        $view = &$this->getView($model_name, 'html');
        
        $view->filter_order = $filter_order = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
        $view->filter_order_Dir = $filter_order_dir = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'DESC');
        $glossaries = $model->getGlossaries(1, $filter_order, $filter_order_dir);
        //var_dump($glossaries);
        //$view->assignRef('glossaries', $glossaries);
        $view->glossaries=&$glossaries;

        $pagination =& $model->getPagination(1); //get the pagination object
        //$view->assignRef('pagination', $pagination);
        $view->pagination=&$pagination;

        $view->published();
    }

    function edit()
    {
        $model_name = 'Glossary';
        $model = &$this->getModel($model_name);

        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '');
        $glossary = $model->getGlossary($id);

        $view = &$this->getView($model_name, 'html');
        if (false !== $glossary) {
           // $view->assign('isNew', false);
           $view->isNew = false;
           // $view->assignRef('glossary', $glossary);
           $view->glossary=&$glossary;
        }


        $view->edit();
    }

    function create()
    {
        $model_name = 'Glossary';
        $view = &$this->getView($model_name, 'html');
        //$view->assign('isNew', true);
        $view->isNew = true;
        $view->edit();
    }

    function save()
    {
        $model_name = 'Glossary';
        $model = &$this->getModel($model_name);
        $glossary = Joomla\CMS\Factory::getApplication()->getInput()->get('glossary', NULL, 'STRING');
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $isNew = (empty($id) ? true : false);
        //now all glossary are published as we do not maintain unpublished glossary for time beign
        $glossary['published'] = 1;
        $result = $model->saveGlossary($glossary, $id);

        if (false !== $result) {
            $msg = (($isNew) ? 'Glossary added successfully' : 'Glossary modified successfully');

        } else {
            $msg = (($isNew) ? 'Glossary could not be added' : 'Glossary could not be updated');
            if (!empty($model->db_error_msg)) {
                $msg .= '. DB ERROR: ' . $model->db_error_msg;
            }
        }
        $this->setRedirect('index.php?option=com_gpo&controller=glossary&task=published', $msg);
        $this->redirect();

    }

    function delete()
    {
        $model_name = 'Glossary';
        $model = &$this->getModel($model_name);
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '');
        echo $id;
        if (empty($id)) {
            $msg = 'No glossary found to delete';
        } else {
            if ($model->deleteGlossary($id)) {
                $msg = 'The glossary item is deleted successfully!';
            } else {
                $msg = 'The glossary item is could not be deleted!';
            }
        }
        $this->setRedirect('index.php?option=com_gpo&controller=glossary&task=published', $msg);
        $this->redirect();
    }
    
        
    /*
      *
      * FIND & REPLACE TOOL RELATED FUNCTIONS
      * @date 2011-03-21 & 2011-08-27
      * @author murshed <khan.morshed@gmail.com>
      *
      */
    function frt()
    {
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $allowedList = array('add', 'getcol');

        //7 and 8 is administrator and super administrator
        $groupsUserIsIn  = JAccess::getGroupsByUser($this->oUser->id);
        $isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        
        if ($isAdministrator !== true) {
            $msg = 'At present, your access level does not allow you to access the FRT tool.';
            $link = JRoute::_('index.php?option=com_gpo&controller=glossary', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $methodName = 'frt_' . $action . '()';
        switch ($action) {
            case 'add':
                $this->frt_add();
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
      * SEARCH FORM for quotes table
      *
      */

    function frt_add()
    {

        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $controller = Joomla\CMS\Factory::getApplication()->getInput()->get('controller', 'glossary');
        $task = 'frt';
        $tableName = 'gpo_datapage_glossary'; //seach in this table
        
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'
            && $_POST['action'] === 'add'
        ) {
            //$tableName = Joomla\CMS\Factory::getApplication()->getInput()->get('table', false);
            $tableName = 'gpo_datapage_glossary'; //seach in the glossary table
            $_POST['swap']['column_name'] = trim(Joomla\CMS\Factory::getApplication()->getInput()->get('column_name', false));
            $_POST['swap']['table_name'] = $tableName;
            $_POST['swap']['created_at'] = date('Y-m-d H:i:s');
            $_POST['swap']['updated_at'] = date('Y-m-d H:i:s');

            //needed this for paginated pages
            if (empty($_POST['swap']['column_name'])) {
                $search_options = Joomla\CMS\Factory::getApplication()->getInput()->get('search_options', false);
                $search_options = unserialize(urldecode($search_options));
                $_POST['swap']['column_name'] = $search_options['column_name'];
                $_POST['swap']['from'] = $search_options['from'];
                $_POST['swap']['to'] = $search_options['to'];
                $_POST['swap']['case_sensitive'] = $search_options['case_sensitive'];
                //var_dump($_POST['swap']);
            }

            //check if column name is empty
            if (empty($_POST['swap']['column_name'])) {
                $msg = 'Sorry, you must select one value from the "Find in Field" drop down box.';
                $link = JRoute::_('index.php?option=com_gpo&controller='.$controller.'&task=frt', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
            }

            $frtModel =& $this->getModel('Findreplacecommon', '', $_POST['swap']);
            //$quotesModel =& $this->getModel('Quotes');
            $searchResult = $frtModel->frtPerformSearch();
            $replacedResult = $frtModel->frtSearchReplace($searchResult, $_POST['swap']);
            //$searchResultQcites = $frtModel->frtQcitesFinding();
            $frtModel->frtInsertLastSearch($_POST['swap'], $this->oUser->username);

            /* view results */
            $view =& $this->getView($controller, 'html');
           /* $view->assignRef('total', $frtModel->total);
            $view->assignRef('options', $_POST['swap']);
            $view->assignRef('items', $searchResult);*/
            
            $view->total=&$frtModel->total;
            $view->options=&$_POST['swap'];
            $view->items=&$searchResult;
            
            //$view->assignRef('qcites_items', $searchResultQcites);
            //$view->assignRef('replacedItems', $replacedResult);
            $view->replacedItems=&$replacedResult;

            //pagination
         /*   $view->assignRef('pagination', $frtModel->pagination);
            $view->assignRef('rows', count($searchResult));
            $view->assignRef('filter_order', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id'));
            $view->assignRef('filter_order_dir', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc'));*/
            
            $view->pagination=&$frtModel->pagination;
            $view->rows=&count($searchResult);
            $view->filter_order=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
            $view->filter_order_dir=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc');
            
            //$view->assignRef('total', $frtModel->total);
            //$view->assignRef('quotesModel', $quotesModel);

            /*$view->assign('action', $action);
            $view->assign('task', $task);
            $view->assign('controller', $controller);*/
            
            $view->action=$action;
            $view->task=$task;
            $view->controller=$controller;

            $view->frt_results();
            return true;
        }

        $action = empty($action) ? 'add' : $action; //by default it will show search form & will search

        $options['swap']['table_name'] = $tableName;
        $frtModel =& $this->getModel('Findreplacecommon', '', $options['swap']);
        $lastSearchedQuery = $frtModel->frtGetLastSearchedQuery($tableName);

        $from = Joomla\CMS\Factory::getApplication()->getInput()->get('from');
        $view =& $this->getView($controller, 'html');
     /*   $view->assign('action', $action);
        $view->assign('task', $task);
        $view->assign('controller', $controller);
        $view->assignRef('lastSearchedQuery', $lastSearchedQuery);
        $view->assignRef('filter_order', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id'));
        $view->assignRef('filter_order_dir', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc'));*/
        
        $view->action=$action;
        $view->task=$task;
        $view->controller=$controller;
        $view->lastSearchedQuery=&$lastSearchedQuery;
        $view->filter_order=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
        $view->filter_order_dir=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc');
        
        $view->frt_add();
    }


    /*
      *
      * The replace portion of the search tool
      * updates the table column according to
      * the posted replaced values.
      *
      */
    function frt_replace()
    {
        $controller = Joomla\CMS\Factory::getApplication()->getInput()->get('controller','glossary');
        if (empty($_POST['cid'])) {
            return false;
        }

        $cids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid', false);
        $search_options = Joomla\CMS\Factory::getApplication()->getInput()->get('search_options', false);
        $search_options = unserialize(urldecode($search_options));

        $frtModel =& $this->getModel('Findreplacecommon', '', $search_options);
        $replaceCount = $frtModel->frtUpdateRows($search_options);

        if ($replaceCount) {
            $responseMsg = "Total <i>$replaceCount</i> rows successfully updated in the column: <i>"
                           . $search_options['column_name'] . '</i>; table: <i>'
                           . $search_options['table_name'] . '</i>';
            $frtModel->frtInsertSearchHistory($search_options, $replaceCount, $this->oUser->username);
        } else {
            $db = JFactory::getDBO();
            $responseMsg = $db->getErrorMsg();
        }

        $responseLink = JRoute::_('index.php?option=com_gpo&controller='.$controller.'&task=frt&action=history', false);
        $this->setRedirect($responseLink, $responseMsg);
        $this->redirect();

        return false;
    }


    /*
     * Shows the past history of find & replace
     */
    function frt_history()
    {
        $controller = Joomla\CMS\Factory::getApplication()->getInput()->get('controller', 'glossary');
        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', false);
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $table_name = 'gpo_datapage_glossary';
        $frtModel =& $this->getModel('Findreplacecommon', array());
        $items = $frtModel->frtGetHistory($table_name);
        $lastSearchedQuery = $frtModel->frtGetLastSearchedQuery($table_name);

        $view =& $this->getView($controller, 'html');
        /*$view->assign('action', $action);
        $view->assign('task', $task);
        $view->assign('controller', $controller);
        $view->assignRef('items', $items);
        $view->assignRef('lastSearchedQuery', $lastSearchedQuery);*/
        
        $view->action=$action;
        $view->task=$task;
        $view->controller=$controller;
        $view->items=&$items;
        $view->lastSearchedQuery=&$lastSearchedQuery;
        
        $view->frt_history();
    }
    
    
    function search(){
      $view =& $this->getView('glossary', 'html');
      $view->search();
    }
    
    function searchresult_old(){
      $model = $this->getModel('glossary');
      $view =& $this->getView('glossary', 'html');
      $result = $model->searchGlossary($_POST);
      if(empty($result)){
        JError::raiseNotice( 100, 'Sorry, no match. Please try another search.');
        $view->search();   
      }else{
        $pagination =& $model->getSearchPagination($_POST); //get the pagination object
        /*$view->assignRef('pagination', $pagination);
        $view->assign('glossaries',$result);*/
        
        $view->pagination=&$pagination;
        $view->glossaries=$result;
        
        JFactory::getApplication()->enqueueMessage('Search Results');
        $view->searchresult();
      }
    }
    
    
    function searchresult(){
        $model =& $this->getModel('GlossarySearch');
        $model->backEnd();
        $view =& $this->getView('glossary', 'html');
        $view->filter_order = $filter_order = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
        $view->filter_order_Dir = $filter_order_dir = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'DESC');
        if(empty($model->results)){
          $application = JFactory::getApplication();
          $application->enqueueMessage('Sorry, no match. Please try another search.', 'error');
          //JError::raiseNotice( 100, 'Sorry, no match. Please try another search.');
          $view->search();   

        }
       /* $view->assignRef('glossaries', $model->results);
        $view->assignRef('pagination', $model->pagination);
        $view->assignRef('totalFound', $model->total);*/
        
        $view->glossaries=&$model->results;
        $view->pagination=&$model->pagination;
        $view->totalFound=&$model->total;
        
        $view->searchresult();
  }
}
