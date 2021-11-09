(function ( $ ) {
	"use strict";

    $(document).ready(function($) {
        $('.ns_featured_posts_icon').on('click', function() {
            var $this = $(this);
            var $table = $('#posts-filter');

			var $post_id    = $this.data('post_id');
			var $post_type  = $this.data('post_type');
			var $max_posts  = $this.data('max_posts');

			var $target_status = ( $this.hasClass( 'selected' ) ) ? 'no' : 'yes';
			var $uno_status    = ( typeof $this.data('uno') !== 'undefined' ) ? 1 : 0;
			var $max_status    = ( typeof $this.data('max_status') !== 'undefined' ) ? 1 : 0;

            $.post(
            	NSFP_OBJ.ajaxurl,
            	{
            		"action": "nsfeatured_posts",
            		"post_id": $post_id,
            		"post_type": $post_type,
            		"ns_featured": $target_status,
            		"uno": $uno_status,
            		"max_posts": $max_posts,
            		"max_status": $max_status,
            		"nonce": NSFP_OBJ.nonce,
            	},
            	function(data, status) {
            		if ( 'success' == status) {
            			if ( true == data.status ) {
		            		$this.toggleClass('selected');

		            		if ( true == data.uno ) {
		            			$table.find('.ns_featured_posts_icon.selected')
			            			.not('[data-post_id="' + $post_id + '"]')
			            			.each(function(i, el){
			            				$(el).removeClass('selected');
			            			});
		            		}
            			} else {
	            			alert( data.message );
            			}
            		}
            	} );
        });
    });

}(jQuery));
