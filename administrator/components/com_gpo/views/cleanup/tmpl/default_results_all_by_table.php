<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();

echo '
<pre>
Table: ' . $jView->escape( $this->table ) . '
Field: ' . $jView->escape( $this->field ) . '
Total: ' . $this->total . ' 
</pre>
<h1>from: [' . $jView->escape( $this->cleanup->from ) . '] to: [' . $jView->escape( $this->cleanup->to ) . ']</h1>
';


foreach( $this->items as $item ):

	switch( $this->table )
	{
		case 'j25_gpo_news':
			$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=news&task=edit&live_id=' . $item->id );
			break;
		case 'j25_gpo_quotes':
			$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=quotes&task=edit&live_id=' . $item->id );
			break;
		case 'j25_gpo_citations_news':
			$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=edit&live_id=' . $item->id );
			break;
		case 'j25_gpo_citations_quotes':
			$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=citations&type=quotes&task=edit&live_id=' . $item->id );
			break;
		default:
			$href_edit = "#";
			break;
	}
	$title = $jView->escape( $item->title );
	echo '<p><a href="' . $href_edit . '" target="_blank">' . $title . "</a></p>";
endforeach;
?>