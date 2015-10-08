/*
 ---
 name: SEATips
 description: Class for creating nice tips that follow the mouse cursor when hovering an element.
 
 Extends :Tips
 
 requires:
 - Core/Options
 - Core/Events
 - Core/Element.Event
 - Core/Element.Style
 - Core/Element.Dimensions
 - /MooTools.More
 
 provides: [Tips]
 
 ...
 */

(function() {
  this.SEATips = new Class({
    Extends: Tips,
    options: {
      canHide: true
    },
    hide: function(element) {
      if (!this.options.canHide)
        return;
      if (!this.tip)
        document.id(this);
      this.fireEvent('hide', [this.tip, element]);
    },
    position: function(event) {
      if (!this.tip)
        document.id(this);
      var size = window.getSize(), scroll = window.getScroll(),
              tip = {
        x: this.tip.offsetWidth,
        y: this.tip.offsetHeight
      },
      props = {
        x: 'left',
        y: 'top'
      },
      bounds = {
        y: false,
        x2: false,
        y2: false,
        x: false
      },
      obj = {};
      for (var z in props) {
        obj[props[z]] = event.page[z] + this.options.offset[z];
        if (obj[props[z]] < 0)
          bounds[z] = true;
        if ((event.page[z] - scroll[z]) > size[z] - this.options.windowPadding[z]) {
          var extra = 1;
          if (z == 'x')
            extra = 51;
          obj[props[z]] = event.page[z] - tip[z] + extra;
          bounds[z + '2'] = true;
        }
      }

      this.fireEvent('bound', bounds);
      this.tip.setStyles(obj);
    }
  });
})();

en4.seaocore = {
  setLayoutWidth: function(elementId, width) {
    var layoutColumn = null;
    if ($(elementId).getParent('.layout_left')) {
      layoutColumn = $(elementId).getParent('.layout_left');
    } else if ($(elementId).getParent('.layout_right')) {
      layoutColumn = $(elementId).getParent('.layout_right');
    } else if ($(elementId).getParent('.layout_middle')) {
      layoutColumn = $(elementId).getParent('.layout_middle');
    }
    if (layoutColumn) {
      layoutColumn.setStyle('width', width);
    }
    $(elementId).destroy();
  }
};
/**
 * likes
 */
en4.seaocore.likes = {
  like: function(type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: 0,
        show_bottom_post: show_bottom_post
      },
      onSuccess: function(responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if ($(type + '_' + id + 'like_link'))
            $(type + '_' + id + 'like_link').style.display = "none";
          if ($(type + '_' + id + 'unlike_link'))
            $(type + '_' + id + 'unlike_link').style.display = "inline-block";
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id)
              //      "force":true
    });
  },
  unlike: function(type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      onSuccess: function(responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if ($(type + '_' + id + 'unlike_link'))
            $(type + '_' + id + 'unlike_link').style.display = "none";
          if ($(type + '_' + id + 'like_link'))
            $(type + '_' + id + 'like_link').style.display = "inline-block";
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id)
              //      "force":true
    });
  }
};

en4.seaocore.comments = {
  loadComments: function(type, id, page, show_bottom_post) {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  attachCreateComment: function(formElement, type, id, show_bottom_post) {
    var bind = this;
    if (show_bottom_post == 1) {
      formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', function(event) {
        if (event.shift && event.key == 'enter') {
        } else if (event.key == 'enter') {
          event.stop();
          var form_values = formElement.toQueryString();
          form_values += '&format=json';
          form_values += '&id=' + formElement.identity.value;
          form_values += '&show_bottom_post=' + show_bottom_post;
          formElement.style.display = "none";
          if ($("comment-form-loading-li_" + type + '_' + id))
            $("comment-form-loading-li_" + type + '_' + id).style.display = "block";
          en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'seaocore/comment/create',
            data: form_values,
            type: type,
            id: id,
            show_bottom_post: show_bottom_post
          }), {
            'element': $('comments' + '_' + type + '_' + id),
            "force": true
          });

        }
      });

      // add blur event
      formElement.body.addEvent('blur', function() {
        formElement.style.display = "none";
        if ($("comment-form-open-li_" + type + '_' + id))
          $("comment-form-open-li_" + type + '_' + id).style.display = "block";
      });
    }
    formElement.addEvent('submit', function(event) {
      event.stop();
      var form_values = formElement.toQueryString();
      form_values += '&format=json';
      form_values += '&id=' + formElement.identity.value;
      form_values += '&show_bottom_post=' + show_bottom_post;
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/comment/create',
        data: form_values,
        type: type,
        id: id,
        show_bottom_post: show_bottom_post
      }), {
        'element': $('comments' + '_' + type + '_' + id),
        "force": true
      });
    })
  },
  comment: function(type, id, body, show_bottom_post) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/create',
      data: {
        format: 'json',
        type: type,
        id: id,
        body: body,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  like: function(type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      onSuccess: function(responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if ($(type + '_' + id + 'like_link'))
            $(type + '_' + id + 'like_link').style.display = "none";
          if ($(type + '_' + id + 'unlike_link'))
            $(type + '_' + id + 'unlike_link').style.display = "inline-block";
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  unlike: function(type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      onSuccess: function(responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if ($(type + '_' + id + 'unlike_link'))
            $(type + '_' + id + 'unlike_link').style.display = "none";
          if ($(type + '_' + id + 'like_link'))
            $(type + '_' + id + 'like_link').style.display = "inline-block";
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  showLikes: function(type, id, show_bottom_post) {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  deleteComment: function(type, id, comment_id) {
    if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
      return;
    }
    (new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/delete',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      onComplete: function() {
        if ($('comment-' + comment_id)) {
          $('comment-' + comment_id).destroy();
        }
        try {
          var commentCount = $$('.comments_options span')[0];
          var m = commentCount.get('html').match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
        } catch (e) {
        }
      }
    })).send();
  }
};

en4.seaocore.facebook = {
  runFacebookSdk: function() {

    window.fbAsyncInit = function() {
      FB.JSON.stringify = function(value) {
        return JSON.encode(value);
      };
      FB.init({
        appId: fbappid,
        status: true, // check login status
        cookie: true, // enable cookies to allow the server to access the session
        xfbml: true  // parse XFBML
      });

      if (window.setFBContent) {

        setFBContent();
      }
    };
    (function() {
      var catarea = $('global_footer');
      if (catarea == null) {
        catarea = $('global_content');
      }
      if (catarea != null && (typeof $('fb-root') == 'undefined' || $('fb-root') == null)) {
        var newdiv = document.createElement('div');
        newdiv.id = 'fb-root';
        newdiv.inject(catarea, 'after');
        var e = document.createElement('script');
        e.async = true;
        if (typeof local_language != 'undefined' && $type(local_language)) {
          e.src = document.location.protocol + '//connect.facebook.net/' + local_language + '/all.js';
        }
        else {
          e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        }
        document.getElementById('fb-root').appendChild(e);
      }
    }());

  }

};

en4.seaocore.advlightbox = {
  createDefaultContent: function() {

  }
}
//window.addEvent('load', function() {
//  if (typeof FB == 'undefined' && typeof fbappid != 'undefined')  {
//    en4.seaocore.facebook.runFacebookSdk (); 
//  }
//  
//});


en4.core.runonce.add(function() {

  // Reload The Page on Pop State Click (Back & Forward) Pop State Button
  var defaultlocationHref = window.location.href;
  var n = defaultlocationHref.indexOf('#');
  defaultlocationHref = defaultlocationHref.substring(0, n != -1 ? n : defaultlocationHref.length);
  window.addEventListener("popstate", function(e) {
    var url = window.location.href;
    var n = url.indexOf('#');
    url = url.substring(0, n != -1 ? n : url.length);
    if (e && e.state && url != defaultlocationHref) {
      window.location.reload(true);
    }
  });
// END
});

function addfriend(el, user_id) {

  en4.core.request.send(new Request.HTML({
    method: 'post',
    'url': en4.core.baseUrl + 'seaocore/feed/addfriendrequest',
    'data': {
      format: 'html',
      'resource_id': user_id
              //'action_id' : action_id,
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
      var parent = el.getParent('div');
      var nextSibling = el.nextSibling;
      el.destroy();
      parent.insertBefore(new Element('span', {
        'html': responseHTML
      }), nextSibling);

    }
  }), {
    'force': true
  });
}



en4.seaocore.nestedcomments = {
  loadComments: function(type, id, page, order, parent_comment_id) {

    if ($('view_more_comments_' + parent_comment_id)) {
      $('view_more_comments_' + parent_comment_id).style.display = 'inline-block';
      $('view_more_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    if ($('view_previous_comments_' + parent_comment_id)) {
      $('view_previous_comments_' + parent_comment_id).style.display = 'inline-block';
      $('view_previous_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    if ($('view_later_comments_' + parent_comment_id)) {
      $('view_later_comments_' + parent_comment_id).style.display = 'inline-block';
      $('view_later_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page,
        order: order,
        parent_div: 1,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  loadcommentssortby: function(type, id, order, parent_comment_id) {
    if ($('sort' + '_' + type + '_' + id + '_' + parent_comment_id)) {
      $('sort' + '_' + type + '_' + id + '_' + parent_comment_id).style.display = 'inline-block';
      $('sort' + '_' + type + '_' + id + '_' + parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        order: order,
        parent_div: 1,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  attachCreateComment: function(formElement, type, id, parent_comment_id) {
    var bind = this;
    formElement.addEvent('submit', function(event) {
      event.stop();
      if (formElement.body.value == '')
        return;
      if ($('seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id))
        $('seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id).destroy();
      var divEl = new Element('div', {
        'class': '',
        'html': '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading">',
        'id': 'seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id,
        'styles': {
          'display': 'inline-block'
        }
      });

      divEl.inject(formElement);
      var form_values = formElement.toQueryString();
      form_values += '&format=json';
      form_values += '&id=' + formElement.identity.value;

      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/nestedcomment/create',
        data: form_values,
        type: type,
        id: id,
        onComplete: function(e) {
          if (parent_comment_id == 0)
            return;
          try {
            var replyCount = $$('.seaocore_replies_options span')[0];
            var m = replyCount.get('html').match(/\d+/);
            replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
          } catch (e) {
          }
        }
      }), {
        'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
      });
    })
  },
  comment: function(type, id, body, parent_comment_id) {
    if (body == '')
      return;
    var formElement = $('comments_form_' + type + '_' + id + '_' + parent_comment_id);
    if ($('seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id))
      $('seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id)
    var divEl = new Element('div', {
      'class': '',
      'html': '<img src="application/modules/Seaocore/externals/images/spinner.gif">',
      'id': 'seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id,
      'styles': {
        'display': 'inline-block'
      }
    });
    divEl.inject(formElement);
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/create',
      data: {
        format: 'json',
        type: type,
        id: id,
        body: body
      },
      onComplete: function(e) {
        if (parent_comment_id == 0)
          return;
        try {
          var replyCount = $$('.seaocore_replies_options span')[0];
          var m = replyCount.get('html').match(/\d+/);
          replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
        } catch (e) {
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  like: function(type, id, comment_id, order, parent_comment_id, option) {
    if ($('like_comments_' + comment_id) && (option == 'child')) {
      $('like_comments_' + comment_id).style.display = 'inline-block';
      $('like_comments_' + comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    if ($('like_comments') && (option == 'parent')) {
      $('like_comments').style.display = 'inline-block';
      $('like_comments').innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function(e) {
        if ($('sitereview_most_likes_' + id)) {
          $('sitereview_most_likes_' + id).style.display = 'none';
        }
        if ($('sitereview_unlikes_' + id)) {
          $('sitereview_unlikes_' + id).style.display = 'block';
        }

        if ($(type + '_like_' + id))
          $(type + '_like_' + id).value = 1;
        if ($(type + '_most_likes_' + id))
          $(type + '_most_likes_' + id).style.display = 'none';
        if ($(type + '_unlikes_' + id))
          $(type + '_unlikes_' + id).style.display = 'inline-block';

      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  unlike: function(type, id, comment_id, order, parent_comment_id, option) {
    if ($('unlike_comments_' + comment_id) && (option == 'child')) {
      $('unlike_comments_' + comment_id).style.display = 'inline-block';
      $('unlike_comments_' + comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    if ($('unlike_comments') && (option == 'parent')) {
      $('unlike_comments').style.display = 'inline-block';
      $('unlike_comments').innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function(e) {
        if ($('sitereview_most_likes_' + id)) {
          $('sitereview_most_likes_' + id).style.display = 'block';
        }
        if ($('sitereview_unlikes_' + id)) {
          $('sitereview_unlikes_' + id).style.display = 'none';
        }

        if ($(type + '_like_' + id))
          $(type + '_like_' + id).value = 0;
        if ($(type + '_most_likes_' + id))
          $(type + '_most_likes_' + id).style.display = 'inline-block';
        if ($(type + '_unlikes_' + id))
          $(type + '_unlikes_' + id).style.display = 'none';

      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  showLikes: function(type, id, order, parent_comment_id) {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true,
        order: order,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  deleteComment: function(type, id, comment_id, order, parent_comment_id) {
    if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
      return;
    }
    if ($('comment-' + comment_id)) {
      $('comment-' + comment_id).destroy();
    }
    (new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/delete',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function(e) {
        try {
          var replyCount = $$('.seaocore_replies_options span')[0];
          var m = replyCount.get('html').match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
        } catch (e) {
        }
      }
    })).send();
  }
};

var ScrollToTopSeao = function(topElementId, buttonId) {
  window.addEvent('scroll', function() {
    var element = $(buttonId);
    if (element) {
      if ($(topElementId)) {
        var elementPostionY = 0;
        if (typeof($(topElementId).offsetParent) != 'undefined') {
          elementPostionY = $(topElementId).offsetTop;
        } else {
          elementPostionY = $(topElementId).y;
        }
      }
      if (elementPostionY + window.getSize().y < window.getScrollTop()) {
        if (element.hasClass('Offscreen'))
          element.removeClass('Offscreen');
      } else if (!element.hasClass('Offscreen')) {
        element.addClass('Offscreen');
      }
    }
  });
  en4.core.runonce.add(function() {
    var scroll = new Fx.Scroll(document.getElement('body').get('id'), {
      wait: false,
      duration: 750,
      offset: {
        'x': -200,
        'y': -100
      },
      transition: Fx.Transitions.Quad.easeInOut
    });

    $(buttonId).addEvent('click', function(event) {
      event = new Event(event).stop();
      scroll.toElement(topElementId);
    });
  });

};


ActivitySEAOUpdateHandler = new Class({
  Implements: [Events, Options],
  options: {
    debug: true,
    baseUrl: '/',
    identity: false,
    delay: 5000,
    admin: false,
    idleTimeout: 600000,
    last_id: 0,
    next_id: null,
    subject_guid: null,
    showImmediately: false
  },
  state: true,
  activestate: 1,
  fresh: true,
  lastEventTime: false,
  title: document.title,
  //loopId : false,

  initialize: function(options) {
    this.setOptions(options);
  },
  start: function() {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout: this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive': function() {
        this._log('activity loop onStateActive');
        this.activestate = 1;
        this.state = true;
      }.bind(this),
      'onStateIdle': function() {
        this._log('activity loop onStateIdle');
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });
    this.loop();
    //this.loopId = this.loop.periodical(this.options.delay, this);
  },
  stop: function() {
    this.state = false;
  },
  checkFeedUpdate: function(action_id, subject_guid) {
    if (en4.core.request.isRequestActive())
      return;

    function getAllElementsWithAttribute(attribute) {
      var matchingElements = [];
      var values = [];
      var allElements = document.getElementsByTagName('*');
      for (var i = 0; i < allElements.length; i++) {
        if (allElements[i].getAttribute(attribute)) {
          // Element exists with attribute. Add to array.
          matchingElements.push(allElements[i]);
          values.push(allElements[i].getAttribute(attribute));
        }
      }
      return values;
    }
    var list = getAllElementsWithAttribute('data-activity-feed-item');
    this.options.last_id = Math.max.apply(Math, list);
    min_id = this.options.last_id + 1;

    var req = new Request.HTML({
      url: en4.core.baseUrl + 'widget/index/name/seaocore.feed',
      data: {
        'format': 'html',
        'minid': min_id,
        'feedOnly': true,
        'nolayout': true,
        'subject': this.options.subject_guid,
        'getUpdate': true
      }
    });
    en4.core.request.send(req, {
      'element': $('activity-feed'),
      'updateHtmlMode': 'prepend'
    }
    );



    req.addEvent('complete', function() {
      (function() {
        if (this.options.showImmediately && $('feed-update').getChildren().length > 0) {
          $('feed-update').setStyle('display', 'none');
          $('feed-update').empty();
          this.getFeedUpdate(this.options.next_id);
        }
      }).delay(50, this);
    }.bind(this));



    // Start LOCAL STORAGE STUFF   
    if (localStorage) {
      var pageTitle = document.title;
      //@TODO Refill Locally Stored Activity Feed

      // For each activity-item, get the item ID number Data attribute and add it to an array
      var feed = document.getElementById('activity-feed');
      // For every <li> in Feed, get the Feed Item Attribute and add it to an array
      var items = feed.getElementsByTagName("li");
      var itemObject = {};
      // Loop through each item in array to get the InnerHTML of each Activity Feed Item
      var c = 0;
      for (var i = 0; i < items.length; ++i) {
        if (items[i].getAttribute('data-activity-feed-item') != null) {
          var itemId = items[i].getAttribute('data-activity-feed-item');
          itemObject[c] = {id: itemId, content: document.getElementById('activity-item-' + itemId).innerHTML};
          c++;
        }
      }
      // Serialize itemObject as JSON string
      var activityFeedJSON = JSON.stringify(itemObject);
      localStorage.setItem(pageTitle + '-activity-feed-widget', activityFeedJSON);
    }


    // Reconstruct JSON Object, Find Highest ID
    if (localStorage.getItem(pageTitle + '-activity-feed-widget')) {
      var storedFeedJSON = localStorage.getItem(pageTitle + '-activity-feed-widget');
      var storedObj = eval("(" + storedFeedJSON + ")");

      //alert(storedObj[0].id); // Highest Feed ID
      // @TODO use this at min_id when fetching new Activity Feed Items
    }
    // END LOCAL STORAGE STUFF


    return req;
  },
  getFeedUpdate: function(last_id) {
    if (en4.core.request.isRequestActive())
      return;
    var min_id = this.options.last_id + 1;
    this.options.last_id = last_id;
    document.title = this.title;
    var req = new Request.HTML({
      url: en4.core.baseUrl + 'widget/index/name/seaocore.feed',
      data: {
        'format': 'html',
        'minid': min_id,
        'feedOnly': true,
        'nolayout': true,
        'getUpdate': true,
        'subject': this.options.subject_guid
      }
    });
    en4.core.request.send(req, {
      'element': $('activity-feed'),
      'updateHtmlMode': 'prepend'
    });
    return req;
  },
  loop: function() {
    this._log('activity update loop start');

    if (!this.state) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.checkFeedUpdate().addEvent('complete', function() {
        try {
          this._log('activity loop req complete');
          this.loop.delay(this.options.delay, this);
        } catch (e) {
          this.loop.delay(this.options.delay, this);
          this._log(e);
        }
      }.bind(this));
    } catch (e) {
      this.loop.delay(this.options.delay, this);
      this._log(e);
    }

    this._log('activity update loop stop');
  },
  // Utility
  _log: function(object) {
    if (!this.options.debug) {
      return;
    }

    try {
      if ('console' in window && typeof(console) && 'log' in console) {
        console.log(object);
      }
    } catch (e) {
      // Silence
    }
  }
});

en4.seaocore.locationBased = {
  startReq: function(params) {
    window.locationsParamsSEAO = {
      latitude: 0,
      longitude: 0
    };
    window.locationsDetactSEAO = false;
    params.isExucute = false;
    var self = this;
    var callBackFunction = self.sendReq;
    if (params.callBack) {
      callBackFunction = params.callBack;
    }

    if (params.detactLocation && !window.locationsDetactSEAO && navigator.geolocation) {

     if (typeof(Cookie.read('seaocore_myLocationDetails')) != 'undefined' && Cookie.read('seaocore_myLocationDetails') != "") {
      var readLocationsDetails = JSON.parse(Cookie.read('seaocore_myLocationDetails'));
     }

      if (typeof(readLocationsDetails) == 'undefined' || readLocationsDetails == null || typeof(readLocationsDetails.latitude) == 'undefined' || typeof(readLocationsDetails.longitude) == 'undefined') {

        navigator.geolocation.getCurrentPosition(function(position) {
            
          
          if($('region')){
              var regionCurrentLocation = $('region').innerHTML;  
              $('region').innerHTML = '<div class="seaocore_content_loader"></div>';
          }
            
          window.locationsParamsSEAO.latitude = position.coords.latitude;
          window.locationsParamsSEAO.longitude = position.coords.longitude;

          var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': '', 'locationmiles': params.locationmiles};
          self.setLocationCookies(myLocationDetails);

          self.setLocationField(position, params);
          params.locationSetInCookies = true;
          params.requestParams = $merge(params.requestParams, window.locationsParamsSEAO);
          params.isExucute = true;
          if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
            callBackFunction(params);
          }

        }, function() {
          params.isExucute = true;
          if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
            callBackFunction(params);
          }

        });
      }
      else {
        window.locationsParamsSEAO.latitude = readLocationsDetails.latitude;
        window.locationsParamsSEAO.longitude = readLocationsDetails.longitude;
        params.requestParams = $merge(params.requestParams, window.locationsParamsSEAO);
        params.isExucute = true;
        if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
          callBackFunction(params);
        }
      }

      window.locationsDetactSEAO = true;
      window.setTimeout(function() {
        if (params.isExucute)
          return;

        if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
          callBackFunction(params);
        }

      }, 3000);
    } else {
      if (params.detactLocation && window.locationsDetactSEAO) {
        params.requestParams = $merge(params.requestParams, window.locationsParamsSEAO);
      }

      if (typeof(params.noSendReq) == 'undefined' || params.noSendReq == null) {
        callBackFunction(params);
      }
    }

  },
  sendReq: function(params) {

    var self = this;
    var url = en4.core.baseUrl + 'widget';

    if (params.requestUrl)
      url = params.requestUrl;
    var request = new Request.HTML({
      url: url,
      data: $merge(params.requestParams, {
        format: 'html',
        subject: en4.core.subject.guid,
        is_ajax_load: true
      }),
      evalScripts: true,
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($(params.responseContainer)) {
          $(params.responseContainer).innerHTML = '';
          Elements.from(responseHTML).inject($(params.responseContainer));
        }
        en4.core.runonce.trigger();
        Smoothbox.bind(params.responseContainer);
      }
    });
    request.send();

  },
  setLocationCookies: function(params, pageReload) {

    var myLocationDetails = {'latitude': params.latitude, 'longitude': params.longitude, 'location': params.location, 'locationmiles': params.locationmiles};
                    
    if (typeof(params.changeLocationWidget) != 'undefined' && params.changeLocationWidget) {
        Cookie.write('seaocore_myLocationDetails', JSON.stringify(myLocationDetails), {duration: 30, path: en4.core.baseUrl});   
    }
    else {
        en4.core.request.send(new Request.JSON({
          url: en4.core.baseUrl + 'seaocore/location/get-specific-location-setting',
          data: {
            format: 'json',
            location: params.location,
            updateUserLocation: params.updateUserLocation
          },
          onSuccess: function(responseJSON) {
              if(responseJSON.saveCookies) {
               Cookie.write('seaocore_myLocationDetails', JSON.stringify(myLocationDetails), {duration: 30, path: en4.core.baseUrl});           
               
                if (pageReload) {
                  window.location.reload();
                }               
               
              }
          }
        }),{force:true});  
    }
    
    
    
    
  },
  setLocationField: function(position, params) {
    var self = this;
    if (!position.address) {
      var mapDetect = new google.maps.Map(new Element('div'), {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(0, 0)
      });
      var service = new google.maps.places.PlacesService(mapDetect);
      var request = {
        location: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
        radius: 500
      };
      service.search(request, function(results, status) {
        if (status == 'OK') {
          var index = 0;
          var radian = 3.141592653589793 / 180;
          var my_distance = 1000;
          var R = 6371; // km
          for (var i = 0; i < results.length; i++) {
            var lat2 = results[i].geometry.location.lat();
            var lon2 = results[i].geometry.location.lng();
            var dLat = (lat2 - position.coords.latitude) * radian;
            var dLon = (lon2 - position.coords.longitude) * radian;
            var lat1 = position.coords.latitude * radian;
            lat2 = lat2 * radian;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var d = R * c;

            if (d < my_distance) {
              index = i;
              my_distance = d;
            }
          }

          if (typeof(params.fieldName) != 'undefined' && params.fieldName != null && document.getElementById(params.fieldName)) {
            document.getElementById(params.fieldName).value = (results[index].vicinity) ? results[index].vicinity : '';

            if (typeof(params.locationmilesFieldName) != 'undefined' && params.locationmilesFieldName != null && document.getElementById(params.locationmilesFieldName)) {
              document.getElementById(params.locationmilesFieldName).value = params.locationmiles;
            }
          }

          var cookiesLocation = (results[index].vicinity) ? results[index].vicinity : '';
          var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': cookiesLocation, 'locationmiles': params.locationmiles};
          
          var pageReload = 0;
          if (typeof(params.reloadPage) != 'undefined' && params.reloadPage != null) {
            pageReload = 1;
          }          
          
          self.setLocationCookies(myLocationDetails, pageReload);

//          if (typeof(params.reloadPage) != 'undefined' && params.reloadPage != null) {
//            window.location.reload();
//          }

        }
      })
    } else {
      var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';
      var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';
      if (typeof(params.fieldName) != 'undefined' && params.fieldName != null && document.getElementById(params.fieldName)) {
        document.getElementById(params.fieldName).value = location;
      }

      var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': location, 'locationmiles': params.locationmiles};
      
          var pageReload = 0;
          if (typeof(params.reloadPage) != 'undefined' && params.reloadPage != null) {
            pageReload = 1;
          }          
      
      self.setLocationCookies(myLocationDetails, pageReload);

//      if (typeof(params.reloadPage) != 'undefined' && params.reloadPage != null) {
//        window.location.reload();
//      }

    }

  },
};
en4.seaocore.setShareButtons = function(wrapper, cont, params) {
  if (cont.getElement('.facebook_container')) {
    if (!document.getElementById('fb-root'))
      new Element('div', {'id': 'fb-root'}).inject($('global_content'), 'top');
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id))
        return;
      js = d.createElement(s);
      js.id = id;
      if (typeof local_language != 'undefined' && $type(local_language)) {
        js.src = "//connect.facebook.net/"+ local_language +"/all.js#xfbml=1";
      }
      else {
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      }
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  }

  if (cont.getElement('.linkedin_container')) {
    new Element('script', {'type': 'IN/Share', 'data-counter': 'top'}).inject(cont.getElement('.linkedin_container'));
    new Element('script', {'src': ("https:" == document.location.protocol ? "https://" : "http://") + 'platform.linkedin.com/in.js'}).inject($('global_content'), 'before');

  }
  if (cont.getElement('.twitter_container')) {
    new Element('script', {'src': ("https:" == document.location.protocol ? "https://" : "http://") + 'platform.twitter.com/widgets.js'}).inject($('global_content'), 'before');

  }
  if (cont.getElement('.google_container')) {
    new Element('script', {'src': 'https://apis.google.com/js/plusone.js', 'async': true}).inject($('global_content'), 'before');

  }

  if (!params.leftValue) {
    params.leftValue = 15;
  }
  wrapper.inject($('global_content'), 'top');
  var top = wrapper.getStyle('top');
  if (!params.type) {
    params.type = 'left';
  }

  if (params.type === 'left') {
    params.leftValue = params.leftValue + parseInt(wrapper.clientWidth);
    cont.setStyle('left', '-' + params.leftValue + 'px');
    $('global_content').addClass('seao_share_buttons_left_content');
  } else {
    params.leftValue = params.leftValue + parseInt($('global_content').clientWidth);
    cont.setStyle('left', params.leftValue + 'px');
    $('global_content').addClass('seao_share_buttons_right_content');
  }
  (function() {
    wrapper.setStyles({width: '1px', visibility: 'visible'});
  }).delay(1500);
  window.addEvent('scroll', function(e) {
    var descripY = parseInt($('global_content').getOffsets().y) - 20, scrollY = $(window).getScroll().y, footerY = parseInt($('global_footer').getOffsets().y), height = parseInt(wrapper.getStyle('height')), fixedShare = wrapper.getStyle('position') === 'fixed';

    if (scrollY < descripY && fixedShare) {
      wrapper.setStyles({
        position: 'absolute',
        top: top
      });
    }
    else if (scrollY > descripY && (scrollY + 20 + height) > footerY) {
      wrapper.setStyles({
        position: 'absolute',
        top: (footerY - height - 90)
      });
    }
    else if (scrollY > descripY && !fixedShare) {
      wrapper.setStyles({
        position: 'fixed',
        top: 20
      });
    }
  });
};

var SmoothboxSEAO = {
  overlay: null,
  wrapper: null,
  content: null,
  contentHTML: null,
  scrollPosition: {
    left: 0,
    top: 0
  },
  addScriptFiles: [],
  addStylesheets: [],
  active: false,
  build: function() {
    SmoothboxSEAO.overlay = new Element('div', {
      'class': 'seao_smoothbox_lightbox_overlay'
    }).inject($('global_wrapper'));
    SmoothboxSEAO.wrapper = new Element('div', {
      'class': 'seao_smoothbox_lightbox_content_wrapper'
    }).inject($('global_wrapper'));
    SmoothboxSEAO.attach();
    SmoothboxSEAO.hide();
  },
  attach: function() {
    if (!SmoothboxSEAO.wrapper)
      return;
    SmoothboxSEAO.wrapper.removeEvents('click').addEvent('click', function(event) {
      var el = $(event.target);
      if (el.hasClass('seao_smoothbox_lightbox_content') || el.getParent('.seao_smoothbox_lightbox_content'))
        return;
      SmoothboxSEAO.close();
    });
  },
  bind: function(selector) {
    // All children of element
    var elements;
    if ($type(selector) == 'element') {
      elements = selector.getElements('a.seao_smoothbox');
    } else if ($type(selector) == 'string') {
      elements = $$(selector);
    } else {
      elements = $$("a.seao_smoothbox");
    }

    elements.each(function(el)
    {
      if (el.get('tag') != 'a' || !SmoothboxSEAO.hasLink(el) || el.retrieve('smoothboxed', false))
      {
        return;
      }


      el.addEvent('click', function(event) {
        event.stop();
        SmoothboxSEAO.open({
          class: el.get('data-SmoothboxSEAOClass'),
          request: {
            url: el.href
          }
        });
      });
      el.store('smoothboxed', true);
    });


  },
  hasLink: function(element) {
    return (
            !element.onclick &&
            element.href &&
            !element.href.match(/^(javascript|[#])/));
  },
  open: function(params) {
    if (!params)
      return;
    if (!SmoothboxSEAO.wrapper) {
      SmoothboxSEAO.build();
    } else {
      SmoothboxSEAO.wrapper.empty();
    }
    if ((typeof params) === 'string') {
      if (params.length < 4000 && (params.substring(0, 1) == '/' ||
              params.substring(0, 1) == '.' ||
              params.substring(0, 4) == 'http' ||
              !params.match(/[ <>"'{}|^~\[\]`]/)
              )
              ) {

        params = {request: {
            url: params
          }};
      } else {
        params = {element: params};
      }

    } else if ($type(params) === 'element') {
      params = {element: params};
    }

    SmoothboxSEAO.content = new Element('div', {
      'class': 'seao_smoothbox_lightbox_content'
    }).inject(SmoothboxSEAO.wrapper);
    // SmoothboxSEAO.content.setStyle('width', 'auto');
    SmoothboxSEAO.contentHTML = new Element('div', {
      'class': 'seao_smoothbox_lightbox_content_html'
    }).inject(SmoothboxSEAO.content);
    if (params.class)
      SmoothboxSEAO.content.addClass(params.class);

    if (params.element && (typeof params.element) === 'string')
      SmoothboxSEAO.contentHTML.innerHTML = params.element;
    else if (params.element)
      params.element.inject(SmoothboxSEAO.contentHTML);
    else if (params.request && params.request.url)
      SmoothboxSEAO.sendReq(params.request);

    SmoothboxSEAO.show();
    $$(".seao_smoothbox_lightbox_close").addEvent('click', function(event) {
      event.stopPropagation();
      SmoothboxSEAO.close();
    });

    SmoothboxSEAO.doAutoResize();
    //  this.fireEvent('open', this);
  },
  doAutoResize: function() {
    var size = Function.attempt(function() {
      return SmoothboxSEAO.contentHTML.getScrollSize();
    }, function() {
      return SmoothboxSEAO.contentHTML.getSize();
    }, function() {
      return {
        x: SmoothboxSEAO.contentHTML.scrollWidth,
        y: SmoothboxSEAO.contentHTML.scrollHeight
      }
    });

    // if (size.x) {
    var winSize = window.getSize();
    if (size.x > (winSize.x - 30)) {
      size.x = winSize.x - 30;
    }
    var marginTop = 30;
    SmoothboxSEAO.content.setStyle('width', size.x);
    if (size.y < winSize.y) {
      marginTop = (winSize.y - size.y) / 2;
    }

    if (marginTop > 150)
      marginTop = 150;
    else if (marginTop < 10)
      marginTop = 10;
    size.x = size.x + 10;
    SmoothboxSEAO.content.setStyles({
      'width': size.x,
      'marginTop': marginTop,
      'marginBottom': 20
    });
    //  }
  },
  show: function() {
    SmoothboxSEAO.overlay.show();
    SmoothboxSEAO.wrapper.show();
    if ($('arrowchat_base'))
      $('arrowchat_base').style.display = 'none';
    if ($('wibiyaToolbar'))
      $('wibiyaToolbar').style.display = 'none';
    SmoothboxSEAO.scrollPosition.top = window.getScrollTop();
    SmoothboxSEAO.scrollPosition.left = window.getScrollLeft();
    SmoothboxSEAO.setHtmlScroll("hidden");
    SmoothboxSEAO.active = true;
  },
  hide: function() {
    SmoothboxSEAO.overlay.hide();
    SmoothboxSEAO.wrapper.hide();
    SmoothboxSEAO.active = false;
  },
  close: function() {
    if (!SmoothboxSEAO.active)
      return;
    SmoothboxSEAO.hide();
    SmoothboxSEAO.setHtmlScroll("auto");
    window.scroll(SmoothboxSEAO.scrollPosition.left, SmoothboxSEAO.scrollPosition.top);
    if ($('arrowchat_base'))
      $('arrowchat_base').style.display = 'block';
    if ($('wibiyaToolbar'))
      $('wibiyaToolbar').style.display = 'block';
    // this.fireEvent('close', this);
  },
  setHtmlScroll: function(cssCode) {
    $$('html').setStyle('overflow', cssCode);
  },
  sendReq: function(params) {
    var container = SmoothboxSEAO.contentHTML;
    container.empty();
    new Element('div', {
      'class': 'seao_smoothbox_lightbox_loading'
    }).inject(container);

    if (!params.requestParams)
      params.requestParams = {};
    SmoothboxSEAO.addScriptFiles = [];
    SmoothboxSEAO.addStylesheets = [];

    var request = new Request.HTML({
      url: params.url,
      method: 'get',
      data: $merge(params.requestParams, {
        format: 'html',
        seaoSmoothbox: true
      }),
      evalScripts: true,
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var onLoadContent = function() {
          container.empty();
          Elements.from(responseHTML).inject(container);
          en4.core.runonce.trigger();
          SmoothboxSEAO.doAutoResize();
          Smoothbox.bind(container);
          SmoothboxSEAO.bind(container);
          if (params.callBack && (typeof params.callBack) === 'function') {
            params.callBack(container);
          }
        };
        var JSCount = SmoothboxSEAO.addScriptFiles.length;
        var StyleSheetCount = SmoothboxSEAO.addStylesheets.length;
        var totalFiles = JSCount + StyleSheetCount;
        var i = 0, succes = 0;
        if (succes === totalFiles)
          onLoadContent();
        for (i; i < JSCount; i++) {
          Asset.javascript(SmoothboxSEAO.addScriptFiles[i], {
            onLoad: function() {
              succes++;
              if (succes === totalFiles)
                onLoadContent();
            }});
        }
        SmoothboxSEAO.addScriptFiles = [];
        for (i = 0; i < StyleSheetCount; i++) {
          Asset.css(SmoothboxSEAO.addStylesheets[i], {
            onLoad: function() {
              succes++;
              if (succes === totalFiles)
                onLoadContent();
            }});
        }
        SmoothboxSEAO.addStylesheets = [];

      }
    });
    request.send();
  }
};

window.addEvent('domready', function()
{
  SmoothboxSEAO.bind();
});

window.addEvent('load', function()
{
  SmoothboxSEAO.bind();
});

en4.seaocore.covertdateDmyToMdy = function(date) {
  starttime = date.split("/");
  date = starttime[1] + '/' + starttime[0] + '/' + starttime[2];
  return date;
};


/*  Community Ad Plugin JS Start here*/
en4.communityad = {
  sendReq: function(container, content_id, isAdboardPage, requestParams) {
    var url = en4.core.baseUrl + 'widget';
    var params = {
      format: 'html',
      is_ajax_load: 1,
      subject: en4.core.subject.guid,
      isAdboardPage: isAdboardPage
    };
    if (!content_id) {
      url = en4.core.baseUrl + 'widget/index/mod/communityad/name/ads';
    } else {
      params.content_id = content_id;
    }
    if (requestParams)
      params = $merge(requestParams, params);
    var request = new Request.HTML({
      url: url,
      method: 'get',
      data: params,
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        container.empty();
        Elements.from(responseHTML).inject(container);
        en4.core.runonce.trigger();
        Smoothbox.bind(container);
      }
    });
    request.send();
  }
};
var communityad_likeinfo = function(ad_id, resource_type, resource_id, owner_id, widgetType, core_like) {
  // SENDING REQUEST TO AJAX
  var request = createLike(ad_id, resource_type, resource_id, owner_id, widgetType, core_like);
  // RESPONCE FROM AJAX
  request.addEvent('complete', function(responseJSON) {
    if (responseJSON.like_id)
    {
      $(widgetType + '_likeid_info_' + ad_id).value = responseJSON.like_id;
      $(resource_type + '_' + widgetType + '_most_likes_' + ad_id).style.display = 'none';
      $(resource_type + '_' + widgetType + '_unlikes_' + ad_id).style.display = 'block';
    }
    else
    {
      $(widgetType + '_likeid_info_' + ad_id).value = 0;
      $(resource_type + '_' + widgetType + '_most_likes_' + ad_id).style.display = 'block';
      $(resource_type + '_' + widgetType + '_unlikes_' + ad_id).style.display = 'none';
    }
  });
}
/* $Id: core.js 2011-02-16 9:40:21Z SocialEngineAddOns Copyright 2009-2011 BigStep Technologies Pvt. Ltd. $ */

// Use: Ads Display.
// Function Call: When click on cross of any advertisment.
function adCancel(div_id, widgetType) {
  $(widgetType + '_ad_cancel_' + div_id).style.display = 'block';
  $(widgetType + '_ad_' + div_id).style.display = 'none';
}

// Use: Ads Display.
// Function Call: After click on cross of any ads then show option of 'undo' if click on the 'undo'.
function adUndo(div_id, widgetType) {
  $(widgetType + '_ad_cancel_' + div_id).style.display = 'none';
  $(widgetType + '_ad_' + div_id).style.display = 'block';
  if ($(widgetType + '_other_' + div_id).checked) {
    $(widgetType + '_other_' + div_id).checked = false;
    $(widgetType + '_other_text_' + div_id).style.display = 'none';
    $(widgetType + '_other_text_' + div_id).value = 'Type your reason here...';
    $(widgetType + '_other_button_' + div_id).style.display = 'none';
  }
}

// Use: Ads Display.
// Function Call: After click on cross of any ads then show radio button if click on 'other' type radio button.
function otherAdCannel(adRadioValue, div_id, widgetType) {
  // Condition: When click on 'other radio button'.
  if (adRadioValue == 4) {
    $(widgetType + '_other_text_' + div_id).style.display = 'block';
    $(widgetType + '_other_button_' + div_id).style.display = 'block';
  }
}

// Use: Ads Display
// Function Call: When save entry in data base.
function adSave(adCancelReasion, adsId, divId, widgetType) {
  var adDescription = 0;
  // Condition: Find out 'Description' if select other options from radio button.

  if (adCancelReasion == 'Other') {
    if ($(widgetType + '_other_text_' + divId).value != 'Type your reason here...') {
      adDescription = $(widgetType + '_other_text_' + divId).value;
    }
  }
  $(widgetType + '_ad_cancel_' + divId).innerHTML = '<center><img src="application/modules/Core/externals/images/loading.gif" alt=""></center>';
  en4.core.request.send(new Request.HTML({
    url: en4.core.baseUrl + 'communityad/display/adsave',
    data: {
      format: 'html',
      adCancelReasion: adCancelReasion,
      adDescription: adDescription,
      adsId: adsId
    }
  }), {
    'element': $(widgetType + '_ad_cancel_' + divId)
  })
}

// Function: For 'Advertisment' liked or unliked.
function createLike(ad_id, resource_type, resource_id, owner_id, widgetType, core_like)
{
  var like_id = $(widgetType + '_likeid_info_' + ad_id).value;
  var request = new Request.JSON({
    url: en4.core.baseUrl + 'communityad/display/globallikes',
    data: {
      format: 'json',
      'ad_id': ad_id,
      'resource_type': resource_type,
      'resource_id': resource_id,
      'owner_id': owner_id,
      'like_id': like_id,
      'core_like': core_like
    }
  });
  request.send();
  return request;
}
/*  Community Ad Plugin JS End here*/

function locationAutoSuggest(countrycities, location_field, city_field) {

    if(city_field && $(city_field)) {

        if(countrycities) {
            var options = {
                types: ['(cities)'],
                componentRestrictions: {country: countrycities}
            };
        }
        else {
            var options = {
                types: ['(cities)']
            };      
        }    
        
        var autocomplete = new google.maps.places.Autocomplete($(city_field), options);        
    }  
    
    if(location_field && $(location_field)) {

        if(countrycities) { 
            var options = {
                //types: [''],//We are not passing any values here for showing all results of some specific country.
                componentRestrictions: {country: countrycities}
            };
        }
        else {
            var options = {
           
            }; 
        }
        
        var autocomplete = new google.maps.places.Autocomplete($(location_field), options);        
    }        
    
}

//WHEN CONTENT ON THE PAGE LOAD FROM THE AJAX IN THAT CASE SMOOTHBOX CLASS DOES NOT WORK THEN WE USE BELOW FUNCTION
function openSmoothbox(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
}

function showShareLinks(val) {
  $(document.body).addEvent('click',function() {
    showHideToggleShareLinks();
  });
  $$('.siteevent_share_links_toggle').addEvent('click',function(event) {
    event.stop();
    //showHideToggleShareLinks();
    $(this).getParent('.siteevent_grid_footer').getElement('.siteevent_share_links').toggle(); 
    
    if(typeof val == 'undefined') {
       $(this).toggle();
    } else {
       $(this).show(); 
    }
  });
}

function showHideToggleShareLinks() {
      $$('.siteevent_share_links_toggle').show();
      $$('.siteevent_share_links_toggle').getParent('.siteevent_grid_footer').getElement('.siteevent_share_links').hide();
} 