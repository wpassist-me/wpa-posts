<?php


// echo post date on widget - filter: wpa_posts_date
function wpa_posts_the_date( $display_date = "off" ){

  if( "on" == $display_date ): 
    echo apply_filters( "wpa_posts_date", "<br><small>" . get_the_time( get_option( 'date_format' ) ) . "</small>" );
  endif;

}


// checks if current request is amp
function wpa_posts_is_amp(){
  return apply_filters( 'wpa_posts_is_amp', (function_exists( 'is_amp' ) && is_amp()) || ( function_exists( 'amp_is_request' ) && amp_is_request() ) );
}


// utility function to get key from an array/object or return default
function wpa_posts_key_or_default( $key, $inparray, $default='' ){
  if( is_array( $inparray ) && array_key_exists( $key, $inparray ) && '' !== $inparray[$key] ){
    if( is_array( $inparray[$key] ) ){
      if( '' !== $inparray[$key][0] ){
        return $inparray[$key][0];
      } else {
        return $default;
      }
    } else {
      return $inparray[$key];
    }
  } else if ( isset( $inparray ) && isset( $inparray->$key ) ){
    return $inparray->$key;
  } else {
    return $default;
  }
}


// get featured image for the post
function wpa_posts_get_featured_image( $size = 'medium' ){
  global $post;
  $post_id = get_the_ID();
  $feat_id = get_post_meta( $post_id, '_thumbnail_id', true );

  if( !$feat_id ){ return null; }

  $feat = wp_get_attachment_image_src( $feat_id, $size );

  return $feat;
}


// display featured image for the post
function wpa_posts_featured_image( $class="thmb", $size="medium" ){
  $featured_image = wpa_posts_get_featured_image( $size );

  if( $featured_image ):
    if( wpa_posts_is_amp() ):
      ?><div class="<?php echo $class; ?>"><amp-img alt="<?php the_title_attribute(); ?>"
        src="<?php echo $featured_image[0]; ?>"
        width="414"
        height="260"
        layout="responsive">
      </amp-img></div><?php
    else:
      echo "<div class=\"{$class}\">";
      the_post_thumbnail( 
        $size, 
        [
          'title' => the_title_attribute( 'echo=0' ) 
        ] 
      );
      echo "</div>";
    endif;
  else:
    if( wpa_posts_is_amp() ):
      ?><div class="<?php echo $class; ?>"><amp-img alt="<?php the_title_attribute(); ?>"
        src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="
        width="414"
        height="260"
        layout="responsive">
      </amp-img></div><?php
    else:
      echo "<div class=\"{$class}\"><img src=\"data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==
\" title=\"". the_title_attribute( 'echo=0' ) ."\"></div>";
    endif;
  endif;
}