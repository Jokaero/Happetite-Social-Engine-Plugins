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

////// FUNCTION FOR CREATING A LIKE OR UNLIKE.
function seaocore_content_type_likes(resource_id, resource_type) {

  content_type_undefined = 0;
  if(seaocore_content_type) {
    var content_type = seaocore_content_type;
  }
  else {
  	var content_type = resource_type;
  }
  
	if (resource_type == '') { 
		content_type_undefined = 1;
		var content_type = resource_type;
	}
	
	// SENDING REQUEST TO AJAX
	var request = seaocore_content_create_like(resource_id, resource_type,content_type);
	
	// RESPONCE FROM AJAX
	request.addEvent('complete', function(responseJSON) {
		if (content_type_undefined == 0) {
			if(responseJSON.like_id )	{
				if($(content_type+'_like_'+ resource_id))
				$(content_type+'_like_'+ resource_id).value = responseJSON.like_id;
				if($(content_type+'_most_likes_'+ resource_id))
				$(content_type+'_most_likes_'+ resource_id).style.display = 'none';
				if($(content_type+'_unlikes_'+ resource_id))
				$(content_type+'_unlikes_'+ resource_id).style.display = 'inline-block';
				if($(content_type+'_num_of_like_'+ resource_id)) {
					$(content_type + '_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
				}
			}	else	{
				if($(content_type+'_like_'+ resource_id))
				$(content_type+'_like_'+ resource_id).value = 0;
				if($(content_type+'_most_likes_'+ resource_id))
				$(content_type+'_most_likes_'+ resource_id).style.display = 'inline-block';
				if($(content_type+'_unlikes_'+ resource_id))
				$(content_type+'_unlikes_'+ resource_id).style.display = 'none';
				if($(content_type+'_num_of_like_'+ resource_id)) {
					$(content_type + '_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
				}
			}
		}
	});
}

function seaocore_content_create_like( resource_id, resource_type, content_type ) {
	if($(content_type + '_like_'+ resource_id)) {
		var like_id = $(content_type + '_like_'+ resource_id).value
	}
	var request = new Request.JSON({
		url : en4.core.baseUrl + 'seaocore/like/like',
		data : {
			format : 'json',
				'resource_id' : resource_id,
				'resource_type' : resource_type,	
				'like_id' : like_id
		}
	});
	request.send();
	return request;
}
//FUNCTION FOR LIKE OR UNLIKE.