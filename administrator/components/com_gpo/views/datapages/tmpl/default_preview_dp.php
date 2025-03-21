<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator",'',JURI::base(true));
$base_url = str_replace( 'administrator/','',JURI::base() );
?>
<link rel="stylesheet" href="<?php echo $base_url;?>/templates/gunpolicy/css/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $base_url;?>/templates/gunpolicy/css/position.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="<?php echo $base_url;?>/templates/gunpolicy/css/layout.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="<?php echo $base_url;?>/templates/gunpolicy/css/general.css" type="text/css" />

<?php
/*
function getHTML($p_field, $p_val, $p_preamble, $p_type='')
{
  $html = '<h4>' . camelize($p_field) . '</h4>';
  $html .= '<p>';
  if( strpos($p_val,';') !== false ){ 
     $p_val = '<p>' . str_replace(';','<br>',$p_val) . '</p>';
  }
  if( strpos($p_preamble,'*****') !== false )
  {
  	$html .= str_replace( '*****',$p_val,$p_preamble );
  }
  else if( strpos($p_preamble,'****') !== false )
  {
  	$html .= str_replace( '****',$p_val,$p_preamble );
  }
  else
  {
    
    $html .= $p_preamble . ' ' . $p_val;	
    
  }
  
  $html .= '</p>';
  return $html;  
}
*/
?>

<html>
<head>
</head>
<body style="font-size: 100%">
<div id="all">
<div align="center" style="margin:30 10 10 10px;" id="wrapper">

<table class="contentpaneopen">
<tbody>
<tr>
<td>
<h1>
<?php 
$displayLocation = ($this->location->name) ? $this->location->name : $this->location_name;
?>
Guns in <?php echo $displayLocation; ?> - The Figures
</h1>

<?php foreach($this->dp_metadata as $key=>$val){

if( ignoreField($val) )
{
   continue;
}

if( !empty( $this->dp_data->{$val} ) ){
   echo getHTML( $val, $this->dp_data->{$val}, $this->dp_data->{$val.'_p'}, '', $displayLocation );
}

}
?>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</body>
</html>