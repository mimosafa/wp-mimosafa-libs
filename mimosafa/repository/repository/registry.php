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

	/**
	 * Set Registry
	 *
	 * @access public
	 *
	 * @param  string
	 * @param  mixed
	 */
	public static function set( $key, $value ) {
		/**
		 * Common
		 */
		if ( get_called_class() === __CLASS__ ) {
			if ( $key === 'prefix' ) {
				if ( $value ) {
					if ( is_string( $value ) && $value === sanitize_key( $value ) && strlen( $value ) < 17 ) {
						self::$prefix = $value;
					}
				}
				else {
					self::$prefix = null;
				}
			}
		}
	}

	/**
	 * Reset Registry
	 *
	 * @access public
	 *
	 * @param  string $key
	 */
	public static function reset( $key ) {
		return static::set( $key, null );
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
		if ( ! isset( $args['label'] ) || ! filter_var( $args['label'] ) ) {
			$args['label'] = Labels::labelize( $name );
		}
		$name = static::$prefix . $name;
	}

}
