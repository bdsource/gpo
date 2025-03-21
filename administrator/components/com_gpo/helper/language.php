<?php
   
   /* 
    * language helper, language related methods
    * 
    */

   function getLanguageName($lang) {
       
       $lang = strtolower($lang);
       if( in_array($lang,array('en')) ) {
           return 'English';
       }
       
       if( in_array($lang,array('es')) ) {
           return 'Español';
       }
       
       if( in_array($lang,array('fr')) ) {
           return 'Français';
       }
       
   }
   
   
   function getLanguageFlag($lang,$type='tiny') {
       $flagBaseURI = JURI::root() . 'templates/gunpolicy/images/flags/'.$type.'/';
       $flagURI = $flagBaseURI . $lang . ".png";
       
       return $flagURI;
   }
   
   function getLanguageOptionsHTML($currentLanguage='en') {
       $languages = array('en','es','fr');
       
       $langHTML  = '<select id="languageDropdown">';
       $langHTML .= '<option value="'.$currentLanguage.'" data-imagesrc="'.getLanguageFlag($currentLanguage).'">
                    '.  getLanguageName($currentLanguage).'</option>';
       $i = 0;
       foreach ($languages as $lang) 
       {
            if($currentLanguage == $lang) {
               continue;
            }
            
            $langHTML .= '<option value="'.$lang.'" data-imagesrc="'.getLanguageFlag($lang).'">
                         '.  getLanguageName($lang).'</option>';
            $i++;
       }
       
       $langHTML .= '</select>';
       
       return $langHTML;
   }
   
