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
<form enctype="multipart/form-data" method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=build',false );?>" id="build-form">
<?php include_once('submenus_startblock.php'); ?>
<fieldset>
	<label>Have you uploaded the database to the ftp?</label>
	<label>I want to build the database <input type="radio" name="build" value="full" /></label>
	<button type="submit">Build the entire database</button>
</fieldset>
<?php include_once('submenus_endblock.php'); ?>
</form>