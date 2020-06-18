<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 14-Jul-18
 * Time: 10:34 AM
 */
//$uid = uniqid( 'sharing' );
//$user_id = get_the_ID();
//$compare_listings   = noo_get_page_link_by_template( 'compare-listings.php' );
//$property_favorites = noo_get_page_link_by_template( 'property_favorites.php' );
?>

<article>
    <div class="featured-style-2">
        <div class="fp-style-2--thumb">
            <?php 
            $defaultIMG = get_template_directory_uri().'/assets/images/placeholder.jpg'; 
            $img = get_the_post_thumbnail(get_the_ID(), 'property-image');
            ?>
            <a class="content-thumb" href="<?php the_permalink() ?>">
                <?php 
                if ($img != null) {
                    echo $img;
                } if ($img == null) {
                    echo '<img src="'.$defaultIMG.'">';
                }
                ?>
            </a>
			
            <div class="content-thumb-overlay">
				 
				
                <div class="property-action">
					
                    <div class="action-container">
						
                        <?php if ( !empty( $social_enabled ) ) : ?>
						
                            <div class="property-sharing <?php echo $uid = uniqid( 'sharing' ); ?>">
								
                                <?php echo  re_property_social_share(); ?>
                            </div>
                        <?php endif; ?>
                        <?php
                        $is_favorites       = get_user_meta( $user_id, 'is_favorites', true );
                        $check_is_favorites = ( !empty( $is_favorites ) && in_array( get_the_ID(), $is_favorites ) ) ? true : false;
                        $class_favorites    = $check_is_favorites ? 'is_favorites' : 'add_favorites';
                        $text_favorites     = $check_is_favorites ? esc_html__( 'View favorites', 'noo' ) : esc_html__( 'Add to favorites', 'noo' );
                        $icon_favorites     = $check_is_favorites ? 'fa-heart' : 'fa-heart-o';
                        ?>

                        <i title="<?php echo esc_html__( 'Compare', 'noo' ); ?>"
                           data-user="<?php echo $user_id; ?>"
                           data-id="<?php echo get_the_ID(); ?>"
                           data-action="compare"
                           data-thumbnail="<?php the_post_thumbnail_url( 'property-floor' ); ?>"
                           data-div="<?php echo esc_attr( $id_compare ); ?>" class="property-action-button fa fa-plus compare-<?php echo get_the_ID(); ?>" aria-hidden="true"></i>
						
                        <?php if( ( re_get_agent_setting('users_can_register', true) && !is_user_logged_in() ) || is_user_logged_in() ) : ?>

                            <i title="<?php echo esc_html( $text_favorites ); ?>" data-user="<?php echo $user_id; ?>" data-id="<?php echo get_the_ID(); ?>" data-action="favorites" data-status="<?php echo esc_attr( $class_favorites ); ?>" data-url="<?php echo esc_attr( $property_favorites ); ?>" class="property-action-button fa <?php echo esc_attr( $icon_favorites ); ?>" aria-hidden="true"></i>

                        <?php endif; ?>

                        <?php if ( !empty( $social_enabled ) ) : ?>
                            <i title="<?php echo esc_html__( 'Share', 'noo' ); ?>" data-user="<?php echo $user_id; ?>" data-id="<?php echo get_the_ID(); ?>" data-action="sharing" data-class="<?php echo esc_attr( $uid ); ?>" class="property-action-button fa fa-share-alt" aria-hidden="true"></i>
                        <?php endif; ?>
						
                    </div>
						   
                </div>
		    
            </div>
			
        </div>
			
        <div class="fp-style-2--wrap">
            <div>
                <?php 
                $price = re_get_property_price_html(get_the_ID(), true);
                if( strlen($price)!= 2):
                ?>
                <span class="fp-style-2--price">
                    <?php echo $price ?>
                </span>
                
                <?php endif; ?>
                <div class="fp-style-2--title">
                    <h4><a href="<?php the_permalink(); ?>"
                           title="<?php the_title(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h4>
                    <?php if ($address = noo_get_post_meta(get_the_ID(), '_address')) : ?>
                        <small class="fp-style-2--address">
                            <i class="fa fa-map-marker"></i> <?php echo esc_html($address); ?>
                        </small>
                    <?php endif; ?>
                </div>
                <div class="fp-style-2--excerpt">
                    <?php if ($excerpt = get_the_excerpt()): ?>
                        <?php
                        $num_word = 30;
                        $excerpt = strip_shortcodes($excerpt);
                        echo '<p>' . wp_trim_words($excerpt, $num_word, '...') . '</p>';
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="fp-style-2--summary">
            <?php echo re_property_summary(); ?>
        </div>

    </div>
</article>