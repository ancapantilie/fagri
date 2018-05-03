<?php
/**
 * This file contains all the pluggable functions from the parent theme that are overwritten in order to extend parent theme functionality. Also, there are some functions used within the overwritten functions of the parent theme
 *
 * @package fagri
 * @since 1.0.0
 */

/**
 * Display metadata for blog post on front page blog section
 *
 * @since 1.0.0
 */
function fagri_blog_section_metadata() {

	$author_name   = get_the_author_meta( 'display_name' );
	$author_email  = get_the_author_meta( 'user_email' );
	$author_avatar = get_avatar( $author_email, 40 );

	$utility_text = '<span class="fagri-metadata-avatar">%1$s</span><span class="fagri-metadata-autor">%2$s</span>';

	/* translators: 1 - is author gravatar, 2 - is author name */
	printf(
		$utility_text,
		$author_avatar,
		$author_name
	);
}

/**
 * Overriding hestia function in order to add a metadata row
 * Get content for blog section.
 *
 * @param bool $is_callback Flag to check if it's callback or not.
 * @since 1.0.0
 */
function hestia_blog_content( $is_callback = false ) {

	$hestia_blog_items = get_theme_mod( 'hestia_blog_items', 3 );
	if ( ! $is_callback ) {
		?>
		<div class="hestia-blog-content">
		<?php
	}

	$args                   = array(
		'ignore_sticky_posts' => true,
	);
	$args['posts_per_page'] = ! empty( $hestia_blog_items ) ? absint( $hestia_blog_items ) : 3;

	$hestia_blog_categories = get_theme_mod( 'hestia_blog_categories' );

	if ( ! empty( $hestia_blog_categories[0] ) && sizeof( $hestia_blog_categories ) >= 1 ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $hestia_blog_categories,
			),
		);
	}

	$loop = new WP_Query( $args );

	$allowed_html = array(
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'i'      => array(
			'class' => array(),
		),
		'span'   => array(),
	);

	if ( $loop->have_posts() ) :
		$i = 1;
		echo '<div class="row" ' . hestia_add_animationation( 'fade-up' ) . '>';
		while ( $loop->have_posts() ) :
			$loop->the_post();
			?>
			<article class="col-xs-12 col-ms-10 col-ms-offset-1 col-sm-8 col-sm-offset-2 <?php echo apply_filters( 'hestia_blog_per_row_class', 'col-md-4' ); ?> hestia-blog-item">
				<div class="card card-plain card-blog">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="card-image">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<?php the_post_thumbnail( 'hestia-blog' ); ?>
							</a>
						</div>
					<?php endif; ?>
					<div class="content">
						<h6 class="category"><?php hestia_category(); ?></h6>
						<h4 class="card-title">
							<a class="blog-item-title-link" href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
								<?php echo wp_kses( force_balance_tags( get_the_title() ), $allowed_html ); ?>
							</a>
						</h4>
						<p class="card-description"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
						<p class="fagri-blog-section-metadata"><?php fagri_blog_section_metadata(); ?></p>
					</div>
				</div>
			</article>
			<?php
			if ( $i % apply_filters( 'hestia_blog_per_row_no', 3 ) == 0 ) {
				echo '</div><!-- /.row -->';
				echo '<div class="row" ' . hestia_add_animationation( 'fade-up' ) . '>';
			}
			$i++;
		endwhile;
		echo '</div>';

		wp_reset_postdata();
	endif;

	if ( ! $is_callback ) {
		?>
		</div>
		<?php
	}
}

/**
 * Overriding pricing section in order to add an icon between the package name and the price
 * Pricing section content.
 * This function can be called from a shortcode too.
 * When it's called as shortcode, the title and the subtitle shouldn't appear and it should be visible all the time,
 * it shouldn't matter if is disable on front page.
 *
 * @since 1.0.0
 */
function hestia_pricing( $is_shortcode = true ) {

	/**
	 * Don't show section if Disable section is checked or it doesn't have any content.
	 * Show it if it's called as a shortcode.
	 */
	$hide_section  = get_theme_mod( 'hestia_pricing_hide', true );
	$section_style = '';
	if ( ! $is_shortcode && (bool) $hide_section === true ) {
		if ( is_customize_preview() ) {
			$section_style = 'style="display: none"';
		} else {
			return;
		}
	}

	/**
	 * Gather data to display the section.
	 */
	$hestia_pricing_title    = get_theme_mod( 'hestia_pricing_title', esc_html__( 'Choose a plan for your next project', 'hestia-pro' ) );
	$hestia_pricing_subtitle = get_theme_mod( 'hestia_pricing_subtitle', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );

	$hestia_pricing_table_one_title    = get_theme_mod( 'hestia_pricing_table_one_title', esc_html__( 'Basic Package', 'hestia-pro' ) );
	$hestia_pricing_table_one_price    = get_theme_mod( 'hestia_pricing_table_one_price', '<small>$</small>0' );
	$default                           =
		sprintf( '<b>%1$s</b> %2$s', esc_html__( '1', 'hestia-pro' ), esc_html__( 'Domain', 'hestia-pro' ) ) .
		sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '1GB', 'hestia-pro' ), esc_html__( 'Storage', 'hestia-pro' ) ) .
		sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '100GB', 'hestia-pro' ), esc_html__( 'Bandwidth', 'hestia-pro' ) ) .
		sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '2', 'hestia-pro' ), esc_html__( 'Databases', 'hestia-pro' ) );
	$hestia_pricing_table_one_features = get_theme_mod( 'hestia_pricing_table_one_features', $default );
	if ( ! is_array( $hestia_pricing_table_one_features ) ) {
		$hestia_pricing_table_one_features = explode( '\n', str_replace( '\r', '', wp_kses_post( force_balance_tags( $hestia_pricing_table_one_features ) ) ) );
	}
	$hestia_pricing_table_one_link = get_theme_mod( 'hestia_pricing_table_one_link', '#' );

	$hestia_pricing_table_one_text     = get_theme_mod( 'hestia_pricing_table_one_text', esc_html__( 'Free Download', 'hestia-pro' ) );
	$hestia_pricing_table_two_title    = get_theme_mod( 'hestia_pricing_table_two_title', esc_html__( 'Premium Package', 'hestia-pro' ) );
	$hestia_pricing_table_two_price    = get_theme_mod( 'hestia_pricing_table_two_price', '<small>$</small>49' );
	$default                           =
		sprintf( '<b>%1$s</b> %2$s', esc_html__( '5', 'hestia-pro' ), esc_html__( 'Domain', 'hestia-pro' ) ) .
		sprintf( ' \n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Storage', 'hestia-pro' ) ) .
		sprintf( ' \n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Bandwidth', 'hestia-pro' ) ) .
		sprintf( ' \n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Databases', 'hestia-pro' ) );
	$hestia_pricing_table_two_features = get_theme_mod( 'hestia_pricing_table_two_features', $default );
	if ( ! is_array( $hestia_pricing_table_two_features ) ) {
		$hestia_pricing_table_two_features = explode( '\n', str_replace( '\r', '', wp_kses_post( force_balance_tags( $hestia_pricing_table_two_features ) ) ) );
	}
	$hestia_pricing_table_two_link = get_theme_mod( 'hestia_pricing_table_two_link', '#' );
	$hestia_pricing_table_two_text = get_theme_mod( 'hestia_pricing_table_two_text', esc_html__( 'Order Now', 'hestia-pro' ) );

	/**
	 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
	 */
	$wrapper_class   = $is_shortcode === true ? 'is-shortcode' : '';
	$container_class = $is_shortcode === true ? '' : 'container';

	hestia_before_pricing_section_trigger();
	?>
	<section class="hestia-pricing pricing section-gray <?php echo esc_attr( $wrapper_class ); ?>" id="pricing" data-sorder="hestia_pricing" <?php echo wp_kses_post( $section_style ); ?>>
		<?php
		hestia_before_pricing_section_content_trigger();
		if ( $is_shortcode === false ) {
			hestia_display_customizer_shortcut( 'hestia_pricing_hide', true );
		}
		?>
		<div class="<?php echo esc_attr( $container_class ); ?>">
			<?php hestia_top_pricing_section_content_trigger(); ?>
			<div class="row">
				<div class="col-md-4 col-lg-4 hestia-pricing-title-area">
					<?php
					hestia_display_customizer_shortcut( 'hestia_pricing_title' );
					if ( ! empty( $hestia_pricing_title ) || is_customize_preview() ) :
						?>
						<h2 class="hestia-title"><?php echo wp_kses_post( $hestia_pricing_title ); ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $hestia_pricing_subtitle ) || is_customize_preview() ) : ?>
						<p class="text-gray"><?php echo wp_kses_post( $hestia_pricing_subtitle ); ?></p>
					<?php endif; ?>
				</div>
				<div class="col-md-8 col-lg-7 col-lg-offset-1">
					<div class="row">
						<div class="col-ms-6 col-sm-6 hestia-table-one" <?php echo hestia_add_animationation( 'fade-up' ); ?>>
							<?php hestia_display_customizer_shortcut( 'hestia_pricing_table_one_title' ); ?>
							<div class="card card-pricing card-raised">
								<div class="content">
									<?php if ( ! empty( $hestia_pricing_table_one_title ) || is_customize_preview() ) : ?>
										<h6 class="category"><?php echo esc_html( $hestia_pricing_table_one_title ); ?></h6>
									<?php endif; ?>
									<?php do_action( 'hestia_after_title_pricing_section_table_one_content_trigger' ); ?>
									<?php if ( ! empty( $hestia_pricing_table_one_price ) || is_customize_preview() ) : ?>
										<h3 class="card-title"><?php echo wp_kses_post( $hestia_pricing_table_one_price ); ?></h3>
									<?php endif; ?>

									<?php if ( ! empty( $hestia_pricing_table_one_features ) ) : ?>
										<ul>
											<?php foreach ( $hestia_pricing_table_one_features as $feature ) : ?>
												<li><?php echo wp_kses_post( $feature ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>

									<?php
									if ( ( ! empty( $hestia_pricing_table_one_link ) && ! empty( $hestia_pricing_table_one_text ) ) || is_customize_preview() ) {
										$link_html = '<a href="' . esc_url( $hestia_pricing_table_one_link ) . '"';
										if ( function_exists( 'hestia_is_external_url' ) ) {
											$link_html .= hestia_is_external_url( $hestia_pricing_table_one_link );
										}
										$link_html .= ' class="btn btn-primary">';
										$link_html .= esc_html( $hestia_pricing_table_one_text );
										$link_html .= '</a>';
										echo wp_kses_post( $link_html );
									}
									?>
								</div>
							</div>
						</div>
						<div class="col-ms-6 col-sm-6 hestia-table-two" <?php echo hestia_add_animationation( 'fade-left' ); ?>>
							<?php hestia_display_customizer_shortcut( 'hestia_pricing_table_two_title' ); ?>
							<div class="card card-pricing card-plain">
								<div class="content">
									<?php if ( ! empty( $hestia_pricing_table_two_title ) || is_customize_preview() ) : ?>
										<h6 class="category"><?php echo esc_html( $hestia_pricing_table_two_title ); ?></h6>
									<?php endif; ?>
									<?php do_action( 'hestia_after_title_pricing_section_table_two_content_trigger' ); ?>
									<?php if ( ! empty( $hestia_pricing_table_two_price ) || is_customize_preview() ) : ?>
										<h3 class="card-title"><?php echo wp_kses_post( $hestia_pricing_table_two_price ); ?></h3>
									<?php endif; ?>
									<?php if ( ! empty( $hestia_pricing_table_two_features ) ) : ?>
										<ul>
											<?php foreach ( $hestia_pricing_table_two_features as $feature ) : ?>
												<li><?php echo wp_kses_post( $feature ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
									<?php
									if ( ( ! empty( $hestia_pricing_table_two_link ) && ! empty( $hestia_pricing_table_two_text ) ) || is_customize_preview() ) {
										echo '<a href="' . esc_url( $hestia_pricing_table_two_link ) . '"';
										if ( function_exists( 'hestia_is_external_url' ) ) {
											echo hestia_is_external_url( $hestia_pricing_table_two_link );
										}
										echo ' class="btn btn-primary">' . esc_html( $hestia_pricing_table_two_text ) . '</a>';
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php hestia_bottom_pricing_section_content_trigger(); ?>
		</div>
		<?php hestia_after_pricing_section_content_trigger(); ?>
	</section>
	<?php
	hestia_after_pricing_section_trigger();
}

/**
 * Add icon to the first pricing package between the title and the price
 *
 * @since 1.0.0
 */
function fagri_table_one_card_pricing_icon() {

	$card_pricing_table_one_icon_type = get_theme_mod( 'fagri_pricing_table_one_icon', 'fa-gift' );

	echo '<div class="fagri-pricing-icon-wrapper"><i class="fa ' . $card_pricing_table_one_icon_type . '"></i></div>';
}
add_action( 'hestia_after_title_pricing_section_table_one_content_trigger', 'fagri_table_one_card_pricing_icon' );

/**
 * Add icon to the second pricing package between the title and the price
 *
 * @since 1.0.0
 */
function fagri_table_two_card_pricing_icon() {

	$card_pricing_table_two_icon_type = get_theme_mod( 'fagri_pricing_table_two_icon', 'fa-gift' );

	echo '<div class="fagri-pricing-icon-wrapper"><i class="fa ' . $card_pricing_table_two_icon_type . '"></i></div>';
}
add_action( 'hestia_after_title_pricing_section_table_two_content_trigger', 'fagri_table_two_card_pricing_icon' );
