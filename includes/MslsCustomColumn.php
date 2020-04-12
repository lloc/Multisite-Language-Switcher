<?php
/**
 * MslsCustomColumn
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Handling of existing/not existing translations in the backend listings of
 * various post types
 * @package Msls
 */
class MslsCustomColumn extends MslsMain {

	/**
	 * Factory
	 *
	 * @codeCoverageIgnore
	 *
	 * @return MslsCustomColumn
	 */
	public static function init() {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();
		$obj        = new static( $options, $collection );

		if ( ! $options->is_excluded() ) {
			$post_type = MslsPostType::instance()->get_request();
			
			if ( ! empty( $post_type ) ) {
				add_filter( "manage_{$post_type}_posts_columns", [ $obj, 'th' ] );
				add_action( "manage_{$post_type}_posts_custom_column", [ $obj, 'td' ], 10, 2 );
				add_action( "manage_edit-{$post_type}_sortable_columns", [ $obj, 'sortable_cols' ], 10, 2 );
				
				add_action( 'trashed_post', [ $obj, 'delete' ] );
			}
		}

		return $obj;
	}
	
	/**
	* Table header- Sorting
	* @param array $columns
	* @return array
	*/
	public function sortable_cols( $columns ) {
		$blogs = $this->collection->get();
		if ( $blogs ) {
			$blogs = $this->collection->get();
			foreach ( $blogs as $blog ) {
				$language = $blog->get_language();
				$col_name = 'mslcol_' . $language;
				$columns[$col_name] = $col_name;
			}
		}
		return $columns;
	}
	
	

	/**
	 * Table header
	 * @param array $columns
	 * @return array
	 */
	public function th( $columns ) {
		
		$blogs = $this->collection->get();
		if ( $blogs ) {		
			$arr = [];
			foreach ( $blogs as $blog ) {
				$language = $blog->get_language();

				$icon = new MslsAdminIcon( null );
				$icon->set_language( $language )->set_icon_type( 'flag' );

				if ( $post_id = get_the_ID() ) {
					$icon->set_id( $post_id );
					$icon->set_origin_language( 'it_IT' );
				}

				/* $arr[] = $icon->get_icon(); */
				$sep_colname = 'mslcol_' . $blog->get_language();
				$columns[$sep_colname] =  $icon->get_icon();
			}
			/* $columns['mslscol'] = implode( '&nbsp;', $arr ); */
		}

		return $columns;
	}

	/**
	 * Table body
	 *
	 * @param string $column_name
	 * @param int $item_id
	 *
	 * @codeCoverageIgnore
	 */
	public function td( $column_name, $item_id ) {
	
		// Check for msl column name
		$msl_pos = strpos($column_name, 'mslcol_');
		if ( $msl_pos == 0 ) {
			$blogs           = $this->collection->get();
			$origin_language = MslsBlogCollection::get_blog_language();
			// Filter out the language 
			$columns_language = substr($column_name, strlen('mslcol_'), strlen($column_name));
			// print_r($columns_language);
			if ( $blogs ) {
				$mydata = MslsOptions::create( $item_id );
				foreach ( $blogs as $blog ) {
					switch_to_blog( $blog->userblog_id );

					$language = $blog->get_language();

					$icon = MslsAdminIcon::create();
					$icon->set_language( $language );
					$icon->set_id( $item_id );
					$icon->set_origin_language( $origin_language );

					$icon->set_icon_type( 'action' );

					if ( $mydata->has_value( $language ) ) {
						$icon->set_href( $mydata->$language );
					}
					
					// Print only thye corresponding flag
					if (strcmp($blog->get_language(), $columns_language) == 0 ) {
						echo $icon->get_a();
					}
					

					restore_current_blog();
				}
			}
		}
	}

}
