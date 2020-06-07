<?php

require_once NOO_FRAMEWORK . '/agent/functions.php';
require_once NOO_FRAMEWORK . '/agent/init.php';
if ( is_admin() ) {
	require_once NOO_FRAMEWORK . '/agent/admin.php';
	require_once NOO_FRAMEWORK . '/agent/admin-settings.php';
	require_once NOO_FRAMEWORK . '/agent/admin-list.php';
	require_once NOO_FRAMEWORK . '/agent/admin-edit.php';
	
}
require_once NOO_FRAMEWORK . '/agent/agent-user.php';
require_once NOO_FRAMEWORK . '/agent/agent-default-fields.php';
require_once NOO_FRAMEWORK . '/agent/agent-custom-fields.php';
require_once NOO_FRAMEWORK . '/agent/agent-social-fields.php';
// require_once NOO_FRAMEWORK . '/agent/agent-query.php';
require_once NOO_FRAMEWORK . '/agent/agent-submit.php';
// require_once NOO_FRAMEWORK . '/agent/agent-enqueue.php';
// require_once NOO_FRAMEWORK . '/agent/agent-template.php';
// require_once NOO_FRAMEWORK . '/agent/agent-template-shortcodes.php';

require_once NOO_FRAMEWORK . '/agent/agent-membership.php';
require_once NOO_FRAMEWORK . '/agent/agent-membership-action.php';
require_once NOO_FRAMEWORK . '/agent/agent-membership-submission.php';
require_once NOO_FRAMEWORK . '/agent/agent-permission.php';

require_once NOO_FRAMEWORK . '/agent/agent-ajax.php';
require_once NOO_FRAMEWORK_ADMIN . '/noo-agent_obsoleted.php';
