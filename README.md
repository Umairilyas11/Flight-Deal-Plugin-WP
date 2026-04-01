# ✈️ GoFly Flight Deals — WordPress Plugin

A WordPress plugin that adds a **Flight Deals** custom post type with fully featured Elementor widgets, designed for the GoFly travel theme. Manage flight deals from the WordPress admin and display them on the frontend with beautiful, responsive layouts.

---

## Features

- **Custom Post Type** — `flight_deal` with full admin management
- **3 Elementor Widgets** — Deals Grid, Deals Carousel, Search Bar
- **18 Meta Fields** — origin/destination, pricing, airline logo, dates, expiry, booking URL and more
- **3 Custom Taxonomies** — Airline, Destination Region, Trip Type
- **Deal Expiry System** — automatically hides deals past their expiry date
- **Countdown Timer** — live JS countdown on deal cards and single pages
- **Search & Filter** — search bar redirects to archive with URL param filtering
- **Related Deals** — auto-displayed on single deal pages by destination
- **GA4 Integration** — optional Book Now click event tracking
- **Theme Template Override** — drop templates in your theme to customise layouts
- **WPML / Polylang Ready** — string registration support
- **Clean Uninstall** — removes all posts, meta, taxonomies and options on delete

---

## Requirements

| Requirement       | Version                                   |
| ----------------- | ----------------------------------------- |
| WordPress         | 5.8 or higher                             |
| PHP               | 7.4 or higher                             |
| Elementor         | 3.0 or higher (free version is enough)    |
| Pretty Permalinks | Must be enabled (any option except Plain) |

---

## Installation

### From GitHub

1. Click **Code → Download ZIP** on this repository
2. In your WordPress admin go to **Plugins → Add New → Upload Plugin**
3. Upload the ZIP and click **Install Now**
4. Click **Activate Plugin**
5. Go to **Settings → Permalinks** and click **Save Changes** to flush rewrite rules

### Manual (FTP)

1. Clone or download this repository
2. Upload the `gofly-flight-deals` folder to `/wp-content/plugins/`
3. Activate the plugin from the **Plugins** menu in WordPress
4. Go to **Settings → Permalinks → Save Changes**

---

## Getting Started

### 1. Add Your First Deal

Go to **Flight Deals → Add New Deal** in the WordPress admin sidebar. Fill in:

- **Route** — Origin city + IATA code (e.g. Colombo / CMB) and Destination city + IATA code (e.g. Dubai / DXB)
- **Airline** — Name and logo image
- **Pricing** — Deal price, currency, and optional original price for strikethrough display
- **Travel details** — Class, stops, departure/return dates, duration
- **Deal badge** — Short label shown on the card (e.g. "Hot Deal", "Limited Offer")
- **Expiry date** — Deal auto-hides after this date
- **Booking URL** — Your affiliate or direct booking link
- **Featured** — Check to highlight the deal on the frontend

### 2. View the Archive Page

After publishing at least one deal, visit:

```
yoursite.com/flight-deals/
```

This is the auto-generated archive page with a search bar, deal grid, and pagination. No page creation needed.

### 3. Add Elementor Widgets

Open any page in the **Elementor editor**. In the widget search box type `flight` or scroll to the **GoFly Flight Deals** category. Three widgets are available:

| Widget                  | Description                                  |
| ----------------------- | -------------------------------------------- |
| Flight Deals Grid       | Responsive CSS grid of deal cards            |
| Flight Deals Carousel   | Swiper.js powered sliding carousel           |
| Flight Deals Search Bar | Search/filter form that redirects to results |

---

## Plugin Settings

Go to **Flight Deals → Settings** to configure:

| Setting                     | Description                                         |
| --------------------------- | --------------------------------------------------- |
| Default Currency            | Fallback currency if not set per deal               |
| Currency Symbol Position    | Show symbol before or after the price               |
| "Book Now" Button Text      | Customise the CTA button label                      |
| Hide Expired Deals Globally | Auto-hide deals past expiry date                    |
| Deals Archive Slug          | Change the URL (default: `flight-deals`)            |
| GA4 Book Now Event          | Push a `book_now_click` event to Google Analytics 4 |

> After changing the archive slug, go to **Settings → Permalinks → Save Changes** to apply it.

---

## Elementor Widget Options

### Flight Deals Grid

**Query tab**

- Number of deals to show
- Filter by Airline, Destination, Trip Type taxonomy
- Show featured deals only
- Hide expired deals (default: on)
- Order by Date / Price Low–High / Price High–Low

**Layout tab**

- Columns (2 / 3 / 4)
- Show/hide: deal badge, airline logo, original strikethrough price, stops info
- Card border radius and box shadow toggle

**Style tab**

- Card background, price colour, badge colours, button colours
- Typography controls for route title and price

### Flight Deals Carousel

All Grid options plus:

- Autoplay with configurable speed
- Loop toggle
- Slides to show / scroll
- Navigation arrows and pagination dots toggle

### Flight Deals Search Bar

- Custom placeholder text for origin and destination fields
- Show/hide date, airline, and class filters
- Button text and colour
- Results page selector — choose which page to redirect to on submit

---

## File Structure

```
gofly-flight-deals/
├── gofly-flight-deals.php          # Main plugin file
├── uninstall.php                   # Cleanup on uninstall
├── includes/
│   ├── class-post-type.php         # CPT + taxonomy registration
│   ├── class-meta-boxes.php        # Admin meta boxes + save logic
│   ├── class-admin-columns.php     # Custom admin list columns
│   ├── class-helpers.php           # Shared utility functions
│   └── class-settings.php          # Plugin settings page
├── elementor/
│   ├── class-elementor-manager.php # Registers widgets with Elementor
│   └── widgets/
│       ├── class-widget-deals-grid.php
│       ├── class-widget-deals-carousel.php
│       └── class-widget-deal-search.php
├── templates/
│   ├── single-flight-deal.php      # Single deal page template
│   ├── archive-flight-deal.php     # Archive/listing page template
│   └── partials/
│       ├── deal-card.php           # Reusable deal card
│       └── deal-search-bar.php     # Search bar partial
├── assets/
│   ├── css/
│   │   ├── frontend.css
│   │   └── admin.css
│   └── js/
│       ├── frontend.js
│       └── admin.js
└── languages/
    └── gofly-flight-deals.pot
```

---

## Template Overrides

You can override any plugin template from your theme without modifying the plugin files. Create a `gofly-flight-deals` folder in your theme and mirror the template path:

```
your-theme/
└── gofly-flight-deals/
    ├── single-flight-deal.php
    ├── archive-flight-deal.php
    └── partials/
        ├── deal-card.php
        └── deal-search-bar.php
```

The plugin checks your theme folder first before loading its own templates.

---

## URL Filter Parameters

The archive page and Search Bar widget support these URL query parameters for filtering:

| Parameter     | Example             | Description                             |
| ------------- | ------------------- | --------------------------------------- |
| `origin`      | `?origin=CMB`       | Filter by origin city or IATA code      |
| `destination` | `?destination=DXB`  | Filter by destination city or IATA code |
| `date`        | `?date=2025-03-15`  | Filter by departure date                |
| `airline`     | `?airline=emirates` | Filter by airline taxonomy slug         |
| `class`       | `?class=economy`    | Filter by travel class                  |

Example combined URL:

```
yoursite.com/flight-deals/?origin=CMB&destination=DXB&class=business
```

---

## Hooks & Filters

The plugin is developer-friendly. Commonly used hooks:

```php
// Modify the deal card query args before it runs
add_filter( 'gfd_query_args', function( $args ) {
    // e.g. only show deals with a price below 500
    $args['meta_query'][] = array(
        'key'     => '_gfd_price',
        'value'   => 500,
        'compare' => '<',
        'type'    => 'NUMERIC',
    );
    return $args;
} );
```

---

## Frequently Asked Questions

**The archive page returns 404.**
Go to **Settings → Permalinks** and click **Save Changes**. Make sure you are not using the "Plain" permalink structure.

**Elementor widgets don't appear.**
Make sure Elementor is active, then open a page in the Elementor editor and search for "flight" in the widget panel. If still missing, deactivate and reactivate the plugin, then flush permalinks.

**Deals are not showing on the frontend.**
Check that at least one deal is published. Also verify the "Hide Expired Deals" setting and that the deal's expiry date has not passed.

**How do I change the archive URL from `/flight-deals/` to something else?**
Go to **Flight Deals → Settings**, update the "Deals Archive Slug" field, save, then go to **Settings → Permalinks → Save Changes**.

**Is Elementor Pro required?**
No. The widgets work with the free version of Elementor.

---

## Changelog

### 1.0.1

- Fixed activation fatal error caused by missing helper function dependency in activation hook
- Replaced `sanitize_url()` with `esc_url_raw()` for WordPress < 5.9 compatibility
- Added support for both Elementor 3.5+ and legacy widget registration APIs
- Moved includes to load immediately to prevent hook timing issues
- Added double flush rewrite rules via transient to catch edge cases

### 1.0.0

- Initial release

---

## License

This plugin is licensed under the [GPL-2.0+](https://www.gnu.org/licenses/gpl-2.0.txt) license.

---

## Credits

Built for the **GoFly** WordPress travel theme. Carousel powered by [Swiper.js](https://swiperjs.com/).
