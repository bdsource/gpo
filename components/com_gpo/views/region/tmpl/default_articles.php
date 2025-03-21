<?php
$user	= & JFactory::getUser();
$groupsUserIsIn = JAccess::getGroupsByUser($user->id);
$this->staff = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
?>
<div style="padding-top:10px;">
<?php if( !$this->staff ): ?>
<h3>Articles</h3>
<?php else: ?>
<h3>Staff</h3>
<?php endif; ?>
<?php
foreach( $this->articles as $item ):
?>
<p><a href="<?php echo JRoute::_('index.php?option=com_gpo&task=region&region=' . $this->article->catid . '&id=' . $item->id,true ); ?>"><?php echo $item->title; ?></a></p>
<?php
endforeach;
?>
</div>

