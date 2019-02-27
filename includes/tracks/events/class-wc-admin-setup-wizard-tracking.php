<?php
/**
 * WooCommerce Admin Setup Wizard Tracking
 *
 * @package WooCommerce\Tracks
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of the WooCommerce Onboarding Wizard.
 */
class WC_Admin_Setup_Wizard_Tracking {
	/**
	 * Init tracking.
	 */
	public static function init() {
		if ( empty( $_GET['page'] ) || 'wc-setup' !== $_GET['page'] ) { // WPCS: CSRF ok, input var ok.
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		$current_step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : '';
		if ( ! empty( $_POST['save_step'] ) ) {
			switch ( $current_step ) {
				case '':
				case 'store_setup':
					add_action( 'admin_init', array( __CLASS__, 'track_store_setup' ), 1 );
					break;
				case 'activate':
					add_action( 'admin_init', array( __CLASS__, 'track_activate' ), 1 );
					break;
			}
		}
		// phpcs:enable
	}

	/**
	 * Track store setup and store properties on save.
	 *
	 * @return void
	 */
	public static function track_store_setup() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput
		$properties = array(
			'country'        => isset( $_POST['store_country'] ) ? sanitize_text_field( $_POST['store_country'] ) : '',
			'currency_code'  => isset( $_POST['currency_code'] ) ? sanitize_text_field( $_POST['currency_code'] ) : '',
			'product_type'   => isset( $_POST['product_type'] ) ? sanitize_text_field( $_POST['product_type'] ) : '',
			'sell_in_person' => isset( $_POST['sell_in_person'] ) && ( 'yes' === sanitize_text_field( $_POST['sell_in_person'] ) ),
		);
		// phpcs:enable

		WC_Tracks::record_event( 'obw_store_setup', $properties );
	}

	/**
	 * Tracks when Jetpack is activated through the OBW.
	 *
	 * @return void
	 */
	public static function track_activate() {
		WC_Tracks::record_event( 'obw_activate' );
	}
}
