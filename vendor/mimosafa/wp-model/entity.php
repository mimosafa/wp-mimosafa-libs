<?php
namespace mimosafa\WP\Model;

abstract class Entity extends Model {

	/**
	 * @var string
	 */
	protected $name;
	protected $id;
	protected $singular;
	protected $plural;

	/**
	 * Repository.
	 *
	 * @var string
	 */
	protected $repository;

	/**
	 * Repository arguments.
	 *
	 * @var array
	 */
	protected $repository_args = [];

	/**
	 * Entity instances (Singleton pattern).
	 *
	 * @var array
	 */
	protected static $entities = [];

	/**
	 * Instance getter (Singleton pattern).
	 *
	 * @access public
	 */
	public static function instance() {
		$name = strtolower( substr( get_called_class(), strripos( get_called_class(), '\\' ) + 1 ) );
		if ( ! isset( static::$entities[$name] ) ) {
			static::$entities[$name] = new static( $name );
		}
		return static::$entities[$name];
	}

	/**
	 * Constructor.
	 *
	 * @access protected
	 *
	 * @param  string $name
	 */
	protected function __construct( $name ) {
		if ( ! $this->has( 'repository' ) ) {
			throw new \Exception( '$repository property required for \'' . get_called_class() . '\' class.' );
		}
		$repository_class = 'mimosafa\WP\Repository\\' . str_replace( '_', '', $this->repository );
		if ( ! class_exists( $repository_class ) ) {
			throw new \Exception( 'Invalid $repository property is defined in \'' . get_called_class() . "'. {$repository_class} is not exist." );
		}
		$this->name = $this->has( 'name', 'string' ) ?: $name;
		$this->id = $this->has( 'id', 'string' ) ?: $this->name;
		$this->singular = $this->has( 'singular', 'string' ) ?: $this->name;
		$this->plural = $this->has( 'plural', 'string' ) && $this->plural !== $this->singular ? $this->plural : $this->name . 's';
		$repository_class::generate( $this->name, $this->id, $this->repository_args );
	}

}
