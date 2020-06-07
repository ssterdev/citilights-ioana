<?php
if( !function_exists( 're_ajax_exit' ) ) :
	function re_ajax_exit( $message = '', $success = false, $redirect = '' ) {
		$response = array(
			'success' => $success,
			'message' => $message,
		);

		if( !empty( $redirect ) ) {
			$response['redirect'] = $redirect;
		}

		echo json_encode($response);
		exit();
	}
endif;