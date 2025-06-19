<!-- header.php -->
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <title><?php wp_title(); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<a class="btn" href="#">Test Button</a>
<button class="btn-test">Test Me</button>

<div class="bg-brand text-black p-6 text-center">
    Tailwind is working!
</div>
<div class="bg-red-500 text-white p-4 text-xl">Tailwind is definitely working</div>