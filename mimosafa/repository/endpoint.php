<?php
namespace mimosafa\WP\Repository;
/**
 * Custom Endpoint Definition Class
 *
 * @access public
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Endpoint extends Repository {

	/**
	 * Initialize Endpoint
	 *
	 * @access public
	 */
	protected function init_repository() {
		if ( class_exists( 'mimosafa\WP\Rewrite\Endpoint' ) ) {
			\mimosafa\WP\Rewrite\Endpoint::register( $this->name, $this->args );
		}
	}

}
