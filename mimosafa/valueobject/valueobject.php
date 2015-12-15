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
	protected function __construct( $name, $repository_id, $args ) {
		$this->name = $name;
		$this->repository_id = $repository_id;
		$this->args = $args;
		add_action( 'init', [ $this, 'regulate' ], 20 );
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
			return new static( $name, $repository_id, $args );
		}
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
