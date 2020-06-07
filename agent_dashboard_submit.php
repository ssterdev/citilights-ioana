<?php
/*
Template Name: Agent Dashboard Submit
*/

NooAgent::check_logged_in_user();

set_time_limit (600);

$current_user = wp_get_current_user();

$user_id  = $current_user->ID;
$agent_id = intval( get_user_meta($user_id, '_associated_agent_id', true ) );
$prop_id  = '';
// Membership information
$membership_info		= NooAgent::get_membership_info( $agent_id );
$membership_type		= $membership_info['type'];
$admin_approve			= re_get_agent_setting('noo_admin_approve', 'add');

$has_err            = false;
$err_message        = array();
$success            = false;

// Default Value
$post_status = 'publish';

// Description & Price
$title              = '';
$desc               = '';
$price              = '';
$price_label        = '';
$before_price_label = '';
$status             = '';
$type               = '';
$bedrooms           = '';
$bathrooms          = '';

// Featured image & Gallery
$featured_img		= '';
$gallery			= '';
$floor_plan			= '';
$file				= '';

// Additional info
$fields = re_get_property_custom_fields();

// Location
$address            = '';
$location           = '';
$sub_location       = '';
$lat                = re_get_property_map_setting('latitude','40.714398');
$long               = re_get_property_map_setting('longitude','-74.005279');

// Featured
$featured           = false;

// Video
$video				= '';
$virtual			= '';

// Amenities & Features
$custom_features = re_get_property_feature_fields();
$features = array();
$features_checklist = array();
$features_prefix = 'noo_property_feature';
if($custom_features){
	foreach ($custom_features as $key => $feature){
		if( empty($key) ) continue;
		$features[$key] = array(
			'label' => ucfirst($feature),
			'id'    => '_'.$features_prefix.'_'.$key,
			'name'  => $features_prefix.'['.$key.']',
			'value' => 'no'
		);
		$features_checklist[] = $key;
	}
}

$sub_listing_default = array(
'title_sub_listing'         => '',
'bedroom_sub_listing'       => '',
'bathroom_sub_listing'      => '',
'size_sub_listing'          => '',
'price_sub_listing'         => '',
'type_sub_listing'          => '',
'available_sub_listing'     => '',
);


$sub_listing = ! empty($sub_listing) ? $prop_id: array_merge(array($sub_listing_default) , array($sub_listing_default));

$current_data_default = array(
	'additional_feature_label' => '',
	'additional_feature_value' => '',
);
$current_data =! empty( $current_data ) ? $prop_id: array_merge( array($current_data_default), array( $current_data_default ));


$action       = 'add';
$submit_title = __('Submit Property', 'noo');
$submit_text  = __('Add Property', 'noo');

// Property editing, get value from database
if( isset( $_GET['prop_edit'] ) && is_numeric( $_GET['prop_edit'] ) ) {

	$prop_id  =  intval ($_GET['prop_edit']);
	if( !NooAgent::can_edit( $agent_id ) || !NooAgent::is_owner( $agent_id, $prop_id ) ) {
		exit('You don\'t have the rights to edit this property');
	}

	$the_property = get_post( $prop_id);

	$post_status = $the_property->post_status;

	// Description & Price
	$title              = get_the_title($prop_id); 
	$desc               = get_post_field('post_content', $prop_id);
	$price              = get_post_meta($prop_id,'_price',true);
	$price_label        = esc_html( get_post_meta($prop_id,'_price_label',true) );
	$before_price_label = esc_html( get_post_meta($prop_id,'_before_price_label',true) );

	$type_array      = get_the_terms( $prop_id, 'property_category' );
    if(isset($type_array[0])) {
        $type = esc_html( $type_array[0]->slug );
    }
	$status_array       = get_the_terms($prop_id, 'property_status');
	if(isset($status_array[0])) {
		$status         = esc_html( $status_array[0]->slug );
	}

	// Featured Image and Gallery
	$featured_img		= get_post_thumbnail_id($prop_id);
	$gallery			= esc_attr( get_post_meta($prop_id,'_gallery',true) );
    $file				= esc_html( get_post_meta($prop_id,'_pdf_file',true) );

	$floor_plan			= esc_attr( get_post_meta($prop_id,'_floor_plan',true) );

	// Location
	$address            = esc_html( get_post_meta($prop_id,'_address',true) );
	$location_array     = get_the_terms($prop_id, 'property_location');
	if(isset($location_array[0])) {
		$location       = esc_html( $location_array[0]->slug );
	}
	$sub_location_array = get_the_terms($prop_id, 'property_sub_location');
	if(isset($sub_location_array[0])) {
		$sub_location   = esc_html( $sub_location_array[0]->slug );
	}
	$lat                = get_post_meta($prop_id,'_noo_property_gmap_latitude',true) ? get_post_meta($prop_id,'_noo_property_gmap_latitude',true) : $lat;
	$long               = get_post_meta($prop_id,'_noo_property_gmap_longitude',true) ?  get_post_meta($prop_id,'_noo_property_gmap_longitude',true) : $long;

	$lat				= esc_html( $lat );
	$long				= esc_html( $long );

	$featured			= esc_attr( get_post_meta($prop_id,'_featured',true) ) == 'yes';

	// Video
	$video				= esc_url( get_post_meta($prop_id,'_video_embedded',true));
	$virtual			= esc_attr(get_post_meta($prop_id,'_virtual_tour',true));

	// Features & Amenities
	if($features){
		foreach ($features as $index => $feature){
			$features[$index]['value'] = esc_html( get_post_meta($prop_id,$feature['id'],true) );
			$features[$index]['value'] = empty( $features[$index]['value'] ) ? 'no' : $features[$index]['value'];
		} 
	}
	$sub_listing = get_post_meta($prop_id, 'sub_listing', true);
	
	$current_data = get_post_meta( $prop_id, 'additional_features', true );


	$action       = 'edit';
	$submit_title = __('Edit Property', 'noo');
	$submit_text  = __('Update Property', 'noo');
}

// Permission for Featured property
$need_approve				= true;
switch( $admin_approve ) {
	case 'add':
		$need_approve		= ( $action == 'add' );
		break;
	case 'none':
		$need_approve		= false;
		break;
	default:
		$need_approve		= true;
		break;
}

if( $need_approve ) {
	$post_status = 'pending';
}

$featured_permision	= array(
		'allow'		=> false,
		'message'	=> ''
	);
if( $membership_type == 'membership' ) {
	if( $membership_info['data']['featured_remain'] == 0 ) {
		$featured_permision['message'] = __('Please upgrade your membership before you can make this listing featured.', 'noo');
	} else {
		if( $post_status == 'publish' ) {
			$featured_permision['allow'] = true;
			$featured_permision['message'] = __('Make this listing featured. The number of featured items will be subtracted from your package.', 'noo');
		} else {
			$featured_permision['message'] = __('You can make this listing featured after your property is approved.', 'noo');
		}
	}
} elseif( $membership_type == 'submission' ) {
	$featured_permision['message'] = __('Make this listing featured from Your Properties list.', 'noo');
}

// Submit handler
// ===============================
if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {

	// Check nonce
	if ( !isset($_POST['_noo_property_nonce']) || !wp_verify_nonce($_POST['_noo_property_nonce'],'submit_property') ){
		exit(__('Sorry, your session is expired or you submitted an invalid property form.', 'noo'));
	}

	// Agent checking
	$submit_agent_id	= intval( $_POST['_agent_id'] );
	if( empty( $agent_id ) && empty( $submit_agent_id ) ) {
		$agent_id = NooAgent::create_agent_from_user( $user_id );
		if( !$agent_id ) {
			$has_err = true;
			$err_message[] = __('There\'s an unknown error when creating an agent profile for your account. Please resubmit your property or contact Administrator.', 'noo');
		}
	} elseif( $agent_id != $submit_agent_id ) {
		$has_err = true;
		$err_message[] = __('There\'s an unknown error. Please resubmit your property or contact Administrator.', 'noo');
	}

	if( !$has_err ) {
		// variable
		$no_html			= array();
		$allowed_html		= array(
			'a' => array(
				'href' => array(),
				'target' => array(),
				'title' => array(),
				'rel' => array(),
			),
			'img' => array(
				'src' => array()
			),
			'h1' => array(),
			'h2' => array(),
			'h3' => array(),
			'h4' => array(),
			'h5' => array(),
			'p' => array(),
			'br' => array(),
			'hr' => array(),
			'span' => array(),
			'em' => array(),
			'strong' => array(),
			'small' => array(),
			'b' => array(),
			'i' => array(),
			'u' => array(),
			'ul' => array(),
			'ol' => array(),
			'li' => array(),
			'blockquote' => array(),
		);
	
		// Submit data
		$title              = wp_kses( $_POST['_title'], $no_html );
		$desc               = wp_kses( $_POST['_desc'], $allowed_html );
		$price              = wp_kses( $_POST['_price'], $no_html );
		$price_label        = wp_kses( $_POST['_price_label'], $no_html );
		$before_price_label = wp_kses( $_POST['_before_price_label'], $no_html );
	
		if( !isset($_POST['_status']) ) {
			$status			= '';
		} else {
			$status			= wp_kses( $_POST['_status'], $no_html );
		}
		if( !isset($_POST['_category']) ) {
			$type			= '';
		} else {
			$type			= wp_kses( $_POST['_category'], $no_html );
		}

		// Featured Image and Gallery
		$gallery			= !empty( $_POST['_gallery'] ) ? $_POST['_gallery'] : '';
		$floor_plan			= !empty( $_POST['_floor_plan'] ) ? $_POST['_floor_plan'] : '';
		$file				= !empty( $_POST['_pdf_file'] ) ? $_POST['_pdf_file'] : '';
		$featured_img		= !empty( $_POST['_featured_img'] ) ?  $_POST['_featured_img']  : '';
		$sub_listing        =  isset($_POST['sub_listing']) ? $_POST['sub_listing'] : '';
		$current_data		=  isset($_POST['additional_features']) ? $_POST['additional_features'] : '';
	
		$address			= wp_kses( $_POST['_address'], $no_html );
		if( !isset($_POST['_location']) ) {
			$location		= '';
		} else {
			$location		= wp_kses( $_POST['_location'], $no_html );
		}
		if( !isset($_POST['_sub_location']) ) {
			$sub_location	= '';
		} else {
			$sub_location	= wp_kses( $_POST['_sub_location'], $no_html );
		}

		$lat				= wp_kses( $_POST['_lat'], $no_html );
		$long				= wp_kses( $_POST['_long'], $no_html );

		// Video
		$video				= wp_kses( $_POST['_video'], $no_html );
		// 360 view
		$virtual			= wp_kses( htmlspecialchars($_POST['_virtual']), $no_html );

		$submit_features	= array();
		if( isset( $_POST['_'.$features_prefix] ) && is_array( $_POST['_'.$features_prefix] ) ) {
			foreach ($_POST['_'.$features_prefix] as $key => $feature) {
				if( in_array($key, $features_checklist)) {
					$submit_features[$key] = wp_kses( $feature, $no_html );
					$features[$key]['value'] = $submit_features[$key];
				}
			}
		}

		// Error data checking
		if( empty($title) ) {
			$has_err = true;
			$err_message[] = __('Please submit a title for your property', 'noo');
		}

		if( empty($desc) ) {
			$has_err = true;
			$err_message[] = __('Please input a description for your property', 'noo');
		}

		if( empty( $featured_img ) && empty($gallery) ) {
			$has_err = true;
			$err_message[] = __('Your property needs at least one image', 'noo');
		}

		if( empty($address) ) {
			$has_err = true;
			$err_message[] = __('Your property needs a specific address', 'noo');
		}
	}
	if( ! $has_err ) {
		$post = array(
			'post_title'	=> $title,
			'post_content'	=> $desc,
			'post_status'	=> $post_status, 
			'post_type'		=> 'noo_property'
		);

		if( $_POST['_action'] == 'add' ) {
			if( !NooAgent::can_add( $agent_id ) ) {
				exit('Sorry, you don\'t have the permission to submit any property!');
			}

			$prop_id = wp_insert_post( $post );
			if( !$prop_id ) {
				$has_err = true;
				$err_message[] = __('There\'s an unknown error when inserting your property to database. Please resubmit your property or contact Administrator.', 'noo');
			} else {
				$success = true;
				update_post_meta( $prop_id, '_agent_responsible', $agent_id );
				update_post_meta( $prop_id, '_featured', '' );
				if( NooMembership::is_submission() ) {
					update_post_meta( $prop_id, '_paid_listing', '' );
				}

				// Membership action
				NooAgent::decrease_listing_remain( $agent_id );

				// Email
				$admin_email = get_option('admin_email');
				$site_name = get_option('blogname');
				$property_admin_link = admin_url( 'post.php?post=' . $prop_id ) . '&action=edit';
				$message = '';
				if( $need_approve ) {
					$message .= sprintf( __("A user has just submitted a listing on %s and it's now waiting for your approval. To approve or reject it, please follow this link: %s", 'noo'), $site_name, $property_admin_link) . "<br/><br/>";
					noo_mail($admin_email,
						sprintf(__('[%s] New submission needs approval','noo'), $site_name),
						$message);
				} else {
					$message .= sprintf( __("A user has just submitted a listing on %s. You can check it at %s", 'noo'), $site_name, $property_admin_link) . "<br/><br/>";
					noo_mail($admin_email,
						sprintf(__('[%s] New property submission','noo'), $site_name),
						$message);
				}
			}
		} elseif( $_POST['_action'] == 'edit' ) {
			$prop_id = intval( $_POST['_prop_id'] );
			if( !NooAgent::can_edit( $agent_id ) || !NooAgent::is_owner( $agent_id, $prop_id ) ) {
				exit('You don\'t have the rights to edit this property');
			}

			if( !empty( $prop_id ) ) {
				$post['ID'] = $prop_id;

				if( 0 === wp_update_post( $post ) ) {
					$has_err = true;
					$err_message[] = __('There\'s an unknown error when updating your property. Please resubmit your property or contact Administrator.', 'noo');
				} else {
					$success = true;

					// Email
					$admin_email = get_option('admin_email');
					$site_name = get_option('blogname');
					$property_admin_link = admin_url( 'post.php?post=' . $prop_id ) . '&action=edit';
					$message = '';

					if( $need_approve ) {
						$message .= sprintf( __("A user has just edited one of his listings and it's now waiting for your approval. To approve or reject it, please follow this link: %s", 'noo'), $property_admin_link) . "<br/><br/>";
						noo_mail($admin_email,
							sprintf(__('[%s] New submission needs approval','noo'), $site_name),
							$message);
					} else {
						$message .= sprintf( __("A user has just edited one of his listings. You can check it at %s", 'noo'), $property_admin_link) . "<br/><br/>";
						noo_mail($admin_email,
							sprintf(__('[%s] A listing has been edited','noo'), $site_name),
							$message);
					}
				}
				
			}
		}

		// Update property meta when insert/update succeeded
		if( $success ) {
			update_post_meta( $prop_id, '_price', $price );
			update_post_meta( $prop_id, '_price_label', $price_label );
			update_post_meta( $prop_id, '_before_price_label', $before_price_label );


			if( !empty($status) ) {
				wp_set_object_terms($prop_id, $status,'property_status'); 
			}
			if( !empty($type) ) {
				wp_set_object_terms($prop_id, $type,'property_category'); 
			}

			$gallery_arr = !empty($gallery) ? array_unique( $gallery ) : array();
			
			$featured_img_index = 0;
			if( empty($featured_img) ) {
				$featured_img = $gallery_arr[0];
			} else {
				$featured_img = $featured_img;
			}
			if(!empty($gallery_arr) && is_array($gallery_arr)){
				foreach ($gallery_arr as $index => $gallery_item) {
					$gallery_arr[$index] = trim($gallery_arr[$index]);

					if( is_numeric( $gallery_arr[$index] ) ) {
						wp_update_post( array(
							'ID' => $gallery_item,
							'post_parent' => $prop_id
						));
					}

					// if( $featured_img == $gallery_item ) {
					// 	$featured_img_index = $index;
					// }
				}

				// unset( $gallery_arr[$featured_img_index] );
				$gallery = implode(',', $gallery_arr);
			}
			/**
			 * Upload Floor Plan
			 */
				$floor_plan_arr = !empty($floor_plan) ? array_unique( $floor_plan ) : array();
				if(!empty($floor_plan_arr)  && is_array($floor_plan_arr)){
					foreach ($floor_plan_arr as $index => $floor_plan_item) {
						$floor_plan_arr[$index] = trim($floor_plan_arr[$index]);

						if( is_numeric( $floor_plan_arr[$index] ) ) {
							wp_update_post( array(
								'ID'          => $floor_plan_item,
								'post_parent' => $prop_id
							));
						}

					}

					$floor_plan = implode(',', $floor_plan_arr);
				}


			$file_arr = !empty($file) ? array_unique( $file ) : array();

			if(!empty($file_arr) && is_array($file_arr)){
				foreach ($file_arr as $index => $file_item) {
					$file_arr[$index] = trim($file_arr[$index]);

					if( is_numeric( $file_arr[$index] ) ) {
						wp_update_post( array(
							'ID'          => $file_item,
							'post_parent' => $prop_id
						));
					}

				}

				$file = implode(',', $file_arr);
			}

			set_post_thumbnail( $prop_id, $featured_img );
			update_post_meta( $prop_id, '_gallery', $gallery );
			update_post_meta( $prop_id, '_floor_plan', $floor_plan );
			update_post_meta( $prop_id, '_pdf_file', $file );

			re_property_save_custom_fields( $prop_id, $_POST );

			update_post_meta( $prop_id, '_address', $address );
			if( !empty($location) ) {
				wp_set_object_terms($prop_id, $location,'property_location'); 
			}
			if( !empty($sub_location) ) {
				wp_set_object_terms($prop_id, $sub_location,'property_sub_location'); 
			}
			update_post_meta( $prop_id, '_noo_property_gmap_latitude', $lat );
			update_post_meta( $prop_id, '_noo_property_gmap_longitude', $long );

			foreach( $submit_features as $feature_key => $submit_feature ) {
				update_post_meta( $prop_id, "_{$features_prefix}_{$feature_key}", $submit_feature );
			}


	        if ( isset( $_POST[ 'sub_listing' ] ) && is_array( $_POST[ 'sub_listing' ] ) ) {
	            update_post_meta( $prop_id, 'sub_listing', array_values( $_POST[ 'sub_listing' ] ) );
	        }
	        if ( isset( $_POST[ 'additional_features' ] ) && is_array( $_POST[ 'additional_features' ] ) ) {
	            update_post_meta( $prop_id, 'additional_features', array_values( $_POST[ 'additional_features' ] ) );
	        }


			// Featured property
			// Only update if change from no featured to featured
			if( !$featured ) {
				$submit_featured = isset( $_POST['_featured'] ) ? (bool) wp_kses( $_POST['_featured'], $no_html ) : $featured;
				
				if( $submit_featured && $featured_permision['allow'] ) {
					update_post_meta( $prop_id, '_featured', 'yes' );
					NooAgent::decrease_featured_remain( $agent_id );
				} elseif ( !$submit_featured ) {
					update_post_meta( $prop_id, '_featured', '' );
				}
			}

			update_post_meta( $prop_id, '_video_embedded', $video );
			update_post_meta( $prop_id, '_virtual_tour', $virtual );

			// reset query
			wp_reset_query();

			// redirect to dashboard default
			$redirect = noo_get_page_link_by_template( 'agent_dashboard.php' );
			wp_redirect( $redirect);
		}
	}
}

get_header(); ?>
<div class="container-wrap">
	<div class="main-content container-boxed max offset">
		<div class="row">
			<div class="noo-sidebar col-md-4">
				<div class="noo-sidebar-wrapper">
				<?php get_template_part( 'layouts/' . 'agent_menu' );  ?>
				</div>
			</div>
			<div class="<?php noo_main_class(); ?>" role="main">   
				<div class="submit-header">
					<h1 class="page-title"><?php echo $submit_title; ?></h1>
				</div>
				<?php if( ( $action == 'add' ) && !NooAgent::can_add( $agent_id ) ) : ?>
				<div class="submit-content">
				<h4><?php if( NooMembership::is_membership() ) {
					_e('Your current package doesn\'t let you publish properties anymore! You will have to upgrade your membership first.', 'noo');
					do_shortcode( '[noo_membership_packages style="ascending" featured_item="2" ]' );
				} else {
					_e('Sorry, you don\'t have the permission to submit any property!', 'noo');
				}
				?></h4>
				</div>
				<?php else : ?>
				<div class="submit-content">
					<?php if( $has_err && !empty($err_message) ) : ?>
						<div class="submit-error">
							<?php foreach ($err_message as $message) : ?>
							<div class="noo-message alert alert-danger alert-dimissible" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e( 'Close', 'noo' ); ?></span></button>
								<?php echo $message; ?>
							</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php re_property_enqueue_map_picker_script(); ?>
					<form id="new_post" name="_new_post" method="post" enctype="multipart/form-data" class="noo-form property-form" role="form">
						<div class="noo-control-group">
							<div class="group-title">
								<?php _e('Property Description & Price', 'noo'); ?>
							</div>
							<div class="group-container row">
								<div class="col-md-8">
									<div class="form-group s-prop-title">
										<label for="title"><?php _e('Title','noo'); ?>&nbsp;*</label>
										<input type="text" id="title" class="form-control required" value="<?php echo $title; ?>" name="_title" required />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group s-prop-type">
										<label><?php _e('Type','noo'); ?></label>
										<?php $categories = wp_get_object_terms( $prop_id, 'property_category', array( 'fields' => 'slugs' ) ); ?>
							   			<?php re_property_render_taxonomy_field( 'property_category',$categories, $type, __('Type', 'noo') ,'search'); ?>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group s-prop-desc">
										<label for="desc"><?php _e('Description','noo'); ?>&nbsp;*</label>
										<textarea class="form-control required" id="desc" name="_desc" rows="10" required ><?php echo $desc; ?></textarea>
									</div>
								</div>
								<div class="col-md-8">
									<div class="form-group s-prop-price">
										<label for="price"><?php _e('Price','noo'); ?>&nbsp;*&nbsp;(<?php echo re_get_currency_symbol(re_get_property_setting('currency')); ?>)</label>
										<input type="text" id="price" class="form-control required" value="<?php echo $price; ?>" name="_price" required />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group s-prop-status">
										<label><?php _e('Status','noo'); ?></label>
										<?php $offer = wp_get_object_terms( $prop_id, 'property_status', array( 'fields' => 'slugs' ) ); ?>
							   			<?php re_property_render_taxonomy_field( 'property_status', $offer, $status, __( 'Status', 'noo' ), '', array( 'exclude' => get_option('default_property_status') ) ); ?>
							   		</div>
								</div>
								
								<div class="before_price_label col-md-6">
									<div class="form-group s-prop-before-label">
										<label for="before_price_label">
											<?php _e('Before price label (ex: "from")','noo'); ?>
										</label>
										<input type="text" id="before_price_label" class="form-control" value="<?php echo $before_price_label; ?>" name="_before_price_label" />
									</div>
								</div>

								<div class="price_label col-md-6">
									<div class="form-group s-prop-after-label">
										<label for="price_label">
											<?php _e('After price label (ex: "per month")','noo'); ?>
										</label>
										<input type="text" id="price_label" class="form-control" value="<?php echo $price_label; ?>" name="_price_label" />
									</div>
								</div>
								
							</div>
						</div>
						<?php $check_file =  re_get_property_setting('check_file')?>
						<div class="noo-control-group">
							<div class="group-title">
								<?php _e('Property Document', 'noo'); ?>
							</div>
							<div class="group-container row">
								<div class="col-md-12">
									<?php
										noo_upload_form_ajax( array(
											'btn_text'     => esc_html__( 'Upload File', 'noo' ),
											'multi_upload' => 'true',
											'multi_input'  => 'true',
											'name'         => '_pdf_file',
											'set_featured' => 'false',
											'post_id'      => esc_attr( $prop_id ),
											'slider'  	   => 'true',
											'notice'  	   => '',
											'allow_format' => $check_file
										), $file );
									?>
									<p style="margin-top: 15px;"><?php echo "Allowed Upload File Types: $check_file";?></p>
								</div>
							</div>
						</div>

						<div class="noo-control-group">
							<div class="group-title">
								<?php _e('Property Images', 'noo'); ?>
							</div>
							<div class="group-container row">
								<div class="col-md-12">
									<?php
										noo_upload_form_ajax( array(
											'btn_text'     => esc_html__( 'Upload Photo', 'noo' ),
											'multi_upload' => 'true',
											'multi_input'  => 'true',
											'name'         => '_gallery',
											'set_featured' => 'true',
											'post_id'      => esc_attr( $prop_id ),
											'slider'  	   => 'true',
											'notice'  	   => '',
										), $gallery );
									?>
									<p style="margin-top: 15px;"><?php _e('At least 1 image is required for a valid submission. The featured image will be used to dispaly on property listing page.','noo');?></p>

								</div>
								<input type="hidden" id="set_featured" name="_featured_img" value="<?php echo esc_attr( $featured_img ) ?>" />
							</div>
						</div>

						<?php if( re_get_property_setting('floor_plan', 'admin') == 'agent' ) : ?>
							<div class="noo-control-group">
								<div class="group-title">
									<?php _e('Floor Plan', 'noo'); ?>
								</div>
								<div class="group-container row">
									<div class="col-md-12">
										<?php
											noo_upload_form_ajax( array(
												'btn_text'     => esc_html__( 'Upload Photo', 'noo' ),
												'multi_upload' => 'true',
												'multi_input'  => 'true',
												'name'         => '_floor_plan',
												'set_featured' => 'false',
												'post_id'      => esc_attr( $prop_id ),
												'slider'  	   => 'true',
												'notice'  	   => '',
											), $floor_plan );
										?>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if(!empty($fields)) : ?>
						<div class="noo-control-group">
							<div class="group-title">
								<?php _e('Additional Info', 'noo'); ?>
							</div>
							<div class="group-container row">
								<?php foreach ($fields as $field) {
									re_property_render_form_field( $field, $prop_id );
								} ?>
							</div>
						</div>
						<?php endif; ?>
						<div class="noo-control-group">
							<div class="group-title">
								<?php _e('Listing Location', 'noo'); ?>
							</div>
							<div class="group-container row">
								<div class="col-md-8">
									<div class="form-group s-prop-address">
										<label for="address"><?php _e('Address','noo'); ?>&nbsp;*</label>
										<textarea id="address" class="form-control required" name="_address" rows="4" required ><?php echo $address; ?></textarea>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group s-prop-location">
										<label><?php _e('Location','noo'); ?></label>
										<?php $locations = wp_get_object_terms( $prop_id, 'property_location', array( 'fields' => 'slugs' ) ); ?>
							   			<?php re_property_render_taxonomy_field( 'property_location', $locations,$location, __('Location', 'noo') ); ?>
									</div>
									<div class="form-group s-prop-sub_location">
										<label><?php _e('Sub Location','noo'); ?></label>
										<?php $sub_locations = wp_get_object_terms( $prop_id, 'property_sub_location', array( 'fields' => 'slugs' ) ); ?>
							   			<?php re_property_render_taxonomy_field( 'property_sub_location', $sub_locations,$sub_location, __('Sub Location', 'noo') ); ?>
									</div>
								</div>
								<?php $map_type = re_get_property_map_setting('map_type',''); ?>
								<?php if ($map_type == 'google'): ?>
									<div class="col-md-6">
										<div class="form-group s-prop-lat">
											<label for="_noo_property_gmap_latitude"><?php _e('Latitude (Google Maps)','noo'); ?></label>
											<input type="text" id="_noo_property_gmap_latitude" class="form-control" value="<?php echo $lat; ?>" name="_lat" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group s-prop-long">
											<label for="_noo_property_gmap_longitude"><?php _e('Longitude (Google Maps)','noo'); ?></label>
											<input type="text" id="_noo_property_gmap_longitude" class="form-control" value="<?php echo $long; ?>" name="_long" />
										</div>
									</div>
									<div class="col-md-12">
										<div class="noo_property_google_map">
											<div id="noo_property_google_map" class="form-group noo_property_google_map" style="height: 300px; margin-top: 25px; overflow: hidden;position: relative;width: 100%;">
											</div>
											<div class="noo_property_google_map_search">
												<input placeholder="<?php echo __('Search your map','noo')?>" type="text" autocomplete="off" id="noo_property_google_map_search_input">
											</div>
										</div>
									</div>
								<?php elseif($map_type == 'bing'): ?>	
									<div class="col-md-6">
										<div class="form-group s-prop-lat">
											<label for="_noo_property_bmap_latitude"><?php _e('Latitude (Bing Maps)','noo'); ?></label>
											<input type="text" id="_noo_property_bmap_latitude" class="form-control" value="<?php echo $lat; ?>" name="_lat" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group s-prop-long">
											<label for="_noo_property_bmap_longitude"><?php _e('Longitude (Bing Maps)','noo'); ?></label>
											<input type="text" id="_noo_property_bmap_longitude" class="form-control" value="<?php echo $long; ?>" name="_long" />
										</div>
									</div>
									<?php 
									wp_enqueue_script('bing-map-api');
									wp_enqueue_script('bing-map');
									 ?>
									 <div class="col-md-12">
										 <div class="noo-box-map">
											<div data-id="noo_property_bing_map" class="noo_property_bing_map" style="height: 380px; margin-bottom: 30px; overflow: hidden;position: relative;width: 100%;">
												<div id="noo_property_bing_map"></div>
											</div>
											<div class="noo_property_bing_map_search" style="position: absolute;top: 0;left: 20%;">
												<input placeholder="<?php echo __('Search your map','noo')?>" type="text" autocomplete="off" id="noo_property_bing_map_search_input">
											</div>
										</div>
									</div>
								<?php endif ?>
							</div>
						</div>
						<?php if( re_get_property_setting('property_sub_listing', 'admin') == 'agent' ) : ?>	
						<div class="noo-control-group">
							<div class="group-title">
								<?php _e('Sub Listing', 'noo'); ?>
							</div>
							<div class="group-container">
								<div >
									<?php 
							        if (is_array($sub_listing ) ):
							            echo '<div id="rp-item-sub_property_wrap-wrap">';
							            foreach ($sub_listing as $index =>$sub_listing ):
							            $title_sub_listing = ( array_key_exists( 'title_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'title_sub_listing' ] : '';
							            $bedroom_sub_listing = ( array_key_exists( 'bedroom_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'bedroom_sub_listing' ] : '';
							            $bathroom_sub_listing = ( array_key_exists( 'bathroom_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'bathroom_sub_listing' ] : '';
							            $size_sub_listing = ( array_key_exists( 'size_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'size_sub_listing' ] : '';
							            $price_sub_listing = ( array_key_exists( 'price_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'price_sub_listing' ] : '';
							            $type_sub_listing = ( array_key_exists( 'type_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'type_sub_listing' ] : '';
							            $available_sub_listing = ( array_key_exists( 'available_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'available_sub_listing' ] : '';
							            ?>
							            
							            <div class="wrap-sub-property">
							                <div <?php echo ( 0 == $index ) ? 'id="clone_element1" style="display: none; positon:relative;" ' : '' ?> class="rp-sub-property-wrap row">
							                    <i class="remove-sub-property fa fa-remove <?php echo ( $index != 1 ) ? 'show-remove' : '' ?> " style="position: relative;"></i>
							                    <div id="title-sub-listing" class="col-md-12">
							                        <label><?php echo esc_html__('Title','noo'); ?></label><br>
							                        <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][title_sub_listing]" value="<?php echo  $title_sub_listing  ?>"class="form-control"/>
							                    </div>
							                    
						                        <div id="bedrooms-sub-listing" class="col-md-6">
						                            <label><?php echo esc_html__('Bedrooms','noo'); ?></label><br>
						                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][bedroom_sub_listing]" value="<?php echo esc_attr( $bedroom_sub_listing ) ?>" class="form-control"/>
						                        </div>
						                        <div id="bathrooms-sub-listing" class="col-md-6">
						                            <label><?php echo esc_html__('Bathrooms','noo'); ?></label><br>
						                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][bathroom_sub_listing]" value="<?php echo esc_attr( $bathroom_sub_listing ) ?>" class="form-control"/>
						                        </div>
						                        <div id="size-sub-listing" class="col-md-6">
						                            <label><?php echo esc_html__('Property Size','noo'); ?></label><br>
						                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][size_sub_listing]" value="<?php echo esc_attr( $size_sub_listing ) ?>" class="form-control"/>
						                        </div>
							                    
							                    
						                        <div id="price-sub-listing" class="col-md-6">
						                            <label><?php echo esc_html__('Property Price','noo'); ?></label><br>
						                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][price_sub_listing]" value="<?php echo esc_attr( $price_sub_listing ) ?>" class="form-control"/>
						                        </div>
						                   
						                  
						                        <div id="type-sub-listing" class="col-md-6">
						                            <label><?php echo esc_html__('Property Type','noo'); ?></label><br>
						                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][type_sub_listing]" value="<?php echo esc_attr( $type_sub_listing ) ?>" class="form-control"/>
						                       </div>
						                        <div id="available-sub-listing" class="col-md-6">
						                            <label><?php echo esc_html__('Available From','noo'); ?></label><br>
						                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][available_sub_listing]" value="<?php echo esc_attr( $available_sub_listing ) ?>" class="form-control"/>
						                        </div>
							               </div>
							            </div>
							               
							                <?php
							            endforeach;
							            ?>
							            <div class="rp-clone-sub-property">
							                <div class="content-clone1"></div>
							                <button class="button-primary add-sub-property" data-total="<?php echo count( $sub_listing ) ?>">
							                    <?php echo esc_html__( 'Add More', 'noo' ) ?>
							                </button>
							            </div>
							            <?php
							        echo "</div>";
							        endif;
									 ?>
								</div>
							</div>
						</div>
						<?php endif; ?>

						<div class="row">
							<div class="col-md-5">
								<?php if( $featured || $featured_permision['allow'] || !empty( $featured_permision['message'] ) ) : ?>
								<div class="noo-control-group small-group">
									<div class="group-title">
										<?php _e('Featured Submission', 'noo'); ?>
									</div>
									<div class="group-container row">
										<div class="col-md-12">
											<div class="form-group s-prop-featured">
												<?php if( $featured ) : ?>
												<span class="label label-success"><?php _e('Featured Property', 'noo'); ?></span>
												<?php elseif( $featured_permision['allow'] ) : ?>
												<input type="hidden" value="0" name="_featured" />
												<label for="featured" class="checkbox-label">
													<input type="checkbox" id="featured" class="" value="1" name="_featured" />&nbsp;<?php echo $featured_permision['message']; ?>
													<i></i>
												</label>
												<?php elseif( !empty( $featured_permision['message'] ) ) : ?>
												<p><?php echo $featured_permision['message']; ?></p>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
								<?php endif; ?>
								<div class="noo-control-group small-group">
									<div class="group-title">
										<?php _e('Property Video', 'noo'); ?>
									</div>
									<div class="group-container row">
										<div class="col-md-12">
											<div class="form-group s-prop-video">
												<label for="_video_embedded"><?php _e('Video Embedded','noo'); ?></label>
												<input type="text" id="_video_embedded" class="form-control" value="<?php echo $video; ?>" name="_video" />
												<small><?php echo sprintf( __('Enter a Youtube, Vimeo, Soundcloud, etc... URL. See supported services at %s', 'noo'), '<a href="http://codex.wordpress.org/Embeds" target="_BLANK">http://codex.wordpress.org/Embeds</a>'); ?>
												</small>
											</div>
										</div>
									</div>
								</div>
								<div class="noo-control-group small-group">
									<div class="group-title">
										<?php _e('360° Virtual Tour ', 'noo'); ?>
									</div>
									<div class="group-container row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="_virtual_tour"><?php _e('Embedded','noo'); ?></label>
												<input type="text" id="_virtual_tour" class="form-control" value="<?php echo  $virtual; ?>" name="_virtual" />
												<small><?php echo sprintf( __('Enter virtual tour iframe/embedded.', 'noo')); ?>
												</small>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php if(!empty($features)) : ?>
							<div class="col-md-7">
								<div class="noo-control-group small-group">
									<div class="group-title">
										<?php _e('Amenities & Features', 'noo'); ?>
									</div>
									<div class="group-container row">
										<?php foreach ($features as $index => $feature) : ?>
										<div class="col-md-6">
											<div class="form-group s-prop-<?php echo $feature['id']; ?> checkbox">
												<input type="hidden" name="_<?php echo $feature['name']; ?>" class="" value="0" />
												<label for="<?php echo $feature['id']; ?>" class="checkbox-label">
													<input type="checkbox" id="<?php echo $feature['id']; ?>" name="_<?php echo $feature['name']; ?>" class="" value="1" <?php checked($feature['value']); ?> />&nbsp;<?php echo $feature['label']; ?>
													<i></i>
												</label>
											</div>
										</div>
										<?php endforeach; ?>
									</div>
									<div class="group-title">
										<?php _e('Additional Information', 'noo'); ?>
									</div>
									<div class="group-container row">
									    <table class="additional-block">
					                        <thead>
					                        <tr>
					                            <td></td>
					                            <td><label><strong><?php echo esc_html__('Label', 'noo'); ?></strong></label></td>
					                            <td><label><strong><?php echo esc_html__('Value', 'noo'); ?></strong></label></td>
					                            <td></td>
					                        </tr>
					                        </thead>
					                        <tbody id="noo_additional_details">
					                        <?php if (is_array($current_data)): ?>
					                            <?php foreach ($current_data as $key => $current_data): 
					                            	$additional_feature_label = ( array_key_exists( 'additional_feature_label', $current_data ) && $key > 0  ) ? $current_data[ 'additional_feature_label' ] : '';
													$additional_feature_value = ( array_key_exists( 'additional_feature_value', $current_data ) && $key > 0 ) ? $current_data[ 'additional_feature_value' ] : '';
					                            	?>
					                                <tr <?php echo ( $key === 0 ) ? 'style="display: none;" ':''?>>
					                                    <td class="action-field">
					                                        <span class="sort-additional-row"><i class="fa fa-navicon"></i></span>
					                                    </td>
					                                    <td class="field-title">
					                                        <input class="noo-ft-field" type="text"
					                                               name="additional_features[<?php echo esc_attr($key); ?>][additional_feature_label]"
					                                                value="<?php echo esc_attr( $additional_feature_label ) ?>">
					                                    </td>
					                                    <td>
					                                    	<input type="text" name="additional_features[<?php echo esc_attr( $key ) ?>][additional_feature_value]" value="<?php echo esc_attr( $additional_feature_value ) ?>"/>
					                                    </td>
					                                    <td class="action-field">
					                                        <span data-remove="<?php echo $key; ?>" class="remove-additional-row"><i class="fa fa-remove"></i></span>
					                                    </td>
					                                </tr>
					                            	
					                            <?php endforeach; ?>
					                        <?php endif; ?>

					                        </tbody>
					                        <tfoot>
					                        <tr>
					                            <td></td>
					                            <td>
					                                <button data-increment="0" class="add-additional-row button-primary"data-key="<?php echo esc_attr( $key ) ?>" data-id="<?php echo $id; ?>">
					                                    <i class="fa fa-plus"></i> <?php echo esc_html__('Add new field', 'noo'); ?>
					                                </button>
					                            </td>
					                            <td></td>
					                        </tr>
					                        </tfoot>
					                    </table>
									</div>

								</div>
							</div>
							<?php endif; ?>
						</div>
						<div class="noo-submit row">
							<div class="col-md-12">
								<input type="submit" class="btn btn-primary btn-lg" id="property_submit" value="<?php echo $submit_text; ?>" />
								<?php if( $need_approve && $action == 'add') : ?>
								<label><?php _e('Your submission will be reviewed by Administrator before it can be published', 'noo'); ?></label>
								<?php elseif( $need_approve && $action == 'edit') : ?>
								<label><?php _e('Your property will be unpublished for Administrator to review your changes', 'noo'); ?></label>
								<?php endif; ?>
							</div>
						</div>  
						<input type="hidden" name="_action" value="<?php echo $action;?>">
						<input type="hidden" name="_agent_id" value="<?php echo $agent_id;?>">
						<input type="hidden" name="_prop_id" value="<?php echo $prop_id;?>">
						<?php wp_nonce_field('submit_property','_noo_property_nonce'); ?>
					</form>
				</div>
				<?php endif; ?>
			</div> <!-- /.main -->
		</div><!--/.row-->
	</div><!--/.container-boxed-->
</div><!--/.container-wrap-->

<?php get_footer(); ?>