<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator",'',JURI::base(true));
?>

<?php

$fields_per_column = floor($dp_total_fields/2);
$display = 2; //number of columns
$cols = 0; 

?>
<link rel="stylesheet" href="<?php echo JURI::base(); ?>templates/khepri/css/form_styles.css" type="text/css" />

<fieldset>
<legend>Data Page Table</legend>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo', false); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="datapages" />
<?php include_once('submenus_startblock.php'); ?>
<?php include_once('submenus_endblock.php'); ?>


view all dp page [coming soon]  
</form>


