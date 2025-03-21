<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator",'',JURI::base(true));
?>
<h1>
<?php 
      echo 'Missing Citations (QCite or NCite) in ';
      echo ($this->location->name) ? $this->location->name.':' : ' all locations:';
?>
</h1>
<h4>
<!--It shows up all the table rows on which an opening squiggly bracket ( { ) [if any] is followed
by anything that's not a lower-case 'q' or an 'n' - including perhaps a space.
<br />
i.e. Search for any squiggly bracket not followed by any q or n cite. e.g. {456} or { 123}
<br /> 
For example, on the US page display we can see the string ' {1302} '. 
<br />
That means we've forgotten to add the prefix which indicates that the value for 'Suicide Rate' 
should be sourced from the Quotes (or perhaps the News) table.
-->
 This search finds all table rows in which an opening squiggly bracket ( { ) is not followed by
    the correct citation syntax, <br />
    for example a missing lower-case q, n or g, as in {456},
    or with a space instead { 123}.
   <br /> In these examples, we've forgotten to add the q, n or g prefix which locates the datapoint in the Quotes,<br />
    News or Glossary tables.

</h4>

<p> &nbsp; &nbsp;</p>

<p>
<h3>Missing Citations found for the following Locations:</h3>
<table class="adminlist" cellspacing="1">
<thead>
<tr>
  <th class="title"> Location Name </td>
  <th class="title"> Columns with missing citations </td>
</tr>
</thead>
<tbody>
<?php
foreach( $this->result as $key => $val ){
  echo '<tr>';
  echo '<td>' . $key . '</td>';
  echo '<td>' . implode(', ', $val) . '</td>';
  echo '</tr>';
}

if( empty($this->result) )
{
  echo '<tr colspan="2">' . '<td> No missing QCite or NCite syntax found </td>' . '</tr>';
}
?>
</tbody>
</table>
</p>

<form action="index.php?option=com_gpo&amp;controller=datapages" method="post" name="adminForm" id="adminForm">
            
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="task" value="<?php echo $this->task;?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="action" value="<?php echo $action;?>" />
<?php include_once('submenus_startblock.php'); ?>
<?php include_once('submenus_endblock.php'); ?>
</form>