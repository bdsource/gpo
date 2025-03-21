<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe = JFactory::getApplication();
$mainframe->registerEvent( 'onAfterDisplayContent', 'plgGpoCitationMaker' );

function plgGpoCitationMaker( &$article2 )
{
	$article=&$article2[0];
    //replace all the ^^ sign from the value if it is surrounded by double caret (^xx^)
    $pattern = '/\^(.*)\^/';

    if(preg_match_all($pattern,$article->text,$matches)){
        $article->text = str_replace($matches[0],$matches[1],$article->text);
    }

    //now find the citation ID
	$format = "{%s}";
	$pattern = '/\{([a-z][0-9]{1,11})\}/';
//echo '<div style="display:none; ">';
//print_r($article->text);
//echo '</div>';die();			
	preg_match_all( $pattern, $article->text,$matches );
	if( count( $matches['1']) == 0 )
	{
		return;
	}
  
	
	$citations = array();
	$from = array();
	$to = array();
	
	$types = array(
						's'=>array(),	
						'q'=>array(),
						'n'=>array(),
                        'g'=>array()
				  );
	
	
	foreach( $matches['1'] as $citation )
	{
		$key = substr($citation,0,1);
		$id  = substr($citation,1);
		
		if( isset( $types[$key] ) && !in_array( $id, $types[$key] )  )
		{
			$cite_id = sprintf( $format, $citation );
			$types[$key][] = $id;
			$from[$cite_id] =  $cite_id;
			$to[$cite_id] = '';
			$citations[$cite_id] = '';
		}
	}

//	echo '<pre>' . print_r( $from, true ) . '</pre>';
//	echo '<pre>' . print_r( $to, true ) . '</pre>';
//	echo '<pre>' . print_r( $citations, true ) . '</pre>';

	
//	$oUser	= & JFactory::getUser();	
//	echo '<pre>' . print_r( $oUser, true ) . '</pre>';exit();

//	$extra_sql = " AND `c`.`share`='0' ";	
//	$extra_sql = " AND `c`.`share`='1' ";
	$extra_sql = "";
	
	$db 		= & JFactory::getDBO();

	$citations = array();
//get and create specific links for each type
	if( isset( $types['n']['0'] ) )	
	{
		$ids= implode( ",", $types['n'] );
		$query = "SELECT 'n' as `type`,'news' as `type_long`,`c`.* FROM `#__gpo_citations_news` as `c` WHERE `c`.`id` IN ( " . $ids . " ) " . $extra_sql . ";";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if( isset( $items['0'] )  )
		{
			foreach( $items as $item )
			{
				$cite_id = sprintf( $format, 'n' . $item->id );
				$citations[$cite_id]=$item;
			}
		}
	}

	if( isset( $types['q']['0'] ) )
	{
		$ids= implode( ",", $types['q'] );
		$query = "SELECT 'q' as `type`,'quotes' as `type_long`,`c`.* FROM `#__gpo_citations_quotes` as `c` WHERE `c`.`id` IN ( " . $ids . " ) " . $extra_sql . ";";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if( isset( $items['0'] )  )
		{
			foreach( $items as $item )
			{
				$cite_id = sprintf( $format, 'q' . $item->id );
				$citations[$cite_id]=$item;
			}
		}
	}
  
    if( isset( $types['g']['0'] ) )
	{
		$ids= implode( ",", $types['g'] );
    
    	$query = "SELECT 'g' as `type`,'glossary' as `type_long`,`c`.* FROM `#__gpo_datapage_glossary` as `c` WHERE `c`.`id` IN ( " . $ids . " ) ;";
		$db->setQuery($query);
		$items = $db->loadObjectList();
    
		if( isset( $items['0'] )  )
		{
			foreach( $items as $item )
			{
				$cite_id = sprintf( $format, 'g' . $item->id );
				$citations[$cite_id]=$item;
			}
		}
	}
	
	// this check needed if incorrect Itemid is given resulting in an incorrect result
	$include_reference = false;
//	echo '<pre>' . print_r( $citations, true ) . '</pre>';
	if( count( $citations )  )
		$include_reference = true;
		ob_start();
?>
<style>
dl.references{
	display:block;
}
.references span{
	padding-right:2px;
	font-size:10px;
}
.references dd p{
	font-size:70%;
line-height:1.2em;
margin-bottom:5px;
padding-left:5px;
}

.references .source{
	font-style:italic;
}
.references .link{
	padding:0px auto;
	padding-left:2px;
}
dl.references dt{
	float:left;
	font-size:10px;	
	display:inline;

}
dl.references dd{
	display:block;
	padding-left:20px;	

}
</style>

<h3><?php echo JText::_('COM_GPO_PLG_CITATION_HEADER');?></h3>
<dl class="references">
	<?php
		$i=1;
		foreach( $from as $key ):
    
			$citation = '';
			if( isset( $citations[$key] ) )
			{
				$citation = $citations[$key];
			}
			if( empty( $citation ) )
			{
				continue;
			}

			if( !empty( $citation->title ) )
			{
				$link_info = "Show citation: " . GpoEndWith( ". ", $citation->author). GpoEndWith('. ', date('Y', strtotime($citation->published))). '‘'.GpoEndWith('.', $citation->title ) .'’ ' . GpoEndWith(', ', $citation->source) . GpoEndWith('. ', $citation->volume). GpoEndWith('. ', $citation->issue). GpoEndWith('. ', $citation->page). GpoEndWith(': ', $citation->city) .  GpoEndWith('. ', $citation->publisher) . GpoEndWith('. ', date('j F', strtotime($citation->published))). '('.strtoupper($citation->type).$citation->id.')';
			}else{
                $link_info = "Show citation: " . GpoEndWith( ". ", $citation->author). GpoEndWith('. ', date('Y', strtotime($citation->published))). GpoEndWith(', ', $citation->source) . GpoEndWith('. ', $citation->volume). GpoEndWith('. ', $citation->issue). GpoEndWith('. ', $citation->page). GpoEndWith(': ', $citation->city) .  GpoEndWith('. ', $citation->publisher) . GpoEndWith('. ', date('j F', strtotime($citation->published))). '('.strtoupper($citation->type).$citation->id.')';
			}			
			$cite_id = $key;
            //&lsquo; &rsquo;
      if($citation->type_long=='glossary'){
        $link_info = "Show citation: " . GpoEndWith( ". ", $citation->author = "GunPolicy.org"). GpoEndWith('. ', date('Y', strtotime($citation->modified))). '‘'.GpoEndWith('\'', $citation->title ) .'. ' . GpoEndWith('. ', $citation->subtitle) . GpoEndWith(', ', $citation->city="Sydney") . GpoEndWith('. ', date('j F', strtotime($citation->modified))). '('.strtoupper($citation->type).$citation->id.')';
        $to[ $cite_id ]= '<sup><a class="incontent-citation" href="javascript:popup=window.open(\'' . JRoute::_( 'index.php?option=com_gpo&task=glossary&type=' . $citation->type_long . '&id=' . $citation->id, false ) . '\',\'GunPolicyCitation\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600\'); popup.focus();" title="' . htmlspecialchars($link_info) . '">' . $i . '</a></sup>';
      }else{
        $to[ $cite_id ]= '<sup><a class="incontent-citation" href="javascript:popup=window.open(\'' . JRoute::_( 'index.php?option=com_gpo&task=citation&type=' . $citation->type_long . '&id=' . $citation->id, false ) . '\',\'GunPolicyCitation\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600\'); popup.focus();" title="' . htmlspecialchars($link_info) . '">' . $i . '</a></sup>';
      }
?>
<dt><?php echo $i . "."; ?></dt>
<dd>
<?php		switch( $citation->type ): ?>
<?php 		case 'n':  //news 


			$parts = array();
			
			if( !empty( $citation->byline ) )
			{
				$str = GpoEndWith( ".", $citation->byline );
				$parts['byline'] = '<span class="author" title="byline">' . $str . '</span>';
			}
			
			if( !empty( $citation->published ) )
			{
				$str = date( 'Y', strtotime( $citation->published ) ) . '.';
				$parts['published_year'] = '<span class="published" title="published year">' . $str . '</span>';

				$str = date( 'j F', strtotime( $citation->published ) ) . '.';				
				$parts['published_daymonth'] = '<span class="published" title="published day month">' . $str . '</span>';
			}

			if( !empty( $citation->title ) )
			{
				$str = GpoEndWith( ".", $citation->title );
				$parts['title'] = '<span class="title" title="title">&lsquo;' . $str . '&rsquo; </span>';
			}

			if( !empty( $citation->source ) )
			{
				$str = GpoEndWith( ".", $citation->source );
				$parts['source'] = '<span class="source" title="source">' . $str .'</span>';
			}


            if( !empty( $citation->id ) )
            {
                $parts['id'] = '<span class="id" title="ID">('.ucfirst($citation->type).$citation->id.')</span>';
            }

			if( !empty( $citation->title ) )
			{
				$link_info = "Show citation: &lsquo;" . GpoEndWith( ".", htmlspecialchars($citation->title) ) . "&rsquo;";
			}else{
				$link_info = "Show citation: &lsquo;" . GpoEndWith( ".", htmlspecialchars($citation->source) ) . "&rsquo;";
			}
			
			$parts['link'] = '<a class="link" href="javascript:popup=window.open(\'' . JRoute::_( 'index.php?option=com_gpo&task=citation&type=' . $citation->type_long . '&id=' . $citation->id, false ) . '\',\'GunPolicyCitation\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600\'); popup.focus();" title="' . $link_info . '">Full Citation</a>';														
			
			
			if( !empty( $citation->byline ) )
			{
				$order = explode(",", "byline,published_year,title,source,published_daymonth,id,link" );
			}else {
				$order = explode(",", "source,published_year,title,published_daymonth,id,link" );
			}
			
			$html ='';
			foreach( $order as $item )
			{
				$html .= ( isset( $parts[$item] )  )? ' ' . $parts[$item] : '';
			}
			
			?>
		<p>
			<?php echo $html; ?>
		</p>
<?php 			break; ?>
<?php 		case 'q': 

			$parts = array();
			
			if( !empty( $citation->author ) )
			{
				$str = GpoEndWith( ".", $citation->author );
				$parts['author'] = '<span class="author" title="author">' . $str . '</span>';
			}
			
			if( !empty( $citation->published ) )
			{
				$str = date( 'Y', strtotime( $citation->published ) ) . '.';
				$parts['published_year'] = '<span class="published" title="published year">' . $str . '</span>';

				$str = date( 'j F', strtotime( $citation->published ) ) . '.';				
				$parts['published_daymonth'] = '<span class="published" title="published day month">' . $str . '</span>';
			}

			if( !empty( $citation->title ) )
			{
				$str = GpoEndWith( ".", $citation->title );
				$parts['title'] = '<span class="title" title="title">&lsquo;' . $str . '&rsquo; </span>';
			}

			if( !empty( $citation->source ) )
			{
				$str = GpoEndWith( ".", $citation->source );
				$parts['source'] = '<span class="source" title="source">' . $str .'</span>';
			}
/*
			if( !empty( $citation->volume ) )
			{
				$parts['volume'] = '<span class="volume" title="volume">; ' . $citation->volume .'</span>';
			}
			if( !empty( $citation->issue ) )
			{
				$parts['issue'] = '<span class="issue" title="issue">(' . $citation->issue .')</span>';
			}
			if( !empty( $citation->page ) )
			{
				$str = GpoEndWith( ".", $citation->page );				
				$parts['page'] = '<span class="page" title="page">:' . $str .'</span>';
			}
*/			
			if( !empty( $citation->city ) )
			{		
				$parts['city'] = '<span class="city" title="city">' . $citation->city .':</span>';
			}
			if( !empty( $citation->publisher ) )
			{		
				$str = GpoEndWith( ",", $citation->publisher );
				$parts['publisher'] = '<span class="publisher" title="publisher">' . $str . '</span>';
			}

                        if( !empty( $citation->id ) )
                        {
                                $parts['id'] = '<span class="id" title="ID"> ('.ucfirst($citation->type).$citation->id.')</span>';
                        }
			
					
			if( !empty( $citation->title ) )
			{
				$link_info = "Show citation: " . GpoEndWith( ". ", $citation->author). '‘'.GpoEndWith('.’', $citation->title ) . GpoEndWith('. ', $citation->publisher) . GpoEndWith(')', '('.strtoupper($citation->type).$citation->id);
			}else{
                $link_info = "Show citation: " . GpoEndWith( ". ", $citation->author). GpoEndWith('. ', $citation->source ) . GpoEndWith('. ', $citation->publisher) . GpoEndWith(')', ' ('.strtoupper($citation->type).$citation->id);
			}
			
			$parts['link'] = '<a class="link" href="javascript:popup=window.open(\'' . JRoute::_( 'index.php?option=com_gpo&task=citation&type=' . $citation->type_long . '&id=' . $citation->id, false ) . '\',\'GunPolicyCitation\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600\'); popup.focus();" title="' . htmlspecialchars($link_info) . '">Full Citation</a>';														
			if( !empty( $citation->author ) )
			{
				$order = explode(",", "author,published_year,title,source,city,publisher,published_daymonth,id,link" );
			}else {
				$order = explode(",", "publisher,published_year,title,source,city,published_daymonth,id,link" );
			}
			
//			$html = $i . ") ";
			$html ='';
			foreach( $order as $item )
			{
				$html .= ( isset( $parts[$item] )  )? $parts[$item] : '';
			}
			
			?>
			
		<p>
			<?php echo $html; ?>
		</p>
<?php 			break; ?>
<?php 		case 'g':  //glossary 


			$parts = array();
			
			if( !empty( $citation->modified ) )
			{
				$str = date( 'Y', strtotime( $citation->modified ) ) . '.';
				$parts['published_year'] = '<span class="published" title="published year">'.$citation->author.'. ' . $str . '</span>';

				$str = date( 'j F', strtotime( $citation->modified ) ) . '.';				
				$parts['published_daymonth'] = '<span class="published" title="published day month"><i>'.$citation->subtitle.'.</i><br />Sydney School of Public Health, ' . $str . '</span>';
			}

			if( !empty( $citation->title ) )
			{
				$str = GpoEndWith( ".", $citation->title );
				$parts['title'] = '<span class="title" title="title">&lsquo;' . $str . '&rsquo; </span>';
			}

			if( !empty( $citation->source ) )
			{
				$str = GpoEndWith( ".", $citation->source );
				$parts['source'] = '<span class="source" title="source">' . $str .'</span>';
			}


            if( !empty( $citation->id ) )
            {
                $parts['id'] = '<span class="id" title="ID">('.ucfirst($citation->type).$citation->id.')</span>';
            }

			if( !empty( $citation->title ) )
			{
				$link_info = "Show citation: &lsquo;" . GpoEndWith( ".", htmlspecialchars($citation->title) ) . "&rsquo;";
			}else{
				$link_info = "Show citation: &lsquo;" . GpoEndWith( ".", htmlspecialchars($citation->source) ) . "&rsquo;";
			}
			
			$parts['link'] = '<a class="link" href="javascript:popup=window.open(\'' . JRoute::_( 'index.php?option=com_gpo&task=glossary&type=' . $citation->type_long . '&id=' . $citation->id, false ) . '\',\'GunPolicyCitation\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600\'); popup.focus();" title="' . $link_info . '">Full Citation</a>';														
			
			$html ='';
			foreach( $order as $item )
			{
				$html .= ( isset( $parts[$item] )  )? ' ' . $parts[$item] : '';
			}
			?>
		<p>
			<?php echo $html; ?>
		</p>
<?php 			break; ?>
<?php 		endswitch; ?>
</dd>
	<?php
	++$i; 
	endforeach; 
	?>
</dl>
<?php
	$html = ob_get_contents();
	ob_end_clean();
	$article->text = str_replace( $from, $to, $article->text);
	
	if( $include_reference === true )
	{
		$article->citations = '<div id="short_references">' . $html . '</div>';	
	}
}
?>