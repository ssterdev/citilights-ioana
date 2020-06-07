<?php

if( !function_exists( 're_get_property_price_html' ) ) :
	function re_get_property_price_html($post_id = '', $label = true){
		if( empty( $post_id ) ) return;

		$price              = trim( get_post_meta($post_id,'_price',true) );
		$price              = is_numeric( $price ) ? re_format_price($price) : esc_html( $price );
		$price_label        = esc_html(get_post_meta($post_id,'_price_label',true));
		$before_price_label = esc_html(get_post_meta($post_id,'_before_price_label',true));
		if($label)
			return $before_price_label . ' ' . $price.' '.$price_label;
		else 
			return $price;
	}
endif;

if( !function_exists( 're_get_property_price_field' ) ) :
	function re_get_property_price_field(){
		$price_field = array(
					'name' => '_price',
					'label' => __('Price','noo'),
					'type' => 'text',
					'value' => '',
					'is_default' => true,
					'required' => false
				);

		return apply_filters( 're_property_price_field', $price_field );
	}
endif;

if( !function_exists( 're_format_price' ) ) :
	function re_format_price( $price, $html = true ) {
		$return            = '';
		$currency_code     = re_get_property_setting('currency');
		$currency_symbol   = re_get_currency_symbol($currency_code);
		$currency_position = re_get_property_setting('currency_position');
		switch ( $currency_position ) {
			case 'left' :
				$format = '%1$s%2$s';
				break;
			case 'right' :
				$format = '%2$s%1$s';
				break;
			case 'left_space' :
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space' :
				$format = '%2$s&nbsp;%1$s';
				break;
			default:
				$format = '%1$s%2$s';
		}
		
		$thousands_sep = wp_specialchars_decode( stripslashes(re_get_property_setting('price_thousand_sep')),ENT_QUOTES);
		$decimal_sep = wp_specialchars_decode( stripslashes(re_get_property_setting('price_decimal_sep')),ENT_QUOTES);
		$num_decimals = re_get_property_setting('price_num_decimals');
		
		if(!$html) {
			return _re_price_number_format( $price, $num_decimals, '.', '', $currency_code );
		}
		
		$price 	= _re_price_number_format( $price, $num_decimals, $decimal_sep, $thousands_sep, $currency_code );
		if('text' === $html) {
			return sprintf( $format, $currency_symbol, $price );
		}

		if('number' === $html) {
			return $price;
		}

		//$price = preg_replace( '/' . preg_quote( re_get_property_setting('price_decimal_sep'), '/' ) . '0++$/', '', $price );
		$return = '<span class="amount">' . sprintf( $format, $currency_symbol, $price ) . '</span>';
		
		return $return;
	}
endif;

if( !function_exists( '_re_price_inr_comma' ) ) :
	function _re_price_inr_comma($input, $thousands_sep = ',') {
	    // This function is written by some anonymous person – I got it from Google
		if(strlen($input)<=2)
			{ return $input; }
		$length=substr($input,0,strlen($input)-2);
		$formatted_input = _re_price_inr_comma($length, $thousands_sep).$thousands_sep.substr($input,-2);
		return $formatted_input;
	}
endif;

if( !function_exists( '_re_price_number_format' ) ) :
	// Create custom function because some currency need special treat
	function _re_price_number_format($num, $num_decimals = 3, $decimal_sep = '.', $thousands_sep = ',', $currency_code = '' ) {

		if ( empty( $num ) ) return;
		
		if( empty( $currency_code ) || $currency_code != 'INR' ) {
			return number_format( (float)$num, $num_decimals, $decimal_sep, $thousands_sep );
		}

	    // Special format for Indian Rupee
		$pos = strpos((string)$num, '.');
		if ($pos === false) {
			$decimalpart = str_repeat("0", $num_decimals);
		}
		else {
			$decimalpart = substr($num, $pos+1, $num_decimals);
			$num = substr($num, 0, $pos);
		}

		$decimalpart = !empty($decimalpart) ? $decimal_sep . $decimalpart : '';

		if(strlen($num) > 3 & strlen($num) <= 12) {
			$last3digits = substr($num, -3 );
			$numexceptlastdigits = substr($num, 0, -3 );
			$formatted = _re_price_inr_comma($numexceptlastdigits, $thousands_sep);
			$stringtoreturn = $formatted.$thousands_sep.$last3digits.$decimalpart ;
		} elseif(strlen($num)<=3) {
			$stringtoreturn = $num.$decimalpart ;
		} elseif(strlen($num)>12) {
			$stringtoreturn = number_format( $num, $num_decimals, $decimal_sep, $thousands_sep );
		}

		if(substr($stringtoreturn,0,2) == ( '-' . $decimal_sep ) ) {
			$stringtoreturn = '-'.substr( $stringtoreturn, 2 );
		}

		return $stringtoreturn;
	}
endif;

if( !function_exists('re_property_render_price_search_field') ):
	function re_property_render_price_search_field() {
		global $wpdb;

		$min_price = $max_price = 0;
		$min_price = ceil( $wpdb->get_var('
				SELECT min(meta_value + 0)
				FROM '.$wpdb->posts.'
				LEFT JOIN '.$wpdb->postmeta.' ON '.$wpdb->posts.'.ID = '.$wpdb->postmeta.'.post_id
				WHERE meta_key = \'_price\' AND post_type = \'noo_property\' AND post_status = \'publish\' ') );
		$max_price = ceil( $wpdb->get_var('
				SELECT max(meta_value + 0)
				FROM '.$wpdb->posts.'
				LEFT JOIN '.$wpdb->postmeta.' ON '.$wpdb->posts.'.ID = '.$wpdb->postmeta.'.post_id
				WHERE meta_key = \'_price\' AND post_type = \'noo_property\' AND post_status = \'publish\' ') );
		$g_min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : $min_price;
		$g_max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : $max_price;
		

		$price_style = re_get_property_search_setting('p_style','slide');		
		?>
		<?php if('slide' == $price_style):?>
			<div class="form-group gprice">
				<span class="gprice-label"><?php _e('Price','noo')?></span>
				<div class="gprice-slider-range"></div>
				<input type="hidden" name="min_price" class="gprice_min" data-min="<?php echo $min_price ?>" value="<?php echo $g_min_price ?>">
				<input type="hidden" name="max_price" class="gprice_max" data-max="<?php echo $max_price ?>" value="<?php echo $g_max_price ?>">
			</div>
		<?php else: ?>
			<div class="form-group">
				<div class="noo-box-select">
					<?php noo_price_range_dropdown($min_price,$max_price);?>
				</div>
			</div>
		<?php
		endif;
	}
endif;
