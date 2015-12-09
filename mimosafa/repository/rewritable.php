<?php
namespace mimosafa\WP\Repository;
/**
 * Rewritable Repository Abstract Class
 *
 * @access public
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 *
 * @global WP         $wp
 * @global WP_Query   $wp_query
 * @global WP_Rewrite $wp_rewrite
 */
abstract class Rewritable extends Repository {

	protected static $instances = [];
	protected static $ids = [];

	abstract public function regulate();
	abstract public function register();
	abstract public function registered();

	protected function __construct( $name, $id, Array $args, $builtin ) {
		parent::__construct( $name, $id, $args, $builtin );
		add_action( 'init', [ $this, 'regulate' ],   1  );
		add_action( 'init', [ $this, 'register' ],   2  );
		add_action( 'init', [ $this, 'registered' ], 11 );
	}

}
