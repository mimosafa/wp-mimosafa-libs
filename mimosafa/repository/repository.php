<?php
namespace mimosafa\WP\Repository;
/**
 * Abstract Repository Definition Class
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
abstract class Repository {

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
	 * == Abstract Properties ==
	 */
	protected static $instances;  # Object instances (Singleton Pattern)
	protected static $prototypes; # Repository's Fixed(Defined) Formats

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
		$this->name = $name;
		if ( $label ) {
			$this->args['label'] = $this->labels['name'] = $label;
		}
		if ( $type ) {
			$this->args = array_merge( $this->args, static::$prototypes[$type] );
		}
		if ( $args && $args !== array_values( $args ) ) {
			$class = get_called_class();
			foreach ( $args as $key => $val ) {
				if ( method_exists( $class, $key ) ) {
					$this->$key( $val );
				}
			}
		}
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
			if ( ! $instance = static::getInstance( $name ) ) {
				$class = get_called_class();
				if ( $name = filter_var( $name, \FILTER_CALLBACK, [ 'options' => $class . '::validateName' ] ) ) {
					$n = count( $lists );
					$label = $type = null;
					$args  = [];
					if ( $n > 1 && $n < 5 ) {
						for ( $i = 1; $i < 4; $i++ ) { 
							if ( isset( $lists[$i] ) ) {
								if ( $lists[$i] ) {
									if ( is_string( $lists[$i] ) ) {
										if ( isset( static::$prototypes[$lists[$i]] ) ) {
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
					$instance = static::$instances[$name] = new $class( $name, $label, $type, $args );
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
	 * @return object
	 */
	public static function getInstance( $name ) {
		if ( $name = filter_var( $name ) ) {
			return isset( static::$instances[$name] ) ? static::$instances[$name] : null;
		}
	}

	/**
	 * Validate Repository's Name
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @return string|null
	 */
	protected static function validateName( $name ) {
		return is_string( $name ) && $name && $name === sanitize_key( $name ) ? $name : null;
	}

}
