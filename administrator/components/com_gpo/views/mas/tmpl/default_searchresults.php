<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$filter_order	=	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '');
$filter_order_Dir	=	Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '');

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');


?>
<div id="message_box"></div>
<form method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="search" />
<input type="hidden" name="controller" value="mas" />
<?php include_once('submenus_startblock.php'); ?>
<?php
foreach( $this->oMas as $key => $value )
{
	if( is_string( $value ) )
	{
		$this->oMas->$key = htmlspecialchars( $value, ENT_QUOTES );
	}
}
foreach( $_GET["mas"] as $key => $value )
{
	if( is_string( $value ) )
	{
		$_GET["mas"][$key] = htmlspecialchars( $value, ENT_QUOTES );
	}
}
echo '
<input type="hidden" name="mas[locations]" value="' . $_GET["mas"]["locations"] . '" />
<input type="hidden" name="mas[source]" value="' . $_GET["mas"]["source"] . '" />
<input type="hidden" name="mas[title]" value="' . $_GET["mas"]["title"] . '" />
<input type="hidden" name="mas[byline]" value="' . $_GET["mas"]["byline"] . '" />
<input type="hidden" name="mas[subtitle]" value="' . $_GET["mas"]["subtitle"] . '" />
<input type="hidden" name="mas[keywords]" value="' . $_GET["mas"]["keywords"] . '" />
<input type="hidden" name="mas[websource]" value="' . $_GET["mas"]["websource"] . '" />
<input type="hidden" name="mas[gpnheader]" value="' . $_GET["mas"]["gpnheader"] . '" />
<input type="hidden" name="mas[share]" value="' . $_GET["mas"]["share"] . '" />
<input type="hidden" name="mas[notes]" value="' . $_GET["mas"]["notes"] . '" />
<input type="hidden" name="mas[content]" value="' . $_GET["mas"]["content"] . '" />
<input type="hidden" name="mas[published_range][to]" value="' . $_GET["mas"]["published_range"]["to"] . '" />
<input type="hidden" name="mas[published_range][from]" value="' . $_GET["mas"]["published_range"]["from"] . '" />
<input type="hidden" name="revise" value="" />
<input type="hidden" name="filter_order" value="' . $_GET['filter_order'] . '" />
<input type="hidden" name="filter_order_Dir" value="' . $_GET['filter_order_Dir'] . '" />
<input type="hidden" name="mas[many]" value="' . $_GET["mas"]["many"] . '" />
';
?>

    <p>
    Found <?php echo $this->totalFound; ?> results.</p>
    <?php
    if( !empty($_GET["mas"]["published_range"]["from"]) ) {
        $searchedIn .= "From Date: " . "<b>" . str_replace('/','-',$_GET["mas"]["published_range"]["from"]) . "</b>, ";
        $searchedIn .= "To Date: "   . "<b>";
        $searchedIn .= !empty($_GET["mas"]["published_range"]["to"]) ? str_replace('/','-',$_GET["mas"]["published_range"]["to"]) : date('d-m-Y');
        $searchedIn .= "</b>, ";
    }
    if( !empty($_GET["mas"]["id_range"]["from"]) || !empty($_GET["mas"]["id_range"]["to"]) ) {
        $searchedIn .= "ID From: " . "<b>" . !empty($_GET["mas"]["id_range"]["from"]) ? $_GET["mas"]["id_range"]["from"] : 1 . "</b>, ";
        $searchedIn .= "ID To: <b>" . !empty($_GET["mas"]["id_range"]["to"]) ? $_GET["mas"]["id_range"]["to"] : 1000000;
        $searchedIn .= "</b>, ";
    }
    
    foreach( $_GET["mas"] as $key => $val ) {
        if( in_array($key,array('published_range','id_range')) ) {
            continue;
        }
        $searchedIn .= empty($val) ? '' : ($key . ": " . "<b>$val</b> ");
    }
    echo !empty($searchedIn) ? "<br> <b>Searched In:</b> $searchedIn</p>" : '';
    ?>

<style>
.gpo-row td{ 
	vertical-align:top; 
}
/* ID, Published, Location and Create */
.gpo-row td.id{ width:15px; }
.gpo-row td.published{ width:80px; }
.gpo-row td.location{ width:120px; }
.gpo-row td.create{ width:20px; vertical-align:middle; }
.gpo-row td.keywords{ width:18% }
/*
.gpo-row td.author{ width:10% }
.gpo-row td.title{ width:30% }
.gpo-row td.source{ width:15% }

*/
</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th><?php 
						echo JHTML::_('grid.sort',   'ID', 'id', $filter_order_Dir, $filter_order );			
			?></th>
			<th><?php 
						echo JHTML::_('grid.sort',   'Published', 'published', $filter_order_Dir, $filter_order );
			?></th>
			<th><?php 
						echo JText::_( 'Source' ); 
			?></th>
			<th><?php echo JText::_( 'Title' ); ?></th>
			<th><?php echo JText::_( 'Location' ); ?></th>
			<th><?php echo JText::_( 'Create' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $pos=>$row ):?>
<?php
	$link_lookup = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=lookup&id='. $row['id'],false );
	$link_edit = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=edit&live_id='. $row['id'],false );
	$link_citation = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=createcitation&id='. $row['id'],false );
?>
		<tr class="gpo-row" id="gpo-row-<?php echo $pos; ?>-<?php echo $row['id']; ?>">
			<td class="id"><a class="track" style="color: green;" href="<?php echo $link_lookup;?>"  title="Open this Mas item in Lookup view"><?php echo $row['id']; ?></a></td>
			<td class="published"><?php echo date("j M Y", strtotime( $row['published'] ) ); ?></td>
			<td class="source"><?php echo $row['source']; ?></td>
			<td class="title"><a href="<?php echo $link_edit;?>"  title="Edit this Mas Item"><?php echo $row['title']; ?></a></td>
			<td class="location"><?php 
			$locations = explode(",", $row['locations'] );
			if( isset( $locations['6'] ) )
			{
				$lo_size = count( $locations );
				for( $i=6;$i<$lo_size;$i++ )
				{
					unset( $locations[$i] );
				}
				$locations['5'].='...';
			}
			echo implode(", ", $locations);
			
			?></td>
			<td class="create">
				<a href="<?php echo $link_citation;?>"  title="Create a Citation">Citation</a>
			</td>
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
<?php include_once('submenus_startblock.php'); ?>
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

$('createTopic').observe('click', function( event ){
	Event.stop( event );
	var data = $('adminForm').serialize( true );
	data = new Hash( data );
	var d = new Hash();
	data.keys().each( function( id ){
		if( id.match( /^mas\[/ ) )
		{
            id = id.match( /^mas\[.*\]/ )[0];
            d.set( id, data.get( id ) );
		} 
	});
	var data = encodeURIComponent( d.toQueryString() );

	var href = this.readAttribute('href');
	
	href += '&d=';
	href += data;
	window.location = href;
});

$$("a.track").each( function(el){
	el.observe("click",function(event){
		Event.stop(event);
		var ids = this.up("tr").readAttribute("id").replace("gpo-row-","");
		ids= ids.split("-");
		pos=ids[0];
		id=ids[1];

		var url = "<?php echo JURI::base(true) . JRoute::_( '/index.php?option=com_gpo&controller=mas&task=searchresult_clicked',false ); ?>";
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