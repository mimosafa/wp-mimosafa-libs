<?php
namespace mimosafa\WP\Object\Repository;
/**
 * Taxonomy Repository Class
 *
 * @access public
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Taxonomy extends Rewritable {

	/**
	 * WordPress built-in taxonomies.
	 *
	 * @var array
	 */
	protected static $builtins = [
		'category' => 'category',
		'tag'      => 'post_tag',
		'type'     => 'post_format',
	];

	/**
	 * Default arguments.
	 *
	 * @var array
	 */
	protected static $defaults = [
		'labels'                => [],
		'description'           => '',
		'public'                => true,
		'hierarchical'          => false,
		'show_ui'               => null,
		'show_in_menu'          => null,
		'show_in_nav_menus'     => null,
		'show_tagcloud'         => null,
		'show_in_quick_edit'    => null,
		'show_admin_column'     => false,
		'meta_box_cb'           => null,
		'capabilities'          => [],
		'rewrite'               => true,
		'query_var'             => true,
		'update_count_callback' => '',
	];
	private static $rewrite_defaults = [
		'slug'         => '',
		'with_front'   => true,
		'hierarchical' => false,
		'ep_mask'      => EP_NONE
	];

	protected static $labels_defaults = [
		'name'          => [ 'plural',   [ '%s', 'taxonomy general name' ] ],
		'singular_name' => [ 'singular', [ '%s', 'taxonomy singular name' ] ],
		'search_items'  => [ 'plural',   'Search %s' ],
		'all_items'     => [ 'plural',   'All %s' ],
		'edit_item'     => [ 'singular', 'Edit %s' ],
		'view_item'     => [ 'singular', 'View %s' ],
		'update_item'   => [ 'singular', 'Update %s' ],
		'add_new_item'  => [ 'singular', 'Add New %s' ],
		'new_item_name' => [ 'singular', 'New %s Name' ],
		'not_found'     => [ 'plural',   'No %s found.' ],
		'no_terms'      => [ 'plural',   'No %s' ],
	];
	/**
	 * No Hierarchical Taxonomy Label
	 *
	 * @var array
	 */
	protected static $labels_nohier = [
		'popular_items'              => [ 'singular', 'Popular %s' ],
		'separate_items_with_commas' => [ 'plural',   'Separate %s with commas' ],
		'add_or_remove_items'        => [ 'plural',   'Add or remove %s' ],
		'choose_from_most_used'      => [ 'plural',   'Choose from the most used %s' ],
	];
	/**
	 * Hierarchical Taxonomy Label
	 *
	 * @var array
	 */
	protected static $labels_hier = [
		'parent_item'       => [ 'singular', 'Parent %s' ],
		'parent_item_colon' => [ 'singular', 'Parent %s:' ],
	];

	/**
	 * Object types
	 *
	 * @var array
	 */
	protected $object_type = [];

	/**
	 * Constructor
	 *
	 * @access protected
	 *
	 * @param  string $name
	 * @param  string $id
	 * @param  array  $args
	 */
	protected function __construct( $name, $id, Array $args, $builtin ) {
		parent::__construct( $name, $id, $args, $builtin );
		if ( isset( $this->args['object_type'] ) ) {
			/**
			 * Object types
			 */
			if ( is_string( $this->args['object_type'] ) ) {
				$this->args['object_type'] = preg_split( '/[\s,]+/', $this->args['object_type'] );
			}
			if ( $this->args['object_type'] && is_array( $this->args['object_type'] ) ) {
				$this->object_type = array_values( $this->args['object_type'] );
			}
			unset( $this->args['object_type'] );
		}
	}

	/**
	 * Validate ID string for taxonomy name.
	 *
	 * @access protected
	 *
	 * @param  string $id
	 * @return string|null
	 */
	protected static function validateID( $id ) {
		if ( $id = parent::validateID( $id ) ) {
			/**
			 * Taxonomy name regulation.
			 *
			 * @see http://codex.wordpress.org/Function_Reference/register_taxonomy#Parameters
			 */
			if ( strlen( $id ) > 32 || @preg_match( '/[0-9]\-/', $id ) ) {
				$id = null;
			}
		}
		return $id;
	}

	/**
	 * Regulate arguments for registration.
	 *
	 * @access public
	 */
	public function regulate() {
		/**
		 * @var array          $labels
		 * @var string         $description
		 * @var boolean        $public
		 * @var boolean        $hierarchical
		 * @var boolean        $show_ui
		 * @var boolean        $show_in_menu
		 * @var boolean        $show_in_nav_menus
		 * @var boolean        $show_tagcloud
		 * @var boolean        $show_in_quick_edit
		 * @var boolean        $show_admin_column
		 * @var callable       $meta_box_cb
		  @var array          $capabilities
		 * @var boolean|array  $rewrite
		 * @var boolean|string $query_var
		 * @var callable       $update_count_callback
		 */
		extract( $this->args );

		/**
		 * Regulate arguments.
		 */
		$public            = filter_var( $public,            \FILTER_VALIDATE_BOOLEAN );
		$hierarchical      = filter_var( $hierarchical,      \FILTER_VALIDATE_BOOLEAN );
		$show_admin_column = filter_var( $show_admin_column, \FILTER_VALIDATE_BOOLEAN );
		if ( isset( $show_ui ) ) {
			$show_ui = filter_var( $show_ui, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $show_in_menu ) ) {
			$show_in_menu = filter_var( $show_in_menu, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $show_in_nav_menus ) ) {
			$show_in_nav_menus = filter_var( $show_in_nav_menus, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $show_tagcloud ) ) {
			$show_tagcloud = filter_var( $show_tagcloud, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( isset( $show_in_quick_edit ) ) {
			$show_in_quick_edit = filter_var( $show_in_quick_edit, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE );
		}
		if ( is_array( $description ) || is_object( $description ) ) {
			$description = '';
		}
		if ( isset( $meta_box_cb ) ) {
			if ( ! is_string( $meta_box_cb ) || ! preg_match( '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $meta_box_cb ) ) {
				$meta_box_cb = null;
			}
		}
		if ( $update_count_callback ) {
			if ( $update_count_callback !== '_update_post_term_count' || $update_count_callback !== '_update_generic_term_count' ) {
				$update_count_callback = '';
			}
		}
		if ( $public ) {
			if ( filter_var( $rewrite, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE ) !== false ) {
				$rewrite = wp_parse_args( is_array( $rewrite ) ? $rewrite : [], self::$rewrite_defaults );
				if ( ! $rewrite['slug'] || ! is_string( $rewrite['slug'] ) ) {
					$rewrite['slug'] = $this->name;
				}
				$rewrite['with_front']   = filter_var( $rewrite['with_front'],   \FILTER_VALIDATE_BOOLEAN );
				$rewrite['hierarchical'] = filter_var( $rewrite['hierarchical'], \FILTER_VALIDATE_BOOLEAN );
				$rewrite['ep_mask'] = filter_var( $rewrite['ep_mask'], \FILTER_VALIDATE_INT, [ 'options' => [ 'default' => EP_NONE ] ] );
			}
			if ( filter_var( $query_var, \FILTER_VALIDATE_BOOLEAN ) !== false ) {
				$query_var = $this->id;
			} else {
				$query_var = false;
			}
		} else {
			$rewrite = $query_var = false;
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
		self::createLabels( $labels, $hierarchical );

		/**
		 * Compact the regulated arguments.
		 */
		$this->args = compact( array_keys( $this->args ) );

		/**
		 * Cache for registration.
		 */
		self::$taxonomies[] = [ 'taxonomy' => $this->id, 'object_type' => $this->object_type, 'args' => $this->args ];
	}

	/**
	 * Create taxonomy labels.
	 *
	 * @access private
	 *
	 * @param  array   &$labels
	 * @param  boolean $hier
	 */
	private static function createLabels( &$labels, $hier ) {
		$formats = self::$labels_defaults;
		$formats = $formats + ( $hier ? self::$labels_hier : self::$labels_hier );
		foreach ( $formats as $key => $format ) {
			if ( ! isset( $labels[$key] ) || ! filter_var( $labels[$key] ) ) {
				$str = '';
				if ( is_array( $format ) ) {
					if ( $format[0] === 'plural' ) {
						$str = $labels['name'];
					}
					else if ( $format[0] === 'singular' ) {
						$str = $labels['singular_name'];
					}
					if ( is_array( $format[1] ) ) {
						$f = _x( $format[1][0], $format[1][1], 'wp-mimosafa-libs' );
					} else {
						$f = __( $format[1], 'wp-mimosafa-libs' );
					}
					if ( $str && $f ) {
						$labels[$key] = sprintf( $f, $str );
					}
				}
			}
		}
	}

}
