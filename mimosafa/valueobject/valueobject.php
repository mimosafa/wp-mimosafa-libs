<?php
namespace mimosafa\WP\ValueObject;
use mimosafa\WP\Repository;

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
		add_action( 'init', [ $this, 'init' ], 20 );
	}

	public function init() {
		$this->regulate();
		if ( ! isset( static::$map[$this->repository_id] ) ) {
			static::$map[$this->repository_id] = [];
		}
		static::$map[$this->repository_id][$this->name] = $this->args;
	}

	/**
	 * Generator
	 *
	 * @access public
	 *
	 * @param  string       $name
	 * @param  string       $repository
	 * @param  array|string $args
	 * @return mimosafa\WP\ValueObjectValueObject
	 */
	public static function create( $repository_id, $name, $args = [] ) {
		if ( filter_var( $repository_id ) && filter_var( $name ) && $name === sanitize_key( $name ) ) {
			$args = wp_parse_args( $args, static::$defaults );
			return new static( $name, $repository_id, $args );
		}
	}

	public function to_array() {
		return array_merge( $this->args, [ 'name' => $this->name ] );
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
