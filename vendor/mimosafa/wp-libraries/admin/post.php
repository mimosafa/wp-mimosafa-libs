<?php
namespace mimosafa\WP\Admin;

class Post {

	/**
	 * @var string
	 */
	protected $post_type;

	/**
	 * @var array
	 */
	protected $args;

	protected $meta_boxes_removed = [];

	protected $meta_boxes = [
		'normal'   => [],
		'advanced' => [],
		'side'     => [],
	];

	protected $hooks = [
		'dbx_post_advanced'          => [],
		'edit_form_top'              => [],
		'edit_form_before_permalink' => [],
		'edit_form_after_title'      => [],
		'edit_form_after_editor'     => [],
		# 'submit_box'                 => [],
		'edit_form_advanced'         => [],
		# 'dbx_post_sidebar'           => [],
	];

	protected static $instances = [];

	public static function getInstance( $post_type, $args = [] ) {
		if ( filter_var( $post_type ) ) {
			if ( ! isset( static::$instances[$post_type] ) ) {
				static::$instances[$post_type] = new static( $post_type, wp_parse_args( $args ) );
			}
			return static::$instances[$post_type];
		}
	}

	protected function __construct( $post_type, Array $args ) {
		$this->post_type = $post_type;
		$this->args = $args;
		did_action( 'admin_init' ) ? $this->admin_init() : add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function __set( $name, $value ) {
		$this->args[$name] = $value;
	}

	public function admin_init() {
		if ( post_type_exists( $this->post_type ) ) {
			add_action( 'load-post.php',     [ $this, 'parse_args' ] );
			add_action( 'load-post-new.php', [ $this, 'parse_args' ] );
		}
	}

	public function parse_args() {
		global $typenow;
		if ( $typenow === $this->post_type && $this->args ) {
			foreach ( $this->args as $key => $value ) {
				if     ( absint( $key ) ) :
				elseif ( $this->post_type_support( $key, $value ) ) :
				elseif ( $this->default_meta_box( $key, $value )  ) :
				elseif ( is_array( $value ) ) :
					$this->extra( array_merge( $value, [ 'id' => $key ] ) );
				endif;
			}
			$this->init();
		}
	}

	public function init() {
		add_action( 'add_meta_boxes_' . $this->post_type, [ $this, 'remove_meta_boxes' ], 9 );
		if ( $this->hooks = array_filter( $this->hooks ) ) {
			foreach ( array_keys( $this->hooks ) as $hook ) {
				add_action( $hook, [ $this, 'advanced_hooks' ] );
			}
		}
		if ( $this->meta_boxes = array_filter( $this->meta_boxes ) ) {
			add_action( 'add_meta_boxes_' . $this->post_type, [ $this, 'add_meta_boxes' ], 10 );
		}
	}

	public function advanced_hooks( \WP_Post $post ) {
		foreach ( $this->hooks as $hook => $array ) {
			if ( doing_action( $hook ) ) {
				unset( $this->hooks[$hook] );
				break;
			}
		}
		foreach ( $array as $args ) {
			if ( isset( $args['callback'] ) && is_callable( $args['callback'] ) ) {
				$func = $args['callback'];
				unset( $args['callback'] );
				call_user_func( $func, $post, $args );
			}
		}
	}

	public function add_meta_boxes( ) {
		foreach ( $this->meta_boxes as $context => $array ) {
			foreach ( $array as $args ) {
				if ( isset( $args['callback'] ) && is_callable( $args['callback'] ) ) {
					/*
					$id = $args['id'];
					$title = isset( $args['title'] ) && filter_var( $args['title'] ) ? esc_html( $args['title'] ) : esc_html( self::labelize( $id ) );
					$callback = $args['callback'];
					unset( $args['callback'] );
					$priority = isset( $args['priority'] ) && filter_var( $args['priority'] ) 
					add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
					*/
				}
			}
		}
	}

	protected function post_type_support( $key, $value ) {
		static $supports = [
			'post_title'     => 'title',
			'post_content'   => 'editor',
			'post_author'    => 'author',
			'post_thumbnail' => 'thumbnail',
			'post_excerpt'   => 'excerpt',
			'post_formats'   => 'post-formats',
		];
		if ( isset( $supports[$key] ) ) {
			if ( filter_var( $value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE ) !== null ) {
				if ( filter_var( $value, \FILTER_VALIDATE_BOOLEAN ) === false ) {
					remove_post_type_support( $this->post_type, $supports[$key] );
				}
				else {
					add_post_type_support( $this->post_type, $supports[$key] );
				}
				return true;
			}
		}
		return false;
	}

	protected function default_meta_box( $key, $value ) {
		static $boxes = [
			'post_submit' => [ 'submitdiv', null, 'side'   ],
			'post_name'   => [ 'slugdiv',   null, 'normal' ],
		];
		if ( isset( $boxes[$key] ) ) {
			if ( filter_var( $value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE ) === false ) {
				$this->meta_boxes_removed[] = $boxes[$key];
				return true;
			}
		}
		return false;
	}

	protected function extra( Array $args ) {
		/**
		 * @var string $context
		 */
		extract( $args );

		$context = isset( $context ) && filter_var( $context ) ? $context : 'advanced';
		if ( array_key_exists( $context, $this->meta_boxes ) ) {
			$this->meta_boxes[$context][] = $args;
			return;
		}
		else if ( array_key_exists( $context, $this->hooks ) ) {
			$this->hooks[$context][] = $args;
			return;
		}
	}

	public function remove_meta_boxes() {
		if ( $this->meta_boxes_removed ) {
			foreach ( $this->meta_boxes_removed as $box ) {
				call_user_func_array( 'remove_meta_box', $box );
			}
		}
	}

	protected static function labelize( $string ) {
		return ucwords( str_replace( [ '-', '_' ], ' ', $string ) );
	}

}
