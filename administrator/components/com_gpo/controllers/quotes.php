<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class GpoControllerQuotes extends GpoController
{
    function __construct()
    {
        parent::__construct();
        $this->registerTask('', 'published');
        $this->oUser = & JFactory::getUser();
        
        $groupsUserIsIn        = JAccess::getGroupsByUser($this->oUser->id);
        $this->can_publish     = $this->oUser->get('isRoot');
        $this->isAdministrator = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
        $this->isSuperAdmin    = $this->oUser->get('isRoot');

        $this->cookie_name_last_search         = 'gpo_admin_quotes_last_search';
        $this->cookie_name_last_search_clicked = 'gpo_admin_quotes_last_search_clicked';
        
        #Sphinx Indexer command, run as gpo
        $this->reindexCommand = "/opt/sphinx/bin/indexer --config /home/gpo/sphinx/etc/sphinx.conf gpo_admin_search_quotes --rotate > /home/gpo/sphinx/sphinx_admin_quotes.log";
    }

    function exporttemplate()
    {
        if( !($this->isSuperAdmin) ) {
            $this->setRedirect('index.php?option=com_gpo&controller=quotes&task=search', 'Sorry, you don\'t have the permission to use this feature');
            return false;
        }

        //check if the form is submitted
        $model = $this->getModel('Quotes');

        $allowedFields = $model->getCSVExportFields();

        if (Joomla\CMS\Factory::getApplication()->getInput()->get('action')) {
            $csvTemplate = Joomla\CMS\Factory::getApplication()->getInput()->get('csv_template');

            $txtTemplate = array('header' => Joomla\CMS\Factory::getApplication()->getInput()->get('txt_header'), 'body' => Joomla\CMS\Factory::getApplication()->getInput()->get('txt_template'), 'footer' => Joomla\CMS\Factory::getApplication()->getInput()->get('txt_footer'));

            $csvupdate = $model->updateCsvTemplate($csvTemplate);

            $txtupdate = $model->updateTxtTemplate($txtTemplate);

            if ($csvupdate) {
                $msg[] = 'CSV template updated successfully!';
            } else {
                $msg[] = 'CSV template update failed!';
            }

            if ($txtupdate) {
                $msg[] = 'Txt template updated successfully!';
            } else {
                $msg[] = 'Txt template update failed!';
            }


            $this->setRedirect('index.php?option=com_gpo&controller=quotes&task=search', implode('<br/>', $msg));

            $this->redirect();
        }
        $template = $model->getExportTemplate();
        $view = &$this->getView('Quotes', 'html');
        $view->template = &$template;
    	$view->valid_fields = &$allowedFields;
        $view->exportTemplate();


    }

    function exporttxt()
    {
        if( !($this->isSuperAdmin || $this->isAdministrator) ) {
            $this->setRedirect('index.php?option=com_gpo&controller=quotes&task=search', 'Sorry, you don\'t have the permission to use this feature');
            return false;
        }
        
        $model = &$this->getModel('Quotes');
        $export_ids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid');
        $texts = $model->exportQuotesToTxt($export_ids);

        //echo '<textarea rows="20" cols="180">'.$texts.'</textarea>';

        $filesize = strlen($texts);
        if ($filesize) {
            // IE FIX : FireFox Compatible
            header("Content-Type: application/octet-stream Charset=utf-8\n");
            header("Content-Disposition: attachment; filename=\"GPO Quotes Export File.txt\"");
            //Print It
            echo $texts;
            ;
            exit;
        } else {
            return 0;
        }

    }

    function exportcsv()
    {
        if( !($this->isSuperAdmin || $this->isAdministrator) ) {
            $this->setRedirect('index.php?option=com_gpo&controller=quotes&task=search', 'Sorry, you don\'t have the permission to use this feature');
            return false;
        }
        
        $model = &$this->getModel('Quotes');

        //check the fields in template
        $template = $model->getExportTemplate();

        //make it an array
        $textToArray = explode(PHP_EOL, $template->csv_template);

        //verify that there are no fields in the template that are not valid. drop invalid fields.

        $csvFieldsInTemplate = array();
        foreach ($textToArray AS $key => $field) {
            $field = trim($field);
            if (!empty($field)) {
                $csvFieldsInTemplate[] = trim($field);
            }
        }
        unset($textToArray);
        //get the quotes ID to export
        $export_ids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid');

        //send the IDs and Fields to model to get CSV data.
        $texts = $model->exportQuotesToCsv($export_ids, $csvFieldsInTemplate);

        //echo '<textarea rows="20" cols="180">'.$texts.'</textarea>';
        //echo $texts;

        $filesize = strlen($texts);
        if ($filesize) {
            // IE FIX : FireFox Compatible
            header("Content-Type: text/csv; charset=utf-16LE\n");
            header("Content-Disposition: attachment; filename=\"GPO Quotes Export File.csv\" \n");
            echo $texts;
            exit;
        } else {
            return 0;
        }


    }


    function exportexcel()
    {
        if( !($this->isSuperAdmin || $this->isAdministrator) ) {
            $this->setRedirect('index.php?option=com_gpo&controller=quotes&task=search', 'Sorry, you don\'t have the permission to use this feature');
            return false;
        }
        $model = &$this->getModel('Quotes');

        //check the fields in template
        $template = $model->getExportTemplate();

        //make it an array
        $textToArray = explode(PHP_EOL, $template->csv_template);

        //verify that there are no fields in the template that are not valid. drop invalid fields.

        $csvFieldsInTemplate = array();
        foreach ($textToArray AS $key => $field) {
            $field = trim($field);
            if (!empty($field)) {
                $csvFieldsInTemplate[] = trim($field);
            }
        }
        unset($textToArray);
        //get the quotes ID to export
        $export_ids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid');

        //send the IDs and Fields to model to get CSV data.
        $texts = $model->exportQuotesToCsv($export_ids, $csvFieldsInTemplate);

        //echo '<textarea rows="20" cols="180">'.$texts.'</textarea>';
        //echo $texts;

        $filesize = strlen($texts);
        if ($filesize) {
            // IE FIX : FireFox Compatible
            header("Content-Type: text/comma-separated-value; charset=utf16le\n");
            header("Content-Disposition: attachment; filename=\"GPO Quotes Export File.csv\" \n");
            echo $texts;
            exit;
        } else {
            return 0;
        }


    }

    function exportcsv2()
    {
        if( !($this->isSuperAdmin || $this->isAdministrator) ) {
            $this->setRedirect('index.php?option=com_gpo&controller=quotes&task=search', 'Sorry, you don\'t have the permission to use this feature');
            return false;
        }
        $model = &$this->getModel('Quotes');

        //check the fields in template
        $template = $model->getExportTemplate();

        //make it an array
        $textToArray = explode(PHP_EOL, $template->csv_template);

        //verify that there are no fields in the template that are not valid. drop invalid fields.

        $csvFieldsInTemplate = array();
        foreach ($textToArray AS $key => $field) {
            $field = trim($field);
            if (!empty($field)) {
                $csvFieldsInTemplate[] = trim($field);
            }
        }
        unset($textToArray);
        //get the quotes ID to export
        $export_ids = Joomla\CMS\Factory::getApplication()->getInput()->get('cid');

        //send the IDs and Fields to model to get CSV data.
        $texts = $model->exportQuotesToCsv2($export_ids, $csvFieldsInTemplate);

        //echo '<textarea rows="20" cols="180">'.$texts.'</textarea>';
        //echo $texts;

        $filesize = strlen($texts);
        if ($filesize) {
            // IE FIX : FireFox Compatible
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=\"GPO Quotes Export File.xls\"");
            //header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //header('Content-Length: ' . strlen(utf8_decode($texts)) );
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            //Print It
            echo $texts;
            exit;
        } else {
            return 0;
        }
        
    }

    function create()
    {
        $model =& $this->getModel('Quotes');
        $oQuote = $model->fields(false);
        $oQuote->id = 0;
        $view =& $this->getView('Quotes', 'html');
        /*$view->assignRef('oQuote', $oQuote);
        $view->assignRef('can_publish', $this->can_publish);*/

        $view->oQuote=&$oQuote;
        $view->can_publish=&$this->can_publish;

        //load lists model
        $list_model = $this->getModel('Lists');
        $staffs = $list_model->getStaffs();
        //$view->assignRef('staffs', $staffs);
        $view->staffs=&$staffs;
        $view->edit();
    }


    function edit()
    {
        $model =& $this->getModel('Quotes');

        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $oQuote =& $model->getUnPublishedById($id);
        //var_dump($oQuote);
        if (!isset($oQuote->id)) {
            $live_id = Joomla\CMS\Factory::getApplication()->getInput()->get('live_id');
            if (!empty($live_id)) {
                $oQuote = $model->copyForEdit($live_id);

                if (!empty($oQuote->id)) {
                    $msg = '';
                    $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=edit&id=' . $oQuote->id . '&live_id=' . $oQuote->live_id, false);
                    $this->setRedirect($link, $msg);
                    $this->redirect();
                }
            }
            $msg = 'Edit failed due to bad id';
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        $mCitations =& $this->getModel('Citations');
        $citations = $mCitations->getCitationsRelations($oQuote->live_id, 'quotes');
        $quote_cited = implode(', ', $citations);

        $view =& $this->getView('Quotes', 'html');
      /*  $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('oQuote', $oQuote);
        $view->assignRef('quotes_cited', $quote_cited);*/

        $view->can_publish=&$this->can_publish;
        $view->oQuote=&$oQuote;
        $view->quotes_cited=&$quote_cited;

        //load lists model
        $list_model = $this->getModel('Lists');
        $staffs = $list_model->getStaffs();
        //$view->assignRef('staffs', $staffs);
        $view->staffs=$staffs;
        $view->edit();
    }


//PUBLISHED SYSTEM - section
    function publish()
    {
        $model =& $this->getModel('Quotes');

        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '0');

        $oQuote = $model->getUnPublishedById($id);

        if (empty($oQuote)) {
            die('Quotes error: maybe id');
        }

        $response = $model->canPublish($oQuote);

        if ($response->pass === false) {
            $response = new stdClass();
            $response->msg = 'This is not ready for publishing';
            $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes$task=edit&id=' . $oQuote->id, false);
            $this->setRedirect($response->link, $response->msg);
            $this->redirect();
        }


        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            if ($this->can_publish === false) {
                $response = $model->readyForPublishing($oQuote);
            } else {
                $response = new stdClass();
                $response = $model->publish($oQuote);
            }

            //Needs to have index rebuilt
            $model->setReIndex();
            $this->setRedirect($response->link, $response->msg);
            $this->redirect();
        }


        $view =& $this->getView('Quotes', 'html');
      /*  $view->assignRef('oQuote', $oQuote);
        $view->assignRef('can_publish', $this->can_publish);*/

        $view->oQuote=$oQuote;
        $view->can_publish=$this->can_publish;

        $view->publish();
    }


    function publish_green()
    {
        if ($this->can_publish !== true) {
            $response = new stdClass();
            $response->msg = 'You are not able to do this, with that account.';
            $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
            $this->setRedirect($response->link, $response->msg);
            $this->redirect();
        }

        $model =& $this->getModel('Quotes');
        $items = $model->getPublishGreen();

        if (empty($items)) {
            $response = new stdClass();
            $response->msg = 'There are no Green Published Items.';
            $response->link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
            $this->setRedirect($response->link, $response->msg);
            $this->redirect();
        }

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            $response = $model->publishGreen($items);
            //Needs to have index rebuilt
            $model->setReIndex();
            $this->setRedirect($response->link, $response->msg);
            $this->redirect();
        }


        $view =& $this->getView('Quotes', 'html');
       /* $view->assignRef('oQuote', $oQuote);
        $view->assignRef('can_publish', $this->can_publish);*/

        $view->oQuote= &$oQuote;
        $view->can_publish= &$this->can_publish;

        $view->publishGreen();
    }


    function published()
    {

        $model =& $this->getModel('Quotes');
        $oItems = $model->published();
        $view =& $this->getView('Quotes', 'html');
       // echo($view);die();
        $shouldReIndex = $model->shouldReIndexForSphinx();
        $view->shouldReIndex=&$shouldReIndex;
        $view->unpublishedTotal=&$model->unpublishedTotal;
        $view->pagination=&$model->pagination;
        $view->can_publish=&$this->can_publish;
        $view->rows=&$oItems;
        $view->oUser=&$this->oUser;
        $view->published();
    }


    /*
      * list all unpublished items.
      * list of last modified
      */
    function unpublished()
    {
        $model =& $this->getModel('Quotes');
        $orderDir = empty(Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc')) ? 'asc' : Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', 'asc');
        $orderBy  = empty(Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id')) ? 'id' : Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
       
        $oItems = $model->unpublished($orderBy, $orderDir);
        $view =& $this->getView('Quotes', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
        
    /*  $view->assignRef('shouldReIndex', $shouldReIndex);
        $view->assignRef('pagination', $model->pagination);
        $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('rows', $oItems);
        $view->assignRef('oUser', $this->oUser);
        $view->assignRef('filter_order', $orderBy);
        $view->assignRef('filter_order_Dir', $orderDir);*/

        $view->shouldReIndex=&$shouldReIndex;
        $view->pagination=&$model->pagination;
        $view->can_publish=&$this->can_publish;
        $view->rows=&$oItems;
        $view->oUser=&$this->oUser;
        $view->filter_order=&$orderBy;
        $view->filter_order_Dir=&$orderDir;

        $view->unpublished();
    }


    function unpublished_empty()
    {
        $model =& $this->getModel('Quotes');
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            if ($_POST['cmd'] === 'del') {
                $response = $model->emptyUnpublished();
                $this->setRedirect($response->link, $response->msg);
                $this->redirect();
            }
        }
        $view =& $this->getView('Quotes', 'html');
        $view->empty_unpublished();
    }


    function unpublished_delete()
    {
        $model =& $this->getModel('Quotes');

        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');

        if ($model->deleteUnpublishedById($id)) {
            $msg = 'Deletion: successful';
        } else {
            $msg = 'Deletion: failed';
        }
        $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=unpublished', false);
        $this->setRedirect($link, $msg);
        $this->redirect();
    }


    function published_delete()
    {
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $model =& $this->getModel('Quotes');

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', '0', 'POST', 'int');
            if ($model->deletePublishedById($id)) {
                //Needs to have index rebuilt
                $model->setReIndex();
                $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=reindex', false);
                $msg = 'Quote deleted. To remove it from the index straight away, <a href="' . $link . '" >Update the Quotes index</a>.';
            } else {
                $msg = 'Deletion: failed';
            }
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=published', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $item = $model->getPublishedById($id);
        if (empty($item)) {
            $msg = 'Failed to find Quote whilst trying to confirm deletion.';
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=published', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        $view =& $this->getView('Quotes', 'html');
        //$view->assignRef('item', $item);
        $view->item=&$item;

        $view->delete();
    }


    function cancel()
    {
        $msg = '';
        $link = 'index.php?option=com_gpo&controller=quotes';
        $this->setRedirect($link, $msg);
    }


    function a_save()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            echo 'Error whilst trying to save and publish: quotes.a_save';
            exit();
        }

        $_POST['quotes']['locations'] = explode(",", $_POST['quotes']['locations']);
        $input = $_POST['quotes'];
        $model =& $this->getModel('Quotes');
        $response = $model->save($input);
        //		echo $response;
        //		exit();
        if (is_array($response) && $response['pass'] === true) {
            echo $response['js'];
        } else {
            echo $response;
        }
        exit();
    }


    /**
     * @todo Copy staff data to quotes table from unpublished table when the quote is published
     *
     */
    function save_publish()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            echo 'Error whilst trying to save and publish: quotes.save_publish';
            exit();
        }

        $_POST['quotes']['locations'] = explode(",", $_POST['quotes']['locations']);
        $input = $_POST['quotes'];
        $model =& $this->getModel('Quotes');
        $response = $model->save($input);

        if (is_array($response) && $response['pass'] === true) {
            if ($response['force'] === true) {
                echo $response['js'];
            } else {
                $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=publish&id=' . $model->new_id, false);
                $js = "<script>window.location='" . $link . "'</script>";
                echo $js;
            }
        } else {
            echo $response;
        }
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
                $_POST['quotes']['locations']=explode(",",$_POST['quotes']['locations']);
                $input = $_POST['quotes'];

                $model =& $this->getModel( 'Quotes' );
                $response = $model->save( $input );

                if( substr( $response,0, strlen( '<script>window.location' ) ) !== '<script>window.location' )
                {
                    echo $response;
                }else{
                    $oQuote = $model->getUnPublishedById( $model->new_id );
                    if( $oQuote === false )
                    {
                        echo 'Failed to create citation';
                        exit();
                    }
                    $mCitation =& $this->getModel( 'CitationsQuotes' );
                    $response = $mCitation->copyFromType( $oQuote, false );
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
        $model =& $this->getModel('Quotes');
        $oQuote = $model->getPublishedById($id);
        if ($oQuote === false) {
            $msg = 'Failed to make citation from Quotes Item: quotes.createcitation';
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=published', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        $mCitation =& $this->getModel('CitationsQuotes');
        $response = $mCitation->copyFromType($oQuote, true);

        /* add this citation number to origination quote table so that we know which citations were made from this quote */
        $citations_model =& $this->getModel('Citations');
        $citations_model->addCitationRelation($id, $response->citation_id, 'quotes');

        if ($response->pass) {
            $msg = 'Done! <a style="color:#000000;" href="' . $response->link . '" title="View Citation">Goto Citation</a>';
        } else {
            $msg = 'Failed to make citation from Quotes Item';
        }
        $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=published', false);
        $this->setRedirect($link, $msg);
        $this->redirect();
    }


    function lookup()
    {
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get('id');
        $published = (Joomla\CMS\Factory::getApplication()->getInput()->get('state', '') == 'unpublished') ? false : true;
        $model =& $this->getModel('Quotes');

        //check if next/prev button is pressed or not!
        $lookupdirection = Joomla\CMS\Factory::getApplication()->getInput()->get('lookupdirection');
        if ($lookupdirection == 'next') {
            $id = $model->getNextById($id, $published);
        }
        if ($lookupdirection == 'prev') {
            $id = $model->getPrevById($id, $published);
        }
        if ($published) {
            $oItem =& $model->getPublishedById($id);
        } else {
            $oItem = &$model->getUnpublishedById($id);
        }

        if (!isset($oItem->id)) {
            $id = '0';
        } else {
            $id = $oItem->id;
        }

        $mCitations =& $this->getModel('Citations');
        $citations = $mCitations->getCitationsRelations($id, 'quotes');
        $quotes_cited = implode(', ', $citations);


        $view =& $this->getView('Quotes', 'html');
        $shouldReIndex = $model->shouldReIndexForSphinx();
   /*   $view->assignRef('shouldReIndex', $shouldReIndex);
        $view->assignRef('id', $id);
        $view->assignRef('oQuote', $oItem);
        $view->assignRef('can_publish', $this->can_publish);
        $view->assignRef('quotes_cited', $quotes_cited); */

        $view->shouldReIndex=&$shouldReIndex;
        $view->id=&$id;
        $view->oQuote=&$oItem;
        $view->can_publish=&$this->can_publish;
        $view->quotes_cited=&$quotes_cited;

        $view->lookup();
    }

    function build()
    {
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        $model =& $this->getModel('Quotes');
        $inprogress = $model->isBuildInProgress();

        if (($inprogress === false) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            $build = (isset($_POST['build'])) ? $_POST['build'] : 'fail';
            if (
                $build === "full"
            ) {
                $cmds = array();
                $log_file = "quotes.build." . date("Y-m-d.H.i.s", $_SERVER['REQUEST_TIME']) . ".log";
                $cmds[] = "/usr/local/bin/php /home/palpers/gp-uploads/bin/build.quotes.php >> /home/palpers/gp-uploads/logs/" . $log_file . " &";
                $cmd = implode("\n", $cmds);
                exec($cmd, $output);
                $msg = "Building FULL. Please wait a minute then, refresh the page till it tells you it has finished.";
            } else {
                $msg = "Whilst trying to rebuild, did you forget to tick the box?";
            }
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=build', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
            exit();
        }
        $view =& $this->getView('Quotes', 'html');
        //$view->assignRef('inprogress', $inprogress);
        $view->inprogress=&$inprogress;
        $view->build();
    }


    function search()
    {   //echo '777';die();
        $last_search_clicked_on = (isset($_COOKIE[$this->cookie_name_last_search_clicked]))
                                  ? $_COOKIE[$this->cookie_name_last_search_clicked] : false;

        $ignore = Joomla\CMS\Factory::getApplication()->getInput()->get('ignore');
        if ($last_search_clicked_on !== false && empty($ignore)) {
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
            echo '643';die();
            $link = JRoute::_("index.php?option=com_gpo&controller=quotes&task=search&back=1#gpo-row-" . $last_search_clicked_on, false);
            $this->setRedirect($link);
            $this->redirect();
        }


        if (isset($_COOKIE[$this->cookie_name_last_search]) && Joomla\CMS\Factory::getApplication()->getInput()->get('back') === '1') {
            $data = $_COOKIE[$this->cookie_name_last_search];
            if (!empty($data)) {
                $data = unserialize($data);
            }
            $_GET = $data;
        }

        $modelQuote =& $this->getModel('Quotes');

        $inprogress = $modelQuote->isBuildInProgress();

        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), a build is in progress.");
        }

        $inprogress = $modelQuote->isReIndexInProgress();

        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), quotes are being reindexed.");
        }

        $shouldReIndex = $modelQuote->shouldReIndexForSphinx();

        $model =& $this->getModel('QuotesSearch');

        //$model->limit = 300;
        $model->backEnd();

        $view =& $this->getView('Quotes', 'html');

   /*   $view->assign('logged_in', $this->logged_in);
        $view->assignRef('rows', $model->results);
        $view->assignRef('pagination', $model->pagination);
        $view->assignRef('sphinxQuery', $model->sphinxQuery);
        $view->assignRef('can_publish', $this->can_publish);
        $view->assign('isSuperAdmin', $this->isSuperAdmin);
        $view->assign('isAdministrator', $this->isAdministrator);
        $view->assignRef('oUser', $this->oUser);
        $view->assignRef('shouldReIndex', $shouldReIndex); */

        $view->logged_in = $this->logged_in;
        $view->rows = &$model->results;
        $view->pagination = &$model->pagination;
        $view->sphinxQuery = &$model->sphinxQuery;
        $view->can_publish = &$this->can_publish;
        $view->isSuperAdmin = $this->isSuperAdmin;
        $view->isAdministrator = $this->isAdministrator;
        $view->oUser = &$this->oUser;
        $view->shouldReIndex = &$shouldReIndex;

        //if (!empty($last_search_clicked_on)) $view->assignRef('last_search_clicked_on', $last_search_clicked_on);

        if (!empty($last_search_clicked_on)) $view->last_search_clicked_on = &$last_search_clicked_on;

        if ($_GET['revise'] === '1') {
            if (isset($_GET['quotes']['locations']) && !empty($_GET['quotes']['locations'])) {
                $locations = explode(",", $_GET['quotes']['locations']);
            } else {
                $locations = null;
            }

            $oQuote = (object)$_GET['quotes'];
            $oQuote->published_range = (object)$oQuote->published_range;
            $oQuote->locations = $locations;
           /* $view->assignRef('oQuote', $oQuote);
            $view->assignRef('locations', $locations);*/

            $view->oQuote = &$oQuote;
            $view->locations = &$locations;

         /*   $view->assignRef('oQuote', $oQuote);
            $view->assignRef('locations', $locations); */

            $view->oQuote = &$oQuote;
            $view->locations = &$locations;


            $view->search();
        } else if (empty($model->results)) {
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

          //$view->assignRef('totalFound', $model->total);
            $view->totalFound = &$model->total;

            $view->searchResults();
        }

    }

    function poaimsearch()
    {
        //ini_set('display_errors', true);
        //error_reporting(E_ERROR OR E_PARSE OR E_WARNING);

        $search_params = Joomla\CMS\Factory::getApplication()->getInput()->get('quotes', null, 'ROW');
        $view =& $this->getView('Quotes', 'html');
    //    $view->assign('poaim', 1);
        $view->poaim = 1;
//print_r($search_params); die();
        if (!empty($search_params)) {
            $model =& $this->getModel('Quotes');


            $results = $model->quotesSearch($search_params);

            if ($results) {
                //$view->assign('logged_in', $this->logged_in);
               /* $view->assignRef('rows', $results);
                $view->assignRef('pagination', $model->pagination);
                $view->assignRef('can_publish', $this->can_publish);
                $view->assignRef('oUser', $this->oUser);
                $view->assignRef('totalFound', $model->total);*/

                $view->rows = &$results;
                $view->pagination = &$model->pagination;
                $view->can_publish = &$this->can_publish;
                $view->oUser = &$this->oUser;
                $view->totalFound = &$model->total;
                //$view =& $this->setView('Quotes', 'html');
                $view->searchResults();

                return;
            } else {
                $search_params = (object) $search_params;
                /*$view->assignRef('oQuote', $search_params);
                $view->assign('noresult',true);*/

                $view->oQuote = $search_params;
                $view->noresult = true;

                //$view =& $this->setView('Quotes', 'html');
                $view->searchPoaim();
                return;
            }
        }


        $view->searchPoaim();

    }


    function reindex()
    {
        //check they have permission
        if ($this->can_publish === false) {
            $msg = 'At present, your access level does not allow you to publish.';
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }

        $modelQuote =& $this->getModel('Quotes');
        $inprogress = $modelQuote->isBuildInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Quotes are currently being built.");
        }

        $inprogress = $modelQuote->isReIndexInProgress();
        if ($inprogress) {
            die("Please wait a moment and try again ( by hitting F5 ), Quotes are currently being updated( Reindexing of Sphinx ).");
        }

        if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            $go = (isset($_POST['reindex'])) ? true : false;
            $force = (isset($_POST['force'])) ? true : false;
            if ($go) {
                if ($force) {
                    //Needs to have index rebuilt
                    $modelQuote->setReIndex();
                }
                //$cmd = "/usr/sbin/sphinx-gpo rqa &";
                $cmd = $this->reindexCommand . " &";
                exec($cmd, $output);

                $msg = "Update of index is running in background, It will take few minutes to complete.";
                $link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
                exit();
            }
        }
        $view =& $this->getView('Quotes', 'html');
        $shouldReIndex = $modelQuote->shouldReIndexForSphinx();
        //$view->assignRef('shouldReIndex', $shouldReIndex);
        $view->shouldReIndex = &$shouldReIndex;
        $view->reindex();
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
window.location="' . JRoute::_("index.php?option=com_gpo&controller=quotes&task=lookup&id=" . $id, false) . '";
//]]>
</script>
		';
        exit();
    }


    function test()
    {
        $link = JRoute::_("index.php", false);
        $this->setRedirect($link);
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

        /* only admins can access this feature */
        if ($this->isAdministrator !== true) {
            $msg = 'At present, your access level does not allow you to access the FRT tool.';
            $link = JRoute::_('index.php?option=com_gpo&controller=quotes', false);
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
        $task = 'frt';
        $tableName = 'gpo_quotes'; //seach in the quotes table

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'
                && $_POST['action'] === 'add'
        ) {
            //$tableName = Joomla\CMS\Factory::getApplication()->getInput()->get('table', false);
            $tableName = 'gpo_quotes'; //seach in the quotes table
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
                $link = JRoute::_('index.php?option=com_gpo&controller=quotes&task=frt', false);
                $this->setRedirect($link, $msg);
                $this->redirect();
            }

            $frtModel =& $this->getModel('Findreplacecommon', '', $_POST['swap']);
            $quotesModel =& $this->getModel('Quotes');
            $searchResult = $frtModel->frtPerformSearch();
            $replacedResult = $frtModel->frtSearchReplace($searchResult, $_POST['swap']);
			if( !in_array($frtModel->columnName, array('keywords','poaim','affiliation','staff')) ){
                $searchResultQcites = $frtModel->frtQcitesFinding();
            }
            //$searchResultQcites = $frtModel->frtQcitesFinding();
            $frtModel->frtInsertLastSearch($_POST['swap'], $this->oUser->username);

            /* view results */
            $view =& $this->getView('Quotes', 'html');
            /*$view->assignRef('total', $frtModel->total);
            $view->assignRef('options', $_POST['swap']);
            $view->assignRef('items', $searchResult);
            $view->assignRef('qcites_items', $searchResultQcites);
            $view->assignRef('replacedItems', $replacedResult);*/

            $view->total = &$frtModel->total;
            $view->options = &$_POST['swap'];
            $view->items = &$searchResult;
            $view->qcites_items = &$searchResultQcites;
            $view->replacedItems = &$replacedResult;


            //pagination
           /* $view->assignRef('pagination', $frtModel->pagination);
            //$view->assignRef('rows', count($searchResult));
            $view->assignRef('filter_order', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id'));
            $view->assignRef('filter_order_dir', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc'));
            //$view->assignRef('total', $frtModel->total);
            $view->assignRef('quotesModel', $quotesModel);

            $view->assign('action', $action);
            $view->assign('task', $task);*/


            $view->pagination = &$frtModel->pagination;
            $view->filter_order = &Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
            $view->filter_order_dir = &Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc');
            $view->quotesModel = &$quotesModel;
            $view->action = $action;
            $view->task = $task;

            $view->frt_results();
            return true;
        }

        $action = empty($action) ? 'add' : $action; //by default it will show search form & will search

        $options['swap']['table_name'] = $tableName;
        $frtModel =& $this->getModel('Findreplacecommon', '', $options['swap']);
        $lastSearchedQuery = $frtModel->frtGetLastSearchedQuery($tableName);

        $from = Joomla\CMS\Factory::getApplication()->getInput()->get('from');
        $view =& $this->getView('Quotes', 'html');
      /*  $view->assign('action', $action);
        $view->assign('task', $task);
        $view->assignRef('lastSearchedQuery', $lastSearchedQuery);
        $view->assignRef('filter_order', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id'));
        $view->assignRef('filter_order_dir', Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc'));*/

        $view->action = $action;
        $view->task = $task;
        $view->lastSearchedQuery = $lastSearchedQuery;
        $view->filter_order = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', 'id');
        $view->filter_order_dir = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_dir', 'desc');

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

        $responseLink = JRoute::_('index.php?option=com_gpo&controller=quotes&task=frt&action=history', false);
        $this->setRedirect($responseLink, $responseMsg);
        $this->redirect();

        return false;
    }


    /*
      * Shows the past history of find & replace
      */
    function frt_history()
    {
        $task = Joomla\CMS\Factory::getApplication()->getInput()->get('task', false);
        $action = Joomla\CMS\Factory::getApplication()->getInput()->get('action', false);
        $table_name = 'gpo_quotes';
        $frtModel =& $this->getModel('Findreplacecommon', array());
        $items = $frtModel->frtGetHistory($table_name);
        $lastSearchedQuery = $frtModel->frtGetLastSearchedQuery($table_name);

        $view =& $this->getView('Quotes', 'html');
       /* $view->assign('action', $action);
        $view->assign('task', $task);
        $view->assignRef('items', $items);
        $view->assignRef('lastSearchedQuery', $lastSearchedQuery);*/

        $view->action = $action;
        $view->task = $task;
        $view->items = &$items;
        $view->lastSearchedQuery = &$lastSearchedQuery;

        $view->frt_history();
    }


}

//end class GpoControllerQuotes
?>
