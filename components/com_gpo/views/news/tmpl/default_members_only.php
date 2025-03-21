<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$u =& Joomla\CMS\Uri\Uri::getInstance();
$jView = new JViewLegacy();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >


<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<?php echo GpoDefaultRssFeeds(); ?>
	<!--
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/gunpolicy/css/template.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/gunpolicy/css/position.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/gunpolicy/css/layout.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/gunpolicy/css/general.css" type="text/css" />
	-->
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/gunpolicy/css/citation-news.css?v=2" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/gunpolicy/css/print_popup.css" type="text/css" media="Print" />
	<!--[if lte IE 6]>
		<link href="<?php echo $this->baseurl ?>/templates/gunpolicy/css/ieonly.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<!--[if IE 7]>
		<link href="<?php echo $this->baseurl ?>/templates/gunpolicy/css/ie7only.css" rel="stylesheet" type="text/css" />
	<![endif]-->

</head>
<body>
<div id="wrapper">
<div id="header">
<a name="top_of_page"></a>
<a href="/" title="Go to our Home Page">
<img src="<?php echo $this->baseurl ?>/templates/gunpolicy/images/logo.gif" border="0"  />
</a>
</div><!-- header -->

<div id="content"><!-- content -->

<h6>
   Restricted Access (Admin Only)
</h6>
    <div class="buttonbar">
      <a class="buttonlink print" href="javascript: window.print();">Print</a> 
      <a class="buttonlink close" href="javascript: self.close();">Close</a>
    </div>


<?php 
if ( isset( $this->oNews->locations['0'] ) ):
echo '<p style="margin-top:18px;">';
$size = count( $this->oNews->locations )-1;
foreach(  $this->oNews->locations as $key => $location ):
$url = JRoute::_( 'index.php?option=com_gpo&task=search' );
$url .= '?q=&l=' . $location;
$str = $jView->escape( $location );
$str .= ( ( $size !== $key ) ? ",":'' );
echo '<a href="' . $url . '" title="Link to articles related to ' . $jView->escape( $location ) .'">' . $str . '</a>';
echo ( ( $size !== $key ) ? " " : '' );
endforeach;
echo '</p>';
endif;
?>

<?php if( !empty($this->oNews->title) ): ?>
<h2 style="margin-top:0px;">
<?php echo $this->oNews->title; ?>
</h2>
<?php endif; ?>

<?php
if ( !empty($this->oNews->subtitle) ) {
	echo  '<p><strong>' . $this->oNews->subtitle . '</strong></p>';
}
?>

<?php
if ( !empty($this->oNews->source) ) {
	echo  '<p><strong>' . $this->oNews->source;
    if ( !empty($this->oNews->category) ) :
	    echo  ', ' . $this->oNews->category;
    endif;
	echo '</strong></p>';
} else {
	if ( !empty($this->oNews->category) ) :
		echo  '<p><strong>' . $this->oNews->category . '</strong></p>';
	endif;
}
?>

<?php echo '<p><strong>' . date( 'j F Y', strtotime( $this->oNews->published ) ) . '</strong></p>';?>

<?php
if ( !empty($this->oNews->byline) ) {
	echo  '<p><strong>By ' . $this->oNews->byline . '</strong></p>';
}
?>


<div style="padding-top: 5px;">&nbsp;&nbsp;</div>

<p>
<?php
echo gpo_helper::full_length( $this->oNews->content );
?>
</p>

<h3>Web Source</h3>
<p>
<?php if( $this->oNews->websource !== 'No Web Source' && $this->oNews->websource !== 'NoWebSource'):?>
    <?php echo addHTTP($this->oNews->websource); ?>
<?php else: ?>
No URL available 
<?php endif; ?>
</p>

<h3>Gun Policy News Header</h3>
<p><?php echo $jView->escape( $this->oNews->gpnheader ); ?></p>
<div id="citation-id">
      ID: <?php echo $this->oNews->id;?>
</div>

<div style="padding-bottom:10px;" class="clear"></div>

<hr>
<p style="text-align:center;" align="center">
FOR NON-COMMERCIAL, ACADEMIC OR PRIVATE USE ONLY<br>
This item is provided by Alpers News (www.alpers.org) for reference
purposes only, under the 'fair use' provisions of copyright legislation.
Before gaining access to this page, the reader has agreed to observe the
intellectual property provisions which apply in the appropriate
jurisdiction(s).
</p>
<hr>

</div><!-- content -->


<div style="padding-bottom:10px;" class="clear"></div>

<!-- footer -->
<div id="footer">
  <p>
  <span style="color: #E8921E;"><?php echo Joomla\CMS\Factory::getApplication()->getCfg('sitename'); ?></span> provides evidence-based, public health-oriented information on<br />
  gun violence, small arms policy and firearm-related injury around the world.<br />
 &copy; <?php echo Joomla\CMS\Factory::getApplication()->getCfg('sitename'); ?> <?php echo date("Y"); ?>  </p>
</div><!-- footer -->


</div><!-- wrapper -->
	
</body>
</html>
