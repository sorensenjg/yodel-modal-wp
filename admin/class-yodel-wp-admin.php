<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.sorensenjg.com
 * @since      1.0.0
 *
 * @package    Yodel_Wp
 * @subpackage Yodel_Wp/admin
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Yodel_Wp
 * @subpackage Yodel_Wp/admin
 * @author     @sorensenjg <hey@sorensenjg.com>
 */
class Yodel_Wp_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'init', array( $this, 'register_post_types' ) ); 
		add_action( 'admin_head', array( $this, 'admin_head_scripts' )); 
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'carbon_fields_register_fields', array( $this, 'register_settings_fields' ) );
		add_action( 'carbon_fields_register_fields', array( $this, 'register_modal_fields' ) );
		add_action( 'carbon_fields_register_fields', array( $this, 'register_submission_fields' ) );

		add_filter( 'carbon_fields_association_field_options_yodel_wp_modal_association_post_page', function( $options ) {
			$options['post_status'] = 'publish';   

			return $options;
		}, PHP_INT_MAX );
		
		add_filter( 'carbon_fields_gravity_form_options', function( $options ) {  
			// TODO: this is not working

			$options[0] = __( 'Please install the Gravity Forms plugin to select a form.', 'yodel-wp' );

			return $options;  
		}, PHP_INT_MAX );
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yodel_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yodel_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/yodel-wp-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yodel_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yodel_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/yodel-wp-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'yodelModalAdmin', array(
			'post_id'    => get_the_ID(),
			'post_title' => get_the_title(),
			'post_type'  => get_post_type(), 
		) );
	}

	public function register_post_types() {
		register_post_type( 'yodel-wp-modal', array(
			'labels'      => array(
				'name'                  => __( 'Modals', 'yodel-wp' ),
				'singular_name'         => __( 'Modal', 'yodel-wp' ),
				'menu_name'             => __( 'Modals', 'yodel-wp' ),
				'name_admin_bar'        => __( 'Modal', 'yodel-wp' ),
				'add_new'               => __( 'Add New Modal', 'yodel-wp' ),
				'add_new_item'          => __( 'Add New Modal', 'yodel-wp' ),
				'new_item'              => __( 'New Modal', 'yodel-wp' ),
				'edit_item'             => __( 'Edit Modal', 'yodel-wp' ),
				'view_item'             => __( 'View Modal', 'yodel-wp' ),
				'all_items'             => __( 'All Modals', 'yodel-wp' ),
				'search_items'          => __( 'Search Modals', 'yodel-wp' ),
				'parent_item_colon'     => __( 'Parent Modals:', 'yodel-wp' ),
				'not_found'             => __( 'No modals found.', 'yodel-wp' ), 
				'not_found_in_trash'    => __( 'No modals found in Trash.', 'yodel-wp' ),
				'featured_image'        => __( 'Modal Cover Image', 'yodel-wp' ),
				'archives'              => __( 'Modal archives', 'yodel-wp' ),
				'insert_into_item'      => __( 'Insert into modal', 'yodel-wp' ),
				'uploaded_to_this_item' => __( 'Uploaded to this modal', 'yodel-wp' ),
				'filter_items_list'     => __( 'Filter modals list', 'yodel-wp' ),
				'items_list_navigation' => __( 'Modals list navigation', 'yodel-wp' ),
				'items_list'            => __( 'Modals list', 'yodel-wp' ),
			),
			'public'      => false,
			'has_archive' => false,
			'show_ui'     => true,
			'show_in_menu' => false,
			'supports'    => array('title'),
			'capability_type' => 'post',
			'capabilities' => array(
				'edit_post' => 'manage_options',
				'edit_posts' => 'manage_options',
				'edit_others_posts' => 'manage_options',
				'publish_posts' => 'manage_options',
				'read_post' => 'manage_options',
				'read_private_posts' => 'manage_options',
				'delete_post' => 'manage_options'
			),
			'rewrite' => false,
			'query_var' => false
		));	 

		register_post_type( 'yodel-wp-topbar', array(
			'labels'      => array(
				'name'                  => __( 'Topbars', 'yodel-wp' ),
				'singular_name'         => __( 'Topbar', 'yodel-wp' ),
				'menu_name'             => __( 'Topbars', 'yodel-wp' ),
				'name_admin_bar'        => __( 'Topbar', 'yodel-wp' ),
				'add_new'               => __( 'Add New Topbar', 'yodel-wp' ),
				'add_new_item'          => __( 'Add New Topbar', 'yodel-wp' ),
				'new_item'              => __( 'New Topbar', 'yodel-wp' ),
				'edit_item'             => __( 'Edit Topbar', 'yodel-wp' ),
				'view_item'             => __( 'View Topbar', 'yodel-wp' ),
				'all_items'             => __( 'All Topbars', 'yodel-wp' ),
				'search_items'          => __( 'Search Topbars', 'yodel-wp' ),
				'parent_item_colon'     => __( 'Parent Topbars:', 'yodel-wp' ),
				'not_found'             => __( 'No topbars found.', 'yodel-wp' ), 
				'not_found_in_trash'    => __( 'No topbars found in Trash.', 'yodel-wp' ),
				'featured_image'        => __( 'Topbar Cover Image', 'yodel-wp' ),
				'archives'              => __( 'Topbar archives', 'yodel-wp' ),
				'insert_into_item'      => __( 'Insert into topbar', 'yodel-wp' ),
				'uploaded_to_this_item' => __( 'Uploaded to this topbar', 'yodel-wp' ),
				'filter_items_list'     => __( 'Filter topbars list', 'yodel-wp' ),
				'items_list_navigation' => __( 'Topbars list navigation', 'yodel-wp' ),
				'items_list'            => __( 'Topbars list', 'yodel-wp' ),
			),
			'public'      => false,
			'has_archive' => false,
			'show_ui'     => true,
			'show_in_menu' => false,
			'supports'    => array('title'),
			'capability_type' => 'post',
			'capabilities' => array(
				'edit_post' => 'manage_options',
				'edit_posts' => 'manage_options',
				'edit_others_posts' => 'manage_options',
				'publish_posts' => 'manage_options',
				'read_post' => 'manage_options',
				'read_private_posts' => 'manage_options',
				'delete_post' => 'manage_options'
			),
			'rewrite' => false,
			'query_var' => false
		));	 

		register_post_type( 'yodel-wp-submission', array(  
			'labels'      => array(
				'name'                  => __( 'Submissions', 'yodel-wp' ),
				'singular_name'         => __( 'Submission', 'yodel-wp' ),
				'menu_name'             => __( 'Submissions', 'yodel-wp' ),
				'name_admin_bar'        => __( 'Submission', 'yodel-wp' ),
				'add_new'               => __( 'Add New Submission', 'yodel-wp' ),
				'add_new_item'          => __( 'Add New Submission', 'yodel-wp' ),
				'new_item'              => __( 'New Submission', 'yodel-wp' ),
				'edit_item'             => __( 'Edit Submission', 'yodel-wp' ),
				'view_item'             => __( 'View Submission', 'yodel-wp' ),
				'all_items'             => __( 'All Submissions', 'yodel-wp' ),
				'search_items'          => __( 'Search Submissions', 'yodel-wp' ),
				'parent_item_colon'     => __( 'Parent Submissions:', 'yodel-wp' ),
				'not_found'             => __( 'No submissions found.', 'yodel-wp' ), 
				'not_found_in_trash'    => __( 'No submissions found in Trash.', 'yodel-wp' ),
				'featured_image'        => __( 'Submission Cover Image', 'yodel-wp' ),
				'archives'              => __( 'Submission archives', 'yodel-wp' ),
				'insert_into_item'      => __( 'Insert into submission', 'yodel-wp' ),
				'uploaded_to_this_item' => __( 'Uploaded to this submission', 'yodel-wp' ),
				'filter_items_list'     => __( 'Filter submissions list', 'yodel-wp' ),
				'items_list_navigation' => __( 'Submissions list navigation', 'yodel-wp' ),
				'items_list'            => __( 'Submissions list', 'yodel-wp' ),
			),
			'public'      => false,
			'has_archive' => false,
			'show_ui'     => true,
			'show_in_menu' => false,
			'supports'    => false,
			'capability_type' => 'post', 
			'capabilities' => array(
				'create_posts' => false,
				'edit_post' => 'manage_options',
				'edit_posts' => 'manage_options',
				'edit_others_posts' => 'manage_options',
				'publish_posts' => 'manage_options',
				'read_post' => 'manage_options',
				'read_private_posts' => 'manage_options',
				'delete_post' => 'manage_options'
			),
			'rewrite' => false,
			'query_var' => false
		));	
	}

	public function load_carbon_fields() {
		\Carbon_Fields\Carbon_Fields::boot();
	}

	public function register_admin_menu() {
		$icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mountain"><path d="m8 3 4 8 5-5 5 15H2L8 3z"/></svg>';

		add_menu_page(
			'Yodel Modal',
			'Yodel Modal', 
			'manage_options',
			'yodel-wp', 
			array( $this, 'admin_menu_overview_html' ),
			'data:image/svg+xml;base64,' . base64_encode( $icon ),
			99
		);  
 
		add_submenu_page( 'yodel-wp', 'Yodel WP Modals', 'Modals', 'manage_options', 'edit.php?post_type=yodel-wp-modal' );
		// add_submenu_page( 'yodel-wp', 'Yodel WP Topbars', 'Topbars', 'manage_options', 'edit.php?post_type=yodel-wp-topbar' );
		add_submenu_page( 'yodel-wp', 'Yodel WP Submissions', 'Submissions', 'manage_options', 'edit.php?post_type=yodel-wp-submission' );

		global $submenu;
		if (isset($submenu['yodel-wp'])) {
			$submenu['yodel-wp'][0][0] = 'Overview';
		}  

		remove_submenu_page( 'yodel-wp', 'yodel-wp' );
	}

	public function register_settings_fields() {	
		Container::make( 'theme_options', __( 'Settings', 'yodel-wp' ) )
			->set_page_parent( 'yodel-wp' )
			->set_page_file( 'yodel-wp-settings.php' ) 
			->add_tab( __( 'Theme', 'yodel-wp' ), array(
				Field::make( 'select', 'yodel_wp_theme_color_scheme', __( 'Color Scheme', 'yodel-wp' ) )
					->add_options( array(
						'yodel-theme-zinc' 	=> __( 'Zinc', 'yodel-wp' ),  
						'yodel-theme-slate' 	=> __( 'Slate', 'yodel-wp' ),
						'yodel-theme-red' 	=> __( 'Red', 'yodel-wp' ),
						'yodel-theme-orange' => __( 'Orange', 'yodel-wp' ),
						'yodel-theme-yellow' => __( 'Yellow', 'yodel-wp' ),
						'yodel-theme-green' 	=> __( 'Green', 'yodel-wp' ),
						'yodel-theme-blue' 	=> __( 'Blue', 'yodel-wp' ),
						'yodel-theme-violet' => __( 'Violet', 'yodel-wp' ), 
					) )
					->set_default_value( 'yodel-theme-zinc' ),  
				Field::make( 'textarea', 'yodel_wp_theme_color_variables', __( 'Custom Color Scheme', 'yodel-wp' ) )
					->set_help_text( 'Generate custom theme color scheme using tools like <a href="https://uicolorful.com/" target="_blank">UI Colorful</a>. This will override the color scheme setting.' ),
			) )
			->add_tab( __( 'Form', 'yodel-wp' ), array(
				Field::make( 'text', 'yodel_wp_form_success_message', __( 'Success message', 'yodel-wp' ) )
					->set_default_value( 'Thanks for your submission!' )
					->set_required( true ), 
				Field::make( 'text', 'yodel_wp_form_error_message', __( 'Error message', 'yodel-wp' ) )
					->set_default_value( 'There was an error with your submission. Please try again.' )
					->set_required( true ),
				Field::make( 'checkbox', 'yodel_wp_form_business_email_only', __( 'Business Email Only', 'yodel-wp' ) )
					->set_default_value( false )  
					->set_help_text( 'If enabled, only business email addresses will be allowed to submit the form.' ),
			) )
			->add_tab( __( 'Email', 'yodel-wp' ), array(
				Field::make( 'text', 'yodel_wp_email_to', __( 'To', 'yodel-wp' ) )
					->set_default_value( get_option('admin_email') )
					->set_required( true ),
				Field::make( 'text', 'yodel_wp_email_from', __( 'From', 'yodel-wp' ) )
					->set_default_value( get_option( 'blogname' ) . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>' )
					->set_required( true ),
				Field::make( 'text', 'yodel_wp_email_subject', __( 'Subject', 'yodel-wp' ) )
					->set_default_value( 'New Form Submission' )
					->set_required( true ),
				Field::make( 'complex', 'yodel_wp_email_headers', __( 'Headers', 'yodel-wp' ) )
					->setup_labels( array(
						'plural_name' => 'Headers',
						'singular_name' => 'Header',
					) )
					->add_fields( array(
						Field::make( 'text', 'header_name', __( 'Name' ) ), 
						Field::make( 'text', 'header_value', __( 'Value' ) )
					) )
			) ) 
			->add_tab( __( 'Integration', 'yodel-wp' ), array(
				Field::make( 'header_scripts', 'yodel_wp_header_scripts', __( 'Custom Header Scripts', 'yodel-wp' ) )
					->set_hook_priority( PHP_INT_MAX )
					->set_help_text( '' ),
				Field::make( 'footer_scripts', 'yodel_wp_footer_scripts', __( 'Custom Footer Scripts', 'yodel-wp' ) )
					->set_help_text( '' ),
			) );
	}

	public function register_modal_fields() {	
		$container = Container::make( 'post_meta', __( ' ', 'yodel-wp' ) )
			->where( 'post_type', '=', 'yodel-wp-modal' )
			->add_fields( array(
				Field::make( 'complex', 'yodel_wp_layout', __( ' ', 'yodel-wp' ) ) 
					->setup_labels( array( 
						'plural_name' => 'Layouts',
						'singular_name' => 'Layout',
					) )
					->set_min( 1 ) 
					->set_max( 1 )
					->set_required( true ) 
					->add_fields( 'layout_1', __( 'One Column', 'yodel-wp' ), array(  
						Field::make( 'image', 'image', __( 'Image', 'yodel-wp' ) ),
						Field::make( 'text', 'title', __( 'Title', 'yodel-wp' ) ),
						Field::make( 'rich_text', 'content', __( 'Content', 'yodel-wp' ) ),
						Field::make( 'complex', 'buttons', __( 'Buttons', 'yodel-wp' ) ) 
							->setup_labels( array( 
								'plural_name' => 'Buttons',
								'singular_name' => 'Button',
							) )
							->set_max( 2 )
							->add_fields( array(  
								Field::make( 'select', 'type', __( 'Type', 'yodel-wp' ) )
									->add_options( array(
										'link' => __( 'Link', 'yodel-wp' ),
										'close' => __( 'Close Button', 'yodel-wp' ),
									) ),
								Field::make( 'select', 'variant', __( 'Variant', 'yodel-wp' ) )
									->add_options( array(
										'default' => __( 'Default', 'yodel-wp' ),
										'destructive' => __( 'Destructive', 'yodel-wp' ),
										'outline' => __( 'Outline', 'yodel-wp' ),
										'secondary' => __( 'Secondary', 'yodel-wp' ),
										'ghost' => __( 'Ghost', 'yodel-wp' ),
										'link' => __( 'Link', 'yodel-wp' ),
									) ),
								Field::make( 'text', 'title', __( 'Title', 'yodel-wp' ) ),
								Field::make( 'text', 'url', __( 'URL', 'yodel-wp' ) )
									->set_conditional_logic( array( 
										'relation' => 'AND',
										array(
											'field' => 'type',
											'value' => 'link',
											'compare' => '=',
										)
									) ),
								Field::make( 'select', 'target', __( 'Target', 'yodel-wp' ) )
									->set_conditional_logic( array( 
										'relation' => 'AND',
										array(
											'field' => 'type',
											'value' => 'link',
											'compare' => '=',
										)
									) )
									->add_options( array(
										'_self' => __( '_self (current window)', 'yodel-wp' ),
										'_blank' => __( '_blank (new window)', 'yodel-wp' ),
									) )
							) ), 
					) ) 
					->add_fields( 'layout_2', __( 'One Column w/ Form', 'yodel-wp' ), array(  
						Field::make( 'text', 'title', __( 'Title', 'yodel-wp' ) ),
						Field::make( 'rich_text', 'content', __( 'Content', 'yodel-wp' ) ),
						Field::make( 'rich_text', 'form_before', __( 'Before Form' ) )
							->set_default_value( 'Please fill out the form below to get started' )
							->set_help_text( 'This will be displayed above the form.' ), 
						Field::make( 'rich_text', 'form_after', __( 'After Form' ) )
							->set_default_value( 'By submitting this form, you consent to join our mailing list. You may unsubscribe at any time. We are dedicated to protecting your privacy and will not sell, rent, or share your information with any third parties.' )
							->set_help_text( 'This will be displayed below the form.' ),
						Field::make( 'image', 'background_image', __( 'Background Image', 'yodel-wp' ) )
					) )  
					->add_fields( 'layout_3', __( 'Two Columns w/ Form', 'yodel-wp' ), array(  
						Field::make( 'text', 'title', __( 'Title', 'yodel-wp' ) ),
						Field::make( 'rich_text', 'content', __( 'Content', 'yodel-wp' ) ),
						Field::make( 'complex', 'buttons', __( 'Buttons', 'yodel-wp' ) ) 
							->setup_labels( array( 
								'plural_name' => 'Buttons',
								'singular_name' => 'Button',
							) )
							->set_max( 2 )
							->add_fields( array(  
								Field::make( 'select', 'type', __( 'Type', 'yodel-wp' ) )
									->add_options( array(
										'link' => __( 'Link', 'yodel-wp' ),
										'close' => __( 'Close Button', 'yodel-wp' ),
									) ),
								Field::make( 'select', 'variant', __( 'Variant', 'yodel-wp' ) )
									->add_options( array(
										'default' => __( 'Default', 'yodel-wp' ),
										'destructive' => __( 'Destructive', 'yodel-wp' ),
										'outline' => __( 'Outline', 'yodel-wp' ),
										'secondary' => __( 'Secondary', 'yodel-wp' ),
										'ghost' => __( 'Ghost', 'yodel-wp' ),
										'link' => __( 'Link', 'yodel-wp' ),
									) ),
								Field::make( 'text', 'title', __( 'Title', 'yodel-wp' ) ),
								Field::make( 'text', 'url', __( 'URL', 'yodel-wp' ) )
									->set_conditional_logic( array( 
										'relation' => 'AND',
										array(
											'field' => 'type',
											'value' => 'link',
											'compare' => '=',
										)
									) ),
								Field::make( 'select', 'target', __( 'Target', 'yodel-wp' ) )
									->set_conditional_logic( array( 
										'relation' => 'AND',
										array(
											'field' => 'type',
											'value' => 'link',
											'compare' => '=',
										)
									) )
									->add_options( array(
										'_self' => __( '_self (current window)', 'yodel-wp' ),
										'_blank' => __( '_blank (new window)', 'yodel-wp' ),
									) )
							) ),  
							Field::make( 'rich_text', 'form_before', __( 'Before Form' ) )
								->set_default_value( 'Please fill out the form below to get started' )
								->set_help_text( 'This will be displayed above the form.' ), 
							Field::make( 'rich_text', 'form_after', __( 'After Form' ) )
								->set_default_value( 'By submitting this form, you consent to join our mailing list. You may unsubscribe at any time. We are dedicated to protecting your privacy and will not sell, rent, or share your information with any third parties.' )
								->set_help_text( 'This will be displayed below the form.' ),
							Field::make( 'image', 'background_image', __( 'Background Image', 'yodel-wp' ) ),
					) ) 
					->add_fields( 'layout_4', __( 'Two Columns w/ Image', 'yodel-wp' ), array(  
						Field::make( 'text', 'title', __( 'Title', 'yodel-wp' ) ),
						Field::make( 'rich_text', 'content', __( 'Content', 'yodel-wp' ) ),
						Field::make( 'complex', 'buttons', __( 'Buttons', 'yodel-wp' ) ) 
							->setup_labels( array( 
								'plural_name' => 'Buttons',
								'singular_name' => 'Button',
							) )
							->set_max( 2 )
							->add_fields( array(  
								Field::make( 'select', 'type', __( 'Type', 'yodel-wp' ) )
									->add_options( array(
										'link' => __( 'Link', 'yodel-wp' ),
										'close' => __( 'Close Button', 'yodel-wp' ),
									) ),
								Field::make( 'select', 'variant', __( 'Variant', 'yodel-wp' ) )
									->add_options( array(
										'default' => __( 'Default', 'yodel-wp' ),
										'destructive' => __( 'Destructive', 'yodel-wp' ),
										'outline' => __( 'Outline', 'yodel-wp' ),
										'secondary' => __( 'Secondary', 'yodel-wp' ),
										'ghost' => __( 'Ghost', 'yodel-wp' ),
										'link' => __( 'Link', 'yodel-wp' ),
									) ),
								Field::make( 'text', 'title', __( 'Title', 'yodel-wp' ) ),
								Field::make( 'text', 'url', __( 'URL', 'yodel-wp' ) )
									->set_conditional_logic( array( 
										'relation' => 'AND',
										array(
											'field' => 'type',
											'value' => 'link',
											'compare' => '=',
										)
									) ),
								Field::make( 'select', 'target', __( 'Target', 'yodel-wp' ) )
									->set_conditional_logic( array( 
										'relation' => 'AND',
										array(
											'field' => 'type',
											'value' => 'link',
											'compare' => '=',
										)
									) )
									->add_options( array(
										'_self' => __( '_self (current window)', 'yodel-wp' ),
										'_blank' => __( '_blank (new window)', 'yodel-wp' ),
									) )
							) ),  
							Field::make( 'image', 'image', __( 'Image', 'yodel-wp' ) ),
					) ) 

					// ->add_fields( 'content', __( 'Content', 'yodel-wp' ), array(  
					// 	Field::make( 'rich_text', 'content', __( 'Content', 'yodel-wp' ) )
					// 		->set_required( true )
					// ) )
					// ->add_fields( 'file', __( 'Download', 'yodel-wp' ), array(  
					// 	Field::make( 'file', 'file', __( 'file', 'yodel-wp' ) )
					// 		->set_required( true )
					// ) )
					// ->add_fields( 'image', __( 'Image', 'yodel-wp' ), array( 
					// 	Field::make( 'image', 'image', __( 'image', 'yodel-wp' ) )  
					// 		->set_required( true )
					// ) )
					// ->add_fields( 'video', __( 'Video', 'yodel-wp' ), array( 
					// 	Field::make( 'oembed', 'embed', __( 'embed', 'yodel-wp' ) )
					// 		->set_required( true )
					// 		->set_help_text( 'Only YouTube video embeds are supported. e.g. https://www.youtube.com/embed/xxxxxxxxxxxx' ),
					// ) ),
			) );

			// yodel_wp_layout|||0|value = layout_2
			
		Container::make( 'post_meta', __( 'Settings', 'yodel-wp' ) ) 
			->where( 'post_type', '=', 'yodel-wp-modal' )   
			->add_tab( __( 'Display', 'yodel-wp' ), array(
				Field::make( 'association', 'yodel_wp_displayed_at', __( 'Display on Specific Pages' ) )
					->set_types( array( 
						array(
							'type'      => 'post',
							'post_type' => 'page',
						)
					) )
					->set_help_text( 'Select the pages where this modal should be displayed. If no pages are selected, this modal will be displayed on all pages.' ),
				Field::make( 'radio', 'yodel_wp_initialization', __( 'Modal Initialization', 'yodel-wp' ) )
					->add_options( array(
						'button' => __( 'Button', 'yodel-wp' ),
						'timer' => __( 'Timer', 'yodel-wp' ),
						'exit_intent' => __( 'Exit Intent', 'yodel-wp' ),
					) ),
				Field::make( 'text', 'yodel_wp_shortcode', __( 'Button Shortcode' ) )
					->set_conditional_logic( array( 
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_initialization',
							'value' => 'button',
							'compare' => '=',
						)
					) )
					// ->set_attribute( 'readOnly', 'readOnly' )
					->set_default_value( '[yodel-wp-button id="" type="modal" title=""]' ), 
				Field::make( 'text', 'yodel_wp_delay', __( 'Delay (seconds)' ) )
					->set_conditional_logic( array( 
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_initialization',
							'value' => 'timer',
							'compare' => '=',
						)
					) )
					->set_attribute( 'type', 'number' )
					->set_default_value( 3 ),
				Field::make( 'text', 'yodel_wp_dismissal_expiration', __( 'Dismissal Expiration (hours)' ) )
					->set_conditional_logic( array( 
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_initialization',
							'value' => ['timer', 'exit_intent'],
							'compare' => 'IN',
						)
					) )
					->set_attribute( 'type', 'number' )
					->set_default_value( 0 )
					->set_help_text( 'This sets the expiration of the dismissal cookie. Empty = no cookie, 0 = session only, 1+ = hours.' ),
			) )
			->add_tab( __( 'Theme', 'yodel-wp' ), array(
				Field::make( 'select', 'yodel_wp_color_scheme', __( 'Color Scheme', 'yodel-wp' ) )
					->add_options( array(
						'yodel-theme-zinc' 	=> __( 'Zinc', 'yodel-wp' ),  
						'yodel-theme-slate' 	=> __( 'Slate', 'yodel-wp' ),
						'yodel-theme-red' 	=> __( 'Red', 'yodel-wp' ),
						'yodel-theme-orange' => __( 'Orange', 'yodel-wp' ),
						'yodel-theme-yellow' => __( 'Yellow', 'yodel-wp' ),
						'yodel-theme-green' 	=> __( 'Green', 'yodel-wp' ),
						'yodel-theme-blue' 	=> __( 'Blue', 'yodel-wp' ),
						'yodel-theme-violet' => __( 'Violet', 'yodel-wp' ), 
					) )
					->set_default_value( carbon_get_theme_option('yodel_wp_theme_color_scheme') ),    
				Field::make( 'textarea', 'yodel_wp_color_variables', __( 'Custom Color Scheme', 'yodel-wp' ) )
					->set_help_text( 'Generate custom theme color scheme using tools like <a href="https://uicolorful.com/" target="_blank">UI Colorful</a>. This will override the color scheme setting.' ),
			) )
			->add_tab( __( 'Form', 'yodel-wp' ), array(
				Field::make( 'radio', 'yodel_wp_form_type', __( 'Form Type', 'yodel-wp' ) )
					->add_options( array(
						'default_form' => __( 'Simple Form (Name, Email, Message)', 'yodel-wp' ),
						'cf7_form' => __( 'Contact Form 7', 'yodel-wp' ),
						// 'gravity_form' => __( 'Gravity Forms (coming soon)', 'yodel-wp' ),
					) ),				
				Field::make( 'association', 'yodel_wp_form_cf7', __( 'Contact Form 7', 'yodel-wp' ) )
					->set_conditional_logic( array( 
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_form_type',  
							'value' => 'cf7_form',
							'compare' => '=',
						)
					) )
					->set_types( array( 
						array(
							'type'      => 'post',
							'post_type' => 'wpcf7_contact_form', 
						)
					) )
					->set_max( 1 )
					->set_help_text( 'Please install the Contact Form 7 plugin to select a form. <a href="https://contactform7.com/" target="_blank">Get it here</a>' ),
				Field::make( 'checkbox', 'yodel_wp_form_disabled', __( 'Form Disabled', 'yodel-wp' ) )
					->set_conditional_logic( array(  
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_form_type',  
							'value' => ['cf7_form'], 
							'compare' => 'IN', 
						)
					) ),
				// Field::make( 'gravity_form', 'yodel_wp_form', __( 'Gravity Form', 'yodel-wp' ) )
				// 	->set_conditional_logic( array( 
				// 		'relation' => 'AND',
				// 		array(
				// 			'field' => 'yodel_wp_form_type',  
				// 			'value' => 'gravity_form',
				// 			'compare' => '=',
				// 		)
				// 	) )
				// 	->set_help_text( 'Please install the Gravity Forms plugin to select a form. <a href="https://www.gravityforms.com/" target="_blank">Get it here</a>' ),
				Field::make( 'multiselect', 'yodel_wp_form_fields', __( 'Form Fields' ) )
					->set_conditional_logic( array( 
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_form_type',  
							'value' => 'default_form',
							'compare' => '=',
						)
					) ) 
					->add_options( array(
						'given_name' => __( 'First Name' ),
						'family_name' => __( 'Last Name' ),
						'phone' => __( 'Phone Number' ), 
						'message' => __( 'Message' ),
					) ),
				Field::make( 'radio', 'yodel_wp_form_submission_type', __( 'Submission Type' ) )
					->set_conditional_logic( array( 
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_form_type',  
							'value' => 'default_form',
							'compare' => '=',
						)
					) )
					->add_options( array(
						'database' => __( 'Database' ),
						'email' => __( 'Email' ),
						'both' => __( 'Both' ), 
					) )
					->set_help_text( 'This is where the form data will be submitted to.' ),
				Field::make( 'text', 'yodel_wp_form_success_message', __( 'Success message', 'yodel-wp' ) )
					->set_conditional_logic( array( 
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_form_type',  
							'value' => 'default_form',
							'compare' => '=',
						)
					) )
					->set_default_value( 'Thanks for your submission!' ) 
					->set_required( true ),
				Field::make( 'text', 'yodel_wp_form_error_message', __( 'Error message', 'yodel-wp' ) )
					->set_conditional_logic( array( 
						'relation' => 'AND',
						array(
							'field' => 'yodel_wp_form_type',  
							'value' => 'default_form',
							'compare' => '=',
						)
					) )
					->set_default_value( 'There was an error with your submission. Please try again.' )
					->set_required( true ),
				Field::make( 'text', 'yodel_wp_form_success_redirect_url', __( 'Success redirect URL' ) )
					->set_help_text( 'This is the URL to redirect to after the form is successfully submitted. Empty = no redirect.' ),
			) )
			->add_tab( __( 'Email', 'yodel-wp' ), array(
				Field::make( 'text', 'yodel_wp_email_to', __( 'To', 'yodel-wp' ) )
					->set_default_value( get_option('admin_email') )
					->set_required( true ),
				Field::make( 'text', 'yodel_wp_email_from', __( 'From', 'yodel-wp' ) )
					->set_default_value( get_option( 'blogname' ) . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>' )
					->set_required( true ),
				Field::make( 'text', 'yodel_wp_email_subject', __( 'Subject', 'yodel-wp' ) )
					->set_default_value( 'New Form Submission' )
					->set_required( true ),
				Field::make( 'complex', 'yodel_wp_email_headers', __( 'Headers', 'yodel-wp' ) )
					->setup_labels( array(
						'plural_name' => 'Headers',
						'singular_name' => 'Header',
					) )
					->add_fields( array(
						Field::make( 'text', 'header_name', __( 'Name' ) ), 
						Field::make( 'text', 'header_value', __( 'Value' ) )
					) )
			) );
	}

	public function register_submission_fields() {	
		$container = Container::make( 'post_meta', __( ' ', 'yodel-wp' ) ) 
			->where( 'post_type', '=', 'yodel-wp-submission' )
			->add_fields( array(
				Field::make( 'text', 'yodel_wp_email', __( 'Email Address', 'yodel-wp' ) )
					->set_attribute( 'readOnly', 'readOnly' ),
				Field::make( 'text', 'yodel_wp_given_name', __( 'First Name', 'yodel-wp' ) )
					->set_attribute( 'readOnly', 'readOnly' ),
				Field::make( 'text', 'yodel_wp_family_name', __( 'Last Name', 'yodel-wp' ) )
					->set_attribute( 'readOnly', 'readOnly' ),
				Field::make( 'text', 'yodel_wp_organization', __( 'Organization', 'yodel-wp' ) )
					->set_attribute( 'readOnly', 'readOnly' ),
				Field::make( 'textarea', 'yodel_wp_meta', __( 'Meta', 'yodel-wp' ) )
					->set_attribute( 'readOnly', 'readOnly' ),  
				Field::make( 'text', 'yodel_wp_referrer', __( 'Referrer', 'yodel-wp' ) ) 
					->set_attribute( 'readOnly', 'readOnly' ), 
				Field::make( 'text', 'yodel_wp_parent_id', __( 'Parent ID', 'yodel-wp' ) ) 
					->set_attribute( 'readOnly', 'readOnly' ), 
			) );
	}
	 
	public function admin_menu_overview_html() { 
		?>
			<div class="wrap">
				<h1><?php echo get_admin_page_title() ?></h1>
			</div>
		<?php 
	}

	public function admin_head_scripts() {
		if (get_post_type() != 'yodel-wp-modal') { 
			return; 
		}
		
		echo '
		<style type="text/css">
			.cf-field.cf-complex:first-of-type > .cf-field__head { 
				display: none;  
			}
		</style> 
		';
	}

	public function console_log( $output, $with_script_tags = true ) {
        $output = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
        
        if ( $with_script_tags ) {
            $output = '<script>' . $output . '</script>';
        }

        echo $output;
    } 
}