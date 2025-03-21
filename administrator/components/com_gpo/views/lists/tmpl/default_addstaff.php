<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

?>
<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_gpo');?>" method="POST">
<?php include_once('submenus_startblock.php'); ?>
 <input type="hidden" name="option" value="com_gpo" />
 <input type="hidden" name="controller" value="lists" />
<input type="hidden" name="task" value="addstaff" />
<input type="hidden" name="validate" value="1" />
    <h3>Add staff</h3>
    <label>Name<br/>
	<input type="text" name="name" />
    </label><br/>
    <label>Initial<br/>
	<input type="text" name="initial" />
    </label><br/><br/>
	<?php include_once('submenus_endblock.php'); ?>
</form>
