<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for
 * enqueuing the admin-specific stylesheet and JavaScript.
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/admin
 * @author     Gunjan Jaswal <hello@gunjanjaswal.me>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Emojfopo_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Check if the current admin screen belongs to this plugin.
     *
     * The block editor in WordPress 7.0 runs inside an iframe. Plugin admin
     * assets (this settings page only) belong to the parent admin chrome, so we
     * scope enqueues tightly to avoid leaking CSS/JS into the editor iframe or
     * other admin screens.
     *
     * @since 1.1.3
     * @param string $hook Current admin page hook.
     * @return bool True if this is one of the plugin's own admin screens.
     */
    private function is_plugin_admin_screen($hook) {
        return is_string($hook) && (false !== strpos($hook, 'emojfopo') || false !== strpos($hook, 'emojis-for-posts-and-pages'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     * @param string $hook Current admin page hook.
     */
    public function enqueue_styles($hook = '') {
        if (!$this->is_plugin_admin_screen($hook)) {
            return;
        }
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/emojfopo-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     * @param string $hook Current admin page hook.
     */
    public function enqueue_scripts($hook = '') {
        if (!$this->is_plugin_admin_screen($hook)) {
            return;
        }
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/emojfopo-admin.js', array('jquery'), $this->version, false);

        wp_localize_script(
            $this->plugin_name,
            'emojfopo_admin',
            array(
                'no_emojis_selected' => esc_html__('No emoji reactions selected. Please select at least one.', 'emojis-for-posts-and-pages'),
                'nonce' => wp_create_nonce('emojfopo_admin_nonce')
            )
        );
    }
    
    /**
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_plugin_admin_menu() {
        add_options_page(
            __('Emojis for Posts and Pages Settings', 'emojis-for-posts-and-pages'),
            __('Emoji Reactions', 'emojis-for-posts-and-pages'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page')
        );
    }
    
    /**
     * Add donate link to plugin action links
     *
     * @since  1.0.0
     * @param  array  $links Array of plugin action links
     * @param  string $file  Plugin file path
     * @return array  Modified array of plugin action links
     */
    public function add_plugin_action_links($links, $file) {
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . 'emojis-for-posts-and-pages.php');
        
        // Check if we're on the correct plugin
        if (plugin_basename($file) === $plugin_basename) {
            // Add settings link at the start
            $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=' . $this->plugin_name)) . '">' . __('Settings', 'emojis-for-posts-and-pages') . '</a>';
            array_unshift($links, $settings_link);

            // Add donate link at the end
            $donate_link = '<a href="https://ko-fi.com/gunjanjaswal" target="_blank" style="color:#0073aa;font-weight:bold;">' . __('Support on Ko-fi', 'emojis-for-posts-and-pages') . '</a>';
            $links[] = $donate_link; // Add to the end of the array
        }

        return $links;
    }

    /**
     * Add Contact Developer link to plugin row meta on the Plugins screen.
     *
     * @since  1.1.2
     * @param  array  $links Existing plugin row meta links.
     * @param  string $file  Plugin file name.
     * @return array Modified row meta links.
     */
    public function add_plugin_row_meta($links, $file) {
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . 'emojis-for-posts-and-pages.php');
        if (plugin_basename($file) === $plugin_basename) {
            $links[] = '<a href="https://wordpress.org/support/plugin/emojis-for-posts-and-pages/" target="_blank">' . __('Plugin Support', 'emojis-for-posts-and-pages') . '</a>';
            $links[] = '<a href="mailto:hello@gunjanjaswal.me">' . __('Contact Developer', 'emojis-for-posts-and-pages') . '</a>';
        }
        return $links;
    }
    
    /**
     * Register plugin settings
     *
     * @since  1.0.0
     */
    public function register_settings() {
        register_setting(
            'emojfopo_settings',
            'emojfopo_enabled_emojis',
            array($this, 'validate_emojis')
        );
        
        register_setting(
            'emojfopo_settings',
            'emojfopo_position',
            array($this, 'validate_position')
        );
        
        register_setting(
            'emojfopo_settings',
            'emojfopo_post_types',
            array($this, 'validate_post_types')
        );
        
        register_setting(
            'emojfopo_settings',
            'emojfopo_custom_names',
            array($this, 'validate_custom_names')
        );
        
        register_setting(
            'emojfopo_settings',
            'emojfopo_title_text',
            array($this, 'validate_title_text')
        );

        register_setting(
            'emojfopo_settings',
            'emojfopo_enable_effects',
            array($this, 'validate_yes_no')
        );

        register_setting(
            'emojfopo_settings',
            'emojfopo_enable_share',
            array($this, 'validate_yes_no')
        );

        add_settings_section(
            'emojfopo_general_settings',
            esc_html__('General Settings', 'emojis-for-posts-and-pages'),
            array($this, 'general_settings_section_callback'),
            $this->plugin_name
        );
        
        add_settings_field(
            'emojfopo_position',
            esc_html__('Display Position', 'emojis-for-posts-and-pages'),
            array($this, 'position_field_callback'),
            $this->plugin_name,
            'emojfopo_general_settings'
        );
        
        add_settings_field(
            'emojfopo_post_types',
            esc_html__('Enable on Post Types', 'emojis-for-posts-and-pages'),
            array($this, 'post_types_field_callback'),
            $this->plugin_name,
            'emojfopo_general_settings'
        );
        
        add_settings_field(
            'emojfopo_title_text',
            esc_html__('Reactions Title Text', 'emojis-for-posts-and-pages'),
            array($this, 'title_text_field_callback'),
            $this->plugin_name,
            'emojfopo_general_settings'
        );

        add_settings_field(
            'emojfopo_enable_effects',
            esc_html__('Engagement Effects', 'emojis-for-posts-and-pages'),
            array($this, 'enable_effects_field_callback'),
            $this->plugin_name,
            'emojfopo_general_settings'
        );

        add_settings_field(
            'emojfopo_enable_share',
            esc_html__('Share Buttons', 'emojis-for-posts-and-pages'),
            array($this, 'enable_share_field_callback'),
            $this->plugin_name,
            'emojfopo_general_settings'
        );

        add_settings_section(
            'emojfopo_emoji_settings',
            __('Emoji Settings', 'emojis-for-posts-and-pages'),
            array($this, 'emoji_settings_section_callback'),
            $this->plugin_name
        );
        
        add_settings_field(
            'emojfopo_enabled_emojis',
            __('Enabled Emojis', 'emojis-for-posts-and-pages'),
            array($this, 'emojis_field_callback'),
            $this->plugin_name,
            'emojfopo_emoji_settings'
        );
    }
    
    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page() {
        include_once('partials/emojfopo-admin-display.php');
    }
    
    /**
     * General settings section callback
     *
     * @since    1.0.0
     */
    public function general_settings_section_callback() {
        echo '<p>' . esc_html__('Configure how emoji reactions should be displayed on your site.', 'emojis-for-posts-and-pages') . '</p>';
    }
    
    /**
     * Emoji settings section callback
     *
     * @since    1.0.0
     */
    public function emoji_settings_section_callback() {
        echo '<p>' . esc_html__('Select which emoji reactions should be available to your visitors.', 'emojis-for-posts-and-pages') . '</p>';
    }
    
    /**
     * Position field callback
     *
     * @since    1.0.0
     */
    public function position_field_callback() {
        $position = get_option('emojfopo_position', 'after_content');
        ?>
        <select name="emojfopo_position" id="emojfopo_position">
            <option value="after_content" <?php selected($position, 'after_content'); ?>><?php esc_html_e('After Content', 'emojis-for-posts-and-pages'); ?></option>
            <option value="floating" <?php selected($position, 'floating'); ?>><?php esc_html_e('Floating (Fixed Position)', 'emojis-for-posts-and-pages'); ?></option>
        </select>
        <p class="description"><?php esc_html_e('Choose where to display the emoji reactions.', 'emojis-for-posts-and-pages'); ?></p>
        <?php
    }
    
    /**
     * Post types field callback
     *
     * @since    1.0.0
     */
    public function post_types_field_callback() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $enabled_post_types = get_option('emojfopo_post_types', array('post'));
        
        echo '<p>' . esc_html__('Select which post types should display emoji reactions:', 'emojis-for-posts-and-pages') . '</p>';
        
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $enabled_post_types) ? 'checked="checked"' : '';
            echo '<label><input type="checkbox" name="emojfopo_post_types[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . '> ' . esc_html($post_type->label) . '</label><br>';
        }
        
        echo '<p class="description">' . esc_html__('Select which post types should display emoji reactions.', 'emojis-for-posts-and-pages') . '</p>';
    }
    
    /**
     * Title text field callback
     *
     * @since    1.0.0
     */
    public function title_text_field_callback() {
        $title_text = get_option('emojfopo_title_text', __('Reactions:', 'emojis-for-posts-and-pages'));
        
        echo '<input type="text" name="emojfopo_title_text" value="' . esc_attr($title_text) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__('Customize the title text shown above reactions. Default: "Reactions:"', 'emojis-for-posts-and-pages') . '</p>';
    }
    
    /**
     * Engagement effects field callback
     *
     * @since    1.2.0
     */
    public function enable_effects_field_callback() {
        $value = get_option('emojfopo_enable_effects', 'yes');
        ?>
        <label>
            <input type="checkbox" name="emojfopo_enable_effects" value="yes" <?php checked($value, 'yes'); ?>>
            <?php esc_html_e('Enable emoji burst, floating reactions, count animation and milestone confetti', 'emojis-for-posts-and-pages'); ?>
        </label>
        <p class="description"><?php esc_html_e('Adds delightful, performant animations when visitors react. Automatically disabled for visitors who prefer reduced motion.', 'emojis-for-posts-and-pages'); ?></p>
        <?php
    }

    /**
     * Share buttons field callback
     *
     * @since    1.2.0
     */
    public function enable_share_field_callback() {
        $value = get_option('emojfopo_enable_share', 'yes');
        ?>
        <label>
            <input type="checkbox" name="emojfopo_enable_share" value="yes" <?php checked($value, 'yes'); ?>>
            <?php esc_html_e('Show one-tap share buttons (X, Facebook, WhatsApp, Copy link) below reactions', 'emojis-for-posts-and-pages'); ?>
        </label>
        <p class="description"><?php esc_html_e('Lets readers spread your post in a single tap. Uses the native share sheet on mobile when available.', 'emojis-for-posts-and-pages'); ?></p>
        <?php
    }

    /**
     * Emojis field callback
     *
     * @since    1.0.0
     */
    public function emojis_field_callback() {
        $default_emojis = array(
            'like' => '👍',
            'love' => '❤️',
            'laugh' => '😂',
            'wow' => '😮',
            'sad' => '😢',
            'angry' => '😠'
        );
        
        $enabled_emojis = get_option('emojfopo_enabled_emojis', $default_emojis);
        $custom_names = get_option('emojfopo_custom_names', array());
        
        $available_emojis = array(
            'like' => '👍',
            'love' => '❤️',
            'laugh' => '😂',
            'wow' => '😮',
            'sad' => '😢',
            'angry' => '😠',
            'clap' => '👏',
            'thinking' => '🤔',
            'fire' => '🔥',
            'party' => '🎉',
            'thumbs_down' => '👎',
            'eyes' => '👀',
            'rocket' => '🚀',
            'heart_eyes' => '😍',
            'hundred' => '💯',
            'tada' => '🎊'
        );
        
        echo '<div class="emoji-grid">';
        foreach ($available_emojis as $key => $emoji) {
            $checked = isset($enabled_emojis[$key]) ? 'checked="checked"' : '';
            $custom_name = isset($custom_names[$key]) ? $custom_names[$key] : ucfirst(str_replace('_', ' ', $key));
            
            echo '<div class="emoji-item">';
            echo '<label>';
            echo '<input type="checkbox" name="emojfopo_enabled_emojis[' . esc_attr($key) . ']" value="' . esc_attr($emoji) . '" ' . esc_attr($checked) . '>';
            echo '<span class="emoji-preview">' . esc_html($emoji) . '</span>';
            echo '<span class="emoji-name">' . esc_html($custom_name) . '</span>';
            echo '</label>';
            echo '<input type="text" name="emojfopo_custom_names[' . esc_attr($key) . ']" value="' . esc_attr($custom_name) . '" class="emoji-custom-name" placeholder="' . esc_attr__('Custom name', 'emojis-for-posts-and-pages') . '">';
            echo '</div>';
        }
        echo '</div>';
        
        echo '<p class="description">' . esc_html__('Select which emoji reactions should be available to your visitors. You can customize the display name for each reaction.', 'emojis-for-posts-and-pages') . '</p>';
    }
    
    /**
     * Validate emojis
     *
     * @since    1.0.0
     * @param    array    $input    Selected emojis input.
     * @return   array    Validated emojis.
     */
    public function validate_emojis($input) {
        // If no emojis are selected, use the default set
        if (empty($input)) {
            $default_emojis = array(
                'like' => '👍',
                'love' => '❤️',
                'laugh' => '😂',
                'wow' => '😮',
                'sad' => '😢',
                'angry' => '😠'
            );
            return $default_emojis;
        }
        
        // Make sure we're returning the correct format
        $validated = array();
        foreach ($input as $key => $emoji) {
            if (!empty($emoji)) {
                $validated[$key] = $emoji;
            }
        }
        
        return $validated;
    }
    
    /**
     * Validate position
     *
     * @since    1.0.0
     */
    public function validate_position($input) {
        $valid_positions = array('after_content', 'floating');
        
        if (!in_array($input, $valid_positions)) {
            return 'after_content';
        }
        
        return $input;
    }
    
    /**
     * Validate post types
     *
     * @since    1.0.0
     */
    public function validate_post_types($input) {
        if (empty($input)) {
            return array('post');
        }
        
        $valid_post_types = get_post_types(array('public' => true));
        
        foreach ($input as $key => $post_type) {
            if (!in_array($post_type, $valid_post_types)) {
                unset($input[$key]);
            }
        }
        
        return $input;
    }
    
    /**
     * Validate custom names
     *
     * @since    1.0.0
     * @param    array    $input    Custom names input.
     * @return   array    Validated custom names.
     */
    public function validate_custom_names($input) {
        $validated = array();
        
        if (is_array($input)) {
            foreach ($input as $key => $name) {
                $validated[$key] = sanitize_text_field($name);
                
                // If empty, use default name based on key
                if (empty($validated[$key])) {
                    $validated[$key] = ucfirst(str_replace('_', ' ', $key));
                }
            }
        }
        
        return $validated;
    }
    
    /**
     * Validate title text
     *
     * @since    1.0.0
     * @param    string    $input    Title text input.
     * @return   string    Validated title text.
     */
    public function validate_title_text($input) {
        if (empty($input)) {
            return __('Reactions:', 'emojis-for-posts-and-pages');
        }

        return sanitize_text_field($input);
    }

    /**
     * Validate a yes/no checkbox option.
     *
     * Unchecked checkboxes are not submitted, so a missing value means "no".
     *
     * @since    1.2.0
     * @param    mixed    $input    Submitted value.
     * @return   string   'yes' or 'no'.
     */
    public function validate_yes_no($input) {
        return ($input === 'yes') ? 'yes' : 'no';
    }
}
