<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();
$url = JRoute::_( 'index.php?option=com_gpo&controller=topics&task=edit&id=' );
$front_end = str_replace( "administrator",'',JURI::base(true));

if( count( $this->all ) > 0 ):
?>
<style>
.gpo-row td{ 
	vertical-align:top; 
}
.gpo-row td.title{ width:30%; text-align:left; }
.gpo-row td.action{ width:40px; text-align:center; }
.gpo-row td.headline{ text-align:left; }
.gpo-row td.seo{text-align:left; }

</style>
<div class="responsive">
<?php include_once('submenus_startblock.php'); ?>
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th><?php echo JText::_( 'Title' ); ?></th>
			<th><?php echo JText::_( 'Headline' ); ?></th>
			<th><?php echo JText::_( 'Seo' ); ?></th>
			<th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->all as $row ):

	$href_live = $front_end . $row->seo;
	$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=topics&task=edit&id='. $row->id, false );
	$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=topics&task=edit&id='. $row->id, false );
	$href_delete = JRoute::_( 'index.php?option=com_gpo&controller=topics&task=delete&id='. $row->id, false );
	
?>
		<tr class="gpo-row">
			<td class="title"><a href="<?php echo $href_edit; ?>"  title="Edit Topic"><?php echo $jView->escape( $row->window_title ); ?></a></td>
			<td class="headline"><span><?php echo ( !empty( $row->page_headline ) ? $jView->escape( $row->page_headline ) : 'n/a' ); ?></span></td>
			<td class="seo"><span><?php echo $jView->escape( $row->seo ); ?></span></td>
			<td class="action">
				<a href="<?php echo $href_live;?>"  target="_blank" title="Open Frontend Topic in a new window">Live</a> | <a href="<?php echo $href_delete;?>"  title="Delete Topic">Delete</a>
			</td>
		</tr>
<?php endforeach;?>
	</tbody>
<?php include_once('submenus_endblock.php'); ?>	
	</table>
</div>
<?php 
else:
	echo '<p>There are no topics yet, create one via the search page.</p>';
endif;