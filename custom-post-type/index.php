<?php
/* Plugin Name: Custom Post Type Plugin
* Description: Create custom post type
* Version: 1.0.0
* Author: Dr. Sudip Chakraborty
*/
function register_custom_post_type() {
	$labels = array
    (
		'name'                  => _x( 'Events', 'Post type general name'),
		'singular_name'         => _x( 'Event', 'Post type singular name'),
		'menu_name'             => _x( 'Events', 'Admin Menu text'),
		'name_admin_bar'        => _x( 'Event', 'Add New on Toolbar'),
		'add_new'               => __( 'Add New'),
		'add_new_item'          => __( 'Add New Event'),
		'new_item'              => __( 'New Event'),
		'edit_item'             => __( 'Edit Event'),
		'view_item'             => __( 'View Event'),
		'all_items'             => __( 'All Events'),
		'search_items'          => __( 'Search Events'),
		'parent_item_colon'     => __( 'Parent Events:'),
		'not_found'             => __( 'No Events found.'),
		'not_found_in_trash'    => __( 'No Events found in Trash.'),
		'featured_image'        => _x( 'Events Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3'),
		'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3'),
		'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3'),
		'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3'),
		'archives'              => _x( 'Events archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4'),
		'insert_into_item'      => _x( 'Insert into Events', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4'),
		'uploaded_to_this_item' => _x( 'Uploaded to this Events', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4'),
		'filter_items_list'     => _x( 'Filter Events list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4'),
		'items_list_navigation' => _x( 'Events list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4'),
		'items_list'            => _x( 'Events list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4'),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'event' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
        'menu_icon'          => 'dashicons-calendar',
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
	);

	register_post_type( 'event', $args );
}
add_action( 'init', 'register_custom_post_type' );
//////////////////////////////////////////////////////////////////////




/////////////< Create Custom Meta Box>////////////////////////////////
function da_custom_meta_boxes()
{
 add_meta_box('da_cpt_id','Event Option', 'da_cpt_callback_func', 'event','normal','low');
}

add_action('add_meta_boxes','da_custom_meta_boxes');

function da_cpt_callback_func()
{
   wp_nonce_field(basename(__FILE__),'wp_da_cpt_nonce');
   $event_loction=get_post_meta(get_the_ID(),'event_location', true); 
?>
    <div>
        <laber for="event_location">Event Location:</laber>
        <input type="text" id="event_location" name="event_location" value="<?php echo $event_loction ?>" >
    </div>
<?php
}

add_action('save_post','custom_cpt_save_meta_box',10,2);

function custom_cpt_save_meta_box($post_id,$post)
{
    if(!isset($_POST['wp_da_cpt_nonce']) || ! wp_verify_nonce($_POST['wp_da_cpt_nonce'],basename(__FILE__)))
	return;

    if('event' != $post->post_type)
    return;

    if(isset($_POST['event_location']))
    {
        $event_location=sanitize_text_field($_POST['event_location']);
        update_post_meta($post_id,'event_location',$event_location);
    }
}
//////////////////////////////////////////////////////////////////////




/////////////< Display Additional field inside the CPT interface>/////
add_action('manage_event_posts_columns','da_custom_cpt_column');

function da_custom_cpt_column($columns)
{
   $custom_columns =[
    'cb' => '<input type="checkbox"/>',
    'title'=> 'Event Title',
    'event_location'=> 'Event Location',
    'date' => 'Date',
   ];
   return $custom_columns;
}

add_action('manage_event_posts_custom_column','da_cpt_custom_column_data',10,2);

function da_cpt_custom_column_data($columns,$post_id)
{
    switch($columns)
    {
        case "event_location":
            echo $event_location=get_post_meta($post_id,'event_location',true);
            break;
    }
}

add_filter('manage_edit-event_sortable_columns', 'da_cpt_sortable_columns');

function da_cpt_sortable_columns($columns)
{
     $columns['event_location']='event_location';
     return $columns;
}
//////////////////////////////////////////////////////////////////////