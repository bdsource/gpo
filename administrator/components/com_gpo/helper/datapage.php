<?php

   function clean_data($p_value){
	  if( empty($p_value) ){
		 return $p_value;
	  }
	  
	  // 1. replace any preceding space before opening squiggly bracket. e.g. "is {q123}" => is{q123}	
	  // 2. add a space in between an closing & opening squiggly bracket. e.g. is{q1}{q2} => is{q1} {q2}
      // 3. check if any double space and replace with single space
	  $trans = array(
						"\s+{"     => '{',
						"}{"	   => '} {',
                        "\s{2}"  => ' '
			   );
          
	  foreach ($trans as $key => $val){
		 $p_value = preg_replace("#".$key."#", $val, $p_value);
	      }
	  return $p_value;
   }
   
   
   function clean_data_type($p_value) {
	  if( empty($p_value) ){
		 return $p_value;
	  }
	  
	  // 1. replace any preceding space before opening squiggly bracket. e.g. "is {q123}" => is{q123}	
	  // 2. add a space in between an closing & opening squiggly bracket. e.g. is{q1}{q2} => is{q1} {q2}
      // 3. check is any double space and replace with single space
	  $trans = array(
					  "\s+{"	=> '{',
					  "}{"	=> '} {',
                      "\s{2}"  => ' '
			   );
      $tt = 1;
	  foreach ($trans as $key => $val){
		 preg_match_all("#".$key."#",$p_value,$temp);
         if(!empty($temp[0])){
            $type = $tt;
         }
         $tt++;
		 $p_value = preg_replace("#".$key."#", $val, $p_value);
	  }
      
      $res['val'] = $p_value;
      $res['type'] = $type;
	  return $res;
   }


   function is_any_missing_q_or_n_cite_found( $p_value )
   {
	  if( empty($p_value) )
	  {
		 return false;
	  }
		
      //search for any squiggly bracket not followed by any q or n cite. e.g. {456} or { 123}
	  $search = "{\s*[0-9]+";
	  
	  $p_result = preg_match( "#".$search."#", $p_value );
	  
	  return $p_result;
   }    
   /*
    * 
    * whether the user can delete a DP column or not
    *
    */
   function canDeleteColumn( )
   {
   	  $oUser = & JFactory::getUser();
   	  
   	  if( $oUser->get('isRoot') )
   	  {
   		  return TRUE;
   	  }
   	  return FALSE;
   }


/**
     * Create URL Title
     *
     * Takes a "title" string as input and creates a
     * human-friendly URL string with either a dash
     * or an underscore as the word separator.
     *
     * @access	public
     * @param	string	the string
     * @param	string	the separator: dash, or underscore
     * @return	string
     */
     
	function field_title($str, $separator = 'underscore')
	{
		if ($separator == 'dash')
		{
			$search		= '_';
			$replace	= '-';
		}
		else
		{
			$search		= '-';
			$replace	= '_';
		}
		
		$trans = array(
						$search								=> $replace,
						"\s+"								=> $replace,
						"[^a-z0-9".$replace."]"				=> '',
						$replace."+"						=> $replace,
						$replace."$"						=> '',
						"^".$replace						=> ''
					   );

		$str = strip_tags(strtolower($str));
	
		foreach ($trans as $key => $val)
		{
			$str = preg_replace("#".$key."#", $val, $str);
		}
	
		return trim(stripslashes($str));
	}
	
	
	function is_nondata_column($field_name)
	{
       $readonly_fields = array(
        			 'id',
                                 'location_id',
                                 'location',
                                 'published',
                                 'created_at',
                                 'updated_at',
                                 'published_at'
                          );	
       if(in_array($field_name,$readonly_fields)){
          return true;
       }
       return false;
    }
	
	function camelize( $str )
    {
   	 $str = 'x'.strtolower(trim($str));
   	 $str = ucwords(preg_replace('/[\s_]+/', ' ', $str));
   	 $str = ucfirst( substr($str, 1) );
	 $str = str_replace( array( 'Unpoa', 'Public Hidden Or Openly', 'Any Method', 'Ak-47'), 
					     array( 'UNPoA', 'Public, Hidden or Openly', '(any method)', 'AK-47'), 
					     $str );
					     
	 /* replace prepositions & articles */ 
	 $explode = explode(' ', $str);
	 $ap_array = array( 'Of', 'And', 'From', 'On', 'In', 'For', 'To', 'An', 'At', 'A', 'An', 'The' );
	 $ap_replace_array = array( 'of', 'and', 'from', 'on', 'in', 'for', 'to', 'an', 'at', 'a', 'an', 'the' );
	 foreach ( $explode as $key => $val ) {
	 	if( in_array($val, $ap_array) ) {
	 	     $explode[$key] = str_replace( $ap_array, $ap_replace_array, $val );
	 	}   
	 }
	 $str = implode(' ', $explode);
	 
	 return $str;
   }
   
   function getDPColumnTitles($language='en')
   {
      $columnTitle = 'column_title';
      $languages = array('es','fr');
      
   	  $db = &JFactory::getDBO();
   	  $query = "SELECT * FROM `#__gpo_datapage_hierarchy`";
   	  $db->setQuery( $query );
   	  $data = $db->loadObjectList();
   	  $columns_titles = array();
   	  foreach( $data as $row ) {
        if( in_arrayi($language, $languages) ) {
            $columnTitle = 'column_title_' . $language;
        }
   		$column_title = empty($row->{$columnTitle}) ? camelize( $row->column_name ) : $row->{$columnTitle};
   	  	$columns_titles[$row->column_name] = $column_title;
   	  }
      
   	  return $columns_titles;
   }
   
   function getDPColumnInfoById($columnId)
   {
      if( empty($columnId) ) {
          return false;
      }
      
   	  $db = &JFactory::getDBO();
   	  $query = "SELECT * FROM `#__gpo_datapage_hierarchy` WHERE id = " . $columnId;
   	  $db->setQuery( $query );
   	  $data = $db->loadObject();
      
   	  return $data;
   }
     
   //php curl function 
   function getLastModified($websource){
               $ch = curl_init($websource);
               $headers = array( 
                 "Cache-Control: no-cache", 
                ); 
               curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
               curl_setopt($ch, CURLOPT_HEADER, TRUE);
               curl_setopt($ch, CURLOPT_NOBODY, FALSE);
               curl_setopt($ch, CURLOPT_FILETIME, TRUE);
               curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
               
               
               $data = curl_exec($ch);
               sleep(1);
              
               $filetime = curl_getinfo($ch, CURLINFO_FILETIME);
                                     
               $needle = strpos($data,"</time>");
               $matches = null;                 
               $timetemp = substr($data,$needle-25,25);
                        
               preg_match('/\b(?:Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|(Nov|Dec)(?:ember)?) (?:\d{1,2}),? (?:19[7-9]\d|2\d{3})/', $timetemp, $matches);              
               $timeupdated = "";
                          
              if(!empty($matches)){
                   $timeupdated = $matches[0];                    
                 }
                 else{                         
                   $timeupdated = "Not Found";
               }
                        
               curl_close($curl);                                   
               $d = new DateTime( date('Y/m/d', $filetime) );
               $date =  $d->format('Y-m-d');// note at point on "u" 
               if($filetime!=-1){
                   $d = null;
                   return $date;
               }
                else{ 
                    $d = DateTime::createFromFormat('F j, Y', $timeupdated);
                     if(!empty($d)){
                          $date =  $d->format('Y-m-d');                     
                           return $date;
                       }
                       else{
                         return $timeupdated;
                   }
          }
   }
   
   
   
   
   function updategpotable($websource,$lastmodified){        
       //getting qcites id
        $db = &JFactory::getDBO();       
        $query  = "SELECT id FROM `j25_gpo_citations_quotes` WHERE websource = '".$websource."'";  
        $ret = $db->setQuery($query);
        $db->execute();
        $idlist = $db->loadObjectList();
        $rows = array(
                       'lastmodified'=> NULL,
                       'websource' => NULL, 
                       'qcitesid'=> NULL,
                       'lastcrawled'=> NULL
                   );        
        $rows['lastmodified'] = $lastmodified;
        $rows['websource']= $websource;
        $rows['qcitesid'] = $idlist[0]->id;
        $rows['lastcrawled'] = date("Y/m/d");                
        $ret = InsertTogpoWebsource($rows); 
        return $ret;
    } 
  
   
    function DeleteTableWebsource(){
                    $db = &JFactory::getDBO();                 
                    $query = "truncate j25_gpo_websource";
                    $ret = $db->setQuery($query);
                    $db->execute();        
    }
     
    function updatelastchecked($qcitesid){
                     $db = &JFactory::getDBO();                  
                     $date = date("Y/m/d");    
                     $query = "Update `j25_gpo_websource` set lastchecked='".$date."' where qcitesid='".$qcitesid."'"; 
                     $ret = $db->setQuery($query);
                     $db->execute(); 
             return $ret;
     }
   
    //Function to insert data into j25_gpo_websource   
    function InsertTogpoWebsource($rows){
            if(empty($rows['websource'])){
                    return false;
                     }
                    $db = &JFactory::getDBO(); 
                  
                  
                    $query = "select id,lastmodified from `j25_gpo_websource` where websource='".$rows['websource']."'";
                    
                    $ret = $db->setQuery($query);
                    $data = $db->loadObjectList();
   	                         
                   
                    if(empty($data)){                                  
                        
                         $query = "INSERT INTO `j25_gpo_websource` (`id`, `lastmodified`, `websource`, `qcitesid`, `lastcrawled`) VALUES (NULL,'".$rows['lastmodified']."', '".$rows['websource']."', '".$rows['qcitesid']."', '".$rows['lastcrawled']."')";                 
                         //$query = "Insert into j25_gpo_websource values ".$rows->lastmodified.", ".$rows->websource.", " .$rows->citationquotesid.";";
                         $ret = $db->setQuery($query);
                         $db->execute();                   
                 }                
                 else{
                     
                     if($rows['lastmodified']=="Not Found"){                       
                            $rows['lastmodified'] = $data[0]->lastmodified; 
                            $r = false;
                     }else{
                         $r = $ret;
                     }
                     
                       // var_dump($rows['qcitesid']); 
                        $query = "Update `j25_gpo_websource` set lastmodified='".$rows['lastmodified']."',lastcrawled='".$rows['lastcrawled']."' where websource='".$rows['websource']."'"; 
                         $ret = $db->setQuery($query);
                         $db->execute(); 
                                                
                 }
            return $r;          
    }
        
    function SelectfromgpoWebsource($filter_order, $filter_order_Dir,$selectedLocationName, $selectedwebsource){
               $db = &JFactory::getDBO();
               
               
               if($selectedLocationName ==='showall'){ 
                   $query = "Select j25_gpo_websource.websource,j25_gpo_websource.lastmodified,j25_gpo_websource.qcitesid, j25_gpo_websource.lastcrawled,j25_gpo_websource.lastchecked, j25_gpo_citations_quotes.id, j25_gpo_citations_quotes.author,j25_gpo_citations_quotes.websource from j25_gpo_websource RIGHT JOIN j25_gpo_citations_quotes ON j25_gpo_websource.qcitesid=j25_gpo_citations_quotes.id where j25_gpo_citations_quotes.websource LIKE '%".$selectedwebsource."%'";                   
                }
               else{
                   $query = "Select j25_gpo_websource.websource ,j25_gpo_websource.lastmodified,j25_gpo_websource.qcitesid, j25_gpo_websource.lastcrawled,j25_gpo_websource.lastchecked, j25_gpo_citations_quotes.id,j25_gpo_citations_quotes.author,j25_gpo_citations_quotes.websource from j25_gpo_websource RIGHT JOIN j25_gpo_citations_quotes ON j25_gpo_websource.qcitesid=j25_gpo_citations_quotes.id where j25_gpo_citations_quotes.websource LIKE '%".$selectedwebsource."%' and j25_gpo_citations_quotes.author LIKE '%".$selectedLocationName."%'";
                }                  
                                      
                    $query .= " ORDER BY {$db->quoteName($filter_order)} $filter_order_Dir ";                  
                    //$query = "Insert into j25_gpo_websource values ".$rows->lastmodified.", ".$rows->websource.", " .$rows->citationquotesid.";";
                    $result = $db->setQuery($query);  
                    $data = $db->loadObjectList();                      
                return $data;          
    }
    
    function getAllWebsourcefromCitations()
     {     
   	  $db = &JFactory::getDBO();
          //SELECT * FROM `j25_gpo_citations_quotes` WHERE `author` LIKE '%Alabama%' ORDER BY `id` ASC              	  
          $query = "SELECT * FROM `j25_gpo_citations_quotes` WHERE `websource` LIKE '%smartgunlaws.org%' ORDER BY `websource` DESC";      
   	  $db->setQuery( $query );
   	  $data = $db->loadObjectList();   
   	  return $data;
   } 
    
    
   
    
      
   function getCitationsByAuthor($jurisdictionName, $webURL=NULL)
     {
      if( empty($jurisdictionName) ) {
          return false;
      }
   	  $db = &JFactory::getDBO();
          //SELECT * FROM `j25_gpo_citations_quotes` WHERE `author` LIKE '%Alabama%' ORDER BY `id` ASC
          
   	  $query = "SELECT * FROM `#__gpo_citations_quotes` WHERE `author` LIKE '%". $jurisdictionName . "%' ";
          if(!empty($webURL)) {
             $query .= " AND `websource` LIKE '%" . $webURL . "%' ";
          }
          $query .= " ORDER BY `id` ASC";
   	  $db->setQuery( $query );
   	  $data = $db->loadObjectList();   
   	  return $data;
   } 
   /*
     * 
     * TO Check if a column/category is 
     * a external link type category
     * 
     */
    function isALinkCategory($columnName)
    {
      if( empty($columnName) ) {
          return false;
      }
      
   	  $db = &JFactory::getDBO();
   	  $query = "SELECT * FROM `#__gpo_datapage_hierarchy` WHERE `column_name` = " . $db->quote($columnName) . 
               " AND (external_hyperlink_name != '' OR external_hyperlink_name IS NOT NULL)";
   	  $db->setQuery( $query );
      $data = $db->loadObject();
      
      if( !empty($data->external_hyperlink_name) ) {
          return true;
      }
      
   	  return false;
    }
    
   
   
   function getDPColumnDisplayTypes( )
   {
   	  $db = &JFactory::getDBO();
   	  $query = "SELECT `id`,`column_name`,`display_type` FROM `#__gpo_datapage_hierarchy`";
   	  $db->setQuery( $query );
   	  $data = $db->loadObjectList();
   	  $display_types = array();
   	  foreach( $data as $row ) {
   	  	$display_types[$row->column_name] = $row->display_type;
   	  }
   	  return $display_types;
   }

   function getDPGCiteIds(){
       $db = &JFactory::getDBO();
   	  $query = "SELECT `id`,`column_name`,`gcite_id` FROM `#__gpo_datapage_hierarchy`";
   	  $db->setQuery( $query );
   	  $data = $db->loadObjectList();
   	  $gcite_id = array();
   	  foreach( $data as $row ) {
   	  	$gcite_id[$row->column_name] = $row->gcite_id;
   	  }
   	  return $gcite_id;
   }

   function is_readonly($field_name)
   {
     $readonly_fields = array('id','location_id','location');	
     if(in_array($field_name,$readonly_fields)){
       return true;
     }
     return false;
   }
   
   function ignoreField( $p_field_name )
   {
	$ignoreList = array( 
	                     'id',
	                     'location_id',
	                     'location',
	                     'published',
	                     'created_at',
	                     'updated_at',
	                     'published_at' 
	              );
	return in_array( $p_field_name, $ignoreList );
   }
   
    /*
    *
    * case insensitive search in array elements
    *
    */

    function in_arrayi($needle, $haystack)
    {
        $needle = strtolower($needle);
        foreach ($haystack as $value)
        {
            if (strtolower($value) == $needle) {
                return true;
            }
        }
        return false;
    }


    /*
    *
    * Add an extra the before the country or location
    * name fro the following cases; needed for DP browser
    * title and/or for the DP preambles
    *
    *
    */

    function isNeedToAddThe($p_location_name)
    {
        if (empty($p_location_name)) {
            return false;
        }
        $addTheArray = array(
            'Australian Capital Territory',
            'Bahamas',
            'Caribbean',
            'Cayman Islands',
            'Congo (DRC)',
            'Congo (ROC)',
            'Cook Islands',
            'Czech Republic',
            'District of Columbia',
            'Dominican Republic',
            'Faeroe Islands',
            'Falkland Islands',
            'Maldives',
            'Marshall Islands',
            'Netherlands',
            'Netherlands Antilles',
            'Northern Mariana Islands',
            'Northern Territory',
            'Philippines',
            'Solomon Islands',
            'United Arab Emirates',
            'United Kingdom',
            'United Nations',
            'United States',
            'Vatican',
            'Virgin Islands (UK)',
            'Virgin Islands (US)',
            'European Union'
        );
        return $this->in_arrayi($p_location_name, $addTheArray);
    }
   
   function getHTML($p_field, $p_val, $p_preamble, $p_type='', $p_location_name)
   {
   	$html = '<h3>' . camelize($p_field) . '</h3>';
   	$html .= '<p>';
   	if( strpos($p_val,';') !== false ){
            $p_val = '<p>' . str_replace(';','<br>',$p_val) . '</p>';
   	}
	
	if( !empty($p_location_name) )
	{
	  $addThe = isNeedToAddThe( $p_location_name );
	  $displayName = ($addThe) ? "the $p_location_name" : $p_location_name;
	  $p_preamble = str_replace( '#', $displayName, $p_preamble);
	}
   	
        preg_replace("/\[([^\[]+)\]/", "", $input_lines);
        preg_match_all("/\[([^\[]+)\]/", $input_lines, $output_array);
    
	if( strpos($p_preamble,'~') !== false )
   	{
   		$html .= str_replace( '~',$p_val,$p_preamble );
   	}
   	else
   	{

   		$html .= $p_preamble . ' ' . $p_val;

   	}

   	$html .= '</p>';
   	return $html;
   }


   function getTopLevelHeaders($type='all') {
   	   $db = &JFactory::getDBO();
   	   $topHeaders = array();
   	   
   	   $query = "SELECT 
		              `#__gpo_datapage_hierarchy`.id,
		              `#__gpo_datapage_hierarchy`.column_name,
		              `#__gpo_datapage_hierarchy`.column_title,
		              `#__gpo_datapage_hierarchy`.jargon_term   
		          FROM 
		              `#__gpo_datapage_hierarchy`				   
		          WHERE 
					   `#__gpo_datapage_hierarchy`.`parent_id` IS NULL" .
		          ' AND ' . 
		               '`#__gpo_datapage_hierarchy`.`active` = 1';
       
       if( is_numeric($type) && 'all' != $type ) {
		       $query .= ' AND ' . 
		                 "`#__gpo_datapage_hierarchy`.`column_type` = '".$type."'";   
       }
       
   	   $query .= ' ORDER BY ' . 
   	             '`#__gpo_datapage_hierarchy`.`sort_order`';
       
	   $db->setQuery( $query );
	   $data = $db->loadObjectList();
		
	   foreach ( $data as $key => $val ) {
			$topHeaders[$val->id] = trim($val->column_name);
	   }
		
	   return $topHeaders;
   }
   
   
   function getColumnsByParentId( $p_parent_id ) {
   	 if( empty($p_parent_id) ) {
   	 	return false;
   	 }
   	 $db = &JFactory::getDBO();
   	 
   	 $query = "SELECT
		              `#__gpo_datapage_hierarchy`.id,
		              `#__gpo_datapage_hierarchy`.column_name,
		              `#__gpo_datapage_hierarchy`.column_title,
		              `#__gpo_datapage_hierarchy`.jargon_term   
		          FROM 
		              `#__gpo_datapage_hierarchy`           
				   
		          WHERE 
					   `#__gpo_datapage_hierarchy`.`parent_id`=" . $db->Quote($p_parent_id) .
   	 	          ' AND ' . 
		               '`#__gpo_datapage_hierarchy`.`active` = 1' . 
   	              ' ORDER BY ' . 
   	                   '`#__gpo_datapage_hierarchy`.`sort_order`';

   	 $db->setQuery( $query );
   	 $data = $db->loadObjectList();
	 return $data;
   }
   
   
   
    /*
    * 
    *  datapage hierarchy related methods
    *  <!-- starts --> 
    * 
    */
   
   
   function getDPHierarchy( $depth=3 ) {
   	  
   	 $db = &JFactory::getDBO();
   	 
	 if (intval ( $depth ) < 1) {
		   $depth = 1; //get top level nodes only
	 }		
		
	 $select = array ();
	 $from = array ();
	 $where = array ();
	 $order = array ();
		
	 for($i = 1; $i <= $depth; $i ++) {
		  $select [] = "level" . $i . ".column_name AS level" . $i . "_column_name";
		  $from [] = "`#__gpo_datapage_hierarchy` AS level" . $i . "";
		  //$where [] = "( level" . $i . ".active = 1 )";
		  $order [] = "level" . $i . ".sort_order";
	 }
		
	 //SELECT
	 $sql = "SELECT " . implode ( ', ', $select ) . " ";
		
	 //FROM
	 $sql .= "FROM " . $from [0] . " ";
		
	 unset ( $from [0] );
	 if (count ( $select ) > 0) {
		  foreach ( $from as $key => $value ) {
				$from [$key] = $value . " ON level" . ($key) . ".id = level" . ($key + 1) . ".parent_id";
		  }
			
			$sql .= " LEFT JOIN " . implode ( " LEFT JOIN ", $from ) . " ";
	 }
		
	 //WHERE
	 $where = "( level1.active = 1 )";
	 $sql .= "WHERE level1.parent_id IS NULL AND " . $where . " ";
		
	 //ORDER
	 $sql .= "ORDER BY " . implode ( ", ", $order );
	 
   	 //RUN QUERY
	 $db->setQuery( $sql );
	 $data = $db->loadAssocList();
	 
	 return $data;
   }
   
   
   function getJargonTerms() {
   	   $db = &JFactory::getDBO();
   	   $jargonTerms = array();
   	   
   	   $query = "SELECT 
		              `#__gpo_datapage_hierarchy`.id,
		              `#__gpo_datapage_hierarchy`.column_name,
		              `#__gpo_datapage_hierarchy`.column_title,
		              `#__gpo_datapage_hierarchy`.jargon_term   
		          FROM 
		              `#__gpo_datapage_hierarchy`           
				   
		          WHERE 
					   `#__gpo_datapage_hierarchy`.`parent_id` IS NULL" .
		          ' AND ' . 
		               '`#__gpo_datapage_hierarchy`.`active` = 1';
				
		$db->setQuery( $query );
		$data = $db->loadObjectList();
		
		foreach ( $data as $key => $val ) {
			$jargonTerms[$val->column_name] = trim($val->jargon_term);
		}
		
		return $jargonTerms;
   }
   
   
   function processDPHierarchy( $dataList ) {
   	  $DPTree = array();
   	  
   	  foreach( $dataList as $key => $val) { 
   	  	$l1 = $val['level1_column_name'];
   	  	$l2 = $val['level2_column_name'];
   	  	$l3 = $val['level3_column_name'];
   	  	

   	  	if( !in_array($l1, $DPTree['level0']) ) {
   	  	    $DPTree['level0'][] = $l1;
   	  	}
   	  	    
   	  	if( $l2 !='' && $l2 !=NULL ) {
   	  		if( !in_array($l2,$DPTree['level1'][ $l1 ]) )
   	  		    $DPTree['level1'][ $l1 ][] = $l2;
   	  	}
   	  	
   	    if( ($l3 !='' && $l3 !=NULL) ) {
   	    	if( !in_array($l3, $DPTree['level2'][ $l1 ]) )
   	  		   $DPTree['level2'][$l1][$l2][] = $l3;
   	  	}
   	  }
   	  
   	  return $DPTree;
   }
   
   
   /*
    * 
    *  datapage hierarchy related methods
    *  <!-- ends --> 
    * 
    */
   
   
   
    /*
    * 
    * javascript event hander to conrtrol
    * expand, collapse functionality of DP Tree
    * 
    */
   function getDPJs( $type = 'both' ) {
      $document = &JFactory::getDocument();
   	  $jsUrl = JURI::base() . 'templates/gunpolicy/javascript/datapage.js?v=2.3';
   	  $stylesheetUrl = JURI::base() . 'templates/gunpolicy/css/dpstyles.css?v=1';
   	  if ( 'both' == $type ) {
   	     JHTML::_("behavior.mootools");
   	     $document->addScript($jsUrl);
   	     $document->addStyleSheet($stylesheetUrl,'text/css',"screen");
      }
      else if( 'stylesheet' == $type ) {
      	 $document->addStyleSheet($stylesheetUrl,'text/css',"screen");
      } 
      else if( 'js' == $type ) {
      	 JHTML::_("behavior.mootools");
   	     $document->addScript($jsUrl);
      }
   }

   /**
    * Returns the array of column name that are gateway
    * @return <type>
    */
   function getDPHierarchyGatewayColumns ()
   {
      $db = & JFactory::getDBO();
      $query = "SELECT `column_name` FROM `#__gpo_datapage_hierarchy` WHERE `is_gateway`={$db->Quote(1)} LIMIT 5000";
      $db->setQuery($query);
      $data = $db->loadObjectList();
      $gateways = array();
      foreach($data as $key=>$val){
         $gateways[] = $val->column_name;
      }
    
      return $gateways;
   }
   
   
   /*
    * 
    * All available options for region aggregation
    * 
    */
   function getRegionAggregationOptions() {
       return array('summation' => 'Summation',
                    'average'   => 'Average',
                    'off'       => 'Off'
              );
   }
   
   /*
    * 
    * All available options for 
    * vertical chart axis label
    * 
    */
   function getYChartLabelOptions($lang='en') {
       
       $enLabels = array('number'            => 'Number',
                         'us_dollar'         => 'US$',
                         'us_dollar_million' => 'US$ million',
                         'rate_per_100k_pop' => 'Rate per 100,000 population',
                         'rate_per_100_pop'  => 'Rate per 100 population',
                         'percent'           => 'Percent',
                         'none'              => 'None'
                   );
       
       $esLabels = array('number'            => 'Número',
                         'us_dollar'         => 'US$',
                         'us_dollar_million' => 'US$ millones',
                         'rate_per_100k_pop' => 'Tasa cada 100.000 habitantes',
                         'rate_per_100_pop'  => 'Tasa cada 100 habitantes',
                         'percent'           => 'Porcentaje',
                         'none'              => 'None'
                   );
       
       
       $frLabels = array('number'            => 'Nombre',
                         'us_dollar'         => 'US$',
                         'us_dollar_million' => 'US$ million',
                         'rate_per_100k_pop' => 'Taux pour 100 000 habitants',
                         'rate_per_100_pop'  => 'Taux pour 100 habitants',
                         'percent'           => 'Pourcentage',
                         'none'              => 'None'
                   );
       
       if( empty($lang) || 'en' == $lang) {
           return $enLabels;
       }else if('es' == $lang) {
           return $esLabels;
       }else if('fr' == $lang) {
           return $frLabels;
       }else {
           return $enLabels;
       }
       
   }
   
   
   /*
    * 
    * All available options for 
    * vertical chart axis label
    * 
    */
    function mergeYearlyDataArray( $firstArray = array(), $secondArray = array() ) {
        $mergedArray = array();
        $mergedArray = array_replace($secondArray, array_filter($firstArray));
        //array_merge( array_filter($firstArray), $secondArray );
        return $mergedArray;
    }

    function mergeYearlyDataWithNewValues( $existingDataArray = array(), $newDataArray = array(), $existingDataWithoutQuotes = array(), $importOnlyBlankYears=false ) {
        
        ### If nothing to merge, return old one.
        if( !isset($newDataArray) ) {
            return $existingDataArray; 
        }
        
        ### Unset Country, QCite keys if exists from the new data array
        //unset($newDataArray['Country']); 
        //unset($newDataArray['QCite']);
        
        $QCite = $newDataArray['QCite'];
        $QCiteFormatted = '';
        if ( !empty(trim($QCite)) ) {
            $QCiteFormatted =  '{' . $QCite . '}';
        }
        
        ### Loop through the new data array and merge with the old one when needed 
        foreach($newDataArray as $key => $value)
        {
        
            if(!array_key_exists($key, $existingDataArray) && isset($value) ) {
               $existingDataArray[$key] = formatNumberAndQCiteForYearlyData($value, $key, $QCite, $newDataArray); //update if new value is provided for a new year
            }
            
            ########
            ### If importOnlyBlankYears is set to true, then don't don't update values for exisitng years whether the values are same or not
            ########
            if( !$importOnlyBlankYears ) {
                if( isset($newDataArray[$key]) && $existingDataWithoutQuotes[$key] != $value ) {
                    if( str_replace(',', '', $existingDataWithoutQuotes[$key]) != $value ) {
                        $existingDataArray[$key] = formatNumberAndQCiteForYearlyData($value, $key, $QCite, $newDataArray); //update if new value is different than old value
                    }
                }
            }
        
        }
        
        return $existingDataArray;
    }
    
    function concatYearlyDataValues( $dataArray=array() ) {
        
        $string = '';
        $QCite  = $dataArray['QCite'];
        
        ### Unset Country & QCite keys if exists from the new data array
        unset($dataArray['Country']);
        unset($dataArray['QCite']);
        unset($dataArray['Category']);
        
        krsort($dataArray);
        foreach( $dataArray as $key => $val ) {
            //$string .= $key . ": " . formatNumberForYearlyData($val,$QCite) . '; ';
            $string .= $key . ": " . $val . '; ';
        }
        $string = substr($string,0,-1);
        
        return $string;
    }
    
    function formatNumberForYearlyData($val,$QCite=NULL) {
        
        ### If value is NULL or Zero, return without applying formatting
        if( !isset($val) ) {
            return $val; 
        }
        
        ###
        ###take two digit after decimal and rounded up 
        ###for full numbers avoid .00 at the end
        ###
        $formattedVal = str_replace('.00', '', number_format($val,2));
        
        if( !empty($QCite) ) {
            $formattedVal .= '{' . $QCite . '}';
        }
        
        return $formattedVal;
    }

    
    function formatNumberAndQCiteForYearlyData($val, $currentYear=NULL, $QCite=NULL, $fullArray=array() ) {
        
        ### If value is NULL or Zero, return without applying formatting
        if( !isset($val) ) {
            return $val; 
        }
        
        ###
        ###take two digit after decimal and rounded up 
        ###for full numbers avoid .00 at the end
        ###
        $formattedVal = str_replace('.00', '', number_format($val,2));
        
        
        ###########
        #### Print QCite for only the latest years
        ###########
        $latestYear = 0;
        foreach( $fullArray as $key => $val ) {
            if( in_arrayi($key, array('QCite', 'Country', 'Category Alias', 'Category')) ) {
                continue;
            }
            
            ### Only count the year if there is a value for that year ### 
            if( isset($val) ) {
                $latestYear = ($key >= $latestYear) ? $key : $latestYear;
            }
        }
        
        if( !empty($QCite) && $latestYear == $currentYear ) {
            $formattedVal .= '{' . $QCite . '}';
        }
        
        return $formattedVal;
    }
