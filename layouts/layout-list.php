<?php

global $wpa_posts_widget_args;
extract( $wpa_posts_widget_args, EXTR_SKIP );

// default css classes for list
$item_class = $item_class == '' ? 'flex mbs' : $item_class;

?>
<ul class="<?php echo $wrap_class; ?>">
<?php
while ( $posts->have_posts() ){
  $posts->the_post(); ?>
<li class="<?php echo $item_class; ?>"><a class="d-block" href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php wpa_posts_the_meta( $meta_line ); ?></li>
<?php
}
?>
</ul>
<?php