<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Smappointmentbooker
 * @subpackage Smappointmentbooker/public
 * @author     Thang Cao <bobuchacha@gmail.com>
 */
class Smappointmentbooker_Public {

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
		 * defined in Smappointmentbooker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smappointmentbooker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . '-frontend', plugin_dir_url( __FILE__ ) . 'css/salon-page.css', array(), $this->version, 'all' );



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
		 * defined in Smappointmentbooker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smappointmentbooker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if (!is_admin()) {
			// comment out the next two lines to load the local copy of jQuery
			//	wp_deregister_script('jquery');
			if ($this->has_shortcode("sm_appointment_booking_form")) {
                wp_register_script('jquery3', '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', false, '1.11.3');
                wp_enqueue_script('jquery3');
                wp_enqueue_script( 'underscore1', '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.10.2/underscore-min.min.js', null, $this->version, false );
                wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smappointmentbooker-public.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smappointmentbooker-public.css', array(), $this->version, 'all' );
                wp_enqueue_script( 'datepicker', plugin_dir_url( __FILE__ ) . 'js/datepicker.js', array( 'jquery' ), "1.0.9", false );
                wp_enqueue_style( 'datepicker', plugin_dir_url( __FILE__ ) . 'css/datepicker.css', array(), $this->version, 'all' );
            }
		}


	}

    /**
     * check if post has shortcode
     * @param null $shortcode
     * @return bool
     */
    function has_shortcode( $shortcode = NULL ) {

        $post_to_check = get_post( get_the_ID() );

        // false because we have to search through the post content first
        $found = false;

        // if no short code was provided, return false
        if ( ! $shortcode ) {
            return $found;
        }
        // check the post content for the short code
        if ( stripos( $post_to_check->post_content, '[' . $shortcode) !== FALSE ) {
            // we have found the short code
            $found = TRUE;
        }

        // return our final results
        return $found;
    }

	/**
	 * return API encryped token that pass to API.php for processing
	 */
	private function get_api_token($attr){
		$token = [];
		$token['account-id'] = $attr['account-id'];
		$token['location-id'] = $attr['location-id'];
		$token['api-token'] = $attr['api-token'];
		$token = json_encode($token);

		return DataEncapsulation::encrypt($token);
	}


	/**
	 * show form
	 */

	function show_sm_appointment_booking_form($attr){

//		// load additional js and css
//		wp_enqueue_script( 'underscore', plugin_dir_url( __FILE__ ) . 'js/_.js', null, $this->version, false );
//		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smappointmentbooker-public.js', array( 'jquery' ), $this->version, false );
//		wp_enqueue_script( 'datepicker', plugin_dir_url( __FILE__ ) . 'js/datepicker.js', array( 'jquery' ), $this->version, false );
//		wp_enqueue_style( 'datepicker', plugin_dir_url( __FILE__ ) . 'css/datepicker.css', array(), $this->version, 'all' );
//		wp_enqueue_style( 'grid', plugin_dir_url( __FILE__ ) . 'css/grid-layout.css', array(), $this->version, 'all' );
//		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smappointmentbooker-public.css', array(), $this->version, 'all' );

		ob_start();
		$attr = shortcode_atts(
			array(
				'account-id' => null,
				'location-id' => null,
				'api-token' => null
			), $attr, 'bartag' );

		$API_url = plugins_url('api/api.php', __FILE__);
		$API_token = $this->get_api_token($attr);
		include_once ('partials/smappointmentbooker-public-display.php');


		return ob_get_clean();
	}


}
