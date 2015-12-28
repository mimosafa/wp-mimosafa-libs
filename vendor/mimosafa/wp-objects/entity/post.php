<?php
namespace mimosafa\WP\Object\Entity;

class Post {

	protected static $repositoryClass = 'mimosafa\\WP\\Object\\Repository\\PostType';

	protected $name;
	protected $id       = '';
	protected $singular = '';
	protected $plural   = '';

	protected $value_objects = [];

	public function __construct() {
		if ( ! filter_var( $name ) ) {
			throw new \Exception( 'Invalid Argument.' );
		}
		$this->init_repository();
	}

}
