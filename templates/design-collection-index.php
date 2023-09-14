<div id="mystyle-design-collection-index-wrapper" class="mystyle-design-collection-index">
    
    <div class="collections-menu">
        <ul>
            <?php foreach($all_terms as $term_menu_item) : ?>
            <li>
                <a href="/design-collections/<?php print $term_menu_item->slug ; ?>" title="<?php print $term_menu_item->name ; ?>"><?php print $term_menu_item->name ; ?></a>
            </li>
            <?php endforeach ; ?>
        </ul>
    </div>
    
    <div class="collections-content">
        <?php foreach($terms as $term) : ?>
            
        <div class="collection-row">
            <h3>
                <a href="/design-collections/<?php print $term->slug ; ?>/" title="<?php print $term->name ; ?>"><?php print $term->name ; ?></a>
            </h3>
            <?php $count = count($term->designs) ; ?>
            <?php if( $count > 0 ) : ?>
            <?php foreach($term->designs as $design) : ?>
            <?php
			$design_url = MyStyle_Design_Profile_page::get_design_url( $design );
			$user       = get_user_by( 'id', $design->get_user_id() );
			?>
            <div class="design-tile">
                
                <div class="design-img">
                    
                    <a href="<?php echo esc_url( $design_url ); ?>" title="<?php echo esc_attr( ( null !== $design->get_title() ) ? $design->get_title() : 'Custom Design ' . $design->get_design_id() ); ?>">
                        <img alt="<?php echo esc_html( ( null !== $design->get_title() ) ? $design->get_title() : 'Custom Design ' . $design->get_design_id() ); ?> Image" src="<?php echo esc_url( $design->mystyle_design_Url() ); ?>" />
                     <?php echo esc_html(html_entity_decode((null !== $design->get_title()) ? $design->get_title() : 'Custom Design ' . $design->get_design_id())); ?>
                    </a>
                    <?php if( $user ) : ?>
                    <div class="mystyle-design-author">Designed by: <?php echo esc_html( $user->user_nicename ); ?></div>
                    <?php endif; ?>
                </div>
                
            </div>
            <?php endforeach ; ?>
            <?php else : ?>
            <div>All designs in this collection are private</div>
            <?php endif ; ?>
            <?php if( count($terms) > 1 && $count > $limit ) : ?>
            <div class="design-tile view-more">
                <a href="/design-collections/<?php print $term->slug ; ?>" title="<?php print $term->name ; ?>">View More</a>
            </div>
            <?php endif ; ?>
        </div>
        <?php endforeach ; ?>
        <nav class="woocommerce-pagination">
            <?php
            echo paginate_links( // WPCS: XSS ok.
                array(
                    'base'      => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
                    'format'    => '',
                    'add_args'  => false,
                    'current'   => $mystyle_pager->get_current_page_number(),
                    'total'     => $mystyle_pager->get_page_count(),
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                    'type'      => 'list',
                    'end_size'  => 3,
                    'mid_size'  => 3,
                )
            );
            ?>
        </nav>
    </div>
    
    
</div>