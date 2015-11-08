<?php
namespace mimosafa\WP\Repository\Endpoint;
use mimosafa\WP\Repository\Repository as Repository;
/**
 * Taxonomy Regulation & Extension
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
class Registry extends Repository\Registry {

	/**
	 * Default Arguments
	 *
	 * @var array
	 */
	protected static $defaults = [
		//
	];

	/**
	 * Prototypes for Endpoint
	 *
	 * @var array
	 */
	protected static $prototypes = [
		//
	];

	/**
	 * Regulate Arguments for Registration
	 *
	 * @access public
	 *
	 * @param  string &$name # Taxonomy Name
	 * @param  array  &$args # Registration Arguments for Taxonomy
	 */
	public static function regulation( &$name, Array &$args ) {
		if ( ! isset( $args['places'] ) || ! is_int( $args['places'] ) || $args['places'] < 0 ) {
			$args['places'] = \EP_ROOT;
		}
		if ( self::$prefix ) {
			$_name = $name;
			$name = self::$prefix . $name;
			if ( ! isset( $args['rewrite'] ) || ! is_string( $args['rewrite'] ) || ! $args['rewrite'] ) {
				$args['rewrite'] = $_name;
			}
		}
	}

}
