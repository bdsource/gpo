<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class GpoControllerMas extends GpoController
{

    function __construct()
    {
        parent::__construct();
        $this->registerTask('', 'published');
        $this->oUser = & JFactory::getUser();
        //$this->can_publish = ( $this->oUser->usertype === 'Super Administrator' ) ? true : false;
        //02-05-2011. allowed temporary permission to Administrator as per Philip's mail to allow Amélie to publish them
        
        //7 and 8 is administrator and super administrator
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->can_publish = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        $this->isAdministrator = $this->can_publish;
        
        $this->cookie_name_last_search = 'gpo_admin_mas_last_search';
        $this->cookie_name_last_search_clicked = 'gpo_admin_mas_last_search_clicked';
        
        #Sphinx Indexer command, run as gpo
        $this->reindexCommand = "/opt/sphinx/bin/indexer --config /home/gpo/sphinx/etc/sphinx.conf gpo_admin_search_mas --rotate > /home/gpo/sphinx/sphinx_admin_mas.log";
    }

    function create()
    {
        $model = $this->getModel('Mas');
        $oMas = $model->fields(false);
        $oMas->id = 0;
        $alljurisdictions = $model->getStatesofJurisdiction();
        $list_model = $this->getModel('Lists');
        $staffs = $list_model->getStaffs();
        
        $view = $this->getView('Mas', 'html');
        /*$view->assignRef('oMas', $oMas);
        $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('alljurisdictions', $alljurisdictions);
        $view->assignRef('staffs', $staffs);*/
        
        $view->oMas=&$oMas;
        $view->can_publish=&$this->can_publish;
        $view->alljurisdictions=&$alljurisdictions;
        $view->staffs=&$staffs;
        
        $view->edit();
    }

    function edit()
    {
        $model = $this->getModel('Mas');
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $oMas = $model->getUnPublishedById($id);
        if (!isset($oMas->id)) {
            $live_id = Joomla\CMS\Factory::getApplication()->getInput()->get('live_id');
            if (!empty($live_id)) {
                $oMas = $model->copyForEdit($live_id);    
                if (!empty($oMas->id)){
                    $msg = '';
                    $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=edit&id=' . $oMas->id . '&live_id=' . $oMas->live_id, false);
                    $this->setRedirect($link, $msg);
                    $this->redirect();
                }
            }
            
            $msg = 'Edit failed due to bad id';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        
        if (!empty($oMas->country_id)) {
            $oMas->country_name = $model->getLocationNameById($oMas->country_id);
        }
        
        if (!empty($oMas->city_id)) {
            $oMas->city_name = $model->getCityNameById($oMas->city_id);
        }   
        
        $alljurisdictions = $model->getStatesofJurisdiction();
           
         //var_dump($alljurisdictions); 
         //print_r("jurisdiction:"+$alljurisdictions);
        /*
        $mCitations =& $this->getModel('Citations');
        $citations  = $mCitations->getCitationsRelations($oMas->live_id, 'mas');
        $mas_cited  = implode(', ', $citations);
        
        // Call bitly url generator
        $mas_bitly_url = $model->generate_twitter_bitly_url($oMas->live_id, $oMas->twitter_url);
        */

        $view = $this->getView('Mas', 'html');
        /* $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('oMas', $oMas);*/
        $view->can_publish=&$this->can_publish;
        $view->oMas=&$oMas;
        
        //$view->assignRef('mas_bitly_url', $mas_bitly_url);
        //$view->assignRef('mas_cited', $mas_cited);
     /*   $view->assignRef('masModel', $model);*/
        $view->masModel=&$model;

        $list_model = $this->getModel('Lists');
        $staffs = $list_model->getStaffs();
      /*  $view->assignRef('staffs', $staffs); 
        $view->assignRef('alljurisdictions',$alljurisdictions);*/
        
        $view->staffs=&$staffs; 
        $view->alljurisdictions=&$alljurisdictions;
        
        $view->edit();
    }
    
    //PUBLISHED SYSTEM - section
    function publish()
    {
        $model = & $this->getModel('Mas');
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '0');
        $oMas = $model->getUnPublishedById($id);        
        if (empty($oMas))
        {
            die('Mas error: maybe id');
        }

        $response = $model->canPublish($oMas);
        
        if ($response->pass === false)
        {
            $response = new stdClass();
            $response->msg = 'This is not ready for publishing';
            $response->link = JRoute::_('index.php?option=com_gpo&controller=mas$task=edit&id=' . $oMas->id, false);
            $this->setRedirect($response->link, $response->msg);
            $this->redirect();
        }

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST')
        {
            if ($this->can_publish === false)
            {
                $response = $model->readyForPublishing($oMas);
            }
            else
            {
                $response = new stdClass();
                $response = $model->publish($oMas);
            }
            
            //GpoClearRssCache();
            //Needs to have index rebuilt
            //$model->setReIndex();
            $this->setRedirect($response->link, $response->msg);
            $this->redirect();
        }

        if (!empty($oMas->live_id))
        {
            //$mailHistory = $model->getMailHistory($oMas->live_id);
        }
        else
        {
            $mailHistory = '';
        }

        $view = & $this->getView('Mas', 'html');
      /*  $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('oMas', $oMas);*/
        
        $view->can_publish=&$this->can_publish;
        $view->oMas=&$oMas;
        
        //$view->assignRef('twitter_flag_message', $twitter_flag_message);
       /* $view->assignRef('testMode', $model->block_emails);
        $view->assignRef('mailHistory', $mailHistory);*/
        
        $view->testMode=&$model->block_emails;
        $view->mailHistory=&$mailHistory;
        
        $view->publish();
    }

/*    
    function published()
    {
        $model = & $this->getModel('Mas');
        $oItems = $model->published();

        $view = & $this->getView('Mas', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
        $mailPending = $model->mailPending();
        $view->assignRef('shouldReIndex', $shouldReIndex);

        $view->assignRef('unpublishedTotal', $model->unPublishedTotal);
        $view->assignRef('pagination', $model->pagination);

        $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('mailPending', $mailPending);
        $view->assignRef('rows', $oItems);
        $view->assignRef('oUser', $this->oUser);
        $view->published($lists);
    }
*/       
    function published()
    {
        $model = & $this->getModel('Mas');
        $jinput = JFactory::getApplication()->input;
        $oItems = $model->published($jinput->get('filter_order', 'id', 'string'), $jinput->get('filter_order_Dir', 'desc', 'string'));
		
        $view = & $this->getView('Mas', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
        $mailPending = $model->mailPending();
		
      /*  $view->assignRef('shouldReIndex', $shouldReIndex);		
        $view->assignRef('unpublishedTotal', $model->unPublishedTotal);
        $view->assignRef('pagination', $model->pagination);		
        $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('mailPending', $mailPending);
        $view->assignRef('rows', $oItems);
        $view->assignRef('oUser', $this->oUser);
        $view->assignRef('filter_order_published', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'published'));
        $view->assignRef('filter_order_Dir_published', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'DESC'));	*/
        
        $view->shouldReIndex=&$shouldReIndex;		
        $view->unpublishedTotal=&$model->unPublishedTotal;
        $view->pagination=&$model->pagination;		
        $view->can_publish=&$this->can_publish;
        $view->mailPending=&$mailPending;
        $view->rows=&$oItems;
        $view->oUser=&$this->oUser;
        $view->filter_order_published=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'published');
        $view->filter_order_Dir_published=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'DESC');	
        
        $view->published($lists);
	}
	
    /*
     * list all unpublished items.
     * list of last modified
     */
	 
    function unpublished()
    {
        $model  = $this->getModel('Mas');
		$jinput = JFactory::getApplication()->input;
        $oItems = $model->unpublished($jinput->get('filter_order', 'published', 'string'), $jinput->get('filter_order_Dir', 'asc', 'string'));
        
        $view = $this->getView('Mas', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
       /* $view->assignRef('shouldReIndex', $shouldReIndex);
        $view->assignRef('pagination', $model->pagination);
        $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('rows', $oItems);
        $view->assignRef('oUser', $this->oUser);*/
        
        $view->shouldReIndex=&$shouldReIndex;
        $view->pagination=&$model->pagination;
        $view->can_publish=&$this->can_publish;
        $view->rows=&$oItems;
        $view->oUser=&$this->oUser;
        
        $mailPending = $model->mailPending();
        /* $view->assignRef('mailPending', $mailPending); */
        $view->mailPending = &$mailPending; 

       /* $view->assignRef('filter_order', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'published'));
        $view->assignRef('filter_order_Dir', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc'));*/
        
        $view->filter_order=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'published');
        $view->filter_order_Dir=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc');
        
        $view->unpublished();
    }

    function unpublished_empty()
    {
        $model = & $this->getModel('Mas');
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            if ($_POST['cmd'] === 'del') {
                $response = $model->emptyUnpublished();
                $this->setRedirect($response->link, $response->msg);
                $this->redirect();
            }
        }
        $view = & $this->getView('Mas', 'html');
        $view->empty_unpublished();
    }

    function unpublished_delete()
    {
        $model = & $this->getModel('Mas');
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');

        if ($model->deleteUnpublishedById($id)) {
            $msg = 'Deletion: successful';
        } else {
            $msg = 'Deletion: failed';
        }
        $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=unpublished', false);
        $this->setRedirect($link, $msg);
        $this->redirect();
    }

    function published_delete()
    {
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $model = & $this->getModel('Mas');
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '0', 'POST', 'int');
            if ($model->deletePublishedById($id)) {
                //Needs to have index rebuilt
                $model->setReIndex();
                $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=reindex', false);
                $msg = 'Mas Item deleted. To remove it from the index straight away, <a href="' . $link . '" >Update the Mas index</a>.';
            } else {
                $msg = 'Deletion: failed';
            }
            $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=published', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $item = $model->getPublishedById($id);
        if (empty($item)) {
            $msg = 'Failed to find Mas Item whilst trying to confirm deletion.';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=published', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $view = & $this->getView('Mas', 'html');
       /* $view->assignRef('item', $item);*/
        $view->item=&$item;
        $view->delete();
    }

    function cancel()
    {
        $msg = '';
        $link = 'index.php?option=com_gpo&controller=mas';
        $this->setRedirect($link, $msg);
    }

    function a_save()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            echo 'Error whilst trying to save and publish: mas.a_save';
            exit();
        }
        
        $model       = $this->getModel('Mas');
        $jinput      = JFactory::getApplication()->input;
        $countryName = $jinput->get('select_mas_location', false, 'string');
        $countryId   = $model->getLocationIdByName($countryName);
        $cityName    = $jinput->get('select_mas_city', false, 'string');
        $cityId      = $model->getCityIdByName($cityName);
	
        $_POST['mas']['locations']  = explode(",", $_POST['mas']['locations']);
        $input                      = $_POST['mas'];
        
        //echo " before <br>";
        //print_r($input);
        
        $input['country_id']        = $countryId;
        $input['state_province_id'] = $jinput->get('select_mas_state_province', false, 'string');
        $input['city_id']           = $cityId;
               
        
        //print_r($input);
        //echo " <br> --- after 2 <br>  ";
        //exit();
        $response = $model->save($input);
        echo "Saved this MAS Item as Unpublished.";
        //exit();

        if (is_array($response) && $response['pass'] === true) {
            echo $response['js'];
        } else {
            echo $response;
        }
    }

    function save_and_clone_mas_to_quotes()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            echo 'Error whilst trying to save and publish: mas.a_save';
            exit();
        }

        //Save To Mas.
        $_POST['mas']['locations'] = explode(",", $_POST['mas']['locations']);
        $input = $_POST['mas'];
        $model = & $this->getModel('Mas');
        $response = $model->save($input);

        //Clone To Quotes Unpublished List
        $inputFromMasToQuotes = array(
                                    'id' => '0',
                                    'new_record' => '1',
                                    'published' => $input['published'],
                                    'locations' => $input['locations'],
                                    'source' => $input['source'],
                                    'city' => $input['city'],
                                    'title' => $input['title'] . ($input['subtitle'] == '' ? '' : ': ') . $input['subtitle'],
                                    'author' => $input['byline'],
                                    'affiliation' => $input['affiliation'],
                                    'publisher' => $input['publisher'],
                                    'volume' => $input['volume'],
                                    'issue' => $input['issue'],
                                    'page' => $input['page'],
                                    'keywords' => $input['keywords'],
                                    'websource' => $input['websource'],
                                    'sourcedoc' => $input['sourcedoc'],
                                    'share' => 1,
                                    'staff' => "",
                                    'entered'  => $input['entered'],
                                    'modified' => $input['modified'],
                                    'notes' => $input['notes'],
                                    'content' => $input['content'],
                                    'clonedFrom' => "NewCloneFromMas"
                                  );
        
        $_POST['new_record']     = '1';    //Save it as a new record instead of edit and show respective response message
        $_POST['clonedFromMas'] =  1;    //Enable to Show Quotes Edit link in the response
        
        $model =& $this->getModel('Quotes');
        $response = $model->save($inputFromMasToQuotes);
        
        if (is_array($response) && $response['pass'] === true)
        {
            echo $response['js'];
        }
        else
        {
            echo $response;
        }
        exit();
    }

    function save_publish()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            echo 'Error whilst trying to save and publish: mas.save_publish';
            exit();
        }
        
        $model       = $this->getModel('Mas');
        $jinput      = JFactory::getApplication()->input;
        $newItem     = $jinput->get('newItem', '', 'string');
        $countryName = $jinput->get('select_mas_location', false, 'string');
        $countryId   = $model->getLocationIdByName($countryName);
        $cityName    = $jinput->get('select_mas_city', false, 'string');
        $cityId      = $model->getCityIdByName($cityName);
	
        $input       = $_POST['mas'];
        $input['country_id']        = $countryId;
        //$input['state_province_id'] = $jinput->get('select_mas_state_province', false, 'string');
        $input['city_id']           = $cityId;
                 
        $response = $model->save($input);
        
        //To publish, the below section of CODE after exit() must be active for the NEXT.
        //exit();

        //echo "<br><br><br>TEST-000";
        if (is_array($response) && $response['pass'] === true)
        {
            //echo "<br><br><br>TEST-001";
            if ($response['force'] === true)
            {
                //echo "<br><br><br>TEST-002";
                echo $response['js'];
            }
            else
            {
                //echo "<br><br><br>TEST-003";
                //exit();
                $link = JRoute::_('index.php?option=com_gpo&controller=mas&newItem='.$newItem.'&task=publish&id=' . $model->new_id, false);
                $js = "<script>window.location='" . $link . "'</script>";
                echo $js;
            }
        }
        else
        {
            //echo "<br><br><br>TEST-004";
            echo $response;
        }
        
        //echo "<br><br><br>TEST-005";
        exit();
    }

    /*
      Save can only happen from edit, which is only when it is unpublished.
     */

    function save_make_citation()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            echo 'error';
            exit();
        }
        echo '<p style="#ff0000;">This has been disabled</p>';
        exit();
        /*
          $_POST['mas']['locations']=explode(",",$_POST['mas']['locations']);
          $input = $_POST['mas'];

          $model =& $this->getModel( 'Mas' );
          $response = $model->save( $input );

          if( substr( $response,0, strlen( '<script>window.location' ) ) !== '<script>window.location' )
          {
          echo $response;
          }else{
          $oMas = $model->getUnPublishedById( $model->new_id );
          if( $oMas === false )
          {
          echo 'Failed to create citation';
          exit();
          }
          $mCitation =& $this->getModel( 'CitationsMas' );
          $response = $mCitation->copyFromType( $oMas, false );
          if( $response->pass )
          {
          $js = "<script>window.location='" . $response->link . "'</script>";
          echo $js;
          }else{
          echo 'Failed to create citation';
          }
          }
          exit();
         */
    }

    /*
      Only to be called from published, so we can by pass checks.
     */

    function createcitation()
    {
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '0', 'GET', 'int');
        $mMas = & $this->getModel('Mas');
        $oMas = $mMas->getPublishedById($id);

        if ($oMas === false){
            $msg = 'Failed to make citation from Mas Item: mas.createcitation';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=published', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        $mCitation = & $this->getModel('CitationsMas');
        $response = $mCitation->copyFromType($oMas, true);
        if ($response->pass){
            $citation = &$this->getModel('Citations');
            /* add this citation number to origination quote table so that we know which citations were made from this quote */

            $citation->addCitationRelation($id, $response->citation_id, 'mas');

            $msg = 'Done! <a style="color:#000000;" href="' . $response->link . '" title="View Citation">Goto Citation</a>';
        }
        else {
          $msg = 'Failed to make citation from Mas Item';
        }
        $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=published', false);
        $this->setRedirect($link, $msg);
        $this->redirect();
    }

    function lookup()
    {
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $published = (Joomla\CMS\Factory::getApplication()->getInput()->get('state', '') == 'unpublished') ? false : true;
        $model = & $this->getModel('Mas');

        //check if next/prev button is pressed or not!
        $lookupdirection = Joomla\CMS\Factory::getApplication()->getInput()->get('lookupdirection');
        if ($lookupdirection == 'next'){
            $id = $model->getNextById($id, $published);
        }
        if ($lookupdirection == 'prev'){
            $id = $model->getPrevById($id, $published);
        }
        if ($published){
            $oItem = $model->getPublishedById($id);
        } else {
            $oItem = $model->getUnpublishedById($id);
        }
        if (!isset($oItem->id)) {
            $id = '0';
        } else {
            $id = $oItem->id;
        }
        
        if (!empty($oItem->country_id)) {
            $oItem->country_name = $model->getLocationNameById($oItem->country_id);
        }

        if (!empty($oItem->city_id)) {
            $oItem->city_name = $model->getCityNameById($oItem->city_id);
        }   
        
        $alljurisdictions = $model->getStatesofJurisdiction(); 
        $mCitations =& $this->getModel('Citations');
        $citations = $mCitations->getCitationsRelations($id, 'mas');
        $mas_cited = implode(', ', $citations);        
        $view = & $this->getView('Mas', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
        
        /*$view->assignRef('shouldReIndex', $shouldReIndex);
        $view->assignRef('id', $id);
        $view->assignRef('oMas', $oItem);
        $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('mas_cited', $mas_cited);
        $view->assignRef('alljurisdictions',$alljurisdictions);
        $view->assignRef('masModel', $model);*/
        
        $view->shouldReIndex=&$shouldReIndex;
        $view->id=&$id;
        $view->oMas=&$oItem;
        $view->can_publish=&$this->can_publish;
        $view->mas_cited=&$mas_cited;
        $view->alljurisdictions=&$alljurisdictions;
        $view->masModel=&$model;
        
        $view->lookup();
    }
       
    function build()
    {
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        $model = & $this->getModel('Mas');
        $inprogress = $model->isBuildInProgress();

        if (($inprogress === false) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            $build = (isset($_POST['build'])) ? $_POST['build'] : 'fail';
            if (
                $build === "add"
                && isset($_FILES['file_input'])
                && (float)$_FILES['file_input']['size'] > (float)'0'
            ) {
                $cmds = array();
                $cmds[] = "cd " . $model->path;
                $cmds[] = "rm *";
                $cmd = implode("\n", $cmds);
                exec($cmd, $output);
                $uploadfile = $model->path . basename($_FILES['file_input']['name']);
                if (move_uploaded_file($_FILES['file_input']['tmp_name'], $uploadfile)) {
                    $cmds = array();
                    $log_file = "mas.add." . date("Y-m-d.H.i.s", $_SERVER['REQUEST_TIME']) . ".log";
                    $cmds[] = "/usr/local/bin/php /home/palpers/gp-uploads/bin/add.mas.php > /home/palpers/gp-uploads/logs/" . $log_file . " &";
                    $cmd = implode("\n", $cmds);
                    exec($cmd, $output);
                    $msg = "Updating the Mas database, Please wait a minute, then refresh this page until it returns you to the 'Build Database'. Please wait...";
                } else {
                    ftp_debug($_FILES, '$_FILES', true, false);
                    $msg = "Failed to upload, reload the page and try again";
                }
                $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=build', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
                exit();
            } else if (
                $build === "full"
            ) {
                $cmds = array();
                $log_file = "mas.build." . date("Y-m-d.H.i.s", $_SERVER['REQUEST_TIME']) . ".log";
                $cmds[] = "/usr/local/bin/php /home/palpers/gp-uploads/bin/build.mas.php > /home/palpers/gp-uploads/logs/" . $log_file . " &";
                $cmd = implode("\n", $cmds);
                exec($cmd, $output);
                $msg = "Building FULL. Please wait a minute then, refresh the page till it tells you it has finished.";
                $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=build', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
                exit();
            } else {
                $msg = "Make sure you select the radio button.";
                $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=build', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
                exit();
            }
        }

        $view = & $this->getView('Mas', 'html');
       /* $view->assignRef('inprogress', $inprogress);*/
        $view->inprogress=&$inprogress;

        $view->build();
    }

    function search()
    {
        $last_search_clicked_on = (isset($_COOKIE[$this->cookie_name_last_search_clicked]))
                                  ? $_COOKIE[$this->cookie_name_last_search_clicked] : false;
        if ($last_search_clicked_on !== false) {
            $cookie = array();
            $cookie['name'] = $this->cookie_name_last_search_clicked; //Name of the cookie
            $cookie['expiry_date'] = $_SERVER['REQUEST_TIME'] - 86400; //Expiry date of the cookie ( 30 seconds )
            $cookie['path'] = '/'; //path allowed - linked to the domain
            setcookie(
            //cookie_name,
                $cookie['name'],
                //cookie_data,
                $cookie['data'],
                //cookie_expiry_date,
                $cookie['expiry_date'],
                //cookie_path,
                $cookie['path']
            );
            $link = JRoute::_("index.php?option=com_gpo&controller=mas&task=search&back=1#gpo-row-" . $last_search_clicked_on, false);
            $this->setRedirect($link);
            $this->redirect();
        }

        if (isset($_COOKIE[$this->cookie_name_last_search]) && $_GET['back'] === '1') {
            $data = $_COOKIE[$this->cookie_name_last_search];
            if (!empty($data)) {
                $data = unserialize($data);
            }
            $_GET = $data;
        }

        $model = & $this->getModel('Mas');

        $inprogress = $model->isBuildInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), a build is in progress.");
        }

        $inprogress = $model->isReIndexInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), The Mas is reindexed.");
        }

        $shouldReIndex = $model->shouldReIndexForSphinx();

        $modelSearch = & $this->getModel('MasSearch');
        $modelSearch->backEnd();
        $alljurisdictions = $model->getStatesofJurisdiction();

        $view = $this->getView('Mas', 'html');
       /* $view->assign('logged_in', $this->logged_in);
        $view->assignRef('rows', $modelSearch->results);
        $view->assignRef('pagination', $modelSearch->pagination);
        $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('oUser', $this->oUser);
        $view->assignRef('shouldReIndex', $shouldReIndex);
        $view->assignRef('alljurisdictions', $alljurisdictions);*/
        
        $view->logged_in=$this->logged_in;
        $view->rows=&$modelSearch->results;
        $view->pagination=&$modelSearch->pagination;
        $view->can_publish=&$this->can_publish;
        $view->oUser=&$this->oUser;
        $view->shouldReIndex=&$shouldReIndex;
        $view->alljurisdictions=&$alljurisdictions;
        
        if ($_GET['revise'] === '1') {
            if (isset($_GET['mas']['locations']) && !empty($_GET['mas']['locations'])) {
                $locations = explode(",", $_GET['mas']['locations']);
            } else {
                $locations = null;
            }
            $oMas = (object)$_GET['mas'];
            $oMas->published_range = (object)$oMas->published_range;
            $oMas->locations = $locations;
        /*    $view->assignRef('oMas', $oMas);
            $view->assignRef('locations', $locations);*/
            
            $view->oMas=&$oMas;
            $view->locations=&$locations;
            
            $view->search();
        } else if (empty($modelSearch->results)) {
            $view->search();
        } else {
            $data = serialize($_GET);
            $cookie = array();
            $cookie['name'] = $this->cookie_name_last_search; //Name of the cookie
            $cookie['data'] = $data; //Data you want to store - 3 is the magic number
            $cookie['expiry_date'] = $_SERVER['REQUEST_TIME'] + 86400; //Expiry date of the cookie ( 30 seconds )
            $cookie['path'] = '/'; //path allowed - linked to the domain
            setcookie(
            //cookie_name,
                $cookie['name'],
                //cookie_data,
                $cookie['data'],
                //cookie_expiry_date,
                $cookie['expiry_date'],
                //cookie_path,
                $cookie['path']
            );


            $cookie = array();
            $cookie['name'] = $this->cookie_name_last_search_clicked; //Name of the cookie
            $cookie['expiry_date'] = $_SERVER['REQUEST_TIME'] - 86400; //Expiry date of the cookie ( 30 seconds )
            $cookie['path'] = '/'; //path allowed - linked to the domain
            setcookie(
            //cookie_name,
                $cookie['name'],
                //cookie_data,
                $cookie['data'],
                //cookie_expiry_date,
                $cookie['expiry_date'],
                //cookie_path,
                $cookie['path']
            );
            //check for "topic" cookie
            $cookie_name_topic = 'topic_id';
            if (isset($_COOKIE[$cookie_name_topic]) && $_GET['back'] !== '1') {
                $cookie = array();
                $cookie['name'] = $cookie_name_topic; //Name of the cookie
                $cookie['expiry_date'] = $_SERVER['REQUEST_TIME'] - 86400; //Expiry date of the cookie ( 30 seconds )
                $cookie['path'] = '/'; //path allowed - linked to the domain
                setcookie(
                //cookie_name,
                    $cookie['name'],
                    //cookie_data,
                    $cookie['data'],
                    //cookie_expiry_date,
                    $cookie['expiry_date'],
                    //cookie_path,
                    $cookie['path']
                );
            }

            /*$view->assignRef('totalFound', $modelSearch->total);*/
            
            $view->totalFound=&$modelSearch->total;

            $view->searchResults();
        }
    }

    function searchresult_clicked()
    {
        $id = (isset($_POST['id'])) ? $_POST['id'] : false;
        $pos = (isset($_POST['pos'])) ? $_POST['pos'] : false;
        if ($pos === false) {
            exit();
        }

        $data = $pos . "-" . $id;
        $cookie = array();
        $cookie['name'] = $this->cookie_name_last_search_clicked; //Name of the cookie
        $cookie['data'] = $data; //Data you want to store - 3 is the magic number
        $cookie['expiry_date'] = $_SERVER['REQUEST_TIME'] + 86400; //Expiry date of the cookie ( 30 seconds )
        $cookie['path'] = '/'; //path allowed - linked to the domain
        setcookie(
        //cookie_name,
            $cookie['name'],
            //cookie_data,
            $cookie['data'],
            //cookie_expiry_date,
            $cookie['expiry_date'],
            //cookie_path,
            $cookie['path']
        );
        echo '
<script type="text/javascript">
//<![CDATA[	
window.location="' . JRoute::_("index.php?option=com_gpo&controller=mas&task=lookup&id=" . $id, false) . '";
//]]>
</script>
		';
        exit();
    }

    function reindex()
    {
        //check they have permission
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $model = & $this->getModel('Mas');
        $inprogress = $model->isBuildInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Mas is currently being built.");
        }

        $inprogress = $model->isReIndexInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Mas are currently being updated( Reindexing of Sphinx ).");
        }

        if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            $go = (isset($_POST['reindex'])) ? true : false;
            $force = (isset($_POST['force'])) ? true : false;
            if ($go) {
                $model->setReIndex();
                GpoClearRssCache();
                //$cmd = "/usr/bin/sphinx-cron.sh > /tmp/sphinx-cron.log &";
                $cmd = $this->reindexCommand . " &";
                exec($cmd, $output);

                $msg = "Update of index is running in background, It will take a while to complete.";
                $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
                exit();
            }
        }
        $view = & $this->getView('Mas', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
       /* $view->assignRef('shouldReIndex', $shouldReIndex);*/
        $view->shouldReIndex=$shouldReIndex;

        $view->reindex();
    }

    function reindex2()
    {
        $model = & $this->getModel('Mas');

        $inprogress = $model->isBuildInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Mas is currently being built.");
        }


        $inprogress = $model->isReIndexInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Mas are currently being updated( Reindexing of Sphinx ).");
        }
        $model->setReIndex();
        GpoClearRssCache();
        //$cmd = "/usr/bin/sphinx-cron.sh > /tmp/sphinx-cron.log &";
        $cmd = $this->reindexCommand . " &";
        exec($cmd, $output);

        $msg = "Update of index is running in background, It will take a while to complete.";
        $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
        $this->setRedirect($link, $msg);
        $this->redirect();
        $view = & $this->getView('Mas', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
        
        /*$view->assignRef('shouldReIndex', $shouldReIndex);*/
        $view->shouldReIndex=&$shouldReIndex;
        $view->reindex();
    }

    function reindex_old()
    {
        //this is kept as backup of the previous method
        //check they have permission
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $model = & $this->getModel('Mas');
        $inprogress = $model->isBuildInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Mas is currently being built.");
        }

        $inprogress = $model->isReIndexInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Mas are currently being updated( Reindexing of Sphinx ).");
        }

        if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            $go = (isset($_POST['reindex'])) ? true : false;
            $force = (isset($_POST['force'])) ? true : false;
            if ($go) {
                if ($force) {
                    //Needs to have index rebuilt
                    $model->setReIndex();
                }
                GpoClearRssCache();
                $cmd = "/usr/sbin/sphinx-gpo mas > /dev/null 2>&1 &";
                exec($cmd, $output);

                $msg = "Update of index completed.";
                $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
                exit();
            }
        }
        $view = & $this->getView('Mas', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
       /* $view->assignRef('shouldReIndex', $shouldReIndex);*/
        $view->shouldReIndex=$shouldReIndex;
        $view->reindex();
    }

    function export()
    {
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        if (file_exists('/home/palpers/gp-downloads/mas/making.txt')) {
            die("Please wait a moment and try again( by hitting F5 ), the system is still exporting your last request.");
        }


        $model = & $this->getModel('Mas');
        $inprogress = $model->isBuildInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Mas is currently being built.");
        }

        if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            //this is used to download the file from the web browser
            if (Joomla\CMS\Factory::getApplication()->getInput()->get('download') === '1') {
                $filename = '/home/palpers/gp-downloads/mas/data.zip';
                if (file_exists($filename)) {
                    $filesize = filesize($filename);
                    if ($filesize) {
                        ob_end_clean();
                        ini_set('zlib.output_compression', 'Off');
                        header("Cache-Control: public");
                        /* IE FIX : FireFox Compatible */

                        header('Content-Description: File Transfer');
                        //					header('Content-Type: application/octet-stream');
                        header('Content-Type: application/zip');
                        header('Content-Disposition: attachment; filename="data.zip"');
                        header('Content-Transfer-Encoding: binary');
                        header('Content-Length: ' . $filesize);
                        ob_flush();
                        readfile($filename);
                        unlink($filename);
                        exit;
                    }
                }
                $msg = 'Error whilst trying to download, Did you export first?';
                $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=export', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
            }
            $cmds = array();
            $cmds[] = "cd /home/palpers/gp-downloads/mas";
            $cmds[] = "rm *";
            $cmds[] = "touch making.txt";
            //how can I make this quicker?
            $extra_argv = ($_POST['nocigar'] === '0') ? "" : ' "nocigar"';
            switch (Joomla\CMS\Factory::getApplication()->getInput()->get('type')) {
                case 'last_24hrs':
                    $orderby = Joomla\CMS\Factory::getApplication()->getInput()->get('last_24hrs_type');
                    $msg = 'Exported records which were ' . $orderby . ' in the last 24hours.';

                    $cmds[] = '/usr/local/bin/php /home/palpers/gp-uploads/bin/mas.export.last_24.php "' . $orderby . '" ' . $extra_argv . ' > /dev/null 2>&1 &';
                    break;
                case 'since_id':
                    $id = Joomla\CMS\Factory::getApplication()->getInput()->get('since_id');
                    $msg = 'Exported records which have an id higher than ' . $id . '.';
                    $cmds[] = '/usr/local/bin/php /home/palpers/gp-uploads/bin/mas.export.since_id.php "' . $id . '" ' . $extra_argv . ' > /dev/null 2>&1 &';
                    break;
                case 'following_ids':
                    $ids = Joomla\CMS\Factory::getApplication()->getInput()->get('following_ids');
                    $msg = 'Exported records with the following ids ' . $ids . '.';
                    $cmds[] = '/usr/local/bin/php /home/palpers/gp-uploads/bin/mas.export.ids.php "' . $ids . '" ' . $extra_argv . ' > /dev/null 2>&1 &';
                    break;
                case 'date_range':
                    $published_range = Joomla\CMS\Factory::getApplication()->getInput()->get('published_range');
                    $msg = 'Exported records which were published from: ' . $published_range['from'] . ' to:' . $published_range['to'] . '.';

                    $cmds[] = '/usr/local/bin/php /home/palpers/gp-uploads/bin/mas.export.published_range.php "' . $published_range['from'] . '" "' . $published_range['to'] . '" ' . $extra_argv . ' > /dev/null 2>&1 &';
                    break;
                case 'all':
                    $msg = 'Exported all records.';
                    $cmds[] = '/usr/local/bin/php /home/palpers/gp-uploads/bin/mas.export.all.php ' . $extra_argv . ' > /dev/null 2>&1 &';
                    break;

                default:
                    $msg = 'Remember to select an option';
                    $cmds = '';
                    break;
            }
            if (!empty($cmds)) {
                $cmd = implode("\n", $cmds);
                $output = null;
                system($cmd);
            }


            $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=export', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $view = & $this->getView('Mas', 'html');
        $view->export();
    }

    function sphinxscript()
    {
        $cmd = "/usr/bin/sphinx-cron.sh > /tmp/sphinx-cron.log 2>&1 &";
        $output = `$cmd`;
        echo '<pre>';
        print_r($output);
        echo '</pre>';
    }

    function maillist()
    {
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }


        $model = & $this->getModel('Mas');
        $view = & $this->getView('Mas', 'html');

        //send mail
        if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            error_reporting(E_ALL);
            //unpublished number of mas
            $model->unPublishedTotal = $model->total(false);
            /*$view->assignRef('unpublishedTotal', $model->unPublishedTotal);*/
            $view->unpublishedTotal=&$model->unPublishedTotal;
            $html = $model->mailToPublic();
            if (!empty($html)) {
                /*$view->assignRef('summary', $html);*/
                $view->summary=$html;
                $view->mailOutSummary();
                return;
            }
        }


        $items = $model->getMailList();
      /*  $view->assignRef('rows', $items);
        $view->assignRef('testMode', $model->block_emails);*/
        
        $view->rows=&$items;
        $view->testMode=&$model->block_emails;
        
        $oItems = $model->published();

        //show number of unpublished items counter
        $unpublished_items = $model->total(false);
        /*$view->assignRef('unpublishedTotal', $unpublished_items);*/
        $view->unpublishedTotal=$unpublished_items;

        $view->maillist();
        //		$location = Joomla\CMS\Factory::getApplication()->getInput()->get( 'location', '','GET','string');
        //		$items = $model->getMailListByLocation( $location );
    }

    function email_public_remove()
    {
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '0', 'GET', 'int');
        $model = & $this->getModel('Mas');

        if ($id !== 'all') {
            $model->deleteMailToPublic($id);
        }

        $msg = 'Mas Id: ' . $id . ' has been removed from the public mail list.';
        $link = JRoute::_('index.php?option=com_gpo&controller=mas&task=maillist', false);
        $this->setRedirect($link, $msg);
        $this->redirect();
    }

    function test()
    {
        jimport('gpo.html_stripper');
        $unclean = "\n\r\n" . "<p>I am a test <br />
here I am
		</p>";
        $html_stripper = new html_stripper();
        $html_stripper->allow_tags(array());

        $clean = $html_stripper->clean($unclean);
        ftp_debug($clean, 'clean html');
    }
    
    
    function mailtest( ) {
        
        $toEmail = Joomla\CMS\Factory::getApplication()->getInput()->get('toemail', false);
        $model   = & $this->getModel('Mas');
        $model->localMailTest($toEmail);
        
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

        /* only admins can access this feature */
        if ($this->isAdministrator !== true) {
            $msg = 'At present, your access level does not allow you to access the FRT tool.';
            $link = JRoute::_('index.php?option=com_gpo&controller=mas', false);
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
        $searchResultNcites = null;

        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $controller = Joomla\CMS\Factory::getApplication()->getInput()->get('controller', 'mas');
        $task = 'frt';
        $tableName = 'gpo_mas'; //seach in this table
        
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'
            && $_POST['action'] === 'add'
        ) {
            //$tableName = Joomla\CMS\Factory::getApplication()->getInput()->get('table', false);
            $tableName = 'gpo_mas'; //seach in the mas table
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

            $frtModel = $this->getModel('Findreplacecommon', '', $_POST['swap']);
            $masModel = $this->getModel('Mas');
			$searchResult = $frtModel->frtPerformSearch();
            $replacedResult = $frtModel->frtSearchReplace($searchResult, $_POST['swap']);
            /*
            if( !in_array($frtModel->columnName, array('share','keywords','city','volume','poaim','issue','page','gpnheader','sourcedoc','affiliation','staff')) ){
                $searchResultNcites = $frtModel->frtNcitesFinding();
            } 
            */
            $frtModel->frtInsertLastSearch($_POST['swap'], $this->oUser->username);

            /* view results */
            $view =& $this->getView($controller, 'html');
         /*   $view->assignRef('total', $frtModel->total);
            $view->assignRef('options', $_POST['swap']);
            $view->assignRef('items', $searchResult);*/
            
            $view->total=&$frtModel->total;
            $view->options=&$_POST['swap'];
            $view->items=&$searchResult;
            
            //$view->assignRef('ncites_items', $searchResultNcites);
            $view->assignRef('replacedItems', $replacedResult);
            
            $view->replacedItems=&$replacedResult;

            //pagination
        /*    $view->assignRef('pagination', $frtModel->pagination);
            $view->assignRef('rows', count($searchResult));
            $view->assignRef('filter_order', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id'));
            $view->assignRef('filter_order_dir', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc'));*/
            
            $view->pagination=&$frtModel->pagination;
            $view->rows=&count($searchResult);
            $view->filter_order=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
            $view->filter_order_dir=&Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc');
            
            //$view->assignRef('total', $frtModel->total);
           /* $view->assignRef('masModel', $masModel);
            $view->assign('action', $action);
            $view->assign('task', $task);
            $view->assign('controller', $controller);*/
            
            $view->masModel=&$masModel;
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
       /* $view->assign('action', $action);
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
    	//$controller = 'mas';
        $controller = Joomla\CMS\Factory::getApplication()->getInput()->get('controller','mas');
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
    	//$controller = 'mas';
        $controller = Joomla\CMS\Factory::getApplication()->getInput()->get('controller', 'mas');
        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', false);
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $table_name = 'gpo_mas';
        $frtModel =& $this->getModel('Findreplacecommon', array());
        $items = $frtModel->frtGetHistory($table_name);
        $lastSearchedQuery = $frtModel->frtGetLastSearchedQuery($table_name);

        $view =& $this->getView($controller, 'html');
       /* $view->assign('action', $action);
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

    function mailTest2() {
        ini_set('display_errors',1);
        error_reporting(E_ALL);
        
        $model = $this->getModel('Mas');
        $model->localMailTest('khan.morshed@gmail.com');
        echo 'Mail sent';
    }
    
    
	/*
	 * the ajax response for getting the list of states 
         * by country code
         * 
	 */
	function getStates() {
           $jinput      = JFactory::getApplication()->input;
           $countryName = $jinput->get('countryName', false, 'string');
           $html        = '<option value="0">No States/Province found</option>';
           
	   if ( empty( $countryName ) ) {
	        echo $html;
	        exit();
	   }
           
           $masModel  = $this->getModel('mas', array());
           $countryId = $masModel->getLocationIdByName($countryName);
	   $data      = $masModel->getStatesByCountry($countryId);
	   
	   if( $data ) {
	   	$html = "<option value='0'>-- Select State or Province  --</option>";
	   	foreach( $data as $key => $location ) {
	   		$locationName = $location['name'];
	   		$html .= '<option title="' . $locationName . '" value="'. $location['id'] .'">' . $locationName . '</option>';
	   	}
	   }
	   
	   echo $html;
	   exit();
	}
     
}

?>