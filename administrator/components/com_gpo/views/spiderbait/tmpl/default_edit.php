<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="spiderbait" />

<input type="hidden" name="sb[id]" value="<?php echo $this->spiderbait->id; ?>" />
<input type="hidden" name="sb[url_hash]" value="<?php echo $this->spiderbait->url; ?>" />
<p>
Enter front url: ( Address in your browser window )<br />

<textarea class="inputbox"  name="sb[url]" id="sb[url]" rows="3" cols="70"><?php echo $this->spiderbait->url; ?></textarea>
</p>
<p>
Enter the text to be displayed. Adding &lt;br /&gt; will insert a line break.<br />
<textarea class="inputbox" name="sb[text]" id="sb[text]" rows="3" cols="70"><?php echo $this->spiderbait->text; ?></textarea>
</p>

</form>

