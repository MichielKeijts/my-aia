<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2015
 */

// -- TAXONOMIES --
define("MY_AIA_TAXONOMY_SPORT", "sport");
define("MY_AIA_TAXONOMY_SPORT_LEVEL", "sport-level");
define("MY_AIA_TAXONOMY_SPORTBETROKKENHEID", "sportbetrokkenheid");
define("MY_AIA_TAXONOMY_KERKSTROMING", "kerkstroming");
define("MY_AIA_TAXONOMY_SPORTWEEK_EIGENSCHAP", "sportweek-eigenschap");
define("MY_AIA_TAXONOMY_OVERNACHTING", "overnachting");
define("MY_AIA_TAXONOMY_DOELGROEP", "doelgroep");
define("MY_AIA_TAXONOMY_TAAL", "taal");
define("MY_AIA_TAXONOMY_PRODUCT_CATEGORIE", "product-categorie");

// -- CUSTOM POST TYPEPS -- 
define('MY_AIA_POST_TYPE_PARTNER','partner');
define('MY_AIA_POST_TYPE_PARTNER_SLUG','partner');
define('MY_AIA_POST_TYPE_CONTRACT', 'contract');			// a partner has a contract
define('MY_AIA_POST_TYPE_PRODUCT', 'product');				// products to sell
define('MY_AIA_POST_TYPE_COUPON', 'coupon');				// coupon to sell
define('MY_AIA_POST_TYPE_PRODUCT_SLUG','product');
define('MY_AIA_TAXONOMY_PRODUCT_CATEGORIE_SLUG', 'shop');
define('MY_AIA_POST_TYPE_ORDER', 'order');				// order has products
define('MY_AIA_POST_TYPE_INVOICE', 'invoice');				// order has invoice
define('MY_AIA_POST_TYPE_PAYMENT', 'payment');				// invoice has a payment
define('MY_AIA_POST_TYPE_TEMPLATE', 'template');				// invoice has a payment
define('MY_AIA_POST_TYPE_DOCUMENT', 'wpdmpro');			// documents (library)
define('MY_AIA_POST_TYPE_DOCUMENT_SLUG', 'download');
define('MY_AIA_POST_TYPE_BOOKING', 'booking');



// -- CAPABILITY --
define("MY_AIA_CAPABILITY_ADMIN", "my_aia_admin");
define("MY_AIA_DEFAULT_ROLE_NEW_USER", "avonturier");

// --- OTHER CONSTANTS
define( "MY_AIA_REGISTERED_HOOKS" , 'my-aia-registered-hooks');
define( "FROM_CRM_TO_WORDPRESS", 1 );
define( "FROM_WORDPRESS_TO_CRM", 2 );
define( "MY_AIA_SYNC_DATES", 'my-aia-sync-dates');
define( "MY_AIA_INVOICE_DIR", WP_CONTENT_DIR . '/uploads/filebase/invoices');
define( "MY_AIA_DOWNLOAD_SLUG", "/my-aia-download");
define( "MY_AIA_ORDER_STATUS_SENT", 'sent');
define( "MY_AIA_ORDER_STATUS_PAID", 'paid');
define( "MY_AIA_ORDER_STATUS_AWAITING_PAYMENT", 'awaiting_payment');
define( "MY_AIA_DATE_FORMAT", 'm/d');	// date format for date() function
define( "MY_AIA_TABLE_ROLES", 'my_aia_roles');

// --- BUDDYPRESS 
define( 'BUDDYPRESS_DIR', MY_AIA_PLUGIN_DIR.'../buddypress/'  );
define ("BUDDYPRESS_TABLE_NAME_DATA", 'bp_xprofile_data');
define( 'MY_AIA_BP_ROOT', 'mijn-aia');			// .nl/mijn-aia/members/..
define( 'MY_AIA_BP_MEMBERS', 'members');


// --- CSV DELIMITER
define( "EM_CSV_DELIMITER", ";");	// For Events Manager


/**
 * SQL to select the active taxonomies
 * 
 * 
 * SELECT `name`,`slug` FROM aia_terms t
INNER JOIN aia_term_taxonomy tt ON tt.term_id = t.term_id AND tt.taxonomy='sport'
INNER JOIN aia_term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
INNER JOIN aia_em_events e ON tr.object_id = e.post_id

WHERE UNIX_TIMESTAMP(e.event_start_date) > (UNIX_TIMESTAMP(NOW())-86400) 
GROUP BY tt.term_taxonomy_id



 */
