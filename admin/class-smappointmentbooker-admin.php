<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://salonmanager.us
 * @since      1.0.0
 *
 * @package    Smappointmentbooker
 * @subpackage Smappointmentbooker/admin
 */

// get PageTemplater for custom page template
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pagetemplater.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smappointmentbooker
 * @subpackage Smappointmentbooker/admin
 * @author     Thang Cao <bobuchacha@gmail.com>
 */
class SMMetaboxField {
    public $name;
    public $fieldId;
    public $metaId;
    public $value;
    public $description;

    public function __construct($name = "", $fieldId = "",$metaId = "", $value = "", $description = "")
    {
        $this->name = $name;
        $this->fieldId = $fieldId;
        $this->metaId = $metaId;
        $this->value = $value;
        $this->description = $description;
    }
}
class Smappointmentbooker_Admin {

    private $plugin_name;
    private $version;

    private $metabox_fields = [];

    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->create_metabox_fields();


        add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );       // let user select template
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }


    }

    function create_metabox_fields(){
        // create metabox field field
        $field1 = new SMMetaboxField();
        $field1->name = "Salon Name";
        $field1->metaId = "salon_name";
        $field1->fieldId = "txtSalonName";
        $this->metabox_fields[] = $field1;

        // create metabox field field
        $field = new SMMetaboxField();
        $field->name = "Slogan";
        $field->metaId = "salon_slogan";
        $field->fieldId = "txtSlogan";
        $this->metabox_fields[] = $field;

        // create metabox field field
        $field = new SMMetaboxField();
        $field->name = "Address Line 1";
        $field->metaId = "salon_address_1";
        $field->fieldId = "txtAddressLine1";
        $this->metabox_fields[] = $field;

        // create metabox field field
        $field = new SMMetaboxField();
        $field->name = "Address Line 2";
        $field->metaId = "salon_address_2";
        $field->fieldId = "txtAddressLine2";
        $this->metabox_fields[] = $field;

        // create metabox field field
        $field = new SMMetaboxField();
        $field->name = "Phone Number";
        $field->metaId = "salon_phone";
        $field->fieldId = "txtPhone";
        $this->metabox_fields[] = $field;

        // create metabox field field
        $field = new SMMetaboxField();
        $field->name = "Backoffice API Key";
        $field->metaId = "api_key";
        $field->fieldId = "txtAPIKey";
        $field->description = "Acquire this from your Back office, Salon Times module.";
        $this->metabox_fields[] = $field;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smappointmentbooker-admin.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smappointmentbooker-admin.js', array( 'jquery' ), $this->version, false );
    }



    /**
     * saves the data
     */
    function wpse44966_add_meta_box_save($post_id) {
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

        foreach ($this->metabox_fields as $field) {
            if( isset( $_POST[$field->fieldId] ) ) update_post_meta( $post_id, $field->metaId,  $_POST[$field->fieldId]);
        }

    }




    /**
     * Meta box initialization.
     */
    public function init_metabox() {

        add_action('add_meta_boxes', array($this, 'add_metabox'));
        add_action('save_post', array($this, 'save_metabox'));

    }

    /**
     * Adds the meta box.
     */
    public function add_metabox() {
        if (!PageTemplater::check_is_my_template(get_page_template_slug())) return;

        add_meta_box(
            'my-meta-box',
            __( 'Salon Information' ),
            array( $this, 'render_metabox' ),
            'page',
            'advanced',
            'default'
        );

    }

    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) {
        $values = get_post_custom( $post->ID );
        wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );

        echo "<table border=0 width=100%><tr><td colspan='2'>Use these information to connect to Salon Management System from Salon Orchid.<br/>Please acquire these information from your back office. If you don't know what your information are, please contact Technical Support for help.</td></tr>";
        foreach($this->metabox_fields as $field){
            $field->value = isset($values[$field->metaId][0]) ? esc_attr($values[$field->metaId][0]) : '';
            echo ("<tr><td width='20%'>{$field->name}:</td><td><input style='width:100%' type='text' id='{$field->fieldId}' name='{$field->fieldId}' value='{$field->value}'/></td></tr>");
        }
        echo "</table>";

    }

    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id ) {

        if (!PageTemplater::check_is_my_template(get_page_template_slug())) return;
        if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;


        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }



        foreach ($this->metabox_fields as $field) {
            if( isset( $_POST[$field->fieldId] ) ) update_post_meta( $post_id, $field->metaId,  $_POST[$field->fieldId]);
        }
    }

}