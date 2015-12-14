<?php
namespace mimosafa\WP;

class Router {

	private $query_vars = [];

	public static function instance() {
		static $instance;
		return $instance ?: $instance = new self();
	}

	private function __construct() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_init() {
		add_action( 'load-edit.php', [ $this, 'load_post' ] );
		add_action( 'load-post.php', [ $this, 'load_post' ] );
		add_action( 'load-post-new.php', [ $this, 'load_post' ] );
	}

	public function load_post() {
		global $pagenow, $typenow;
		if ( $pagenow === 'edit.php' ) {
			Admin\Page\Edit::load( $typenow );
		}
		else {
			Admin\Page\Post::load( $typenow );
		}
	}

}
