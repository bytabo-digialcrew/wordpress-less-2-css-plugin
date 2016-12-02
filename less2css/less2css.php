<?php
/*
Plugin Name: less2css
Plugin URI: http://bytabo.de
Version: 1.0
Author: Jo Höhn
*/

require_once('lessphp/lessc.inc.php');

add_action('admin_menu', 'less2css_create_menu');

function less2css_create_menu()
{
    add_submenu_page('tools.php', 'Less2Css', 'Less2Css', 'administrator', __FILE__, 'less2css_settings_page');
    add_action('admin_init', 'register_less2css_plugin_settings');
    convert_less();
}

function register_less2css_plugin_settings()
{
    register_setting('less2css-group', 'less_path');
    register_setting('less2css-group', 'css_path');
    register_setting('less2css-group', 'live_converting');
}

function less2css_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Less2Css</h1>
        <?php if(get_option('live_converting')) { ?>
        <div id="message" class="updated notice notice-success is-dismissible"><p>Less-Dateien werden konvertiert. </p>
            <button type="button" class="notice-dismiss"><span
                    class="screen-reader-text">Diese Meldung verwerfen.</span></button>
        </div>
    <?php } ?>

        <form method="post" action="options.php">
            <?php settings_fields('less2css-group'); ?>
            <?php do_settings_sections('less2css-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Input Less-File<br>
                        <small style="font-weight:normal;">Filename in <?php echo get_path(); ?></small>
                    </th>
                    <td><input type="text" name="less_path" value="<?php echo esc_attr(get_option('less_path')); ?>" placeholder="less/main.less" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Output Css-File<br>
                        <small style="font-weight:normal;">Filename in <?php echo get_path(); ?></small>
                    </th>
                    <td><input type="text" name="css_path" value="<?php echo esc_attr(get_option('css_path')); ?>" placeholder="css/main.css" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="live_converting">Live-Converting</label></th>
                    <td><input type="checkbox" id="live_converting"
                               name="live_converting" value="1" <?php if (get_option('live_converting')) {
                            echo 'checked';
                        } ?> />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

function get_path()
{
    $path = str_replace($_SERVER['HTTP_HOST'], "", get_bloginfo('template_url'));
    $path = str_replace("http://", "", $path);
    $path = str_replace("https://", "", $path);
    $path .= "/";
    return $path;
}


function convert_less()
{
    if (get_option('live_converting')) {
        $less = new lessc;
        $less->setFormatter("compressed");
        $less->checkedCompile($_SERVER['DOCUMENT_ROOT'] . get_path() . get_option('less_path'), $_SERVER['DOCUMENT_ROOT'] . get_path() . get_option('css_path'));
    }
}