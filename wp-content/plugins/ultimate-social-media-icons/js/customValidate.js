
function sfsi_validationStep2()
{
   //var class_name= SFSI(element).hasAttr('sfsi_validate');
  SFSI('input').removeClass('inputError');  // remove previous error 
  if(sfsi_validator(SFSI('input[name="sfsi_rss_display"]'),'checked'))
  {
        if(!sfsi_validator(SFSI('input[name="sfsi_rss_url"]'),'url'))
        {   showErrorSuc("error","Error : Invalid Rss url ",2);
            SFSI('input[name="sfsi_rss_url"]').addClass('inputError');
            
            return false;
        }    
  }
  
  /* validate facebook */
  if(sfsi_validator(SFSI('input[name="sfsi_facebookPage_option"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_facebookPage_option"]'),'checked'))
  {
        if( !sfsi_validator(SFSI('input[name="sfsi_facebookPage_url"]'),'blank'))
        {   showErrorSuc("error","Error : Invalid Facebook page url ",2);
            SFSI('input[name="sfsi_facebookPage_url"]').addClass('inputError');
            
            return false;
        }    
  }
  
  /* validate twitter user name */
    if(sfsi_validator(SFSI('input[name="sfsi_twitter_followme"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_twitter_followme"]'),'checked'))
  {     
        
         if(!sfsi_validator(SFSI('input[name="sfsi_twitter_followUserName"]'),'blank'))
        {   showErrorSuc("error","Error : Invalid Twitter UserName ",2);
            SFSI('input[name="sfsi_twitter_followUserName"]').addClass('inputError');
            
            return false;
        }    
  }
  /* validate twitter about page */
  //   if(sfsi_validator(SFSI('input[name="sfsi_twitter_aboutPage"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_twitter_aboutPage"]'),'checked'))
  // {
       
  //       // if(!sfsi_validator(SFSI('#sfsi_twitter_aboutPageText'),'blank'))
  //       // {   showErrorSuc("error","Error : Tweet about my page is blank ",2);
  //       //     SFSI('#sfsi_twitter_aboutPageText').addClass('inputError');
            
  //       //     return false;
  //       // }    
  // }  
  
  /* twitter validation */
    if(sfsi_validator(SFSI('input[name="sfsi_twitter_page"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_twitter_page"]'),'checked'))
  {     
       
        if(!sfsi_validator(SFSI('input[name="sfsi_twitter_pageURL"]'),'blank') )
        {   showErrorSuc("error","Error : Invalid twitter page Url ",2);
            SFSI('input[name="sfsi_twitter_pageURL"]').addClass('inputError');
            
            return false;
        }    
  }
  /* youtube validation */
    if(sfsi_validator(SFSI('input[name="sfsi_youtube_page"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_youtube_page"]'),'checked'))
  {     
        
         if(!sfsi_validator(SFSI('input[name="sfsi_youtube_pageUrl"]'),'blank') )
        {   showErrorSuc("error","Error : Invalid youtube Url ",2);
            SFSI('input[name="sfsi_youtube_pageUrl"]').addClass('inputError');
            
            return false;
        }    
  }
  /* youtube validation */
    if(sfsi_validator(SFSI('input[name="sfsi_youtube_page"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_youtube_follow"]'),'checked'))
  {     
         if(!sfsi_validator(SFSI('input[name="sfsi_ytube_user"]'),'blank') )
        {   showErrorSuc("error","Error : Invalid youtube user ",2);
            SFSI('input[name="sfsi_ytube_user"]').addClass('inputError');
            
            return false;
        }    
  }
    /* pinterest validation */
  if(sfsi_validator(SFSI('input[name="sfsi_pinterest_page"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_pinterest_page"]'),'checked'))
  {     
        
         if(!sfsi_validator(SFSI('input[name="sfsi_pinterest_pageUrl"]'),'blank') )
        {   showErrorSuc("error","Error : Invalid pinterest page url ",2);
            SFSI('input[name="sfsi_pinterest_pageUrl"]').addClass('inputError');            
            return false;
        }    
  }
    /* instagram validation */
  if(sfsi_validator(SFSI('input[name="sfsi_instagram_display"]'),'checked'))
  {     
        
         if(!sfsi_validator(SFSI('input[name="sfsi_instagram_pageUrl"]'),'blank') )
        {   showErrorSuc("error","Error : Invalid Instagram url ",2);
            SFSI('input[name="sfsi_instagram_pageUrl"]').addClass('inputError');            
            return false;
        }    
  }
  
      /* LinkedIn validation */
  if(sfsi_validator(SFSI('input[name="sfsi_linkedin_page"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_linkedin_page"]'),'checked'))
  {     
        
        if(!sfsi_validator(SFSI('input[name="sfsi_linkedin_pageURL"]'),'blank') )
        {   showErrorSuc("error","Error : Invalid LinkedIn page url ",2);
            SFSI('input[name="sfsi_linkedin_pageURL"]').addClass('inputError');            
            return false;
        }    
  }
  if(sfsi_validator(SFSI('input[name="sfsi_linkedin_recommendBusines"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_linkedin_recommendBusines"]'),'checked'))
  {     
        
         if(!sfsi_validator(SFSI('input[name="sfsi_linkedin_recommendProductId"]'),'blank') || !sfsi_validator(SFSI('input[name="sfsi_linkedin_recommendCompany"]'),'blank') )
        {   showErrorSuc("error","Error : Please Enter Product Id and Company for LinkedIn Recommendation ",2);
            SFSI('input[name="sfsi_linkedin_recommendProductId"]').addClass('inputError'); 
            SFSI('input[name="sfsi_linkedin_recommendCompany"]').addClass('inputError');  
            return false;
        }    
  }
  
  /* validate custom links */
    var er=0;
  SFSI("input[name='sfsi_CustomIcon_links[]']").each(function(){
        
		if(!sfsi_validator(SFSI(this),'blank') || !sfsi_validator(SFSI(SFSI(this)),'url') )
        {      showErrorSuc("error","Error : Please Enter a valid Custom link ",2);
               SFSI(this).addClass('inputError');  
              er=1;
        }    
     });
     if(!er) return true; else return false;
}

function sfsi_validationStep3()
{
  
  SFSI('input').removeClass('inputError');  // remove previous error  
  /* validate shuffle effect  */
  if(sfsi_validator(SFSI('input[name="sfsi_shuffle_icons"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_shuffle_icons"]'),'checked'))
  {
        if((!sfsi_validator(SFSI('input[name="sfsi_shuffle_Firstload"]'),'activte') || !sfsi_validator(SFSI('input[name="sfsi_shuffle_Firstload"]'),'checked')) && (!sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'),'activte') || !sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'),'checked')))
        {   showErrorSuc("error","Error : Please Chose a Shuffle option ",3);
            SFSI('input[name="sfsi_shuffle_Firstload"]').addClass('inputError');
             SFSI('input[name="sfsi_shuffle_interval"]').addClass('inputError');
            
            return false;
        }    
  }
  if(!sfsi_validator(SFSI('input[name="sfsi_shuffle_icons"]'),'checked') && (sfsi_validator(SFSI('input[name="sfsi_shuffle_Firstload"]'),'checked') || sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'),'checked')))
  {
      showErrorSuc("error","Error : Please check \"Shuffle them automatically\" option also ",3);
      SFSI('input[name="sfsi_shuffle_Firstload"]').addClass('inputError');
      SFSI('input[name="sfsi_shuffle_interval"]').addClass('inputError');
       return false;
  }
    
    /* validate twitter user name */
  if(sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'),'checked'))
  {     
        
         if(!sfsi_validator(SFSI('input[name="sfsi_shuffle_intervalTime"]'),'blank') || !sfsi_validator(SFSI('input[name="sfsi_shuffle_intervalTime"]'),'int'))
        {   showErrorSuc("error","Error : Invalid shuffle time interval",3);
            SFSI('input[name="sfsi_shuffle_intervalTime"]').addClass('inputError');
            return false;
        }    
  }
    
    return true;

}
function sfsi_validationStep4()
{
   //var class_name= SFSI(element).hasAttr('sfsi_validate');
  
  /* validate email */
  if(sfsi_validator(SFSI('input[name="sfsi_email_countsDisplay"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_email_countsDisplay"]'),'checked'))
  {    
        
        if(SFSI('input[name="sfsi_email_countsFrom"]:checked').val()=='manual')
        {  
          if(!sfsi_validator(SFSI('input[name="sfsi_email_manualCounts"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter manual counts for Email icon ",4);
                SFSI('input[name="sfsi_email_manualCounts"]').addClass('inputError');
                return false;
            }      
        }    
  }
  /* validate RSS count */
  
  if(sfsi_validator(SFSI('input[name="sfsi_rss_countsDisplay"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_rss_countsDisplay"]'),'checked'))
  {    
          if(!sfsi_validator(SFSI('input[name="sfsi_rss_manualCounts"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter manual counts for Rss icon ",4);
                SFSI('input[name="sfsi_rss_countsDisplay"]').addClass('inputError');
                return false;
            }      
            
  }
  /* validate facebook */
  if(sfsi_validator(SFSI('input[name="sfsi_facebook_countsDisplay"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_facebook_countsDisplay"]'),'checked'))
  {    
        
        /*if(SFSI('input[name="sfsi_facebook_countsFrom"]:checked').val()=='likes' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_facebook_PageLink"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter facebook page Url ",4);
                SFSI('input[name="sfsi_facebook_PageLink"]').addClass('inputError');
                return false;
            }      
        } */
        if(SFSI('input[name="sfsi_facebook_countsFrom"]:checked').val()=='manual' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_facebook_manualCounts"]'),'blank') && !sfsi_validator(SFSI('input[name="sfsi_facebook_manualCounts"]'),'url'))
            {   showErrorSuc("error","Error : Please Enter a valid facebook manual counts ",4);
                SFSI('input[name="sfsi_facebook_manualCounts"]').addClass('inputError');
                return false;
            }      
        }
  }
  /* validate twitter */
  if(sfsi_validator(SFSI('input[name="sfsi_twitter_countsDisplay"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_twitter_countsDisplay"]'),'checked'))
  {    
        
        if(SFSI('input[name="sfsi_twitter_countsFrom"]:checked').val()=='source' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="tw_consumer_key"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a valid consumer key",4);
                SFSI('input[name="tw_consumer_key"]').addClass('inputError');
                return false;
            }
              
          if(!sfsi_validator(SFSI('input[name="tw_consumer_secret"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a valid consume secret ",4);
                SFSI('input[name="tw_consumer_secret"]').addClass('inputError');
                return false;
            } 
              
          if(!sfsi_validator(SFSI('input[name="tw_oauth_access_token"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a valid oauth access token",4);
                SFSI('input[name="tw_oauth_access_token"]').addClass('inputError');
                return false;
            }
          
          if(!sfsi_validator(SFSI('input[name="tw_oauth_access_token_secret"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a oAuth access token secret",4);
                SFSI('input[name="tw_oauth_access_token_secret"]').addClass('inputError');
                return false;
            }    
        }
        if(SFSI('input[name="sfsi_linkedIn_countsFrom"]:checked').val()=='manual' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_twitter_manualCounts"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter Twitter manual counts ",4);
                SFSI('input[name="sfsi_twitter_manualCounts"]').addClass('inputError');
                return false;
            }      
        }
        
  }
  /* validate LinkedIn */
  if(sfsi_validator(SFSI('input[name="sfsi_linkedIn_countsDisplay"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_linkedIn_countsDisplay"]'),'checked'))
  {    
        
        if(SFSI('input[name="sfsi_linkedIn_countsFrom"]:checked').val()=='follower' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="ln_company"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a valid company name",4);
                SFSI('input[name="ln_company"]').addClass('inputError');
                return false;
            }
              
          if(!sfsi_validator(SFSI('input[name="ln_api_key"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a valid API key ",4);
                SFSI('input[name="ln_api_key"]').addClass('inputError');
                return false;
            } 
              
          if(!sfsi_validator(SFSI('input[name="ln_secret_key"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a valid secret ",4);
                SFSI('input[name="ln_secret_key"]').addClass('inputError');
                return false;
            }
          if(!sfsi_validator(SFSI('input[name="ln_oAuth_user_token"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a oAuth Access Token",4);
                SFSI('input[name="ln_oAuth_user_token"]').addClass('inputError');
                return false;
            }
         
        }
        if(SFSI('input[name="sfsi_linkedIn_countsFrom"]:checked').val()=='manual' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_linkedIn_manualCounts"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter LinkedIn manual counts ",4);
                SFSI('input[name="sfsi_linkedIn_manualCounts"]').addClass('inputError');
                return false;
            }      
        }
        
  }
  /* validate youtube */
  if(sfsi_validator(SFSI('input[name="sfsi_youtube_countsDisplay"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_youtube_countsDisplay"]'),'checked'))
  {    
        
        if(SFSI('input[name="sfsi_youtube_countsFrom"]:checked').val()=='subscriber' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_youtube_user"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a youtube user name",4);
                SFSI('input[name="sfsi_youtube_user"]').addClass('inputError');
                return false;
            }      
        }
        if(SFSI('input[name="sfsi_youtube_countsFrom"]:checked').val()=='manual' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_youtube_manualCounts"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter youtube manual counts ",4);
                SFSI('input[name="sfsi_youtube_manualCounts"]').addClass('inputError');
                return false;
            }      
        }
  }
   /* validate pinterest */
  if(sfsi_validator(SFSI('input[name="sfsi_pinterest_countsDisplay"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_pinterest_countsDisplay"]'),'checked'))
  {    
        
       
        if(SFSI('input[name="sfsi_pinterest_countsFrom"]:checked').val()=='manual' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_pinterest_manualCounts"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter Pinterest manual counts ",4);
                SFSI('input[name="sfsi_pinterest_manualCounts"]').addClass('inputError');
                return false;
            }      
        }
  }
  /* validate instagram */
  if(sfsi_validator(SFSI('input[name="sfsi_instagram_countsDisplay"]'),'activte') && sfsi_validator(SFSI('input[name="sfsi_instagram_countsDisplay"]'),'checked'))
  {    
        
       
        if(SFSI('input[name="sfsi_instagram_countsFrom"]:checked').val()=='manual' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_instagram_manualCounts"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter Instagram manual counts ",4);
                SFSI('input[name="sfsi_instagram_manualCounts"]').addClass('inputError');
                return false;
            }      
        }
        if(SFSI('input[name="sfsi_instagram_countsFrom"]:checked').val()=='followers' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_instagram_User"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter a instagram user name",4);
                SFSI('input[name="sfsi_instagram_User"]').addClass('inputError');
                return false;
            }      
        }
  }
  
  
    return true;

}

function sfsi_validationStep5()
{
   //var class_name= SFSI(element).hasAttr('sfsi_validate');
  
 
  /* validate size   */
 
        if(!sfsi_validator(SFSI('input[name="sfsi_icons_size"]'),'int'))
        {   
            showErrorSuc("error","Error : Please enter a numeric value only ",5);
            SFSI('input[name="sfsi_icons_size"]').addClass('inputError');
            
            
            return false;
        } 
       if(parseInt(SFSI('input[name="sfsi_icons_size"]').val())>100)
        {   
            showErrorSuc("error","Error : Icons Size allow  100px maximum ",5);
            SFSI('input[name="sfsi_icons_size"]').addClass('inputError');
            
            
            return false;
        }
       if(parseInt(SFSI('input[name="sfsi_icons_size"]').val())<=0)
        {   
            showErrorSuc("error","Error : Icons Size should be more than 0 ",5);
            SFSI('input[name="sfsi_icons_size"]').addClass('inputError');
            
            
            return false;
        }  
   /* validate spacing   */      
        if(!sfsi_validator(SFSI('input[name="sfsi_icons_spacing"]'),'int'))
        {   
            showErrorSuc("error","Error : Please enter a numeric value only ",5);
            SFSI('input[name="sfsi_icons_spacing"]').addClass('inputError');
            
            
            return false;
        }
         if(parseInt(SFSI('input[name="sfsi_icons_spacing"]').val())<0)
        {   
            showErrorSuc("error","Error : Icons Spacing should be 0 or more",5);
            SFSI('input[name="sfsi_icons_spacing"]').addClass('inputError');
            
            
            return false;
        }  
        
    /* icons per row  spacing   */      
        if(!sfsi_validator(SFSI('input[name="sfsi_icons_perRow"]'),'int'))
        {   
            showErrorSuc("error","Error : Please enter a numeric value only ",5);
            SFSI('input[name="sfsi_icons_perRow"]').addClass('inputError');
            
            
            return false;
        }
        if(parseInt(SFSI('input[name="sfsi_icons_perRow"]').val())<=0)
        {   
            showErrorSuc("error","Error : Icons Per row should be more than 0",5);
            SFSI('input[name="sfsi_icons_perRow"]').addClass('inputError');
            
            
            return false;
        }   
        
       /* validate icons effects   */      
        // if(SFSI('input[name="sfsi_icons_float"]:checked').val()=="yes" && SFSI('input[name="sfsi_icons_stick"]:checked').val()=="yes")
        // {   
        //     showErrorSuc("error","Error : Only one allow from Sticking & floating ",5);
        //     SFSI('input[name="sfsi_icons_float"][value="no"]').prop("checked", true);
        //     return false;
        // }
 
    
    return true;

}

function sfsi_validationStep7()
{
   //var class_name= SFSI(element).hasAttr('sfsi_validate');
  
   /* validate border thikness   */      
        if(!sfsi_validator(SFSI('input[name="sfsi_popup_border_thickness"]'),'int'))
        {   
            showErrorSuc("error","Error : Please enter a numeric value only ",7);
            SFSI('input[name="sfsi_popup_border_thickness"]').addClass('inputError');
            
            
            return false;
        }
 /* validate fotn size   */      
        if(!sfsi_validator(SFSI('input[name="sfsi_popup_fontSize"]'),'int'))
        {   
            showErrorSuc("error","Error : Please enter a numeric value only ",7);
            SFSI('input[name="sfsi_popup_fontSize"]').addClass('inputError');
            
            
            return false;
        }
  /* validate pop up shown    */
 
        if(SFSI('input[name="sfsi_Shown_pop"]:checked').val()=='once' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_Shown_popupOnceTime"]'),'blank') && !sfsi_validator(SFSI('input[name="sfsi_Shown_popupOnceTime"]'),'url'))
            {   showErrorSuc("error","Error : Please Enter a valid pop up shown time ",7);
                SFSI('input[name="sfsi_Shown_popupOnceTime"]').addClass('inputError');
                return false;
            }      
        }
    /* validate page ids   */     
     if(SFSI('input[name="sfsi_Show_popupOn"]:checked').val()=='selectedpage' )
        {   
            
          if(!sfsi_validator(SFSI('input[name="sfsi_Show_popupOn"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter page ids with comma  ",7);
                SFSI('input[name="sfsi_Show_popupOn"]').addClass('inputError');
                return false;
            }      
        }   
        
   /* validate spacing   */      
        if(!sfsi_validator(SFSI('input[name="sfsi_icons_spacing"]'),'int'))
        {   
            showErrorSuc("error","Error : Please enter a numeric value only ",7);
            SFSI('input[name="sfsi_icons_spacing"]').addClass('inputError');
            
            
            return false;
        }
    /* icons per row  spacing   */      
        if(!sfsi_validator(SFSI('input[name="sfsi_icons_perRow"]'),'int'))
        {   
            showErrorSuc("error","Error : Please enter a numeric value only ",7);
            SFSI('input[name="sfsi_icons_perRow"]').addClass('inputError');
            
            
            return false;
        }    
  
 
    
    return true;

}


function sfsi_validator(element,valType)
{  
	var Vurl = new RegExp("^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
    //var Vurl = /http:\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{3}/;

    switch(valType) {
        case "blank"     : if(!element.val().trim()) return false; else return true;
        break;
        case "url"       : if(!Vurl.test(element.val().trim())) return false; else return true;
        break;
        case "checked"   : if(!element.attr('checked')===true) return false; else return true;
        break;
        case "activte"   : if(!element.attr('disabled')) return true; else return false;
        break;
        case "int"       : if(!isNaN(element.val())) return true; else return false;
        break;
        
       }
}

