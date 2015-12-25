<?php
namespace mimosafa\WP\Object\ValueObject;

abstract class ValueObject implements ValueObjectValueObject {

	/**
	 * Value object name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Repository value object belongs.
	 *
	 * @var string
	 */
	protected $repository_id;

	/**
	 * @var array
	 */
	protected $args;

	protected static $defaults = [];
	protected static $map = [];
	protected static $_repository_class = null;

	abstract public function regulate();

	/**
	 * Constructor
	 *
	 * @access protected
	 *
	 * @param  string       $name
	 * @param  string       $repository_id
	 * @param  array|string $args
	 */
	protected function __construct( $name, $repository_id, Array $args ) {
		$this->name = $name;
		$this->repository_id = $repository_id;
		$this->args = $args;
		add_action( 'init', [ $this, 'map' ], 20 );
	}

	public function map() {
		$this->regulate();
		if ( ! isset( static::$map[$this->repository_id] ) ) {
			static::$map[$this->repository_id] = [];
		}
		static::$map[$this->repository_id][$this->name] = array_merge( $this->args, [ 'name' => $this->name ] );
	}

	/**
	 * Generator
	 *
	 * @access public
	 *
	 * @param  string                                                    $name
	 * @param  mimosafa\WP\Object\Repository\RepositoryRepository|string $repository
	 * @param  array|string                                              $args
	 * @return mimosafa\WP\Object\ValueObjectValueObject
	 */
	public static function create( $repository, $name, $args = [] ) {
		if ( filter_var( $name ) && $name === sanitize_key( $name ) ) {
			$args = wp_parse_args( $args, static::$defaults );
			if ( is_object( $repository ) ) {
				if ( isset( static::$_repository_class) && $repository instanceof static::$_repository_class ) {
					return new static( $name, $repository->id, $args );
				}
			}
			else if ( is_string( $repository ) && $repository && $repository_class = static::$_repository_class ) {
				if ( $instance = $repository_class::getRepository( $repository ) ) {
					return $instance->add_value_object( $name, $args );
				}
			}
		}
		return null;
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
