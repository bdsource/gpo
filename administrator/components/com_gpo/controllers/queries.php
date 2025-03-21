<?php
defined('_JEXEC') or die();

class GpoControllerQueries extends GpoController
{
	function __construct()
	{
		parent::__construct();
		$this->oUser	= & JFactory::getUser();
        
        //Only Super Admin can access this 
        $this->can_access = $this->oUser->get('isRoot');
		if( !$this->can_access )
		{
			$msg = 'Access to Queries requires higher permissions.';
			$link = JRoute::_( 'index.php?option=com_gpo', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		$this->registerTask( '','checklist');		
	}
	
	
	function checklist()
	{
		$view =& $this->getView( 'Queries', 'html' );
		$view->checklist();
	}
        
        function publishdate_update_on_quotes_qcite_table()
	{       
                
		$view =& $this->getView( 'Queries', 'html' );
                $db =& JFactory::getDBO();
                
		$Quotes_query = "UPDATE #__gpo_quotes SET published = modified WHERE notes LIKE '%PopCite%' and modified > published";
	        $db->setQuery( $Quotes_query );
                $Quotes_results = $db->execute();
                $Qoutes_affectedRows = $db->getAffectedRows($Quotes_results);

                
                $Citation_query = "UPDATE #__gpo_citations_quotes SET published = modified WHERE notes LIKE '%PopCite%' and modified > published";
	        $db->setQuery( $Citation_query );
                $Citation_result = $db->execute();
                $Citation_affectedRows = $db->getAffectedRows($Citation_result);
                
		
                $finalresult['quotes_query'] =  $Quotes_query;
                $finalresult['quotes_affected_rows'] =  $Qoutes_affectedRows;
                $finalresult['citation_query'] =  $Citation_query;
                $finalresult['citation_affected_rows'] =  $Citation_affectedRows;
                
		
                
                // $view->assignRef( 'results', $finalresult );
                $view->results=&$finalresult;
                
		$view->dateupdate();
                
               
           
	}
    
}
?>