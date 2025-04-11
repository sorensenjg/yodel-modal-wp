<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.sorensenjg.com
 * @since      1.0.0
 *
 * @package    Yodel_Wp
 * @subpackage Yodel_Wp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Yodel_Wp
 * @subpackage Yodel_Wp/public
 * @author     @sorensenjg <hey@sorensenjg.com>
 */
class Yodel_Wp_Public {

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

        add_action( 'init', array( $this, 'register_shortcodes' ) );
        add_action( 'wp_footer', array( $this, 'render_react_container' ) );
        add_action( 'wp_ajax_yodel_form_submit', array( $this, 'handle_form_submission' ) );
        add_action( 'wp_ajax_nopriv_yodel_form_submit', array( $this, 'handle_form_submission' ) ); 
        add_action( 'wp_ajax_yodel_akismet_check_spam', array( $this, 'akismet_check_spam' ) );
        add_action( 'wp_ajax_nopriv_yodel_akismet_check_spam', array( $this, 'akismet_check_spam' ) );
        add_filter( 'wpcf7_spam', array( $this, 'bypass_wpcf7_spam' ) );
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
		 * defined in Yodel_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yodel_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/yodel-wp-public.css', array(), $this->version, 'all' );

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
		 * defined in Yodel_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yodel_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$build_dir = plugin_dir_path(__FILE__) . 'build/';
        $build_url = plugins_url('build/', __FILE__);

        if (!is_dir($build_dir)) {
            return;
        }

        $asset_file = file_exists($build_dir . 'index.asset.php') ? include($build_dir . 'index.asset.php') : null;
        

        $files = scandir($build_dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $file_path = $build_dir . $file;
            $file_url = $build_url . $file;

            if (is_file($file_path)) {
                $file_info = pathinfo($file);
                $ext = $file_info['extension'];
                $filename = $file_info['filename'];
                $handle = "{$this->plugin_name}-{$filename}";     
                
                if ($ext === 'js') {
                    $deps = $asset_file['dependencies'] ?? [];
                    $version = $asset_file['version'] ?? filemtime($file_path);

                    wp_enqueue_script($handle, $file_url, $deps, $version, true);
                } elseif ($ext === 'css') {
                    wp_enqueue_style($handle, $file_url, [], filemtime($file_path));
                }
            }
        } 

        $modals = $this->get_modals();

        $theme_color_scheme = carbon_get_theme_option('yodel_wp_theme_color_scheme');
        $theme_color_variables = carbon_get_theme_option('yodel_wp_theme_color_variables');
        $form_business_email_only = carbon_get_theme_option('yodel_wp_form_business_email_only');

        wp_localize_script($this->plugin_name . '-index', 'yodelWp', [
            'config' => array(
                'nonce'             => wp_create_nonce('yodel-nonce'),
                'baseUrl'           => get_site_url(),
                'ajaxUrl'           => admin_url('admin-ajax.php'),   
                'containerId'       => 'yodel-wp-container',
                'isUserAdmin'       => in_array('administrator',  wp_get_current_user()->roles),
                'isUserLoggedIn'    => is_user_logged_in(),
                'akismetEnabled'   => function_exists('akismet_init') && class_exists('Akismet'),  
            ),
            'modals' => $modals, 
            'settings' => array(
                'theme' => array(
                    'color_scheme'      => $theme_color_scheme,
                    'color_variables'   => $theme_color_variables
                ),
                'form' => array(
                    'businessEmailOnly' => $form_business_email_only,
                ), 
            ), 
        ]);

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/yodel-wp-public.js', array( 'jquery' ), $this->version, false );

	}

    public function render_react_container() {
        ?>
            <div id="yodel-wp-container"></div>
        <?php 
    }
    
    public function handle_form_submission() { 
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'yodel-nonce')) {
            wp_send_json_error('Yodel nonce is invalid', array(
                'nonce' => $_POST['nonce'],
                'expected' => wp_create_nonce('yodel-nonce'),
            )); 
        }  

        $post_id = $_POST['post_id'];
        $submission_type = $_POST['submission_type'];  
        
        if (!$post_id || !$submission_type) {
            wp_send_json_error('Missing required parameters');
            return;
        }

        // Remove 'action', 'nonce', 'post_id', and 'submission_type' from the form data
        $form_data = array_diff_key($_POST, array_flip(['action', 'nonce', 'post_id', 'submission_type'])); 
        $success = $this->process_submission($submission_type, $post_id, $form_data);  

        if ( $success ) {   
            wp_send_json_success('Form submitted successfully');
        } else {
            wp_send_json_error('Form submission failed');  
        }
    }

    private function process_submission($submission_type, $post_id, $form_data) {
        switch ($submission_type) {
            case 'email':
                return $this->send_email($post_id, $form_data);
            case 'database':
                return $this->save_record($post_id, $form_data);
            case 'both':
                return $this->send_email($post_id, $form_data) && $this->save_record($post_id, $form_data);
            default:
                return false;
        }
    }

    private function send_email($post_id, $form_data) {       
        $to = carbon_get_post_meta($post_id, 'yodel_wp_email_to');
        $from = carbon_get_post_meta($post_id, 'yodel_wp_email_from');
        $subject = carbon_get_post_meta($post_id, 'yodel_wp_email_subject');  
        
        $headers = array();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $from;
        // foreach (carbon_get_post_meta($id, 'yodel_wp_email_headers') as $header) {
        //     $headers[] = $header['header_name'] . ': ' . $header['header_value'];
        // }  

        $body = '<div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f9f9f9; padding: 20px;">';
        $body .= '<h1 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">New Form Submission</h1>';
        $body .= '<div style="background-color: #ffffff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">';

        foreach ($form_data as $key => $value) { 
            $body .= '<p><strong style="color: #2c3e50;">' . ucfirst($key) . ':</strong> ' . $value . '</p>'; 
        } 

        $body .= '</div>'; 
        $body .= '<p style="font-size: 12px; color: #7f8c8d; text-align: center; margin-top: 20px;">Created using <a href="https://useyodel.com" style="color: #3498db; text-decoration: none;">Yodel</a></p>'; 
        $body .= '</div>'; 

        $mail_sent = wp_mail($to, $subject, $body, $headers); 
        
        return $mail_sent; 
    }

    private function save_record($parent_id, $form_data) {
        $parent = get_post($parent_id);

        $post_data = array(
            'post_type'     => 'yodel-wp-submission',
            'post_status'   => 'publish',
            'post_date'     => date('Y-m-d H:i:s'),
            'post_title'    => 'Submission from ' . $form_data['given_name'] . ' ' . $form_data['family_name'] . ' <' . $form_data['email'] . '>',
        ); 

        $post_id = wp_insert_post($post_data);

        if( $post_id ) { 
            $meta_data = array_diff_key($form_data, array_flip(['email', 'given_name', 'family_name', 'organization', 'referrer'])); 
            
            carbon_set_post_meta( $post_id, 'yodel_wp_email', $form_data['email'] ); 
            carbon_set_post_meta( $post_id, 'yodel_wp_given_name', $form_data['given_name'] );
            carbon_set_post_meta( $post_id, 'yodel_wp_family_name', $form_data['family_name'] );
            carbon_set_post_meta( $post_id, 'yodel_wp_organization', $form_data['organization'] );  
            carbon_set_post_meta( $post_id, 'yodel_wp_meta', json_encode($meta_data, JSON_PRETTY_PRINT));
            carbon_set_post_meta( $post_id, 'yodel_wp_referrer', $form_data['referrer'] ); 
            carbon_set_post_meta( $post_id, 'yodel_wp_parent_id', $parent_id ); 
        }
        
        return $post_id !== false;   
    }   
    
    private function get_modals() {
        $posts = get_posts( array(
            'post_type' => 'yodel-wp-modal',
            'post_status' => array('publish', 'private'), 
            'numberposts' => -1, 
        ) );

        $modals = array();
        foreach ( $posts as $post ) {
            $modal = array(
                'id' => $post->ID,
                'status' => $post->post_status,
            );
            
            $layout = $this->get_layout_object($post->ID); 
            $modal = array_merge($modal, $layout);   

            $form = $this->get_form_object($post->ID, $modal['layout']);   
            $modal['form'] = $form; 

            $settings = $this->get_settings_object($post->ID);   
            $modal['settings'] = $settings; 

            $modals[] = $modal;
        }

        return $modals;
    }

    private function get_layout_object($post_id) { 
        $layout = array();

        $data = carbon_get_post_meta($post_id, 'yodel_wp_layout')[0] ?? [];

        $columns = $this->get_layout_columns($data['_type']); 

        $layout['layout'] = $data['_type']; 
        $layout['columns'] = $columns;
        $layout['title'] = apply_filters( 'yodel_modal_title', $data['title'] ?? '', $post_id ); 

        // Apply content filters
        $content_fields = ['content', 'form_before', 'form_after'];
        foreach ($content_fields as $field) {
            if (isset($data[$field])) {
                $content = apply_filters('the_content', $data[$field]);  

                switch ($field) {
                    case 'content':
                        $content = apply_filters( 'yodel_modal_content', $content, $post_id );      
                        break;

                    case 'form_before':
                        $content = apply_filters( 'yodel_modal_form_before', $content, $post_id );      
                        break;

                    case 'form_after':
                        $content = apply_filters( 'yodel_modal_form_after', $content, $post_id );      
                        break; 
                }      
                
                $layout[$field] = $content;  
            }
        }

        // Process buttons
        $layout['buttons'] = $this->process_buttons($data['buttons'] ?? array());    

        // Process image
        $layout['image'] = $this->process_image($data['image'] ?? null); 

        // Process background image
        $layout['background_image'] = $this->process_image($data['background_image'] ?? null);

        return $layout;   
    }

    private function get_layout_columns($layout) { 
        $column_map = array(
            'layout_1' => 1,
            'layout_2' => 2,
            'layout_3' => 2, 
            'layout_4' => 2,
        );
        return $column_map[$layout] ?? 1;
    } 

    private function get_form_object($post_id) { 
        $form = array();
        
        $form['form_type'] = carbon_get_post_meta($post_id, 'yodel_wp_form_type');  
           
        switch ($form['form_type']) {   
            case 'cf7_form': 
                $form['form_id'] = carbon_get_post_meta($post_id, 'yodel_wp_form_cf7')[0]['id'] ?? null;    
                $form['form_fields'] = $this->get_cf7_form($form['form_id'])['fields'];   
                $form['messages'] = $this->get_cf7_form($form['form_id'])['messages'];
                break; 

            default:
                $form['form_id'] = null; 
                $form['form_fields'] = carbon_get_post_meta($post_id, 'yodel_wp_form_fields');
                $form['messages']['success'] = carbon_get_post_meta($post_id, 'yodel_wp_form_success_message');
                $form['messages']['error'] = carbon_get_post_meta($post_id, 'yodel_wp_form_error_message');
                break;
        } 

        $form['form_disabled'] = carbon_get_post_meta($post_id, 'yodel_wp_form_disabled');  
        $form['submission_type'] = carbon_get_post_meta($post_id, 'yodel_wp_form_submission_type');
        $form['redirects']['success'] = carbon_get_post_meta($post_id, 'yodel_wp_form_success_redirect_url');
        
        return $form;
    }

    private function get_cf7_form($form_id) {
        if (!$form_id || !function_exists('wpcf7_contact_form')) {
            return array();
        }
    
        $cf7_form = wpcf7_contact_form($form_id);
        if (!$cf7_form) {
            return array();
        }
    
        
        $properties = $cf7_form->get_properties();
        $form_tags = $cf7_form->scan_form_tags();
    
        $form_fields = array();
        foreach ($form_tags as $tag) {
            if (!empty($tag['name'])) {

                $processed_options = array();
                if (!empty($tag['options'])) {
                    foreach ($tag['options'] as $option) {
                        $parts = explode(':', $option);
                        if (count($parts) === 2) {
                            $processed_options[trim($parts[0])] = trim($parts[1]);
                        } else {
                            $processed_options[$option] = $option;
                        }
                    }
                }

                $form_fields[] = array(
                    'name' => $tag['name'],
                    'type' => str_replace('*', '', $tag['type']),
                    'required' => strpos($tag['type'], '*') !== false,
                    'options' => $processed_options,
                    'label' => ucwords(str_replace(['_', '-'], ' ', $tag['name'])),
                );
            }
        }

        $messages = array(
            'success' => $properties['messages']['mail_sent_ok'],
            'error' => $properties['messages']['mail_sent_ng'],
        );

        return array( 
            'fields' => $form_fields, 
            'messages' => $messages 
        ); 
    }

    private function get_settings_object($post_id) {
        $settings = array();

        $displayed_at = array();
        foreach (carbon_get_post_meta($post_id, 'yodel_wp_displayed_at') as $page) {
            $page_uri = parse_url( get_permalink($page['id']), PHP_URL_PATH );
            $displayed_at[] = $page_uri; 
        }
        $settings['display']['displayed_at'] = $displayed_at;  

        $initialization = carbon_get_post_meta($post_id, 'yodel_wp_initialization');
        $settings['display']['initialization'] = $initialization; 

        $delay = carbon_get_post_meta($post_id, 'yodel_wp_delay');
        $settings['display']['delay'] = $delay;

        $dismissal_expiration = carbon_get_post_meta($post_id, 'yodel_wp_dismissal_expiration');
        $settings['display']['dismissal_expiration'] = $dismissal_expiration;

        $color_scheme = carbon_get_post_meta($post_id, 'yodel_wp_color_scheme');
        $settings['theme']['color_scheme'] = $color_scheme;

        $color_variables = carbon_get_post_meta($post_id, 'yodel_wp_color_variables'); 
        $settings['theme']['color_variables'] = $color_variables;  
        
        return $settings;
    }

    private function process_buttons($buttons) {
        $processed = array();

        foreach ($buttons as &$button) { 
            $button_type = $button['type'];
            
            $processed[] = array(
                'type' => $button_type,
                'variant' => $button['variant'],
                'title' => $button['title'],
                'url' => $button_type === 'close' ? null : esc_url($button['url']),
                'target' => $button_type === 'close' ? null : $button['target'],
            );
        } 

        return $processed;
    }

    private function process_image($image_id) {        
        if( !$image_id ) {
            return null;
        }

        $image_arr = wp_get_attachment_image_src($image_id, 'full');
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true); 
        $image_meta = wp_get_attachment_metadata($image_id, true); 

        if (!$image_arr) {
            return null;
        }

        return [
            'src'    => $image_arr[0],
            'alt'    => $image_alt ?? '', 
            'width'  => $image_arr[1], 
            'height' => $image_arr[2],
        ];
    }

    public function register_shortcodes(){
        add_shortcode('yodel-wp-button', array($this, 'render_button'));
    }

    public function render_button($atts) { 
        $atts = shortcode_atts(array(
            'id' => null, 
            'type' => null, 
            'title' => 'Get Started',
        ), $atts, 'yodel-wp-button');     

        ob_start();
        ?>       
        <button class="yodel-wp-button" data-id="<?php echo esc_attr($atts['id']); ?>" data-type="<?php echo esc_attr($atts['type']); ?>"><?php echo esc_html($atts['title']); ?></button>
        <?php
        return ob_get_clean();
    } 

    function akismet_check_spam() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'yodel-nonce')) {
            wp_send_json_error('Yodel nonce is invalid'); 
        } 
    
        // Check if Akismet is available and active
        if (!function_exists('akismet_init') || !class_exists('Akismet')) {
            wp_send_json(array(
                'enabled' => false,
                'is_spam' => false,
                'message' => 'Akismet not enabled'
            ));
            return;
        } 
    
         // Prepare comment data for Akismet check
         $comment = array(
            'blog'                  => get_option('home'),
            'blog_lang'             => get_locale(),
            'blog_charset'          => get_option('blog_charset'),
            'user_ip'               => Akismet::get_ip_address(),
            'user_agent'            => $_SERVER['HTTP_USER_AGENT'],
            'referrer'              => $_SERVER['HTTP_REFERER'],
            'permalink'             => $_POST['permalink'],
            'comment_type'          => 'contact-form',
            'comment_author'        => $_POST['comment_author'],
            'comment_author_email'  => $_POST['comment_author_email'],
            'comment_content'       => $_POST['comment_content'],
        ); 
    
        // Check if it's spam
        $is_spam = Akismet::check_db_comment($comment, array(), 'contact_form');
    
        wp_send_json(array(
            'data'      => $comment,
            'enabled'   => true,
            'is_spam'   => $is_spam,
            'message'   => $is_spam ? 'This submission has been identified as potential spam' : ''
        ));
    }

    // TODO: Add cf7 recaptcha compatibility
    public function bypass_wpcf7_spam($skip) {    
        $submission = WPCF7_Submission::get_instance();
        $spam_log = $submission->get_spam_log();
        $is_recaptcha = !!array_column($spam_log, 'agent', 'recaptcha');
    
        if(isset($_POST['_yodel_modal_form']) && $is_recaptcha) {
            return false;
        }
    
        return $skip; 
    }

    private function console_log( $output, $with_script_tags = true ) {
        $output = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
        
        if ( $with_script_tags ) {
            $output = '<script>' . $output . '</script>';
        }

        echo $output;
    } 

}
