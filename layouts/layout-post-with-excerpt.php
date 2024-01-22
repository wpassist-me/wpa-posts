<?php

global $wpa_posts_widget_args;
extract( $wpa_posts_widget_args, EXTR_SKIP );

$wrap_class = $wrap_class == '' ? 'feed' : $wrap_class;
$item_class = $item_class == '' ? 'post' : $item_class;
$feature_wrap_class = $feature_wrap_class == '' ? 'feat mbs' : $feature_wrap_class;

echo "<div class=\"{$wrap_class}\">";

while ( $posts->have_posts() ){
  $posts->the_post(); 

?>
<article class="<?php echo $item_class; ?>">
<header>
<a href="<?php the_permalink(); ?>"><?php wpa_posts_featured_image( $feature_wrap_class, $image_size); ?></a>
<?php echo "<{$title_element}>"; ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php echo "</{$title_element}>"; ?>
<?php wpa_posts_the_meta( $meta_line ); ?>
</header>
<?php wpa_posts_the_excerpt( '<p>', '</p>', $link_on_excerpt, $excerpt_length ); ?>
</article>
<?php

}

echo "</div>";
