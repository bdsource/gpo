<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
$jinput = JFactory::getApplication()->input;

?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="unpublished" />
<input type="hidden" name="controller" value="news" />
<?php include_once('submenus_startblock.php'); ?>
<?php if( !isset( $this->rows ) || count( $this->rows ) < 1) { ?>

<p>	
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=news&task=create'); ?>">There are no unpublished News articles in the queue. Click here to create one.</a>
</p>

<?php } else {

$state = $jinput->getVar('state','');
if(empty($state)){
    $state = ($jinput->getVar('task','') == 'unpublished') ? 'unpublished' : 'published';
}
?>
<style type="text/css">
.gpo-row td{ 
	vertical-align:top; 
}
.gpo-row td.id{ width:15px; }
.gpo-row td.published{ width:80px; text-align:center; }
.gpo-row td.action{ width:80px; text-align:center; }
.gpo-row td.gpnheader{text-align:left; }

.adminlist a.ap_new{ color:#ff0000; }
.adminlist a.ap_exists{ color:#4CC417; }

</style>
<script type="text/javascript">
    function sortTable(){
        var tl = new HtmlTable('news_unpublished');
        tl.sort(index,reverse,prepare);
    }
</script>
<div class="responsive">
<table class="adminlist table-striped table-hover" id="news_unpublished">
	<thead>
		<tr>
			<th><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->filter_order_Dir ,$this->filter_order);?></th>
			<th><?php echo JHTML::_('grid.sort', JText::_('Published'), 'published', $this->filter_order_Dir ,$this->filter_order);?></th>
                        <th><?php echo JHTML::_('grid.sort', JText::_('Source'), 'source', $this->filter_order_Dir ,$this->filter_order);?></th>
			<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'title', $this->filter_order_Dir ,$this->filter_order);?></th>
            <th><?php echo JHTML::_('grid.sort', JText::_('GPNHeader'), 'gpnheader', $this->filter_order_Dir ,$this->filter_order);?></th>
            <th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=news&task=edit&id='. $row['id'] );
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
		
		$href_publish = JRoute::_( 'index.php?option=com_gpo&controller=news&task=publish&id='. $row['id'] );		
		$publish_link = '<a class="' . $awaiting . '" href="' . $href_publish . '" title="Approve and publish this News item">Publish</a> |';
	}else{
		if( $row['ap_id'] === null )
		{
			$href_publish = JRoute::_( 'index.php?option=com_gpo&controller=news&task=publish&id='. $row['id'] );		
			$publish_link = '<a href="' . $href_publish . '" title="Approve this News item to the Publish Queue">Publish</a> |';
		}else{
			$publish_link = '<span>Queued</span> |';
		}		
	}
	$href_delete = JRoute::_( 'index.php?option=com_gpo&controller=news&task=unpublished_delete&id='. $row['id'] );

    if('published'==$state){
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=news&task=lookup&state=published&id='.$row['id']);
    } else {
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=news&task=lookup&state=unpublished&id='.$row['id']);
    }
?>
		<tr class="gpo-row">
			<td class="id"><a style="color:green" href="<?php echo $link_lookup;?>" title="Open this News item in Lookup view"><?php echo $row['id']; ?></a></td>
			<td class="published"><?php echo date("j M Y", strtotime( $row['published'] ) ); ?></td>						
			<td class="source"><?php echo $row['source']; ?></td>
			<td class="title"><a href="<?php echo $href_edit;?>" title="Edit this unpublished News Item"><?php echo $row['title']; ?></a></td>
            <td class="gpnheader"><?php echo $row['gpnheader']; ?></td>
            <td class="action">
				<?php echo $publish_link; ?>
				<a href="<?php echo $href_delete;?>" title="Permanently delete this unpublished News Item from queue">Delete</a>
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
    <input type="hidden" name="filter_order" value="" />
    <input type="hidden" name="filter_order_Dir" value=""/>
	<?php include_once('submenus_endblock.php'); ?>
</form>
