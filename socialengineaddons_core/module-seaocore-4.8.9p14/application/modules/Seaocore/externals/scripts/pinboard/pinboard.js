/* $Id: pinboard.js 6590 2013-04-01 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */
if(!window.locationsParamsSEAO){
  window.locationsParamsSEAO = {
    latitude:0, 
    longitude:0
  };
  window.locationsDetactSEAO = false;
  window.locationsDetactedSEAO = false;
  window.locationCallBack = [];
}
PinBoardSeao = new Class({
  Implements : [Events, Options],
  widgets : new Array(),
  options : {
  },
  currentIndex : 0,
  currentPage : 1,
  currentActive : false,
  viewMoreEl : null,
  layout : 'middle',
  initialize : function(layout) {
    
    this.layout = layout;
  // doucment.getElement('.layout_middle');
  },
  add : function(params){
    $(params.contentId).getParent('.'+params.responseContainerClass).set('id','pinboard_wrapper_'+params.widgetId);
    
    params.requestParams.content_id = params.widgetId;
    params.responseContainer = $('pinboard_wrapper_'+params.widgetId);

    if(params.requestParams.noOfTimes != 0 && params.totalCount > (params.requestParams.noOfTimes * params.requestParams.itemCount))
      params.totalCount = (params.requestParams.noOfTimes * params.requestParams.itemCount);
    params.active = false;
    this.widgets.push(params);
    if(!this.viewMoreEl){
      this.viewMoreEl =  new Element('div',{
        });
    }
    if(!this.loading){
      this.loading =  new Element('div',{
        'class':'dnone'
      });
    }
    this.loading.inject(params.responseContainer,'after');
    this.viewMoreEl.inject(params.responseContainer,'after');
  },
  start:function(){
    if(this.currentActive)
      return;
    if(this.widgets.length > this.currentIndex){
      this.currentActive = true;
      var params = this.widgets[this.currentIndex];
      params.currentIndex = this.currentInde;
      params.requestParams.contentpage = this.currentPage;
      this.startReq(params);
      if(params.totalCount <= params.requestParams.itemCount * this.currentPage){
        this.currentIndex++;
        this.currentPage = 1;
      }else{
        this.currentPage++;
      }
    }
  },
  viewMore : function(){
    if(!this.viewMoreEl)
      return;
    var self=this;
    var elementPostionY = 0;
    if( typeof( this.viewMoreEl.offsetParent ) != 'undefined' ) {
      elementPostionY = this.viewMoreEl.offsetTop;
    }else{
      elementPostionY = this.viewMoreEl.y; 
    }
    if(elementPostionY <= window.getScrollTop()+(window.getSize().y -10)){
      self.start();  
    }
    
  },
  callBackLocation : function(){
    var fn;
    while( (fn = window.locationCallBack.shift()) ){
      $try(function(){
        fn();
      });
    }
    window.locationCallBack = [];

  },
  startReq: function(params){
   
    var self=this;
    
    params.callBack=this.callBackLocation;
    window.locationCallBack.push(function(){
        params.requestParams= $merge(params.requestParams,window.locationsParamsSEAO);
        self.sendReq(params)
      });
    en4.seaocore.locationBased.startReq(params);
//    if (params.detactLocation && !window.locationsDetactedSEAO && navigator.geolocation) {
//      
//      if(!window.locationsDetactSEAO){
//        navigator.geolocation.getCurrentPosition(function(position){
//          window.locationsParamsSEAO.latitude = position.coords.latitude;
//          window.locationsParamsSEAO.longitude = position.coords.longitude;
//          window.locationsDetactedSEAO = true;
//          self.callBackLocation();
//        },function(){
//           window.locationsDetactedSEAO = true;
//          self.callBackLocation();
//          },{
//            maximumAge:6000,
//            timeout:3000
//          });
//          
//        window.setTimeout(function(){
//          if(window.locationsDetactedSEAO)
//            return;
//          self.callBackLocation();
//        },3000);  
//      }
//      window.locationCallBack.push(function(){
//        params.requestParams= $merge(params.requestParams,window.locationsParamsSEAO);
//        self.sendReq(params)
//      });
//      
//      window.locationsDetactSEAO = true;
//    }else{
//      if(params.detactLocation && window.locationsDetactSEAO)
//        params.requestParams= $merge(params.requestParams,window.locationsParamsSEAO);
//      this.sendReq(params);
//    }
    
  },
  sendReq: function(params){
    var self=this;
    // params.responseContainer.empty();
    this.loading.removeClass('dnone');
    var url = en4.core.baseUrl+'widget';
   
    if(params.requestUrl)
      url= params.requestUrl;
    var request = new Request.HTML({
      url : url,
      data : $merge(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        self.loading.addClass('dnone');
        if($(params.contentId))
        $(params.contentId).destroy();
        if(!self.loading.hasClass('seaocore_loading')){
          self.loading.addClass('seaocore_loading');
          new Element('img', {
            src: en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'
          }).inject(self.loading);
        }
        Elements.from(responseHTML).inject(params.responseContainer);
        
        if(params.requestParams.contentpage == 1){
          var elem = new Element('div',{
            id : 'seaocore_board_wrapper_'+params.widgetId,
            'class' : 'seaocore_board_wrapper'
          }).inject(params.responseContainer);
          params.responseContainer.getElements('.seaocore_list_wrapper').inject(elem);
          params.responseContainer = elem;
          params.active = true;
          self.widgets[params.currentIndex] = params;  
        }
        for (var i=0; i<= 10;i++){
          (function(){
            self.setMasonry(params);
          }).delay(500*i);
        }
       
        en4.core.runonce.trigger();
        Smoothbox.bind(params.responseContainer);
        $$(".pb_ch_wd").addEvent('click',self.childWindowOpen.bind(this));
        self.currentActive=false;
      }
    });
    request.send();
  },
  childWindowOpen:function(event){
    
    var element =  $(event.target);
    if((
      element.get('tag') == 'a' &&
      !element.onclick &&
      element.href &&
      !element.href.match(/^(javascript|[#])/)
      )){
      event.stop();
      open(element.href,element.get('html'),'width=700,height=350,resizable,toolbar,status');
    }
  },
  setMasonry:function(params){
    if(!params.active)return;
    params.responseContainer.pinBoardSeaoMasonry({
      columnWidth: params.requestParams.itemWidth,  //224 columnWidth does not need to be set if singleMode is set to true.
      singleMode: true,
      itemSelector: '.seaocore_list_wrapper'
    }); 
  },
  setAllPinBoardLayout: function(){
    if(this.widgets.length > 0){
      for (var i = 0; i < this.widgets.length;i++){
        var params = this.widgets[i];
        this.setMasonry(params);
      }
    }
  }
  
});
//**********
var PinBoardSeaoObject = new Array();
var PinBoardSeaoViewMoreObjects = new Array();
var PinBoardSeaoColumn = new Array('middle','left','right');
for(var i = 0;i < PinBoardSeaoColumn.length;i++)
  PinBoardSeaoObject[PinBoardSeaoColumn[i]] = new PinBoardSeao(PinBoardSeaoColumn[i]);
en4.core.runonce.add(function(){
  for(var i = 0; i < PinBoardSeaoColumn.length; i++){
    PinBoardSeaoObject[PinBoardSeaoColumn[i]].start();  
  }
  window.addEvent('scroll', function(){
    for(var i = 0; i < PinBoardSeaoColumn.length; i++){
      PinBoardSeaoObject[PinBoardSeaoColumn[i]].viewMore();
    }
    en4.seaocorepinboard.comments.setLayout(true);
  });
});

//********
PinBoardSeaoViewMore = new Class({
  Implements : [Events, Options],
  options : {
  },
  params:{},
  currentPage : 1,
  currentActive:false,
  viewMoreEl:null,
  layout:'middle',
  active:false,
  initialize : function(params) {
      
    if(params.detactLocation != 'undefined') {
        params.callBack = function(params) {
            if(params.locationSetInCookies) {
                window.location.reload();
            }
        };
        en4.seaocore.locationBased.startReq(params); 
    }       
      
    $(params.contentId).getParent('.'+params.responseContainerClass).set('id','pinboard_wrapper_'+params.widgetId);
    $(params.contentId).destroy();
    params.requestParams.content_id =params.widgetId;
    params.responseContainer=$('pinboard_wrapper_'+params.widgetId);

    if(params.requestParams.noOfTimes !=0 && params.totalCount>(params.requestParams.noOfTimes * params.requestParams.itemCount))
      params.totalCount = (params.requestParams.noOfTimes * params.requestParams.itemCount);
    
    this.params=params;
    this.viewMoreEl=  $(params.viewMoreId);
    this.loading= $(params.loadingId);
    
    // doucment.getElement('.layout_middle');
    var elem=new Element('div',{
      id:'seaocore_board_wrapper_'+params.widgetId,
      'class':'seaocore_board_wrapper'
    }).inject(this.params.responseContainer);
    params.responseContainer.getElements('.seaocore_list_wrapper').inject(elem);
    params.responseContainer=elem;
    
    this.loading.inject(params.responseContainer,'after');
    this.viewMoreEl.inject(params.responseContainer,'after');
    this.viewMoreEl.addEvent('click',this.start.bind(this));
    if(this.params.totalCount > (this.currentPage*this.params.requestParams.itemCount)){
      this.viewMoreEl.removeClass('dnone');
    }
    this.active=true;
    this.params=params;
   
    
    this.pinBoardLayout();
    
    $$(".pb_ch_wd").addEvent('click',this.childWindowOpen.bind(this));
  },
  pinBoardLayout:function(){ 
    var self=this;
    if(!this.active)return;
    (function(){
      self.params.responseContainer.pinBoardSeaoMasonry({
        columnWidth: self.params.requestParams.itemWidth,  //224 columnWidth does not need to be set if singleMode is set to true.
        singleMode: true,
        itemSelector: '.seaocore_list_wrapper'
      });
    }).delay(100);
  },
  start:function(){
    if(this.currentActive)
      return;
   
    this.currentActive = true;
    var params = this.params;
    if(params.totalCount < (this.currentPage*params.requestParams.itemCount))
      return;
 
    this.currentPage++;
    params.requestParams.contentpage = this.currentPage;
    this.sendReq(params);  
  },
  sendReq: function(params){
    var self=this;
    // params.responseContainer.empty();
    this.loading.removeClass('dnone');
    this.viewMoreEl.addClass('dnone');
    var url = en4.core.baseUrl+'widget';
   
    if(params.requestUrl)
      url = params.requestUrl;
    
    var request = new Request.HTML({
      url : url,
      data : $merge(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        self.loading.addClass('dnone');
        if(self.params.totalCount > (self.currentPage*self.params.requestParams.itemCount)){
          self.viewMoreEl.removeClass('dnone');
        }
        Elements.from(responseHTML).inject(params.responseContainer);
        for (var i=0; i<= 10;i++){
          (function(){
            self.pinBoardLayout();
          }).delay(500*i);
        }
        en4.core.runonce.trigger();
        Smoothbox.bind(params.responseContainer);
        $$(".pb_ch_wd").addEvent('click',self.childWindowOpen.bind(this));
        self.currentActive=false;
      }
    });
    request.send();
  },
  childWindowOpen:function(event){
    
    var element=  $(event.target);
    if((
      element.get('tag') == 'a' &&
      !element.onclick &&
      element.href &&
      !element.href.match(/^(javascript|[#])/)
      )){
      event.stop();
      open(element.href,element.get('html'),'width=700,height=350,resizable,toolbar,status');
    }
  }
});

/**
 * likes
 */
en4.seaocorepinboard = {
  masonryArray:new Array(),
  masonryWidgetAllow:new Array(),
  setMasonryLayout:function(){
    for(var i=0;i< en4.seaocorepinboard.masonryArray.length;i++){
      if(en4.seaocorepinboard.masonryArray[i].allowId && en4.seaocorepinboard.masonryWidgetAllow[en4.seaocorepinboard.masonryArray[i].allowId]){
        en4.seaocorepinboard.setMasonry(en4.seaocorepinboard.masonryArray[i]);
      }
    }
  },
  setMasonry:function(params){
    if(!params.responseContainer)
      return;
    params.responseContainer.pinBoardSeaoMasonry(params); 
  }
}

en4.seaocorepinboard.comments = {
  setLayoutActive:false,
  setLayout:function(force){
    if(this.setLayoutActive)
      return;
    var delay=100;
    if(force==true){
      delay=1;
    }
    this.setLayoutActive=true;
    (function(){
      for(var i=0;i<PinBoardSeaoColumn.length;i++){
        PinBoardSeaoObject[PinBoardSeaoColumn[i]]. setAllPinBoardLayout();
      }
      for(var i=0;i<PinBoardSeaoViewMoreObjects.length;i++){
        PinBoardSeaoViewMoreObjects[i].pinBoardLayout();
      }
      en4.seaocorepinboard.setMasonryLayout();
      en4.seaocorepinboard.comments.setLayoutActive=false;
    }).delay(delay);
  },
  addComment:function(elem_id){
    if($("comment-form-open-li_"+elem_id))
      $("comment-form-open-li_"+elem_id).style.display = "none";
    $('comment-form_'+elem_id).style.display = '';
    $('comment-form_'+elem_id).body.focus();
    en4.seaocorepinboard.comments.setLayout(true);
   
    if(!$('comment-form_'+elem_id).retrieve('bodyheight',false))
      $('comment-form_'+elem_id).store('bodyheight',$($('comment-form_'+elem_id).body).offsetHeight); 
  },
  loadComments : function(type, id, page,widget_id){
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/list',
      data : {
        format : 'html',
        type : type,
        id : id,
        page : page,
        widget_id : widget_id
      },
      onSuccess : function(responseJSON) {
        en4.seaocorepinboard.comments.setLayout(); 
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  },
	
  attachCreateComment : function(formElement,type,id,widget_id){
    var bind = this;
    formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown':'keypress',function (event){
      if(formElement.retrieve('bodyheight',false)&& $(formElement.body).offsetHeight > formElement.retrieve('bodyheight',false)){
        en4.seaocorepinboard.comments.setLayout(true);
      }
      if (event.shift && event.key == 'enter') {      	
      } else if(event.key == 'enter') {
        if(formElement.body.value.replace(/\s/g, '')==''){
          return;
        }
        event.stop();             
        bind.submit(formElement,type,id,widget_id);         
      }
    });
      
    //    // add blur event
    //    formElement.body.addEvent('blur',function(){
    //      formElement.style.display = "none";
    //      if($("comment-form-open-li_"+type+'_'+id+'_'+widget_id))
    //        $("comment-form-open-li_"+type+'_'+id+'_'+widget_id).style.display = "block";
    //    } );

    formElement.addEvent('submit', function(event){
      event.stop(); 
      bind.submit(formElement,type,id,widget_id);
    })
  },
  submit:function(formElement,type,id,widget_id){
    var form_values  = formElement.toQueryString();
    form_values += '&format=json';
    form_values += '&id='+formElement.identity.value;
    form_values += '&widget_id='+widget_id;
    if(formElement.body.value.replace(/\s/g, '')==''){
      return;
    }
    if($("comment-form-loading-li_"+type+'_'+id+'_'+widget_id))
      $("comment-form-loading-li_"+type+'_'+id+'_'+widget_id).style.display = "block";
    formElement.style.display = "none";
    en4.seaocorepinboard.comments.setLayout(true);
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/create',
      data : form_values,
      onSuccess : function(responseJSON) {
        en4.seaocorepinboard.comments.setLayout();
        if($('pin_comment_st_'+type+'_'+id+'_'+widget_id)){
          (function(){
            var commentCountHtml = $('comments'+'_'+type+'_'+id+'_'+widget_id).getElements('.comments_options span')[0].get('html');
            $('pin_comment_st_'+type+'_'+id+'_'+widget_id).set('html', commentCountHtml);
          }).delay(100);
        }
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  },
  comment : function(type, id, body, widget_id){
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/create',
      data : {
        format : 'json',
        type : type,
        id : id,
        body : body,
        widget_id : widget_id
      },
      onSuccess : function(responseJSON) {
        en4.seaocorepinboard.comments.setLayout(); 
        if($('pin_comment_st_'+type+'_'+id+'_'+widget_id)){
          (function(){
            var commentCountHtml = $('comments'+'_'+type+'_'+id+'_'+widget_id).getElements('.comments_options span')[0].get('html');
            $('pin_comment_st_'+type+'_'+id+'_'+widget_id).set('html', commentCountHtml);
          }).delay(100);
        }
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  },
  deleteComment : function(type, id, comment_id,widget_id) {
    if( !confirm(en4.core.language.translate('Are you sure you want to delete this?')) ) {
      return;
    }
    (new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/delete',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id
      },
      onComplete: function() {
        $$('.comment-' + comment_id).each(function(element){
          try {
            var commentParent=element.getParent('.comments');
            var commentCount = commentParent.getElements('.comments_options span')[0];
            var m = commentCount.get('html').match(/\d+/);
            var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
            element.destroy();
            commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
            var pinStComment=commentParent.get('id').replace('comments', 'pin_comment_st');
            if($(pinStComment)){
              var commentCountHtml = commentCount.get('html');
              $(pinStComment).set('html', commentCountHtml);
            }
          } catch( e ) {}
        });
        en4.seaocorepinboard.comments.setLayout(); 
      }
    })).send();
  },
  like : function(type, id, widget_id, comment_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/like',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        widget_id : widget_id
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  },
	
  unlike : function(type, id, widget_id, comment_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/unlike',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        widget_id : widget_id
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  }
};

en4.seaocorepinboard.likes = {
  like : function(type, id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/like',
      data : {
        format : 'json',
        type : type,
        id : id
      },
      onSuccess : function(responseJSON) {
        if( $type(responseJSON) == 'object' && $type(responseJSON.status)) {
          $$('.'+type+'_'+id+'like_link').setStyle('display','none');
          $$('.'+type+'_'+id+'unlike_link').setStyle('display','block');
        }
        
        $$('.pin_like_st_'+type+'_'+id).each(function(likeCount){
          try {
            var m = likeCount.get('html').match(/\d+/);
            var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 0 ? parseInt(m[0]) + 1 : 1 );
            likeCount.set('html', likeCount.get('html').replace(m[0], newCount));
          } catch( e ) {}
        });
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id)
    //      "force":true
    });
  },

  unlike : function(type, id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/unlike',
      data : {
        format : 'json',
        type : type,
        id : id
      },
      onSuccess : function(responseJSON) {
        if( $type(responseJSON) == 'object' && $type(responseJSON.status)  ) {
          $$('.'+type+'_'+id+'unlike_link').setStyle('display','none');
          $$('.'+type+'_'+id+'like_link').setStyle('display','block');
        }
        $$('.pin_like_st_'+type+'_'+id).each(function(likeCount){
          try {
            var m = likeCount.get('html').match(/\d+/);
            var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
            likeCount.set('html', likeCount.get('html').replace(m[0], newCount));
          } catch( e ) {}
        });
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id)
    //      "force":true
    });
  }
};

