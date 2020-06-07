<?php

require_once NOO_FRAMEWORK . '/property/functions.php';
require_once NOO_FRAMEWORK . '/property/init.php';
if ( is_admin() ) {
	require_once NOO_FRAMEWORK . '/property/admin.php';
	require_once NOO_FRAMEWORK . '/property/admin-settings.php';
	require_once NOO_FRAMEWORK . '/property/admin-list.php';
	require_once NOO_FRAMEWORK . '/property/admin-edit.php';
	require_once NOO_FRAMEWORK . '/property/admin-property-category.php';
	require_once NOO_FRAMEWORK . '/property/admin-property-sub-location.php';
	require_once NOO_FRAMEWORK . '/property/admin-property-label.php';
	require_once NOO_FRAMEWORK . '/property/admin-taxonomy-no-parent.php';
}
require_once NOO_FRAMEWORK . '/property/property-default-fields.php';
require_once NOO_FRAMEWORK . '/property/property-tax-fields.php';
require_once NOO_FRAMEWORK . '/property/property-custom-fields.php';
require_once NOO_FRAMEWORK . '/property/property-feature-fields.php';
require_once NOO_FRAMEWORK . '/property/property-price.php';
require_once NOO_FRAMEWORK . '/property/property-area.php';
require_once NOO_FRAMEWORK . '/property/property-summary.php';
require_once NOO_FRAMEWORK . '/property/property-query.php';
require_once NOO_FRAMEWORK . '/property/property-map.php';
require_once NOO_FRAMEWORK . '/property/property-enqueue.php';
require_once NOO_FRAMEWORK . '/property/property-template.php';
require_once NOO_FRAMEWORK . '/property/property-template-shortcodes.php';

require_once NOO_FRAMEWORK . '/property/property-IDX.php';

require_once NOO_FRAMEWORK . '/property/property-ajax-contact.php';
require_once NOO_FRAMEWORK . '/property/property-cf7-contact.php';

require_once NOO_FRAMEWORK . '/property/property-ajax-favorites.php';
require_once NOO_FRAMEWORK . '/property/property-ajax-filter-map.php';
require_once NOO_FRAMEWORK . '/property/property-nearby.php';
require_once NOO_FRAMEWORK . '/property/property-save-json.php';
require_once NOO_FRAMEWORK . '/property/property-sub-listing.php';
require_once NOO_FRAMEWORK . '/property/property-additional-feature.php';

/**
 * Support Optima Express
 */
require_once NOO_FRAMEWORK . '/property/idx-optima-express.php';

// require_once NOO_FRAMEWORK . '/property/extra.php';

require_once NOO_FRAMEWORK_ADMIN . '/noo-property_obsoleted.php';


