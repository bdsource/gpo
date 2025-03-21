<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$url = JRoute::_( 'index.php?option=com_gpo&controller=lists&type=' );
$jView = new JViewLegacy();
?>
<div id="message_box"></div>
<p>
This list is made up of entries added by editing the &quot;<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=lists&task=order&type=' . $this->type,false ); ?>">Drop-down</a>&quot;, you can also add entries via <a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=lists&type=' . $this->type .'&task=viewall' )?>">here</a> 
</p>
<?php $type = Joomla\CMS\Factory::getApplication()->getInput()->get( 'type' );
if($type == 'hashtags'){
        echo "<p><b>Add a star at the end of the Hashtag to mark it as default.</b></p>";
   }
?>
<?php if( !empty( $this->rows ) ):?>
<?php include_once('submenus_startblock.php'); ?>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
	<tr>
		<th>Entry</th><th>Action</th>
	</tr>
	</thead>
	<tbody id="list-data">
<?php
foreach( $this->rows as $row ):

	echo '
		<tr>
			<td class="center" id="row-' . $row->id . '">' . $jView->escape( $row->value ) .'</td>
			<td class="center"><a href="#' . $row->id . '" class="remove">Delete</a></td>
		</tr>
  ';
  endforeach;
  ?>
 </tbody>
</table>    
</div>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=lists&task=a_removelistitem' ); ?>" id="form-remove">

     <input type="hidden" id="remove_id" name="id" />
     <input type="hidden"  value="<?php echo $type; ?>" name="type"/>


</form>

    
<?php include_once('submenus_endblock.php'); ?>
<script>
$("list-data").observe( "click", function( event ){
	Event.stop( event );
	var e = Event.element( event );
	if( e.hasClassName( "remove" ) )
	{
		$("remove_id").value = e.readAttribute("href").substr( 1 );
		new Ajax.Updater( 'message_box', $('form-remove').action,{
			parameters :  $('form-remove').serialize( true ),
			evalScripts : true,
			onComplete: function(transport){
				var response,id,e;

				response = transport.responseText.strip();
				id = transport.request.options.parameters.id;
				e = $("row-" + id );
				
				if(  response === 'ok' )
				{
					$("message_box").update( e.innerHTML + " has been removed" );
					e.up('tr').remove();
				}else{
					$("message_box").update( 'Failed to remove &quot;' + e.innerHTML + '&quot; at this time, please try again.' );
				}
			}}
		);

	}
    });
</script>
<?php endif;?>