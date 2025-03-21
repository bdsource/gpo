<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );


$parts = array();

if( !empty( $this->citation->author ) )
{
	$str = GpoEndWith( ".", $this->citation->author );
	$parts['author'] = '<span class="author" title="author">' . $str . '</span> ';
}

if( !empty( $this->citation->published ) )
{
	$str = date( 'Y', strtotime( $this->citation->published ) ) . '.';
	$parts['published_year'] = '<span class="published" title="published year">' . $str . '</span> ';

	$str = date( 'F', strtotime( $this->citation->published ) ) . '.';				
	$parts['published_daymonth'] = '<span class="published" title="published month">' . $str . '</span>';
}

if( !empty( $this->citation->title ) )
{
	$str = GpoEndWith( ".", $this->citation->title );
	$parts['title'] = '<span class="title" title="title">&lsquo;' . $str . '&rsquo; </span>';
}

if( !empty( $this->citation->source ) )
{
	$str = $this->citation->source;
	$parts['source'] = '<span class="source" title="source">' . $str .'</span>';
}


if( !empty( $this->citation->volume ) )
{
	$parts['volume'] = '<span class="volume" title="volume">; ' . $this->citation->volume .'</span>';
	if( !empty( $this->citation->issue ) )
	{
		$parts['volume'] .=	" ";
	}
}
if( !empty( $this->citation->issue ) )
{
	$parts['issue'] = '<span class="issue" title="issue">(' . $this->citation->issue .')</span>';
}
if( !empty( $this->citation->page ) )
{
	$str = GpoEndWith( ".", $this->citation->page );				
	$parts['page'] = ', <span class="page" title="page">' . $str .'</span> ';
}

/*quick fix for source not ending in . */
if( empty( $this->citation->volume ) && empty( $this->citation->issue ) && empty( $this->citation->page ) )
{
	$str = GpoEndWith( ".", $this->citation->source );
	$parts['source'] = '<span class="source" title="source">' . $str .'</span>';
}


if( !empty( $this->citation->city ) )
{
//add the space here
	$parts['city'] = ' <span class="city" title="city">' . $this->citation->city .':</span> ';
}
if( !empty( $this->citation->publisher ) )
{		
	$str = GpoEndWith( ".", $this->citation->publisher );
	$parts['publisher'] = '<span class="publisher" title="publisher">' . $str . '</span> ';
}
//ftp_debug( $this->citation, 'citation', true, false );
$order = explode(",", "author,published_year,title,source,volume,issue,page,city,publisher,published_daymonth" );

$html ='';
//ftp_debug( $parts,'parts',true,false );
foreach( $order as $item )
{
	$html .= ( isset( $parts[$item] )  )? $parts[$item] : '';
}	

$html_link = '';
if( !empty( $this->citation->websource ) )
{
	$html_link= 'Last accessed ' . date( 'j F Y', strtotime( $this->citation->modified ) ) . ' at:<br /><a href="' . $this->citation->websource . '" title="" target="_blank">' . $this->citation->websource . '</a>';
}

$html_content = '';
$html_content = str_replace( "\r\n", "\n", $this->citation->content );
$html_content = str_replace( "\n", "<br />", $html_content );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Citation from the Alpers.org literature library</title>
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
h1{ color: #048DD4; font-size:1.2em; }
p { margin:5px 0;  }

p, span{ line-height:1.2em; font-size:80%; }


#citation h1{ line-height:1.2em; font-size:1em; color: #333333; font-weight:normal; }
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
span.source{
	font-style:italic;
}
</style>
	</head>
	<body>
<div id="wrapper">
<div id="header">
	<img src="//www.gunpolicy.org/images/gpo_logo.gif" width="260" height="73" alt="gunpolicy.org popup logo"/>
</div>

<div id="content">
<h3 style="color:#048DD4;">Citation from the <span class="logo">Alpers.org</span> literature library</h3>	
<?php if( !empty( $this->citation->id ) ): ?>

<div id="citation"><h1><?php echo $html; ?></h1></div>



<?php if( !empty( $html_content ) ): ?>
<div id="content">
<h3>Relevant contents</h3>
<p><?php echo $html_content; ?></p>
</div>
<?php endif; ?>


<?php if( !empty( $this->citation->websource ) ): ?>
<div id="citation-link">
	<p><?php echo $html_link; ?></p>
</div>
<?php endif; ?>


<?php else: ?>
<p>The citation you are looking for seems to have gone.</p>
<?php endif; ?>
<div id="citation-id">
<span>ID: Q<?php echo $this->citation->id;?></span>
</div>
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