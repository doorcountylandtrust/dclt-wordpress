<?php
/**
 * Site footer (no Tailwind build required)
 * Uses plain CSS classes defined in /components/footer.css
 */
?>
<footer class="site-footer">
  <div class="footer-top">
    <div class="footer-brand">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo">
        <?php if (function_exists('the_custom_logo')) { the_custom_logo(); } else { ?>
          <span class="footer-site-name"><?php bloginfo('name'); ?></span>
        <?php } ?>
      </a>
      <p class="footer-mission">
        Protecting Door County’s exceptional lands and waters—forever.
      </p>

      <form class="footer-signup" method="post" action="#">
        <label for="footer_email" class="sr-only">Your email address</label>
        <input id="footer_email" name="email" type="email" placeholder="Your email address" required>
        <button type="submit">Subscribe</button>
        <small class="footer-signup-note">We send only conservation news. Unsubscribe anytime.</small>
      </form>
    </div>

    <nav class="footer-nav">
      <div class="footer-col">
        <h3>About</h3>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/about')); ?>">Who We Are</a></li>
          <li><a href="<?php echo esc_url(home_url('/board-of-directors')); ?>">Board of Directors</a></li>
          <li><a href="<?php echo esc_url(home_url('/staff')); ?>">Staff</a></li>
          <li><a href="<?php echo esc_url(home_url('/careers')); ?>">Careers</a></li>
          <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h3>Explore</h3>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/preserves')); ?>">Explore Preserves</a></li>
          <li><a href="<?php echo esc_url(home_url('/maps-and-trails')); ?>">Maps &amp; Trails</a></li>
          <li><a href="<?php echo esc_url(home_url('/stories-news')); ?>">Stories &amp; News</a></li>
          <li><a href="<?php echo esc_url(home_url('/events')); ?>">Events</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h3>Get Involved</h3>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/protect-your-land')); ?>">Protect Land &amp; Give</a></li>
          <li><a href="<?php echo esc_url(home_url('/donate')); ?>">Donate</a></li>
          <li><a href="<?php echo esc_url(home_url('/membership')); ?>">Membership</a></li>
          <li><a href="<?php echo esc_url(home_url('/volunteer')); ?>">Volunteer</a></li>
          <li><a href="<?php echo esc_url(home_url('/business-partners')); ?>">Business Partners</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h3>Contact</h3>
        <address class="footer-address">
          PO Box 65<br>
          Sturgeon Bay, WI 54235<br>
          <a href="mailto:info@doorcountylandtrust.org">info@doorcountylandtrust.org</a><br>
          <a href="tel:+19207461359">(920) 746-1359</a>
        </address>
      </div>
    </nav>
  </div>

  <div class="footer-bottom">
    <p>© <?php echo esc_html(date('Y')); ?> Door County Land Trust · 501(c)(3)</p>
    <ul class="footer-legal">
      <li><a href="<?php echo esc_url(home_url('/privacy')); ?>">Privacy</a></li>
      <li><a href="<?php echo esc_url(home_url('/terms')); ?>">Terms</a></li>
      <li><a href="<?php echo esc_url(home_url('/accessibility')); ?>">Accessibility</a></li>
    </ul>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>