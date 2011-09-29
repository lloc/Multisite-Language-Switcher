<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Registry
 * 
 * @package Msls
 */
class MslsRegistry {

    /**
     * @access private
     * @var array $objects
     */
    private static $objects = array();

    /**
     * @access private
     * @var mixed $instance
     */
    private static $instance;

    /**
     * Constructor
     */
    final private function __construct() { }

    /**
     * Clone
     */
    final private function __clone() { }

    /**
     * @param mixed $key
     * @return mixed
     */
    private function get( $key ) {
        if ( isset( $this->objects[$key] ) ) {
            return $this->objects[$key];
        }
        return null;
    }

    /**
     * @param mixed $key
     * @param mixed $instance
     */
    private function set( $key, $instance ) {
        $this->objects[$key] = $instance;
    }

    /**
     * @return mixed
     */
    public static function singleton() {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
    /**
     * @param mixed $key
     * @return mixed
     */
    public static function get_object( $key ) {
        return self::singleton()->get( $key );
    }

    /**
     * @param mixed $key
     * @param mixed $instance
     */
    public static function set_object( $key, $instance ) {
        self::singleton()->set( $key, $instance );
    }

}

/**
 * Interface for classes which are to register in the MslsRegistry-instance
 *
 * get_called_class is just avalable in php >= 5.3 so I defined an interface here
 * 
 * @package Msls
 */
interface IMslsRegistryInstance {

    /**
     * @return object
     */
    public static function instance();

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
