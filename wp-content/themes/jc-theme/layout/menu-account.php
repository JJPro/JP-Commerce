<?php if (!is_user_logged_in()) : ?>
<a class="btn btn-signin" href="<?php echo home_url('signin'); ?>">Sign In</a>
<?php else : ?>
<div class="user-dropdown">

    <a href="#">
        <?php
            $logged_user = wp_get_current_user();
            echo $logged_user->display_name;
        ?>
    </a>
    <ul class="sub-menu">
        <li><a href="<?php echo home_url('favorites'); ?>"><span class="icon-heart-o"></span> Favorites</a></li>
        <li><a href="<?php echo home_url('orders'); ?>"><span class="icon-cube"></span> Orders</a></li>
        <li><a href="<?php echo home_url('account'); ?>"><span class="icon-cog"></span> Account</a></li>
        <hr>
        <li><a href="<?php echo wp_logout_url( home_url() ); ?>"><span class="icon-sign-out"></span> Sign Out</a></li>
    </ul>
</div>
<?php endif; ?>
<a class="btn btn-primary" href="<?php echo home_url('cart'); ?>"> <i class="icon-shopping-cart"></i><span class="badge pull-right"><?php echo shopping_cart_items_count(); ?></span></a>


<?php //if (current_user_can('artist')): ?>
<!--    <li><a href="--><?php //echo admin_url('post-new.php?post_type=artwork'); ?><!--">Upload New Art</a> </li>-->
<!--    <li><a href="--><?php //echo admin_url('edit.php?post_type=artwork'); ?><!--">All Arts</a> </li>-->
<?php //endif; ?>