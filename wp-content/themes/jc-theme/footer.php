</main>
<footer class="container">
    <div class="row">
        <div class="col-xs-12 text-center">
            <?php wp_nav_menu( array('theme_location' => 'footer') ); ?>
        </div>
    </div>
    <div class="row text-center">
        <span class="copyright">&copy; <?php echo date('Y'); ?> YVESYANG, Inc. </span>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
