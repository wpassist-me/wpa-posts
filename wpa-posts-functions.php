<?php


// echo post date on widget - filter: wpa_posts_date
function wpa_posts_the_date( $display_date = false, $before = '<small>', $after='</small>' ){

  if( "on" == $display_date ): 
    echo apply_filters( "wpa_posts_date", "{$before}" . get_the_time( get_option( 'date_format' ) ) . "{$after}" );
  endif;

}

// %author%, %term:category%, %published%, %updated%
function wpa_posts_the_meta( $meta_line ){
  if( empty($meta_line) ){ return; }
  
  $replacements = array(
    '%author%' => get_the_author_meta( 'display_name' ),
    '%published%' => get_the_date(),
    '%updated%' => get_the_modified_date(),
  );
  
  foreach( $replacements as $s=>$r ){
    if( false !== stripos( $meta_line, $s ) ){
      $meta_line = str_replace( $s, $r, $meta_line );
    }
  }

  // replace term link
  if( false !== stripos( $meta_line, '%term:' ) ){
    preg_match('/%term:(.*?)%/', $meta_line, $matches );
    if( is_array( $matches ) && count($matches) > 1 ){
      $taxonomy = $matches[1];
      $term_link = wpa_posts_get_single_term_link( $taxonomy );
      $meta_line = str_replace( $matches[0], $term_link, $meta_line );
    }

  }

  echo wp_kses(
    $meta_line,
    wpa_posts_meta_line_allowed_html() );
}


function wpa_posts_the_term( $display = false, $taxonomy = 'category', $before = '', $after = '' ){
  if( "on" === $display ){
    wpa_posts_single_term_link( $taxonomy, $before, $after );
  }
}


function wpa_posts_get_single_term( $taxonomy, $post_id = false ){
  if( !$post_id ) $post_id = get_the_ID();
  $terms = get_the_terms( $post_id, $taxonomy );
  if( !$terms ) return false;
  if( !is_array( $terms ) ) return false;
  return $terms[0];
}


function wpa_posts_single_term_link( $taxonomy, $prefix="", $suffix="" ){
  echo wpa_posts_get_single_term_link( $taxonomy, $prefix, $suffix );
}


function wpa_posts_get_single_term_link( $taxonomy, $prefix="", $suffix="" ){
  $term = wpa_posts_get_single_term( $taxonomy );
  if( $term ){
    $term_link = get_term_link( $term->term_id );
    return "<a href='{$term_link}'>{$prefix}{$term->name}{$suffix}</a>";
  }
}


function wpa_posts_the_excerpt( $before, $after, $link_on_excerpt = false, $words_count = 25 ){
  $excerpt = wpa_posts_get_the_excerpt( $words_count );
  if(!empty($excerpt)){
    $link_open='';
    $link_close='';
    if( $link_on_excerpt == "on" ){
      $link = get_the_permalink();
      $link_open = "<a href=\"{$link}\">";
      $link_close = "</a>";
    }
    echo "{$before}{$link_open}{$excerpt}{$link_close}{$after}";
  }
}


function wpa_posts_get_the_excerpt( $words_count = 25 ){
  global $post;
  
  $words_count = intval( $words_count );

  $content_empty = (strlen( $post->post_content ) === 0);
  $excerpt_empty = (strlen( $post->post_excerpt ) === 0);

  if ( $content_empty && $excerpt_empty ){ return ''; }

  if( !$excerpt_empty ){
    $description = $post->post_excerpt;
  } else {
    $description = do_shortcode( $post->post_content );
  }
  
  $urls_regex = '/https:\/\/[a-zA-Z0-9]+\.[a-zA-Z0-9]{2,8}((\/[a-zA-Z0-9?_=%&\.#;-]+)+)?/';
  $description = preg_replace( $urls_regex, '', $description );
  $description = strip_tags( $description );
  $description = strip_shortcodes( $description );
  $description = str_replace( array("\n", "\r", "\t"), ' ', $description );
  $description = wp_trim_words( $description, $words_count );

  return $description;
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