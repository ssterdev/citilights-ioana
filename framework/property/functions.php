<?php
if( !function_exists( 're_get_property_setting' ) ) :
	function re_get_property_setting( $id = null, $default = null ) {
		return noo_get_setting('noo_property_general', $id, $default);
	}
endif;

if( !function_exists( 're_get_property_contact_setting' ) ) :
	function re_get_property_contact_setting( $id = null, $default = null ) {
		return noo_get_setting('noo_property_contact', $id, $default);
	}
endif;

if( !function_exists( 're_get_property_search_setting' ) ) :
	function re_get_property_search_setting( $id = null, $default = null ) {
		return noo_get_setting('noo_property_advanced_search', $id, $default);
	}
endif;

if (!function_exists('re_get_property_near_by_setting')) :
	function re_get_property_near_by_setting($id = null , $default = null ){
		return noo_get_setting('noo_property_near_by', $id ,$default);
	}
endif;

if( !function_exists( 're_get_property_map_setting' ) ) :
	function re_get_property_map_setting( $id = null, $default = null ) {
		return noo_get_setting('noo_property_google_map', $id, $default);
	}
endif;

if( !function_exists( 're_get_property_cf_setting' ) ) :
	function re_get_property_cf_setting( $id = null, $default = null ) {
		if( $id == 'custom_field' ) {
			return re_get_property_custom_fields();
		} else {
			$options = get_option('noo_property_custom_filed');
			if (isset($options[$id])) {
				return $options[$id];
			}
		}

		return $default;
	}
endif;

if( !function_exists( 're_get_currencies' ) ) :
	function re_get_currencies() {
		return array_unique(
			apply_filters( 'noo_property_currencies',
				array(
						'AED' => __( 'United Arab Emirates Dirham', 'noo' ),
						'EUR' => __( 'Euros', 'noo' ),
						'AUD' => __( 'Australian Dollars', 'noo' ),
						'BDT' => __( 'Bangladeshi Taka', 'noo' ),
						'BRL' => __( 'Brazilian Real', 'noo' ),
						'BGN' => __( 'Bulgarian Lev', 'noo' ),
						'CAD' => __( 'Canadian Dollars', 'noo' ),
						'CLP' => __( 'Chilean Peso', 'noo' ),
						'CNY' => __( 'Chinese Yuan', 'noo' ),
						'COP' => __( 'Colombian Peso', 'noo' ),
						'HRK' => __( 'Croatia kuna', 'noo' ),
						'CZK' => __( 'Czech Koruna', 'noo' ),
						'DKK' => __( 'Danish Krone', 'noo' ),
						'HKD' => __( 'Hong Kong Dollar', 'noo' ),
						'HUF' => __( 'Hungarian Forint', 'noo' ),
						'ISK' => __( 'Icelandic krona', 'noo' ),
						'IDR' => __( 'Indonesia Rupiah', 'noo' ),
						'INR' => __( 'Indian Rupee', 'noo' ),
						'ILS' => __( 'Israeli Shekel', 'noo' ),
						'JPY' => __( 'Japanese Yen', 'noo' ),
						'KES' => __( 'Kenyan Shilling', 'noo' ),
						'MYR' => __( 'Malaysian Ringgits', 'noo' ),
						'MXN' => __( 'Mexican Peso', 'noo' ),
						'NGN' => __( 'Nigerian Naira', 'noo' ),
						'NOK' => __( 'Norwegian Krone', 'noo' ),
						'NZD' => __( 'New Zealand Dollar', 'noo' ),
						'PHP' => __( 'Philippine Pesos', 'noo' ),
						'PKR' => __( 'Pakistani Rupees', 'noo' ),
						'PLN' => __( 'Polish Zloty', 'noo' ),
						'GBP' => __( 'Pounds Sterling', 'noo' ),
						'RON' => __( 'Romanian Leu', 'noo' ),
						'RUB' => __( 'Russian Ruble', 'noo' ),
						'SGD' => __( 'Singapore Dollar', 'noo' ),
						'ZAR' => __( 'South African rand', 'noo' ),
						'KRW' => __( 'South Korean Won', 'noo' ),
						'SEK' => __( 'Swedish Krona', 'noo' ),
						'CHF' => __( 'Swiss Franc', 'noo' ),
						'TWD' => __( 'Taiwan New Dollars', 'noo' ),
						'THB' => __( 'Thai Baht', 'noo' ),
						'TRY' => __( 'Turkish Lira', 'noo' ),
						'USD' => __( 'US Dollars', 'noo' ),
						'VND' => __( 'Vietnamese Dong', 'noo' ),
						'CLN' => __( 'Colones', 'noo' ),
				)
			)
		);
	}
endif;

if( !function_exists( 're_get_currency_symbol' ) ) :
	function re_get_currency_symbol( $currency = '' ) {
		if ( ! $currency ) {
			$currency = re_get_property_setting('currency');
		}
	
		switch ( $currency ) {
			case 'AED' :
				$currency_symbol = 'د.إ';
				break;
			case 'BDT':
				$currency_symbol = '&#2547;&nbsp;';
				break;
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'BGN' :
				$currency_symbol = '&#1083;&#1074;.';
				break;
			case 'AUD' :
			case 'CAD' :
			case 'CLP' :
			case 'MXN' :
			case 'NZD' :
			case 'HKD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '&#36;';
				break;
			case 'EUR' :
				$currency_symbol = '&euro;';
				break;
			case 'CNY' :
			case 'RMB' :
			case 'JPY' :
				$currency_symbol = '&yen;';
				break;
			case 'RUB' :
				$currency_symbol = '&#1088;&#1091;&#1073;.';
				break;
			case 'KRW' : $currency_symbol = '&#8361;'; break;
			case 'TRY' : $currency_symbol = '&#84;&#76;'; break;
			case 'NOK' : $currency_symbol = '&#107;&#114;'; break;
			case 'ZAR' : $currency_symbol = '&#82;'; break;
			case 'CZK' : $currency_symbol = '&#75;&#269;'; break;
			case 'MYR' : $currency_symbol = '&#82;&#77;'; break;
			case 'DKK' : $currency_symbol = 'kr.'; break;
			case 'HUF' : $currency_symbol = '&#70;&#116;'; break;
			case 'IDR' : $currency_symbol = 'Rp'; break;
			case 'INR' : $currency_symbol = '&#8377;'; break;
			case 'ISK' : $currency_symbol = 'Kr.'; break;
			case 'ILS' : $currency_symbol = '&#8362;'; break;
			case 'PHP' : $currency_symbol = '&#8369;'; break;
			case 'PKR' : $currency_symbol = 'Rs'; break;
			case 'PLN' : $currency_symbol = '&#122;&#322;'; break;
			case 'SEK' : $currency_symbol = '&#107;&#114;'; break;
			case 'CHF' : $currency_symbol = '&#67;&#72;&#70;'; break;
			case 'TWD' : $currency_symbol = '&#78;&#84;&#36;'; break;
			case 'THB' : $currency_symbol = '&#3647;'; break;
			case 'GBP' : $currency_symbol = '&pound;'; break;
			case 'RON' : $currency_symbol = 'lei'; break;
			case 'VND' : $currency_symbol = '&#8363;'; break;
			case 'NGN' : $currency_symbol = '&#8358;'; break;
			case 'HRK' : $currency_symbol = 'Kn'; break;
			case 'KES' : $currency_symbol = 'KSh'; break;
			case 'CLN' : $currency_symbol = '&#8353;'; break;
			default    : $currency_symbol = ''; break;
		}
	
		return apply_filters( 'noo_property_currency_symbol', $currency_symbol, $currency );
	}
endif;


function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}
function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}