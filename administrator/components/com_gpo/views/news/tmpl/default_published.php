<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$document = &JFactory::getDocument();
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
$front_end = str_replace( "administrator",'',JURI::base(true));

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');

$document->addScript( JURI::root(true).'/media/system/js/mootools-core-uncompressed.js');
$document->addScript( JURI::root(true).'/media/system/js/core-uncompressed.js');
$document->addScript( JURI::root(true).'/media/system/js/modal-uncompressed.js');
$document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');
?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="published" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="news" />

<?php include_once('submenus_startblock.php'); ?>
                
<?php if( count( $this->rows ) < 1) { ?>

<p>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=news&task=create' ); ?>">There are no published News in the queue. Click here to create one.</a>
</p>

<?php } else {
$jinput = JFactory::getApplication()->input;
    
$state = $jinput->getVar('state','');
if(empty($state)){
    $state = ($jinput->getVar('task','') == 'unpublished') ? 'unpublished' : 'published';
}    
?>
<style>
.gpo-row td{ 
	vertical-align:top; 
}
.gpo-row-citation td{
    vertical-align:top;
}
.gpo-row td.id{ width:15px; }
.gpo-row td.published{ width:80px; text-align:center; }
.gpo-row td.gpnheader{text-align:left; }
.gpo-row td.access{ width:30px; text-align:center; }
.gpo-row td.action{ width:80px; text-align:center; }

</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
            <th><?php echo JText::_( 'Id' ); ?></th>
			<th><?php echo JText::_( 'Published' ); ?></th>
			<th><?php echo JText::_( 'Source' ); ?></th>
			<th><?php echo JText::_( 'Title' ); ?></th>
            <th><?php echo JText::_( 'GPNHeader' ); ?></th>
            <th><?php echo JText::_( 'Access' ); ?></th>
			<th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>

<?php foreach( $this->rows as $row ):?>
<?php
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=news&task=edit&live_id='. $row['id'],false );
	$link_citation = JRoute::_( 'index.php?option=com_gpo&controller=news&task=createcitation&id='. $row['id'],false );

	if( $this->can_publish )		
	{
		$href = JRoute::_( 'index.php?option=com_gpo&controller=news&task=published_delete&id='. $row['id'],false );			
		$a_delete ='| <a href="' . $href . '"  title="Permanently delete this News Item from the database">Delete</a>';
	}else{
		$a_delete ='';
	}
	$access = ( (int)$row['share'] == (int)'1' ) ? 'Public' : '<span style="color:#ff0000;">Members</span>';

    if('published'==$state){
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=news&task=lookup&state=published&id='.$row['id']);
    } else {
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=news&task=lookup&state=unpublishedid='.$row['id']);
    }
?>
		<tr class="gpo-row">
			<td class="id"><a style="color:green" href="<?php echo $link_lookup;?>"  title="Open this News item in Lookup view"><?php echo $row['id']; ?></a></td>
            <td class="published"><?php echo date("j M Y", strtotime( $row['published'] ) ); ?></td>
			<td class="source"><?php echo $row['source']; ?></td>
			<td class="title"><a href="<?php echo $link_edit;?>"  title="Edit this News Item"><?php echo $row['title']; ?></a></td>
            <td class="gpnheader"><?php echo $row['gpnheader']; ?></td>
            <td class="access">
				<?php echo $access; ?>
			</td>
			<td class="action">
				<a href="<?php echo $link_citation;?>"  title="Create a Citation from this News Item">Citation</a> <?php echo $a_delete; ?>
			</td>
		</tr>
<?php endforeach;?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="6" style="padding-top:20px;">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
	</table>
</div>
<?php } ?>
  <?php include_once('submenus_endblock.php'); ?>
</form>
