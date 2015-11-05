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
	public static function arguments( &$name, Array &$args ) {
		//
	}

}
