<?php

/**
 * Output
 *
 * @package Msls
 * @subpackage Output
 */
class MslsOutput extends MslsMain {

    /**
     * Init
     * 
     * Just a placeholder
     */
    public static function init() { }

    /**
     * Get the output as array
     * 
     * @param string $display
     * @param bool frontend
     * @param bool $exists
     * @return array
     */
    public function get( $display, $filter = false, $exists = false ) {
        $arr   = array();
        $blogs = $this->blogs->get_filtered( $filter );
        if ( $blogs ) {
            $mydata = MslsOptions::create();
            $link   = MslsLink::create( $display );
            foreach ( $blogs as $blog ) {
                $language = $blog->get_language();
                $current  = ( $blog->userblog_id == $this->blogs->get_current_blog_id() );
                if ( $current ) {
                    $url = $mydata->get_current_link();
                }
                else {
                    switch_to_blog( $blog->userblog_id );
                    if ( 'MslsOptions' != get_class( $mydata ) && $exists && !$mydata->has_value( $language ) ) {
                        restore_current_blog();
                        continue;
                    }
                    $url = $mydata->get_permalink( $language );
                    restore_current_blog();
                }
                $link->txt = $blog->get_description();
                $link->src = $this->options->get_flag_url( $language );
                $link->alt = $language;
                if ( has_filter( 'msls_output_get' ) ) {
                    $arr[] = apply_filters(
                        'msls_output_get',
                        $url,
                        $link,
                        $current
                    );
                }
                else {
                    $arr[] = sprintf(
                        '<a href="%s" title="%s"%s>%s</a>',
                        $url,
                        $link->txt,
                        ( $current ? ' class="current_language"' : '' ),
                        $link
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * Returns a string when the object will be treated like a string
     * 
     * @see get_the_msls()
     * @return string
     */ 
    public function __toString() {
        $arr = $this->get(
            (int) $this->options->display,
            false,
            $this->options->has_value( 'only_with_translation' )
        );
        $str = '';
        if ( !empty( $arr ) ) {
            $str = $this->options->before_output .
                $this->options->before_item .
                implode(
                    $this->options->after_item . $this->options->before_item,
                    $arr
                ) .
                $this->options->after_item .
                $this->options->after_output;
        }
        return $str;
    }

}
