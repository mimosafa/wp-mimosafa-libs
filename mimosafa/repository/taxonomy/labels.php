<?php
namespace mimosafa\WP\Repository\Taxonomy;
use mimosafa\WP\Repository\Repository as Repository;
/**
 * Taxonomy Labels Generator & Extension
 *
 * @access private
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Labels extends Repository\Labels {

	/**
	 * Common Labels
	 *
	 * @var array
	 */
	protected static $defaults = [
		'name'          => [ 'plural', [ '%s', 'taxonomy general name' ] ],
		'singular_name' => [ 'singular', [ '%s', 'taxonomy singular name' ] ],
		'search_items'  => [ 'plural', 'Search %s' ],
		'all_items'     => [ 'plural', 'All %s' ],
		'edit_item'     => [ 'singular', 'Edit %s' ],
		'view_item'     => [ 'singular', 'View %s' ],
		'update_item'   => [ 'singular', 'Update %s' ],
		'add_new_item'  => [ 'singular', 'Add New %s' ],
		'new_item_name' => [ 'singular', 'New %s Name' ],
		'not_found'     => [ 'plural', 'No %s found.' ],
		'no_terms'      => [ 'plural', 'No %s' ],
	];

	/**
	 * No Hierarchical Taxonomy Label
	 *
	 * @var array
	 */
	protected static $nohier = [
		'popular_items'              => [ 'singular', 'Popular %s' ],
		'separate_items_with_commas' => [ 'plural', 'Separate %s with commas' ],
		'add_or_remove_items'        => [ 'plural', 'Add or remove %s' ],
		'choose_from_most_used'      => [ 'plural', 'Choose from the most used %s' ],
	];

	/**
	 * Hierarchical Taxonomy Label
	 *
	 * @var array
	 */
	protected static $hier = [
		'parent_item'       => [ 'singular', 'Parent %s' ],
		'parent_item_colon' => [ 'singular', 'Parent %s:' ],
	];

	/**
	 * Initialize Taxonomy Registration Arguments for Labels
	 *
	 * @access public
	 *
	 * @param  string $taxonomy
	 * @param  array  &$args     # Registration Arguments for Post Type
	 */
	public static function init( $taxonomy, Array &$args ) {
		if ( ! isset( $args['labels'] ) ) {
			$args['labels'] = [];
		}
		if ( ! isset( $args['labels']['name'] ) || ! filter_var( $args['labels']['name'] ) ) {
			if ( ! isset( $args['label'] ) ) {
				$args['label'] = self::labelize( $taxonomy );
			}
			$args['labels']['name'] = $args['label'];
		}
		$plural = $args['labels']['name'];
		if ( ! isset( $args['labels']['singular_name'] ) || ! filter_var( $args['labels']['singular_name'] ) ) {
			$args['labels']['singular_name'] = $plural;
		}
		$singular = $args['labels']['singular_name'];
		$defaults = self::$defaults;
		if ( isset( $args['hierarchical'] ) && $args['hierarchical'] ) {
			$defaults = $defaults + self::$hier;
		}
		else {
			$defaults = $defaults + self::$nohier;
		}
		$args['labels'] = array_merge(
			self::generate_labels( $defaults, $plural, $singular ),
			$args['labels']
		);
		/**
		 * Extentions
		 */
		# $self = self::getInstance();
		# $self->expand_labels( $args['labels'] );
	}

	/**
	 * Generate Custom Labels
	 *
	 * @access protected
	 *
	 * @param  array  $formats
	 * @param  string $plural
	 * @param  string $singular
	 * @return array
	 */
	protected static function generate_labels( Array $formats, $plural, $singular ) {
		$labels = [];
		foreach ( $formats as $key => $f ) {
			$string = $f[0] === 'singular' ? $singular : $plural;
			if ( is_array( $f[1] ) ) {
				$format = _x( $f[1][0], $f[1][1], 'wp-mimosafa-libs' );
			} else {
				$format = __( $f[1], 'wp-mimosafa-libs' );
			}
			$labels[$key] = sprintf( $format, $string );
		}
		return $labels;
	}

}
