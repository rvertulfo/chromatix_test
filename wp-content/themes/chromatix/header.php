<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">

	<header>

		<div class="container">
		<div class="logo"><img src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" /></div>
		<nav id="site-navigation" class="primary-navigation" role="navigation">
			<?php
			wp_nav_menu(
				array(
					'container' => 'ul',
					'menu_class' => 'menu-wrapper',
					'walker' 	=> new Menu_Walker
				)
			);
			?>
		</nav><!-- #site-navigation -->


	</header>
	<div id="content" class="site-content ">
			<main id="main" class="site-main container" role="main">