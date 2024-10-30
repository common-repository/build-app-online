<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://buildapp.online
 * @since      1.0.0
 *
 * @package    Build_App_Online_Blog
 * @subpackage Build_App_Online_Blog/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Build_App_Online_Blog
 * @subpackage Build_App_Online_Blog/public
 * @author     Abdul Hakeem <info@buildapp.online>
 */
class Build_App_Online_Blog_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Build_App_Online_Blog_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Build_App_Online_Blog_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/build-app-online-blog-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Build_App_Online_Blog_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Build_App_Online_Blog_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/build-app-online-blog-public.js', array( 'jquery' ), $this->version, false );

	}

    public function app_details() {

        $data = array();

        $dotapp_blocks = get_option('dotapp_blocks_blog');

        $data['dotapp_settings'] = get_option('dotapp_settings_blog');
        
        if(isset($_REQUEST['lang'])) {
            $locale = sanitize_text_field($_REQUEST['lang']);
        } else {
            $locale = 'en';
        }
        
        if($locale != 'en') {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . '/languages/' . $locale . '.php';

            $locale_cls = new Build_App_Online_i18nt();

            $language_texts = $locale_cls->load_plugin_textdomain();
        }

        if(is_array($dotapp_blocks))
        foreach ($dotapp_blocks as $key => $value) {

            if($dotapp_blocks[$key]['blockType'] == 'postList' || $dotapp_blocks[$key]['blockType'] == 'postScroll' || $dotapp_blocks[$key]['blockType'] == 'postSlider' || $dotapp_blocks[$key]['blockType'] == 'postListTile') {

                $args = array();
                
                $link_id = isset($dotapp_blocks[$key]['linkId']) ? $dotapp_blocks[$key]['linkId'] : 0;
                
                if($dotapp_blocks[$key]['linkType'] == 'category') {
                    $args['category'] = $link_id;
                } else if($dotapp_blocks[$key]['linkType'] == 'post_tag') {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'post_tag',
                            'field'    => 'id',
                            'terms'    => $link_id
                        )
                    );
                }

                $dotapp_blocks[$key]['posts'] = $this->posts($args);

            }

            if($locale != 'en' && isset($language_texts[$dotapp_blocks[$key]['title']])) {
                $dotapp_blocks[$key]['title'] = $language_texts[$dotapp_blocks[$key]['title']];
            }
        }

        $data['dotapp_blocks'] = $dotapp_blocks;

        $data['dotapp_theme'] = get_option('dotapp_theme_blog');

        $data['categories'] = $this->get_taxonomy('category');

        $adminUIDs = array();

        $admins = get_users( [ 'role__in' => [ 'administrator' ] ] );
        foreach ( $admins as $user ) {
            $adminUID = get_user_meta( $user->id, 'bao_blog_uid', true );
            if($adminUID) {
                $adminUIDs[] = $adminUID;
            }
        }

        $data['settings'] = array(
            'siteName' => get_bloginfo('name'),
            'siteDescription' => get_bloginfo('description'),
            'is_rtl' => is_rtl(),
            'adminUIDs' => $adminUIDs,
        );

        if (is_user_logged_in()) {

            $user_id = get_current_user_id();

            $data['user'] = $this->get_formatted_item_data_customer( $user_id );

        } else {

            $data['user'] = null;

        }

        $data['posts'] = $this->posts();

        wp_send_json($data);

        die();
    }

    public function design_app_details() {

        $data = array();

        $dotapp_blocks = get_option('dotapp_blocks_blog');

        $data['dotapp_settings'] = get_option('dotapp_settings_blog');

        $data['firebaseSettings'] = get_option('bao_firebase_blog');
        
        if(isset($_REQUEST['lang'])) {
            $locale = sanitize_text_field($_REQUEST['lang']);
        } else {
            $locale = 'en';
        }
        
        if($locale != 'en') {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . '/languages/' . $locale . '.php';

            $locale_cls = new Build_App_Online_i18nt();

            $language_texts = $locale_cls->load_plugin_textdomain();
        }

        if(is_array($dotapp_blocks))
        foreach ($dotapp_blocks as $key => $value) {

            if($dotapp_blocks[$key]['blockType'] == 'postList' || $dotapp_blocks[$key]['blockType'] == 'postScroll' || $dotapp_blocks[$key]['blockType'] == 'postSlider' || $dotapp_blocks[$key]['blockType'] == 'postListTile') {

                $args = array();
                
                $link_id = isset($dotapp_blocks[$key]['linkId']) ? $dotapp_blocks[$key]['linkId'] : 0;
                
                if($dotapp_blocks[$key]['linkType'] == 'category') {
                    $args['category'] = $link_id;
                } else if($dotapp_blocks[$key]['linkType'] == 'post_tag') {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'post_tag',
                            'field'    => 'id',
                            'terms'    => $link_id
                        )
                    );
                }

                $dotapp_blocks[$key]['posts'] = $this->posts($args);

            }

            if($locale != 'en' && isset($language_texts[$dotapp_blocks[$key]['title']])) {
                $dotapp_blocks[$key]['title'] = $language_texts[$dotapp_blocks[$key]['title']];
            }
        }

        $data['dotapp_blocks'] = $dotapp_blocks;

        $data['dotapp_theme'] = get_option('dotapp_theme_blog');

        $data['categories'] = $this->get_taxonomy('category');

        if (is_user_logged_in()) {

            $user_id = get_current_user_id();

            $data['user'] = $this->get_formatted_item_data_customer( $user_id );

        } else {

            $data['user'] = null;

        }

        $data['posts'] = $this->posts();

        wp_send_json($data);

        die();
    }

    protected function get_formatted_item_data_customer($id) {
        
        $user_info = get_userdata($id);

        return array(
            'id'                => $id,
            'username'          => $user_info->user_login,
            'first_name'        => $user_info->first_name,
            'last_name'         => $user_info->last_name,
            'display_name'      => $user_info->display_name,
            'email'             => $user_info->user_email,
            'role'              => reset($user_info->roles),
            'avatar_url'        => get_avatar_url( $id ),
        );
    }

    public function posts( $args = array() ) {
        
        $pre_args['posts_per_page'] = 10;

        $args = wp_parse_args($pre_args, $args);

        $posts = get_posts( $args );
        $data = array();
        foreach ($posts as $key => $post) {

            $category_detail = get_the_category( $post->ID );//$post->ID
            if(count($category_detail) > 0) {
                $category_name = $category_detail[0]->cat_name;
                $category_id = $category_detail[0]->cat_ID;
            }

            $data[] = array(
                'date' => $post->post_date,
                'id' => $post->ID,
                'link' => get_post_permalink($post->ID),
                'title' => array(
                    'rendered' => apply_filters( 'the_title', $post->post_title )
                ),
                'content' => array(
                    'rendered' => $post->post_content
                ),
                'excerpt' => array(
                    'rendered' => apply_filters( 'the_excerpt', $post->post_excerpt )
                ),
                'image' => array(
                    'id' => null,
                    'src' => has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail_url( $post->ID, 'full' ) : null,
                ),
                'authorDetails' => array(
                    'name' => get_the_author_meta('display_name', $post->post_author),
                    'avatar' => get_avatar_url($post->post_author)
                ),
                'category_name' => $category_name,
                'category' => $category_id,
                'link' => get_permalink( $post->ID ),
            );
        }
        return $data;
    }

    /*public function get_posts() {

        $args = array();

        $args['category'] = 2;

        $posts = $this->posts( $args );

        wp_send_json($posts);

    }*/

    public function get_taxonomy($taxonomy = 'product_cat') {

        if(isset($_REQUEST['taxonomy'])) {
            $taxonomy = sanitize_text_field($_REQUEST['taxonomy']);
        }

        $taxonomy     = $taxonomy;
        $orderby      = 'name';  
        $show_count   = 1;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no  
        $title        = '';  
        $empty        = 0;

        $args = array(
             'taxonomy'     => $taxonomy,
             'show_count'   => $show_count,
             'pad_counts'   => $pad_counts,
             'hierarchical' => $hierarchical,
             'title'     => $title,
             'hide_empty'   => $empty,
             'menu_order' => 'asc',
        );

        $categories = get_categories( $args );

        if (($key = array_search('uncategorized', array_column($categories, 'slug'))) !== false) {
            unset($categories[$key]);
        }

        $data = array();

        foreach ($categories as $key => $value) {

            $image_id = get_term_meta( $value->term_id, 'thumbnail_id', true );
            $image = '';

            if ( $image_id ) {
                $image = wp_get_attachment_url( $image_id );
            }

            $data[] = array(
                'id' => $value->term_id,
                'name' => $value->name,
                'slug' => $value->slug,
                'description' => $value->description,
                'parent' => $value->parent,
                'count' => $value->count,
                'image' => $image,
            );

        }

        return $data;
    }

    public function login() {

        if(isset($_REQUEST['username']) && isset($_REQUEST['password'])) {

            $creds = array(
                'user_login'    => addslashes(rawurldecode($_REQUEST['username'])),
                'user_password' => addslashes(rawurldecode($_REQUEST['password'])),
                'remember'      => true,
            );

            $user = wp_signon( $creds, is_ssl() );

            if(!is_wp_error( $user )) {
                $data = $this->get_formatted_item_data_customer( $user->ID );
                wp_send_json($data);
            } else {
                wp_send_json_error( $user, 400 );
            }

        } else if(isset($_REQUEST['access_token'])) {
            $this->facebook_login();
        } else if(isset($_REQUEST['token'])) {
            $this->google_login();
        } else if(isset($_REQUEST['userIdentifier'])) {
            $this->apple_login();
        } else if(isset($_REQUEST['verificationId'])) {
            $this->otp_verification();
        }

    }

    public function save_new_post( $post_id, $post, $update ) {

        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( 'post' == $post->post_type ) {
            
            $categories = get_the_category( $post_id );

            if(is_array($categories)) {
                
                $post_title = get_the_title( $post_id );
            
                $meaasge = array (
                    'body'  => $post_title,
                    'title'     => reset($categories)->name,
                    'sound'     => 1,
                );
                
                $fields = array (
                    'to'  => '/topics/' . reset($categories)->slug,
                    'notification' => $meaasge,
                    'data' => array(
                        'post' => $post_id
                    )
                );

                $this->fcm($fields);
            }
            
        }

    }

    public function send_fcm($token, $data){

        $options = get_option('bao_firebase_blog');

        $server_key = $options['serverKey'];
        
        $fields = array();

        $fields['mtitle'] = $title;
        $fields['mdesc'] = $message;

        $data = '{ "notification": { "title": "' . $fields['mtitle'] . '", "body": "' . $fields['mdesc'] . '" }, "to" : "'. $token .'" }';

        $this->fcm($data, $server_key);

    }

    public function fcm($data) {

        $fcm_remote_url = "https://fcm.googleapis.com/fcm/send";
        
        $options = get_option('bao_firebase_blog');
        $server_key = $options['serverKey'];

        //$server_key = 'AAAAGogO8MI:APA91bFUw4s3Ox5Ewnbo5-8YSk6IcM4zOJ6Du7WOO7c07QmmZHZZgCw4AcljUgg1sYdUdNUOfltLqohhyworwAgWS8JbcVA7twxP0SpmVXwAaOe8x7VGG-paaM9txz0wWrtSNoQjkz_k';
         
        $args = array(
          'body' => json_encode($data),
          'timeout' => '5',
          'redirection' => '5',
          'httpversion' => '1.0',
          'blocking' => true,
          'headers' => array(),
          'cookies' => array(),
          'headers' => array(
            'Content-type' => 'application/json',
            'Authorization' => 'key=' . $server_key
          )
        );

        $response = wp_remote_post( $fcm_remote_url, $args );

    }

    public function site_details() {

        $data = array(
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'wpurl' => get_bloginfo('wpurl'),
            'home' => get_bloginfo('url')
        );

        wp_send_json($data);

    }

    public function app_save_options() {
        $entityBody = file_get_contents('php://input');

        if(isset($entityBody) && !empty($entityBody)) {
            $data = json_decode($entityBody, true);
            $option_key = $data['option'] . '_blog';
            $user = wp_get_current_user();
            if ( in_array( 'administrator', (array) $user->roles ) ) {
                update_option($option_key, $data['data']);
            } else if ( in_array( 'editor', (array) $user->roles ) ) {
                update_option($option_key, $data['data']);
            } else {
                $notice = array(
                    'success' => false,
                    'data' => array(
                        'notice' => 'Login as admin to edit'
                    )
                );
                wp_send_json_error( $notice, 400 );
            } 
        }

        wp_send_json(true);
    }

    public function fcm_details() {
        if(isset($_REQUEST['token'])) {

            $token = sanitize_key($_REQUEST['token']);
        
            $options = get_option('bao_firebase_blog');

            $apikey = $options['serverKey'];

            $fcm_remote_url = "https://iid.googleapis.com/iid/info/" . $token . "?details=true";
             
            $args = array(
              'timeout' => '5',
              'redirection' => '5',
              'httpversion' => '1.0',
              'blocking' => true,
              'headers' => array(),
              'cookies' => array(),
              'headers' => array(
                'Content-type' => 'application/json',
                'Authorization' => 'key=' . $apikey
              )
            );

            $response = wp_remote_get( $fcm_remote_url, $args );


            if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                $data = (array)json_decode($response['body']);
                $data['topics'] = array();
                if(isset($data['rel'])) {
                    foreach ($data['rel']->topics as $key => $value) {
                        $data['topics'][] = $key;
                    }
                }
                wp_send_json($data);// use the content
            }

            wp_send_json_error($response['body']);

        }
    }

    public function user_bookmark() {

        $results = array();

        $user_id = get_current_user_id();

        if(get_current_user_id() == 0) {
            return $results;
        }

        $bookmarkIds = get_user_meta($user_id, 'bao_bookmark', true);
        $ids = $bookmarkIds ? array_values($bookmarkIds) : array();

        $page = $_REQUEST['page'] ? absint($_REQUEST['page']) : 1;

        if(!empty($ids)) {

            $args = array(
                'include' => $ids,
                //'page' => $page,
            );

            $results = $this->posts($args);

        }

        return $results;

    }


    public function get_bookmark() {
        
        wp_send_json($this->user_bookmark());

    }

    public function get_bookmarkids() {

        $bookmarkIds = get_user_meta(get_current_user_id(), 'bao_bookmark', true);
        $ids = $bookmarkIds ? array_values($bookmarkIds) : array();
        
        wp_send_json($ids);

    }

    public function update_bookmark() {

        if(isset($_REQUEST['id'])) {
            $id = absint($_REQUEST['id']);
            $user_id = get_current_user_id();

            if(get_current_user_id() == 0) {
                wp_send_json(array());
            }

            $bookmarkIds = get_user_meta($user_id, 'bao_bookmark', true);
            $ids = $bookmarkIds ? array_values($bookmarkIds) : array();

            if(empty($ids)) {
                $ids = array();
                array_push($ids, $id);
                update_user_meta($user_id, 'bao_bookmark', $ids);
                wp_send_json(array_values($ids));
            }

            if(array_search($id, $ids) === false) {
                array_push($ids, $id);
            } else {
                unset($ids[array_search($id, $ids)]);
            }
            
            update_user_meta($user_id, 'bao_bookmark', $ids);
            wp_send_json(array_values($ids));
        }

        wp_send_json(array());

    }

    public function bao_my_blocks() {
        $dotapp_blocks = get_option('bao_my_blocks_blog');
        if(is_array($dotapp_blocks)) {
            wp_send_json($dotapp_blocks);
        } else {
            wp_send_json(array());
        }
    }

    public function get_blocks() {

        $blocks = get_option('bao_my_blocks_blog');

        if(is_array($blocks) && isset($_REQUEST['id'])) {

            $id = absint($_REQUEST['id']);

            if(isset($blocks[$id]) && isset($blocks[$id]['blocks'])) {
                $dotapp_blocks = $blocks[$id]['blocks'];
            } else {
                $dotapp_blocks = $blocks[array_rand($blocks, 1)]['blocks'];
            }

            foreach ($dotapp_blocks as $key => $value) {
                
                if($dotapp_blocks[$key]['blockType'] == 'postList' || $dotapp_blocks[$key]['blockType'] == 'postScroll' || $dotapp_blocks[$key]['blockType'] == 'postSlider' || $dotapp_blocks[$key]['blockType'] == 'postListTile') {

                    $args = array();
                    
                    $link_id = isset($dotapp_blocks[$key]['linkId']) ? $dotapp_blocks[$key]['linkId'] : 0;
                    
                    if($dotapp_blocks[$key]['linkType'] == 'category') {
                        $args['category'] = $link_id;
                    } else if($dotapp_blocks[$key]['linkType'] == 'post_tag') {
                        $args['tax_query'] = array(
                            array(
                                'taxonomy' => 'post_tag',
                                'field'    => 'id',
                                'terms'    => $link_id
                            )
                        );
                    }

                    $dotapp_blocks[$key]['posts'] = $this->posts($args);

                }

                if($locale != 'en' && isset($language_texts[$dotapp_blocks[$key]['title']])) {
                    $dotapp_blocks[$key]['title'] = $language_texts[$dotapp_blocks[$key]['title']];
                }

            }

            wp_send_json($dotapp_blocks);
        }

        wp_send_json(array());
        
    }

    function google_login() { 

        if (isset($_REQUEST['token'])) {

            $id_token = sanitize_key($_REQUEST['token']);
            $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $id_token;

            $response = wp_remote_get( $url );

            $body = wp_remote_retrieve_body( $response );

            $result = json_decode($body, true);

            if (isset($result["email_verified"])) {
                $email = sanitize_email($_POST['email']);
                $first_name = $result["given_name"];
                $last_name = $result["family_name"];
                $display_name = $result["name"];
                $email_exists = email_exists($email);
                if ($email_exists) {
                    $user = get_user_by('email', $email);
                    $user_id = $user->ID;
                    $user_name = $user->user_login;
                }

                if (!$user_id && $email_exists == false) {
                    $user_name = $email;
                    $i = 0;
                    while (username_exists($user_name)) {
                        $i++;
                        $user_name = strtolower($first_name . '.' . $last_name) . '.' . $i;
                    }

                    $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                    $userdata = array(
                        'user_login' => $user_name,
                        'user_email' => $email,
                        'user_pass' => $random_password,
                        'display_name' => $display_name,
                        'first_name' => $first_name,
                        'last_name' => $last_name
                    );
                    $user_id = wp_insert_user($userdata);

                    if ($user_id) {
                        update_user_meta( $user_id, 'first_name', $first_name );
                        update_user_meta( $user_id, 'last_name', $last_name );
                        update_user_meta( $user_id, 'billing_first_name', $first_name );
                        update_user_meta( $user_id, 'billing_last_name', $last_name );
                        update_user_meta( $user_id, 'shipping_first_name', $first_name );
                        update_user_meta( $user_id, 'shipping_last_name', $last_name );
                        $user = get_user_by( 'id', $user_id );
                        $user->add_role( 'customer' );
                        $user->remove_role( 'subscriber' );
                    } else {
                        wp_send_json($user_id, 400);
                    }
                }

                $expiration = time() + apply_filters('auth_cookie_expiration', 91209600, $user_id, true);
                $cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');
                wp_set_auth_cookie($user_id, true);

                $data = $this->get_formatted_item_data_customer( $user_id );
                wp_send_json($data);

            } else {
                $response = array(
                    array(
                    'message' => 'Login failed',
                    'code' => 0
                ));
                wp_send_json_error($response, 400);
            }
            
        } else {
            $response = array(
                array(
                'message' => 'Login failed',
                'code' => 0
            ));
            wp_send_json_error($response, 400);
        }
    }

    public function apple_login() {

        //https://flutter-sign-in-with-apple-example.glitch.me/callbacks/sign_in_with_apple

        
        if (isset($_POST['userIdentifier'])) {
            $userIdentifier = sanitize_key($_POST['userIdentifier']);
            $first_name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
            $display_name = $first_name;
            $username_exists = username_exists($userIdentifier);
            if ($username_exists) {
                $user = get_user_by('login', $userIdentifier);
                $user_id = $user->ID;
                $user_name = $user->user_login;
            }

            if ($username_exists == false) {
                $user_name = $userIdentifier;

                $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                $userdata = array(
                    'user_login' => $user_name,
                    //'user_email' => $email,
                    'user_pass' => $random_password,
                    'display_name' => $display_name,
                    'first_name' => $display_name,
                );
                $user_id = wp_insert_user($userdata);

                if ($user_id) {
                    update_user_meta( $user_id, 'first_name', $first_name );
                    update_user_meta( $user_id, 'billing_first_name', $first_name );
                    update_user_meta( $user_id, 'shipping_first_name', $first_name );
                    $user = get_user_by( 'id', $user_id );
                    $user->add_role( 'customer' );
                    $user->remove_role( 'subscriber' );
                } else {
                    wp_send_json($user_id, 400);
                }
            }

            $expiration = time() + apply_filters('auth_cookie_expiration', 91209600, $user_id, true);
            $cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');
            wp_set_auth_cookie($user_id, true);

            $data = $this->get_formatted_item_data_customer( $user_id );
            wp_send_json($data);
            
        } else {
            $response = array(
                'errors' => array('Login failed'),
                'status' => false
            );
        }
        
        wp_send_json($response, 400);
    }

    function otp_verification() {

        $options = get_option('bao_firebase_blog');

        if (isset($_REQUEST['verificationId']) && isset($_REQUEST['smsOTP'])) {

            $sessionInfo = sanitize_key($_REQUEST['verificationId']);
            $code = absint($_REQUEST['smsOTP']);

            $data = array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'sessionInfo' => $sessionInfo,
                    'code' => $code
                ),
                'cookies'     => array()
            );

            $firebase_serverkey = $options['webAPIKey'];

            $url = 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPhoneNumber?key=' . $firebase_serverkey;

            $response = wp_remote_post( $url, $data );

            $body = wp_remote_retrieve_body( $response );
            
            $result = json_decode($body, true);

            //Uncomment for debug olny
            //update_option('bao_verificationId', $sessionInfo);
            //update_option('bao_code', $code);
            //update_option('bao_otp_verification_result', $result);
            
            if(isset($result['error']) && ( $result['error']['message'] == 'SESSION_EXPIRED' || $result['error']['message'] == 'INVALID_SESSION_INFO' ) && isset($_REQUEST['phoneNumber'])) {
                
                $number = sanitize_text_field($_REQUEST['phoneNumber']);
                $phone  = preg_replace('/[^a-zA-Z0-9_ -]/s','',$number);

                //Use when its required to stripe country code
                /*if (strlen($phone)==12) {
                    $phone = substr($phone, 2);
                }*/

                $username_exists = username_exists($phone);

                if(!$username_exists) {
                    $username_exists = username_exists('+'.$phone);
                }
                
                if ($username_exists) {
                    $user = get_user_by('login', $phone);
                    $user_id = $user->ID;
                    $user_name = $user->user_login;
                }


                if (!$username_exists) {
                    $user_name = $phone;
                    $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                    $userdata = array(
                        'user_login' => $user_name,
                        'user_pass' => $random_password,
                    );
                    $user_id = wp_insert_user($userdata);

                    if ($user_id) {
                        $user = get_user_by( 'id', $user_id );
                        update_user_meta( $user_id, 'billing_phone', $phone );
                        $user->add_role( 'customer' );
                        $user->remove_role( 'subscriber' );
                    } else {
                        wp_send_json_error($user_id, 400);
                    }
                }

                wp_set_current_user( $user_id, $user->user_login );
                $expiration = time() + apply_filters('auth_cookie_expiration', 91209600, $user_id, true);
                $cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');
                wp_set_auth_cookie($user_id, true);
                
                $data = $this->get_formatted_item_data_customer( $user_id );
                wp_send_json($data);

            } else if(isset($result['error']) && $result['error']['message'] != 'SESSION_EXPIRED') {
                $result = json_decode($body, true);

                wp_send_json_error($result['error']['errors'], 400);

            } else if (isset($result['phoneNumber']) || (isset($result['error']) && $result['error']['message'] != 'SESSION_EXPIRED')) {
                
                $number = $result['phoneNumber'];
                $phone  = preg_replace('/[^a-zA-Z0-9_ -]/s','',$number);
                $username_exists = username_exists($phone);

                if(!$username_exists) {
                    $username_exists = username_exists('+'.$phone);
                }

                if ($username_exists) {
                    $user = get_user_by('login', $phone);
                    $user_id = $user->ID;
                    $user_name = $user->user_login;
                }

                if (!$username_exists) {
                    $user_name = $phone;
                    $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                    $userdata = array(
                        'user_login' => $user_name,
                        'user_pass' => $random_password,
                    );
                    $user_id = wp_insert_user($userdata);

                    if ($user_id) {
                        $user = get_user_by( 'id', $user_id );
                        update_user_meta( $user_id, 'billing_phone', $phone );
                        $user->add_role( 'customer' );
                        $user->remove_role( 'subscriber' );
                    } else {
                        wp_send_json_error($user_id, 400);
                    }
                }

                wp_set_current_user( $user_id, $user->user_login );
                $expiration = time() + apply_filters('auth_cookie_expiration', 91209600, $user_id, true);
                $cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');
                wp_set_auth_cookie($user_id, true);
                
                $data = $this->get_formatted_item_data_customer( $user_id );
                wp_send_json($data);

            } else {
                $response = array(
                    array(
                    'message' => 'Phone auth failed',
                    'code' => 0
                ));
                wp_send_json_error($response, 400);
            }
            
        } else {
            $response = array(
                array(
                'message' => 'Login failed',
                'code' => 0
            ));
            wp_send_json_error($response, 400);
        }
    }

}
