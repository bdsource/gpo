<?php
echo '<div style="padding-top:10px;">';
switch( $this->location->type )
{
	case 'subregion':
		$title= "Countries and Territories";
		break;
	case 'region':
		$title= "Sub Regions";
		break;
//	case 'region':
//		$title="Regions";
//		break;
	default:
		$title="Regions";
		break;
}
echo '<h3>' . $title . '</h3>';
?>
<style>
tbody.locations tr td{
	padding:2px;
	width:33%;
}
tbody.locations tr td img{
	padding-right:2px;
}
</style>
<table width="100%">
<tbody class="locations">
<tr><?php
		$col=0;
		$total_col=2;
		foreach( $this->locations as $item )
		{?>
			<td><img src="<?php echo JURI::base();?>images/M_images/arrow.png" /><a href="<?php echo JRoute::_('index.php?option=com_gpo&task=region&region=' . $item->id, true ); ?>"><?php echo $item->title; ?></a></td>
		<?php
			if( $col >= $total_col )
			{
				echo '</tr><tr>';
				$col=0;
			}else{
				++$col;
			}
		}
		if( $col < $total_col )
		{
			echo str_repeat("<td>&nbsp;</td>", ($total_col+1) - $col );
		}
?></tr></tbody></table>
<?php

echo '</div>';
?>
