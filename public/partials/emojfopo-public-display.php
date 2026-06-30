<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://gunjanjaswal.me
 * @since      1.0.0
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/public/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$emojfopo_share_on   = get_option('emojfopo_enable_share', 'yes') === 'yes';
$emojfopo_total      = array_sum($reaction_counts);
$emojfopo_permalink  = get_permalink($post_id);
$emojfopo_share_title = get_the_title($post_id);
?>
<div class="<?php echo esc_attr($container_class); ?>" data-post-id="<?php echo esc_attr($post_id); ?>" data-total="<?php echo esc_attr($emojfopo_total); ?>" data-url="<?php echo esc_url($emojfopo_permalink); ?>" data-title="<?php echo esc_attr($emojfopo_share_title); ?>">
    <?php if (!is_archive() && !is_home() && !is_front_page()): ?>
    <div class="emojfopo-title"><?php echo esc_html(get_option('emojfopo_title_text', esc_html__('Reactions:', 'emojis-for-posts-and-pages'))); ?></div>
    <?php endif; ?>
    <div class="emojfopo-buttons">
        <?php foreach ($enabled_emojis as $emojfopo_key => $emojfopo_emoji) : ?>
            <?php $emojfopo_count = isset($reaction_counts[$emojfopo_key]) ? $reaction_counts[$emojfopo_key] : 0; ?>
            <?php $emojfopo_active_class = ($user_reaction === $emojfopo_key) ? 'active' : ''; ?>
            <?php $emojfopo_custom_name = isset($custom_names[$emojfopo_key]) ? $custom_names[$emojfopo_key] : ucfirst(str_replace('_', ' ', $emojfopo_key)); ?>
            <button class="emojfopo-reaction-button<?php echo esc_attr($user_reaction === $emojfopo_key ? ' active' : ''); ?>" data-reaction="<?php echo esc_attr($emojfopo_key); ?>" title="<?php echo esc_attr(isset($custom_names[$emojfopo_key]) ? $custom_names[$emojfopo_key] : ucfirst($emojfopo_key)); ?>">
                <span class="emoji"><?php echo esc_html($emojfopo_emoji); ?></span>
                <span class="count"><?php echo esc_html($emojfopo_count); ?></span>
            </button>
        <?php endforeach; ?>
    </div>
    
    <?php if ($user_reaction) : ?>
        <div class="emojfopo-message">
            <?php
            esc_html_e('You reacted with', 'emojis-for-posts-and-pages');
            echo ' ' . esc_html($enabled_emojis[$user_reaction]);
            ?>
        </div>
    <?php endif; ?>

    <?php if ($emojfopo_share_on) : ?>
        <div class="emojfopo-share" aria-label="<?php esc_attr_e('Share this post', 'emojis-for-posts-and-pages'); ?>">
            <span class="emojfopo-share-label"><?php esc_html_e('Share:', 'emojis-for-posts-and-pages'); ?></span>
            <a class="emojfopo-share-btn emojfopo-share-x" data-network="x" href="#" rel="nofollow noopener" target="_blank" title="<?php esc_attr_e('Share on X', 'emojis-for-posts-and-pages'); ?>" aria-label="<?php esc_attr_e('Share on X', 'emojis-for-posts-and-pages'); ?>"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.66l-5.214-6.817-5.967 6.817H1.677l7.73-8.835L1.254 2.25h6.83l4.713 6.231 5.447-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644Z"/></svg></a>
            <a class="emojfopo-share-btn emojfopo-share-fb" data-network="facebook" href="#" rel="nofollow noopener" target="_blank" title="<?php esc_attr_e('Share on Facebook', 'emojis-for-posts-and-pages'); ?>" aria-label="<?php esc_attr_e('Share on Facebook', 'emojis-for-posts-and-pages'); ?>"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073Z"/></svg></a>
            <a class="emojfopo-share-btn emojfopo-share-wa" data-network="whatsapp" href="#" rel="nofollow noopener" target="_blank" title="<?php esc_attr_e('Share on WhatsApp', 'emojis-for-posts-and-pages'); ?>" aria-label="<?php esc_attr_e('Share on WhatsApp', 'emojis-for-posts-and-pages'); ?>"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg></a>
            <a class="emojfopo-share-btn emojfopo-share-copy" data-network="copy" href="#" rel="nofollow noopener" title="<?php esc_attr_e('Copy link', 'emojis-for-posts-and-pages'); ?>" aria-label="<?php esc_attr_e('Copy link', 'emojis-for-posts-and-pages'); ?>"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></a>
        </div>
    <?php endif; ?>
</div>
