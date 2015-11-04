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

	/**
	 * Attach Other Repositories for Post Type
	 *
	 * @access public
	 *
	 * @param  string|mimosafa\Repository\Repos $repository
	 * @return mimosafa\WP\Repository\PostType
	 */
	public function attach( $repository ) {
		if ( $repository = self::getRepos( $repository ) ) {
			if ( $repository instanceof Taxonomy ) {
				$repository->attach( $this->name );
			}
		}
	}

}
