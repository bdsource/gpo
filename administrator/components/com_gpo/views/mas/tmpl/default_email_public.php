<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'spiderbait'.DS.'tmpl'.DS.'toolbar.default.php' );
$front_end = str_replace( "administrator",'',JURI::base(true));
if( $this->testMode === true )
{
	echo '<p style="color:#ff0000;">Test Mode</p>';
	
}
?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="maillist" />
<input type="hidden" name="controller" value="mas" />
<?php include_once('submenus_startblock.php'); ?>

<?php //if( count( $this->rows ) < 1): ?>
<?php if( !isset( $this->rows['0'] ) ): ?>

<p>There are no emails awaiting to be sent.</p>

<?php else: ?>
<style>
.gpo-row td{ 
	vertical-align:top; 
}
.gpo-row td.id{ width:15px; }
.gpo-row td.source{ width:150px; }
.gpo-row td.access{ width:30px; text-align:center; }
.gpo-row td.action{ width:30px; text-align:center; }
</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th><?php echo JText::_( 'Mas Id' ); ?></th>
			<th>Source</th>
			<th>Title</th>
            <th>GPNHeader</th>
            <th><?php echo JText::_( 'Access' ); ?></th>
			<th><?php echo JText::_( 'Action' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $row ):?>
<?php
	$link_lookup = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=lookup&id='. $row['id'],false );	
	$link_delete = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=email_public_remove&id='. $row['id'],false );
	$access = ( (int)$row['share'] == (int)'1' ) ? 'Public' : '<span style="color:#ff0000;">Members</span>';		
	if( $row['global_interest'] === '1' )
	{
		$access = '<span style="color:#00ff00;">Public( Global )</span>';
	}	
?>
		<tr class="gpo-row">
			<td class="id warn"><a href="<?php echo $link_lookup;?>"  title="Lookup Mas Item"><?php echo $row['id']; ?></a></td>
			<td class="source"><?php echo $row['source']; ?></td>
			<td class="title"><?php echo $row['title']; ?></td>
            <td class="gpnheader"><?php echo $row['gpnheader']; ?></td>
            <td class="access">
				<?php echo $access; ?>
			</td>			
			<td class="action">
				<a href="<?php echo $link_delete;?>"  title="Remove from Mail Queue">Delete</a>
			</td>
		</tr>
<?php endforeach;?>
	</tbody>
	</table>
</div>>
<?php endif; ?>
<?php include_once('submenus_endblock.php'); ?>
</form>
<script type="text/javascript">
//<![CDATA[	
Event.observe(window,'load',function(){
	
$("submit-send").observe("click",function(event){
	Event.stop(event);
	$("adminForm").submit();
});
var els = $$(".gpo-row .warn");
if( els.size() > 0 )
{
	els.each(function(el){
		el.observe("click",function(event){
			alert("If you edit and re-publish this article, prevent it from being posted once again to the Members' list by selecting the 'Don't publish to Members' option on the Publish page");
		});
	});
}
});
//]]>
</script>
