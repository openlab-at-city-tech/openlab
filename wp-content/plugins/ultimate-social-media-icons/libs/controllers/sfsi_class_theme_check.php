<?php

class sfsi_ThemeCheck
{
	public $metaArray = null;
	public function sfsi_plus_string_to_arr($str){

		$arrSingleQuote = array();

		if(strlen(trim($str))>0){
			$arrSingleQuote = explode(",", $str);
		}
		return $arrSingleQuote;
	}

	public function sfsi_plus_getdomain($url)
	{
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			return $regs['domain'];
		}
		return false;
	}

	public function sfsi_plus_returningElement($element) {return $element[0];}

	public function sfsi_plus_get_themeData(){
	    
	    $keywordFile    = SFSI_DOCROOT."/themedata.csv";
	    $keywordData    = @file_get_contents($keywordFile);
	    $keywordEnglish = array_map("str_getcsv", explode("\n", $keywordData));
	    $themeDataArr = array();

	    if(isset($keywordEnglish) && is_array($keywordEnglish) && count($keywordEnglish)>0){

	    	unset($keywordEnglish[0]);

		    $finalArr = array_filter(array_values($keywordEnglish));

		    if(isset($finalArr) && is_array($finalArr) && count($finalArr)>0){
			    
			    for($i=0;$i<count($finalArr);$i++) {
			              
			            if( is_array($finalArr[$i]) 

			            	// Theme name should be non-empty
			            	&& isset($finalArr[$i][0]) && !empty($finalArr[$i][0])
			            ){

		                	$arrVal = $finalArr[$i];

		                    $themeArr 					   = array();
		                    $themeArr['themeName']         = preg_replace('/^[,\s]+|[\s,]+$/', '', trim($arrVal[0]));
		                    $themeArr['noBrainerKeywords'] = $this->sfsi_plus_string_to_arr(preg_replace('/^[,\s]+|[\s,]+$/', '', trim($arrVal[1])));
		                    $themeArr['separateKeywords']  = $this->sfsi_plus_string_to_arr(preg_replace('/^[,\s]+|[\s,]+$/', '', trim($arrVal[2])));
		                    $themeArr['negativeKeywords']  = $this->sfsi_plus_string_to_arr(preg_replace('/^[,\s]+|[\s,]+$/', '', trim($arrVal[3])));
		                    $themeArr['headline']          = (isset($arrVal[6]) && strlen(trim($arrVal[6]))==0)? "You like ".trim($arrVal[0])." ?" : trim($arrVal[6]);
		                    $themeArr['themeLink']         = trim($arrVal[8]);
		                    $themeArr['bottomtext']        = (isset($arrVal[10]) && strlen(trim($arrVal[10]))==0)? "See all ".strtolower(trim($arrVal[0]))."-themed-icons": trim($arrVal[10]);                   

		                    array_push($themeDataArr, (object)$themeArr);
			            } 
			    }
		    }	    	
	    }

	    return $themeDataArr;
	}

	public function sfsi_plus_get_keywordEnglish(){
	    $keywordFile    = SFSI_DOCROOT."/All_english_words_better_list.csv";
	    $keywordData    = @file_get_contents($keywordFile);
	    $keywordEnglish = array_map("str_getcsv", explode("\n", $keywordData));
	    $keywordEnglish = array_map('array_filter', $keywordEnglish);
		$keywordEnglish = array_filter(array_map(sfsi_returningElement($element), $keywordEnglish)); 	    
	    return $keywordEnglish;
	}
	public function sfsi_plus_regex_for_keywords($arrKeyWords){

		$strRegex = "";

		if(isset($arrKeyWords) && is_array($arrKeyWords) && count($arrKeyWords)>0 && is_array($arrKeyWords)){

			$count = count($arrKeyWords);

	        if($count==1){
	            $strRegex .= "/".$arrKeyWords[0]."/im";
	        }
	        else{
				for ($i=0; $i <$count ; $i++) { 

					$val = trim($arrKeyWords[$i]);

					if($i==0){
						$strRegex .= "/(".$val."|";
					}
					elseif ($i==$count-1) {
						$strRegex .= $val.")/im";				
					}
					else{
						$strRegex.= $val."|";
					}			
				}	        	
	        }
		}
		return $strRegex;
	}

	public function sfsi_plus_regex_forNegative_keywords($arrKeyWords){

	      $strRegex = "";

	      if(isset($arrKeyWords) && is_array($arrKeyWords) && count($arrKeyWords)>0 && is_array($arrKeyWords)){

	            $count = count($arrKeyWords);

	            if($count==1){
	            	$strRegex .= "/".$arrKeyWords[0]."/i";
	            }
	            else{
	            	
		            for ($i=0; $i <$count ; $i++) { 

						  $val = trim($arrKeyWords[$i]);

		                  if($i==0){
		                        $strRegex .= "/".$val."|";
		                  }
		                  elseif ($i==$count-1) {
		                        $strRegex .= $val."/i";                       
		                  }
		                  else{
		                        $strRegex.= $val."|";
		                  }                 
		            }	            	
	            }
	      }
	      return $strRegex;
	}

	public function sfsi_plus_match_separate_word_with_csv_data($seprateWord,$domainname){
	      $keywordEnglish = $this->sfsi_plus_get_keywordEnglish();

	      $finalKeywordEnglish = array();

	      foreach ($keywordEnglish as $val) {
	        if(is_array($val)) {
	            array_push($finalKeywordEnglish, $val[0]);
	        }
	      }

	        $catflag = false;

	        $explode    = explode($seprateWord,$domainname);
	        $left       = trim($explode[0]);
	        $right      = trim($explode[1]);
	        
	        $leftcatflag = false;

	                if(!empty($left))
	                {
	                    $left = str_split($left);

	                    $matchKeyword = ''; $j = 0;
	                    for($i = (count($left)-1); $i >= 0; $i--)
	                    {
	                        $matchKeyword = $left[$i].$matchKeyword;
	                        
	                        if($j > 0)
	                        {
	                            if(in_array($matchKeyword, $finalKeywordEnglish))
	                            {
	                                $leftcatflag = true;
	                                break;
	                            }
	                            else
	                            {
	                                continue;
	                            }
	                        }
	                        else
	                        {
	                            if(preg_match("/\.|\-|[0-9]/im", $matchKeyword))
	                            {
	                                $leftcatflag = true;
	                                break;
	                            } 
	                        }

	                        $j++;
	                    }       
	                }

	                $rightcatflag = false;
	                if(!empty($right))
	                {
	                    $right = str_split($right);
	                    
	                    $matchKeyword = '';
	                    for($i = 0; $i < count($right); $i++)
	                    {
	                        $matchKeyword .= $right[$i];
	                        
	                        if($i > 0)
	                        {
	                            if(in_array($matchKeyword, $finalKeywordEnglish))
	                            {
	                                $rightcatflag = true;
	                                break;
	                            }
	                            else
	                            {
	                                continue;
	                            }
	                        }
	                        else
	                        {
	                            if(preg_match("/\.|\-|[0-9]/im", $matchKeyword))
	                            {
	                                $rightcatflag = true;
	                                break;
	                            } 
	                        }
	                    }       
	                }

	                if(empty($left) && empty($right))
	                {
	                    $catflag = true;
	                }
	                else
	                {
	                    if(!empty($left) && !empty($right))
	                    {
	                        if($rightcatflag && $leftcatflag)
	                        {
	                            $catflag = true;           
	                        }
	                    }
	                    elseif(empty($left) && !empty($right))
	                    {
	                        if($rightcatflag)
	                        {
	                            $catflag = true;           
	                        }
	                    }
	                    elseif(!empty($left) && empty($right))
	                    {
	                        if($leftcatflag)
	                        {
	                            $catflag = true;           
	                        }
	                    }
	                }

	    return $catflag;
	}

	public function sfsi_plus_SeparateKeywordCheck($arrSeparateKeywords,$domainname){

	    $boolSeparateWord = false;

	    if(isset($arrSeparateKeywords) && is_array($arrSeparateKeywords) && count($arrSeparateKeywords)>0){

	        foreach ($arrSeparateKeywords as $value) {

	            $val = trim($value);

	            if(isset($value) && strlen($val)>0){

	                if(preg_match("/(".$val.")/im", $domainname)){
	                    
	                    $boolSeparateWord = $this->sfsi_plus_match_separate_word_with_csv_data($val,$domainname);

	                    if($boolSeparateWord) {
	                    	break;	                    	
	                    }

	                }
	            }
	        }
	    }

	    return $boolSeparateWord;
	}

	public function sfsi_plus_MetaKeywordCheck($arrSeparateKeywords,$domainname){
 		$keywordInMeta = false;
 		$metaArray = $this->sfsi_plus_GetMetaName($domainname);
 		foreach($metaArray as $index=>$meta){
 			if($this->sfsi_plus_noBrainerKeywordCheck($arrNoBrainerKeywords, $domainname)){
                $flag = true;	                
            }
            else if($this->sfsi_plus_SeparateKeywordCheck($arrSeparateKeywords,$domainname)){
                $flag = true;
            }
 		}
 		return $keywordInMeta;
 	}

 	public function sfsi_plus_GetMetaKeywords($domainname){
 		$url = get_bloginfo('url'); 
 		$res= wp_remote_get($url);
 		$meta_local = array("title"=>array(),"description"=>array(),"keyword"=>array());
 		if ( is_array( $res ) && ! is_wp_error( $res ) ) {
		    $body    = $res['body']; // use the content
			$meta = array();
		    if(false==class_exists("DomDocument")) {
		    	$metas=array();
	    		preg_match_all( '/\<meta.+name="(\w*)".+content="(.*)"/i', $body, $metas);
	    		preg_match_all( '/\<meta.+property="og:(\w*)".+content="(.*)"/i', $body, $metas2);
	    		// $metas[1]=array_merge($metas[1],$metas2[1]);
	    		// $metas[2]=array_merge($metas[2],$metas2[2]);
		    	if(isset($metas)&&is_array($metas)&&isset($metas[1])&&isset($metas[2])){
		    		foreach($metas[1] as $index=>$meta_name){
		    			if($meta_name==="keywords" && isset($metas[2][$index])) {
		    				$meta['keywords']=$metas[2][$index];
		    			}
		    			if($meta_name === "description" && isset($metas[2][$index])){
		    				$meta['description']=$metas[2][$index];
		    			}
		    		}
		    	}
		    	if(isset($metas2)&&is_array($metas2)&&isset($metas2[1])&&isset($metas2[2])){
		    		foreach($metas2[1] as $index=>$meta_name){
		    			// var_dump($meta_name,$meta_name === "description" ,$metas2[2][$index]);
		    			if($meta_name==="keywords" && isset($metas2[2][$index])) {
		    				$meta[$meta_name]=$metas2[2][$index];
		    			}
		    			if($meta_name === "description" && isset($metas2[2][$index])&&!isset($meta[$meta_name])){
		    				$meta[$meta_name]=$metas2[2][$index];
		    			}
		    		}
		    	}
		    	// var_dump($meta);die();
		    	if(isset($meta['keywords'])){
		    		$meta_local["keyword"]=array_filter(explode(',',$meta['keywords']),function($data){
						return $data!=="";
					});
		    	}
		    	if(isset($meta['description'])){
		    		$meta['description']=preg_replace("/[^A-Za-z ]/", '', strtolower($meta['description']));
		    		$meta_local["description"]=array_filter(explode( '\s+',$meta['description']),function($data){
						return $data!=="";
					});
		    	}
	    		$preg_res=preg_match("/<title>(.+)<\/title>/i", $body, $matches);
	    		if($preg_res){
					$meta['title']=preg_replace("/[^A-Za-z ]/", '', strtolower($matches[1]));
					$meta_local["title"]=array_filter(explode('\s+',$meta['title']),function($data){
						return $data!=="";
					});
				}
				
		    }else{
				$doc = new \DOMDocument();
				@$doc->loadHTML($body);
				$nodes = $doc->getElementsByTagName('meta');
				foreach($nodes as $index=>$node){
					if(null!==$node->getAttribute('name')) {
						$meta[$node->getAttribute('name')]=$node->getAttribute('content');
					}elseif(null!==$node->getAttribute('property')){
						$meta[$node->getAttribute('property')]=$node->getAttribute('content');
					}
				}
				$meta['title'] = (null!==$doc->getElementsByTagName('title'))&&count($doc->getElementsByTagName('title'))>0?$doc->getElementsByTagName('title')->item(0)->nodeValue:'';
				if(isset($meta['keywords'])) {
					$meta_local["keyword"]=array_filter(explode(',',$meta['keywords']),function($data){
						return $data!=="";
					});
				}
				if(isset($meta['description'])){
					$meta['description']=preg_replace("/[^A-Za-z ]/", '', strtolower($meta['description']));
		    		$meta_local["description"]=array_filter(explode( '\s+',$meta['description']),function($data){
						return $data!=="";
					});
				}
				if(count($meta_local["description"])==0 && isset($meta['og:description'])){
					$meta['description']=preg_replace("/[^A-Za-z ]/", '', strtolower($meta['og:description']));
		    		$meta_local["description"]=array_filter(explode( '\s+',$meta['description']),function($data){
						return $data!=="";
					});
				}
				if(isset($meta['title'])){
					$meta['title']=preg_replace("/[^A-Za-z ]/", '', strtolower($meta['title']));
					// var_dump($meta['title']);die();
					$meta_local["title"]= array_filter(explode('\s+',$meta['title']),function($data){
						return $data!=="";
					});
				}
			}
		}
		return $meta_local; 
 	}

	public function sfsi_plus_noBrainerKeywordCheck($arrNoBrainerKeywords,$domainname){

	    $bflag = false;

	    if(isset($arrNoBrainerKeywords) && is_array($arrNoBrainerKeywords) && count($arrNoBrainerKeywords)>0 && is_array($arrNoBrainerKeywords)>0){
	    	
	    	if(preg_match($this->sfsi_plus_regex_for_keywords($arrNoBrainerKeywords), $domainname)){
	        	$bflag = true;
	    	}	    	
	    }
	    return $bflag;		
	}

	public function sfsi_plus_check_type_of_websiteWithNoBrainerAndSeparateAndNegativeKeywords($strCheckForThemeType,$arrNoBrainerKeywords,$arrSeparateKeywords,$arrNoBrainerAndSeparateKeywords,$arrNegativeKeywords,$domainname){

	    $flag = false;

		    if(isset($arrNoBrainerAndSeparateKeywords) && is_array($arrNoBrainerAndSeparateKeywords) && count($arrNoBrainerAndSeparateKeywords)>0){

		        if(preg_match($this->sfsi_plus_regex_for_keywords($arrNoBrainerAndSeparateKeywords), $domainname))
		        {
		            if(!empty($domainname))
		            {
		                if(isset($arrNegativeKeywords) && is_array($arrNegativeKeywords) && count($arrNegativeKeywords)){
		                    $domainname = preg_replace($this->sfsi_plus_regex_forNegative_keywords($arrNegativeKeywords), '', $domainname);              
		                    $explode    = explode(".", $domainname);
		                    $domainname = @$explode[0];                    
		                }
			        }
		               
		            if($this->sfsi_plus_noBrainerKeywordCheck($arrNoBrainerKeywords, $domainname)){
		                $flag = true;	                
		            }
		            else if($this->sfsi_plus_SeparateKeywordCheck($arrSeparateKeywords,$domainname)){
		                $flag = true;
		            }
		        } 
		    }
		    return ($flag)? $strCheckForThemeType:$flag;	    	
	}

	public function sfsi_plus_check_type_of_metaTitleWithNoBrainerAndSeparateAndNegativeKeywords($strCheckForThemeType,$arrNoBrainerKeywords,$arrSeparateKeywords,$arrNoBrainerAndSeparateKeywords,$arrNegativeKeywords,$domainname){
		$flag = false;

	    if(isset($arrNoBrainerAndSeparateKeywords) && is_array($arrNoBrainerAndSeparateKeywords) && count($arrNoBrainerAndSeparateKeywords)>0){

            	if(null==$this->metaArray){
            		$this->metaArray = $this->sfsi_plus_GetMetaKeywords($domainname);
            	}
            	foreach($this->metaArray["title"] as $index=>$keyword){
            		if(!empty($keyword))
		            {
		                if(isset($arrNegativeKeywords) && is_array($arrNegativeKeywords) && count($arrNegativeKeywords)){
		                    $keyword = preg_replace($this->sfsi_plus_regex_forNegative_keywords($arrNegativeKeywords), '', $keyword);
		                }
			        }
            		if($this->sfsi_plus_noBrainerKeywordCheck($arrNoBrainerKeywords, $keyword)){
		                $flag = true;
		            }
		            else if($this->sfsi_plus_SeparateKeywordCheck($arrSeparateKeywords,$keyword)){
		                $flag = true;
		            }
            	}
		    } 
		    return ($flag)? $strCheckForThemeType:$flag;    
	}
	public function sfsi_plus_check_type_of_metaKeywordsWithNoBrainerAndSeparateAndNegativeKeywords($strCheckForThemeType,$arrNoBrainerKeywords,$arrSeparateKeywords,$arrNoBrainerAndSeparateKeywords,$arrNegativeKeywords,$domainname){
		$flag = false;

	    if(isset($arrNoBrainerAndSeparateKeywords) && is_array($arrNoBrainerAndSeparateKeywords) && count($arrNoBrainerAndSeparateKeywords)>0){

            	if(null==$this->metaArray){
            		$this->metaArray = $this->sfsi_plus_GetMetaKeywords($domainname);
            	}
            	foreach($this->metaArray["keyword"] as $index=>$keyword){
            		if(!empty($keyword))
		            {
		                if(isset($arrNegativeKeywords) && is_array($arrNegativeKeywords) && count($arrNegativeKeywords)){
		                    $keyword = preg_replace($this->sfsi_plus_regex_forNegative_keywords($arrNegativeKeywords), '', $keyword);
		                }
			        }
            		if($this->sfsi_plus_noBrainerKeywordCheck($arrNoBrainerKeywords, $keyword)){
		                $flag = true;
		            }
		            else if($this->sfsi_plus_SeparateKeywordCheck($arrSeparateKeywords,$keyword)){
		                $flag = true;
		            }
            	}
		    } 
		    return ($flag)? $strCheckForThemeType:$flag;    
	}
	public function sfsi_plus_check_type_of_metaDescriptionWithNoBrainerAndSeparateAndNegativeKeywords($strCheckForThemeType,$arrNoBrainerKeywords,$arrSeparateKeywords,$arrNoBrainerAndSeparateKeywords,$arrNegativeKeywords,$domainname){
		$flag = false;

	    if(isset($arrNoBrainerAndSeparateKeywords) && is_array($arrNoBrainerAndSeparateKeywords) && count($arrNoBrainerAndSeparateKeywords)>0){

            	if(null==$this->metaArray){
            		$this->metaArray = $this->sfsi_plus_GetMetaKeywords($domainname);
            	}
            	foreach($this->metaArray["description"] as $index=>$keyword){
            		if(!empty($keyword))
		            {
		                if(isset($arrNegativeKeywords) && is_array($arrNegativeKeywords) && count($arrNegativeKeywords)){
		                    $keyword = preg_replace($this->sfsi_plus_regex_forNegative_keywords($arrNegativeKeywords), '', $keyword);
		                }
			        }
            		if($this->sfsi_plus_noBrainerKeywordCheck($arrNoBrainerKeywords, $keyword)){
		                $flag = true;
		            }
		            else if($this->sfsi_plus_SeparateKeywordCheck($arrSeparateKeywords,$keyword)){
		                $flag = true;
		            }
            	}
		    } 
		    return ($flag)? $strCheckForThemeType:$flag;    
	}

 	public function sfsi_plus_bannereHtml_main($title, $siteLink, $bannerImage, $buttonTitle)
	{
		echo '<script type="text/javascript">
		SFSI(".sfsi_webtheme").show();
		SFSI(".icns_tab_3.sfsi_premium_ad span ").css("background-image", \'url('.$bannerImage.')\');
		SFSI(".sfsi_premium_ad_lable").text(\''.$siteLink.'\');
		</script>';
	}		

	public function sfsi_plus_bannereHtml($title, $siteLink, $bannerImage, $buttonTitle)
	{
		echo '<div class="sfsi_new_notification_cat">
	        <div class="sfsi_new_notification_header_cat">
	            <h1>'.$title.'</h1>
	            <h3>The <a href="'.$siteLink.'" target="_blank">Premium Plugin</a> Includes these icons...</h3>
	            <div class="sfsi_new_notification_cross_cat">X</div>
	        </div>
	        
	        <div class="sfsi_new_notification_body_link_cat">
	            <a href="'.$siteLink.'" target="_blank">
	                <div class="sfsi_new_notification_body_cat">
	                    <div class="sfsi_new_notification_image_cat">
	                        <img src="'.$bannerImage.'" id="newImg" alt="Banner" />
	                    </div>
	                </div>
	            </a>
	            <div class="bottom_text">
	                <a href="'.$siteLink.'">
	                    '.$buttonTitle.' >
	                </a>
	            </div>    
	        </div>
	    </div>';
	}	

	
}
?>