<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<script>
function customDeletFun(){
  if (document.adminForm.toggle.checked==true){
    if(confirm('Do you really want to delete all Data Page footers?')==true){
      Joomla.submitbutton('remove');
    }else{
      
    }
  }else{
    if (document.adminForm.boxchecked.value==0){
      alert('Please first make a selection from the list');
      return false;
    }else{ 
      Joomla.submitbutton('remove');
    }
  }
  }
</script>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<?php include_once('submenus_startblock.php'); ?>
<?php if( $this->total < 1): ?>
<p>There are no FOOTERS that have been created for any pages. Please create one</p>

<?php else: ?>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th><?php echo JText::_( 'Url' ); ?></th>
			<th><?php echo JText::_( 'Publish' ); ?></th>
			<th><?php echo JText::_( 'Authors' ); ?></th>
			<th><?php echo JText::_( 'Comment' ); ?></th>
			<th><?php echo JText::_( 'Created At' ); ?></th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" id="check_all" />
			</th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$link = JRoute::_( 'index.php?option=com_gpo&controller=dpfooters&task=edit&id='. $row->id );
	$checked 	= JHTML::_('grid.id',   $i, $row->id );
?>
		<tr>
			<td><a href="<?php echo $link;?>" title="Edit"><?php echo $row->url; ?></a></td>
			<td>
			<a href="<?php echo $link;?>" title="Edit">
			<?php 
			if($row->is_published == 1):
			   echo 'Yes';
			else:
			   echo 'No';
			endif;      
			?>
			</a>
			</td>
			<td><a href="<?php echo $link;?>" title="Edit"><?php echo $row->footer_credit; ?></a></td>
			<td><a href="<?php echo $link;?>" title="Edit"><?php echo $row->comment; ?></a></td>
			<td><a href="<?php echo $link;?>" title="Edit"><?php echo date( 'Y-M-d', strtotime($row->created_at) ); ?></a></td>
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
<input type="hidden" name="controller" value="dpfooters" />
<?php include_once('submenus_startblock.php'); ?>
</form>
