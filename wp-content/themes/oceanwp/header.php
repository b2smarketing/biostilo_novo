<?php

/**
 * The Header for our theme.
 *
 * @package OceanWP WordPress theme
 */

?>
<!DOCTYPE html>
<html class="<?php echo esc_attr(oceanwp_html_classes()); ?>" <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script type="text/javascript" src="/wp-content/themes/oceanwp/assets/js/personalizacao-produto.js"></script>
	<link rel="stylesheet" href="/wp-content/themes/oceanwp/assets/css/personalizacao-produto.css">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php oceanwp_schema_markup('html'); ?>>
	<div id="quadro">
		<h2>Logos</h2>
		<div class="produtos">
			<?php include "personalizacao-produto.php"; ?>
		</div><span>Fechar</span>
	</div>
	<div id="site">
		<?php wp_body_open(); ?>

		<?php do_action('ocean_before_outer_wrap'); ?>

		<div id="outer-wrap" class="site clr">

			<a class="skip-link screen-reader-text" href="#main"><?php oceanwp_theme_strings('owp-string-header-skip-link', 'oceanwp'); ?></a>

			<?php do_action('ocean_before_wrap'); ?>

			<div id="wrap" class="clr">

				<?php do_action('ocean_top_bar'); ?>

				<?php do_action('ocean_header'); ?>

				<?php do_action('ocean_before_main'); ?>

				<main id="main" class="site-main clr" <?php oceanwp_schema_markup('main'); ?> role="main">

					<?php do_action('ocean_page_header'); ?>