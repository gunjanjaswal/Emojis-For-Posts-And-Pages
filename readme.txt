=== Emojis for Posts and Pages ===
Contributors: gunjanjaswal
Donate link: https://ko-fi.com/gunjanjaswal
Tags: emoji, reactions, feedback, comments, social
Requires at least: 5.0
Tested up to: 7.0
Stable tag: 1.1.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add colorful emoji reactions to your WordPress posts and pages, similar to Facebook reactions.

== Description ==

Emojis for Posts and Pages allows your visitors to react to your content with colorful emoji reactions, similar to Facebook's reaction system. This plugin adds a simple and intuitive reaction system to your posts, pages, or any custom post type.

### Features

* Add emoji reactions to posts, pages, or any custom post type
* Choose from a variety of colorful emoji reactions
* Track reaction counts and statistics
* Display reactions after content or as a floating element
* One reaction per IP address to prevent spam
* Mobile-friendly and responsive design
* Uses Google's Noto Color Emoji font for consistent cross-platform display

### How It Works

1. Visitors can click on an emoji to react to your content
2. Each visitor can only react once per post (based on IP address)
3. Visitors can change their reaction by clicking on a different emoji
4. Reaction counts are displayed in real-time

### Use Cases

* Increase engagement on your blog posts
* Get quick feedback on your content
* Add a fun interactive element to your website
* Understand which content resonates with your audience

== Installation ==

1. Upload the `emojis-for-posts-and-pages` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Emoji Reactions to configure the plugin
4. Choose which emoji reactions to enable and where to display them

== Privacy ==

This plugin does not collect or share any data with external services.

The following information is stored in your WordPress database:
* IP addresses of users who react to posts (for preventing multiple reactions from the same user)
* User IDs of logged-in users who react to posts
* Reaction choices made by users

This data is stored solely on your server and is not transmitted elsewhere.

== Frequently Asked Questions ==

= Can visitors react multiple times to the same post? =

No, each visitor can only react once per post, based on their IP address. They can change their reaction by clicking on a different emoji.

= Can I customize which emoji reactions are available? =

Yes, you can choose which emoji reactions to enable from the plugin settings page.

= Can I display reactions on custom post types? =

Yes, you can choose which post types should display emoji reactions from the plugin settings page.

= Does this plugin work with page builders? =

Yes, the plugin should work with most page builders as it hooks into WordPress's content filter.

= Does this plugin slow down my website? =

No, the plugin is lightweight and optimized for performance. It only loads resources on pages where reactions are displayed.

= Can I see statistics on reactions? =

Yes, basic statistics are available on the plugin settings page. More detailed analytics may be added in future versions.

== Screenshots ==

1. Emoji reactions displayed after post content
2. Admin settings page
3. Reaction statistics

== Changelog ==

= 1.1.2 =
* Updated "Tested up to" to WordPress 7.0.
* Replaced Buy Me a Coffee donation link with Ko-fi (https://ko-fi.com/gunjanjaswal).
* Added "Contact Developer" link to plugin row meta on the Plugins screen.
* Added GitHub Plugin URI header (https://github.com/gunjanjaswal/Emojis-For-Posts-And-Pages).
* Author display name updated to "Gunjan Jaswal".

= 1.1.1 =
* Fixed WordPress coding standards: Added proper prefixes to all global variables
* Variable names now use 'emojfopo_' prefix for compliance
* Improved code quality and WordPress.org plugin check compatibility

= 1.1.0 =
* Updated for WordPress 6.9 compatibility
* Updated minimum PHP requirement to 7.4
* Added proper plugin headers (Plugin URI, Requires at least, Requires PHP, Tested up to)
* Enhanced WordPress coding standards compliance
* Verified compatibility with WordPress 6.9 features

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.2 =
Compatibility with WordPress 7.0; donation link moved to Ko-fi; Contact Developer row meta + GitHub link added.

= 1.1.1 =
Coding standards fix for WordPress.org plugin check compliance.

= 1.1.0 =
Compatibility update for WordPress 6.9. Requires PHP 7.4 or higher.

= 1.0.0 =
Initial release of WP Emoji Reactions.

== Privacy Policy ==

This plugin collects IP addresses to prevent multiple reactions from the same visitor. IP addresses are stored in your WordPress database and are not shared with any third parties.

If a user is logged in, their user ID is also stored along with their reaction. This allows their reaction to persist across different devices.

No personal information is collected or shared with external services.
