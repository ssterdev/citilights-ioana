<?php
if( !function_exists( 're_property_category_add_marker_icon' ) ) :
	function re_property_category_add_marker_icon(){
		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		}else{
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}
		?>
		<div class="form-field">
			<label><?php _e( 'Map Marker Icon', 'noo' ); ?></label>
			<div id="category_map_marker_icon" style="float:left;margin-right:10px;">
				<img src="<?php echo NOO_FRAMEWORK_ADMIN_URI . '/assets/images/placeholder.png'; ?>" width="60px" height="60px" />
			</div>
			<div style="line-height:60px;">
				<input type="hidden" id="category_map_marker_icon_id" name="category_map_marker_icon_id" />
				<button type="button" class="upload_image_button button"><?php _e('Upload/Add image', 'noo'); ?></button>
				<button type="button" class="remove_image_button button"><?php _e('Remove image', 'noo'); ?></button>
			</div>
			<script type="text/javascript">
				
				 // Only show the "remove image" button when needed
				 if ( ! jQuery('#category_map_marker_icon_id').val() )
					 jQuery('.remove_image_button').hide();
		
				// Uploading files
				var file_frame;
		
				jQuery(document).on( 'click', '.upload_image_button', function( event ){
		
					event.preventDefault();
		
					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}
		
					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: "<?php _e( 'Choose an image', 'noo' ); ?>",
						button: {
							text: "<?php _e( 'Use image', 'noo' ); ?>",
						},
						multiple: false
					});
		
					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();
		
						jQuery('#category_map_marker_icon_id').val( attachment.id );
						jQuery('#category_map_marker_icon img').attr('src', attachment.url );
						jQuery('.remove_image_button').show();
					});
		
					// Finally, open the modal.
					file_frame.open();
				});
		
				jQuery(document).on( 'click', '.remove_image_button', function( event ){
					jQuery('#category_map_marker_icon img').attr('src', '<?php echo NOO_FRAMEWORK_ADMIN_URI . '/assets/images/placeholder.png'; ?>');
					jQuery('#category_map_marker_icon_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});
		
			</script>
			<div class="clear"></div>
		</div>
		<?php
	}
	add_action( 'property_category_add_form_fields', 're_property_category_add_marker_icon' );
endif;

if( !function_exists( 're_property_category_edit_marker_icon' ) ) :
	function re_property_category_edit_marker_icon( $term, $taxonomy ) {
		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		}else{
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}
		$map_markers = get_option( 'noo_category_map_markers' );
		$image 			= '';
		$category_map_marker_icon_id 	= isset($map_markers[$term->term_id]) ? $map_markers[$term->term_id] : '';
		if ($category_map_marker_icon_id) :
			$image = wp_get_attachment_url( $category_map_marker_icon_id );
		else :
			$image = NOO_FRAMEWORK_ADMIN_URI . '/assets/images/placeholder.png';
		endif;
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e('Map Marker Icon', 'noo'); ?></label></th>
			<td>
				<div id="category_map_marker_icon" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="category_map_marker_icon_id" name="category_map_marker_icon_id" value="<?php echo $category_map_marker_icon_id; ?>" />
					<button type="button" class="upload_image_button button"><?php _e('Upload/Add image', 'noo'); ?></button>
					<button type="button" class="remove_image_button button"><?php _e('Remove image', 'noo'); ?></button>
				</div>
				<script type="text/javascript">
	
					jQuery(function(){
	
						 // Only show the "remove image" button when needed
						 if ( ! jQuery('#category_map_marker_icon_id').val() )
							 jQuery('.remove_image_button').hide();
	
						// Uploading files
						var file_frame;
	
						jQuery(document).on( 'click', '.upload_image_button', function( event ){
	
							event.preventDefault();
	
							// If the media frame already exists, reopen it.
							if ( file_frame ) {
								file_frame.open();
								return;
							}
	
							// Create the media frame.
							file_frame = wp.media.frames.downloadable_file = wp.media({
								title: "<?php _e( 'Choose an image', 'noo' ); ?>",
								button: {
									text: "<?php _e( 'Use image', 'noo' ); ?>",
								},
								multiple: false
							});
	
							// When an image is selected, run a callback.
							file_frame.on( 'select', function() {
								attachment = file_frame.state().get('selection').first().toJSON();
	
								jQuery('#category_map_marker_icon_id').val( attachment.id );
								jQuery('#category_map_marker_icon img').attr('src', attachment.url );
								jQuery('.remove_image_button').show();
							});
	
							// Finally, open the modal.
							file_frame.open();
						});
	
						jQuery(document).on( 'click', '.remove_image_button', function( event ){
							jQuery('#category_map_marker_icon img').attr('src', '<?php echo NOO_FRAMEWORK_ADMIN_URI . '/assets/images/placeholder.png'; ?>');
							jQuery('#category_map_marker_icon_id').val('');
							jQuery('.remove_image_button').hide();
							return false;
						});
					});
	
				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}
	add_action( 'property_category_edit_form_fields', 're_property_category_edit_marker_icon', 10, 2 );
endif;

if( !function_exists( 're_property_category_save_marker_icon' ) ) :
	function re_property_category_save_marker_icon( $term_id, $tt_id, $taxonomy ){
		if ( isset( $_POST['category_map_marker_icon_id'] ) ){
			$map_markers = get_option( 'noo_category_map_markers' );
			if ( ! $map_markers )
				$map_markers = array();
			$map_markers[$term_id] = absint($_POST['category_map_marker_icon_id']);
			update_option('noo_category_map_markers', $map_markers);
		}	
	}
	add_action( 'created_term', 're_property_category_save_marker_icon', 10,3 );
	add_action( 'edit_term', 're_property_category_save_marker_icon', 10,3 );
endif;