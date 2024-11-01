jQuery(document).ready(function() {
 jQuery('div.sdfwr_id').each(function(){
 var id= jQuery(this).data('name');
    jQuery('#'+id+' .sdfwr_loading').hide();
    var att=jQuery('#'+id+' .sdfwr_load').data('attribs');

    jQuery('#'+id+' .sdfwr_load').on('click', function(e) {
    e.preventDefault();
    jQuery('#'+id+' .sdfwr_load').hide();
    jQuery('#'+id+' .sdfwr_loading').show();
    var count=jQuery('#'+id+' .sdfwr_item').length;
	jQuery.ajax({
		type: "POST",                 // use $_POST request to submit data
		url: sdfwr_ajax_url.ajax_url,      // URL to "wp-admin/admin-ajax.php"
		data: {
			action     : 'sdfwreviews', // wp_ajax_*, wp_ajax_nopriv_*
            security : sdfwr_ajax_url.check_nonce,
			current_count : count,
            load_count : jQuery('#'+id+' div.sdfwr_id').data('count'),
            load_orderby : jQuery('#'+id+' div.sdfwr_id').data('orderby'),
            load_sort : jQuery('#'+id+' div.sdfwr_id').data('sort'),
            load_attribs : jQuery('#'+id+' div.sdfwr_id').data('attribs'),
            load_cats : jQuery('#'+id+' div.sdfwr_id').data('cats'),
            load_authors : jQuery('#'+id+' div.sdfwr_id').data('authors'),
            load_tags : jQuery('#'+id+' div.sdfwr_id').data('tags')
		},
		success:function( data ) {
		  jQuery('#'+id+' .sdfwr_container').fadeOut(1).append( data ).fadeIn(500); //
          jQuery('#'+id+' .sdfwr_loading').hide();
          jQuery('#'+id+' .sdfwr_load').show();
          var newcount=jQuery('#'+id+' .sdfwr_item').length;
          if(newcount-count == 0)jQuery('#'+id+' .sdfwr_load').fadeOut(500);
		},
		error: function(){
			console.log(errorThrown); // error
		}
	});

    });
    });

});