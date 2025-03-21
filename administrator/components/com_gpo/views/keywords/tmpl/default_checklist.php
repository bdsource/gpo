<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$filename = JPATH_BASE . '/components/com_gpo/cache/keywords.txt';
if( file_exists( $filename ) )
{
	$legal_keywords = explode("\r\n",file_get_contents( $filename ));
}

if( !empty( $legal_keywords ) )
{

	foreach( $this->unique_keywords as $lo )
	{
		if( !in_array( $lo, $legal_keywords ) )
		{
			$with_errors[]=$lo;
		}
	}
	sort( $with_errors );
}

?>
<p>
If any mis-spellings or illegal Keywords appear in this list, they should be found and corrected in the original News database record.
</p>


<div id="message-box"></div>


<?php if( isset( $with_errors ) ): 
echo '<style>
.legal-list a{
	display:block;
}
</style>
<p class="legal-list">';
foreach( $with_errors as $v )
{
	$href = JRoute::_( 'index.php?option=com_gpo&task=search&controller=news&news[keywords]=' . $v );
	echo '<a target="blank" href="' . $href . '" title="Open in new Window with the search results from News">' . $v . '</a>';
}
echo '</p>';
?>
<?php else: ?>
<p>
	At present, there are no illegal keywords.
</p>

<?php endif; ?>
<?php include_once('submenus_startblock.php'); ?>
<?php include_once('submenus_endtblock.php'); ?>
