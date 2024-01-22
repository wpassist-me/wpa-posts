<?php

global $wpa_posts_widget_args;
extract( $wpa_posts_widget_args, EXTR_SKIP );

$wrap_class = $wrap_class == '' ? 'lhs' : $wrap_class;
$item_class = $item_class == '' ? 'flex mbs' : $item_class;

?>
<ul class="<?php echo $wrap_class; ?>">
<?php
  while ( $posts->have_posts() ){
    $posts->the_post(); ?>
<li class="<?php echo $item_class; ?>">
<a href="<?php the_permalink(); ?>"><?php wpa_posts_featured_image('thmb-s', $image_size); ?></a><div class='fill pxs'><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php wpa_posts_the_meta( $meta_line ); ?></div>
</li>
<?php
  }
?>
</ul>
<?php
