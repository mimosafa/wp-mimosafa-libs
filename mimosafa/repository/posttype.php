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
	 * Initialize Post Type
	 *
	 * @access protected
	 */
	protected function init_repository() {
		add_action( 'init', [ $this, 'register' ], 0 );
	}

	/**
	 * Register Post Type
	 *
	 * @access public
	 */
	public function register() {
		if ( ! $this->_builtin ) {
			Register::post_type( $this->name, $this->args );
		}
	}

	/**
	 * Attach Other Repositories for Post Type
	 *
	 * @access public
	 *
	 * @param  string|mimosafa\Repository\Repos $repository
	 * @param  mixed|null                       $args
	 * @return mimosafa\WP\Repository\PostType
	 */
	public function attach( $repository, $args = null ) {
		if ( $repository = self::getRepository( $repository ) ) {
			if ( $repository instanceof Taxonomy ) {
				/**
				 * With Taxonomy
				 */
				$repository->attach( $this->name );
			}
			else if ( $repository instanceof Role ) {
				/**
				 * With Role
				 */
			}
		}
		return $this;
	}

	/**
	 *
	 */
	public function init_caps() {
		//
	}

}
