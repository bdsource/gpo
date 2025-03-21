<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/mootools-core.js');
$document->addScript( JURI::root(true).'/media/system/js/core-uncompressed.js');

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');
?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="published" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir; ?>" />

<?php include_once('submenus_startblock.php'); ?>

<?php if( ( strtolower( $this->is_post ) !== 'post' )  && count( $this->rows ) < 1): ?>
<p>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=create' ); ?>">There are no published Quote Citations in the queue. Click here to create one</a>
</p>
<?php else: ?>

<style>
.adminlist th{
	vertical-align:top;
	text-align:left;
}
#search_id{ width: 40px; }
#search_ext_id{ width: 40px; }
#search_title{ width: 250px; }

.gpo-row-citation td{ 
	vertical-align:top; 
}
#nciteid{ width:20px;}
#newsid{ width: 20px;}
.gpo-row-citation td.published{ width:80px; }
.gpo-row-citation td.source{ }
.gpo-row-citation td.access,
.gpo-row-citation td.action{ width:30px; text-align:center; }
</style>
<div class="responsive">
<table class="table-striped table-hover">
	<thead>
		<tr class="info">
			<th id="nciteid">
				<?php echo JHTML::_('grid.sort',   'NCite ID', 'id', $this->filter_order_Dir, $this->filter_order ); ?><br />
					<input type="text" name="search_id" id="search_id" value="" class="text_area" onchange="document.adminForm.submit();" />
			</th>
			<th id="newsid">
				<?php echo JText::_( 'News ID' ); ?><br />
					<input type="text" name="search_ext_id" id="search_ext_id" value="" class="text_area" onchange="document.adminForm.submit();" />
			</th>
			<th><?php echo JHTML::_('grid.sort',   'Published', 'published', $this->filter_order_Dir, $this->filter_order ); ?></th>
			<th><?php echo JText::_( 'Source' ); ?></th>			
			<th><?php echo JText::_( 'Title' ); ?><br />
					<input type="text" name="search_title" id="search_title" value="" class="text_area" onchange="document.adminForm.submit();" />
			</th>			
			<th>Access</th>
			<th><?php echo JText::_( 'Action' ); ?><br />
			</th>
		</tr>
	</thead>
	<tbody>
<?php 
	if( ( strtolower( $this->is_post ) === 'post' )  && count( $this->rows ) < 1)
	{

?>
<tr>
	<td colspan="6"><p>Sorry, no match. Please try another search.</p></td>
</tr>

<?php
	}else{
	foreach( $this->rows as $row ):
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=edit&type=' . $this->type . '&live_id='. $row['id'] );
	$link_delete = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=published_delete&type=' . $this->type . '&id='. $row['id'] );	
    $link_lookup = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=lookup&state=published&live_id='. $row['id'] );	
	$access = ( (int)$row['share'] == (int)'1' ) ? 'Public' : '<span style="color:#ff0000;">Members</span>';
	$published = '&nbsp;';
?>
		<tr class="gpo-row-citation">
			<td><a style="color:green" href="<?php echo $link_lookup;?>" title="Open this NCite item in Lookup View"><?php echo $row['id']; ?></a></td>			
			<td><?php echo $row['ext_id']; ?></td>
			<td class="published"><?php echo date("j M Y", strtotime( $row['published'] ) ); ?></td>
			<td class="source"><?php echo $row['source']; ?></td>
			<td class="title"><a href="<?php echo $link_edit;?>" title="Edit this NCite"><?php echo $row['title']; ?></a></td>			
			<td class="access">
				<?php echo $access; ?>
			</td>
			<td class="action">
				<?php if( $this->can_publish ): ?>
					<a href="<?php echo $link_delete;?>"  title="Delete this citation">Delete</a>
				<?php else: ?>
					&nbsp;
				<?php endif; ?>
			</td>
		</tr>
<?php 
	endforeach;
	}
?>
	</tbody>
<?php if( count( $this->rows ) > 0 ): ?>	
	<tfoot>
	<tr>
		<td colspan="7" style="padding-top:20px;">
<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
<?php endif; ?>
	</table>
</div>
<?php endif; ?>
<?php include_once('submenus_endblock.php'); ?>
</form>
