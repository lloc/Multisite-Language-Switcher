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
				add_action( "pre_get_{$post_type}", [$obj, "orderby_translation"]);
				add_action( 'trashed_post', [ $obj, 'delete' ] );
			}
		}

		return $obj;
	}
	
	/**
	* Making stuff sortable 
	* @param WP_Query $query
	*/
	public function orderby_translation ($query) {
		
		$blogs = $this->collection->get();
		$orderby = $query->get('orderby');
		if ($blogs) {
			// Scan data for matching language 
			foreach ( $blogs as $blog ) {
				$language = $blog->get_language();
				$col = 'mslcol_' . $language;
				if (strcmp($col, $orderby) == 0) {
					//print_r('Matching language - not sure how to check existing language versions from here');
				}
			}
			
		}
		
		$origin_language = MslsBlogCollection::get_blog_language();

		if( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		// Not sure how to go from here (limited dbaccess to look into records)
	  
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

				$sep_colname = 'mslcol_' . $blog->get_language();
				$columns[$sep_colname] =  $icon->get_icon();
			}
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
			// Get all blogs
			$blogs           = $this->collection->get();
			$origin_language = MslsBlogCollection::get_blog_language();
			
			// Set original source
			$ids[]			 = $item_id;
			$langs[]		 = $origin_language;
			
			// Filter out the language 
			$columns_language = substr($column_name, strlen('mslcol_'), strlen($column_name));
			// print_r($columns_language);
			if ( $blogs ) {
				
				// Get interlinking between translations 
				$mydata = MslsOptions::create( $item_id );
				foreach( $blogs as $blog) {
					switch_to_blog( $blog->userblog_id );
					$lang = $blog->get_language();
					
					// Set as nothing
					$obj_id = null; 
					$term = null;
					 
					// Handle Terms
					if ($mydata instanceof MslsOptionsTaxTerm){
						
						$temp = get_term( $mydata->$lang, $mydata->base  );
						
						if (!empty($temp) && !is_wp_error($temp)){ 
							$obj_id = $temp->term_id; 
						}
					}
					
					// Handle Posts
					if ($mydata instanceof MslsOptionsPost){
						$temp = get_post( $mydata->$lang );						
						if (!empty($temp) && !is_wp_error($term)){ 
							$obj_id = $temp->ID;
						}
					}

					// Do not store empty records 
					if ( strcmp($blog->get_language(), $origin_language) != 0 && $obj_id != $item_id && $obj_id != null) { 
						$ids[] = $obj_id;
						$langs[] = $blog->get_language();
					}
					restore_current_blog();
				}
				
				// Print icons with changed links 
				foreach ( $blogs as $blog ) {
					switch_to_blog( $blog->userblog_id );

					$language = $blog->get_language();

					$icon = MslsAdminIcon::create();
					$icon->set_language( $language );
					$icon->set_id( $ids );
					$icon->set_origin_language( $langs );

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
