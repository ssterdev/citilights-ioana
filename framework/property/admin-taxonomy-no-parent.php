<?php
if( !function_exists( 're_property_taxonomy_no_parent_script' ) ) :
	function re_property_taxonomy_no_parent_script( $term = null, $taxonomy = '' ) {
		?>
		<script type="text/javascript">
		<!--
		jQuery(document).ready(function($){
			$('#parent').closest('.form-field').hide();
		});
		//-->
		</script>
		<?php
	}
	add_action( 'property_location_add_form_fields', 're_property_taxonomy_no_parent_script' );
	add_action( 'property_location_edit_form_fields', 're_property_taxonomy_no_parent_script', 10, 2 );
	add_action( 'property_status_add_form_fields', 're_property_taxonomy_no_parent_script' );
	add_action( 'property_status_edit_form_fields', 're_property_taxonomy_no_parent_script', 10, 2 );
endif;
