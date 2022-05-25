<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Authentication
$route['login_attempt'] = 'AuthenticationController/login_attempt';

// Extra
$route['upload_document'] = 'ExtraController/upload_document';
$route['post_your_requirement'] = 'ExtraController/post_your_requirement';
$route['import_lat_long'] = 'ExtraController/import_lat_long';
$route['testing'] = 'ExtraController/testing';

// Cron
$route['json_filter_search_data'] = 'CronController/testing';


// State
$route['states/get_states'] = 'StateMasterController/get_states';

// Cities
$route['cities/get_cities_by_state_id'] = 'CityMasterController/get_cities_by_state_id';
$route['cities/get_cities_by_like_city_name'] = 'CityMasterController/get_cities_by_like_city_name';


// Cosmatic 
$route['vendor_type/get_vendor_type'] = 'VendorTypeMasterController/get_vendor_type';

// Category Master
$route['category/get_list_vendor_type_with_categories'] = 'CategoryMasterController/get_list_vendor_type_with_categories';
$route['category/get_list_vendor_type_with_category_with_sub_category'] = 'CategoryMasterController/get_list_vendor_type_with_category_with_sub_category';
$route['category/get_list_categories_and_sub_category_by_vendor_type_id'] = 'CategoryMasterController/get_list_categories_and_sub_category_by_vendor_type_id';
$route['category/get_sub_category_by_category'] = 'CategoryMasterController/get_sub_category_by_category';
$route['category/get_category_by_vendor_type_id'] = 'CategoryMasterController/get_category_by_vendor_type_id';
$route['category/get_category_by_vendor_type_slug'] = 'CategoryMasterController/get_category_by_vendor_type_slug';
$route['category/get_search_keywords_filter'] = 'CategoryMasterController/get_search_keywords_filter';

// Vendor Master
$route['vendor/get_vendor_by_tag_slug'] = 'VendorMasterController/get_vendor_by_tag_slug';
$route['vendor/get_vendor_detail_by_user_id'] = 'VendorMasterController/get_vendor_detail_by_user_id';
$route['vendor/become-a-vendor'] = 'VendorMasterController/become_a_vendor';
$route['vendor/get_sub_category_by_user_id'] = 'VendorMasterController/get_sub_category_by_user_id';
$route['vendor/get_cities_by_user_id'] = 'VendorMasterController/get_cities_by_user_id';
$route['vendor/upload_profile_picture'] = 'VendorMasterController/upload_profile_picture';

// Vendor Profile
$route['vendor_profile/predefind-meta-data/stora_predefined_meta_data'] = 'VendorProfile/PredefinedMetaDataController/stora_predefined_meta_data';

// Advanced Search
$route['search/advanced_search'] = 'AdvancedSearchController/Search';

// Account
$route['account/account_get_personal_information_section'] = 'VendorMasterController/account_get_personal_information_section';
$route['account/account_set_personal_information_section'] = 'VendorMasterController/account_set_personal_information_section';

// Menu
$route['mega_menu/get_mega_menu_sub_category_and_category'] = 'MegaMenuController/get_mega_menu_sub_category_and_category';



