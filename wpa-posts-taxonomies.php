<?php 

add_action( 'init', 'wpa_posts_create_vertical_tax' );
function wpa_posts_create_vertical_tax() {
  global $wp_rewrite;

  if(taxonomy_exists('vertical'))
    return;
  register_taxonomy(
    'vertical',
    'post',
    array(
      'label' => __( 'Verticals' ),
      'rewrite' => array( 'slug' => 'v' ),
      'hierarchical' => true,
    )
  );

  $wp_rewrite->flush_rules();
}

add_action( 'init', 'wpa_posts_create_section_tax' );
function wpa_posts_create_section_tax() {
  global $wp_rewrite;

  if(taxonomy_exists('section'))
    return;
  register_taxonomy(
    'section',
    'post',
    array(
      'label' => __( 'section' ),
      'rewrite' => false,
      'hierarchical' => true,
      'publicly_queryable' => false,
    )
  );

  $wp_rewrite->flush_rules();
}