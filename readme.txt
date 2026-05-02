=== User Account ===
Contributors: stef
Tags: account, my account, user profile, members
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight "My Account" page for logged-in WordPress users — no WooCommerce, no BuddyPress required.

== Description ==

User Account gives your logged-in visitors a clean, self-contained "My Account" page with zero third-party dependencies.

**Features**

* Auto-creates a My Account page on activation
* Shortcode `[user_account]` — drop it anywhere
* Redirects guests to the login page, then back after sign-in
* **Dashboard tab** — customisable welcome message and up to 3 quick-link shortcuts
* **Profile tab** — edit first/last name, email, bio; upload a custom avatar or fall back to Gravatar; change password
* All saves done in real time via AJAX — no page reload
* **Settings page** (Settings > User Account) to configure message, shortcuts, and active tabs
* Developer-friendly: extensible tab system, hooks before/after every section, filterable profile fields

**For developers**

Add a custom tab in two lines:

`
add_filter( 'ua_tabs', function( $tabs ) {
    $tabs['orders'] = [
        'label'    => 'My Orders',
        'callback' => 'my_render_orders',
        'order'    => 30,
    ];
    return $tabs;
} );
`

Available actions: `ua_before_account`, `ua_after_account`, `ua_before_tab`, `ua_after_tab`, `ua_before_save_profile`, `ua_after_save_profile`.
Available filters: `ua_tabs`, `ua_profile_fields`, `ua_redirect_url`.

== Installation ==

1. Upload the `user-account` folder to `/wp-content/plugins/`.
2. Activate the plugin via **Plugins > Installed Plugins**.
3. A "My Account" page is created automatically — add it to your navigation menu.
4. Configure the plugin at **Settings > User Account**.

== Frequently Asked Questions ==

= Can I use the shortcode in Bricks Builder? =
Yes. Add a Code block and drop in `[user_account]`.

= What happens if I delete the My Account page and reactivate the plugin? =
The plugin detects the missing page and recreates it automatically.

= Can I add my own tabs? =
Yes — use the `ua_tabs` filter. See the description for a code sample.

= Does it work without WooCommerce? =
Absolutely. It has no dependency on WooCommerce, BuddyPress, ACF, or any other plugin.

== Screenshots ==

1. My Account page — Dashboard tab
2. My Account page — Profile tab
3. Settings page in the WordPress admin

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
