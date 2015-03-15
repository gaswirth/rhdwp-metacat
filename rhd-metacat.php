<?php
/**
 * Plugin Name: MetaCat
 * Description: Adds custom meta data to categories
 * Author: Roundhouse Designs
 * Author URI: http://roundhouse-designs.com
 * Version: 0.1
**/

// Adapted from http://php.quicoto.com/add-metadata-categories-wordpress/

// Sample front-end retrieval:
/*
$cats = get_categories();

foreach ( $cats as $cat ) {
	$catmeta = get_option( "taxonomy_" . $cat->term_id );
	if ( !$catmeta['excluded'] || $catmeta['excluded'] === 0 ) {
		echo	'<li class="cat-item">' .
					'<a href="' . get_category_link( $cat->cat_ID ) . '">' . $cat->name . '</a>' .
				'</li>';
	}
}
*/

/* ==========================================================================
	Plugin Setup
   ========================================================================== */

define( 'RHD_METACAT_DIR', plugin_dir_url(__FILE__) );


/* ==========================================================================
	MetaCat
   ========================================================================== */

function rhd_edit_excluded_category_field( $term ) {
	$term_id = $term->term_id;
	$term_meta = get_option( "taxonomy_$term_id" ); ?>

	<tr class="form-field">
		<th scope="row">
			<label for="term_meta[excluded]"><?php echo _e( 'Exclude from Dropdown' ) ?></label>
			<td>
				<select name="term_meta[excluded]" id="term_meta[excluded]">
					<option value="0" <?=( $term_meta['excluded'] == 0) ? 'selected': ''?>><?php echo _e( 'No' ); ?></option>
					<option value="1" <?=( $term_meta['excluded'] == 1) ? 'selected': ''?>><?php echo _e( 'Yes' ); ?></option>
				</select>
			</td>
		</th>
	</tr>
	<?php
}
add_action( 'category_edit_form_fields', 'rhd_edit_excluded_category_field' );


function rhd_create_excluded_category_field( $term ) {
	$term_id = $term->term_id;
	$term_meta = get_option( "taxonomy_$term_id" ); ?>

	<div class="form-field">
		<th scope="row">
			<label for="term_meta[excluded]"><?php echo _e( 'Exclude from Dropdown?' ) ?></label>
			<td>
				<select name="term_meta[excluded]" id="term_meta[excluded]">
					<option value="0" <?=( $term_meta['excluded'] == 0 ) ? 'selected': ''?>><?php echo _e( 'No' ); ?></option>
					<option value="1" <?=( $term_meta['excluded'] == 1 ) ? 'selected': ''?>><?php echo _e( 'Yes' ); ?></option>
				</select>
			</td>
		</th>
	</div>
	<?php
}
add_action( 'category_add_form_fields', 'rhd_create_excluded_category_field' );


function rhd_save_tax_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$term_meta = array();

		// Sanitize me, if necessary.
		$term_meta['excluded'] = isset ( $_POST['term_meta']['excluded'] ) ? intval( $_POST['term_meta']['excluded'] ) : '';

		update_option( "taxonomy_$term_id", $term_meta );
	}
}
add_action( 'edited_category', 'rhd_save_tax_meta', 10, 2 );
add_action( 'create_category', 'rhd_save_tax_meta', 10, 2 );


// Add column to Category list
function rhd_excluded_category_columns( $columns ) {
	return array_merge( $columns, array( 'excluded' =>  __( 'Excluded from Dropdown' ) ) );
}
add_filter( 'manage_edit-category_columns' , 'rhd_excluded_category_columns' );


// Add the value to the column
function rhd_excluded_category_columns_values( $deprecated, $column_name, $term_id) {
	if ( $column_name === 'excluded' ) {
		$term_meta = get_option( "taxonomy_$term_id" );

		if ( $term_meta['excluded'] === 1 ){
			echo _e( 'Yes' );
		} else{
			echo _e( 'No' );
		}
	}
}
add_action( 'manage_category_custom_column' , 'rhd_excluded_category_columns_values', 10, 3 );