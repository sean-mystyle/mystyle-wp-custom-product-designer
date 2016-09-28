<?php

require_once( MYSTYLE_INCLUDES . 'frontend/class-mystyle-frontend.php' );

/**
 * Mock the WC_Product_Variable class. This is used by the 
 * test_mystyle_add_to_cart_handler_customize and
 * test_loop_add_to_cart_link_for_variable_product functions/tests.
 */
class WC_Product_Variable {
    public $id;
    public function get_variation_attributes() {
        return null;
    }
}

/**
 * The FrontEndTest class includes tests for testing the MyStyle_FrontEnd class.
 *
 * @package MyStyle
 * @since 0.2.1
 */
class MyStyleFrontEndTest extends WP_UnitTestCase {
    
    /**
     * Test the constructor
     */    
    public function test_constructor() {
        $mystyle_frontend = new MyStyle_FrontEnd();
        
        global $wp_filter;
        
        //Assert that the filter_cart_button_text function is registered.
        $function_names = get_function_names( $wp_filter['woocommerce_product_single_add_to_cart_text'] );
        $this->assertContains( 'filter_cart_button_text', $function_names );
        
        //Assert that the filter_add_to_cart_handler function is registered.
        $function_names = get_function_names( $wp_filter['woocommerce_add_to_cart_handler'] );
        $this->assertContains( 'filter_add_to_cart_handler', $function_names );
        
        //Assert that the init function is registered.
        $function_names = get_function_names( $wp_filter['init'] );
        $this->assertContains( 'init', $function_names );
        
        //Assert that the mystyle_add_to_cart_handler_customize function is registered.
        $function_names = get_function_names( $wp_filter['woocommerce_add_to_cart_handler_mystyle_customizer'] );
        $this->assertContains( 'mystyle_add_to_cart_handler_customize', $function_names );
        
        //Assert that the mystyle_add_to_cart_handler function is registered.
        $function_names = get_function_names( $wp_filter['woocommerce_add_to_cart_handler_mystyle_add_to_cart'] );
        $this->assertContains( 'mystyle_add_to_cart_handler', $function_names );
        
        //Assert that the loop_add_to_cart_link function is registered.
        $function_names = get_function_names( $wp_filter['woocommerce_loop_add_to_cart_link'] );
        $this->assertContains( 'loop_add_to_cart_link', $function_names );
    }
    
    /**
     * Test the mystyle_frontend_init function.
     */    
    public function test_mystyle_frontend_init() {
        $mystyle_frontend = new MyStyle_Frontend();
        
        //Assert that our scripts are registered
        global $wp_scripts;
        $this->assertContains( 'swfobject', serialize( $wp_scripts ) );
        
        //Assert that our stylesheets are registered
        global $wp_styles;
        $this->assertContains( 'myStyleFrontendStylesheet', serialize( $wp_styles ) );
    }
    
    /**
     * Test the filter_body_class function.
     */    
    public function test_filter_body_class_adds_class_to_customize_page() {
        global $post;
        
        //Create the MyStyle Customize page
        MyStyle_Customize_Page::create();
        
        //Create the MyStyle Design Profile page
        MyStyle_Design_Profile_Page::create();
        
        //init the MyStyle_Frontend
        $mystyle_frontend = new MyStyle_Frontend();
        
        //mock the post and get vars
        $post = new stdClass();
        $post->ID = MyStyle_Customize_Page::get_id();
        $_GET['product_id'] = 1;
        
        //mock the $classes var
        $classes = array();
        
        //call the function
        $returned_classes = $mystyle_frontend->filter_body_class( $classes );

        //Assert that the mystyle-customize class is added to the classes array.
        $this->assertEquals( 'mystyle-customize', $returned_classes[0] );
    }
    
    /**
     * Test the filter_body_class function.
     */    
    public function test_filter_body_class_adds_class_to_design_profile_page() {
        global $post;
        
        //Create the MyStyle Customize page
        MyStyle_Customize_Page::create();
        
        //Create the MyStyle Design Profile page
        MyStyle_Design_Profile_Page::create();
        
        //init the MyStyle_Frontend
        $mystyle_frontend = new MyStyle_Frontend();
        
        //mock the post and get vars
        $post = new stdClass();
        $post->ID = MyStyle_Design_Profile_Page::get_id();
        
        //mock the $classes var
        $classes = array();
        
        //call the function
        $returned_classes = $mystyle_frontend->filter_body_class( $classes );

        //Assert that the mystyle-design-profile class is added to the classes array.
        $this->assertEquals( 'mystyle-design-profile', $returned_classes[0] );
    }
    
    /**
     * Test the filter_title function.
     */    
    public function test_filter_title() {
        global $post;
        global $wp_query;
        
        //Create the MyStyle Customize page
        MyStyle_Customize_Page::create();
        
        //Create the MyStyle Design Profile page
        MyStyle_Design_Profile_Page::create();
        
        //init the MyStyle_Frontend
        $mystyle_frontend = new MyStyle_Frontend();
        
        //mock the post, etc.
        $post = new stdClass();
        $post->ID = MyStyle_Customize_Page::get_id();
	$wp_query->in_the_loop = true;
        
        //Enable the hide title option
        $options = get_option( MYSTYLE_OPTIONS_NAME, array() );
        $options['customize_page_title_hide'] = 1;
        update_option( MYSTYLE_OPTIONS_NAME, $options );
        
        //mock the $classes var
        $classes = array();
        
        //call the function
        $new_title = $mystyle_frontend->filter_title( 'foo', MyStyle_Customize_Page::get_id() );

        //Assert that the title has been set to the empty string
        $this->assertEquals( '', $new_title );
    }
    
    
    /**
     * Mock the mystyle_metadata
     * @param type $metadata
     * @param type $object_id
     * @param type $meta_key
     * @param type $single
     * @return string
     */
    function mock_mystyle_metadata( $metadata, $object_id, $meta_key, $single ){
        return 'yes';
    }
    
    /**
     * Test the filter_cart_button_text function when product isn't mystyle
     * enabled.
     */    
    public function test_filter_cart_button_text_doesnt_modify_button_text_when_not_mystyle_enabled() {
        global $product;
        
        $mystyle_frontend = new MyStyle_Frontend();
        
        //Mock the global $post variable
        $post_vars = new stdClass();
        $post_vars->ID = 1;
        $GLOBALS['post'] = new WP_Post( $post_vars );
        
        //Create a mock product using the mock Post
        $product = new WC_Product_Simple($GLOBALS['post']);
        
        $text = $mystyle_frontend->filter_cart_button_text( 'Add to Cart' );
        
        //Assert that the expected text is returned
        $this->assertContains( 'Add to Cart', $text );
    }
    
    /**
     * Test the filter_cart_button_text function when product is mystyle enabled.
     */    
    public function test_filter_cart_button_text_modifies_button_text_when_mystyle_enabled() {
        global $product;
        
        $mystyle_frontend = new MyStyle_Frontend();
        
        //Mock the global $post variable
        $post_vars = new stdClass();
        $post_vars->ID = 1;
        $GLOBALS['post'] = new WP_Post( $post_vars );
        
        //Create a mock product using the mock Post
        $product = new WC_Product_Simple($GLOBALS['post']);
        
        //Mock the mystyle_metadata
        add_filter('get_post_metadata', array( &$this, 'mock_mystyle_metadata' ), true, 4);
        
        $text = $mystyle_frontend->filter_cart_button_text( 'Add to Cart' );
        
        //Assert that the expected text is returned
        $this->assertContains( 'Customize', $text );
    }
    
    /**
     * Test the filter_add_to_cart_handler function when product isn't mystyle
     * enabled.
     */    
    public function test_filter_add_to_cart_handler_doesnt_modify_handler_when_not_mystyle_enabled() {
        global $product;
        
        $mystyle_frontend = new MyStyle_Frontend();
        
        //Mock the global $post variable
        $post_vars = new stdClass();
        $post_vars->ID = 1;
        $GLOBALS['post'] = new WP_Post( $post_vars );
        
        //Create a mock product using the mock Post
        $product = new WC_Product_Simple($GLOBALS['post']);
        
        $text = $mystyle_frontend->filter_add_to_cart_handler( 'test_handler', $product );
        
        //Assert that the expected text is returned
        $this->assertContains( 'test_handler', $text );
    }
    
    /**
     * Test the filter_add_to_cart_handler function when product is mystyle enabled.
     */    
    public function test_filter_add_to_cart_handler_modifies_handler_when_mystyle_enabled() {
        global $product;
        
        $mystyle_frontend = new MyStyle_Frontend();
        
        //Mock the global $post variable
        $post_vars = new stdClass();
        $post_vars->ID = 1;
        $GLOBALS['post'] = new WP_Post( $post_vars );
        
        //Create a mock product using the mock Post
        $product = new WC_Product_Simple($GLOBALS['post']);
        
        //Mock the mystyle_metadata
        add_filter('get_post_metadata', array( &$this, 'mock_mystyle_metadata' ), true, 4);
        
        if(WC_VERSION >= 2.3) { //we intercept the filter and call the the handler in old versions of WC, so this test always fails
            $text = $mystyle_frontend->filter_add_to_cart_handler( 'test_handler', $product );
        
            //Assert that the expected text is returned
            $this->assertContains( 'mystyle_customizer', $text );
        }
    }
    
    /**
     * Test the loop_add_to_cart_link function for a regular (uncustomizable) 
     * product.
     */    
    public function test_loop_add_to_cart_link_for_uncustomizable_product() {
        $mystyle_frontend = new MyStyle_FrontEnd();
        
        //Create a mock link
        $link = '<a href="">link</a>';
        
         //Mock the global $post variable
        $post_vars = new stdClass();
        $post_vars->ID = 1;
        $GLOBALS['post'] = new WP_Post( $post_vars );
        
        //Create a mock product using the mock Post
        $product = new WC_Product_Simple($GLOBALS['post']);
        
        $html = $mystyle_frontend->loop_add_to_cart_link( $link, $product );
        
        $this->assertContains( $link, $html );
    }
    
    /**
     * Test the loop_add_to_cart_link function for a customizable product.
     */    
    public function test_loop_add_to_cart_link_for_customizable_product() {
        $mystyle_frontend = new MyStyle_FrontEnd();
        
        //Create a mock link
        $link = '<a href="">link</a>';
        
        //Mock the global $post variable
        $post_vars = new stdClass();
        $post_vars->ID = 1;
        $GLOBALS['post'] = new WP_Post( $post_vars );
        
        //Create a mock product using the mock Post
        $product = new WC_Product_Simple($GLOBALS['post']);
        
        //Mock the mystyle_metadata
        add_filter('get_post_metadata', array( &$this, 'mock_mystyle_metadata' ), true, 4);
        
        //Create the MyStyle Customize page (needed by the function)
        MyStyle_Customize_Page::create();
        
        //Run the function
        $html = $mystyle_frontend->loop_add_to_cart_link( $link, $product );
        
        //var_dump($html);
        
        $cust_pid = MyStyle_Customize_Page::get_id();
        $h = base64_encode( json_encode( array( 'post' => array( 'quantity' => 1, 'add-to-cart' => 1 ) ) ) );
        
        $expectedUrl = 'http://example.org/?page_id=' . $cust_pid . '&#038;product_id=1&#038;h=' . $h;
        
        $expectedHtml = '<a href="'.$expectedUrl.'" rel="nofollow" class="button  product_type_simple" >Customize</a>';
        
        $this->assertEquals( $expectedHtml, $html );
    }
    
    /**
     * Test the loop_add_to_cart_link function for a customizable but variable
     * product.  It should leave the button "Select Options" unchanged.
     */    
    public function test_loop_add_to_cart_link_for_variable_product() {
        $mystyle_frontend = new MyStyle_FrontEnd();
        
        //Create a mock link
        $link = '<a href="">link</a>';
        
        //Mock the global $post variable
        $post_vars = new stdClass();
        $post_vars->ID = 1;
        $GLOBALS['post'] = new WP_Post( $post_vars );
        
        //Mock the mystyle_metadata
        add_filter('get_post_metadata', array( &$this, 'mock_mystyle_metadata' ), true, 4);
        
        //Create a mock VARIABLE product using the mock Post
        $product = new WC_Product_Variable( $GLOBALS['post'] );
        
        $html = $mystyle_frontend->loop_add_to_cart_link( $link, $product );
        
        //assert that the link is returned unmodified
        $this->assertContains( $link, $html );
    }
    
    /**
     * Test the mystyle_add_to_cart_handler function.
     */    
    public function test_mystyle_add_to_cart_handler() {
        global $woocommerce;
        
        $mystyle_frontend = new MyStyle_FrontEnd();
        
        //Create the MyStyle Customize page
        MyStyle_Customize_Page::create();
        
        //Mock woocommerce
        $woocommerce = new MyStyle_MockWooCommerce();
        $woocommerce->cart = new MyStyle_MockWooCommerceCart();
        
        //Mock the request
        $_REQUEST['add-to-cart'] = 1;
        $_REQUEST['design_id'] = 2;
        
        //call the function
        MyStyle_FrontEnd::mystyle_add_to_cart_handler( 'http://www.example.com' );
        
        //Assert that the mock add_to_cart function was called.
        $this->assertEquals( 1, $woocommerce->cart->add_to_cart_call_count );
    }
    
    /**
     * Disable the wp_redirect function so that it returns false and doesn't
     * perform the redirect. This is used by the 
     * test_mystyle_add_to_cart_handler function below.
     * @param string $location
     * @param integer $status
     * @return type
     */
    function filter_wp_redirect( $location, $status ){
        global $filter_wp_redirect_called;
        
        $filter_wp_redirect_called = true;
    }
    
    /**
     * Test the mystyle_add_to_cart_handler_customize function.
     */    
    public function test_mystyle_add_to_cart_handler_customize() {
        global $product;
        global $filter_wp_redirect_called;
        
        $mystyle_frontend = new MyStyle_Frontend();
        
        //Mock the global $post variable
        $post_vars = new stdClass();
        $post_vars->ID = 1;
        $GLOBALS['post'] = new WP_Post( $post_vars );
        
        //Create a mock product using the mock Post
        $product = new WC_Product_Simple($GLOBALS['post']);
        
        //Set the expected request variables
        $_REQUEST['add-to-cart'] = $product->id;
        $_REQUEST['quantity'] = 1;
        
        //Create the MyStyle Customize page (needed by the function)
        MyStyle_Customize_Page::create();
        
        //Disable the redirect
        add_filter('wp_redirect', array( &$this, 'filter_wp_redirect' ), 10, 2);
        
        $mystyle_frontend->mystyle_add_to_cart_handler_customize( '' );
        
        //Assert that the function called the filter_wp_redirect function (see above)
        $this->assertTrue( $filter_wp_redirect_called );
    }
}
