<?php if (!is_user_logged_in()) : ?>
<a class="btn btn-signin" href="<?php echo home_url('signin'); ?>">Sign In</a>
<?php else : ?>
<div class="dropdown user-dropdown">

    <button class="btn btn-user-dropdown" type="button" id="user-account-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?php
            $logged_user = wp_get_current_user();
            echo $logged_user->display_name;
        ?>
    </button>
    <ul class="dropdown-menu" aria-labelledby="user-account-menu">
        <li><a href="#">Action</a></li>
        <li><a href="#">Another action</a></li>
        <li><a href="#">Something else here</a></li>
        <li role="separator" class="divider"></li>
        <li><a href="#">Separated link</a></li>
    </ul>
</div>
<?php endif; ?>
<a class="btn btn-primary" href="<?php echo home_url('shopping_cart'); ?>"><i class="icon-shopping-cart"></i><span class="badge pull-right"><?php echo shopping_cart_items_count(); ?></span></a>


<?php //if (current_user_can('artist')): ?>
<!--    <li><a href="--><?php //echo admin_url('post-new.php?post_type=artwork'); ?><!--">Upload New Art</a> </li>-->
<!--    <li><a href="--><?php //echo admin_url('edit.php?post_type=artwork'); ?><!--">All Arts</a> </li>-->
<?php //endif; ?>