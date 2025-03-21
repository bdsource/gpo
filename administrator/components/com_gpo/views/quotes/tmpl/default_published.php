<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
$front_end = str_replace( "administrator",'',JURI::base(true));
$is_post = Joomla\CMS\Factory::getApplication()->getInput()->getMethod();
$filter_order	=	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '');
$filter_order_Dir	=	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '');

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');


/*$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/mootools-core.js');
$document->addScript( JURI::root(true).'/media/system/js/core-uncompressed.js');
 
 */
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="published" />
<input type="hidden" name="controller" value="quotes" />
<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
<?php include_once('submenus_startblock.php'); ?>

<?php if( count( $this->rows ) < 1){ ?>

<p>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=create' ); ?>">There are no unpublished Quotes in the queue. Click here to create one</a>
</p>

<?php } else {
    $state = Joomla\CMS\Factory::getApplication()->getInput()->get('state','');
    if(empty($state)){
        $state = (Joomla\CMS\Factory::getApplication()->getInput()->get('task','') == 'unpublished') ? 'unpublished' : 'published';
    }
    ?>
<style>
.gpo-row td{ 
	vertical-align:top; 
}
.gpo-row td.id{ width:15px; }
.gpo-row td.author{  }
.gpo-row td.published{ width:80px; }
.gpo-row td.publisher{  }
.gpo-row td.action{ width:80px; text-align:center; }
</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th>
                <?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $filter_order_Dir, $filter_order ); ?>
            </th>
			<th><?php echo JText::_( 'Published' ); ?></th>
			<th><?php echo JText::_( 'Author' ); ?></th>
			<th><?php echo JText::_( 'Title' ); ?></th>
			<th><?php echo JText::_( 'Publisher' ); ?></th>
			<th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=edit&live_id='. $row['id'],false );
	$link_citation = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=createcitation&id='. $row['id'],false );

	if( $this->can_publish )		
	{
		$href = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=published_delete&id='. $row['id'],false );			
		$a_delete ='| <a href="' . $href . '"  title="Permanently delete this Quote from the database">Delete</a>';
	}else{
		$a_delete ='';
	}



    if('published'==$state){
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=quotes&task=lookup&state=published&id='.$row['id']);
    } else {
        $link_lookup = JRoute::_('index.php?option=com_gpo&controller=quotes&task=lookup&state=unpublished&id='.$row['id']);
    }
?>
		<tr class="gpo-row">
			<td class="id"><a style="color:green" href="<?php echo $link_lookup;?>"  title="Open this Quote in Lookup view"><?php echo $row['id']; ?></a></td>
            <td class="published"><?php echo date("j M Y", strtotime( $row['published'] ) ); ?></td>
			<td class="author"><?php echo $row['author']; ?></td>
			<td class="title"><a href="<?php echo $link_edit;?>"  title="Edit this Quote"><?php echo $row['title']; ?></a></td>
			<td class="publisher"><?php echo $row['publisher']; ?></td>
			<td class="action">
				<a href="<?php echo $link_citation;?>"  title="Create a Citation from this Quote">Citation</a> <?php echo $a_delete; ?>
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