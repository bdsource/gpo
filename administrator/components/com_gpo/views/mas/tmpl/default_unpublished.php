<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );

?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="unpublished" />
<input type="hidden" name="controller" value="mas" />
<?php include_once('submenus_startblock.php'); ?>
<?php if( !isset( $this->rows ) || count( $this->rows ) < 1) { ?>

<p>	
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=mas&task=create'); ?>">There are no unpublished MAS articles in the queue. Click here to create one.</a>
</p>

<?php } else {

$state = Joomla\CMS\Factory::getApplication()->getInput()->get('state','');
if(empty($state)){
    $state = (Joomla\CMS\Factory::getApplication()->getInput()->get('task','') == 'unpublished') ? 'unpublished' : 'published';
}
?>
<style type="text/css">
.gpo-row td{ 
	vertical-align:top; 
}
.gpo-row td.id{ width:15px; }
.gpo-row td.published{ width:80px; text-align:center; }
.gpo-row td.action{ width:80px; text-align:center; }
.gpo-row td.gpnheader{text-align:center; }

.adminlist a.ap_new{ color:#ff0000; }
.adminlist a.ap_exists{ color:#4CC417; }

</style>
<script type="text/javascript">
    function sortTable(){
        var tl = new HtmlTable('mas_unpublished');
        tl.sort(index,reverse,prepare);
    }
</script>
<div class="responsive">
<table class="adminlist table-striped table-hover" id="mas_unpublished">
	<thead>
		<tr>
		        <th><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->filter_order_Dir ,$this->filter_order);?></th>
                        <th><?php echo JHTML::_('grid.sort', JText::_('Date'), 'date_of_shooting', $this->filter_order_Dir ,$this->filter_order);?></th>
			            <th><?php echo JHTML::_('grid.sort', JText::_('Venue'), 'primary_venue', $this->filter_order_Dir ,$this->filter_order);?></th>
                        <th><?php echo JHTML::_('grid.sort', JText::_('Perpetrator'), 'primary_perpetrator_name', $this->filter_order_Dir ,$this->filter_order);?></th>
                        <th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	   </thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=edit&id='. $row['id'] );
	if( $this->can_publish === true )
	{
		$classNames = array();
		
		if( $row['ap_id'] !== null )
		{
			if( $row['live_id'] !== "0" )
			{
				$classNames[] = "ap_exists";
			}else{
				$classNames[] = "ap_new";
			}
		}
		
		$awaiting = implode( " ", $classNames );
		
		$href_publish = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=publish&id='. $row['id'] );		
		$publish_link = '<a class="' . $awaiting . '" href="' . $href_publish . '" title="Approve and publish this MAS item">Publish</a> |';
	}else{
		if( $row['ap_id'] === null )
		{
			$href_publish = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=publish&id='. $row['id'] );		
			$publish_link = '<a href="' . $href_publish . '" title="Approve this MAS item to the Publish Queue">Publish</a> |';
		}else{
			$publish_link = '<span>Queued</span> |';
		}		
	}
	$href_delete = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=unpublished_delete&id='. $row['id'] );

    if('published'==$state){
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=mas&task=lookup&state=published&id='.$row['id']);
    } else {
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=mas&task=lookup&state=unpublished&id='.$row['id']);
    }
?>
		<tr class="gpo-row">
			<td class="id"><a style="color:green" href="<?php echo $link_lookup;?>" title="Open this MAS item in Lookup view"><?php echo $row['id']; ?></a></td>						
			<td class="date_of_shooting"><?php 
                            $date = $row['date_of_shooting'];
                            echo date(("j M Y"),strtotime($date));   
                        ?>
                        </td>
			<td class="title"><a href="<?php echo $href_edit;?>" title="Edit this unpublished MAS Item"><?php echo $row['primary_venue']; ?></a></td>
                        <td class="gpnheader"><?php echo $row['primary_perpetrator_name']; ?></td>
            <td class="action">
				<?php echo $publish_link; ?>
				<a href="<?php echo $href_delete;?>" title="Permanently delete this unpublished MAS Item from queue">Delete</a>
			</td>
		</tr>
<?php endforeach;?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="5">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>	
	</table>
</div>
<?php } ?>
    <input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir; ?>"/>
	<?php include_once('submenus_endblock.php'); ?>
</form>