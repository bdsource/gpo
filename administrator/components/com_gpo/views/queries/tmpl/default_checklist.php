<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$FRTHref = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=frt&action=add' );
$keywordsHref = JRoute::_( 'index.php?option=com_gpo&controller=keywords' );
$cleanupHref = JRoute::_( 'index.php?option=com_gpo&controller=cleanup' );
$update_datepublished = JRoute::_( 'index.php?option=com_gpo&controller=queries&task=publishdate_update_on_quotes_qcite_table' );
?>

<style>
    #links {
        font-size: 14px;
        font-weight: bold;
    }
    
    #links li {
        line-height: 24px;
        list-style-type: circle;
    }
</style>
<?php include_once('submenus_startblock.php'); ?>
<div>
    <h3>
        Run these SQL ‘Find & Replace’ queries to locate common errors in News, Quotes, NCite, QCite, Glossary and similar database records. 
        Each query provides editing options to make corrections. To run error correction queries in the Data Pages, 
        open the <a href="<?php echo $FRTHref;?>" title="Open DP F&R Tool in a new window" target="_blank">DP F&R tool</a> instead.
    </h3>
</div>

<div id="links">
    <ul>
         <li>
             <a href="<?php echo $keywordsHref;?>" title="Open Keywords page in a new window" target="_blank"> 
                 Cleanup Keywords Field
             </a>
         </li>
         
         <li>
             <a href="<?php echo $cleanupHref;?>" title="Open Cleanup Database page in a new window" target="_blank"> 
                 Cleanup Database Records
             </a>
         </li>
         
         <li>
             <a href="<?php echo $update_datepublished;?>" title="In both Quote and QCite tables, update the Published date field in each PopCite record to match its most recent Modified date" target="_blank"> 
                 Update PopCite Publication Dates
             </a>
         </li>
    </ul>
</div>
<?php include_once('submenus_endblock.php'); ?>