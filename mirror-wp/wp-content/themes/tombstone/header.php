<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php wp_head(); ?>

    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri().'/assets/css/swiper-bundle.min.css' ?>"/>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" /> -->

    
    
</head>
<body <?php body_class(); ?>>
    <header class="site-header glass-effect">
        
        <div class="container">
            <div class="logo">
                <a href="<?php echo home_url(); ?>">
                    <?php 
                if ( has_custom_logo() ) {
                    $logo = get_custom_logo();
                    $logo = preg_replace( '/(width|height)="\d*"\s/', '', $logo );
                    echo $logo;
                } else {
                    echo '<h1>'. get_bloginfo('name') .'</h1>';
                }
                ?>
            </a>
        </div>
        <nav class="main-menu desktop ff-inter-300">
            <?php
            wp_nav_menu([
                'theme_location' => 'header_menu',
                // 'container'      => false,
                // 'menu_class'     => 'menu',
            ]);
            ?>
        </nav>
        <span class="openbtn fs-36" onclick="openNav()">&#9776;</span>
    </div>
</header>

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <?php
    wp_nav_menu([
        'theme_location' => 'header_menu',
        // 'container'      => false,
        // 'menu_class'     => 'menu',
    ]);
    ?>
</div>
<div id="substrate" class="glass-effect"></div>

<script>
    function openNav() {
  mySidenav.style.left = "0";
  substrate.style.width = "100%";
}
function closeNav() {
  mySidenav.style.left = "-250px";
  substrate.style.width = "0";
}
substrate.addEventListener( 'click', (event) => closeNav() )
</script>

<div class="cloud">
  <img src="<? echo get_stylesheet_directory_uri() . '/assets/images/bg-top.png' ?>" alt="">
</div>

<?php
    if (!is_front_page()) { echo '<main class="mt-150">'; } 
    else {                  echo '<main class="mt-270">'; } 
?>