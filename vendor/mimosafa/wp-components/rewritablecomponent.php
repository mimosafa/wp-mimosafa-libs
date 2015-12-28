<?php
namespace mimosafa\WP\Component;

/**
 * Abstract rewritable component class.
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 *
 * @uses mimosafa\WP\Component\Component
 */
abstract class RewritableComponent extends Component {

	/**
	 * Instances, whole post types & taxonomies created by this class.
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Post types & Taxonomies `id` <=> `name` map.
	 *
	 * @var array
	 */
	protected static $ids = [];

	/**
	 * Abstract method: Regulate arguments for registration.
	 *
	 * @access public
	 */
	abstract public function regulation();

	/**
	 * Constructor.
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @param  string $id
	 * @param  array  $args
	 * @return void
	 */
	protected function __construct( $name, $id, Array $args, $builtin ) {
		parent::__construct( $name, $id, $args, $builtin );
		/**
		 * Regulate arguments.
		 */
		add_action( 'init', [ &$this, 'regulation' ], 0 );
		/**
		 * Flag for action at only once.
		 *
		 * @var boolean
		 */
		static $done = false;
		if ( ! $done ) {
			add_action( 'init', [ &$this, 'register_taxonomies' ], 1 );
			add_action( 'init', [ &$this, 'register_post_types' ], 1 );
			$done = true;
		}
	}

	/**
	 * Register taxonomy components.
	 *
	 * @access public
	 */
	public function register_taxonomies() {
		//
	}

	/**
	 * Register post type components.
	 *
	 * @access public
	 */
	public function register_post_types() {
		//
	}

}
