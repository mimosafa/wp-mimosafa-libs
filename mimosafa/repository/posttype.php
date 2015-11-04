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
	 * @var boolean
	 */
	protected $_builtin = false;

	/**
	 * Constructor
	 *
	 * @access protected
	 *
	 * @uses   mimosafa\WP\Repository\Repository::__construct()
	 *
	 * @param  string      $name
	 * @param  null|string $label
	 * @param  null|string $type
	 * @param  array       $args
	 */
	protected function __construct( $name, $label, $type, Array $args ) {
		if ( isset( self::$builtins[$name] ) ) {
			if ( self::$builtins[$name] === 'PostType' ) {
				$this->_builtin = true;
			}
			else {
				unset( self::$instances[$name] );
				return;
			}
		}
		parent::__construct( $name, $label, $type, $args );
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
