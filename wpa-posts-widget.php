<?php

add_action( 'widgets_init', 'wpa_posts_register_posts_widget' );
function wpa_posts_register_posts_widget() {
  register_widget( 'WPA_Posts_Widget' );
}

class WPA_Posts_Widget extends WP_Widget {

  function __construct() {
    parent::__construct( false, __( 'WPA Posts', 'wpa-posts' ) );
    $this->widget_defaults = array(
        'title' => '',
        'layout' => 'list',
        'section' => '',
        'count' => 4,
        'orderby' => 'date',
        'order' => 'DESC',
        'item_class' => '',
        'wrap_class' => '',
        'display_date' => 'off',
        'image_size' => 'thumbnail',
      );

    $this->layout_options = array(
      'List' => 'list',
      'List w/ Thumbs' => 'list-with-thumbs',
      'Grid' => 'grid',
    );

    $this->order_options = array(
      'Ascending' => 'ASC',
      'Descending' => 'DESC',
    );

    $this->orderby_options = array(
      'none'=>'none',
      'ID'=>'ID',
      'author'=>'author',
      'title'=>'title',
      'name'=>'name',
      'type'=>'type',
      'date'=>'date',
      'modified'=>'modified',
      'parent'=>'parent',
      'rand'=>'rand',
      'comment_count'=>'comment_count',
      'relevance'=>'relevance',
      'menu_order'=>'menu_order',
      'meta_value'=>'meta_value',
      'meta_value_num'=>'meta_value_num',
      'post__in'=>'post__in',
      'post_name__in'=>'post_name__in',
      'post_name__in'=>'post_name__in',
      'post_parent__in'=>'post_parent__in',
      'post_parent__in'=>'post_parent__in',
    );

    $this->image_sizes = array(
      'Thumbnail' => 'thumbnail',
      'Medium' => 'medium',
      'Medium Large' => 'medium_large',
      'Large' => 'large',
      'Full' => 'full',
    );

  }

  function widget( $args, $instance ) {

    extract( $args );
    $widget_options = wp_parse_args( $instance, $this->widget_defaults );
    extract( $widget_options, EXTR_SKIP );

    echo $before_widget;

    $post_query = array(
        'post_type'=>'post',
        'posts_per_page'=>$count,
        'ignore_sticky_posts' => 1,
      );

    if( 'recent' !== $section && '' !== $section ){
      $post_query["tax_query"] = array(
          array(
            'taxonomy' => 'section',
            'field' => 'slug',
            'terms' => $section
          )
        );
      $post_query["ignore_sticky_posts"] = false;
    }

    if( 'date' !== $orderby && '' !== $orderby ){
      $post_query["orderby"] = $orderby;
    }

    $posts = new WP_Query( $post_query );

    if ( $posts->have_posts() ){

      if ( '' !== $title )
        echo $before_title . $title . $after_title;

if( 'grid' == $layout ){

$wrap_class = $wrap_class == '' ? 'flex lhs' : $wrap_class;
$item_class = $item_class == '' ? 'md-c1_2 mb1' : $item_class;

///// GRID /////
?>
<ul class="<?php echo $wrap_class; ?>">
<?php
  while ( $posts->have_posts() ){
    $posts->the_post(); ?>
<li class="<?php echo $item_class; ?>">
<a class="d-block" href="<?php the_permalink(); ?>">
<?php wpa_posts_featured_image( 'thmb mbs', $image_size ); ?>
<?php the_title(); ?><?php wpa_posts_the_date( $display_date ); ?></a>
</li>
<?php
  }
?>
</ul>
<?php
///// GRID /////

} elseif( 'list-with-thumbs' == $layout ) {

///// LIST with THUMBS /////

$wrap_class = $wrap_class == '' ? 'lhs' : $wrap_class;
$item_class = $item_class == '' ? 'flex mbs' : $item_class;

?>
<ul class="<?php echo $wrap_class; ?>">
<?php
  while ( $posts->have_posts() ){
    $posts->the_post(); ?>
<li class="<?php echo $item_class; ?>">
<a href="<?php the_permalink(); ?>"><?php wpa_posts_featured_image('thmb-s', $image_size); ?></a><div class='fill pxs'><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php wpa_posts_the_date( $display_date ); ?></div>
</li>
<?php
  }
?>
</ul>
<?php
///// GRID /////

} else { /* display list by default */

$item_class = $item_class == '' ? 'flex mbs' : $item_class;

?>
<ul class="<?php echo $wrap_class; ?>">
<?php
while ( $posts->have_posts() ){
  $posts->the_post(); ?>
<li class="<?php echo $item_class; ?>"><a class="d-block" href="<?php the_permalink(); ?>"><?php the_title(); ?><?php wpa_posts_the_date( $display_date ); ?></a></li>
<?php
} /* while ( $posts->have_posts() ) */
?>
</ul>
<?php

} /* layout switch */

    } /* if ( $posts->have_posts() ) */

    echo $after_widget;
  }

  function update( $new_instance, $old_instance ) {
    return $new_instance;
  }

  function form( $instance ) {
    $widget_options = wp_parse_args( $instance, $this->widget_defaults );
    extract( $widget_options, EXTR_SKIP );

    $title = esc_attr( $title );

    $sections = get_terms( array(
      'taxonomy' => 'section',
      'hide_empty' => false,
    ) );

    ?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :'); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
    <p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of Posts :'); ?><input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" value="<?php echo $count; ?>" min=0 max=20 /></label></p>

    <p><label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image size:'); ?> <select name="<?php echo $this->get_field_name('image_size'); ?>" id="<?php echo $this->get_field_id('image_size'); ?>" >
    <?php

  foreach ($this->image_sizes as $value=>$key) {
    $option = '<option value="'. $key .'" '. ( $key === $image_size ? ' selected="selected"' : '' ) .'>';
    $option .= $value;
    $option .= '</option>\n';
    echo $option;
  }
 ?>
    </select></label></p>

    <p><label for="<?php echo $this->get_field_id('display_date'); ?>"><input class="widefat" id="<?php echo $this->get_field_id('display_date'); ?>" name="<?php echo $this->get_field_name('display_date'); ?>" type="checkbox" value="on" <?php checked( $display_date, "on" ) ?>/> <?php _e('Display date'); ?></label></p>
    <p><label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e('Layout:'); ?> <select name="<?php echo $this->get_field_name('layout'); ?>" id="<?php echo $this->get_field_id('layout'); ?>" >
    <?php

  foreach ($this->layout_options as $value=>$key) {
    $option = '<option value="'. $key .'" '. ( $key === $layout ? ' selected="selected"' : '' ) .'>';
    $option .= $value;
    $option .= '</option>\n';
    echo $option;
  }
 ?>
    </select></label></p>

<?php if( count($sections) > 0 ) { ?>

    <p><label for="<?php echo $this->get_field_id('section'); ?>"><?php _e('Section:'); ?> <select name="<?php echo $this->get_field_name('section'); ?>" id="<?php echo $this->get_field_id('section'); ?>" >
      <option value="recent" <?php 'recent' === $section ? ' selected="selected"' : ''; ?>>Recent</option>
      <?php

  foreach ($sections as $ID=>$term) {
    $option = '<option value="'. $term->slug .'" '. ( $term->slug === $section ? ' selected="selected"' : '' ) .'>';
    $option .= $term->name;
    $option .= '</option>\n';
    echo $option;
  }
 ?>
    </select></label></p>

<?php } ?>

    <p><label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order By:'); ?> <select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" >
      <?php

  foreach ($this->orderby_options as $value=>$key) {
    $option = '<option value="'. $key .'" '. ( $key === $orderby ? ' selected="selected"' : '' ) .'>';
    $option .= $value;
    $option .= '</option>\n';
    echo $option;
  }
 ?>
    </select></label></p>

    <p><label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:'); ?> <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" >
      <?php

  foreach ($this->order_options as $value=>$key) {
    $option = '<option value="'. $key .'" '. ( $key === $order ? ' selected="selected"' : '' ) .'>';
    $option .= $value;
    $option .= '</option>\n';
    echo $option;
  }
 ?>
    </select></label></p>

    <p><label for="<?php echo $this->get_field_id('item_class'); ?>"><?php _e('Item Class :'); ?><input class="widefat" id="<?php echo $this->get_field_id('item_class'); ?>" name="<?php echo $this->get_field_name('item_class'); ?>" type="text" value="<?php echo esc_html($item_class); ?>" /></label></p>

    <p><label for="<?php echo $this->get_field_id('wrap_class'); ?>"><?php _e('Wrap Class :'); ?><input class="widefat" id="<?php echo $this->get_field_id('wrap_class'); ?>" name="<?php echo $this->get_field_name('wrap_class'); ?>" type="text" value="<?php echo esc_html($wrap_class); ?>" /></label></p>
    <?php
  }
}