# 😀 Emojis for Posts and Pages 👍

![WordPress Version](https://img.shields.io/badge/WordPress-7.1.4%20tested-blue.svg)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)

> Add colorful emoji reactions to your WordPress posts and pages, similar to Facebook reactions.

## ✨ Features

- 🎨 Add emoji reactions to posts, pages, or any custom post type
- 🌈 Choose from a variety of colorful emoji reactions
- 📊 Track reaction counts and statistics
- 📱 Mobile-friendly and responsive design
- 🔒 One reaction per IP address to prevent spam
- 🌐 Uses Google's Noto Color Emoji font for consistent cross-platform display
- 🔄 Display reactions after content or as a floating element

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## 🚀 Installation

### From GitHub:

1. Download the zip file from this repository
2. Upload the `emojis-for-posts-and-pages` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Settings > Emoji Reactions to configure the plugin

### From WordPress.org:

1. Search for "Emojis for Posts and Pages" in the WordPress plugin repository
2. Click "Install Now" and then "Activate"
3. Go to Settings > Emoji Reactions to configure the plugin

## 🛠️ Configuration

1. Navigate to **Settings > Emoji Reactions** in your WordPress admin panel
2. Choose which emoji reactions to enable
3. Customize the display position (after content or floating)
4. Set custom names for each reaction (optional)
5. Select which post types should display reactions

## 📝 Usage

### Basic Usage

The plugin will automatically display emoji reactions on your posts and pages based on your settings.

### Shortcode

You can also use the shortcode to display reactions anywhere in your content:

```
[emojfopo_reactions]
```

### Template Tag

For theme developers, you can use the template tag to display reactions anywhere in your theme:

```php
<?php if (function_exists('emojfopo_reactions_display')) {
    emojfopo_reactions_display();
} ?>
```

## 🎨 Customization

### CSS Customization

You can customize the appearance of the reactions by adding custom CSS to your theme:

```css
/* Example: Change the background color of the reactions container */
.wp-emoji-reactions-container {
    background-color: #f0f8ff;
}

/* Example: Change the hover effect of reaction buttons */
.wp-emoji-reaction-button:hover {
    transform: scale(1.2);
}
```

### Filter Hooks

The plugin provides several filter hooks for developers to customize its behavior:

```php
// Customize the enabled emojis
add_filter('emojfopo_enabled_emojis', function($emojis) {
    // Add a custom emoji
    $emojis['awesome'] = '😎';
    return $emojis;
});

// Customize the title text
add_filter('emojfopo_title_text', function($title) {
    return 'How do you feel about this?';
});
```

## 📊 Analytics

The plugin stores reaction data in the database, which you can use for analytics:

- See which content gets the most positive reactions
- Track engagement over time
- Identify content that resonates with your audience

## 🔄 Updates

This plugin is actively maintained. Updates will be available through:

1. WordPress.org plugin repository
2. This GitHub repository

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. Fork the repository
2. Create a feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request

## 📜 License

This plugin is licensed under the GPL v2 or later.

## 👨‍💻 Author

- [Gunjan Jaswal](https://gunjanjaswal.me/)
- GitHub: [gunjanjaswal/Emojis-For-Posts-And-Pages](https://github.com/gunjanjaswal/Emojis-For-Posts-And-Pages)
- Contact: hello@gunjanjaswal.me

## Support

If this plugin has been helpful, consider supporting development on Ko-fi:

[![Support on Ko-fi](https://img.shields.io/badge/Ko--fi-Support-FF5E5B?style=for-the-badge&logo=ko-fi&logoColor=white)](https://ko-fi.com/gunjanjaswal)

## 📝 Changelog

The full changelog lives in [`readme.txt`](readme.txt). Recent releases:

### Version 1.3.1
- Tested and confirmed compatible with WordPress 7.1.4.

### Version 1.3.0
- New: a Reaction Analytics screen under Settings. See your total reactions, a breakdown by emoji with each one's share, and the posts people react to the most, each linked straight to its editor.

### Version 1.2.0
- New: Engagement effects — emojis burst and float upward when visitors react (Facebook-Live style).
- New: Animated reaction counts that pop as they grow.
- New: Milestone celebrations — confetti and a toast when a post crosses 10, 25, 50, 100, 250, 500, 1000+ reactions.
- New: Light haptic feedback on supported mobile devices when reacting.
- New: One-tap share buttons (X, Facebook, WhatsApp, Copy link) with native share sheet on mobile.
- New: Two settings toggles to enable/disable effects and share buttons (both on by default).
- New: "Settings" link added to the plugin row on the Plugins screen.
- Accessibility: All animations honor the visitor's prefers-reduced-motion setting.
- No external libraries — confetti and animations are pure CSS/JS, keeping the plugin lightweight.

### Version 1.1.3
- WordPress 7.0 iframed-editor hygiene: scoped admin CSS/JS to the plugin's own admin screens so nothing leaks into the editor iframe or unrelated admin pages.
- Front-end reactions are emitted server-side via the `the_content` filter — no editor canvas assets are loaded, so the editor iframe transition has no impact.

### Version 1.1.2
- Updated "Tested up to" to WordPress 7.0.
- Replaced Buy Me a Coffee donation link with Ko-fi (https://ko-fi.com/gunjanjaswal).
- Added "Contact Developer" link to plugin row meta on the Plugins screen.
- Added GitHub Plugin URI header (https://github.com/gunjanjaswal/Emojis-For-Posts-And-Pages).
- Author display name updated to "Gunjan Jaswal".

## 🙏 Acknowledgements

- [WordPress Plugin Boilerplate](https://wppb.me/) for the plugin structure
- [Google Noto Color Emoji](https://fonts.google.com/noto/specimen/Noto+Color+Emoji) for the emoji font
