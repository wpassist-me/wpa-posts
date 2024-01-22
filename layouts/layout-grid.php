<?php

$wrap_class = $wrap_class == '' ? 'flex lhs' : $wrap_class;
$item_class = $item_class == '' ? 'md-c1_2 mb1' : $item_class;

?>
<ul class="<?php echo $wrap_class; ?>">
<?php
  while ( $posts->have_posts() ){
    $posts->the_post(); ?>
<li class="<?php echo $item_class; ?>">
<a class="d-block" href="<?php the_permalink(); ?>">
<?php wpa_posts_featured_image( 'thmb mbs', $image_size ); ?>
<?php the_title(); ?><?php wpa_posts_the_meta( $meta_line ); ?></a>
</li>
<?php
  }
?>
</ul>

<?php
