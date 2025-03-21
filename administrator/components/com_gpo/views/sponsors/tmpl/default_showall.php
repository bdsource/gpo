<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$gpoSponsors = new GpoSponsors();
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<?php include_once('submenus_startblock.php'); ?>
<?php
$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/jquery1.6.2.js');
$document->addScript( JURI::root(true).'/media/system/js/jquery.tablesorter.min.js');
$document->addStyleSheet(JURI::root(true).'/media/system/js/table_sorter_theme/blue/style.css', 'text/css', 'print, projection, screen');
$document->addScript( JURI::root(true).'/media/system/js/messi.min.js');
$document->addStyleSheet(JURI::root(true).'/media/system/js/messi.min.css', 'text/css', 'print, projection, screen');
?>
<style>
.sort_column_head{color: #025A8D !important;cursor: pointer;}
</style>
<?php if( $this->total < 1): ?>
<p>There are no sponsors that have been assigned to any pages. Please create one</p>

<?php else: ?>
<div class="responsive">
<table class="adminlist  table-striped table-hover" id="adminlist">
	<thead>
		<tr>
			<th sort="asc" class="sort_column_head"><?php echo JText::_( 'Url' ); ?></th>
			<th sort="asc" class="sort_column_head">
                <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('en');?>" />
                <?php echo JText::_( 'Module Title' ); ?>
            </th>
            <th sort="asc" class="sort_column_head">
                <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('fr');?>" />
                <?php echo JText::_( 'Module Title' ); ?>
            </th>
            <th sort="asc" class="sort_column_head">
                <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('es');?>" />
                <?php echo JText::_( 'Module Title' ); ?>
            </th>
			<th><?php echo JText::_( 'Comment' ); ?></th>
			<th><?php echo JText::_( 'Created At' ); ?></th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
			</th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$link = JRoute::_( 'index.php?option=com_gpo&controller=sponsors&task=edit&id='. $row->id );
	$checked 	= JHTML::_('grid.id',   $i, $row->id );
?>
		<tr>
			<td><a href="<?php echo $link;?>" title="Edit"><?php echo $row->url; ?></a></td>
			<td><a href="<?php echo $link;?>" title="Edit"><?php echo $row->title; ?></a></td>
            <td><a href="<?php echo $link;?>" title="Edit"><?php echo $gpoSponsors->getModuleTitle($row->module_id_fr);?></a></td>
            <td><a href="<?php echo $link;?>" title="Edit"><?php echo $gpoSponsors->getModuleTitle($row->module_id_es);?></a></td>
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
<input type="hidden" name="controller" value="sponsors" />
<?php include_once('submenus_endblock.php'); ?>
</form>

<script>

//use jquery tablesorter plugin to enable column sorting
$.noConflict();
jQuery(document).ready(function() {

	 jQuery('.sort_column_head').click(function(){
    var sort_order = jQuery(this).attr('sort');
    var sort_image_asc = '<img class="sort_image_asc" alt="" src="<?php echo JURI::root() ?>media/system/images/sort_asc.png">';
    var sort_image_desc = '<img alt="" class="sort_image_desc" src="<?php echo JURI::root() ?>media/system/images/sort_desc.png">';
    if(sort_order=='desc'){ jQuery(this).attr('sort','asc');jQuery('.sort_image_desc,.sort_image_asc').remove(); jQuery(this).append(sort_image_asc);  }else{ jQuery('.sort_image_asc,.sort_image_desc').remove();jQuery(this).append(sort_image_desc);jQuery(this).attr('sort','desc');}
  });
	jQuery("#adminlist").tablesorter({ 
        headers: { 2: {sorter: false },3: {sorter: false },4: {sorter: false } },
        sortList: [[0,0],[1,0]]
    });
});

</script>
