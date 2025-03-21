<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$title = "";
if( !empty( $this->oCitation->title ) )
{
	$str = $this->oCitation->title;
	$title .= $str;
}

if( !empty( $this->oCitation->source ) )
{
	$str = '<span class="source">' . $this->oCitation->source . '</span>';
	if( !empty( $title ) )
	{
		$title .= ", " . $str;
	}else{
		$title = $str;
	}
}
if( !empty($this->oCitation->live_id ) )
{
	$title .= '<span class="id">Q' . $this->oCitation->live_id . '</span>';	
}

switch( $this->can_publish )
{
	case true:
		$publish_text = "Publish Now!";
		break;
	default:
		$publish_text = "Place in the publish queue.";	
		break;
}
?>

<style>
span.source{
	font-style:italic;
}
span.id{
	padding-left:30px;
}
</style>
<h1>Publish</h1>
<div id="message_box"></div>

<style>
#adminForm label{
	display:block;
}
</style>
<form method="post" action="<?php echo JRoute::_( 'index.php' ); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="hasinput" value="1" />

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="publish" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="type" value="quotes" />
<input type="hidden" name="id" value="<?php echo $this->oCitation->id; ?>" />
<?php include_once('submenus_startblock.php'); ?>

<p>
	<?php echo $title; ?>
</p>
	<input type="hidden" name="publish[approve]" value="1" />
	<input type="submit" value="<?php echo $publish_text; ?>" />
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>
<script type="text/javascript"> 
//<![CDATA[
document.observe("keydown",function(event){
	
	if( event.keyCode === Event.KEY_RETURN )
	{
		Event.stop(event);
		$('adminForm_task').value='publish';
		$('adminForm').submit();
	}
});
$('adminForm').observe('submit', function(event) {
	$('adminForm_task').value='publish';
});
//]]> 
</script>