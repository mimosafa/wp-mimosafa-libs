<?php
namespace mimosafa\WP\Repository;
/**
 * Abstract Repository Definition Class
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
abstract class Repository implements Repos {

	/**
	 * Object Instances (Singleton Pattern)
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * @var array
	 */
	protected static $ids = [];

	/**
	 * Repository's Name (with Prefix)
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Arguments for Registration
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * @var boolean
	 */
	protected $_builtin = false;

	/**
	 * WordPress Built-in Repositories
	 *
	 * @var array
	 */
	protected static $builtins = [
		/**
		 * Post Types
		 */
		'post' => 'PostType',
		'page' => 'PostType',
		'attachment' => 'PostType',

		/**
		 * Taxonomies
		 */
		'category' => 'Taxonomy',
		'post_tag' => 'Taxonomy',
	];

	/**
	 * Black List for Repository Name
	 *
	 * @var array
	 */
	protected static $blacklist = [
		'revision', 'nav_menu_item',
		'link_category', 'post_format'
	];

	/**
	 * Abstract: Register Repository
	 *
	 * @access public
	 */
	abstract protected function init_repository();

	/**
	 * Constructor
	 *
	 * @access protected
	 *
	 * @uses   mimosafa\WP\Repository\{PostType|Taxonomy}\Registry
	 *
	 * @param  string      $name
	 * @param  null|string $label
	 * @param  null|string $type
	 * @param  array       $args
	 */
	protected function __construct( $name, $label, $type, Array $args ) {
		$id = $name;
		if ( in_array( $name, self::$blacklist, true ) ) {
			/**
			 * Black List
			 */
			unset( self::$instances[$id] );
			return;
		}
		if ( isset( self::$builtins[$name] ) ) {
			/**
			 * Built-ins
			 */
			$maybe = __NAMESPACE__ . '\\' . self::$builtins[$name];
			if ( $maybe !== get_called_class() ) {
				unset( self::$instances[$id] );
				return;
			}
			$this->_builtin = true;
		}
		else {
			/**
			 * Custom Repository
			 */
			if ( $label ) {
				$args['label'] = $label;
			}
			$registry = get_called_class() . '\\Registry';
			if ( $type ) {
				$args = array_merge( $args, $registry::prototypes()[$type] );
			}
			call_user_func_array( [ $registry, 'arguments' ], [ &$name, &$args ] );
			/**
			 * To Avoid Overwriting of Built-in/Existing Repository
			 *
			 * @var string $name # Repository's System Registered Name
			 */
			if ( in_array( $name, self::$ids, true ) || isset( self::$builtins[$name] ) || in_array( $name, self::$blacklist, true ) ) {
				unset( self::$instances[$id] );
				return;
			}
			$this->args = $args;
		}
		$this->name = $name;
		self::$ids[$name] = $id;
		/**
		 * Initialize Repository
		 */
		$this->init_repository();
	}

	/**
	 * Initialize & Return Instance
	 *
	 * @access public
	 *
	 * @uses   mimosafa\WP\Repository\{PostType|Taxonomy}\Registry
	 *
	 * @param  string $name
	 * @param  mixed  $label, $type, $args # Variable-length arguments
	 * @return object
	 */
	public static function init() {
		/**
		 * @var array {
		 *     @type string $name
		 *     @type string $label
		 *     @type string $type
		 *     @type array  $args
		 * }
		 */
		if ( $lists = func_get_args() ) {
			$name = $lists[0];
			if ( ! $instance = self::getInstance( $name ) ) {
				$class = get_called_class();
				$registry = $class . '\\Registry';
				if ( $name = filter_var( $name, \FILTER_CALLBACK, [ 'options' => $registry . '::validateName' ] ) ) {
					$n = count( $lists );
					$label = $type = null;
					$args  = [];
					if ( $n > 1 ) {
						for ( $i = 1; $i < 4; $i++ ) {
							if ( isset( $lists[$i] ) ) {
								if ( $lists[$i] ) {
									if ( is_string( $lists[$i] ) ) {
										if ( static::isPrototype( $lists[$i] ) ) {
											if ( ! $type ) {
												$type = $lists[$i];
											} else {
												// Error
											}
										}
										else {
											if ( ! $label ) {
												$label = $lists[$i];
											} else {
												// Error
											}
										}
									}
									else if ( is_array( $lists[$i] ) ) {
										if ( ! $args ) {
											$args = $lists[$i];
										} else {
											// Error
										}
									}
								}
								continue;
							}
							break;
						}
					}
					$instance = self::$instances[$name] = new $class( $name, $label, $type, $args );
				}
			}
			return $instance;
		}
		// Error
	}

	/**
	 * Return Initialized Instance
	 *
	 * @access public
	 *
	 * @param  string $name
	 * @return mimosafa\WP\Repository\Repos|null
	 */
	public static function getInstance( $name ) {
		if ( ( $name = filter_var( $name ) ) && isset( self::$instances[$name] ) ) {
			$class = get_called_class();
			if ( $class === __CLASS__ || self::$instances[$name] instanceof $class ) {
				return self::$instances[$name];
			}
		}
		return null;
	}

	/**
	 * @access protected
	 *
	 * @param  string|mimosafa\WP\Repository\Repos $repository
	 * @return mimosafa\WP\Repository\Repos|null
	 */
	protected static function getRepository( $repository ) {
		if ( is_string( $repository ) ) {
			if ( isset( self::$instances[$repository] ) ) {
				$repository = self::$instances[$repository];
			}
			else if ( isset( self::$ids[$repository] ) ) {
				$repository = self::$instances[self::$ids[$repository]];
			}
			else if ( isset( self::$builtins[$repository] ) ) {
				$class = __NAMESPACE__ . '\\' . self::$builtins[$repository];
				if ( class_exists( $class ) ) {
					$init = $class . '::init';
					$repository = call_user_func( $init, $repository );
				}
			}
		}
		return is_object( $repository ) && $repository instanceof Repos ? $repository : null;
	}

	/**
	 * @access protected
	 *
	 * @param  string  $var
	 * @return boolean
	 */
	protected static function isPrototype( $var ) {
		$registry = get_called_class() . '\\Registry';
		return isset( $registry::prototypes()[$var] );
	}

}
