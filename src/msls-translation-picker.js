jQuery( document ).ready(
	function ( $ ) {
		if ( typeof window.mslsTranslationPicker === 'undefined' ) {
			return;
		}

		var config = window.mslsTranslationPicker;
		var $root = $( '#' + config.inlineId );

		if ( ! $root.length ) {
			return;
		}

		var $list = $root.find( '.msls-tp-list' );
		var $status = $root.find( '.msls-tp-status' );
		var $blogSelect = $root.find( '#msls-tp-blog' );
		var $searchRow = $root.find( '.msls-tp-search-row' );
		var $searchInput = $root.find( '#msls-tp-search' );

		var sourceAlpha2ById = {};
		$.each(
			config.sourceBlogs || [],
			function ( _, blog ) {
				sourceAlpha2ById[ String( blog.blog_id ) ] = blog.alpha2;
			}
		);

		// Inject the "Add from Translation" page-title-action next to core's
		// "Add New". WordPress exposes no server-side hook at this location.
		var $wrap = $( '.wp-header-end' ).prev( '.wp-heading-inline' ).parent();
		if ( ! $wrap.length ) {
			$wrap = $( '.wrap' ).first();
		}

		var $existingAction = $wrap.find( '.page-title-action' ).first();
		var href = '#TB_inline?width=620&height=520&inlineId=' + encodeURIComponent( config.inlineId );
		var $button = $( '<a>' )
			.addClass( 'page-title-action thickbox msls-tp-button' )
			.attr( 'href', href )
			.attr( 'title', config.i18n.modalTitle )
			.text( config.i18n.buttonLabel );

		if ( $existingAction.length ) {
			$existingAction.after( ' ', $button );
		} else {
			$wrap.find( '.wp-heading-inline' ).after( ' ', $button );
		}

		function renderSkeleton() {
			$status.text( config.i18n.loading );
			$list.empty();
			for ( var i = 0; i < 3; i++ ) {
				$list.append(
					$( '<li>' )
						.addClass( 'msls-tp-item msls-tp-skeleton' )
						.attr( 'aria-hidden', 'true' )
						.append( $( '<span>' ).addClass( 'msls-tp-skeleton-line msls-tp-skeleton-title' ) )
						.append( $( '<span>' ).addClass( 'msls-tp-skeleton-line msls-tp-skeleton-meta' ) )
				);
			}
		}

		function statusLabel( key ) {
			return ( config.i18n.statusLabels && config.i18n.statusLabels[ key ] ) || key;
		}

		function formatDate( dateGmt ) {
			if ( ! dateGmt ) {
				return '';
			}
			try {
				return new Date( dateGmt ).toLocaleDateString();
			} catch ( e ) {
				return dateGmt;
			}
		}

		function renderItems( items, isSearch ) {
			$list.empty();

			if ( ! items.length ) {
				$status.text( isSearch ? config.i18n.emptySearch : config.i18n.empty );
				return;
			}

			$status.text( '' );

			var alpha2 = sourceAlpha2ById[ String( $blogSelect.val() ) ] || '';

			$.each(
				items,
				function ( _, item ) {
					var $title = $( '<span>' ).addClass( 'msls-tp-item-title' ).text( item.title );

					var $badges = $( '<span>' ).addClass( 'msls-tp-item-badges' );
					if ( alpha2 ) {
						$badges.append(
							$( '<span>' ).addClass( 'msls-tp-lang-chip' ).text( alpha2.toUpperCase() )
						);
					}
					$badges.append(
						$( '<span>' )
							.addClass( 'msls-tp-status-badge msls-tp-status-' + item.post_status )
							.text( statusLabel( item.post_status ) )
					);

					var date = formatDate( item.date_gmt );
					if ( date ) {
						$badges.append(
							$( '<span>' ).addClass( 'msls-tp-item-date' ).text( date )
						);
					}

					var $primary = $( '<div>' ).addClass( 'msls-tp-item-primary' )
						.append( $title )
						.append( $badges );

					var $actions = $( '<div>' ).addClass( 'msls-tp-item-actions' );
					if ( item.view_url ) {
						$actions.append(
							$( '<a>' )
								.addClass( 'msls-tp-item-view' )
								.attr( 'href', item.view_url )
								.attr( 'target', '_blank' )
								.attr( 'rel', 'noopener noreferrer' )
								.text( config.i18n.viewOriginal )
						);
					}

					var $create = $( '<button>' )
						.attr( 'type', 'button' )
						.addClass( 'button button-primary msls-tp-item-create' )
						.attr( 'data-source-post-id', item.id )
						.text( config.i18n.buttonLabel );
					$actions.append( $create );

					$list.append(
						$( '<li>' ).addClass( 'msls-tp-item' ).append( $primary ).append( $actions )
					);
				}
			);
		}

		var currentRequestToken = 0;

		function loadList( sourceBlogId, search ) {
			renderSkeleton();

			var params = {
				source_blog_id: sourceBlogId,
				target_blog_id: config.targetBlogId,
				post_type: config.postType
			};
			if ( search ) {
				params.search = search;
			}

			var token = ++currentRequestToken;

			wp.apiFetch(
				{
					path: '/msls/v1/untranslated-posts?' + $.param( params )
				}
			).then(
				function ( response ) {
					if ( token !== currentRequestToken ) {
						return;
					}
					renderItems( response.items || [], Boolean( search ) );
				}
			).catch(
				function () {
					if ( token !== currentRequestToken ) {
						return;
					}
					$status.text( config.i18n.error );
					$list.empty();
				}
			);
		}

		function createTranslation( sourcePostId, sourceBlogId, $triggerButton ) {
			$status.text( config.i18n.creating );
			if ( $triggerButton ) {
				$triggerButton.prop( 'disabled', true ).addClass( 'msls-loading' );
			}

			wp.apiFetch(
				{
					path: '/msls/v1/create-translation',
					method: 'POST',
					data: {
						source_post_id: sourcePostId,
						source_blog_id: sourceBlogId,
						target_blog_id: config.targetBlogId
					}
				}
			).then(
				function ( response ) {
					if ( response && response.edit_url ) {
						window.location.href = response.edit_url;
					}
				}
			).catch(
				function () {
					$status.text( config.i18n.error );
					if ( $triggerButton ) {
						$triggerButton.prop( 'disabled', false ).removeClass( 'msls-loading' );
					}
				}
			);
		}

		function onSourceSelected() {
			var sourceBlogId = parseInt( $blogSelect.val(), 10 );
			if ( ! sourceBlogId ) {
				$list.empty();
				$status.text( '' );
				$searchRow.attr( 'hidden', 'hidden' );
				return;
			}
			$searchRow.removeAttr( 'hidden' );
			$searchInput.val( '' );
			loadList( sourceBlogId, '' );
		}

		$blogSelect.on( 'change', onSourceSelected );

		var searchTimer = null;
		$searchInput.on(
			'input',
			function () {
				var term = $( this ).val().trim();
				var sourceBlogId = parseInt( $blogSelect.val(), 10 );
				if ( ! sourceBlogId ) {
					return;
				}
				window.clearTimeout( searchTimer );
				searchTimer = window.setTimeout(
					function () {
						loadList( sourceBlogId, term );
					},
					300
				);
			}
		);

		$list.on(
			'click',
			'.msls-tp-item-create',
			function ( event ) {
				event.preventDefault();
				var sourceBlogId = parseInt( $blogSelect.val(), 10 );
				var $btn = $( this );
				var sourcePostId = parseInt( $btn.data( 'source-post-id' ), 10 );
				if ( ! sourceBlogId || ! sourcePostId ) {
					return;
				}
				createTranslation( sourcePostId, sourceBlogId, $btn );
			}
		);

		// Auto-select: last-used source (if still available), else single source.
		var sources = config.sourceBlogs || [];
		var autoPick = 0;

		if ( config.lastSourceBlogId ) {
			for ( var i = 0; i < sources.length; i++ ) {
				if ( parseInt( sources[ i ].blog_id, 10 ) === parseInt( config.lastSourceBlogId, 10 ) ) {
					autoPick = parseInt( sources[ i ].blog_id, 10 );
					break;
				}
			}
		}

		if ( ! autoPick && sources.length === 1 ) {
			autoPick = parseInt( sources[ 0 ].blog_id, 10 );
		}

		if ( autoPick ) {
			$blogSelect.val( String( autoPick ) );
			// Defer so Thickbox open doesn't race with the initial fetch.
			$( document ).on(
				'tb_unload',
				function () {
					currentRequestToken++;
				}
			);
			$( document ).on(
				'click',
				'.msls-tp-button',
				function () {
					// Re-sync on re-open in case the user picked something else earlier.
					$blogSelect.val( String( autoPick ) );
					onSourceSelected();
				}
			);
		}
	}
);
