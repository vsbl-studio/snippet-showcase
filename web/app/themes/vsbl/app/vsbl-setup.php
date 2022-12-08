<?php

// Adds class to current post type parent menu link
add_filter(
  'nav_menu_css_class',
  function ($classes, $item) {
    $qObject = get_queried_object();

    if (!$qObject) {
      return $classes;
    }

    // Skip if page - all page menu items would get active class
    if (get_queried_object()->post_type === 'page') {
      return $classes;
    }

    if (isset($qObject->taxonomy)) {
      $postType = get_taxonomy($qObject->taxonomy)->object_type[0] ?? null;
    } elseif (get_queried_object()->post_type !== 'page') {
      $postType = get_queried_object()->post_type;
    }

    if (empty($postType)) {
      return $classes;
    }

    foreach ($item->classes as $class) {
      if ($class === 'menu-item-object-' . $postType) {
        array_push($classes, 'current-item-ancestor');
      }
    }

    return $classes;
  },
  10,
  2
);


// Uploads directory URL change
if (WP_ENV === "development" && defined('REMOTE_UPLOAD_URL')) {
  add_filter(
    'upload_dir',
    function ($data) {
      $data['url'] = str_replace(get_home_url(), REMOTE_UPLOAD_URL, $data['url']);
      $data['baseurl'] = str_replace(get_home_url(), REMOTE_UPLOAD_URL, $data['baseurl']);
      return $data;
    }
  );
}



// Auto toggle indexing
add_action(
  "init",
  function () {
    if (empty($_SERVER['HTTP_HOST'])) {
      return;
    }

    $devDomains = ['invsbl', 'vsbl-dev', 'why404', 'staging', 'dev', 'test'];


    foreach (explode(".", $_SERVER['HTTP_HOST']) as $part) {
      if (in_array($part, $devDomains)) {
        update_option('blog_public', '0');
        break;
      } else {
        update_option('blog_public', '1');
      }
    }
  }
);



// SMTP
add_action(
  'phpmailer_init',
  function ($phpmailer) {

    if (!defined('SMTP_HOST')) {
      return;
    }

    if (defined('SMTP_HOST')) {
      $phpmailer->Host =  SMTP_HOST;
    }

    if (defined('SMTP_PORT')) {
      $phpmailer->Port =  SMTP_PORT;
    }

    if (defined('SMTP_USERNAME')) {
      $phpmailer->Username =  SMTP_USERNAME;
    }

    if (defined('SMTP_PASSWORD')) {
      $phpmailer->Password =  SMTP_PASSWORD;
    }

    if (defined('SMTP_AUTH')) {
      $phpmailer->SMTPAuth =  SMTP_AUTH;
    }

    if (defined('SMTP_SECURE')) {
      $phpmailer->SMTPSecure =  SMTP_SECURE;
    }

    if (defined('SMTP_FROM_ADDRESS')) {
      $phpmailer->From =  SMTP_FROM_ADDRESS;
    }

    if (defined('SMTP_FROM_NAME')) {
      $phpmailer->FromName =  SMTP_FROM_NAME;
    }

    $phpmailer->IsSMTP();
  }
);


// Sanitize uploads filenames
add_filter(
  'sanitize_file_name',
  function ($fileName) {
    $fileParts = explode('.', $fileName);
    $extension  = array_pop($fileParts);
    $fileName   = sanitize_title(preg_replace('/[^A-Za-z0-9\-]/', '', join('.', $fileParts)));

    return sprintf('%s.%s', $fileName, $extension);
  }
);


// Disable Gutenberg where necessary
add_filter(
  'use_block_editor_for_post_type',
  function ($isEnabled) {
    $postID = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);

    if (empty($postID)) {
      return $isEnabled;
    }

    // Add necessary logic
    if ($postID === get_option("page_on_front")) {
      return false;
    }

    return $isEnabled;
  },
  10,
  2
);


// Options pages
add_action(
  "init",
  function () {
    if (function_exists('acf_add_options_sub_page')) {
      acf_add_options_sub_page('Theme Options');
      acf_add_options_sub_page('Header Options');
      acf_add_options_sub_page('Footer Options');
    }
  }
);

// Add image sizes
add_action(
  "init",
  function () {
    add_image_size('mobile', 568);
  }
);

// Dummy CPT
add_action(
  'init',
  function () {

    register_post_type(
      "case-studies",
      [
        'label'               => __("Dummy CPT", ""),
        'supports'            => ['title', 'editor'],
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_in_rest'        => true,
        'capability_type'     => 'page',
        'map_meta_cap' => true,
        'query_var' => true,
      ]
    );

    register_taxonomy(
      'dummy-category',
      'dummy-cpt',
      [
        'label' => __("Dummy Category", ""),
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => false,
      ]
    );
  }
);
