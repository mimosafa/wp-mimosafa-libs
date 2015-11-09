<?php
namespace mimosafa\WP\Repository\Role;
use mimosafa\WP\Repository\Repository as Repository;
/**
 * Role Regulation & Extension
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
	 * Return Prototypes
	 *
	 * @access public
	 */
	public static function prototypes() {
		return wp_roles()->roles;
	}

	/**
	 * Regulate Arguments for Registration
	 *
	 * @access public
	 *
	 * @param  string &$name # Post Type Name
	 * @param  array  &$args # Registration Arguments for Post Type
	 */
	public static function regulation( &$name, Array &$args ) {
		$_name = $name;
		parent::regulation( $name, $args );
		if ( ! isset( $args['capabilities'] ) || ! is_array( $args['capabilities'] ) ) {
			$args['capabilities'] = [];
		}
		$args['name'] = $name;
	}

	/**
	 * Whole Roles Reset, and ReConstruct
	 *
	 * @access public
	 */
	public static function reconstruct() {
		//
	}

}
