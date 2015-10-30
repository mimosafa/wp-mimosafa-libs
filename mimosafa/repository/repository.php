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
abstract class Repository {

	/**
	 * Object Instances (Singleton Pattern)
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Repository's Name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Labels for Registration
	 *
	 * @var array
	 */
	protected $labels = [];

	/**
	 * Arguments for Registration
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * Abstract: Repository's Fixed(Defined) Formats
	 *
	 * @var    array
	 * @access protected
	 */
	# protected static $prototypes;

	/**
	 * Abstract: Register Repository
	 *
	 * @access public
	 */
	abstract public function register();

	/**
	 * Constructor
	 *
	 * @access protected
	 *
	 * @param  string      $name
	 * @param  null|string $label
	 * @param  null|string $type
	 * @param  array       $args
	 */
	protected function __construct( $name, $label, $type, Array $args ) {
		if ( $label ) {
			$args['label'] = $label;
		}
		$registry = get_called_class() . '\\Registry';
		if ( $type ) {
			$this->args = array_merge( $this->args, $registry::prototypes()[$type] );
		}
		call_user_func_array( [ $registry, 'arguments' ], [ &$name, &$args ] );
		$this->args = array_merge( $this->args, $args );
		$this->name = $name;
		add_action( 'init', [ $this, 'register' ], 0 );
	}

	/**
	 * Initialize & Return Instance
	 *
	 * @access public
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
	 * @return object|null
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

	protected static function isPrototype( $var ) {
		$registry = get_called_class() . '\\Registry';
		return isset( $registry::prototypes()[$var] );
	}

}
