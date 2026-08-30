=== HeaderFooterFlow for Elementor ===
Contributors: azijulhaque076
Tags: elementor, header, footer, header footer builder, hello elementor
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Design your site header and footer in Elementor, then control exactly where each one appears. Built for the Hello Elementor theme.

== Description ==

**HeaderFooterFlow** lets you build your site header and footer as ordinary Elementor layouts, then decide page by page which one is used.

No theme file editing, no child theme, no code. Create a template, mark it as a header or a footer, pick where it should show, and publish.

= What you get =

* **Build headers and footers in Elementor.** Every widget, every style control, the normal editor.
* **Per-template display rules.** Show a template across the entire site, on the homepage only, on all pages, on all single posts, or on a hand-picked list of pages.
* **Automatic priority.** When several templates could apply, the most specific rule wins — specific pages beat homepage, which beats all-pages and all-posts rules, which beat the site-wide default.
* **Searchable page picker.** Filter long page lists right in the sidebar instead of scrolling a giant multi-select.
* **Nav Menu widget.** Drop any WordPress menu into your header or footer layout, choose a horizontal or vertical layout, and style every part of it — typography, normal/hover/active colours, item spacing, padding, borders and the drop-down box.
* **Mobile Hamburger widget.** A toggle button for the off-canvas panel, with animated lines or your own icon, an optional label, per-breakpoint visibility, and full colour, size and spacing controls.
* **Style the off-canvas panel from Elementor.** Flip one switch on the hamburger widget and the Style tab gains Panel, Panel Header and Panel Menu sections — slide direction, width, background, overlay, close button, menu typography, colours, spacing and submenu indent. No custom CSS needed.
* **Off-canvas mobile menu.** Assign a menu to the plugin's menu location and a slide-in panel is added, complete with Escape-to-close, focus trapping and reduced-motion support. Leave the location empty and nothing is output at all.
* **Templates stay out of search results.** Template posts are marked `noindex, nofollow` and send an `X-Robots-Tag` header, so Google never lists your bare header as a page.
* **Safe by default.** If Elementor is missing, out of date, or the theme is not Hello Elementor, the plugin stays inactive and tells you why instead of breaking the site.

= Requirements =

* The **Hello Elementor** theme (or a child theme of it)
* The **Elementor** plugin, version 3.5.0 or newer
* WordPress 6.0+ and PHP 7.4+

= Third-party notice =

This plugin extends **Elementor**, a separate plugin developed by Elementor Ltd., and is designed for the **Hello Elementor** theme. It is not affiliated with, endorsed by, or sponsored by Elementor Ltd. Both are distributed independently from wordpress.org.

== Installation ==

1. Make sure the **Hello Elementor** theme and the **Elementor** plugin are both installed and active.
2. Upload the plugin through **Plugins → Add New → Upload Plugin**, or install it directly from the WordPress plugin directory.
3. Activate **HeaderFooterFlow for Elementor**.
4. Go to **HeaderFooterFlow → Add New**.
5. Give the template a name, choose **Header** or **Footer** under *Template Type*, and pick a rule under *Display Rules*.
6. Click **Edit with Elementor** and design the layout.
7. Publish. The template now replaces your theme header or footer wherever the rule matches.

== Frequently Asked Questions ==

= Does this work with any theme? =

No. This release targets the **Hello Elementor** theme specifically, including child themes of it. On any other theme the plugin deactivates itself functionally and shows an admin notice, so nothing breaks — it simply does nothing.

= Do I need Elementor Pro? =

No. The free Elementor plugin is enough.

= What happens if I have not created a template yet? =

Nothing changes. Your theme's own header and footer keep rendering. The swap only happens once a published template matches the page being viewed.

= Two templates could apply to the same page. Which one wins? =

The more specific rule. The order is: Specific pages (highest) → Homepage only → All single posts → All pages → Entire site (lowest).

= How do I open the mobile off-canvas menu? =

Assign a menu to **Appearance → Menus → HeaderFooterFlow — Off-canvas Menu**. Then drop the **Mobile Hamburger (Flow)** widget into your header template — it is wired to the panel out of the box.

If you would rather use your own element, add the class `hfflow-offcanvas-open` to any element in your Elementor header and it becomes a trigger. An element with the `hamburger` class also works out of the box.

= Can I change the off-canvas colours? =

Two ways. The easy one: select the **Mobile Hamburger (Flow)** widget, turn on **Customize The Panel** in the Content tab, and use the Panel, Panel Header and Panel Menu sections that appear in the Style tab.

The panel is shared by the whole site, so turn that switch on for one hamburger widget only — otherwise two widgets write competing rules for the same panel.

The manual one: the panel is styled entirely with CSS custom properties such as `--hfflow-offcanvas-bg`, `--hfflow-offcanvas-color`, `--hfflow-offcanvas-accent` and `--hfflow-offcanvas-width`. Override them in your theme or in Elementor custom CSS.

= Does uninstalling remove my templates? =

Yes. Deleting the plugin permanently removes the header/footer templates it created and their settings. Deactivating the plugin does not delete anything.

== Screenshots ==

1. The template list, showing each template's type and where it is displayed.
2. Template Type and Display Rules in the editor sidebar, with the searchable page picker.
3. A header template being designed in Elementor.
4. The Nav Menu widget in the Elementor panel.
5. The off-canvas mobile menu on a phone-sized viewport.
6. The Mobile Hamburger widget and its style controls.

== Changelog ==

= 1.1.0 =
* New **Mobile Hamburger (Flow)** widget: opens the off-canvas panel, with animated lines (two, three or four) or a custom icon, an optional label before or after the icon, an accessible label, alignment, and per-breakpoint show/hide.
* Hamburger style controls: icon size, padding, border, border radius, box shadow, normal and hover colours, line thickness, line gap, line radius, and label typography.
* Nav Menu widget: new Layout control (horizontal or vertical), responsive alignment, and an optional submenu arrow.
* Nav Menu widget: full Style tab — typography, normal/hover/active text and background colours, item padding, space between items, border and border radius.
* Nav Menu widget: new Dropdown style section — typography, colours, width, box and item padding, border, border radius and box shadow, with CSS-only hover and keyboard-focus drop-downs.
* Widget stylesheets are registered separately and loaded only on pages that use the widget.
* Triggers carrying `aria-expanded` are now kept in sync when the off-canvas panel opens and closes.
* The off-canvas panel can now be styled from the Mobile Hamburger widget. Turning on **Customize The Panel** adds Panel, Panel Header and Panel Menu sections to the Style tab, covering slide direction (left or right), width, background, overlay colour, padding, logo width, site title typography, close button size, radius and normal/hover colours, and menu typography, alignment, normal/hover/active colours, padding, item spacing and submenu indent.
* Fixed: the off-canvas close button picked up the colours and border radius from Elementor’s Global Theme Style for buttons, because `.elementor-kit-{id} button` outranked the plugin’s single-class reset. The close button and the new hamburger button are now reset at a specificity that wins.
* Fixed: the off-canvas panel background was 97.3%% opaque (#000000f8), so the page behind it bled through and bright header content appeared as faint grey ghosts over the menu. The panel is now opaque by default; use the new Background Color control if you want it translucent.
* The off-canvas stylesheet is now driven end to end by CSS custom properties, so every panel value can be overridden from a theme or from Elementor custom CSS.

= 1.0.1 =
* Added the `Requires Plugins` header so WordPress enforces the Elementor dependency at activation.
* Templates now support revisions, enabling Elementor's revision history.
* Template permalinks no longer depend on rewrite rules, so the Elementor editor preview works on a fresh install.
* Removed the redundant translation loader; WordPress loads translations automatically.
* Hardened template cleanup during uninstall.
* Updated compatibility: WordPress 7.1, Elementor 4.2.3.

= 1.0.0 =
* Initial release.
* Header and footer templates built with Elementor.
* Display rules: entire site, specific pages, homepage only, all single posts, all pages.
* Specificity-based template matching.
* Searchable page picker in the Display Rules meta box.
* Nav Menu Elementor widget with depth control.
* Accessible off-canvas mobile menu with focus trapping.
* Requirement checks for Elementor, Elementor version and the Hello Elementor theme.

== Upgrade Notice ==

= 1.1.0 =
Adds the Mobile Hamburger widget and a full Style tab for the Nav Menu widget. Templates, menus and display rules are unaffected, but the Nav Menu widget now ships its own base layout (a flex row with a 24px gap and no list bullets). If you were styling `.hfflow-menu` by hand, check that page after updating.

= 1.0.1 =
Compatibility and housekeeping update. Existing templates and display rules are unaffected.

= 1.0.0 =
First public release.
