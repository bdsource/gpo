<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$parts = array();

if( !empty( $this->citation->title ) )
{
	$parts['title'] = '<h1 title="title">' . $this->citation->title . '</h1>';
}


if( !empty( $this->citation->published ) )
{
	$str = date( 'j F Y', strtotime( $this->citation->published ) );
	$parts['published'] = '<span class="published" title="published">' . $str . '</span>';
}


if( !empty( $this->citation->subtitle ) )
{
	$parts['subtitle'] = '<span class="subtitle" title="subtitle">' . $this->citation->subtitle . '</span>';
}

if( !empty( $this->citation->source ) || !empty( $this->creation->category ) )
{
	$str = '';
	if ( !empty( $this->citation->source ) )
	{
//		$str .= '<span class="source" title="source">' . $this->citation->source .'</span>';
		$str .= '<span class="source" title="source">' . $this->citation->source;
	}
	if ( !empty( $this->citation->category ) )
	{
		//added comma with space before source, category
		$str .= ( ( !empty( $str ) ) ? ", " : "<span>"  )  . $this->citation->category .'</span>';
	}else{
		$str .= '</span>';
	}
	$parts['source_category'] = $str;
}	


if( !empty( $this->citation->byline ) )
{
	$parts['byline'] = '<span class="byline" title="byline">By ' . $this->citation->byline . '</span>';
}

$order = explode(",", "title,subtitle,source_category,published,byline" );
/*
if( empty( $this->citation->byline ) )
{
	$order = explode(",", "title,subtitle,source_category,byline" );
}else{
	$order = explode(",", "title,subtitle,source_category,byline" );	
}

*/

$html ='';
foreach( $order as $item )
{
//	$html .= ( isset( $parts[$item] )  )? $parts[$item] . "<br />": '';
	$html .= ( isset( $parts[$item] )  )? $parts[$item] . "": '';	
}	

$html_link = '';
if( !empty( $this->citation->websource ) )
{
	$html_link .= '<a href="' . $this->citation->websource . '" title="" target="_blank">' . $this->citation->websource . '</a>';
}


$html_content = '';
$html_content = str_replace( "\r\n", "\n", $this->citation->content );
$html_content = str_replace( "\n", "<br />", $html_content );

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Alpers.org | <?php echo GpoEndWith( ".", $this->citation->title ); ?></title>
<style>
body
{
	background: #fff;
	color: #333333;
	font-family: arial, helvetica, sans-serif;
	font-size: 100%;
	line-height : 1em;
	text-align: center;
	padding:0px;
	margin:0px;	
}


h3{
/* color:#BC751D; */
color:#E8921E;
font-size:1.3em;
font-weight:normal;
}

/*h3{	font-size:80%; color: #E8921E; }*/
h1{ 
	color: #048DD4; font-size:1.2em; 
}

p { margin:5px 0;  }

p, span{ line-height:1.2em; font-size:80%; }


#citation h1{ line-height:1.1em; font-size:1em; color: blue; padding:0px; margin:0px; }
#citation span{
	color: #000000;
	display:block;
}
div#wrapper{
	padding:0;
	margin:0;
	width:550px;
	margin-left:30px;
}

div#header{
	margin:0;
	padding:0px;
	width:100%;
	background-color:#3399fe;
	text-align: left;	
}
div#header img{
	margin:0px;
	padding:0px;
}

div#footer{
	background-color:#dddddd;
	color:#777777;
	padding:1px;
}

div#footer p{
	text-align:center;
	font-size:90%;
	font-weight:400;
}

#content{
	padding-top:10px;	
	text-align:left;
}

#citation-link{
	padding-top:10px;	
	text-align:left;
	width:100%;	
}

#warning{
	text-align:left;
}
#warning p{
	font-size:60%;
	color: #048DD4;
}


#citation-id{
	text-align:right;
	margin-top:10px;	
	margin-bottom:10px;	
	font-size:60%;
	color:#999;	
	font-weight:bold;	
}
span.logo{
	font-size:inherit;
	color: #E8921E;
}
span.subtitle{
	font-weight:bold;
}
span.source{}

.clear{
	clear:all;
	clear:both;	
}
</style>	
	</head>
	<body>
<div id="wrapper">
<div id="header">
	<img src="//www.gunpolicy.org/images/gpo_logo.gif" width="260" height="73" alt="alpers.org popup logo"/>
</div>

<div id="content">
	
<h3 style="color:#048DD4;">Citation from the Alpers News media archive</h3>

<?php if( !empty( $this->citation->id ) ): ?>

<div id="citation">
	<?php echo $html; ?>
</div>

<?php if( !empty( $html_content ) ): ?>
<div id="content">
<h3>Relevant contents</h3>
<p><?php echo $html_content; ?></p>
</div>
<?php endif; ?>


<?php if( !empty( $this->citation->websource ) ): ?>
<div id="citation-link">
    <p><?php echo JText::_('COM_GPO_NEWS_PUBLISHERS_LINK');?> <?php echo $html_link; ?></p>
</div>
<?php endif; ?>


<?php else: ?>
<p>The citation you are looking for seems to have gone.</p>
<?php endif; ?>
</div>

<div id="citation-id">
<span>ID: N<?php echo $this->citation->id;?></span>
</div>
<div class="clear"></div>
<div>
<p style="color:#048DD4;">As many publishers change their links, archive their pages and charge for access after a time, we regret that the full-text versions of some articles may no longer be available from the original web link. If this is the case, please consult the publisher's web site, or use your favourite search engine.</p>
</div>
<div class="clear"></div>
<div id="footer">
  <p>
  <span class="logo">Alpers.org</span> provides evidence-based, public health-oriented information on<br />
  gun violence, small arms policy and firearm-related injury around the world.<br />
  © Alpers.org <?php echo date("Y"); ?>
  </p>
</div>

</div>
</body>
</html>