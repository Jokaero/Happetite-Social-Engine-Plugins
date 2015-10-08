en4.siteusercoverphoto={};

en4.siteusercoverphoto.friend = {
  add : function(user_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'siteusercoverphoto/friends/add',
      data : {
        format : 'json',
        user_id : user_id,
        subject : en4.core.subject.guid       
      },
      onComplete: function (response) {
        $('friendship_user').innerHTML ='';
        $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.friend.cancel(' + user_id + ')"><span>' + en4.core.language.translate("Cancel Friend Request") + '</span></a>';
      }
    })); 
  },
  cancel : function(user_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'siteusercoverphoto/friends/cancel',
      data : {
        format : 'json',
        user_id : user_id,
        subject : en4.core.subject.guid       
      },
      onComplete: function (response) {
        $('friendship_user').innerHTML ='';
        $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.friend.add(' + user_id + ')"><span>' + en4.core.language.translate("Add Friend") + '</span></a>';
      }
    })); 
  },
  confirm : function(user_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'siteusercoverphoto/friends/confirm',
      data : {
        format : 'json',
        user_id : user_id,
        subject : en4.core.subject.guid       
      },
      onComplete: function (response) {
        $('friendship_user').innerHTML ='';
        $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.friend.remove(' + user_id + ')"><span>' + en4.core.language.translate("Remove Friend") + '</span></a>';
      }
    })); 
  },
  remove : function(user_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'siteusercoverphoto/friends/remove',
      data : {
        format : 'json',
        user_id : user_id,
        subject : en4.core.subject.guid       
      },
      onComplete: function (response) {
        $('friendship_user').innerHTML ='';
        $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.friend.add(' + user_id + ')"><span>' + en4.core.language.translate("Add Friend") + '</span></a>';
      }
    })); 
  }
};
      
      
en4.siteusercoverphoto.follow = {
  add : function(user_id, varified) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'siteusercoverphoto/friends/add',
      data : {
        format : 'json',
        user_id : user_id,
        subject : en4.core.subject.guid       
      },
      onComplete: function (response) {
        if(varified) {
          $('friendship_user').innerHTML ='';
          $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.follow.cancel(' + user_id + ', ' + varified+')"><span>' + en4.core.language.translate("Cancel Follow Request") + '</span></a>';
        } else {
          $('friendship_user').innerHTML ='';
          $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.follow.remove(' + user_id + ', ' + varified+')"><span>' + en4.core.language.translate("Unfollow") + '</span></a>';
        }
      }
    })); 
  },
  cancel : function(user_id, varified) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'siteusercoverphoto/friends/cancel',
      data : {
        format : 'json',
        user_id : user_id,
        subject : en4.core.subject.guid       
      },
      onComplete: function (response) {
        $('friendship_user').innerHTML ='';
        $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.follow.add(' + user_id + ', ' + varified+')"><span>' + en4.core.language.translate("Follow") + '</span></a>';
      }
    })); 
  },
  confirm : function(user_id, varified) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'siteusercoverphoto/friends/confirm',
      data : {
        format : 'json',
        user_id : user_id,
        subject : en4.core.subject.guid       
      },
      onComplete: function (response) {
        $('friendship_user').innerHTML ='';
        $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.follow.remove(' + user_id + ', ' + varified+')"><span>' + en4.core.language.translate("Unfollow") + '</span></a>';
      }
    })); 
  },
  remove : function(user_id, varified) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'siteusercoverphoto/friends/remove',
      data : {
        format : 'json',
        user_id : user_id,
        subject : en4.core.subject.guid       
      },
      onComplete: function (response) {
        $('friendship_user').innerHTML ='';
        $('friendship_user').innerHTML = '<a id="aaf_addfriend_' + user_id + '" href="javascript: void(0);" onClick="en4.siteusercoverphoto.follow.add(' + user_id + ', ' + varified+')"><span>' + en4.core.language.translate("Follow") + '</span></a>';
      }
    })); 
  }
}