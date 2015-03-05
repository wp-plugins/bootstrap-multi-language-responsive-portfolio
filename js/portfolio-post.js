jQuery(document).ready(function($){
	$('a.add').click(function(){
		
		var image_id = $('input[name="image_id"]').val();
		image_id = parseInt(image_id) + 1;
		imgDetails = '<div class="postbox clone imgbox-'+image_id+'"><div class="handlediv clonedata" title="Click to toggle"><br></div><h3 class="hndle"><span>Image Details</span></h3><div class="inside" style="margin-left: 20px;"><div class="form-field"><label for="cover_image">Portfolio Image</label><div class="cover_image" style="display:none;"><img src="" name="slider_display_cover_image"  /></div><p><span><i>Best image size : <strong>1600px * 837px</strong> (upload : JPG, PNG & GIF )</i></span></p><input type="hidden" size="36" name="slider_upload_image[]" value="" /><p><input name="slider_upload_image_button" type="button" value="Upload" class="portfolio_image_issue button button-primary" style="margin-right: 2px;"/><input name="slider_remove_image_button" type="button" value="Remove Image"  width="8%" class="portfolio_remove_issue button button-primary" style="display:none;"></p></div></div><div class="hr" style="margin-bottom: 10px;"></div><p style="overflow:hidden; padding-right:10px;"><a href="javascript:void(0);" onclick="removebox('+image_id+');" class="btn-right button button-remove button-sm">- Remove</a></p></div>';
		
		$('div.imageDetailsClone').prepend(imgDetails);
		$('input[name="image_id"]').val(image_id);
		manageremove();
		
	});

		var upload_third_loc_file;
		var upload_third_locationlabel = 0;
	 	var myval;
		 // Bind to our click event in order to open up the new media experience.
		 $(document.body).on('click.mojoOpenMediaManager', 'input[name="slider_upload_image_button"]', function(e){ //portfolio_image_issue is the class of our form button
		 	// Prevent the default action from occuring.
			 e.preventDefault();
			// Get our Parent element
			 upload_third_locationlabel = $(this).parent();
			 
			 myval = $(this).parents('.form-field');
			 // If the frame already exists, re-open it.
			 if ( upload_third_loc_file ) {
			 upload_third_loc_file.open();
			 return;
			 }
			 
			 upload_third_loc_file = wp.media.frames.upload_third_loc_file = wp.media({
						 title: "Add Portfolio Slider Banner",
					     button: {
						  text: "Insert Portfolio Slider Banner",
					     },
						 editing:    true,
						 className: 'media-frame upload_third_loc_file',
						 frame: 'select', //Allow Select Only
						 multiple: false, //Disallow Mulitple selections
						 library: {
						 type: 'image' //Only allow images type: 'image'
				 },
			});
			
			 upload_third_loc_file.on('select', function(){
			 // Grab our attachment selection and construct a JSON representation of the model.
			 var loc_media_attachment = upload_third_loc_file.state().get('selection').first().toJSON();
			 
			 if(typeof(loc_media_attachment.sizes.thumbnail) != "undefined")
			 	var thum_url = loc_media_attachment.sizes.thumbnail.url;
			 else	
			 	var thum_url = loc_media_attachment.sizes.full.url;
			 
			 var thumb_id = loc_media_attachment.id; 
			// Send the attachment URL to our custom input field via jQuery.
			 loc_url = loc_media_attachment.url;
			 locurls = loc_url.substr( (loc_url.lastIndexOf('.') +1) );
			
			 if(locurls !='pdf' && locurls !='zip' && locurls !='rar')
			 {
			 	 myval.find('.cover_image').css('display','block');
				 myval.find('input[name="slider_remove_image_button"]').css('display','inline-block');
				 myval.find('input[name="slider_upload_image[]"]').val(thumb_id);
				 myval.find('img[name="slider_display_cover_image"]').attr('src',thum_url );
			 }else{
				     alert('Please add only image');
		     }
			 });
	 
			// Now that everything has been set, let's open up the frame.
			 upload_third_loc_file.open();
		 });
		 
		 
		 // extrnal image  upload data
		 		var upload_third_loc_file;
		var upload_third_locationlabel = 0;
	 
		 // Bind to our click event in order to open up the new media experience.
		 
		
		 
		 // image remove process
		 manageremove();
		
	$('.imageDetailsClone').sortable();
});
function removebox(image_id)
{
	jQuery('.imgbox-'+image_id).remove();
	totalbox = parseInt(jQuery('input[name="image_id"]').val())-1;
	jQuery('input[name="image_id"]').val(totalbox);
}
function manageremove()
{
	$ = jQuery; 
	$('input[name="slider_remove_image_button"]').click(function(){
		$(this).parents('.form-field').find('input[name="slider_display_cover_image"]').attr('src','');
		$(this).parents('.form-field').find('input[name="slider_upload_image[]"]').attr('value','');	
		$(this).parents('.form-field').find('.cover_image').css('display','none');
		$(this).parents('.form-field').find('input[name="slider_remove_image_button"]').css('display','none');
	});
	
	$('.clonedata').click(function(){
		$(this).parents('.clone').toggleClass('closed');	
	});
}