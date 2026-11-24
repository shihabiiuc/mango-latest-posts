=== Mango Latest Posts for Elementor and Beyond ===
Contributors: shihab0915
Tags: latest posts, recent posts, blog grid, elementor, shortcode
Requires at least: 6.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Show your latest blog posts in a clean card grid or masonry layout. Works with Elementor, the block editor, and any theme via shortcode.

== Description ==

**Mango Latest Posts** shows your newest blog posts as cards. Each card has a featured image, the post title, a short excerpt and a "Read More" button. The image, title and button all link to the post.

It works almost anywhere:

* **Elementor:** drag the **Mango Blog Grid** widget onto your page.
* **Block editor (Gutenberg):** add the **Mango Blog Grid** block.
* **Any other theme or page builder:** use the `[mango_latest_posts]` shortcode.

Elementor is **not** required. If Elementor is installed, the widget appears automatically.

= Features =

* Shows the 3 latest posts by default. You can show 1 to 24.
* **Equal-height grid:** images are cropped to 1200 × 628 from the center and the buttons line up.
* **Masonry grid:** images keep their original proportions.
* 1 to 4 columns. Tablets show up to 2 columns and phones show 1.
* Excerpt length in words (30 by default, or 0 to hide it).
* Filter by category, skip the newest posts (offset), and hide the post being viewed on single post pages.
* Custom "Read More" text, with a right-chevron icon.
* Change the title color and size, excerpt color and size, and the button background, text color, padding and font size.
* Card border (1px solid #cfcfcf by default), border radius, padding, background and gap are all editable.
* A built-in placeholder image for posts without a featured image.
* Lightweight: one small stylesheet that loads only where the grid is used, and no JavaScript on the front end.
* Translation ready.

= Shortcode =

The basic shortcode shows the 3 latest posts:

`[mango_latest_posts]`

Every option is available as an attribute:

`[mango_latest_posts posts="6" columns="3" layout="masonry" excerpt_words="20" category="news,tips" offset="0" exclude_current="yes" read_more_text="Continue reading" title_color="#111111" title_font_size="22" excerpt_color="#555555" excerpt_font_size="15" button_bg="#ff6600" button_color="#ffffff" button_font_size="14" button_padding="10px 22px"]`

**Content attributes**

* `posts`: number of posts, 1–24. Default `3`.
* `columns`: desktop columns, 1–4. Default `3`.
* `layout`: `grid` (equal height, cropped images) or `masonry`. Default `grid`.
* `excerpt_words`: excerpt length in words, 0–100. Default `30`.
* `category`: category slugs or IDs, separated by commas. Default: all categories.
* `offset`: number of newest posts to skip. Default `0`.
* `exclude_current`: `yes` or `no`. Hides the post being viewed. Default `yes`.
* `read_more_text`: button text. Default `Read More`.
* `title_tag`: `h2`, `h3`, `h4`, `h5`, `h6`, `p` or `div`. Default `h3`.

**Style attributes** (colors are hex or rgb/rgba; a number without a unit means pixels)

* `title_color`, `title_font_size`
* `excerpt_color`, `excerpt_font_size`
* `button_bg`, `button_color`, `button_font_size`, `button_padding` (for example `10px 22px`)
* `card_bg`, `border_color`, `border_width`, `border_radius`, `card_padding`, `gap`

= Need a custom WordPress website? =

This plugin is built and maintained by Shihab, a WordPress developer. If you need a custom theme, plugin or Elementor website, [get in touch](https://shihabiiuc.com/contact/) or visit [shihabiiuc.com](https://shihabiiuc.com/).

== Installation ==

1. In your WordPress dashboard, go to **Plugins → Add New**, search for "Mango Latest Posts", then install and activate it.
2. Add the grid in one of these ways:
   * **Elementor:** search for "Mango Blog Grid" in the widget panel.
   * **Block editor:** add the "Mango Blog Grid" block.
   * **Anywhere else:** paste `[mango_latest_posts]` into a Shortcode block, text widget, or your page builder's shortcode element.

== Frequently Asked Questions ==

= Do I need Elementor? =

No. The Elementor widget is an extra. The block and the shortcode work on any WordPress site.

= Where are the options in Elementor? =

Select the widget and use the Elementor panel. **Content** has the layout and query options. **Style** has the card, title, excerpt and button options.

= My existing featured images are not cropped exactly to 1200 × 628. =

The grid always shows images at 1200 × 628 using CSS. For the sharpest crop on images uploaded before you installed the plugin, regenerate your thumbnails with a thumbnail regeneration plugin.

= Can I change the placeholder image? =

Yes. Use the `mango_lp_placeholder_image_url` filter:

`add_filter( 'mango_lp_placeholder_image_url', function () { return 'https://example.com/my-placeholder.jpg'; } );`

= Can I change the query? =

Yes. Use the `mango_lp_query_args` filter to change the `WP_Query` arguments.

== Screenshots ==

1. Gutenberg block: equal-height grid with a custom button color, and the block's color and button settings.
2. Gutenberg block: masonry layout with 4 columns, where images keep their original proportions.
3. Elementor widget: content settings (number of posts, layout, columns, excerpt length, button text).
4. Elementor widget: style options, here changing the button background color.

== Changelog ==

= 1.0.0 =
* First release: an Elementor widget, a Gutenberg block and a shortcode.
