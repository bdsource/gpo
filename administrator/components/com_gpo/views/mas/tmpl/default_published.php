<?php
   defined( '_JEXEC' ) or die( 'Restricted Access' );
   $document = &JFactory::getDocument();
   //require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
   $front_end = str_replace( "administrator",'',JURI::base(true));  
   $is_post = Joomla\CMS\Factory::getApplication()->getInput()->getMethod();
   
// Load the tooltip behavior.
  // JHtml::_('behavior.tooltip');
   JHtml::_('behavior.multiselect');
  // JHtml::_('behavior.modal');
   
   $document->addScript( JURI::root(true).'/media/system/js/mootools-core-uncompressed.js');
   $document->addScript( JURI::root(true).'/media/system/js/core-uncompressed.js');
   $document->addScript( JURI::root(true).'/media/system/js/modal-uncompressed.js');
   $document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js'); 
 
?>


<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
    <input type="hidden" name="controller" value="mas" />
    <input type="hidden" name="option" value="com_gpo" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="task" value="published" />
    <input type="hidden" name="filter_order" value="<?php echo $this->filter_order_published; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir_published; ?>"/>
		
<?php include_once('submenus_startblock.php'); ?>               
<?php if( count( $this->rows ) < 1 && ( strtolower( $is_post ) === 'post' ) ){?>
    
<p>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=mas&task=create' ); ?>">There are no published Mas in the queue. Click here to create one.</a>
</p>

<?php } else {
$state = Joomla\CMS\Factory::getApplication()->getInput()->get('state','');
if(empty($state)){
    $state = (Joomla\CMS\Factory::getApplication()->getInput()->get('task','') == 'unpublished') ? 'unpublished' : 'published';
}    
?>
<style>
    .adminlist th{
	vertical-align:top;
      }
    #search_id{ width: 60px; }
    #search_ext_id{ width: 60px; }
    .gpo-row td {vertical-align:top;}
    .gpo-row-citation td {vertical-align:top;}
    .gpo-row td.id {width:15px;}
    .gpo-row td.published {width:80px; text-align:center;}
    .gpo-row td.gpnheader {text-align:left;}
    .gpo-row td.access {width:30px; text-align:center;}
    .gpo-row td.action {width:80px; text-align:center;}
    table.adminlist {width: 100%; text-align: center;}
</style>

<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
                      <th style="width:13%;">  <?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->filter_order_Dir_published, $this->filter_order_published ); ?>
                      <input type="text" name="search_id" id="search_id" value="" class="text_area" onchange="document.adminForm.submit();"/></th>	
		      <th style="width:13%"><?php echo JHTML::_('grid.sort', JText::_('Date'), 'date_of_shooting', $this->filter_order_Dir_published ,$this->filter_order_published);?></th>
                      <th style="width:13%"><?php echo "<p style='color:#0088cc'>".JText::_('Country')."</p>"; ?></th>
		      <th style="width: 35%"><?php echo JHTML::_('grid.sort', JText::_('Venue'), 'primary_venue', $this->filter_order_Dir_published ,$this->filter_order_published);?></th>
                      <th style="width: 13%"><?php echo JHTML::_('grid.sort', JText::_('Perpetrator'), 'primary_perpetrator_name', $this->filter_order_Dir_published ,$this->filter_order_published);?></th>
		      <th style="width: 13%"><?php echo JText::_( 'Action' );?></th>
		</tr>
	</thead>
    
        <tbody>    
       
<?php foreach( $this->rows as $row ):?>
<?php
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=edit&live_id='. $row['id'],false );
	$link_citation = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=createcitation&id='. $row['id'],false );
        
	if( $this->can_publish )		
	{
		$href = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=published_delete&id='.$row['id'],false );			
		$a_delete ='<a href="' . $href . '"  title="Permanently delete this MAS Item from the database">Delete</a>';
	}else{
		$a_delete ='';
	}
	$access = ( (int)$row['share'] == (int)'1' ) ? 'Public' : '<span style="color:#ff0000;">Members</span>';
        
    if('published'==$state){
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=mas&task=lookup&state=published&id='.$row['id']);
    } else {
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=mas&task=lookup&state=unpublishedid='.$row['id']);
    }
    
?>       
            
		<tr class="gpo-row">
			<td class="id"><a style="color:green" href="<?php echo $link_lookup;?>"  title="Open this MAS item in Lookup view"><?php echo $row['id']; ?></a></td>            
			<td class="date_of_shooting"><?php 
                            $date = $row['date_of_shooting'];
                            echo date(("j M Y"),strtotime($date));   
                        ?></td> 
                        <td class="primary_venue"> <?php echo $row['name']; ?></a></td>
			<td class="primary_venue"><a href="<?php echo $link_edit;?>"  title="Edit this MAS Item"><?php echo $row['primary_venue']; ?></a></td>
                        <td class="primary_perpetrator_name"><?php echo $row['primary_perpetrator_name']; ?></td>  
			<td class="action">
				<a href=""></a> <?php echo $a_delete; ?>
			</td>
		</tr>
<?php endforeach;?>
	</tbody>
	<tfoot>
	   <tr>
		<td colspan="6" style="padding-top:20px;">
		    <?php echo $this->pagination->getListFooter(); ?>
		</td>
	  </tr>
	</tfoot>
	</table>
    </div>
    <?php
            if (count($this->rows)===1 && ($this->rows)===false)
               {
                   echo "<span style=\"color: blue; margin-left: 400px;\">Please try another search. The item couldn't be found</span>";
            }      
    ?>     
  
   <?php } ?>
  <?php include_once('submenus_endblock.php'); ?>
</form>