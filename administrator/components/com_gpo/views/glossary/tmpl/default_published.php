<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');



$document = &JFactory::getDocument();
        $document->addScript( JURI::root(true).'/media/system/js/mootools-core.js');
        $document->addScript( JURI::root(true).'/media/system/js/core-uncompressed.js');

         // $document->addScript( JURI::root(true).'/includes/js/joomla.javascript.js');
?>
<script type="text/javascript">
function confirmDel(url){
    if(confirm('Are you sure to delete this glossary item?')){
        document.location = url;
    } else {
        return false;
    }
}
</script>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="published" />
<input type="hidden" name="controller" value="glossary" />
<input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir; ?>" />
<?php include_once('submenus_startblock.php'); ?>

<?php  if( count( $this->glossaries ) < 1): ?>
<p>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=glossary&task=create' ); ?>">There are no published glossary. Click here to create one</a>
</p>
<?php else: ?>

<style>
.adminlist th{
	vertical-align:top;
	text-align:left;
}
#search_id{ width: 60px; }
#search_ext_id{ width: 60px; }
#search_title{ width: 250px; }

.gpo-row-citation td{
	vertical-align:top;
}
.gpo-row-citation td.id{ width:20px; text-align:center }
.gpo-row-citation td.modified{ width:80px; }
.gpo-row-citation td.source{ width:220px; }
.gpo-row-citation td.subtitle{ width:350px; }
.gpo-row-citation td.access,
.gpo-row-citation td.action{ width:30px; text-align:center; }
</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th>
				<?php echo JHTML::_('grid.sort',   'ID', 'id', $this->filter_order_Dir, $this->filter_order ); ?><br />
        <input type="text" name="search_id" id="search_id" value="" class="text_area" onchange="document.adminForm.submit();" /> 
			</th>
      <th><?php echo JHTML::_('grid.sort',   'Title', 'title', $this->filter_order_Dir, $this->filter_order ); ?><br />
      <input type="text" name="search_title" id="search_title" value="" class="text_area" onchange="document.adminForm.submit();" /> 
      </th>
			<th><?php echo JHTML::_('grid.sort',   'Sub Title', 'subtitle', $this->filter_order_Dir, $this->filter_order ); ?></th>
			<th><?php echo JHTML::_('grid.sort',   'Modified', 'modified', $this->filter_order_Dir, $this->filter_order ); ?></th>
			<th><?php echo JText::_( 'Source' ); ?></th>

			<th><?php echo JHTML::_('grid.sort',   'Access', 'share', $this->filter_order_Dir, $this->filter_order ); ?></th>
			<th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
	if( count( $this->glossaries ) < 1)
	{

?>
<tr>
	<td colspan="6"><p>Sorry, no match. Please try another search.</p></td>
</tr>

<?php
	} else {
	foreach( $this->glossaries AS $row ):
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=glossary&task=edit&id='.$row['id']);
	$link_delete = JRoute::_( 'index.php?option=com_gpo&controller=glossary&task=delete&id='.$row['id']);
	$access = ( (int)$row['share'] == (int) '1' ) ? 'Public' : '<span style="color:#ff0000;">Members</span>';
	$published = '&nbsp;';
?>
		<tr class="gpo-row-citation">
			<td class="id"><a href="<?php echo $link_edit;?>" title="Edit this glossary"><?php echo $row['id']; ?></a></td>
			<td class="title"><a title="Edit this glossary" href="<?php echo $link_edit;?>"><?php echo $row['title']; ?></a></td>
            <td class="subtitle"><?php echo $row['subtitle']; ?></td>
			<td class="modified"><?php echo date("j M Y", strtotime( $row['modified'] ) ); ?></td>
			<td class="source"><?php echo $row['websource']; ?></td>

			<td class="access">
				<?php echo $access; ?>
			</td>
			<td class="action">
			    <a href="#" onClick="confirmDel('<?php echo $link_delete;?>');"  title="Delete this glossary">Delete</a>
			</td>
		</tr>
<?php
	endforeach;
	}
?>
	</tbody>
<?php if( count( $this->glossaries ) > 0 ): ?>
	<tfoot>
	<tr>
		<td colspan="7">
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
