sm4.siteusercoverphoto={};

sm4.siteusercoverphoto.friend = {
  add : function(user_id) {
    sm4.core.request.send({
      url : sm4.core.baseUrl + 'siteusercoverphoto/friends/add',
			type: "POST", 
			dataType: "json",
      data : {
        format : 'json',
        user_id : user_id,
        subject : sm4.core.subject.guid       
      },
			complete: function (response) {
				$('#friendship_user').html('<a id="#aaf_addfriend_cancel_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.friend.cancel(' + user_id + ')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Cancel Friend Request") + ' </a>');
				sm4.core.runonce.trigger();
				sm4.core.refreshPage();
			}
    }); 
  },
	cancel : function(user_id) {
    sm4.core.request.send({
      url : sm4.core.baseUrl + 'siteusercoverphoto/friends/cancel',
			type: "POST", 
			dataType: "json",
      data : {
        format : 'json',
        user_id : user_id,
        subject : sm4.core.subject.guid       
      },complete: function (response) {
        $('#friendship_user').html('<a id="#aaf_addfriend_add_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.friend.add(' + user_id + ')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Add Friend") + ' </a>');
				sm4.core.runonce.trigger();
				sm4.core.refreshPage();
			}
    }); 
  },
	confirm : function(user_id) {
    sm4.core.request.send({
      url : sm4.core.baseUrl + 'siteusercoverphoto/friends/confirm',
			type: "POST", 
			dataType: "json",
      data : {
        format : 'json',
        user_id : user_id,
        subject : sm4.core.subject.guid       
      },complete: function (response) {
				$('#friendship_user').html('<a id="#aaf_addfriend_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.friend.remove(' + user_id + ')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Remove Friend") + ' </a>');
				sm4.core.runonce.trigger();
				sm4.core.refreshPage();
			}
    }); 
  },
	remove : function(user_id) {
    sm4.core.request.send({
      url : sm4.core.baseUrl + 'siteusercoverphoto/friends/remove',
			type: "POST", 
			dataType: "json",
      data : {
        format : 'json',
        user_id : user_id,
        subject : sm4.core.subject.guid       
      },complete: function (response) {
				$('#friendship_user').html('<a id="#aaf_addfriend_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.friend.add(' + user_id + ')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Add Friend") + ' </a>');
				sm4.core.runonce.trigger();
				sm4.core.refreshPage();
			}
    }); 
  }
};
      
      
sm4.siteusercoverphoto.follow = {
  add : function(user_id, varified) {
    sm4.core.request.send({
      url : sm4.core.baseUrl + 'siteusercoverphoto/friends/add',
			type: "POST", 
			dataType: "json",
      data : {
        format : 'json',
        user_id : user_id,
        subject : sm4.core.subject.guid       
      },
			complete: function (response) {
				if(varified) {
					$('#friendship_user').html('<a id="#aaf_addfriend_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.follow.cancel(' + user_id + ', ' + varified+')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Cancel Follow Request") + ' </a>');
				} else {
					$('#friendship_user').html('<a id="#aaf_addfriend_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.follow.remove(' + user_id + ', ' + varified+')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Unfollow") + ' </a>');
				}
				sm4.core.runonce.trigger();
				sm4.core.refreshPage();
			}
    }); 
  },
	cancel : function(user_id, varified) {
    sm4.core.request.send({
      url : sm4.core.baseUrl + 'siteusercoverphoto/friends/cancel',
			type: "POST", 
			dataType: "json",
      data : {
        format : 'json',
        user_id : user_id,
        subject : sm4.core.subject.guid       
      },complete: function (response) {
				$('#friendship_user').html('<a id="#aaf_addfriend_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.follow.add(' + user_id + ', ' + varified+')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Follow") + ' </a>');
				sm4.core.runonce.trigger();
				sm4.core.refreshPage();
			}
    }); 
  },
	confirm : function(user_id, varified) {
    sm4.core.request.send({
      url : sm4.core.baseUrl + 'siteusercoverphoto/friends/confirm',
			type: "POST", 
			dataType: "json",
      data : {
        format : 'json',
        user_id : user_id,
        subject : sm4.core.subject.guid       
      },complete: function (response) {
				$('#friendship_user').html('<a id="#aaf_addfriend_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.follow.remove(' + user_id + ', ' + varified+')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Unfollow") + ' </a>');
				sm4.core.runonce.trigger();
				sm4.core.refreshPage(); 
			}
    }); 
  },
	remove : function(user_id, varified) {
    sm4.core.request.send({
      url : sm4.core.baseUrl + 'siteusercoverphoto/friends/remove',
			type: "POST", 
			dataType: "json",
      data : {
        format : 'json',
        user_id : user_id,
        subject : sm4.core.subject.guid       
      },complete: function (response) {
				$('#friendship_user').html('<a id="#aaf_addfriend_' + user_id + '" href="javascript://" onClick="sm4.siteusercoverphoto.follow.add(' + user_id + ', ' + varified+')" data-role="button" data-icon="false" data-inset="false" data-mini="true" data-corners="false" data-shadow="true">' + sm4.core.language.translate("Follow") + ' </a>');
				sm4.core.runonce.trigger();
				sm4.core.refreshPage(); 
			}
    }); 
  }
}