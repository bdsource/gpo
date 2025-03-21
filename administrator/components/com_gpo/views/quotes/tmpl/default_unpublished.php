<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

//$is_post = JRequest::getMethod();
$is_post =  Joomla\CMS\Factory::getApplication()->getInput()->getMethod();
$filter_order	=	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '');
$filter_order_Dir	=	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '');

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');

?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="unpublished" />
<input type="hidden" name="controller" value="quotes" />
    <input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	<?php include_once('submenus_startblock.php'); ?>

<?php if( !isset( $this->rows ) || count( $this->rows ) < 1) { ?>

<p>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=create' ); ?>">There are no unpublished Quotes in the queue. Click here to create one</a>
</p>

<?php  } else {
    $state = Joomla\CMS\Factory::getApplication()->getInput()->get('state','');
    if(empty($state)){
        $state = (Joomla\CMS\Factory::getApplication()->getInput()->get('task','') == 'unpublished') ? 'unpublished' : 'published';
    }
    ?>
<style>
.gpo-row td{ 
	vertical-align:top; 
}
.gpo-row td.id{ width:25px; }
.gpo-row td.author{ }
.gpo-row td.modified{ width:110px; }
.gpo-row td.action{ width:80px; text-align:center; }
.gpo-row td.staff{text-align:center; }

.adminlist a.ap_new{ color:#ff0000; }
.adminlist a.ap_exists{ color:#4CC417; }
.adminlist {
text-align: center;
width: 100%;
}
</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->filter_order_Dir ,$this->filter_order );//echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->filter_order_Dir ,$this->filter_order);?></th>
			<th><?php echo JHTML::_('grid.sort', JText::_('Author'), 'author', $this->filter_order_Dir ,$this->filter_order);?></th>
			<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'title', $this->filter_order_Dir ,$this->filter_order);?></th>
			<th><?php echo JHTML::_('grid.sort', JText::_('Last Modified'), 'modified', $this->filter_order_Dir ,$this->filter_order);?></th>
			<th><?php echo JHTML::_('grid.sort', JText::_('Staff'), 'staff', $this->filter_order_Dir ,$this->filter_order);?></th>
			<th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=edit&id='. $row['id'] );
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
		
		$href_publish = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=publish&id='. $row['id'] );		
		$publish_link = '<a class="' . $awaiting . '" href="' . $href_publish . '" title="Approve and publish this Quote">Publish</a> |';
	}else{
		if( $row['ap_id'] === null )
		{
			$href_publish = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=publish&id='. $row['id'] );		
			$publish_link = '<a href="' . $href_publish . '" title="Approve this Quote to the Publish Queue">Publish</a> |';
		}else{
			$publish_link = '<span>Queued</span> |';
		}
	}
	$href_delete = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=unpublished_delete&id='. $row['id'] );

    if('published'==$state){
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=quotes&task=lookup&state=published&live_id='.$row['id']);
    } else {
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=quotes&task=lookup&state=unpublished&id='.$row['id']);
    }

?>
		<tr class="gpo-row">
			<td class="id"><a style="color:green" href="<?php echo $link_lookup;?>" title="Open this Quote in Lookup view"><?php echo $row['id']; ?></a></td>
			<td class="author"><?php echo $row['author']; ?></td>			
			<td class="title"><a href="<?php echo $href_edit;?>" title="Edit this unpublished Quote"><?php echo $row['title']; ?></a></td>
			<td class="modified"><?php echo date("j M Y H:i:s", strtotime( $row['modified'] ) ); ?></td>
			<td class="staff"><?php echo $row['staff']; ?></td>
			<td class="action">
				<?php echo $publish_link; ?>
				<a href="<?php echo $href_delete;?>" title="Permanently delete this unpublished Quote from queue">Delete</a>
			</td>
		</tr>
<?php endforeach;?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="6">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
	</table>
</div>
<?php } ?>
<?php include_once('submenus_endblock.php'); ?>
</form>
