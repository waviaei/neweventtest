<?php

add_action( 'wp_enqueue_scripts', 'twentysixteen_parent_theme_enqueue_styles' );

function twentysixteen_parent_theme_enqueue_styles() {
    wp_enqueue_style( 'twentysixteen-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'neweventtest-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'twentysixteen-style' )
    );

}

/**
 * Change the order to order by event start date
 */

add_action('pre_get_posts','reorder_postasevent_demo');
function reorder_postasevent_demo( $query ) {
  if ( is_admin() || ! $query->is_main_query() ){
    return;
  }
  if ( $query->is_category() ) {
    $query->set( 'order', 'ASC');
    $query->set( 'orderby', 'meta_value_num');
    $query->set( 'meta_key', '_postasevent_demo_event_start_date');
  }
}


/**
 * Desplay meta values on the frontend
 */

// Display meta values from CMB2, by hooking to the_content filter
add_filter( 'the_content', 'display_postasevent_demo' );
function display_postasevent_demo( $content ) {
  // Check if we're inside the main loop in a single post page.
  if ( is_single() && in_the_loop() && is_main_query() ) {

    // Grab the metadata from the database
    $event_schedule = get_post_meta( get_the_ID(), '_postasevent_demo_event_start_date', true );
    $start_date = date('F j, Y', $event_schedule );

    return $content . esc_html( $start_date );

  }
  return $content;
}




// CMB2
// https://github.com/WebDevStudios/CMB2

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

//if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
//	require_once dirname( __FILE__ ) . '/cmb2/init.php';
//} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
//	require_once dirname( __FILE__ ) . '/CMB2/init.php';
//}

/**
 * Conditionally displays a metabox when used as a callback in the 'show_on_cb' cmb2_box parameter
 *
 * @param  CMB2 object $cmb CMB2 object
 *
 * @return bool             True if metabox should show
 */
function postasevent_show_if_front_page( $cmb ) {
	// Don't show this metabox if it's not the front page template
	if ( $cmb->object_id !== get_option( 'page_on_front' ) ) {
		return false;
	}
	return true;
}

/**
 * Conditionally displays a field when used as a callback in the 'show_on_cb' field parameter
 *
 * @param  CMB2_Field object $field Field object
 *
 * @return bool                     True if metabox should show
 */
function postasevent_hide_if_no_cats( $field ) {
	// Don't show this field if not in the cats category
	if ( ! has_tag( 'cats', $field->object_id ) ) {
		return false;
	}
	return true;
}

/**
 * Manually render a field.
 *
 * @param  array      $field_args Array of field arguments.
 * @param  CMB2_Field $field      The field object
 */
function postasevent_render_row_cb( $field_args, $field ) {
	$classes     = $field->row_classes();
	$id          = $field->args( 'id' );
	$label       = $field->args( 'name' );
	$name        = $field->args( '_name' );
	$value       = $field->escaped_value();
	$description = $field->args( 'description' );
	?>
	<div class="custom-field-row <?php echo $classes; ?>">
		<p><label for="<?php echo $id; ?>"><?php echo $label; ?></label></p>
		<p><input id="<?php echo $id; ?>" type="text" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/></p>
		<p class="description"><?php echo $description; ?></p>
	</div>
	<?php
}

/**
 * Manually render a field column display.
 *
 * @param  array      $field_args Array of field arguments.
 * @param  CMB2_Field $field      The field object
 */
function postasevent_display_text_small_column( $field_args, $field ) {
	?>
	<div class="custom-column-display <?php echo $field->row_classes(); ?>">
		<p><?php echo $field->escaped_value(); ?></p>
		<p class="description"><?php echo $field->args( 'description' ); ?></p>
	</div>
	<?php
}

/**
 * Conditionally displays a message if the $post_id is 2
 *
 * @param  array             $field_args Array of field parameters
 * @param  CMB2_Field object $field      Field object
 */
function postasevent_before_row_if_2( $field_args, $field ) {
	if ( 2 == $field->object_id ) {
		echo '<p>Testing <b>"before_row"</b> parameter (on $post_id 2)</p>';
	} else {
		echo '<p>Testing <b>"before_row"</b> parameter (<b>NOT</b> on $post_id 2)</p>';
	}
}

add_action( 'cmb2_admin_init', 'postasevent_register_demo_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function postasevent_register_demo_metabox() {
	$prefix = '_postasevent_demo_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Event Schedule', 'cmb2' ),
		'object_types'  => array( 'post', ), // Post type
		// 'show_on_cb' => 'postasevent_show_if_front_page', // function should return a bool value
		// 'context'    => 'normal',
		'priority'   => 'high',
		// 'show_names' => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // true to keep the metabox closed by default
		// 'classes'    => 'extra-class', // Extra cmb2-wrap classes
		// 'classes_cb' => 'postasevent_add_some_classes', // Add classes through a callback.
	) );

  $cmb_demo->add_field( array(
		'name' => esc_html__( 'Start Date', 'cmb2' ),
		'desc' => esc_html__( 'Event date or start date (stored as UNIX timestamp)', 'cmb2' ),
		'id'   => $prefix . 'event_start_date',
		'type' => 'text_date_timestamp',
		// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
	) );

  $cmb_demo->add_field( array(
		'name' => esc_html__( 'Start Time', 'cmb2' ),
		'desc' => esc_html__( 'Event start time', 'cmb2' ),
		'id'   => $prefix . 'event_start_time',
		'type' => 'text_time',
		'time_format' => 'H:i', // Set to 24hr format
	) );

  $cmb_demo->add_field( array(
		'name' => esc_html__( 'End Date', 'cmb2' ),
		'desc' => esc_html__( 'End date (stored as UNIX timestamp)', 'cmb2' ),
		'id'   => $prefix . 'event_end_date',
		'type' => 'text_date_timestamp',
		// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
	) );

  $cmb_demo->add_field( array(
		'name' => esc_html__( 'End Time', 'cmb2' ),
		'desc' => esc_html__( 'End time', 'cmb2' ),
		'id'   => $prefix . 'event_end_time',
		'type' => 'text_time',
		'time_format' => 'H:i', // Set to 24hr format
	) );

  $cmb_demo->add_field( array(
		'name' => esc_html__( 'Registration Deadline Date', 'cmb2' ),
		'desc' => esc_html__( 'Deadline date (stored as UNIX timestamp)', 'cmb2' ),
		'id'   => $prefix . 'event_reg_date',
		'type' => 'text_date_timestamp',
		// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
	) );

  $cmb_demo->add_field( array(
		'name' => esc_html__( 'Registration Deadline Time', 'cmb2' ),
		'desc' => esc_html__( 'Enter a time registration ends. Leave blank if not any.', 'cmb2' ),
		'id'   => $prefix . 'event_reg_time',
		'type' => 'text_time',
		'time_format' => 'H:i', // Set to 24hr format
	) );


}



/**
 * Only show this box in the CMB2 REST API if the user is logged in.
 *
 * @param  bool                 $is_allowed     Whether this box and its fields are allowed to be viewed.
 * @param  CMB2_REST_Controller $cmb_controller The controller object.
 *                                              CMB2 object available via `$cmb_controller->rest_box->cmb`.
 *
 * @return bool                 Whether this box and its fields are allowed to be viewed.
 */
function postasevent_limit_rest_view_to_logged_in_users( $is_allowed, $cmb_controller ) {
	if ( ! is_user_logged_in() ) {
		$is_allowed = false;
	}

	return $is_allowed;
}

add_action( 'cmb2_init', 'postasevent_register_rest_api_box' );
/**
 * Hook in and add a box to be available in the CMB2 REST API. Can only happen on the 'cmb2_init' hook.
 * More info: https://github.com/WebDevStudios/CMB2/wiki/REST-API
 */
function postasevent_register_rest_api_box() {
	$prefix = '_postasevent_rest_';

	$cmb_rest = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'REST Test Box', 'cmb2' ),
		'object_types'  => array( 'page', ), // Post type
		'show_in_rest' => WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::EDITABLE, // Determines which HTTP methods the box is visible in.
		// Optional callback to limit box visibility.
		// See: https://github.com/WebDevStudios/CMB2/wiki/REST-API#permissions
		// 'get_box_permissions_check_cb' => 'postasevent_limit_rest_view_to_logged_in_users',
	) );

	$cmb_rest->add_field( array(
		'name'       => esc_html__( 'REST Test Text', 'cmb2' ),
		'desc'       => esc_html__( 'Will show in the REST API for this box and for pages.', 'cmb2' ),
		'id'         => $prefix . 'text',
		'type'       => 'text',
	) );

	$cmb_rest->add_field( array(
		'name'       => esc_html__( 'REST Editable Test Text', 'cmb2' ),
		'desc'       => esc_html__( 'Will show in REST API "editable" contexts only (`POST` requests).', 'cmb2' ),
		'id'         => $prefix . 'editable_text',
		'type'       => 'text',
		'show_in_rest' => WP_REST_Server::EDITABLE// WP_REST_Server::ALLMETHODS|WP_REST_Server::READABLE, // Determines which HTTP methods the field is visible in. Will override the cmb2_box 'show_in_rest' param.
	) );
}
