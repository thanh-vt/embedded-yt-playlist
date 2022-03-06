<?php
/**
 * Plugin Name: Embedded Youtube Playlist
 * Plugin URI: https://github.com/thanh-vt/embedded-yt-playlist
 * Description: Embed Youtube playlist section into Wordpress page
 * Version: 1.0
 * Author: ThanhVt
 * Author URI: https://github.com/thanh-vt
 * License: GPLv2 or later
 */
?>
<?php
if (!class_exists('EmbeddedYtPlaylist')) { // Kiểm tra class đã tồn tại chưa
    class EmbeddedYtPlaylist // Tạo 1 class với tên là tên của plugin
    {
        function __construct()
        {
            if (!function_exists('add_shortcode')) {
                return;
            }
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_and_styles'));

            add_shortcode('embedded_yt_playlist_app', array(&$this, 'render_app')); // Tạo short code
            add_filter('script_loader_tag', array($this, 'add_type_attribute'), 10, 3);
        }

        function render_app($attrs = array(), $content = null): string
        {
            //            wp_enqueue_script('vue', 'https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js', [], '2.5.17');
            wp_enqueue_style('vue-style');
            wp_enqueue_script('vue-vendors-script');
            wp_enqueue_script('embedded-yt-playlist-vue-script');
            extract(shortcode_atts(array('playlistid' => '', 'channelid' => ''), $attrs)); // Bung các biến tùy chọn của short code
            wp_localize_script('embedded-yt-playlist-vue-script', 'eypInfo',
                array(
                    'playlistId' => $playlistid,
                    'channelId' => $channelid
                )
            );
            return '<script src="https://apis.google.com/js/platform.js"></script><div id="app"></div>';
        }

        function add_type_attribute($tag, $handle, $src)
        {
            // if not your script, do nothing and return original $tag
            if ('your-script-handle' !== $handle) {
                return $tag;
            }
            // change the script tag by adding type="module" and return it.
            return '<script type="module" src="' . esc_url($src) . '"></script>';
        }

        function enqueue_scripts_and_styles()
        {
            // Vue
            wp_register_style('vue-style', plugins_url('/dist/css/app.css', __FILE__));
            wp_register_script('vue-vendors-script', plugins_url('/dist/js/chunk-vendors.js', __FILE__));
            wp_register_script('embedded-yt-playlist-vue-script', plugins_url('/dist/js/app.js', __FILE__), ['vue-vendors-script']);

//            wp_enqueue_script('youtube-embedded-script', 'https://apis.google.com/js/platform.js');
        }


    }
}
function eyp_load() // hàm load plugin
{
    global $eyp;
    $eyp = new EmbeddedYtPlaylist(); // tạo đối tượng plugin
}

add_action('plugins_loaded', 'eyp_load'); // Dùng action chạy hàm khởi tạo biến $eyp_load khi plugin được tải
?>