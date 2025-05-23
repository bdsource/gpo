<?php defined( '_JEXEC' ) or die( 'Restricted Access' ); ?>

<div style="margin-bottom:20px;">
	<h1>Find Gun Policy News by Date Published</h1>
</div>

<?php if( !isset( $this->years[ '0' ] ) ): ?>

<p>Unable to find any years. This is most likely a mistake. Please try again.</p>

<?php 
return;
endif; ?>

<style>
ul.li-inline li{ display:inline; }
ul.li-inline li a{
	background:url(/images/M_images/arrow.png) no-repeat scroll 0 1px #FFFFFF;
	list-style-type:none;
	margin-left:0;
	padding:0 5px 0 15px;
}

</style>

<div style="margin-bottom:20px;">
	<ul class="li-inline">
<?php foreach( $this->years as $year ): ?>
		<li><a href="<?php echo JRoute::_( 'index.php?option=com_gpo&task=news&id=archive&y=' . $year ); ?>"><?php echo $year; ?></a></li>
<?php endforeach;?>
	</ul>
</div>

<div style="margin-bottom:20px;">
	<h1><?php echo $this->year; ?></h1>
</div>

<?php if( count( $this->months ) < 1 ): ?>
<p>At present there are no News items for this month.</p>
<?php 
return;
endif; ?>

<ul class="li-inline">
<?php foreach( $this->months as $i ):
$a = explode("/", $i->date ); 
$stub = "y=" . $a[ '0' ] . '&m=' . $a['1'];
?>
<li><a href="<?php echo JRoute::_( 'index.php?option=com_gpo&task=news&id=archive&' . $stub ); ?>"><?php echo $i->name; ?></a></li>
<?php endforeach;?>
</ul>