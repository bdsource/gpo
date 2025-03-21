<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
$front_end = str_replace( "administrator",'',JURI::base(true));
?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="citations" />
<?php include_once('submenus_startblock.php'); ?>

<?php if( count( $this->rows ) < 1): ?>
<p>At the moment there are no Citations to display. Please create one</p>
<?php else: ?>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th><?php echo JText::_( 'Id' ); ?></th>
			<th><?php echo JText::_( 'Title' ); ?></th>
			<th><?php echo JText::_( 'Access' ); ?></th>
			<th><?php echo JText::_( 'Published' ); ?></th>
			<th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=edit&type=' . $this->type . '&live_id='. $row['id'] );
	$link_delete = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=published_delete&type=' . $this->type . '&id='. $row['id'] );	
	$access = ( (int)$row['share'] == (int)'0' ) ? 'public' : 'members only';
	$published = '&nbsp;';
?>
		<tr>
			<td><a href="<?php echo $link_edit;?>"  title="Edit"><?php echo $row['id']; ?></a></td>
			<td><a href="<?php echo $link_edit;?>"  title="Edit"><?php echo $row['title']; ?></a></td>
			<td>
				<?php echo $access; ?>
			</td>
			<td>
					<a href="<?php echo $link_delete;?>"  title="Delete this citation">delete</a>
			</td>
			<!-- <td><a href="<?php echo $link_view;?>" target="_blank" title="View what public / members can see">View</a></td> -->
			<td>&nbsp;</td>
		</tr>
<?php endforeach;?>
	</tbody>
	</table>
</div>
<?php endif; ?>
<?php include_once('submenus_endblock.php'); ?>
</form>
