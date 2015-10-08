en4.core.runonce.add(function() {
  if(typeof en4.core.staticBaseUrl == 'undefined')
    en4.core.staticBaseUrl ='';
  //HREF
  var href = '';  
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
    addSEAOPhotoOpenEvent(Array('feed_attachment_group_photo','feed_attachment_event_photo','feed_attachment_sitepage_photo','feed_attachment_list_photo','feed_attachment_recipe_photo','feed_attachment_sitepagenote_photo','feed_attachment_album_photo','feed_attachment_sitebusiness_photo','feed_attachment_sitebusinessnote_photo','feed_attachment_sitegroup_photo','feed_attachment_sitegroupnote_photo','feed_attachment_sitegroupevent_photo','feed_attachment_sitebusinessevent_photo','feed_attachment_sitepageevent_photo'));
  }
});

function addSEAOPhotoOpenEvent(classnames){
  classnames.each(function(classname) {    
    classname="."+classname;
    if($$(classname)) {
      $$(classname).each(function(el) {       
        if(el.getElement('.thumb_profile')) {
          el.getElement('.thumb_profile').removeEvents('click').addEvent('click', function(e) {
            e.stop();     
            href = openLightboxforActivityFeedHREF(el);
            openSeaocoreLightBox(href);
          });   
        } else {
          el.getElement('.thumb_normal').removeEvents('click').addEvent('click', function(e) {
            e.stop();     
            href = openLightboxforActivityFeedHREF(el);
            openSeaocoreLightBox(href);
          });
        }

      });
    }
  });
}
/*  
  RETURN HREF
*/
function openLightboxforActivityFeedHREF(spanElement) {
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
var lightbox_communityads_hidden;
var locationHref = window.location.href,defaultLoad = true,defaultSEAOLBAlbumPhotoContent = '',fullmode_photo=false,addAgainscrollFalg=true,rightSidePhotoContent,canClosePhotoLB=true,scrollPosition = {
  left:0,
  top:0
},loadedAllPhotos = '',contentPhotoSizeSEAO={
  width:0,
  height:0
};


var createDefaultContentAdvLBSEAO=function(element){ 
  new Element('input', {
    'id' : 'canReloadSeaocore',
    'type' : 'hidden',
    'value' :0      
  }).inject(element);
  new Element('div', {      
    'class' : 'photo_lightbox_overlay'      
  }).inject(element);
  new Element('div', {
    'id' : 'photo_lightbox_close',
    'class' : 'photo_lightbox_close',
    'onclick' : "closeSEAOLightBoxAlbum()",
    'title':en4.core.language.translate("Press Esc to Close")        
  }).inject(element);
   
  var photoContentDiv = new Element('div', {
    'id' : 'white_content_default_sea_lightbox',
    'class' : 'photo_lightbox_content_wrapper'         
  });     
  var photolbCont= new Element('div', {      
    'class' : 'photo_lightbox_cont'         
  }).inject(photoContentDiv);
  if(en4.orientation=='ltr'){   
    var photolbLeft = new Element('div', {
      'id' : 'photo_lightbox_seaocore_left',
      'class' : 'photo_lightbox_left',
      'styles' : {
        'right' : '1px'       
      }
    }).inject(photolbCont);
  }else{
    var photolbLeft = new Element('div', {
      'id' : 'photo_lightbox_seaocore_left',
      'class' : 'photo_lightbox_left',
      'styles' : {
        'left' : '1px'       
      }
    }).inject(photolbCont);
  }
    
  var photolbLeftTable = new Element('table', {
    'width' : '100%',
    'height' : '100%'
  }).inject(photolbLeft); 
  var photolbLeftTableTr = new Element('tr', {    
    }).inject(photolbLeftTable); 
      
  var photolbLeftTableTrTd = new Element('td', {
    'width' : '100%',
    'height' : '100%',
    'valign':'middle'
  }).inject(photolbLeftTableTr);
    
  new Element('div', {
    'id' : 'media_image_div_seaocore',
    'class' : 'photo_lightbox_image'      
  }).inject(photolbLeftTableTrTd);
  new Element('div', {     
    'class' : 'lightbox_btm_bl'     
  }).inject(photoContentDiv);
  photoContentDiv.inject(element);    
  photoContentDiv.addEvent('click', function(event) {
    event.stopPropagation();
  });
};

function openSeaocoreLightBox(href){ 
  if(!$("white_content_default_sea_lightbox")){
    createDefaultContentAdvLBSEAO($("seaocore_photo_lightbox"));    
  }

  if(document.getElementById('seaocore_photo_lightbox'))
    document.getElementById('seaocore_photo_lightbox').style.display = 'block';
  if($('arrowchat_base'))
    $('arrowchat_base').style.display = 'none';
  if($('wibiyaToolbar'))
    $('wibiyaToolbar').style.display = 'none';
  scrollPosition['top']=window.getScrollTop();
  scrollPosition['left']=window.getScrollLeft();
  setHtmlScroll("hidden");
  
  
  getSEAOCorePhoto(href,0);
}
/*  
  GET NEXT AND PREVIOUS PHOTO
*/

function photopaginationSocialenginealbum(href,params,imagepath) { 
  getSEAOCorePhoto(href,1,params,imagepath);
}
function getSEAOCorePhoto(href,isajax,params,imagepath){
 
  if (history.replaceState) {
    history.replaceState( {}, document.title, href );
  } else {
    window.location.hash = href;
  }
  if(isajax==0){
    document.getElementById('media_image_div_seaocore').innerHTML = "&nbsp;<img class='photo_lightbox_loader' src='"+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/icons/loader-large.gif'+"'  />";
  }else{
    $$(".lightbox_btm_bl").each(function(el){
      el.innerHTML="<center><img src='"+en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/icons/loader-large.gif' style='height:30px;' /> </center>";
    }); 
  } 
    if (isajax) 
  document.getElementById('media_image_div_seaocore').innerHTML = "&nbsp;<img class='lightbox_photo' src=" + imagepath + " style='max-width: " + contentPhotoSizeSEAO['width'] + "px; max-height: " + contentPhotoSizeSEAO['height'] + "px;'  />";
  var remove_extra = 2;
  contentPhotoSizeSEAO['height'] = $("photo_lightbox_seaocore_left").getCoordinates().height - remove_extra;
  if(isajax == 0 )
    remove_extra = remove_extra + 289;
  contentPhotoSizeSEAO['width'] = $("photo_lightbox_seaocore_left").getCoordinates().width - remove_extra;

  addAgainscrollFalg = true;
  en4.core.request.send(new Request.HTML({      
    method : 'get',
    'url' : href, 
    'data' : $merge(params,{
      format : 'html',
      'lightbox_type' : 'photo',
      //  module_name : modulename,
      // tab : tab_id,
      is_ajax_lightbox : isajax
    }),
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if($('white_content_default_sea_lightbox')){
        $('white_content_default_sea_lightbox').innerHTML = responseHTML;        
        switchFullModePhotoSEAO(fullmode_photo);
      }
      
      Smoothbox.bind($("display_current_location"));
      en4.core.runonce.trigger();
    }
  }), {
    "force":true
  });
}



/*  
  CLOSE LIGHTBOX
*/
var closeSEAOLightBoxAlbum = function()
{
  if(fullScreenApi.isFullScreen()){
    fullScreenApi.cancelFullScreen()
  } else{
    defaultLoad = true;
    document.getElementById('seaocore_photo_lightbox').style.display = 'none';
    setHtmlScroll("auto");
    window.scroll(scrollPosition['left'],scrollPosition['top']); // horizontal and vertical scroll targets
    if($('arrowchat_base'))
      $('arrowchat_base').style.display = 'block';
    if($('wibiyaToolbar'))
      $('wibiyaToolbar').style.display = 'block';
    if (history.replaceState)
      history.replaceState( {}, document.title, locationHref );
    else{  
      window.location.hash = "0";
    }
    if($type(keyDownEventsSEAOCorePhoto))
      document.removeEvent("keydown",keyDownEventsSEAOCorePhoto);
    if($type(keyUpLikeEventSEAOCorePhoto))
      document.removeEvent("keyup" , keyUpLikeEventSEAOCorePhoto);  
    if(document.getElementById('canReloadSeaocore').value == 1){
      window.location.reload(true);
    }
    loadedAllPhotos = '';
    document.getElementById('seaocore_photo_lightbox').empty();
    fullmode_photo = false;
  }
};


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
function saveEditDescriptionPhotoSEAO(photo_id,resourcetype)
{
    
  var str = document.getElementById('editor_seaocore_description').value.replace('/\n/g','<br />');
  var str_temp = document.getElementById('editor_seaocore_description').value;
   
  if(document.getElementById('seaocore_description_loading'))
    document.getElementById('seaocore_description_loading').style.display="";
  document.getElementById('edit_seaocore_description').style.display="none";
  en4.core.request.send(new Request.HTML({
    url :en4.core.baseUrl +'seaocore/photo/edit-description',
    data : {
      format : 'html',
      text_string : str_temp,
      photo_id : photo_id,
      resource_type : resourcetype
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(str=='')
        str_temp=en4.core.language.translate('Add a caption');
      document.getElementById('seaocore_description').innerHTML=str_temp.replace(/\n/gi, "<br /> \n");
      showeditDescriptionSEAO();
    }
  }), {
    "force":true
  });
}
/*  
 EDIT THE DESCRIPTION
*/
function showeditDescriptionSEAO(){
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
 EDIT THE TITLE
*/
function showeditPhotoTitleSEAO(){
  if(document.getElementById('edit_seaocore_title')){
    if(document.getElementById('link_seaocore_title').style.display=="block"){
      document.getElementById('link_seaocore_title').style.display="none";
      document.getElementById('edit_seaocore_title').style.display="block";
      $('editor_seaocore_title').focus();
    } else{
      document.getElementById('link_seaocore_title').style.display="block";
      document.getElementById('edit_seaocore_title').style.display="none";
    }

    if(document.getElementById('seaocore_title_loading'))
      document.getElementById('seaocore_title_loading').style.display="none";
  }
}

function saveEditTitlePhotoSEAO(photo_id,resourcetype)
{
   
  var str = document.getElementById('editor_seaocore_title').value.replace('/\n/g','<br />');
  var str_temp = document.getElementById('editor_seaocore_title').value;   
  if(document.getElementById('seaocore_title_loading'))
    document.getElementById('seaocore_title_loading').style.display="";
  document.getElementById('edit_seaocore_title').style.display="none";
  en4.core.request.send(new Request.HTML({
    url :en4.core.baseUrl+'seaocore/photo/edit-title',
    data : {
      format : 'html',
      text_string : str_temp,
      photo_id : photo_id,
      resource_type : resourcetype
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(str=='')
        str_temp=en4.core.language.translate('Add a title');
      document.getElementById('seaocore_title').innerHTML=str_temp;
      showeditPhotoTitleSEAO();
    }
  }), true);
}  

//---------------------------------------------------------
var // Close the All Photo Contener
closeAllPhotoContener=function(){  
  $("all_photos").style.height = "0px";
  $("close_all_photos").style.height = "0px";
  $("close_all_photos_btm").style.height = "0px";
},
// View all photos of the album in bottom
showAllSEAOPhotoContener = function(subjectguid,photo_id,count_photo){
  var onePhotoSizeW = 144,onePhotoSizeH = 112;
  heightContent = onePhotoSizeH + 60;
  var inOneRow = Math.ceil((window.getSize().x/(onePhotoSizeW+40)));
  if(count_photo > inOneRow){    
    heightContent = heightContent + onePhotoSizeH -2;    
  }
 
  $("all_photos").style.height = heightContent+"px";
  $("close_all_photos").style.height = "100%"; 
  $("close_all_photos_btm").style.height = "60px"; 
  $("photos_contener").setStyle("max-height",(heightContent-40)+"px")
  if(loadedAllPhotos !=''){
    $("photos_contener").empty();
    Elements.from(loadedAllPhotos).inject($("photos_contener"));
    onclickPhotoThumb($("lb-all-thumbs-photo-" + photo_id)); 
    if(addAgainscrollFalg) {
      new SEAOMooVerticalScroll('main_photos_contener', 'photos_contener', {} );
      addAgainscrollFalg = false;
    }
  }else{
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl+'seaocore/photo/get-all-photos',
      data : {
        format : 'html',
        subjectguid : subjectguid
      // photo_id : photo_id
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $("photos_contener").empty();
        loadedAllPhotos=responseHTML;
        Elements.from(responseHTML).inject($("photos_contener"));
        onclickPhotoThumb($("lb-all-thumbs-photo-"+photo_id)); 
        new SEAOMooVerticalScroll('main_photos_contener', 'photos_contener',{});
        addAgainscrollFalg=false;
      }
    }), {
      "force":true
    });
  }
},
// Selected the Thumb
onclickPhotoThumb =function (element){
  if( element.tagName.toLowerCase() == 'a' ) {
    element = element.getParent('li');
  }
  var myContainer = element.getParent('.lb_photos_contener').getParent(); 
  myContainer.getElements('ul > li').removeClass('sea_val_photos_thumbs_selected');          
  element.addClass('sea_val_photos_thumbs_selected');     
   
},
showPhotoToggleContent=function (element_id){
  var el = $(element_id);
  el.toggleClass('sea_photo_box_open');
  el.toggleClass('sea_photo_box_closed');
},// Hide and Show Right Side Box
switchFullModePhotoSEAO=function(fullmode){
  if(!$("full_screen_display_captions_on_image"))
    return;
  if(fullmode){
    fullScreenApi.requestFullScreen(document.body);
    if($("photo_owner_lb_fullscreen"))
      $("photo_owner_lb_fullscreen").style.display = 'block';
    if($("photo_owner_titile_lb_fullscreen"))
      $("photo_owner_titile_lb_fullscreen").style.display = 'block';
    if($("photo_owner_titile_lb_fullscreen_sep"))
      $("photo_owner_titile_lb_fullscreen_sep").style.display = 'block';
    $("full_screen_display_captions_on_image").style.display = 'block';
    $("photo_lightbox_right_content").style.width = '1px'; 
    $("photo_lightbox_right_content").style.visibility = 'hidden'; 
    if(en4.orientation=='ltr')
      $("photo_lightbox_seaocore_left").style.right = '1px';
    else
      $("photo_lightbox_seaocore_left").style.left = '1px';
    $("full_mode_photo_button").style.display = 'none';
    $("comment_count_photo_button").style.display = 'block'; 
    if($("full_screen_display_captions_on_image_dis")){      
      (function(){
        if(!$("media_photo"))return;
        var width_ln=  $("media_photo").getCoordinates().width;
        var total_char=2 *(width_ln/6).toInt();
        if(total_char <= 100 ) total_char=100;
        var str = $("full_screen_display_captions_on_image_dis").innerHTML;
        if(str.length >total_char){
          $("full_screen_display_captions_on_image_dis").innerHTML=str.substr(0,(total_char-3))+"...";
        }
      }).delay(50);
    }
  }else{     
    if($("photo_owner_lb_fullscreen"))
      $("photo_owner_lb_fullscreen").style.display = 'none';
    if($("photo_owner_titile_lb_fullscreen"))
      $("photo_owner_titile_lb_fullscreen").style.display = 'none';
    if($("photo_owner_titile_lb_fullscreen_sep"))
      $("photo_owner_titile_lb_fullscreen_sep").style.display = 'none';
    $("full_screen_display_captions_on_image").style.display = 'none';
    $("photo_lightbox_right_content").style.width = '300px';
    $("photo_lightbox_right_content").style.visibility = 'visible'; 
    if(en4.orientation=='ltr')
      $("photo_lightbox_seaocore_left").style.right = '300px';
    else
      $("photo_lightbox_seaocore_left").style.left = '300px';
    $("full_mode_photo_button").style.display = 'block';
    $("comment_count_photo_button").style.display = 'none';        
  }
    
  fullmode_photo = fullmode; 
  contentPhotoSizeSEAO['height'] = $("photo_lightbox_seaocore_left").getCoordinates().height -2;      
  contentPhotoSizeSEAO['width'] = $("photo_lightbox_seaocore_left").getCoordinates().width - 2;    
  setPhotoContentSEAO();
}, 

setPhotoContentSEAO =function(){
  if($("media_photo")){
      
    $("media_photo").setStyle("max-width",contentPhotoSizeSEAO['width'] + "px");
    $("media_photo").setStyle("max-height",contentPhotoSizeSEAO['height'] + "px");
    $("media_photo_next").setStyle("max-width",contentPhotoSizeSEAO['width'] + "px");
    $("media_photo_next").setStyle("max-height",contentPhotoSizeSEAO['height'] + "px");  
    setTimeout("getTaggerInstanceSEAO()",1250);
  }
}; 
// ADD Fullscreen api
(function() {  
  var api = {  
    supportsFullScreen: false,  
    isFullScreen: function() {
      return false;
    },  
    requestFullScreen: function() {},  
    cancelFullScreen: function() {},  
    fullScreenEventName: '',  
    prefix: ''  
  },  
    
  browserPrefixes = 'webkit moz o ms khtml'.split(' ');  
    
  // Check for native support.  
  if (typeof document.cancelFullScreen != 'undefined') {  
    api.supportsFullScreen = true;  
  } else {  
    // Check for fullscreen support by browser prefix.  
    for (var i = 0, il = browserPrefixes.length; i < il; i++ ) {  
      api.prefix = browserPrefixes[i];  
      functionName = api.prefix + 'CancelFullScreen';  
        
      if (typeof document[functionName] != 'undefined') {  
        api.supportsFullScreen = true;  
        break;  
      }  
    }  
  }  
    
  // Update methods.  
  if (api.supportsFullScreen) {  
    api.fullScreenEventName = api.prefix + 'fullscreenchange';  
      
    api.isFullScreen = function() {  
      switch (this.prefix) {  
        case '':
          return document.fullScreen;  
        case 'webkit':
          return document.webkitIsFullScreen;  
        default:
          return document[this.prefix + 'FullScreen'];  
      }  
    }  
    api.requestFullScreen = function(el) {  
      switch (this.prefix) {  
        case '':
          return el.requestFullScreen();
        case 'webkit':
          /* @TODO:: INPUT KEYS (A-I) NOT WORKING*/
          return /*el.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT)*/;
        default:
          return el[this.prefix + 'RequestFullScreen']();
      } 

    }  
    api.cancelFullScreen = function(el) {  
      if (this.prefix === '') {  
        return document.cancelFullScreen();  
      } else {  
        return document[this.prefix + 'CancelFullScreen']();  
      }  
    }  
  }  
    
  // Export api.  
  window.fullScreenApi = api;  
})();  
  
if(fullScreenApi.supportsFullScreen===true){
  document.addEventListener(fullScreenApi.fullScreenEventName, function(e) {
    if(document.getElementById('seaocore_photo_lightbox').style.display != 'block')
      return;
    switchFullModePhotoSEAO(fullScreenApi.isFullScreen());
    var html_titile=en4.core.language.translate("Press Esc to Close");
    if(fullScreenApi.isFullScreen()){
      html_titile=en4.core.language.translate("Press Esc to exit Full-screen");
    }
    $("photo_lightbox_close").title=html_titile;
    resetPhotoContentSEAO();
    if(typeof rightSidePhotoContent != 'undefined')
      rightSidePhotoContent.update();
  },true);
}
  
var resetPhotoContentSEAO=function(){
  if($('ads')){
    if($('ads_hidden')){
      if($('ads').getCoordinates().height < 30){
        $('ads').empty();
      }
      adsinnerHTML= $('ads').innerHTML;      
    }else{
      $('ads').innerHTML = adsinnerHTML;
    }
    (function(){
      if(!$('ads')) return;
      $('ads').style.bottom="0px";
      if($('photo_lightbox_right_content').getCoordinates().height < ($('photo_right_content').getCoordinates().height+$('ads').getCoordinates().height+10)){
        $('ads').empty();
        $('main_right_content_area').style.height= $('photo_lightbox_right_content').getCoordinates().height -2 +"px";
        $('main_right_content').style.height= $('photo_lightbox_right_content').getCoordinates().height -2 +"px";
      }else{
        $('main_right_content_area').style.height= $('photo_lightbox_right_content').getCoordinates().height - ($('ads').getCoordinates().height+10)+"px";
        $('main_right_content').style.height= $('photo_lightbox_right_content').getCoordinates().height - ($('ads').getCoordinates().height+10)+"px";
      }
    }).delay(1000);
  } 
};
var featuredPhoto=function(subject_guid)
{
  en4.core.request.send(new Request.HTML({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitealbum/photo/featured',
    'data' : {
      format : 'html',
      'subject' : subject_guid
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if($('featured_sitealbum_photo').style.display=='none'){
        $('featured_sitealbum_photo').style.display="";
        $('un_featured_sitealbum_photo').style.display="none";
      }else{
        $('un_featured_sitealbum_photo').style.display="";
        $('featured_sitealbum_photo').style.display="none";
      }
    }
  }), true);

  return false;

},

featuredpagealbumPhoto = function(photo_id)
{
  en4.core.request.send(new Request.HTML({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitepage/photo/featured',
    'data' : {
      format : 'html',
      'photo_id' : photo_id
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if($('featured_sitepagealbum_photo').style.display=='none'){
        $('featured_sitepagealbum_photo').style.display="";
        $('un_featured_sitepagealbum_photo').style.display="none";
      }else{
        $('un_featured_sitepagealbum_photo').style.display="";
        $('featured_sitepagealbum_photo').style.display="none";
      }
    }
  }), true);

  return false;
};

featuredgroupalbumPhoto = function(photo_id)
{
  en4.core.request.send(new Request.HTML({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitegroup/photo/featured',
    'data' : {
      format : 'html',
      'photo_id' : photo_id
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if($('featured_sitegroupalbum_photo').style.display=='none'){
        $('featured_sitegroupalbum_photo').style.display="";
        $('un_featured_sitegroupalbum_photo').style.display="none";
      }else{
        $('un_featured_sitegroupalbum_photo').style.display="";
        $('featured_sitegroupalbum_photo').style.display="none";
      }
    }
  }), true);

  return false;
};

featuredbusinessalbumPhoto = function(photo_id)
{
  en4.core.request.send(new Request.HTML({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitebusiness/photo/featured',
    'data' : {
      format : 'html',
      'photo_id' : photo_id
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if($('featured_sitebusinessalbum_photo').style.display=='none'){
        $('featured_sitebusinessalbum_photo').style.display="";
        $('un_featured_sitebusinessalbum_photo').style.display="none";
      } else{
        $('un_featured_sitebusinessalbum_photo').style.display="";
        $('featured_sitebusinessalbum_photo').style.display="none";
      }
    }
  }), true);

  return false;
};


/*  
  FUNCTION FOR ROTATING AND FLIPING THE IMAGES
*/
en4.photoadvlightbox= {
  rotate : function(photo_id, angle,resourcetype) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/photo/rotate',
      data : {
        format : 'json',
        photo_id : photo_id,
        angle : angle,
        resource_type : resourcetype
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
          $type(response.status) &&
          response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
        $('canReloadSeaocore').value=1;
        $('media_photo').src=response.href;
        $('media_photo').style.marginTop="0px";     
      }
    });
    request.send();
    return request;
  },

  flip : function(photo_id, direction,resourcetype) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/photo/flip',
      data : {
        format : 'json',
        photo_id : photo_id,
        direction : direction,
        resource_type : resourcetype
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
          $type(response.status) &&
          response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess     
        $('canReloadSeaocore').value=1;
        $('media_photo').src=response.href;
        $('media_photo').style.marginTop="0px";
      }
    });
    request.send();
    return request;
  }
};