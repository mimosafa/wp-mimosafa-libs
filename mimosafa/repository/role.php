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
class Role extends Repository {

	/**
	 * Capabilities
	 *
	 * @var array
	 */
	private $caps = [];

	/**
	 * Initialize Role
	 *
	 * @access protected
	 *
	 * @uses REPOSITORIES_WRITABLE # for Force to Update WP_Roles
	 */
	protected function init_repository() {
		if ( is_super_admin() && defined( 'REPOSITORIES_WRITABLE' ) && REPOSITORIES_WRITABLE ) {
			add_action( 'init', [ $this, 'register' ], 0 );
		}
	}

	/**
	 * Register Role
	 *
	 * @access public
	 */
	public function register() {
		if ( ! $this->_builtin && ! $this->exists() ) {
			add_role( $this->name, $this->args['label'], $this->args['caps'] );
		}
	}

	//

	/**
	 * Attach Other Repositories for Role
	 *
	 * @access public
	 *
	 * @param  string|mimosafa\Repository\Repos $repository
	 * @param  string|array                     $caps
	 * @return mimosafa\WP\Repository\Role
	 */
	public function attach( $repository, $caps = null ) {
		if ( $repository = self::getRepository( $repository ) ) {
			$capsClass = __NAMESPACE__ . '\\' . $repository->whitch() . '\\Capabilities';
			if ( class_exists( $capsClass ) ) {
				// var_dump( $this );
			}
		}
	}

	/**
	 * Check Existing on DB, or Not
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function exists() {
		return isset( wp_roles()->role_objects[$this->name] );
	}

}
