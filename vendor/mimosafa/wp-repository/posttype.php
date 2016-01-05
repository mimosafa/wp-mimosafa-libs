<?php
namespace mimosafa\WP\Repository;

/**
 * Post type repository builder.
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 *
 * @uses mimosafa\WP\Repository\RewritableRepository
 */
class PostType extends RewritableRepository {

	/**
	 * WordPress built-in post types
	 *
	 * @var array
	 */
	protected static $builtins = [
		'post'       => 'post',
		'page'       => 'page',
		'attachment' => 'attachment',
	];

	/**
	 * Post type default arguments.
	 *
	 * @var array
	 */
	protected static $defaults = [
		'labels'               => [],
		'description'          => '',
		'public'               => false,
		'hierarchical'         => false,
		'exclude_from_search'  => null,
		'publicly_queryable'   => null,
		'show_ui'              => null,
		'show_in_menu'         => null,
		'show_in_nav_menus'    => null,
		'show_in_admin_bar'    => null,
		'menu_position'        => null,
		'menu_icon'            => null,
		'capability_type'      => null,
		'capabilities'         => [],
		'map_meta_cap'         => null,
		'supports'             => [ 'title', 'editor' ],
		'register_meta_box_cb' => null,
		'taxonomies'           => [],
		'has_archive'          => false,
		'rewrite'              => true,
		'query_var'            => true,
		'can_export'           => true,
		'delete_with_user'     => null
	];

	/**
	 * Rewrites default arguments.
	 *
	 * @var array
	 */
	private static $rewrite_defaults = [
		'slug'       => '',
		'with_front' => true,
		'pages'      => true,
		'feeds'      => null,
		'ep_mask'    => null
	];

	/**
	 * Label formats.
	 *
	 * @var array
	 */
	protected static $label_formats = [
		/**
		 * WordPress default post type labels.
		 */
		'name'                  => null,
		'singular_name'         => null,
		'add_new'               => null,
		'add_new_item'          => [ 'singular', 'Add New %s' ],
		'edit_item'             => [ 'singular', 'Edit %s' ],
		'new_item'              => [ 'singular', 'New %s' ],
		'view_item'             => [ 'singular', 'View %s' ],
		'search_items'          => [ 'plural', 'Search %s' ],
		'not_found'             => [ 'plural', 'No %s found.' ],
		'not_found_in_trash'    => [ 'plural', 'No %s found in Trash.' ],
		'all_items'             => [ 'plural', 'All %s' ],
		'parent_item_colon'     => [ 'singular', 'Parent %s:' ],
		'uploaded_to_this_item' => [ 'singular', 'Uploaded to this %s' ],
		'featured_image'        => null,
		'set_featured_image'    => [ 'featured_image', 'Set %s' ],
		'remove_featured_image' => [ 'featured_image', 'Remove %s' ],
		'use_featured_image'    => [ 'featured_image', 'Use as %s' ],
		'archives'              => [ 'singular', '%s Archives' ],
		'insert_into_item'      => [ 'singular', 'Insert into %s' ],
		'filter_items_list'     => [ 'plural', 'Filter %s list' ],
		'items_list_navigation' => [ 'plural', '%s list navigation' ],
		'items_list'            => [ 'plural', '%s list' ],
		/**
		 * Custom labels.
		 */
		'enter_title_here' => null,
	];

	/**
	 * Parameter setter.
	 *
	 * @access public
	 */
	public function __set( $name, $value ) {
		if ( preg_match( '/^support_(\w+)/', $name, $m ) ) {
			/**
			 * Post type's supports.
			 *
			 * @var array
			 */
			static $supports = [
				'title',
				'editor',
				'comments',
				'revisions',
				'trackbacks',
				'author',
				'excerpt',
				'page-attributes',
				'thumbnail',
				'custom-fields',
				'post-formats'
			];
			$support = str_replace( '_', '-', $m[1] );
			if ( in_array( $support, $supports, true ) ) {
				if ( ! is_array( $this->args['supports'] ) ) {
					if ( filter_var( $this->args['supports'], \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE ) === false ) {
						$this->args['supports'] = [];
					} else {
						$this->args['supports'] = preg_split( '/[\s,]+/', $this->args['supports'] );
					}
				}
				if ( filter_var( $value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE ) !== null ) {
					if ( $value && ! in_array( $support, $this->args['supports'], true ) ) {
						$this->args['supports'][] = $support;
					}
					else if ( ! $value && in_array( $support, $this->args['supports'], true ) ) {
						$i = array_search( $support, $this->args['supports'], true );
						unset( $this->args['supports'][$i] );
					}
				}
			}
		}
		else if ( preg_match( '/^rewrite_([a-z_]+)/', $name, $m ) ) {
			/**
			 * Post type rewrite arguments.
			 */
			$key = $m[1];
			if ( array_key_exists( $key, self::$rewrite_defaults ) ) {
				if ( ! is_array( $this->args['rewrite'] ) ) {
					$this->args['rewrite'] = [];
				}
				$this->args['rewrite'][$key] = $value;
			}
		}
		else if ( preg_match( '/^label_([a-z_]+)/', $name, $m ) ) {
			/**
			 * Post type labels.
			 */
			$key = $m[1];
			if ( array_key_exists( $key, self::$label_formats ) && filter_var( $value ) ) {
				if ( ! is_array( $this->args['labels'] ) ) {
					$this->args['labels'] = [];
				}
				$this->args['labels'][$key] = esc_html( $value );
			}
		}
		else {
			parent::__set( $name, $value );
		}
	}

	/**
	 * Validate $id string for post type.
	 *
	 * @access protected
	 *
	 * @param  string $id
	 * @return string|null
	 */
	protected static function validateID( $id ) {
		/**
		 * Post type name regulation.
		 *
		 * @see http://codex.wordpress.org/Function_Reference/register_post_type#Parameters
		 */
		return parent::validateID( $id ) && strlen( $id ) < 21 ? $id : null;
	}

	/**
	 * Regulate arguments for registration.
	 *
	 * @access public
	 */
	public function regulation() {
		if ( post_type_exists( $this->id ) ) {
			return;
		}
		/**
		 * @var array          $labels
		 * @var string         $description
		 * @var boolean        $public
		 * @var boolean        $hierarchical
		 * @var boolean        $exclude_from_search
		 * @var boolean        $publicly_queryable
		 * @var boolean        $show_ui
		 * @var boolean|string $show_in_menu
		 * @var boolean        $show_in_nav_menus
		 * @var boolean        $show_in_nav_menus
		 * @var boolean        $show_in_admin_bar
		 * @var int            $menu_position
		 * @var string         $menu_icon
		 * @var string|array   $capability_type
		 * @var array          $capabilities
		 * @var boolean        $map_meta_cap
		 * @var array          $supports
		 * @var callable       $register_meta_box_cb
		 * @var array          $taxonomies
		 * @var boolean        $has_archive
		 * @var boolean|array  $rewrite
		 * @var boolean|string $query_var
		 * @var boolean        $can_export
		 * @var boolean        $delete_with_user
		 */
		extract( $this->args );

		/**
		 * Regulate arguments.
		 */
		$public        = filter_var( $public,        \FILTER_VALIDATE_BOOLEAN );
		$hierarchical  = filter_var( $hierarchical,  \FILTER_VALIDATE_BOOLEAN );
		$has_archive   = filter_var( $has_archive,   \FILTER_VALIDATE_BOOLEAN );
		$can_export    = filter_var( $can_export,    \FILTER_VALIDATE_BOOLEAN );
		$menu_position = filter_var( $menu_position, \FILTER_VALIDATE_INT,     [ 'options' => [ 'default' => null ] ] );
		$can_export    = filter_var( $can_export,    \FILTER_VALIDATE_BOOLEAN, [ 'options' => [ 'default' => true ] ] );
		if ( isset( $exclude_from_search ) ) {
			$exclude_from_search = filter_var( $exclude_from_search, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $publicly_queryable ) ) {
			$publicly_queryable = filter_var( $publicly_queryable, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $show_in_nav_menus ) ) {
			$show_in_nav_menus = filter_var( $show_in_nav_menus, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $show_ui ) ) {
			$show_ui = filter_var( $show_ui, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $show_in_menu ) && ! ( is_string( $show_in_menu ) && preg_match( '/\w+(\.php){1}\w*/', $show_in_menu ) ) ) {
			$show_in_menu = filter_var( $show_in_menu, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $show_in_admin_bar ) ) {
			$show_in_admin_bar = filter_var( $show_in_admin_bar, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $map_meta_cap ) ) {
			$map_meta_cap = filter_var( $map_meta_cap, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $delete_with_user ) ) {
			$delete_with_user = filter_var( $delete_with_user, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( is_array( $description ) || is_object( $description ) ) {
			$description = '';
		}
		if ( ! is_array( $taxonomies ) ) {
			$taxonomies = [];
		}
		if ( isset( $register_meta_box_cb ) ) {
			if ( ! is_string( $register_meta_box_cb ) || ! preg_match( '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $register_meta_box_cb ) ) {
				$register_meta_box_cb = null;
			}
		}
		if ( $publicly_queryable || ( ! isset( $publicly_queryable ) && $public ) ) {
			if ( isset( $permalink_epmask ) ) {
				$permalink_epmask = filter_var( $permalink_epmask, \FILTER_VALIDATE_INT, [ 'options' => [ 'default' => null ] ] );
			}
			if ( filter_var( $rewrite, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE ) !== false ) {
				$rewrite = wp_parse_args( is_array( $rewrite ) ? $rewrite : [], self::$rewrite_defaults );
				if ( ! $rewrite['slug'] || ! is_string( $rewrite['slug'] ) ) {
					$rewrite['slug'] = $this->name !== $this->id ? $this->name : $this->id;
				}
				$rewrite['with_front'] = filter_var( $rewrite['with_front'], \FILTER_VALIDATE_BOOLEAN );
				$rewrite['pages']   = filter_var( $rewrite['pages'],   \FILTER_VALIDATE_BOOLEAN );
				$rewrite['feeds']   = filter_var( $rewrite['feeds'],   \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
				$rewrite['ep_mask'] = filter_var( $rewrite['ep_mask'], \FILTER_VALIDATE_INT, [ 'options' => [ 'default' => null ] ] );
			} else {
				$rewrite = false;
			}
			$query_var = filter_var( $query_var, \FILTER_VALIDATE_BOOLEAN );
		} else {
			$rewrite = $query_var = false;
		}
		if ( filter_var( $supports, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE ) !== false && $supports !== [] ) {
			if ( ! is_array( $supports ) ) {
				$supports = is_string( $supports ) ? preg_split( '/[\s,]+/', $supports ) : [];
			}
			$supports = array_unique( $supports );
		} else {
			$supports = false;
		}
		if ( ! $capability_type || is_object( $capability_type ) || ( is_array( $capability_type ) && count( $capability_type ) !== 2 ) ) {
			unset( $this->args['capability_type'] );
		}
		else if ( is_array( $capability_type ) ) {
			$capability_type = array_values( $capability_type );
			if ( ! is_string( $capability_type[0] ) || ! is_string( $capability_type[1] ) ) {
				unset( $this->args['capability_type'] );
			}
			else if ( $capability_type[0] === $capability_type[1] ) {
				unset( $this->args['capability_type'] );
			}
		}
		if ( ! is_array( $labels ) ) {
			$labels = [];
		}
		if ( ! isset( $labels['name'] ) || ! filter_var( $labels['name'] ) ) {
			$labels['name'] = isset( $label ) && filter_var( $label ) ? $label : self::labelize( $this->name );
		}
		if ( ! isset( $labels['singular_name'] ) || ! filter_var( $labels['singular_name'] ) ) {
			$labels['singular_name'] = $labels['name'];
		}
		self::generateLabels( $labels );

		/**
		 * Compact the regulated arguments.
		 */
		$this->args = compact( array_keys( $this->args ) );

		/**
		 * Cache for registration.
		 */
		self::$post_types[] = [ 'post_type' => $this->id, 'args' => $this->args ];
	}

	/**
	 * Create post type labels & expand labels.
	 *
	 * @access private
	 *
	 * @param  array &$labels
	 */
	private static function generateLabels( &$labels ) {
		$singular = $labels['singular_name'];
		$plural   = $labels['name'];
		$featured_image = isset( $labels['featured_image'] ) && filter_var( $labels['featured_image'] ) ? $labels['featured_image'] : null;
		foreach ( self::$label_formats as $key => $format ) {
			if ( ! isset( $labels[$key] ) || ! filter_var( $labels[$key] ) ) {
				if ( is_array( $format ) && ( $string = ${$format[0]} ) ) {
					$labels[$key] = esc_html( sprintf( __( $format[1], 'wp-mimosafa-libs' ), $string ) );
				}
			}
		}
	}

}
