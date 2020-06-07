<?php
/**
 * Register NOO Agent.
 * This file register Item and Category for NOO Agent.
 *
 * @package    NOO CitiLights
 * @subpackage NOO Agent
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */

if(!class_exists('NooMembership')) :
	class NooMembership {

		const MEMBERSHIP_POST_TYPE = 'noo_membership';
		const MEMBERSHIP_META_PREFIX = '_noo_membership';

		public function __construct() {

			if( self::is_membership() ) {
				add_action('init', array(&$this,'register_post_type'));

				if( is_admin() ) {
					
					// Membership
					add_filter( 'enter_title_here', array (&$this,'custom_enter_title') );
					add_action ( 'add_meta_boxes', array (&$this,'remove_meta_boxes' ), 20 );
					add_action ( 'add_meta_boxes', array (&$this, 'add_meta_boxes' ), 30 );

					// Ajax to create new membership on Agent edit page.
					add_action( 'wp_ajax_noo_create_membership', 'NooMembership::create_membership' );
				}
			}

			//hook into the query before it is executed to add the filter for not paid properties.
			add_action( 'pre_get_posts', array (&$this, 'pre_get_posts'), 1 );

			if( self::is_submission() ) {

				// Add Paid status to Property edit
				add_action ( 'add_meta_boxes', array (&$this, 'add_meta_boxes_property' ), 30 );
			}
		}

		public function register_post_type () {
			// Text for NOO Membership Packages.
			$noo_membership_package_labels = array(
				'name' => __('Membership Packages', 'noo'),
				'singular_name' => __('Membership Package', 'noo'),
				'menu_name' => __('Membership Packages', 'noo'),
				'all_items' => __('Membership Packages', 'noo'),
				'add_new' => __('Add New', 'noo'),
				'add_new_item' => __('Add New Membership Package', 'noo'),
				'edit_item' => __('Edit Membership Package', 'noo'),
				'new_item' => __('New Membership Package', 'noo'),
				'view_item' => __('View Membership Package', 'noo'),
				'search_items' => __('Search Membership Package', 'noo'),
				'not_found' => __('No membership packages found', 'noo'),
				'not_found_in_trash' => __('No membership packages found in trash', 'noo'),
			);


			// Options
			$noo_membership_package_args = array(
				'labels' => $noo_membership_package_labels,
				'public' => true,
				'publicly_queryable' => false,
				'show_in_menu' => 'edit.php?post_type=noo_agent',
				'hierarchical' => false,
				'supports' => array(
					'title',
					'revisions'
				),
				'has_archive' => false,
			);
			
			register_post_type(self::MEMBERSHIP_POST_TYPE, $noo_membership_package_args);
		}

		public function custom_enter_title( $input ) {
			global $post_type;

			if ( self::MEMBERSHIP_POST_TYPE == $post_type )
				return __( 'Package Title', 'noo' );

			return $input;
		}

		public function remove_meta_boxes() {
			remove_meta_box( 'slugdiv', self::MEMBERSHIP_POST_TYPE, 'normal' );
			remove_meta_box( 'mymetabox_revslider_0', self::MEMBERSHIP_POST_TYPE, 'normal' );
		}

		public function add_meta_boxes() {

			// Declare helper object
			$prefix = self::MEMBERSHIP_META_PREFIX;
			$helper = new NOO_Meta_Boxes_Helper( $prefix, array( 'page' => self::MEMBERSHIP_POST_TYPE ) );

			// Membership metabox
			$meta_box = array(
				'id'           => "{$prefix}_meta_box_membership",
				'title'        => __( 'Package Details', 'noo' ),
				'context'      => 'normal',
				'priority'     => 'core',
				'description'  => '',
				'fields'       => array(
					array(
						'id' => "{$prefix}_interval",
						'label' => __( 'Package Interval', 'noo' ),
						'desc' => __( 'Duration time of this package.', 'noo' ),
						'type' => 'billing_period',
						'std' => '0',
						'callback' => 'NooMembership::render_metabox_fields'
					),
					array(
						'id' => "{$prefix}_price",
						'label' => __( 'Package Price', 'noo' ),
						'desc' => __( 'The price of this package.', 'noo' ),
						'type' => 'text',
						'std' => '20.00'
					),
					array(
						'id' => "{$prefix}_listing_num",
						'label' => __( 'Number of Listing', 'noo' ),
						'desc' => __( 'Number of listing available for this package.', 'noo' ),
						'type' => 'listing_num',
						'std' => '3',
						'callback' => 'NooMembership::render_metabox_fields'
					),
					array(
						'id' => "{$prefix}_featured_num",
						'label' => __( 'Number of Featured Properties', 'noo' ),
						'desc' => __( 'Number of properties can make featured with this package.', 'noo' ),
						'type' => 'text',
						'std' => '2'
					)
				),
			);

			$helper->add_meta_box($meta_box);

			// Membership metabox
			$meta_box = array(
				'id'           => "{$prefix}_additional_info",
				'title'        => __( 'Aditional Information', 'noo' ),
				'context'      => 'normal',
				'priority'     => 'default',
				'description'  => '',
				'fields'       => array(
					array(
						'id' => "{$prefix}_additional_info",
						'label' => __( 'Additional Info', 'noo' ),
						'desc' => __( 'Add more detail for this package.', 'noo' ),
						'type' => 'addable_text',
						'std' => '',
						'callback' => 'NooMembership::render_metabox_fields'
					)
				),
			);

			$helper->add_meta_box($meta_box);
		}

		public function add_meta_boxes_property() {

			// Declare helper object
			$prefix = '';
			$helper = new NOO_Meta_Boxes_Helper( $prefix, array( 'page' => 'noo_property' ) );

			// Membership metabox
			$meta_box = array(
				'id'           => "{$prefix}_meta_box_paid_status",
				'title'        => __( 'Payment Status', 'noo' ),
				'context'      => 'side',
				'priority'     => 'default',
				'description'  => '',
				'fields'       => array(
					array(
						'id' => "{$prefix}_paid_listing",
						'label'    => __( 'Paid Listing', 'noo' ),
						'desc'    => __( 'Set the submission payment status for this Property. Please remember that only Paid submision is available on the site.', 'noo' ),
						'type'    => 'select',
						'std'     => '0',
						'options' => array(
							array('value'=>'0','label'=>__('Not Paid', 'noo')),
							array('value'=>'1','label'=>__('Paid', 'noo'))
						)
					),
				),
			);

			$helper->add_meta_box($meta_box);
		}

		public static function render_metabox_fields( $post, $id, $type, $meta, $std = null, $field = null ) {
			switch( $type ) {
				case 'billing_period':
					$value = $meta ? ' value="' . $meta . '"' : '';
					$value = empty( $value ) && ( $std != null && $std != '' ) ? ' placeholder="' . $std . '"' : $value;
					$unit  = esc_attr(get_post_meta($post->ID, $id . '_unit', true));
					$unit  = empty( $unit ) ? 'day' : $unit;
					echo '<div class="input-group">';
					echo '<input type="text" name="noo_meta_boxes[' . $id . ']" ' . $value . ' style="width:200px;display: inline-block;float: left;margin: 0;height:28px;"/>';
					echo '<select name="noo_meta_boxes[' . $id . '_unit]" style="width:100px;display: inline-block;float: left;margin: 0;box-shadow: none;background-color:#ddd;">';
					echo '<option value="day" ' . selected( $unit, 'day', false ) . '>' . __( 'Days', 'noo') . '</option>';
					echo '<option value="week" ' . selected( $unit, 'week', false ) . '>' . __( 'Weeks', 'noo') . '</option>';
					echo '<option value="month" ' . selected( $unit, 'month', false ) . '>' . __( 'Months', 'noo') . '</option>';
					echo '<option value="year" ' . selected( $unit, 'year', false ) . '>' . __( 'Years', 'noo') . '</option>';
					echo '</select>';
					echo '</div>';
					break;

				case 'listing_num':
					$unlimited = (bool) get_post_meta( $post->ID, $id . '_unlimited', true );
					$value = $meta ? ' value="' . $meta . '"' : '';
					$value = empty( $value ) && ( $std != null && $std != '' ) ? ' placeholder="' . $std . '"' : $value;
					echo '<input type="text" name="noo_meta_boxes[' . $id . ']" ' . $value . disabled( $unlimited, true, false ) . '/>';
					echo '<label><input type="checkbox" name="noo_meta_boxes[' . $id . '_unlimited]" ' . checked( $unlimited, true, false ) . 'value="1" />';
					echo __( 'Unlimited Listing?', 'noo' ) . '</label>';

					echo '<script>
						jQuery( document ).ready( function ( $ ) {
							$("input[name=\'noo_meta_boxes[' . $id . '_unlimited]\']").click( function() {
								if( $(this).is(":checked") ) {
									$("input[name=\'noo_meta_boxes[' . $id . ']\']").prop("disabled", true);
								} else {
									$("input[name=\'noo_meta_boxes[' . $id . ']\']").prop("disabled", false);
								}
							});

						} );
					</script>';

					break;

				case 'addable_text':
					$max_fields = 5;
					if ( !empty( $field['max_fields'] ) && is_numeric( $field['max_fields'] ) ) {
						$max_fields = $field['max_fields'];
					}
					if( $max_fields == -1 ) $max_fields = 100;
					$meta = array();
					?>
					<div class="noo-membership-additional" data-max="<?php echo $max_fields; ?>" data-name="<?php echo $id; ?>" >
					<?php
					$count = 0;
					for( $index = 0; $index <= $max_fields; $index++ ) {
						$meta_i = get_post_meta( get_the_ID(), $id . '_' . $index, true );
						if( !empty( $meta_i ) ) {
							$count++;
							$meta[] = get_post_meta( get_the_ID(), $id . '_' . $index, true );
						}
					}
					
					foreach( $meta as $index => $meta_i ) :
					?>
						<div class="additional-field">
							<input type="text" value="<?php echo $meta_i; ?>" name="noo_meta_boxes[<?php echo $id . '_' . ( $index + 1 ); ?>]" style="max-width:350px;padding-right: 10px;display: inline-block;float: left;" />
							<input class="button button-secondary delete_membership_add_info" type="button" value="<?php _e('Delete', 'noo'); ?>" style="display: inline-block;float: left;" />
							<br/>
						</div>
					<?php
					endforeach;
					?>
					</div>
					<br class="clear" />
					<input type="button" value="<?php _e('Add', 'noo'); ?>" class="button button-primary add_membership_add_info" <?php disabled( $count >= $max_fields ); ?>/>
					<?php
					break;
			}
		}

		public static function create_membership() {  
			try{

				$prefix = self::MEMBERSHIP_META_PREFIX;
				$new_package = array(
					'post_title' => $_POST['title'],
					'post_type' => self::MEMBERSHIP_POST_TYPE,
					'post_status' => 'publish'
				);
				$new_post_ID = wp_insert_post( $new_package );
				if( $new_post_ID ) {
					update_post_meta( $new_post_ID, "{$prefix}_interval", $_POST['interval'] );
					update_post_meta( $new_post_ID, "{$prefix}_interval_unit", $_POST['interval_unit'] );
					update_post_meta( $new_post_ID, "{$prefix}_price", $_POST['price'] );
					update_post_meta( $new_post_ID, "{$prefix}_listing_num", $_POST['listing_num'] );
					update_post_meta( $new_post_ID, "{$prefix}_listing_num_unlimited", $_POST['listing_num_unlimited'] );
					update_post_meta( $new_post_ID, "{$prefix}_featured_num", $_POST['featured_num'] );

					echo $new_post_ID;
					exit();
				}
			} catch (Exception $e){  
				exit('-1');  
			}  
			exit('-1'); 
		}

		public static function get_membership_type() {
			return re_get_agent_setting('noo_membership_type', 'membership');
		}

		public static function is_membership() {
			return self::get_membership_type() == 'membership';
		}

		public static function is_submission() {
			return self::get_membership_type() == 'submission';
		}

		public function pre_get_posts( $query ) {
			if ( is_admin() ) {
				return;
			}

			if ( NooAgent::is_dashboard() ) {
				return;
			}

			//if is querying noo_property
			if( re_is_property_query() ) {

				if ( !self::is_submission() ) {
					return;
				}

				$meta_query = isset( $query->meta_query ) && !empty( $query->meta_query ) ? $query->meta_query : array();
				$paid_filter = array(
						'key' => '_paid_listing',
						'value' => '1'
					);
				$meta_query[] = $paid_filter;

				$query->set('meta_query', $meta_query);
			}

			//if is querying noo_agent
			if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'noo_agent' ) {
				if( $query->is_singular && $query->is_main_query() ) {
					return;
				}

				$agent_must_has_property = (bool) re_get_agent_setting('noo_agent_must_has_property');
				$agent_must_has_property = apply_filters( 'noo_agent_must_has_property', $agent_must_has_property );

				if( $agent_must_has_property ) {
					global $noo_show_sold;
					$noo_show_sold = true;
					$meta_query = new WP_Query(
						array(
							'post_type'			=> 'noo_property',
							'fields'			=> 'ids',
							'posts_per_page'	=> -1,
							'meta_query'	=> array(
								'key'		=> '_agent_responsible',
								'value'		=> array( null, '', '0', 0 ),
								'compare'	=> 'NOT IN'
							)
						)
					);

					$property_list = $meta_query->posts;
					$agent_ids = array();
					if( !empty($property_list) ) {
						foreach ($property_list as $prop_id) {
							$agent_id = get_post_meta($prop_id, '_agent_responsible', true);
							if( !in_array($agent_id, $agent_ids) && !empty( $agent_id) && is_numeric( $agent_id ) ) {
								$agent_ids[] = $agent_id;
							}
						}
					}

					$noo_show_sold = false;

					$query->set('post__in', $agent_ids );
				}
			}

			return;
		}
	}
endif;

new NooMembership();

function check_expired_properties( $agent_id = '' ) {
	if( empty( $agent_id ) ) {
		return;
	}

	$is_expired = NooAgent::is_expired( $agent_id );

	if( $is_expired ) {
		$args = array(
			'post_type' => 'noo_property',
			'meta_query' => array(
				array(
					'key' => '_agent_responsible',
					'value' => (string) $agent_id
					)
				)
			);
		$query = new WP_Query( $args );
		if ($query->have_posts()):
			while($query->have_posts()): $query->the_post();
				$p_args = array('ID' => get_the_ID(), 'post_status' => 'pending');
				wp_update_post( $p_args );
			endwhile;
		endif;
	}
}

add_action( 'noo_scheduled_expired_agent', 'check_expired_properties' );

function set_expired_schedule( $agent_id = null ) {
	if( empty( $agent_id ) ) {
		return; 
	}

	$expired_date = NooAgent::get_expired_date( $agent_id );

	if( !empty($expired_date) ) {

		wp_schedule_event( $expired_date, 'none', 'noo_scheduled_expired_agent', array( $agent_id ) );
	}
}
add_action( 'noo_set_agent_membership', 'set_expired_schedule', 10, 1 );

