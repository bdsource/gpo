<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');



$filter_order	=	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '');
$filter_order_Dir	=	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '');
?>
<div id="message_box"></div>
<form method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="search" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<?php include_once('submenus_startblock.php'); ?>
<?php
foreach( $this->oCitation as $key => $value )
{
	if( is_string( $value ) )
	{
		$this->oCitation->$key = htmlspecialchars( $value, ENT_QUOTES );
	}
}
foreach( $_GET["citation"] as $key => $value )
{
	if( is_string( $value ) )
	{
		$_GET["citation"][$key] = htmlspecialchars( $value, ENT_QUOTES );
	}
}

echo '
<input type="hidden" name="citation[published]" value="' . $_GET["citation"]["published"] . '" />
<input type="hidden" name="citation[title]" value="' . $_GET["citation"]["title"] . '" />
<input type="hidden" name="citation[source]" value="' . $_GET["citation"]["source"] . '" />
<input type="hidden" name="citation[publisher]" value="' . $_GET["citation"]["publisher"] . '" />
<input type="hidden" name="citation[volume]" value="' . $_GET["citation"]["volume"] . '" />
<input type="hidden" name="citation[issue]" value="' . $_GET["citation"]["issue"] . '" />
<input type="hidden" name="citation[page]" value="' . $_GET["citation"]["page"] . '" />
<input type="hidden" name="citation[city]" value="' . $_GET["citation"]["city"] . '" />
<input type="hidden" name="citation[author]" value="' . $_GET["citation"]["author"] . '" />
<input type="hidden" name="citation[content]" value="' . $_GET["citation"]["content"] . '" />
<input type="hidden" name="citation[websource]" value="' . $_GET["citation"]["websource"] . '" />
<input type="hidden" name="citation[notes]" value="' . $_GET["citation"]["notes"] . '" />
<input type="hidden" name="citation[share]" value="' . $_GET["citation"]["share"] . '" />
<input type="hidden" name="citation[ext_id]" value="' . $_GET["citation"]["ext_id"] . '" />
<input type="hidden" name="citation[sourcedoc]" value="' . $_GET["citation"]["sourcedoc"] . '" />

<input type="hidden" name="citation[published_range][to]" value="' . $_GET["citation"]["published_range"]["to"] . '" />
<input type="hidden" name="citation[published_range][from]" value="' . $_GET["citation"]["published_range"]["from"] . '" />
<input type="hidden" name="revise" value="" />
<input type="hidden" name="filter_order" value="' . $_GET['filter_order'] . '" />
<input type="hidden" name="filter_order_Dir" value="' . $_GET['filter_order_Dir'] . '" />
';
?>

    <p>
    <b>Found:</b> <?php echo $this->totalFound; ?> results.
    <?php
    if( !empty($_GET["citation"]["published_range"]["from"]) ) {
        $searchedIn .= "From Date: " . "<b>" . str_replace('/','-',$_GET["citation"]["published_range"]["from"]) . "</b>, ";
        $searchedIn .= "To Date: "   . "<b>";
        $searchedIn .= !empty($_GET["citation"]["published_range"]["to"]) ? str_replace('/','-',$_GET["citation"]["published_range"]["to"]) : date('d-m-Y');
        $searchedIn .= "</b>, ";
    }
    if( !empty($_GET["citation"]["id_range"]["from"]) || !empty($_GET["citation"]["id_range"]["to"]) ) {
        $searchedIn .= "ID From: " . "<b>" . !empty($_GET["citation"]["id_range"]["from"]) ? $_GET["citation"]["id_range"]["from"] : 1 . "</b>, ";
        $searchedIn .= "ID To: <b>" . !empty($_GET["citation"]["id_range"]["to"]) ? $_GET["citation"]["id_range"]["to"] : 1000000;
        $searchedIn .= "</b>, ";
    }
    foreach( $_GET["citation"] as $key => $val ) {
        if( in_array($key,array('published_range','id_range')) ) {
            continue;
        }
        $searchedIn .= empty($val) ? '' : ($key . ": " . "<b>$val</b> ");
    }
    echo !empty($searchedIn) ? "<br> <b>Searched In:</b> $searchedIn" : '';
    ?>
    </p>
    
<style>
.gpo-row td{ 
	vertical-align:top; 
}
/* ID, Published, Location and Create */
.gpo-row td.id{ width:15px; }
.gpo-row td.published{ width:80px; }
.gpo-row td.create{ width:20px; vertical-align:middle; }
</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th><?php 
						echo JHTML::_('grid.sort',   'Id', 'id', $filter_order_Dir, $filter_order );			
			?></th>
			<th><?php 
						echo JHTML::_('grid.sort',   'Published', 'published', $filter_order_Dir, $filter_order );
			?></th>
			<th><?php 
						echo JText::_( 'Source' ); 
			?></th>
			<th><?php echo JText::_( 'Title' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $pos=>$row ):?>
<?php
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=citations&task=edit&type=' . $this->type . '&live_id='. $row['id'] );
?>
		<tr class="gpo-row" id="gpo-row-<?php echo $pos; ?>-<?php echo $row['id']; ?>">
			<td class="id"><a class="track" href="<?php echo $link_edit;?>"  title="View Citation"><?php echo $row['id']; ?></a></td>
			<td class="published"><?php echo date("j M Y", strtotime( $row['published'] ) ); ?></td>
			<td class="source"><?php echo $row['source']; ?></td>
			<td class="title"><a class="track" href="<?php echo $link_edit;?>"  title="View News Item"><?php echo $row['title']; ?></a></td>
		</tr>
<?php endforeach;?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="6">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>	
	</table>
</div>
<?php include_once('submenus_endblock.php'); ?>
</form>
<script type="text/javascript">
//<![CDATA[	
function tableOrdering( order, dir, task ) {
	var form = document.adminForm;
	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;
	submitform( task );
} 

function reviseSearch()
{
	var form = document.adminForm;
	form.revise.value = 1;
	form.submit();
}

$$("a.track").each( function(el){
	el.observe("click",function(event){
		Event.stop(event);
		var ids = this.up("tr").readAttribute("id").replace("gpo-row-","");
		ids= ids.split("-");
		pos=ids[0];
		id=ids[1];

		var url = "<?php echo JURI::base(true) . JRoute::_( '/index.php?option=com_gpo&controller=citations&type=' . $this->type . '&task=searchresult_clicked',false ); ?>";
		var data = new Hash();
		data.set("id",id);
		data.set("pos",pos);

		new Ajax.Updater( 'message_box', url, {
		parameters :  data,
		evalScripts : true
		});		

	});
});
//]]>
</script>