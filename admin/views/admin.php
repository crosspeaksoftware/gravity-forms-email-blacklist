<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Gravity_Forms_Email_Blacklist
 * @author    Tim Howe <timbhowe@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.hallme.com
 * @copyright 2014 Tim Howe
 */
?>

<div class="wrap">

	<h2><?php echo __( esc_html( get_admin_page_title(), 'oscimp_trdom' )); ?></h2>

    <form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <p><?php _e("Database host: " ); ?><input type="text" name="oscimp_dbhost" value="<?php echo $dbhost; ?>" size="20"><?php _e(" ex: localhost" ); ?></p>
        <p><?php _e("Database name: " ); ?><input type="text" name="oscimp_dbname" value="<?php echo $dbname; ?>" size="20"><?php _e(" ex: oscommerce_shop" ); ?></p>


        <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Update Options', 'oscimp_trdom' ) ?>" />
        </p>
    </form>
</div>