<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page page--services" id="main">
	<div class="page__container">
		<nav class="breadcrumbs" aria-label="Хлебные крошки">
			<a class="breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span aria-hidden="true">/</span><span aria-current="page">Услуги</span>
		</nav>
		<div class="page-heading"><h1 class="page-heading__title"><?php post_type_archive_title(); ?></h1></div>
		<div class="collection collection--services">
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="service-card">
					<div class="service-card__body">
						<div class="service-card__heading"><h2 class="service-card__title"><?php the_title(); ?></h2></div>
						<div class="service-card__details">
							<?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
							<a class="action action--primary action--card-link service-card__action" href="<?php the_permalink(); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Подробнее</span></a>
						</div>
					</div>
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'class' => 'service-card__image' ) ); endif; ?>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
