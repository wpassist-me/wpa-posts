<?php

add_action( 'widgets_init', 'wpa_posts_register_posts_widget' );
function wpa_posts_register_posts_widget() {
  register_widget( 'WPA_Posts_Widget' );
}


function wpa_posts_meta_line_allowed_html(){
  return array(
      'a'      => array(
        'class'  => array(),
        'target'  => array(),
        'href'  => array(),
        'title' => array(),
      ),
      'br'     => array(),
      'em'     => array(),
      'strong' => array(),
      'small' => array(),
      'p' => array(
        'class' => array(),
      ),
    );
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
        'feature_wrap_class' => '',
        'display_excerpt' => 'off',
        'meta_line' => '<small>%term:category% - %updated%</small>',
        'link_on_excerpt' => 'off',
        'excerpt_length' => 55,
        'title_element' => 'h3',
        'image_size' => 'thumbnail',
        'post_type' => 'post',
        'offset' => false,
      );

    $this->layout_options = array(
      'List' => 'list',
      'List w/ Thumbs' => 'list-with-thumbs',
      'Grid' => 'grid',
      'Post with Excerpt' => 'post-with-excerpt',
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
    
    $this->title_elements = array(
      'H1' => 'h1',
      'H2' => 'h2',
      'H3' => 'h3',
      'H4' => 'h4',
      'span' => 'span',
      'div' => 'div',
    );

  }

  function widget( $args, $instance ) {
    global $wpa_posts_widget_args;

    extract( $args );
    $widget_options = wp_parse_args( $instance, $this->widget_defaults );
    $wpa_posts_widget_args = $widget_options;
    extract( $widget_options, EXTR_SKIP );

    echo $before_widget;

    $post_query = array(
        'post_type'=>$post_type,
        'posts_per_page'=>$count,
        'ignore_sticky_posts' => 1,
      );

    if( false !== $offset ){
      $post_query["offset"] = $offset;
    }

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

      include_once( 'layouts/layout-' . $layout . '.php' );

    } /* if ( $posts->have_posts() ) */

    wp_reset_postdata();

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

    <p><label for="<?php echo $this->get_field_id('link_on_excerpt'); ?>"><input class="widefat" id="<?php echo $this->get_field_id('link_on_excerpt'); ?>" name="<?php echo $this->get_field_name('link_on_excerpt'); ?>" type="checkbox" value="on" <?php checked( $link_on_excerpt, "on" ) ?>/> <?php _e('Link on Excerpt'); ?></label></p>

<?php 

$post_types = get_post_types( array(
     'public'   => true
  ), 'objects', 'and' );

?>

<p><label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:'); ?> <select name="<?php echo $this->get_field_name('post_type'); ?>" id="<?php echo $this->get_field_id('post_type'); ?>" >
    <?php

  foreach ($post_types as $key => $cpt) {
    $option = '<option value="'. $key .'" '. ( $key === $post_type ? ' selected="selected"' : '' ) .'>';
    $option .= $cpt->label;
    $option .= '</option>\n';
    echo $option;
  }
 ?>
    </select></label></p>

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
    
    <p><label for="<?php echo $this->get_field_id('meta_line'); ?>"><?php _e('Meta Line :'); ?><input class="widefat" id="<?php echo $this->get_field_id('meta_line'); ?>" name="<?php echo $this->get_field_name('meta_line'); ?>" type="text" value="<?php echo wp_kses(
    $meta_line,
    wpa_posts_meta_line_allowed_html() ); ?>" /></label></p>

    <p><label for="<?php echo $this->get_field_id('wrap_class'); ?>"><?php _e('Wrap Class :'); ?><input class="widefat" id="<?php echo $this->get_field_id('wrap_class'); ?>" name="<?php echo $this->get_field_name('wrap_class'); ?>" type="text" value="<?php echo esc_html($wrap_class); ?>" /></label></p>

    <p><label for="<?php echo $this->get_field_id('item_class'); ?>"><?php _e('Item Class :'); ?><input class="widefat" id="<?php echo $this->get_field_id('item_class'); ?>" name="<?php echo $this->get_field_name('item_class'); ?>" type="text" value="<?php echo esc_html($item_class); ?>" /></label></p>

    <p><label for="<?php echo $this->get_field_id('feature_wrap_class'); ?>"><?php _e('Feature Wrap Class :'); ?><input class="widefat" id="<?php echo $this->get_field_id('feature_wrap_class'); ?>" name="<?php echo $this->get_field_name('feature_wrap_class'); ?>" type="text" value="<?php echo esc_html($feature_wrap_class); ?>" /></label></p>

    <p><label for="<?php echo $this->get_field_id('offset'); ?>"><?php _e('Offset :'); ?><input class="widefat" id="<?php echo $this->get_field_id('offset'); ?>" name="<?php echo $this->get_field_name('offset'); ?>" type="number" value="<?php echo esc_html($offset); ?>" /></label></p>

    <p><label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e('Excerpt Length :'); ?><input class="widefat" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" type="number" value="<?php echo esc_html($excerpt_length); ?>" /></label></p>

    <p><label for="<?php echo $this->get_field_id('title_element'); ?>"><?php _e('Title element:'); ?> <select name="<?php echo $this->get_field_name('title_element'); ?>" id="<?php echo $this->get_field_id('title_element'); ?>" >
    <?php

  foreach ($this->title_elements as $value=>$key) {
    $option = '<option value="'. $key .'" '. ( $key === $title_element ? ' selected="selected"' : '' ) .'>';
    $option .= $value;
    $option .= '</option>\n';
    echo $option;
  }
 ?>
    </select></label></p>

    <?php

  }
}