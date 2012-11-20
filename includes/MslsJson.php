<?php
/**
 * MslsJson
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.9
 */

/**
 * Container for array which will used in JavaScript as JSON object
 * @package Msls
 * @subpackage Main
 */
class MslsJson {

    /**
     * @var array
     */
    protected $arr = array();

    /**
     * add
     * @param int $value
     * @param string $label
     * @return MslsJson
     */
    public function add( $value, $label ) {
        $this->arr[] = array( 'value' => (int) $value, 'label' => (string) $label );
        return $this;
    }

    /**
     * compare
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compare( array $a, array $b ) {
        return strnatcmp( $a['label'], $b['label'] );
    }

    /**
     * __toString
     * 
     * Sort the array container by labele and convert it to a JSON-ified
     * string
     * @return string
     */ 
    public function __toString() {
        usort( $this->arr, array( __CLASS__, 'compare' ) );
        return json_encode( $this->arr );
    }

}
