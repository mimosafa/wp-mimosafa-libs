<?php
namespace mimosafa\WP\Object\ValueObject;

abstract class Post extends ValueObject {

	protected static $map = [];
	protected static $_repository_class = '\\mimosafa\\WP\\Object\\Repository\\PostType';

	protected function __construct( $name, $repository_id, Array $args ) {
		parent::__construct( $name, $repository_id, $args );
		add_action( 'init', [ $this, 'init' ], 21 );
	}

	public function init() {
		static $initialized;
		if ( ! $initialized ) {
			add_action( 'admin_init', [ $this, 'admin_init' ] );
			$initialized = true;
		}
	}

	public function admin_init() {
		if ( self::$map ) {
			global $pagenow, $typenow;
			if ( in_array( $pagenow, [ 'post.php', 'post-new.php' ] ) ) {
				if ( $typenow && $typenow !== $this->repository_id ) {
					return;
				}
				$this->admin_post( $typenow );
			}
		}
	}

	protected function admin_post( $typenow ) {
		if ( ! $typenow ) {
			if ( $post_id = filter_input( \INPUT_GET, 'post' ) ) {
				$typenow = get_post_type( $post_id );
			}
			if ( ! $typenow ) {
				return;
			}
		}
		if ( $show_uis = wp_list_filter( self::$map[$typenow], [ 'show_ui' => true ] ) ) {
			foreach ( $show_uis as $key => $args ) {
				$this->show_ui( $key, $args );
			}
		}
	}

	protected function show_ui( $id, $args ) {
		$admin = \mimosafa\WP\Admin\Post::instance( $this->repository_id );
		$admin->$id = array_merge( $args, [ 'callback' => [$this, 'meta_box_test' ] ] );
	}

	public function meta_box_test( \WP_Post $post, $box ) {
		var_dump( $box );
	}

}
