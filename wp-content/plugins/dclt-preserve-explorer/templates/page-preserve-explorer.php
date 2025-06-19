<?php
/**
 * Template Name: Preserve Explorer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="bg-green-800 text-white p-10 text-center">
    <h1 class="text-4xl font-bold">Door County Preserve Explorer</h1>
    <p class="text-xl mt-2">Discover and explore Door County's natural treasures</p>
</div>

<div id="preserve-explorer-root" class="relative z-10"></div>

<?php get_footer(); ?>