<?php
/**
 * The MyStyle Product class is used to wrap the WC_Product class to allow us to
 * support multiple versions of WooCommerce.
 *
 * We don't extend the WC_Product because other classes (WC_Product_Variation,
 * etc) already extend it and we would actually need to extend each of those.
 * Furthermore, plugins such as the WooCommerce Dynamic Pricing Plugin expect
 * the product to be from the set of standard product types and if it isn't,
 * the Dynamic Pricing Plugin breaks.
 *
 * @package MyStyle
 * @since 1.7.0
 */

/**
 * MyStyle_Product class.
 */
class MyStyle_Product {

	/**
	 * The key for the mystyle data in the db.
	 *
	 * @var string
	 */
	const MYSTYLE_DATA_KEY = 'mystyle_data';

	/**
	 * The WC_Product that this class wraps.
	 *
	 * @var \WC_Product
	 */
	private $product;

	/**
	 * Constructor.
	 *
	 * @param mixed $product The WC_Product that we are wrapping. Might be
	 * something else (false, null, etc.) if the product was deleted.
	 */
	public function __construct( $product ) {

		// Create dummy product if it does not exist.
		if ( ! $product instanceof \WC_Product ) {
			$product = new \WC_Product();
			$product->set_stock_quantity( 0 );
			$product->set_stock_status( 'outofstock' );
			$product->set_name( 'Custom Product Design' );
			$product->set_description( 'Reload and customize this design with any product in the list.' );
		}

		$this->product = $product;
	}

	/**
	 * Static function to instantiate a MyStyle_Product from a product_id. Call
	 * using `MyStyle_Product::get_by_id( $product_id );`.
	 *
	 * @param integer $product_id The id of the product that you want to get.
	 */
	public static function get_by_id( $product_id ) {
		$instance = new self( new \WC_Product( $product_id ) );

		return $instance;
	}

	/**
	 * Returns the underlying WC_Product.
	 *
	 * @return \WC_Product The WC_Product that this class wraps.
	 */
	public function get_product() {
		return $this->product;
	}

	/**
	 * Gets the product id.
	 *
	 * Works with WC 2.x and WC 3.x.
	 *
	 * @return number Returns the product id.
	 */
	public function get_id() {
		if ( method_exists( $this->product, 'get_id' ) ) {
			$id = $this->product->get_id();
		} else {
			$id = $this->product->ID;
		}

		return $id;
	}

	/**
	 * Gets the product type.
	 *
	 * Works with WC 2.x and WC 3.x.
	 *
	 * @return string Returns the product type.
	 */
	public function get_type() {
		if ( method_exists( $this->product, 'get_type' ) ) {
			$product_type = $this->product->get_type();
		} else {
			$product_type = $this->product->product_type;
		}

		return $product_type;
	}

	/**
	 * Gets the product's children (as an array of product ids).
	 *
	 * Works with WC 2.x and WC 3.x.
	 *
	 * @return array Returns the product's children (as an array of product
	 * ids).
	 */
	public function get_children() {
		if ( method_exists( $this->product, 'get_children' ) ) {
			$product_type = $this->product->get_children();
		} else {
			$product_type = $this->product->children;
		}

		return $product_type;
	}

	/**
	 * Function that looks to see if the product is mystyle enabled.
	 *
	 * @return boolean Returns true if the product is customizable, otherwise,
	 * returns false.
	 */
	public function is_customizable() {
		$is_customizable = false;
		$mystyle_enabled = get_post_meta( $this->get_id(), '_mystyle_enabled', true );

		if ( 'yes' === $mystyle_enabled ) {
			$is_customizable = true;
		}

		return $is_customizable;
	}

	/**
	 * Function that looks to see if the product has configur8 enabled.
	 *
	 * @return boolean Returns true if the product has configur8 enabled,
	 * otherwise, returns false.
	 */
	public function configur8_enabled() {
		$configur8_enabled      = false;
		$configur8_option_value = get_post_meta( $this->get_id(), '_mystyle_configur8_enabled', true );

		if ( 'yes' === $configur8_option_value ) {
			$configur8_enabled = true;
		}

		return $configur8_enabled;
	}

	/**
	 * Method that gets the parent design that the product was spawned
	 * (upgraded) from. If the product wasn't spawned from a design, this method
	 * just returns null.
	 *
	 * @return MyStyle_Design|null Returns the design that spawned this product
	 * or null if no parent design was found.
	 */
	public function get_parent_design() {
		$design    = null;
		$design_id = get_post_meta(
			$this->get_id(),
			'_mystyle_design_id',
			true
		);

		if ( ! empty( $design_id ) ) {
			/* @var $current_user \WP_User The current user. */
			$current_user = wp_get_current_user();

			/* @var $design \MyStyle_Design The current design. */
			$design = MyStyle_DesignManager::get( $design_id, $current_user );
		}

		return $design;
	}

	/**
	 * Gets the product title.
	 *
	 * @returns string Returns the product title.
	 */
	public function get_title() {
		return $this->product->get_title();
	}

	/**
	 * Gets the product description.
	 *
	 * @returns string Returns the product description.
	 */
	public function get_description() {
		return $this->product->get_description();
	}

	/**
	 * Gets a link to the product info page.
	 *
	 * @returns string Returns a link to the product info page.
	 */
	public function get_permalink() {
		return $this->product->get_permalink();
	}
}
