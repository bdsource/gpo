<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/jquery1.6.2.js' );

//JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');  
//$pane = &JPane::getInstance('sliders', array('allowAllClose'=>true, 'opacityTransition'=>true, 'duration'=>600, 'startOffset'=>-1));

$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}
$base_link='index.php?option=com_gpo&controller=locations';

function link_preview_dp($groupName)
{
	    $liveSite = JURI::root();
		$url = JRoute::_( $liveSite . 'index.php?option=com_gpo&task=preview&' . 'group=' . $groupName );
        
	    $href = "javascript:popup=window.open('" . $url . "','GunPolicy.org Data Page - Preview','toolbar=no,
                location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600'); popup.focus();";
		
		$anchorHtml = '<a  href="' . $href . '" title="Preview DP">Preview DP</a>';
        return $anchorHtml;
}	
?>

<h1>View Groups</h1>

<?php if( count($this->rows) > 0 ): ?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=locations&task=a_edit'); ?>">
<?php include_once('submenus_startblock.php');?>

<?php
$model = $this->locModel;
//echo $pane->startPane( 'pane' );
foreach( $this->rows as $key => $row ):
   $groupLocations = $model->getAllLocationsByGroupId($row['id']);  
  // echo $pane->startPanel( $row['name'], 'group-'.$row['id'] );
     echo "<div class='responsive' style='padding-left:10px;'>";
     echo "<table class='adminlist table-striped table-hover'>";     
     echo "<tr cellpadding='5'>
              <th colspan='4'>
                  Actions: &nbsp; &nbsp; &nbsp;
                  <a href=" . JRoute::_($base_link . '&task=group_edit&groupid='.$row['id']) . ">Edit Group</a>
                  &nbsp; &nbsp; &nbsp;
                  <a href=" . JRoute::_($base_link . '&task=group_new&action=delete&groupid='.$row['id']) . ">Delete Group</a>
                  &nbsp; &nbsp; &nbsp;
          " . 
            link_preview_dp($row['id']) . 
          " 
              </th>
          </tr>";
     
     echo "<tr cellpadding='5'>
              <th>Location ID</th>
              <th>Location Name</th>
              <th>Sort Order</th>
              <th>Group ID</th>
          </tr>";
     
     foreach( $groupLocations as $key=>$val ) {
        echo "<tr>
                 <td>" . $val['location_id'] . "</td>
                 <td>" . $val['name'] . "</td>
                 <td>" . $val['sort'] . "</td>
                 <td>" . $val['group_id'] . "</td>
             </tr>";
     }
     echo "</table>";
     echo "</div>";
  // echo $pane->endPanel();
endforeach;
//echo $pane->endPane('pane');
?>
<?php include_once('submenus_startblock.php');?>
</form>


<?php
else:
?>

<h4> No Groups found. Create one! </h4>

<?php endif; ?>
