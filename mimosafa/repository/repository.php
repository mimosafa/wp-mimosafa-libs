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
abstract class Repository implements RepositoryRepository {

	protected static $instances = [];
	protected static $ids = [];
	protected static $builtins = [];

	/**
	 * @var string
	 */
	protected $name;
	protected $id;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var boolean
	 */
	protected $_builtin;

	/**
	 * Constructor
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @param  string $id
	 * @param  array  $args
	 */
	protected function __construct( $name, $id, Array $args, $builtin ) {
		$this->name = $name;
		$this->id   = $id;
		$this->args = $args;
		$this->_builtin = $builtin;
		static::$ids[$name] = $id;
		static $done;
		if ( ! $done ) {
			\mimosafa\WP\Router::instance();
			$done = true;
		}
	}

	/**
	 * Parameter setter.
	 *
	 * @access public
	 */
	public function __set( $name, $value ) {
		$this->args[$name] = $value;
	}

	public function __get( $name ) {
		return in_array( $name, [ 'name', 'id' ] ) ? $this->$name : null;
	}

	/**
	 * Instance initializer
	 *
	 * @access public
	 *
	 * @param  string        $name
	 * @param  string        $id    Optional
	 * @param  array|string  $args  Optional
	 * @return mimosafa\WP\Repository\RepositoryInterface|null
	 */
	public static function init( $name, $id = null, $args = [] ) {
		if ( filter_var( $name ) && isset( static::$instances[$name] ) ) {
			/**
			 * If instance is already existing, Do nothing.
			 */
			return null;
		}
		$builtin = false;
		if ( isset( static::$builtins[$name] ) ) {
			/**
			 * If built-in, the ID is defined.
			 */
			$id = static::$builtins[$name];
			$builtin = true;
		}
		else if ( static::validateStrings( $name, $id ) ) {
			if ( in_array( $name, static::$ids, true ) ) {
				/**
				 * The name same as an existing ID is not allowed.
				 */
				return null;
			}
			if ( isset( static::$ids[$id] ) ) {
				/**
				 * The ID same as an existing name is not allowed
				 */
				return null;
			}
			if ( in_array( $id, static::$builtins, true ) ) {
				/**
				 * If not built-in, the ID same as an built-in is not allowed.
				 */
				return null;
			}
		}
		else {
			/**
			 * Invalid name|ID string
			 */
			return null;
		}
		$args = wp_parse_args( $args );
		return static::$instances[$name] = new static( $name, $id, $args, $builtin );
	}

	/**
	 * Existing Instance Getter
	 *
	 * @access public
	 *
	 * @param  string $name
	 * @return mimosafa\WP\Repository\RepositoryInterface|null
	 */
	public static function getInstance( $name ) {
		return filter_var( $name ) && isset( static::$instances[$name] ) ? static::$instances[$name] : null;
	}

	/**
	 * Validate Name|ID String
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @param  string &$id
	 * @return boolean
	 */
	protected static function validateStrings( $name, &$id ) {
		if ( $name = self::validateID( $name ) ) {
			$id = isset( $id ) && $id ? filter_var( $id, \FILTER_CALLBACK, [ 'options' => get_called_class() . '::validateID' ] ) : $name;
			return !! $id;
		}
		return false;
	}

	/**
	 * Validate ID String
	 *
	 * @access protected
	 *
	 * @param  string $id
	 * @return string|null
	 */
	protected static function validateID( $id ) {
		return ! is_array( $id ) && ! is_object( $id ) && $id && $id === sanitize_key( $id ) ? $id : null;
	}

	/**
	 * Labelize
	 *
	 * @access protected
	 *
	 * @param  string $string
	 * @return string
	 */
	protected static function labelize( $string ) {
		return ucwords( str_replace( [ '-', '_' ], ' ', $string ) );
	}

}
