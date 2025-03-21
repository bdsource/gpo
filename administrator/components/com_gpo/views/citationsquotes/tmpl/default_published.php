<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
$front_end = str_replace( "administrator",'',JURI::base(true));
$is_post = Joomla\CMS\Factory::getApplication()->getInput()->getMethod();
$filter_order =	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '');
$filter_order_Dir = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '');

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/mootools-core.js');
$document->addScript( JURI::root(true).'/media/system/js/core-uncompressed.js'); 
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="published" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
<?php include_once('submenus_startblock.php'); ?>

<?php if(( strtolower( $is_post ) !== 'post' )  && count( $this->rows ) < 1): ?>
<p>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=create' ); ?>">There are no published Quote Citations in the queue. Click here to create one</a>
</p>
<?php else:?>

<style>
.adminlist th{
	vertical-align:top;
}
#search_id{ width: 60px; }
#search_ext_id{ width: 60px; 
}

.gpo-row-citation td{ 
	vertical-align:top; 
    }
.gpo-row-citation td.id{ width:15px; }
.gpo-row-citation td.published{ width:80px; }
.gpo-row-citation td.action{ width:30px; text-align:center; }
</style>

<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th style="max-width:60px">
				<?php echo JHTML::_('grid.sort',   'QCite ID', 'id', $filter_order_Dir, $filter_order ); ?><br />
				<input type="text" name="search_id" id="search_id" value="" class="text_area" onchange="document.adminForm.submit();" />
			</th>
			<th style="max-width:60px">
				<?php echo JText::_( 'Quote ID' ); ?><br />
				<input type="text" name="search_ext_id" id="search_ext_id" value="" class="text_area" onchange="document.adminForm.submit();" />
			</th>
			<th style="min-width:80px"><?php echo JHTML::_('grid.sort', 'Published', 'published', $filter_order_Dir, $filter_order ); ?></th>
			<th style="max-width:280px">
				<?php echo JText::_( 'Author' ); ?><br />
				<?php echo $this->model->filterAuthor(); ?>
			</th>
			
                        <th style="min-width:420px" ><?php echo JText::_( 'Title' ); ?></th>
			<th>
				<?php echo JText::_( 'Action' ); ?><br />
<!--
				<button onclick="document.adminForm.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search_id').value='';document.getElementById('search_ext_id').value='';document.getElementById('author').value='';document.adminForm.submit();"><?php echo JText::_( 'Reset' ); ?></button>
-->
			</th>
		</tr>
	</thead>
	<tbody>
<?php 
	if( ( strtolower( $is_post ) === 'post' )  && count( $this->rows ) < 1)
	{

?>
<tr>
	<td colspan="5"><p>Sorry, no match. Please try another search.</p></td>
</tr>

<?php
	}else{
	foreach( $this->rows as $row ):
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=edit&type=' . $this->type . '&live_id='. $row['id'] );
	$link_delete = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=published_delete&type=' . $this->type . '&id='. $row['id'] );	
        $link_lookup = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=quotes&task=lookup&state=published&live_id='. $row['id'] );	
	$published = '&nbsp;';
?>

		        <tr class="gpo-row-citation">
			<td><a style="color:green" href="<?php echo $link_lookup;?>" title="Open this QCite record in Lookup view"><?php echo $row['id']; ?></a></td>
			<td><?php echo $row['ext_id']; ?></td>
			<td class="published"><?php echo date("j M Y", strtotime( $row['published'] ) ); ?></td>
			<td style="max-width:280px"><?php echo $row['author']; ?></td>
			<td class="title"><a href="<?php echo $link_edit;?>" title="Edit this QCite"><?php echo $row['title']; ?></a></td>			
			<td class="action">
				<?php if( $this->can_publish ): ?>
					<a href="<?php echo $link_delete;?>"  title="Delete this citation">Delete</a>
				<?php else: ?>
					&nbsp;
				<?php endif; ?>
			</td>
			<!-- <td><a href="<?php echo $link_view;?>" target="_blank" title="View what public / members can see">View</a></td> -->
		</tr>
<?php endforeach;
	}?>

	</tbody>

<?php if( count( $this->rows ) > 0 ): ?>	
	<tfoot>
	<tr>
		<td colspan="6" style="padding-top:20px;">
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
<!--<script type="text/javascript"> 
//<![CDATA[
$('search_id').observe("keydown",function(event){		
	if( event.keyCode === Event.KEY_RETURN )
	{
		Event.stop(event);
		$('adminForm').submit();
	}
});
$('search_ext_id').observe("keydown",function(event){		
	if( event.keyCode === Event.KEY_RETURN )
	{
		Event.stop(event);
		$('adminForm').submit();
	}
});
//]]> 
</script>-->