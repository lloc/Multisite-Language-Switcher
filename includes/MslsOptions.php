<?php

/**
 * Options
 *
 * @package Msls
 */

/**
 * MslsOptions
 * 
 * @package Msls
 */
class MslsOptions extends MslsGetSet implements IMslsRegistryInstance {

    /**
     * @var array
     */
    protected $args;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $exists = false;

    /**
     * @var string
     */
    protected $sep = '';

    /**
     * @var string
     */
    protected $autoload = 'yes';

    /**
     * @var string
     */
    protected $base;

    /**
     * Factory method
     * 
     * @param int $id
     * @return MslsOptions
     */
    public static function create( $id = 0 ) {
        if ( is_admin() ) {
            $id  = (int) $id;
            $obj = MslsContentTypes::create();
            if ( $obj->is_taxonomy() ) {
                return MslsTaxOptions::create( $id );
            }
            return new MslsPostOptions( $id );
        }
        else {
            if ( is_front_page() || is_search() || is_404() ) {
                return new MslsOptions();
            }
            elseif ( is_category() || is_tag() || is_tax() ) {
                return MslsTaxOptions::create();
            }
            elseif ( is_date() || is_author() || is_post_type_archive() ) {
                return MslsQueryOptions::create();
            }
            global $wp_query;
            return new MslsPostOptions( $wp_query->get_queried_object_id() );
        }
        return null;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->args   = func_get_args();
        $this->name   = 'msls' . $this->sep . implode( $this->sep, $this->args );
        $this->exists = $this->set( get_option( $this->name ) );
        $this->base   = $this->get_base();
    }

    /**
     * Save
     * 
     * @param mixed $arr
     */
    public function save( $arr ) {
        if ( $this->set( $arr ) ) {
            $this->delete();
            $arr = $this->get_arr();
            if ( !empty( $arr ) )
                add_option( $this->name, $arr, '', $this->autoload );
        }
    }

    /**
     * Delete
     */
    public function delete() {
        delete_option( $this->name );
    }

    /**
     * Set
     * 
     * @param mixed $arr
     * @return bool
     */
    public function set( $arr ) {
        if ( is_array( $arr ) ) {
            foreach ( $arr as $key => $value ) {
                $this->__set( $key, $value );
            }
            return true;
        }
        return false;
    }

    /**
     * Get base
     * 
     * @return null
     */
    protected function get_base() {
        return null;
    }

    /**
     * Get permalink
     * 
     * @param string $language
     * @return string
     */
    public function get_permalink( $language ) {
        $postlink = $this->get_postlink( $language );
        if ( has_filter( 'msls_options_get_permalink' ) ) {
            $postlink = apply_filters(
                'msls_options_get_permalink',
                $postlink,
                $language
            );
        }
        return(
            '' != $postlink ?
            $postlink :
            site_url()
        );
    }

    /**
     * Get postlink
     * 
     * @param string $language
     * @return string
     */
    public function get_postlink( $language ) {
        return '';
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return site_url();
    }

    /**
     * Is excluded
     * 
     * @return bool
     */
    public function is_excluded() {
        return $this->has_value( 'exclude_current_blog' );
    }

    /**
     * Is content
     * 
     * @return bool
     */
    public function is_content_filter() {
        return $this->has_value( 'content_filter' );
    }

    /**
     * Get order
     * 
     * @return string
     */
    public function get_order() {
        return ( 
            $this->has_value( 'sort_by_description' ) ?
            'description' :
            'language'
        );
    }

    /**
     * Get url
     * 
     * @param string $dir
     * @return string
     */
    public function get_url( $dir ) {
        return esc_url( plugins_url( $dir, MSLS_PLUGIN__FILE__ ) );
    }

    /**
     * Get flag url
     * 
     * @param string $language
     * @param bool $plugin
     * @return string
     */
    public function get_flag_url( $language ) {
        $url = ( 
            !is_admin() && $this->has_value( 'image_url' ) ?
            $this->__get( 'image_url' ) :
            $this->get_url( 'flags' )
        );
        if ( 5 == strlen( $language ) )
            $language = strtolower( substr( $language, -2 ) );
        return sprintf( '%s/%s.png', $url, $language );
    }

    /**
     * Instance
     * 
     * @return MslsOptions
     */
    public static function instance() {
        $registry = MslsRegistry::singleton();
        $cls      = __CLASS__;
        $obj      = $registry->get_object( $cls );
        if ( is_null( $obj ) ) {
            $obj = new $cls;
            $registry->set_object( $cls, $obj );
        }
        return $obj;
    }

}

/**
 * MslsPostOptions
 * 
 * @package Msls
 */
class MslsPostOptions extends MslsOptions {

    /**
     * @var string
     */
    protected $sep = '_';

    /**
     * @var string
     */
    protected $autoload = 'no';

    /**
     * Get postlink
     * 
     * @param string $language
     * @return string
     */
    public function get_postlink( $language ) {
        if ( $this->has_value( $language ) ) {
            $id   = (int) $this->__get( $language );
            $post = get_post( $id );
            if ( !is_null( $post ) && 'publish' == $post->post_status ) {
                return get_permalink( $post );
            }
        }
        return '';
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_permalink( (int) $this->args[0] );
    }

}

/**
 * MslsTermOptions
 * 
 * @package Msls
 */
class MslsTaxOptions extends MslsOptions {

    /**
     * @var string
     */
    protected $sep = '_term_';

    /**
     * @var string
     */
    protected $autoload = 'no';

    /**
     * Factory method
     * 
     * @param int $id
     * @return MslsTaxOptions
     */
    public static function create( $id = 0 ) {
        if ( is_admin() ) {
            $id  = (int) $id;
            $obj = MslsContentTypes::create();
            if ( $obj->is_taxonomy() ) {
                switch ( $obj->get_request() ) {
                    case 'category':
                        return new MslsCategoryOptions( $id );
                        break;
                    case 'post_tag':
                        return new MslsTermOptions( $id );
                        break;
                    default:
                        return new MslsTaxOptions( $id );
                }
            }
        }
        else {
            global $wp_query;
            if ( is_category() ) {
                return new MslsCategoryOptions( $wp_query->get_queried_object_id() );
            } 
            elseif ( is_tag() ) {
                return new MslsTermOptions( $wp_query->get_queried_object_id() );
            }
            elseif ( is_tax() ) {
                return new MslsTaxOptions( $wp_query->get_queried_object_id() );
            }
        }
        return null;
    }

    /**
     * Get the queried taxonomy
     */
    protected function get_tax_query() {
        global $wp_query;
        return(
            isset( $wp_query->tax_query->queries[0]['taxonomy'] ) ?
            $wp_query->tax_query->queries[0]['taxonomy'] :
            ''
        );
    }

    /**
     * Check and correct URL
     * 
     * @param string $url
     * @return string
     */
    protected function check_url( $url ) {
        return( 
            empty( $url ) || !is_string( $url ) ?
            '' :
            $url
        );
    }
        
    /**
     * Get postlink
     *
     * @param string $language
     * @return string
     */
    public function get_postlink( $language ) {
        $url = '';
        if ( $this->has_value( $language ) ) {
            $taxonomy = $this->get_tax_query();
            $url = get_term_link(
                (int) $this->__get( $language ),
                $taxonomy
            );
            $url = $this->check_url( $url );
        }
        return $url;
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        $taxonomy = $this->get_tax_query();
        return(
            !empty( $taxonomy ) ?
            get_term_link( (int) $this->args[0], $taxonomy ) :
            null
        );
    }

}

/**
 * MslsTermOptions
 * 
 * @package Msls
 */
class MslsTermOptions extends MslsTaxOptions {

    /**
     * @var string
     */
    protected $base_option = 'tag_base';

    /**
     * @var string
     */
    protected $base_defined = 'tag';

    /**
     * Check and correct URL
     * 
     * @param string $url
     * @return string
     */
    protected function check_url( $url ) {
        if ( empty( $url ) || !is_string( $url ) ) return '';
        $base = $this->get_base();
        if ( $this->base != $base ) {
            $search  = '/' . $this->base . '/';
            $replace = '/' . $base . '/';
            $count   = 1;
            $url     = str_replace( $search, $replace, $url, $count );
        }
        return $url;
    }

    /**
     * Get base
     * 
     * @return string
     */
    protected function get_base() {
        $base = get_option( $this->base_option );
        return(
            !empty( $base ) ?
            $base:
            $this->base_defined
        );
    }

}

/**
 * MslsCategoryOptions
 * 
 * @package Msls
 */
class MslsCategoryOptions extends MslsTermOptions {

    /**
     * @var string
     */
    protected $base_option = 'category_base';

    /**
     * @var string
     */
    protected $base_defined = 'category';

}

/**
 * MslsQueryOptions
 * 
 * @package Msls
 */
class MslsQueryOptions extends MslsOptions {

    /**
     * Factory method
     * 
     * @return MslsQueryOptions
     */
    public static function create() {
        if ( is_day() ) {
            return new MslsDayOptions( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
        } 
        elseif ( is_month() ) {
            return new MslsMonthOptions( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
        }
        elseif ( is_year() ) {
            return new MslsYearOptions( get_query_var( 'year' ) );
        }
        elseif ( is_author() ) {
            global $wp_query;
            return new MslsAuthorOptions( $wp_query->get_queried_object_id() );
        }
        elseif ( is_post_type_archive() ) {
            return new MslsPostTypeOptions( get_query_var( 'post_type' ) );
        }
        return null;
    }

    /**
     * Get postlink
     * 
     * @param string $language
     * @return string
     */
    public function get_postlink( $language ) {
        return(
            $this->has_value( $language ) ?
            $this->get_current_link() :
            ''
        );
    }

}

/**
 * MslsDayOptions
 * 
 * @package Msls
 */
class MslsDayOptions extends MslsQueryOptions {

    /**
     * Check if the array has an non empty item which has $language as a key
     * 
     * @param string $language
     * @return bool
     */ 
    public function has_value( $language ) {
        if ( !isset( $this->arr[$language] ) ) {
            global $wpdb;
            $sql = sprintf(
                "SELECT count(ID) FROM {$wpdb->posts} WHERE DATE(post_date) = '%d-%02d-%02d' AND post_status = 'publish'",
                (int) $this->args[0],
                (int) $this->args[1],
                (int) $this->args[2]
            );
            $this->arr[$language] = $wpdb->get_var( $sql );
        }
        return (bool) $this->arr[$language];
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_day_link( $this->args[0], $this->args[1], $this->args[2] );
    }

}

/**
 * MslsMonthOptions
 * 
 * @package Msls
 */
class MslsMonthOptions extends MslsQueryOptions {

    /**
     * Check if the array has an non empty item which has $language as a key
     * 
     * @param string $language
     * @return bool
     */
    public function has_value( $language ) {
        if ( !isset( $this->arr[$language] ) ) {
            global $wpdb;
            $sql = sprintf(
                "SELECT count(ID) FROM {$wpdb->posts} WHERE YEAR(post_date) = %d AND MONTH(post_date) = %d AND post_status = 'publish'",
                (int) $this->args[0],
                (int) $this->args[1]
            );
            $this->arr[$language] = $wpdb->get_var( $sql );
        }
        return (bool) $this->arr[$language];
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_month_link( $this->args[0], $this->args[1] );
    }

}

/**
 * MslsYearOptions
 * 
 * @package Msls
 */
class MslsYearOptions extends MslsQueryOptions {

    /**
     * Check if the array has an non empty item which has $language as a key
     * 
     * @param string $language
     * @return bool
     */
    public function has_value( $language ) {
        if ( !isset( $this->arr[$language] ) ) {
            global $wpdb;
            $sql = sprintf(
                "SELECT count(ID) FROM {$wpdb->posts} WHERE YEAR(post_date) = %d AND post_status = 'publish'",
                (int) $this->args[0]
            );
            $this->arr[$language] = $wpdb->get_var( $sql );
        }
        return (bool) $this->arr[$language];
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_year_link( $this->args[0] );
    }

}

/**
 * MslsAuthorOptions
 * 
 * @package Msls
 */
class MslsAuthorOptions extends MslsQueryOptions {

    /**
     * Check if the array has an non empty item which has $language as a key
     * 
     * @param string $language
     * @return bool
     */
    public function has_value( $language ) {
        if ( !isset( $this->arr[$language] ) ) {
            global $wpdb;
            $sql = sprintf(
                "SELECT count(ID) FROM {$wpdb->posts} WHERE post_author = %d AND post_status = 'publish'",
                (int) $this->args[0]
            );
            $this->arr[$language] = $wpdb->get_var( $sql );
        }
        return (bool) $this->arr[$language];
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_author_posts_url( $this->args[0] );
    }

}

/**
 * MslsPostTypeOptions
 * 
 * @package Msls
 */
class MslsPostTypeOptions extends MslsQueryOptions {

    /**
     * Check if the array has an non empty item which has $language as a key
     * 
     * @param string $language
     * @return bool
     */
    public function has_value( $language ) {
        if ( !isset( $this->arr[$language] ) ) {
            $this->arr[$language] = get_post_type_object( $this->args[0] );
        }
        return (bool) $this->arr[$language];
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_post_type_archive_link( $this->args[0] );
    }

}

?>
