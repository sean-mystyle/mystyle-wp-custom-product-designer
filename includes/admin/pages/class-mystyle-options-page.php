<?php

/**
 * Class for rendering the MyStyle Options/Settings page within the WordPress
 * Administrator.
 * @package MyStyle
 * @since 0.1.0
 */
class MyStyle_Options_Page {

    /**
     * Constructor, constructs the options page and adds it to the Settings
     * menu.
     */
    public function __construct() {
        add_action( 'admin_menu', array( &$this, 'add_page_to_menu' ) );
        add_action( 'admin_init', array( &$this, 'admin_init' ) );
    }

    /**
     * Function to initialize the MyStyle options page.
     */
    public function admin_init() {
        $sanitize_callback = array( &$this, 'validate' ); //A callback function that sanitizes the option's value.
        // ************** ACCOUNT SETTINGS SECTION ******************//
        add_settings_section(
                'mystyle_options_access_section',
                'Account Settings',
                array( &$this, 'render_access_section_text' ),
                'mystyle_account_settings'
        );
        add_settings_field(
                'api_key',
                'API Key',
                array( &$this, 'render_api_key' ),
                'mystyle_account_settings',
                'mystyle_options_access_section'
        );
        add_settings_field(
                'secret',
                'Secret',
                array( &$this, 'render_secret' ),
                'mystyle_account_settings',
                'mystyle_options_access_section'
        );
        
        // ************** ADVANCED SETTINGS SECTION ******************//
        add_settings_section(
                'mystyle_options_advanced_section',
                'Advanced Settings',
                array( &$this, 'render_advanced_section_text' ),
                'mystyle_advanced_settings'
        );
        add_settings_field(
                'force_mobile',
                'Disable Flash (Not Recommended)',
                array( &$this, 'render_force_mobile' ),
                'mystyle_advanced_settings',
                'mystyle_options_advanced_section'
        );
        
        // ************** TOOLS SECTION ******************//
        add_settings_section(
                'mystyle_options_tools_section',
                'MyStyle Tools',
                array( &$this, 'render_tools_section_text' ),
                'mystyle_tools'
        );
        if ( ( ! empty( $_GET['action'] ) ) && ( $_SERVER['REQUEST_METHOD'] == 'POST' ) ) {
            $sanitize_callback = ''; //turn off validation
            switch ( $_GET['action'] ) {    
                case 'fix_customize_page' :
                    
                    //Attempt the fix
                    $message = MyStyle_Customize_Page::fix();
                    
                    //Post Fix Notice
                    $fix_notice = MyStyle_Notice::create( 'notify_fix', $message );
                    mystyle_notice_add_to_queue( $fix_notice );
                
                    break;
            }
        }
        register_setting( 'mystyle_options', MYSTYLE_OPTIONS_NAME, $sanitize_callback );
    }

    /**
     * Function to add the options page to the settings menu.
     */
    public function add_page_to_menu() {
        global $mystyle_hook;
        $mystyle_hook = 'mystyle';

        add_menu_page('MyStyle', 'MyStyle', 'manage_options', $mystyle_hook, array( &$this, 'render_page' ), MYSTYLE_ASSETS_URL . '/images/mystyle-icon.png', '56' );
        add_submenu_page( $mystyle_hook, 'Settings', 'Settings', 'manage_options', $mystyle_hook );
        //add_submenu_page( $mystyle_hook, 'Designs', 'Designs', 'manage_product_terms', 'edit-tags.php?taxonomy=product_shipping_class&post_type=product' );
    }

    /**
     * Function to render the MyStyle options page.
     */
    public static function render_page() {
    ?>
        <div class="wrap">
            <h2 class="mystyle-admin-title"><div id="icon-options-general" class="icon100"></div> MyStyle Settings</h2>
            
            <form action="options.php" method="post">
                <?php settings_fields( 'mystyle_options' ); ?>
                <div class="mystyle-admin-box">
                    <?php do_settings_sections( 'mystyle_account_settings' ); ?>
                </div>
                <br/>
                <div class="mystyle-admin-box">
                    <?php do_settings_sections( 'mystyle_advanced_settings' ); ?>
                </div>
                <p class="submit">
                    <input type="submit" name="Submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
                </p>
            </form>
            <br/>
            <div class="mystyle-admin-box">
                <form action="admin.php?page=mystyle&action=fix_customize_page" method="post">
                    <?php do_settings_sections( 'mystyle_tools' ); ?>
                    <p class="submit">
                        <input type="submit" name="Submit" id="submit_fix_customize_page" class="button button-primary" value="<?php esc_attr_e('Fix Customize Page'); ?>" /><br/>
                        <small>This tool will attempt to fix the Customize page. This may involve creating, recreating, or restoring the page.</small>
                    </p>
                </form>
            </div>
            <br/>
            <ul>
                <li>Go to <a href="http://www.mystyleplatform.com/mystyle-personalization-plugin-wordpress-woo-commerce/" target="_blank" title="mystyleplatform.com">mystyleplatform.com</a>.</li>
                <li>Get <a href="#" onclick="jQuery('a#contextual-help-link').trigger('click'); return false;" title="Get help using this plugin.">help</a> using this plugin.</li>
            </ul>
        </div>
    <?php
    }

    /**
     * Function to render the text for the access section.
     */
    public static function render_access_section_text() {
    ?>
        <p>
            To use MyStyle, you will need to <a href="http://www.mystyleplatform.com/apply-mystyle-license-developer-account-api-key-secret/?ref=wp_plugin_1" target="_blank" title="mystyleplatform.com">register for a developer account</a> to get your own MyStyle API Key and Secret.
        </p>
    <?php
    }

    /**
     * Function to render the API Key field and description
     */
    public static function render_api_key() {
        $options = get_option( MYSTYLE_OPTIONS_NAME, array() );
        $api_key = ( array_key_exists('api_key', $options) ) ? $options['api_key'] : '';
     ?>
        <input id="mystyle_api_key" name="mystyle_options[api_key]" size="5" type="text" value="<?php echo $api_key ?>" />
        <p class="description">
            You must enter a valid MyStyle API Key here. If you need an
            API Key, you can create one
            <a href="http://www.mystyleplatform.com/apply-mystyle-license-developer-account-api-key-secret/?ref=wp_plugin_2" target="_blank" title="MyStyle Signup">here</a>.
        </p>
    <?php
    }

    /**
     * Function to render the Secret field and description
     */
    public static function render_secret() {
        $options = get_option( MYSTYLE_OPTIONS_NAME, array() );
        $secret = ( array_key_exists( 'secret', $options ) ) ? $options['secret'] : '';
     ?>
        <input id="mystyle_secret" name="mystyle_options[secret]" size="27" type="text" value="<?php echo $secret ?>" />
        <p class="description">
            You must enter a valid MyStyle Secret here. If you need a MyStyle
            Secret, you can create one
            <a href="http://www.mystyleplatform.com/apply-mystyle-license-developer-account-api-key-secret/?ref=wp_plugin_3" target="_blank" title="MyStyle Signup">here</a>.
        </p>
    <?php
    }
    
    /**
     * Function to render the text for the advanced section.
     */
    public static function render_advanced_section_text() {
    ?>
        <p>
            For advanced users only.
        </p>
    <?php
    }
    
    /**
     * Function to render the Force Mobile field and description
     */
    public static function render_force_mobile() {
        $options = get_option( MYSTYLE_OPTIONS_NAME, array() );
        $force_mobile = ( array_key_exists( 'force_mobile', $options ) ) ? $options['force_mobile'] : 0;
     ?>
        <input type="checkbox" id="mystyle_force_mobile" name="mystyle_options[force_mobile]" value="1" <?php echo checked( 1, $force_mobile, false ) ?> />
        <p class="description">
            Check to always use the HTML5 (rather than the Flash) version of the MyStyle customizer.
        </p>
    <?php
    }

    /**
     * Function to render the text for the tools section.
     */
    public static function render_tools_section_text() {
    ?>
        <p>
            The below tools are available to repair your MyStyle configuration.
        </p>
    <?php
    }

    /**
     * Function to validate the submitted MyStyle options field values.
     *
     * This function overrites the old values instead of completely replacing them so
     * that we don't overwrite values that weren't submitted (such as the
     * version).
     * @param array $input The submitted values
     * @return array Returns the new options to be stored in the database.
     */
    public static function validate( $input ) {
        $old_options = get_option( MYSTYLE_OPTIONS_NAME );
        $new_options = $old_options;  //start with the old options.

        $has_errors = false;
        $msg_type = null;
        $msg_message = null;

        //------------ process the new values ------------

        //API Key
        $new_options['api_key'] = trim( $input['api_key'] );
        if(!preg_match( '/^[a-z0-9]*$/i', $new_options['api_key'] ) ) {
            $has_errors = true;
            $msg_type = 'error';
            $msg_message = 'Please enter a valid API Key.';
            $new_options['api_key'] = '';
        }

        //Secret
        $new_options['secret'] = trim( $input['secret'] );
        if( ! preg_match('/^[a-z0-9]*$/i', $new_options['secret'] ) ) {
            $has_errors = true;
            $msg_type = 'error';
            $msg_message = 'Please enter a valid Secret.';
            $new_options['secret'] = '';
        }
        
        //Force Mobile
        $new_options['force_mobile'] = intval( $input['force_mobile'] );
        if( ! preg_match('/^[01]$/', $new_options['force_mobile'] ) ) {
            $has_errors = true;
            $msg_type = 'error';
            $msg_message = 'Invalid HTML5 Customizer option';
            $new_options['force_mobile'] = 0;
        }

        if(!$has_errors) {
            $msg_type = 'updated';
            $msg_message = 'Settings saved.';
        }

        add_settings_error(
            'MyStyleOptionsSaveMessage',
            esc_attr('settings_updated'),
            $msg_message,
            $msg_type
        );

        return $new_options;
    }
}