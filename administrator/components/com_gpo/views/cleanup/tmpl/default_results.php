<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();
?>
<p>
	Looking for <span style="color:#ff0000;">&quot;</span><?php $this->from; ?><span style="color:#ff0000;">&quot;</span>.<br />
	Found <?php echo $this->total; ?>.
</p>

<?php

foreach( $this->items as $item ):
	$href = JRoute::_( 'index.php?option=com_gpo&controller=news&task=lookup&id=' . $item->id );
	$title = $jView->escape( $item->title );
	echo '<p><a href="' . $href .'">' . $title . "</a></p>";
endforeach;
?>