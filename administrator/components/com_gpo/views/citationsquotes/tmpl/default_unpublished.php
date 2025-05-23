<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');

?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="unpublished" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<?php include_once('submenus_startblock.php'); ?>

    <?php if( !isset( $this->rows ) || count( $this->rows ) < 1): ?>
<p>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=create' ); ?>">There are no unpublished Quote Citations in the queue. Click here to create one</a>
</p>

<?php else: ?>

<style>
.adminlist th{
	vertical-align:top;
}
.search_id{ width: 50px; }
.search_ext_id{ width: 50px; }

.gpo-row-citation td{ 
	vertical-align:top; 
}
.gpo-row-citation td.published{ width:80px; }
.gpo-row-citation td.action{ width:80px; text-align:center; }

.adminlist a.ap_new{ color:#ff0000; }
.adminlist a.ap_exists{ color:#4CC417; }
</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th class="search_id"><?php echo JText::_( 'QCite ID' ); ?></th>
			<th class="search_ext_id"><?php echo JText::_( 'Quote ID' ); ?></th>
			<th><?php echo JText::_( 'Last Modified' ); ?></th>			
			<th><?php echo JText::_( 'Author' ); ?></th>
			<th><?php echo JText::_( 'Title' ); ?></th>			
			<th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
    $link_lookup = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=quotes&task=lookup&state=unpublished&id='. $row['id'] );	
	$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=edit&id='. $row['id'] );	
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
	
		$href_publish = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=publish&id='. $row['id'] );
		$publish_link = '<a class="' . $awaiting . '" href="' . $href_publish . '" title="Approve and publish this Citation">Publish</a> |';
	}else{
		if( $row['ap_id'] === null )
		{
			$href_publish = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=publish&id='. $row['id'] );			
			$publish_link = '<a href="' . $href_publish . '" title="Approve this Citation to the Publish Queue">Publish</a> |';
		}else{
			$publish_link = '<span>Queued</span> |';
		}		
	}
	$href_delete = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=unpublished_delete&id='. $row['id'] );
?>
		<tr class="gpo-row-citation">
			<td><a style="color:green" href="<?php echo $link_lookup;?>" title="Open this QCite record in Lookup view"><?php echo ( !empty( $row['live_id'] ) ) ? $row['live_id'] : 'n/a'; ?></a></td>
			<td><?php echo $row['ext_id']; ?></td>
			<td class="published"><?php echo date("j M Y", strtotime( $row['modified'] ) ); ?></td>
			<td><?php echo $row['author']; ?></td>
			<td class="title"><a href="<?php echo $href_edit;?>" title="Edit this QCite"><?php echo $row['title']; ?></a></td>						
			<td class="action">
				<?php echo $publish_link; ?>				
				<a href="<?php echo $href_delete;?>" title="Delete">Delete</a>
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
	<!-- </tbody>  -->
	</table>
</div>
<?php endif; ?>
<?php include_once('submenus_endblock.php'); ?>
</form>
