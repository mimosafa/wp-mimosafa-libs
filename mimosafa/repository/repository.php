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

	/**
	 * Repository instances.
	 *
	 * @var array {
	 *     @type mimosafa\WP\Repository\RepositoryRepository ${$name}
	 * }
	 */
	protected static $instances = [];

	/**
	 * Cache of initialized repository ids & names.
	 *
	 * @var array {
	 *     @type string ${$id} # Repository name string.
	 * }
	 */
	protected static $ids = [];

	/**
	 * WordPress built-in repositories.
	 *
	 * @var array
	 */
	protected static $builtins = [];

	/**
	 * Default arguments.
	 *
	 * @var array
	 */
	protected static $defaults = [];

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var array
	 */
	protected $args = [];

	/**
	 * @var boolean
	 */
	protected $_builtin;

	protected static $gettable = [ 'name', 'id' ];

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
		$this->args = wp_parse_args( $args, static::$defaults );
		$this->_builtin = $builtin;
		static::$ids[$id] = $name;
	}

	/**
	 * Parameter setter.
	 *
	 * @access public
	 */
	public function __set( $name, $value ) {
		$this->args[$name] = $value;
	}

	/**
	 * Getter
	 *
	 * @access public
	 */
	public function __get( $name ) {
		return in_array( $name, static::$gettable ) ? $this->$name : null;
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
	public static function create( $name, $id = null, $args = [] ) {
		if ( ! filter_var( $name ) || isset( static::$instances[$name] ) ) {
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
			if ( isset( static::$ids[$name] ) ) {
				/**
				 * The name same as an existing ID is not allowed.
				 */
				return null;
			}
			if ( in_array( $id, static::$ids, true ) ) {
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
		if ( filter_var( $name ) ) {
			if ( isset( static::$instances[$name] ) ) {
				return static::$instances[$name];
			}
			if ( isset( static::$ids[$name] ) ) {
				return self::getInstance( static::$ids[$name] );
			}
		}
	}

	public static function getRepository( $repository ) {
		if ( is_string( $repository ) && $repository ) {
			if ( ! $repository = self::getInstance( $repository ) ) {
				$class = get_called_class();
				if ( isset( static::$builtins[$repository] ) ) {
					$repository = $class::create( $repository );
				}
				else if ( in_array( $repository, static::$builtins, true ) ) {
					$name = array_search( $repository, static::$builtins );
					$repository = $class::create( $name );
				}
			}
		}
		return is_object( $repository ) && $repository instanceof RepositoryRepository ? $repository : null;
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
