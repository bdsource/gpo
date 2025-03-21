<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<?php include_once('submenus_startblock.php'); ?>
<?php if( $this->total < 1): ?>
<p>There is no spiderbait. Please create one</p>

<?php else: ?>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th><?php echo JText::_( 'Url' ); ?></th>
			<th><?php echo JText::_( 'Text' ); ?></th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
			</th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$link = JRoute::_( 'index.php?option=com_gpo&controller=spiderbait&task=edit&id='. $row->id );
	$checked 	= JHTML::_('grid.id',   $i, $row->id );
?>
		<tr>
			<td><a href="<?php echo $link;?>" title="Edit"><?php echo $row->url; ?></a></td>
			<td><a href="<?php echo $link;?>" title="Edit"><?php echo $row->text; ?></a></td>
			<td>
				<?php echo $checked; ?>
			</td>
		</tr>
<?php endforeach;?>
	</tbody>
	</table>
</div>
<?php endif; ?>
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="spiderbait" />
<?php include_once('submenus_startblock.php'); ?>
</form>
