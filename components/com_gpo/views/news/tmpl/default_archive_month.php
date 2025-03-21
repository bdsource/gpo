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
	text-align:center;
}
.days {
	margin:0px;
	margin-left:27px;
}

.days tr td a{
	background:url(/images/M_images/arrow.png) no-repeat scroll 0 1px #FFFFFF;
	list-style-type:none;
	margin-left:0;
	padding:0 5px 0 15px;
	text-align:center;
}
</style>

<div style="margin-bottom:20px;">
	<ul class="li-inline">
<?php foreach( $this->years as $year ): ?>
		<li><a href="<?php echo JRoute::_( 'index.php?option=com_gpo&task=news&id=archive&y=' . $year ); ?>"><?php echo $year; ?></a></li>
<?php endforeach;?>
	</ul>
</div>


<?php if( count( $this->days ) < 1 ): ?>
<p>At present there are no News items for this month.</p>
<?php 
return;
endif; ?>

<div style="margin-bottom:20px;">
	<h1>Gun Policy News, <?php echo $this->month_name . ' ' . $this->year; ?></h1>
</div>


<div style="margin-bottom:20px;">
<?php 
$size = count( $this->days );
$rows = round( $size / 4 );

$i=0;
$rows = array();
$rows[ $i ] = array();
$j=0;
foreach( $this->days as $item )
{
	$a = explode("/", $item->date ); 
	$stub = "y=" . $a[ '0' ] . '&m=' . $a['1'] . '&d=' . $a['2'];	
	$rows[ $i ][] = '<td><a href="' . JRoute::_( 'index.php?option=com_gpo&task=news&id=archive&' . $stub ) . '">' . $item->name . '</a></td>';
	if( $j == 4 )
	{
		$j=0;
		++$i;
	}else{
		++$j;	
	}
}
$html = '';
foreach( $rows as $row )
{
	$html .= '<tr>';
	foreach( $row as $col )
	{
		$html .= $col;
	}
	$html .= '</tr>';
}
echo '<table class="days">' . $html . '</table>';
?>
</div>