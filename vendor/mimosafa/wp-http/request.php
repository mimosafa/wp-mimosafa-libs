<?php
namespace mimosafa\WP\Http;
use mimosafa\WP\Repository;

class Request {

	public static function init() {
		static $instance;
		$instance ?: $instance = new self();
	}

	private function __construct() {
		add_action( 'parse_request', [ $this, 'parse_request' ] );
	}

	public function parse_request( \WP $wp ) {
		$query_vars = $wp->query_vars;
		if ( isset( $query_vars['post_type'] ) ) {
			$this->post_type_request( $query_vars );
		}
	}

	private function post_type_request( Array $query_vars ) {
		$post_type = $query_vars['post_type'];
		if ( $repo = Repository\PostType::getInstance( $post_type ) ) {
			var_dump( $repo );
		}
	}

}
