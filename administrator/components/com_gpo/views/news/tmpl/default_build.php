<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

if( $this->inprogress )
{
?>
<p>The system is still processing your last request</p>
<?php
return;
}
?>
<style>
#build-form label{ display:block; }
</style>
<form enctype="multipart/form-data" method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=news&task=build',false );?>" id="build-form">
<?php include_once('submenus_startblock.php'); ?>
<fieldset>
	<label>Have you uploaded the database to the ftp?</label>
	<label>I want to build the database <input type="radio" name="build" value="full" /></label>
	<button type="submit">Build the entire database</button>
</fieldset>	

<fieldset>
<p>
	(In IE8, switch ‘Compatibility View’ OFF before uploading) 
</p>	
	<label>Do you want to upload new records? <input type="radio" name="build" value="add" /></label>
	<label><input type="file" name="file_input" value="" /></label>
	<button type="submit">Add new records only</button>	
</fieldset>
<?php include_once('submenus_endblock.php'); ?>
</form>