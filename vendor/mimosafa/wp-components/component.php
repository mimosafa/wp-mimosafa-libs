<?php
namespace mimosafa\WP\Component;

/**
 * Abstract component class.
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
abstract class Component {

	/**
	 * {Post type|Taxonomy|Role} alias name.
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
	protected $id;

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
	 * {Post types|Taxonomies|Roles} `id` <=> `name` map.
	 *
	 * @var array
	 */
	protected static $ids = [];

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
	 * @param  string $id
	 * @param  array  $args
	 * @return void
	 */
	protected function __construct( $name, $id, Array $args, $builtin ) {
		$this->name = $name;
		$this->id   = $id;
		$this->args = $args;
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
	 * Post type generator.
	 *
	 * @access public
	 *
	 * @param  string       $name
	 * @param  string       $id
	 * @param  array|string $args
	 * @return mimosafa\WP\CComponent\PostType|null
	 */
	public static function generate( $name, $id = '', $args = [] ) {
		if ( ! filter_var( $name ) ) {
			/**
			 * $name must be valid string.
			 */
			return null;
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
			$id = static::$builtins[$name];
			$builtin = true;
		}
		else if ( static::validateStrings( $name, $id ) ) {
			if ( isset( static::$ids[$name] ) ) {
				/**
				 * The $name same as an existing $id is not allowed.
				 */
				return null;
			}
			if ( in_array( $id, static::$ids, true ) ) {
				/**
				 * The $id same as an existing $name is not allowed.
				 */
				return null;
			}
			if ( in_array( $id, static::$builtins, true ) ) {
				/**
				 * If {post type|taxonomy|role} is not built-in,
				 * the $id same as an built-in one is not allowed.
				 */
				return null;
			}
		}
		else {
			/**
			 * Invalid $name|$id string.
			 */
			return null;
		}
		$args = wp_parse_args( $args, static::$defaults );
		return static::$instances[$name] = new static( $name, $id, $args, $builtin );
	}

	/**
	 * Validate $name|$id strings.
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @param  string &$id
	 * @return boolean
	 */
	protected static function validateStrings( $name, &$id ) {
		if ( $name = self::validateID( $name ) ) {
			$options = [ 'options' => get_called_class() . '::validateID' ];
			$id = isset( $id ) && $id ? filter_var( $id, \FILTER_CALLBACK, $options ) : $name;
			return !! $id;
		}
		return false;
	}

	/**
	 * Validate $id string.
	 *
	 * @access protected
	 *
	 * @param  string $id
	 * @return string|null
	 */
	protected static function validateID( $id ) {
		return filter_var( $id ) && $id === sanitize_key( $id ) ? $id : null;
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
