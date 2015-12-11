<?php
namespace mimosafa\WP\Admin\Page;
use mimosafa\WP\Admin\Element as El;

class Post {

	private $post_type;

	private $meta_boxes = [];

	private $forms = [];

	public function __construct( $post_type ) {
		if ( $post_type = filter_var( $post_type ) ) {
			$this->post_type = $post_type;
		}
		add_action( 'admin_init', [ $this, 'load' ] );
	}

	public function add( El\ElementInterface $el ) {
		if ( $el instanceof El\MetaBox ) {
			$this->meta_boxes[] = $el;
		}
		else if ( $el instanceof El\Form ) {
			$this->forms[] = $el;
		}
	}

	public function load() {
		if ( post_type_exists( $this->post_type ) ) {
			add_action( 'load-post.php', [ $this, 'init' ] );
			add_action( 'load-post-new.php', [ $this, 'init' ] );
		}
	}

	public function init() {
		if ( get_current_screen()->post_type === $this->post_type ) {
			if ( $this->meta_boxes ) {
				$this->init_meta_boxes();
			}
			if ( $this->forms ) {
				$this->init_forms();
			}
		}
	}

	private function init_meta_boxes() {
		foreach ( $this->meta_boxes as $meta_box ) {
			var_dump( $meta_box );
		}
	}

	private function init_forms() {
		foreach ( $this->forms as $form ) {
			var_dump( $form );
		}
	}

}
