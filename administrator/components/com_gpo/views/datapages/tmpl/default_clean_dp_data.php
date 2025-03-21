<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator",'',JURI::base(true));
?>


<h1>
<?php 
      echo 'Correcting error for '; 
      echo ($this->location->name) ? $this->location->name.':' : 'all locations:';      
?>
</h1>

<h4>
In the DP data table it will check following type of errors, if found any then it will correct automatically. <br />
<span style="font-weight:normal;">
<b>Type 1.</b> replace any preceding space before opening squiggly bracket. e.g. "is {q123}" to is{q123}
<br />	
<b>Type 2.</b> add a space in between an closing & opening squiggly bracket. e.g. is{q1}{q2} to is{q1} {q2}
<br />	
<b>Type 3. </b>find any double spaces and will be reduced to a single space
</span>
</h4>

<p>
<?php
switch( $this->type )
{
case 'run':

foreach( $this->clean_result as $val ){
  echo $val;
}

if( empty($this->clean_result) )
{
  echo 'no uncleaned data found';   
}
else
{
  echo "<p><b> total location data cleaned: " . count($this->clean_result) . "</b></p>";	
}
break;

case 'dry_run':

if( count($this->clean_result)>0 )
{
  $href = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=clean_data&type=run',false );
  //echo "<p><a href='$href' title='clean data'> [Clean Data & Update rows] </a></p>";
}

foreach( $this->clean_result as $val ){
  echo $val;
}

if( empty($this->clean_result) )
{
  echo '<h2 style="background: #CCFFCC;">No uncleaned data found</h2>';   
}
else
{
  echo "<p style='background: #CCFFCC;'><b> total location data to be cleaned: " . count($this->clean_result) . "</b></p>";
}
break;

case 'show_options':
  
  $href = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=clean_data&type=run',false );
  $href_dry = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=clean_data&type=dry_run',false );
  
  echo '<p> Use the Dry Run option below, 
        just to see the how many matched items are found in the DP data table...</p>';
  
  echo '<ul>';
  echo "<li><h4><a href='$href_dry' title='dry run'> check to see is there any uncleaned data - dry run </a></h4></li>";
  echo "<li><h4><a href='$href' title='clean data'> clean data & update rows </a></h4></li>";
  echo '</ul>';
break;

default:
  $href = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=clean_data&type=run',false );
  $href_dry = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=clean_data&type=dry_run',false );
  echo '<p> Use the Dry Run option below, 
        just to see the how many matched items are found in the DP data table...</p>';
  
  echo '<ul>';
  echo "<li><h4><a href='$href_dry' title='dry run'> check to see is there any uncleaned data - dry run </a></h4></li>";
  echo "<li><h4><a href='$href' title='clean data'> clean data & update rows </a></h4></li>";
  echo '</ul>';
break;
}
?>
</p>
