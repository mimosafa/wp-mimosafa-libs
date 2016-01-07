<?php
namespace mimosafa\WP\Repository;

/**
 * Abstract repository class.
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
abstract class Repository {

	/**
	 * {Post type|Taxonomy|Role} name.
	 * Use as {post type|taxonomy} rerite slug.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * {Post type|Taxonomy|Role} real name in WordPress.
	 *
	 * @var string
	 */
	protected $alias;

	/**
	 * Arguments for {post type|taxonomy|role} registration.
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * WordPress built-in {post type|taxonomy|role}, OR not.
	 *
	 * @var boolean
	 */
	protected $_builtin;

	/**
	 * Instances, whole {post types|taxonomies|roles} created by this class.
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * {Post types|Taxonomies|Roles} `alias` <=> `name` map.
	 *
	 * @var array
	 */
	protected static $names = [];

	/**
	 * WordPress built-in {post types|taxonomies|roles}.
	 *
	 * @var array
	 */
	protected static $builtins = [];

	/**
	 * {Post type|Taxonomy|Role} default arguments.
	 *
	 * @var array
	 */
	protected static $defaults = [];

	/**
	 * Constructor.
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @param  string $alias
	 * @param  array  $args
	 * @return void
	 */
	protected function __construct( $name, $alias, Array $args, $builtin ) {
		$this->name = $name;
		$this->alias   = $alias;
		$this->args = $args;
		$this->_builtin = $builtin;
		static::$names[$alias] = $name;
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
	 * Post type generator.
	 *
	 * @access public
	 *
	 * @param  string       $name
	 * @param  string       $alias
	 * @param  array|string $args
	 * @return mimosafa\WP\CComponent\PostType|null
	 */
	public static function init( $name, $alias = '', $args = [] ) {
		if ( ! filter_var( $name ) ) {
			/**
			 * $name must be valid string.
			 */
			throw new \Exception( '$name is required and must be valid string.' );
		}
		if ( isset( static::$instances[$name] ) ) {
			/**
			 * If instance is already existing, return null.
			 */
			return null;
		}
		/**
		 * Built-in post type OR not.
		 *
		 * @var boolean
		 */
		$builtin = false;
		if ( isset( static::$builtins[$name] ) ) {
			/**
			 * If built-in post type, $id is fixed.
			 */
			$alias = static::$builtins[$name];
			$builtin = true;
		}
		else if ( static::validateStrings( $name, $alias ) ) {
			if ( isset( static::$names[$name] ) ) {
				/**
				 * The $name same as an existing $alias is not allowed.
				 */
				return null;
			}
			if ( in_array( $alias, static::$names, true ) ) {
				/**
				 * The $alias same as an existing $name is not allowed.
				 */
				return null;
			}
			if ( in_array( $alias, static::$builtins, true ) ) {
				/**
				 * If {post type|taxonomy|role} is not built-in,
				 * the $alias same as an built-in one is not allowed.
				 */
				return null;
			}
		}
		else {
			/**
			 * Invalid $name|$alias string.
			 */
			return null;
		}
		$args = wp_parse_args( $args, static::$defaults );
		return static::$instances[$name] = new static( $name, $alias, $args, $builtin );
	}

	/**
	 * Instance getter.
	 *
	 * @access public
	 */
	public static function getInstance( $var ) {
		if ( isset( static::$instances[$var] ) ) {
			return static::$instances[$var];
		}
		if ( isset( static::$names[$var] ) ) {
			$name = static::$names[$var];
			return static::$instances[$name];
		}
		return false;
	}

	/**
	 * Validate $name|$alias strings.
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @param  string &$alias
	 * @return boolean
	 */
	protected static function validateStrings( $name, &$alias ) {
		if ( $name = self::validateAlias( $name ) ) {
			$options = [ 'options' => get_called_class() . '::validateAlias' ];
			$alias = isset( $alias ) && $alias ? filter_var( $alias, \FILTER_CALLBACK, $options ) : $name;
			return !! $alias;
		}
		return false;
	}

	/**
	 * Validate $alias string.
	 *
	 * @access protected
	 *
	 * @param  string $alias
	 * @return string|null
	 */
	protected static function validateAlias( $alias ) {
		return filter_var( $alias ) && $alias === sanitize_key( $alias ) ? $alias : null;
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

	/**
	 * Create repositories interface.
	 *
	 * @access public
	 */
	public static function parseJSON( $text ) {
		if ( get_called_class() !== __CLASS__ ) {
			return;
		}
		$array = json_decode( $text, true );
		static::parseArray( $array );
	}
	public static function parseArray( Array $array ) {
		if ( get_called_class() !== __CLASS__ ) {
			return;
		}
		foreach ( $array as $name => $args ) {
			if ( ! isset( $args['repository'] ) ) {
				continue;
			}
			if ( $args['repository'] === 'post_type' ) {
				$class = __NAMESPACE__ . '\\PostType';
			}
			else if ( $args['repository'] === 'taxonomy' ) {
				$class = __NAMESPACE__ . '\\Taxonomy';
			}
			else {
				continue;
			}
			$alias = isset( $args['alias'] ) && filter_var( $args['alias'] ) ? $args['alias'] : $name;
			$repository_args = isset( $args['arguments'] ) ? $args['arguments'] : [];
			$class::init( $name, $alias, $repository_args );
		}
	}

}
