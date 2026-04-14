jQuery( document ).ready(
	function ( $ ) {
		$( '.msls-quick-create' ).on(
			'click',
			function ( e ) {
				e.preventDefault();

				var $button = $( this );
				var $li     = $button.closest( 'li' );

				if ( $button.hasClass( 'msls-loading' ) ) {
					return;
				}

				$button.addClass( 'msls-loading' );
				$button.find( '.dashicons' ).removeClass( 'dashicons-plus' ).addClass( 'dashicons-update' );

				wp.apiFetch(
					{
						path: '/msls/v1/create-translation',
						method: 'POST',
						data: {
							source_post_id: parseInt( $button.data( 'source-post-id' ), 10 ),
							source_blog_id: parseInt( $button.data( 'source-blog-id' ), 10 ),
							target_blog_id: parseInt( $button.data( 'target-blog-id' ), 10 )
						}
					}
				).then(
					function ( response ) {
						$button.remove();

						var $icon = $li.find( '.dashicons-plus' );
						if ( $icon.length ) {
							$icon.removeClass( 'dashicons-plus' ).addClass( 'dashicons-edit' );
							$icon.parent( 'a' ).attr( 'href', response.edit_url );
						}

						var $hiddenInput = $li.find( 'input[type="hidden"][name^="msls_input_"]' );
						if ( $hiddenInput.length ) {
							$hiddenInput.val( response.post_id );
						}

						var $textInput = $li.find( 'input.msls_title' );
						if ( $textInput.length ) {
							$textInput.show();
						}

						var $select = $li.find( 'select[name^="msls_input_"]' );
						if ( $select.length ) {
							$select.append(
								$( '<option>' ).val( response.post_id ).text( response.edit_url ).prop( 'selected', true )
							);
						}
					}
				).catch(
					function () {
						$button.removeClass( 'msls-loading' );
						$button.find( '.dashicons' ).removeClass( 'dashicons-update' ).addClass( 'dashicons-plus' );
					}
				);
			}
		);
	}
);
