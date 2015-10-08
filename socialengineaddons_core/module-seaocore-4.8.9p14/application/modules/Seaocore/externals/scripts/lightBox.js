en4.core.runonce.add(function() {
  //HREF
  var href = '';
  
  //TAG NAME
  var tagName = '';
  
  //ONLY OPEN LIGHTBOX WHEN FLAG IS ONE 
  if( flag != 0 ) {
    if($$('.thumbs_photo')) {
      $$('.thumbs_photo').each(function(el){ 
				var addEventClickFlag=true;
        if(el.getProperty('onclick') != null)
          addEventClickFlag=false;
				
				var	parentEl=el.getParent('.generic_layout_container');
				  if(parentEl) {
						parentEl.get('class').split(' ').each(function(className){
							className = className.trim();
						
						if( className.match(/sitealbum/) ) {   
							 addEventClickFlag=false;
							}
						});
					}
				if(addEventClickFlag){	
					el.removeEvents('click').addEvent('click', function(e) { 
						var href = this.href;  
						if( href.match(/photo_id/) ) {   
								e.stop();
								openSeaocoreLightBox(href);
						}
					}); 
				}
      });
    }  
  }
  //SHOW IMAGES IN THE LIGHTBOX FOR THE ACTIVITY FEED
  if( activityfeed_lightbox != 0 ) {
    //DISPLAY ACTIVITY FEED IMAGES IN THE LIGHTBOX FOR THE GROUP 
    addSEAOPhotoOpenEventLightbox(Array('feed_attachment_group_photo','feed_attachment_event_photo','feed_attachment_sitepage_photo','feed_attachment_list_photo','feed_attachment_recipe_photo','feed_attachment_sitepagenote_photo','feed_attachment_album_photo','feed_attachment_sitebusiness_photo','feed_attachment_sitebusinessnote_photo','feed_attachment_sitegroup_photo','feed_attachment_sitegroupnote_photo','feed_attachment_sitestore_photo','feed_attachment_sitestorenote_photo', 'seaocore_comments_attachment_photo'));
  }
  
  function addSEAOPhotoOpenEventLightbox(classnames){
    classnames.each(function(classname) {    
    classname="."+classname;
      if($$(classname)) {
				$$(classname).each(function(el) {       
					if(el.getElement('.thumb_profile')) {
						el.getElement('.thumb_profile').removeEvents('click').addEvent('click', function(e) {
							e.stop();     
							href = openLightboxforActivityFeed(el);
							openSeaocoreLightBox(href);
						});   
					} 
					else {
						el.getElement('.thumb_normal').removeEvents('click').addEvent('click', function(e) {
							e.stop();     
							href = openLightboxforActivityFeed(el);
							openSeaocoreLightBox(href);
						});
					}

				});
      }
    });
  }

});

/*  
  RETURN HREF
*/
function openLightboxforActivityFeed(spanElement) {
  if(spanElement.getElement('.feed_item_thumb')) {
    href = spanElement.getElement('.feed_item_thumb');  
  } 
  else {
    var tagName = spanElement.getElementsByTagName('a'); 
    for (i = 0; i <= tagName.length-1; i++)
    {
      href = tagName[i];
    } 
  }
  return href;
}

/*  
  OPEN IMAGES IN LIGHTBOX
*/
var lightbox_communityads_hidden,scrollPosition={left:0,top:0};
function openSeaocoreLightBox(href){
	
	if(document.getElementById('showlocation'))
		document.getElementById('showlocation').style.display = 'none';

  scrollPosition['top']=window.getScrollTop();
  scrollPosition['left']=window.getScrollLeft(); 
	lightbox_communityads_hidden='';
  if(document.getElementById('seaocore_photo_lightbox'))
    document.getElementById('seaocore_photo_lightbox').style.display = 'block';
  setHtmlScroll("hidden");
  if(document.getElementById('media_image_div_seaocore'))
    document.getElementById('media_image_div_seaocore').innerHTML="<img src='application/modules/Seaocore/externals/images/icons/loader.gif' class='lightbox_loader_img' />";
  en4.core.request.send(new Request.HTML({
    method : 'get',
    'url' : href, 
    'data' : {
      format : 'html',
      'lightbox_type' : 'photo'
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {     

      if(document.getElementById('white_content_default_sea_lightbox'))
        $('white_content_default_sea_lightbox').innerHTML = responseHTML;
      if($('community_sea_ads') && $('lightbox_communityads_hidden')){
        $('community_sea_ads').innerHTML =  $('lightbox_communityads_hidden').innerHTML;
        $('lightbox_communityads_hidden').innerHTML='';
      }
			Smoothbox.bind($("display_current_location"));
			en4.core.runonce.trigger();			
    }
    }), {"force":true});
	//request.send();
}

/*  
  SET VARIABLES
*/
var locationHref=window.location.href;
var defaultLoad=true;

/*  
  GET LIGHTBOX CONTENT
*/
window.addEvent('domready', function() {
  if(document.getElementById('white_content_default_sea_lightbox')) {
    $('white_content_default_sea_lightbox').addEvent('click', function(event) {
      event.stopPropagation();
    });
	}
});


/*  
  CLOSE LIGHTBOX
*/
var closeSEALightBoxAlbum = function()
{
  defaultLoad=true;
  document.getElementById('seaocore_photo_lightbox').style.display='none';
  setHtmlScroll("auto");
  window.scroll(scrollPosition['left'],scrollPosition['top']); // horizontal and vertical scroll targets
  if(document.getElementById('photo_sea_lightbox_text')){
    document.getElementById('photo_sea_lightbox_text').innerHTML="";
    document.getElementById('photo_sea_lightbox_text').style.display="none";
  }
  if(document.getElementById('photo_sea_lightbox_photo_detail'))
    document.getElementById('photo_sea_lightbox_photo_detail').style.display="none";
  if(document.getElementById('photo_sea_lightbox_user_options'))
    document.getElementById('photo_sea_lightbox_user_options').style.display="none";
  if(document.getElementById('seaocore_photo_scroll'))
    document.getElementById('seaocore_photo_scroll').style.display="none";
  if(document.getElementById('photo_sea_lightbox_user_right_options'))
    document.getElementById('photo_sea_lightbox_user_right_options').style.display="none";
  if(document.getElementById('photo_view_comment'))
    document.getElementById('photo_view_comment').innerHTML="";

  if (history.replaceState)
    history.replaceState( {}, document.title, locationHref );
  else{  
		window.location.hash = "0";
  }

  if(document.getElementById('canReloadSeaocore').value==1){
    window.location.reload(true);
  }
};

/*  
  GET NEXT AND PREVIOUS PHOTO
*/

function photopaginationSocialenginealbum(url, modulename, tab_id) {
	if(document.getElementById('showlocation'))
		document.getElementById('showlocation').style.display = 'none';
	if($('community_sea_ads') && $('lightbox_communityads_hidden')){
		//$('lightbox_communityads_hidden').innerHTML = $('community_sea_ads').innerHTML;
		lightbox_communityads_hidden = $('community_sea_ads').innerHTML;
		$('lightbox_communityads_hidden').innerHTML='';
	}
  var photoUrl=url.replace("/seaocore/", "/"+modulename+"/");
  if(tab_id != '')
    photoUrl = photoUrl + '/tab/' + tab_id;
  if (history.replaceState)
    history.replaceState( {}, document.title, photoUrl );
  else{
    window.location.hash = photoUrl;
  }
  if(document.getElementById('photo_sea_lightbox_photo_detail'))
    document.getElementById('photo_sea_lightbox_photo_detail').style.display="none";
	
  setHtmlScroll("hidden");
  setImageScrollAlbum("auto");

  if(document.getElementById('media_image_div_seaocore'))
    document.getElementById('media_image_div_seaocore').innerHTML="<img src='application/modules/Seaocore/externals/images/icons/loader.gif'  class='lightbox_loader_img' />";
  en4.core.request.send(new Request.HTML({
    url : url,
    data : {
      format : 'html',
      module_name : modulename,
      tab : tab_id,
      is_ajax_lightbox : 1
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      setHtmlScroll("hidden");
      setImageScrollAlbum("auto");
      $('white_content_default_sea_lightbox').innerHTML = responseHTML;
      if($('community_sea_ads')){
        $('community_sea_ads').innerHTML =  lightbox_communityads_hidden;
      }
			if(typeof initializemap == 'function') {
				initializemap();
			}

    }
   }), {"force":true});
}

/*  
  SET HTML SCROLLING
*/
function setHtmlScroll(cssCode) {
  $$('html').setStyle('overflow',cssCode);
}

/*  
  SET IMAGE SCROLLING
*/
function setImageScrollAlbum(cssCode) {
  $$('.photo_lightbox_white_content_wrapper').setStyle('overflow',cssCode);
}


/*  
  OPEN URLS IN SMOOTHBOX
*/
function showSmoothBox(url)
{
  Smoothbox.open(url);
  parent.Smoothbox.close;
}

/*  
 EDIT THE DESCRIPTION
*/
function showeditDescription(){
  if(document.getElementById('edit_seaocore_description')){
    if(document.getElementById('link_seaocore_description').style.display=="block"){
      document.getElementById('link_seaocore_description').style.display="none";
      document.getElementById('edit_seaocore_description').style.display="block";
      $('editor_seaocore_description').focus();
    } else{
      document.getElementById('link_seaocore_description').style.display="block";
      document.getElementById('edit_seaocore_description').style.display="none";
    }

    if(document.getElementById('seaocore_description_loading'))
      document.getElementById('seaocore_description_loading').style.display="none";
  }
}



/*  
 SET LOADER IMAGE IN THE LIGHTBOX
*/
var loadingImageSeaocore = function() {
  if(document.getElementById('media_image_div_seaocore'))
    $('media_photo').src = "application/modules/Seaocore/externals/images/icons/loader.gif";
  $('media_photo').style.marginTop='245px';
};

