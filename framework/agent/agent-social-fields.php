<?php
if( !function_exists( 're_agent_render_social_field') ) :
	function re_agent_render_social_field( $social = '', $agent_id = 0 ) {
		$all_socials = noo_get_social_fields();
		if( empty( $social ) || !isset( $all_socials[$social] ) ) return;

		$field = $all_socials[$social];
		$field['name'] = RE_AGENT_META_PREFIX . '_' . $social;
		$field['type'] = 'text';
		$field_id = $field['name'];

		$value = !empty( $agent_id ) ? get_post_meta( $agent_id, $field_id, true ) : '';
		$value = !is_array($value) ? trim($value) : $value;

		$params = apply_filters( 're_agent_render_social_field_params', compact( 'field', 'field_id', 'value' ), $agent_id );
		extract($params);

		$field_id = esc_attr($field_id);
		?>
		<div class="form-group s-profile-<?php echo $field_id; ?>">
			<label for="<?php echo $field_id; ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<?php noo_render_field( $field, $field_id, $value ); ?>
		</div>
		<?php
	}
endif;
