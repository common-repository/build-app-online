<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://buildapp.online
 * @since             1.0.0
 * @package           Build_App_Online
 *
 * @wordpress-plugin
 * Plugin Name:       Build App Online
 * Plugin URI:        https://wordpress.org/plugins/build-app-online
 * Description:       Help you to build and run mobile app.
 * Version:           1.0.22
 * Author:            Abdul Hakeem
 * Author URI:        https://buildapp.online
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       build-app-online
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BUILD_APP_ONLINE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-build-app-online-activator.php
 */
function activate_build_app_online() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-build-app-online-activator.php';
	Build_App_Online_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-build-app-online-deactivator.php
 */
function deactivate_build_app_online() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-build-app-online-deactivator.php';
	Build_App_Online_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_build_app_online' );
register_deactivation_hook( __FILE__, 'deactivate_build_app_online' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-build-app-online.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

add_action('rest_api_init', 'register_rest_data' );

function run_build_app_online() {

	$object_type = 'post';
	$args1 = array(
	    'type'         => 'integer',
	    'description'  => 'A meta key associated with a string meta value.',
	    'single'       => true,
	    'show_in_rest' => true,
	);
		
	register_meta( $object_type, 'mstoreapp_likes', $args1 );


	function register_rest_data(){
	    register_rest_field( array('post'),
	        'featuredUrl',
	        array(
	            'get_callback'    => 'get_rest_featured_image',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	    register_rest_field( array('post'),
	        'image',
	        array(
	            'get_callback'    => 'get_rest_featured_image_src',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	    register_rest_field( array('post'),
	        'author_details',
	        array(
	            'get_callback'    => 'get_rest_auther_name',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	    register_rest_field( array('post'),
	        'featured_details',
	        array(
	            'get_callback'    => 'get_rest_featured_image_details',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	    register_rest_field( array('post'),
	        'category_name',
	        array(
	            'get_callback'    => 'get_rest_category_name',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	}
	function get_rest_featured_image( $object, $field_name, $request ) {
	    //if( $object['featured_media'] ){
	    //has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail_url( $post->ID, 'medium' ) : null,
	        $img = wp_get_attachment_image_src( $object['featured_media'], 'app-thumb' );
	        if(is_array($img))
	        return $img[0];
	   // }
	    return false;
	}

	function get_rest_featured_image_src( $object, $field_name, $request ) {
	    if( $object['featured_media'] ){
	        $img = wp_get_attachment_image_src( $object['featured_media'], 'app-thumb' );
	        if(is_array($img)) {
	            return array(
	                'src' => $img[0]
	            );
	        } else {
	            return null;
	        }
	    }
	    return null;
	}

	function get_rest_featured_image_details( $object, $field_name, $request ) {
	    if( $object['featured_media'] ){
	        $img = wp_get_attachment_metadata( $object['featured_media'] );
	        if($img) {
	            return $img;
	        } else return null;   
	    }
	    return null;
	}

	function get_rest_excerpt( $object, $field_name, $request ) {
	        $excerpt = get_the_excerpt($object);
	        if($excerpt != null)
	        return $excerpt;
	        return 'data';
	}

	function get_rest_auther_name( $object, $field_name, $request ) {
	    if( $object['author'] ){
	        //$user_info = get_userdata($object['author']);
	        $data = array (
	            'name' => get_the_author_meta('display_name', $object['author']),
	            'avatar' => get_avatar_url($object['author'])
	        );
	        return $data;
	    }
	    return false;
	}

	function get_rest_category_name( $object, $field_name, $request ) {
	    $category_detail = get_the_category( $object['id'] );//$post->ID
	    if(count($category_detail) > 0){
	        $category = $category_detail[0]->cat_name;
	        return $category;
	    }
	    return null;
	}


	$plugin = new Build_App_Online();
	$plugin->run();

}

run_build_app_online();
