<?php
while ( have_posts() ) : 
	the_post();
 ?>  
   <article id="post-<?php the_ID(); ?>" <?php post_class( 'twelve columns entry' ); ?>>
       
       <div class="entry-header">
       	<?php get_template_part( 'partials/post', 'header' ); ?>
       	
       	<?php if ( has_post_thumbnail() ) : ?>
   		<figure class="entry-media">
   		    <?php
   		    /**
   		     * Featured Image Link
   		     *
   		     * Determine how, if at all, the featured image should be linked.
   		     * First we'll check for a custom field. If the custom field is\
   		     * provided, we'll use that link. If not, we'll use the option
   		     * set within the Theme Options.
   		     *
   		     * By default, the featured image will link to the post. If the
   		     * custom meta option is provided, it will trump any Theme Options set.
   		     */
   		    if ( ( $featured_image_link = get_post_meta( get_the_ID(), 'featured_image_link', true ) ) ) :
   		    	$link = $featured_image_link;
   		    	$link_option = 'custom';
   		    else :
   		    	$link_option = hoon_option( 'image_featured_links', 'post' );
   		    	
   		    	switch ( $link_option ) :
   		    		case 'none' :
   		    			$link = '#';
   		    			break;
   		    		case 'view' :
   		    			$thumbnail = wp_get_attachment_image_src ( get_post_thumbnail_id( get_the_ID() ), 'large' );
   		    			$link = $thumbnail[0];
   		    			break;
   		    		case 'file' :
   		    			$thumbnail = wp_get_attachment_image_src ( get_post_thumbnail_id( get_the_ID() ), 'large' );
   		    			$link = $thumbnail[0];
   		    			break;
   		    		case 'post' :
   		    			$link = is_single() ? '#' : get_permalink();
   		    			break;
   		    	endswitch;
   		    endif;
   		    
   		    printf( '<a href="%1$s" title="%2$s" %4$s>%3$s</a>',
   		    	esc_url( $link ),
   		    	esc_attr( get_the_title() ),
   		    	get_the_post_thumbnail( get_the_ID(), hoon_get_image_size() ),
   		    	'custom' == $link_option ? 'target="_blank"' : 'class="' . esc_attr( $link_option ) . '"'
   		    );
   		    ?>
   		</figure>
   		<?php endif; ?>
       </div>
       
   	<div class="entry-content">
       <?php get_template_part( 'partials/post', 'sharing' ); ?>
       
       <?php edit_post_link( '<i class="icon-edit"></i>' ); ?>
       
       <h1 class="entry-title">
           <?php
           $link_it =  is_singular() && ! is_page_template( 'template-blog.php' ) ? false : true;
           
           printf( '%1$s<span>%2$s</span>%3$s',
           	$link_it ? sprintf( '<a href="%1$s" title="%2$s">', get_permalink(), the_title_attribute( array( 'echo' => 0 ) ) ) : '',
           	get_the_title(),
           	$link_it ? '</a>' : ''
           );
           ?>
       </h1>
       
    <?php 
    if(get_post_meta($post->ID, '_cmb_price', true)){ ?>
       <div class="posted-on">
      
       <span>Price:</span>
           <div class="comment-count">
           <?php 
    
           echo get_post_meta($post->ID, '_cmb_price', true); 
           
           ?>
           </div>
       </div>
       <?php } ?>

       
       <?php
       
       /**
        * The Content
        *
        */
       if( is_page_template( 'template-blog.php' ) || ! is_singular() ) {
       	global $more; $more = 0;
       }
       ?>
       
       <div class="posted-content">
       	<?php the_content( sprintf( '<span class="moretext">%1$s</span>', __( '&hellip; Continue Reading', 'hoon' ) ) ); ?>
       </div>
       
       
       <?php
       /**
        * Page Links
        *
        */
       wp_link_pages( array(
           'before' => sprintf( '<p class="pagelinks"><span>%s</span><br />', __( 'Pages:', 'hoon' ) ),
           'after' => '</p>',
           'link_before' => '<span class="page-numbers">',
           'link_after' => '</span>'
       ) ); ?>
 
       </div>
       
   </article><!-- #post-<?php the_ID(); ?> -->				
   				
<?php
endwhile;
?>
