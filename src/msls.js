jQuery( document ).ready( function( $ ) {
	$( 'input.msls_title' ).focus( function() {
		var blog_id   = $( this ).attr( 'name' ).slice( 11 );
		var hid_field = $( '#msls_id_' + blog_id );
		var mslsinput = {
			id: hid_field.val(),
			title: $( this ).val()
		};
		$( this ).select().autocomplete( {
			minLength: 0,
			source: function( request, response ) {
				$.ajax( {
					url: ajaxurl,
					data: {
						post_type: $( '#msls_post_type' ).val(),
						blog_id: blog_id,
						s: request.term,
						action: $( '#msls_action' ).val()
					},
					dataType: 'JSON',
					type: 'POST',
					success: function( data ) {
						response( data ); 
					}
				} );
			},
			focus: function( event, ui ) {
				$( event.target ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( event.target ).val( ui.item.label );
				hid_field.val( ui.item.value );
				return false;
			},
			change: function( event, ui ) {
				if ( !$( event.target ).val() ) {
					hid_field.val( '' );
				}
				else if (
					mslsinput.id === hid_field.val() &&
					mslsinput.title !== $( event.target ).val()
				) {
					$( event.target ).val( mslsinput.title );
				}
				return false;
			}
		} );
	} );
} );
