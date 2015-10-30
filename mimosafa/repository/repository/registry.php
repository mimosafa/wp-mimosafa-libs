<?php
namespace mimosafa\WP\Repository\Repository;
/**
 * Abstract: Repository Regulation & Extension
 *
 * @access private
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
abstract class Registry {

	/**
	 * Repository Name Prefix
	 *
	 * @var string
	 */
	protected static $prefix;

	/**
	 * Organized Only Static Methods (Singleton Pattern)
	 *
	 * @access protected
	 */
	protected function __construct() {}
	protected static function getInstance() {
		static $instance;
		return $instance ?: $instance = new static();
	}

	/**
	 * Validate Repository's Name
	 *
	 * @access public
	 *
	 * @param  string $name
	 * @return string|null
	 */
	public static function validateName( $name ) {
		return is_string( $name ) && $name && $name === sanitize_key( $name ) ? $name : null;
	}

	public static function prefix( $string ) {
		if ( get_called_class() === __CLASS__ ) {
			self::$prefix = $string;
		}
	}

	/**
	 * Return Prototypes
	 *
	 * @access public
	 */
	public static function prototypes() {
		return static::$prototypes;
	}

	/**
	 * Regulate Arguments for Registration
	 *
	 * @access public
	 *
	 * @param  string &$name # Taxonomy Name
	 * @param  array  &$args # Registration Arguments for Taxonomy
	 */
	public static function arguments( &$name, Array &$args ) {
		$class = get_called_class();
		$func = [ substr( $class, 0, strrpos( $class, '\\' ) ) . '\\Labels', 'init' ];
		call_user_func_array( $func, [ &$name, &$args ] );
		$name = static::$prefix . $name;
	}

}
