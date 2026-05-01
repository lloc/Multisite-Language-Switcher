jQuery( document ).ready(
	function ( $ ) {
		var config = window.mslsTranslationPicker;
		if ( ! config || ! config.targetBlogId ) {
			return;
		}

		var $form = $( '#msls-tp-form' );
		if ( ! $form.length ) {
			return;
		}

		var $notice = $( '<div class="notice notice-info inline msls-tp-progress" hidden><p></p></div>' );
		$form.before( $notice );
		var $noticeMsg = $notice.find( 'p' );

		function showNotice( text, cls ) {
			$notice.removeClass( 'notice-info notice-success notice-error' )
				.addClass( cls || 'notice-info' )
				.removeAttr( 'hidden' );
			$noticeMsg.text( text );
		}

		function hideNotice() {
			$notice.attr( 'hidden', 'hidden' );
		}

		function createOne( sourcePostId, sourceBlogId ) {
			return wp.apiFetch(
				{
					path: '/msls/v1/create-translation',
					method: 'POST',
					data: {
						source_post_id: sourcePostId,
						source_blog_id: sourceBlogId,
						target_blog_id: config.targetBlogId
					}
				}
			);
		}

		$form.on(
			'click',
			'.msls-tp-create',
			function ( event ) {
				event.preventDefault();
				var $btn = $( this );
				if ( $btn.prop( 'disabled' ) ) {
					return;
				}

				var sourcePostId = parseInt( $btn.data( 'source-post-id' ), 10 );
				var sourceBlogId = parseInt( $btn.data( 'source-blog-id' ), 10 );
				if ( ! sourcePostId || ! sourceBlogId ) {
					return;
				}

				$btn.prop( 'disabled', true ).addClass( 'msls-loading' );
				showNotice( config.i18n.creating );

				createOne( sourcePostId, sourceBlogId ).then(
					function ( response ) {
						if ( response && response.edit_url ) {
							window.location.href = response.edit_url;
						}
					}
				).catch(
					function () {
						$btn.prop( 'disabled', false ).removeClass( 'msls-loading' );
						showNotice( config.i18n.error, 'notice-error' );
					}
				);
			}
		);

		function selectedRows() {
			return $form.find( 'tbody input[type="checkbox"][name="post[]"]:checked' );
		}

		function runBulk( sourceBlogId ) {
			var $checked = selectedRows();
			if ( ! $checked.length ) {
				showNotice( config.i18n.noneChose, 'notice-warning' );
				return;
			}

			var tasks = $checked.map(
				function () {
					return {
						postId: parseInt( this.value, 10 ),
						$button: $( this ).closest( 'tr' ).find( '.msls-tp-create' )
					};
				}
			).get();

			var total = tasks.length;
			var done = 0;
			var errors = 0;
			var completed = [];

			function step() {
				if ( ! tasks.length ) {
					showNotice(
						config.i18n.completed.replace( '%1$d', done ).replace( '%2$d', errors ),
						errors ? 'notice-warning' : 'notice-success'
					);
					// Fade out successfully-created rows
					completed.forEach(
						function ( $row ) {
							$row.css( 'opacity', 0.5 );
						}
					);
					// Reload so the now-translated rows drop out of the list
					// and pagination/totals reflect the new state. Brief
					// delay lets the user read the completion notice.
					if ( done > 0 ) {
						window.setTimeout(
							function () {
								window.location.reload();
							},
							1500
						);
					}
					return;
				}

				var task = tasks.shift();
				showNotice(
					config.i18n.progress
						.replace( '%1$d', done + errors + 1 )
						.replace( '%2$d', total )
				);

				if ( task.$button.length ) {
					task.$button.prop( 'disabled', true ).addClass( 'msls-loading' );
				}

				createOne( task.postId, sourceBlogId ).then(
					function () {
						done++;
						if ( task.$button.length ) {
							task.$button
								.prop( 'disabled', true )
								.removeClass( 'msls-loading' )
								.addClass( 'msls-tp-done' );
						}
						completed.push( task.$button.closest( 'tr' ) );
						step();
					}
				).catch(
					function () {
						errors++;
						if ( task.$button.length ) {
							task.$button.prop( 'disabled', false ).removeClass( 'msls-loading' );
						}
						step();
					}
				);
			}

			step();
		}

		// Derive the source blog id from any visible create button (all
		// rows share the same source on the current page).
		function currentSourceBlogId() {
			var $any = $form.find( '.msls-tp-create' ).first();
			return $any.length ? parseInt( $any.data( 'source-blog-id' ), 10 ) : 0;
		}

		$form.on(
			'submit',
			function ( event ) {
				var action = $form.find( 'select[name="action"]' ).val();
				if ( 'msls_bulk_create' !== action ) {
					action = $form.find( 'select[name="action2"]' ).val();
				}
				if ( 'msls_bulk_create' !== action ) {
					return;
				}

				event.preventDefault();
				var sourceBlogId = currentSourceBlogId();
				if ( ! sourceBlogId ) {
					return;
				}
				runBulk( sourceBlogId );
			}
		);
	}
);
