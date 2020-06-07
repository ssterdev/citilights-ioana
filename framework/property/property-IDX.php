<?php
if( !function_exists( 're_hide_IDX_page_template' ) ) :
	function re_hide_IDX_page_template() {
		global $pagenow;
		if ( !in_array( $pagenow, array( 'post-new.php', 'post.php') ) || get_post_type() != 'page' ) {
			return false;
		}

		?>
		<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					$('#page_template option[value="page-dsIDX.php"]').remove();
				})
			})(jQuery)
		</script>
		<?php 
	}

	add_action( 'admin_footer', 're_hide_IDX_page_template', 10 );
endif;


/**
 * Get list property idx
 *
 * @package 	Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( ! function_exists( 'noo_idx_list_property' ) ) :
	
	function noo_idx_list_property() {

		if ( class_exists( 'dsSearchAgent_ApiRequest' ) ) {

			$apiRequestParams = array();
			$apiRequestParams["directive.ResultsPerPage"] = 100;
			$apiRequestParams["responseDirective.ViewNameSuffix"] = "widget";
			$apiRequestParams["responseDirective.DefaultDisplayType"] = 'listed';
			$apiRequestParams['responseDirective.IncludeDisclaimer'] = 'true';
			$apiRequestParams["directive.SortOrders[0].Column"] = 'DateAdded';
			$apiRequestParams["directive.SortOrders[0].Direction"] = 'DESC';

			$apiHttpResponse = dsSearchAgent_ApiRequest::FetchData("Results", $apiRequestParams);
			if (empty($apiHttpResponse["errors"]) && $apiHttpResponse["response"]["code"] == "200") {
				$data       = $apiHttpResponse["body"];
				$data       = preg_match( '/\<script\>(.*?)\<\/script\>/is', $data, $dsidx_data);
				
				if (isset($dsidx_data[0])) {

					preg_match_all( '/\{"PhotoCount(.*?)\}/is', $dsidx_data[0], $list_property);

					$render_list_property = array();

					foreach ($list_property[0] as $property_items) {

					    $property_item = json_decode( $property_items, true );

					    $info_address = array_map( 'trim', explode( ',', $property_item['ShortDescription'] ) );

					    $property_item['noo_property_bathrooms'] = (int)substr( $property_item['BathsShortString'], 0, 1 );
					    $property_item['noo_property_bedrooms'] = (int)substr( $property_item['BedsShortString'], 0, 1 );
					    $property_item['noo_property_area'] = $property_item['ImprovedSqFt'] . ' ' . re_get_property_setting('area_unit');
					    $property_item['latitude'] = $property_item['Latitude'];
					    $property_item['longitude'] = $property_item['Longitude'];
					    $property_item['url'] = home_url( '/idx/' . $property_item['PrettyUriForUrl'] );
					    $property_item['image'] = '<img src="' . $property_item['PhotoUriBase'] . '0-medium.jpg" alt="*" />';
					    $property_item['icon_markers'] = 'fa-home';
					    $property_item['city'] = $info_address[1];
					    $property_item['title'] = $info_address[0] . ', ' . $info_address[1];
						
						$theme_uri = get_stylesheet_directory_uri();
						
					    $field_icons = re_property_summary_field_icons();
					    $icon_bedrooms = $theme_uri . "/assets/images/size-icon-2.png";
					    $icon_bathrooms = $theme_uri . "/assets/images/bedroom-icon-2.png";
					    $icon_area = $theme_uri . "/assets/images/bathroom-icon-2.png";

					    $property_item['info_summary'] = '<div class="info-detail">
															<div class="bedrooms">
																<span class="property-meta-icon" style="background-image: url(' . $icon_bedrooms . ');"></span>
																<span class="property-meta">' . $property_item['noo_property_bedrooms'] . '</span>
															</div>
															<div class="bathrooms">
																<span class="property-meta-icon" style="background-image: url(' . $icon_bathrooms . ');"></span>
																<span class="property-meta">' . $property_item['noo_property_bathrooms'] . '</span>
															</div>
															<div class="area">
																<span class="property-meta-icon" style="background-image: url(' . $icon_area . ');"></span>
																<span class="property-meta">' . $property_item['noo_property_area'] . '</span>
															</div>
														  </div>';

					    /**
					     * Unset item
					     */
					    unset( $property_item['BathsShortString'] );
					    unset( $property_item['BedsShortString'] );
					    // unset( $property_item['Latitude'] );
					    // unset( $property_item['Longitude'] );
					    unset( $property_item['PrettyUriForUrl'] );
					    unset( $property_item['ShortDescription'] );
					    unset( $property_item['PhotoCount'] );
					    unset( $property_item['PhotoUriBase'] );
					    unset( $property_item['SalePrice'] );
					    unset( $property_item['Status'] );
					    unset( $property_item['MlsNumber'] );
					    unset( $property_item['ListingAttribution'] );
					    unset( $property_item['IdxIconUri'] );
					    unset( $property_item['ImprovedSqFt'] );

					    $render_list_property[] = $property_item;
					}

					echo '<script>Noo_Source_IDX = ' . json_encode( $render_list_property ) . '</script>';
				}

			}

		}

	}

	add_action( 'wp_footer', 'noo_idx_list_property' );

endif;