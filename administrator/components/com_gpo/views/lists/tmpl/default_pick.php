<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$url = JRoute::_( 'index.php?option=com_gpo&controller=lists&task=view&type=' );
?>


<ul>
<?php include_once('submenus_startblock.php'); ?>
<li><a href="<?php echo $url; ?>source">Source</a></li>
    <li><a href="<?php echo $url;?>hashtags">Hashtags</a></li>
<li><a href="<?php echo $url; ?>city">City</a></li>
<li><a href="<?php echo $url; ?>keywords">Keywords</a></li>
<li><a href="<?php echo $url; ?>category">Categories</a></li>
<li><a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=lists&task=editstaff');?>">Staff</a></li>
<?php include_once('submenus_endblock.php'); ?>
</ul>
