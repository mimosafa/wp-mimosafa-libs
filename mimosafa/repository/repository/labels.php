<?php
namespace mimosafa\WP\Repository\Repository;
/**
 * Abstract Repository Labels Generator & Extension
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
abstract class Labels {

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
	 * Labelize Method
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @return string
	 */
	protected static function labelize( $name ) {
		return ucwords( str_replace( [ '-', '_' ], ' ', $name ) );
	}

}
