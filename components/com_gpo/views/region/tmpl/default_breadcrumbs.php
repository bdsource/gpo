<p>
<?php
foreach( $this->breadcrumbs as $item ):
?><a href="<?php 
echo JRoute::_('index.php?option=com_gpo&task=region&region=' . $item->id, true ); 
?>"><?php echo $item->name; ?></a> <?php
endforeach;
?>
</p>
