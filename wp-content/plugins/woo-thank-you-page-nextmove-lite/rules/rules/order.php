<?php
defined( 'ABSPATH' ) || exit;

class xlwcty_Rule_Order_Item_Count extends xlwcty_Rule_Base {

	public function __construct() {
		parent::__construct( 'order_item_count' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'==' => __( 'is equal to', 'woo-thank-you-page-nextmove-lite' ),
			'!=' => __( 'is not equal to', 'woo-thank-you-page-nextmove-lite' ),
			'>'  => __( 'is greater than', 'woo-thank-you-page-nextmove-lite' ),
			'<'  => __( 'is less than', 'woo-thank-you-page-nextmove-lite' ),
			'>=' => __( 'is greater or equal to', 'woo-thank-you-page-nextmove-lite' ),
			'<=' => __( 'is less or equal to', 'woo-thank-you-page-nextmove-lite' ),
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Text';
	}

	public function is_match( $rule_data, $order_id ) {

		$order = wc_get_order( $order_id );
		$count = absint( $order->get_item_count() );
		$value = absint( $rule_data['condition'] );

		switch ( $rule_data['operator'] ) {
			case '==':
				$result = $count === $value;
				break;
			case '!=':
				$result = $count !== $value;
				break;
			case '>':
				$result = $count > $value;
				break;
			case '<':
				$result = $count < $value;
				break;
			case '>=':
				$result = $count >= $value;
				break;
			case '<=':
				$result = $count <= $value;
				break;
			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data );
	}

}

class xlwcty_Rule_Order_Item extends xlwcty_Rule_Base {

	public function __construct() {
		parent::__construct( 'order_item' );
	}

	public function get_possibile_rule_operators() {

		$operators = array(
			'>'  => __( 'contains at least', 'woo-thank-you-page-nextmove-lite' ),
			'<'  => __( 'contains at most', 'woo-thank-you-page-nextmove-lite' ),
			'==' => __( 'contains exactly', 'woo-thank-you-page-nextmove-lite' ),
			'!=' => __( 'does not contains at least', 'woo-thank-you-page-nextmove-lite' ),
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Cart_Product_Select';
	}

	public function is_match( $rule_data, $order_id ) {
		$order          = wc_get_order( $order_id );
		$products       = $rule_data['condition']['products'];
		$quantity       = absint( $rule_data['condition']['qty'] );
		$type           = $rule_data['operator'];
		$found_quantity = 0;

		if ( $order->get_items() && is_array( $order->get_items() ) && count( $order->get_items() ) ) {
			foreach ( $order->get_items() as $item_key => $cart_item ) {

				$product   = XLWCTY_Compatibility::get_product_from_item( $order, $cart_item );
				$productID = $product->get_id();
				$productID = ( XLWCTY_Common::get_product_parent_id( $product ) ) ? XLWCTY_Common::get_product_parent_id( $product ) : $productID;

				if ( version_compare( WC()->version, '3.0', '>=' ) ) {
					$variationID = $cart_item->get_variation_id();
				} else {
					$variationID = ( is_array( $cart_item['variation_id'] ) && count( $cart_item['variation_id'] ) > 0 ) ? $cart_item['variation_id'][0] : 0;
				}

				if ( in_array( $productID, $products ) || ( ( $productID ) && in_array( $variationID, $products ) ) ) {

					$found_quantity += absint( $cart_item['qty'] );
				}
			}
		}

		if ( 0 === $found_quantity ) {
			if ( '!=' === $type ) {
				return $this->return_is_match( true, $rule_data );
			}

			return $this->return_is_match( false, $rule_data );
		}
		switch ( $type ) {
			case '<':
				$result = ( $quantity >= $found_quantity );
				break;
			case '>':
				$result = ( $quantity <= $found_quantity );
				break;
			case '==':
				$result = ( $quantity === $found_quantity );
				break;
			case '!=':
				$result = ! ( $quantity <= $found_quantity );
				break;
			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data );
	}

}

class xlwcty_Rule_Order_Category extends xlwcty_Rule_Base {

	public function __construct() {
		parent::__construct( 'order_category' );
	}

	public function get_possibile_rule_operators() {

		$operators = array(
			'any' => __( 'matched any of', 'woo-thank-you-page-nextmove-lite' ),
			'all' => __( 'matches all of ', 'woo-thank-you-page-nextmove-lite' ),

		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();
		$terms  = get_terms( 'product_cat', array(
			'hide_empty' => false,
		) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $order_id ) {
		$type      = $rule_data['operator'];
		$order     = wc_get_order( $order_id );
		$all_terms = array();

		if ( $order->get_items() && count( $order->get_items() ) ) {
			foreach ( $order->get_items() as $item_key => $cart_item ) {
				$product = XLWCTY_Compatibility::get_product_from_item( $order, $cart_item );

				$productID      = $product->get_id();
				$parent_id_find = XLWCTY_Compatibility::get_product_parent_id( $product );
				$productID      = ( $parent_id_find ) ? $parent_id_find : $productID;

				$terms = wp_get_object_terms( $productID, 'product_cat', array(
					'fields' => 'ids',
				) );

				$all_terms = array_merge( $all_terms, $terms );

			}
		}

		$all_terms = array_filter( $all_terms );
		if ( empty( $all_terms ) ) {
			return $this->return_is_match( false, $rule_data );
		}

		switch ( $type ) {
			case 'all':
				$result = count( array_intersect( $rule_data['condition'], $all_terms ) ) === count( $rule_data['condition'] );
				break;
			case 'any':
				$result = count( array_intersect( $rule_data['condition'], $all_terms ) ) >= 1;
				break;

			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data );
	}

}

class xlwcty_Rule_Order_Item_Type extends xlwcty_Rule_Base {

	public function __construct() {
		parent::__construct( 'order_item_type' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'any' => __( 'matched any of', 'woo-thank-you-page-nextmove-lite' ),
			'all' => __( 'matches all of ', 'woo-thank-you-page-nextmove-lite' ),

		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();
		$terms  = get_terms( 'product_type', array(
			'hide_empty' => false,
		) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $order_id ) {
		$type      = $rule_data['operator'];
		$order     = wc_get_order( $order_id );
		$all_types = array();

		if ( $order->get_items() && count( $order->get_items() ) ) {
			foreach ( $order->get_items() as $item_key => $cart_item ) {
				$product = XLWCTY_Compatibility::get_product_from_item( $order, $cart_item );

				$productID      = $product->get_id();
				$parent_id_find = XLWCTY_Compatibility::get_product_parent_id( $product );
				$productID      = ( $parent_id_find ) ? $parent_id_find : $productID;

				$product_types = wp_get_post_terms( $productID, 'product_type', array(
					'fields' => 'ids',
				) );

				$all_types = array_merge( $all_types, $product_types );

			}
		}

		$all_types = array_filter( $all_types );
		if ( empty( $all_types ) ) {
			return $this->return_is_match( false, $rule_data );
		}

		switch ( $type ) {
			case 'all':
				$result = count( array_intersect( $rule_data['condition'], $all_types ) ) === count( $rule_data['condition'] );
				break;
			case 'any':
				$result = count( array_intersect( $rule_data['condition'], $all_types ) ) >= 1;
				break;

			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data );
	}

}
