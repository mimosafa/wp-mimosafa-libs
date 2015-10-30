<?php
namespace mimosafa\WP\Repository;
/**
 * Post Type Definition Class
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
class PostType extends Repository {

	/**
	 * Register Post Type
	 *
	 * @access public
	 */
	public function register() {
		Register::post_type( $this->name, $this->args );
	}

}
