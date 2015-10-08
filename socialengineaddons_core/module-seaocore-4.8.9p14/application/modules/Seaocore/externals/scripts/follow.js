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
function seaocore_content_type_follows(resource_id, resource_type) {

  content_type_undefined = 0;
	var content_type = seaocore_content_type;
	if (seaocore_content_type == '') { 
		content_type_undefined = 1;
		var content_type = resource_type;
	}
	
	// SENDING REQUEST TO AJAX
	var request = seaocore_content_create_follow(resource_id, resource_type, content_type);
	
	// RESPONCE FROM AJAX
	request.addEvent('complete', function(responseJSON) {
		if (content_type_undefined == 0) {
			if(responseJSON.follow_id )	{
				$(content_type+'_follow_'+ resource_id).value = responseJSON.follow_id;
				$(content_type+'_most_follows_'+ resource_id).style.display = 'none';
				$(content_type+'_unfollows_'+ resource_id).style.display = 'inline-block';

				if($(content_type+'_num_of_follow_'+ resource_id)) {
					$(content_type + '_num_of_follow_'+ resource_id).innerHTML = responseJSON.follow_count;
				}
				
				if($(content_type+'_num_of_follows_'+ resource_id)) { 
					$(content_type + '_num_of_follows_'+ resource_id).innerHTML = responseJSON.follow_count;
				}
			}	else	{
				$(content_type+'_follow_'+ resource_id).value = 0;
				$(content_type+'_most_follows_'+ resource_id).style.display = 'inline-block';
				$(content_type+'_unfollows_'+ resource_id).style.display = 'none';
				
				if($(content_type+'_num_of_follow_'+ resource_id)) {
					$(content_type + '_num_of_follow_'+ resource_id).innerHTML = responseJSON.follow_count;
				}
				
				if($(content_type+'_num_of_follows_'+ resource_id)) {
					$(content_type + '_num_of_follows_'+ resource_id).innerHTML = responseJSON.follow_count;
				}
			}
		}
	});
}

function seaocore_content_create_follow( resource_id, resource_type, content_type ) {
	if($(content_type + '_follow_'+ resource_id)) {
		var follow_id = $(content_type + '_follow_'+ resource_id).value
	}
	var request = new Request.JSON({
		url : en4.core.baseUrl + 'seaocore/follow/global-follows',
		data : {
			format : 'json',
				'resource_id' : resource_id,
				'resource_type' : resource_type,	
				'follow_id' : follow_id
		}
	});
	request.send();
	return request;
}
//FUNCTION FOR FOLLOW OR UNFOLLOW.
