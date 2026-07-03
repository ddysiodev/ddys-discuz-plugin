# DDYS Discuz! Plugin

English | [中文](README.zh-CN.md)

Official Discuz! X plugin for the [DDYS](https://ddys.io/) API.

- Repository: [ddysiodev/ddys-discuz-plugin](https://github.com/ddysiodev/ddys-discuz-plugin)
- Plugin identifier: `ddys_open`
- Install directory: `source/plugin/ddys_open`
- Target runtime: Discuz! X3.4 / X3.5, UTF-8 package

## Features

- Native Discuz! plugin directory and XML import file.
- Admin plugin variables for API Base URL, API Key, cache TTLs, frontend style, source links, navigation, hook widgets, and request form.
- Admin diagnostics page for connection testing, cache cleanup, current settings, and shortcode generation.
- Discuz post shortcode parsing through the plugin class.
- Optional widgets for forum index, forum display, and thread pages.
- Standalone plugin pages through `plugin.php?id=ddys_open:index`.
- Local JSON proxy through `plugin.php?id=ddys_open:api`.
- Server-side request form through `plugin.php?id=ddys_open:request`.
- Optional pretty frontend URLs with Apache, Nginx, and IIS rewrite rules.
- Database cache table and rate-limit table.
- DDYS icons copied from the main site icon set.

## Installation

1. Copy the `ddys_open` directory to `source/plugin/ddys_open`.
2. In Discuz AdminCP, import `discuz_plugin_ddys_open_SC_UTF8.xml`.
3. Install and enable the plugin.
4. Open the plugin variable settings and confirm API Base URL, cache TTLs, display options, and request form settings.
5. Open the plugin admin page to run the connection test and generate shortcodes.

## Shortcodes

```text
[ddys_movies type="movie" per_page="24"]
[ddys_latest type="movie" limit="12"]
[ddys_hot limit="10"]
[ddys_search]
[ddys_suggest q="interstellar"]
[ddys_calendar year="2026" month="7"]
[ddys_movie slug="this-tempting-madness"]
[ddys_sources slug="this-tempting-madness"]
[ddys_related slug="this-tempting-madness"]
[ddys_comments slug="this-tempting-madness" per_page="20"]
[ddys_collections per_page="10"]
[ddys_collection slug="best-sci-fi" per_page="12"]
[ddys_shares per_page="10"]
[ddys_share id="1"]
[ddys_requests per_page="10"]
[ddys_activities per_page="10"]
[ddys_user username="demo"]
[ddys_types]
[ddys_genres]
[ddys_regions]
[ddys_request_form]
```

## Public Pages

```text
plugin.php?id=ddys_open:index
plugin.php?id=ddys_open:index&view=hot
plugin.php?id=ddys_open:index&view=search
plugin.php?id=ddys_open:index&view=calendar
plugin.php?id=ddys_open:index&view=movie&slug=this-tempting-madness
plugin.php?id=ddys_open:index&view=collections
plugin.php?id=ddys_open:index&view=requests
```

After enabling the plugin variable `Enable Pretty URLs`, the frontend links default to:

```text
/ddys/
/ddys/hot
/ddys/search
/ddys/calendar
/ddys/movie/this-tempting-madness
/ddys/collections
/ddys/requests
```

The request form posts to:

```text
/ddys/request-submit
```

If you change the pretty URL base path from `ddys`, replace `ddys` in the rules below with your chosen path. If Discuz is installed in a subdirectory, place the rules in that site or subdirectory rewrite configuration.

### Apache

Add the rules to the Discuz root `.htaccess` and make sure `mod_rewrite` is enabled:

```apache
RewriteEngine On
RewriteRule ^ddys/?$ plugin.php?id=ddys_open:index [L,QSA]
RewriteRule ^ddys/(hot|search|calendar|collections|requests)/?$ plugin.php?id=ddys_open:index&view=$1 [L,QSA]
RewriteRule ^ddys/movie/([^/]+)/?$ plugin.php?id=ddys_open:index&view=movie&slug=$1 [L,QSA]
RewriteRule ^ddys/request-submit/?$ plugin.php?id=ddys_open:request [L,QSA]
```

### Nginx

Place the rules in the Discuz site `server` block before the generic PHP entry rules:

```nginx
rewrite ^/ddys/?$ /plugin.php?id=ddys_open:index last;
rewrite ^/ddys/(hot|search|calendar|collections|requests)/?$ /plugin.php?id=ddys_open:index&view=$1 last;
rewrite ^/ddys/movie/([^/]+)/?$ /plugin.php?id=ddys_open:index&view=movie&slug=$1 last;
rewrite ^/ddys/request-submit/?$ /plugin.php?id=ddys_open:request last;
```

### IIS

Add the rules under `<rewrite><rules>` in the Discuz root `web.config`:

```xml
<rule name="DDYS Discuz Latest" stopProcessing="true">
  <match url="^ddys/?$" />
  <action type="Rewrite" url="plugin.php?id=ddys_open:index" appendQueryString="true" />
</rule>
<rule name="DDYS Discuz Views" stopProcessing="true">
  <match url="^ddys/(hot|search|calendar|collections|requests)/?$" />
  <action type="Rewrite" url="plugin.php?id=ddys_open:index&amp;view={R:1}" appendQueryString="true" />
</rule>
<rule name="DDYS Discuz Movie" stopProcessing="true">
  <match url="^ddys/movie/([^/]+)/?$" />
  <action type="Rewrite" url="plugin.php?id=ddys_open:index&amp;view=movie&amp;slug={R:1}" appendQueryString="true" />
</rule>
<rule name="DDYS Discuz Request Submit" stopProcessing="true">
  <match url="^ddys/request-submit/?$" />
  <action type="Rewrite" url="plugin.php?id=ddys_open:request" appendQueryString="true" />
</rule>
```

## Local Checks

```bash
node tools/check.mjs
node --test tests/*.test.mjs
```

The checks verify plugin structure, XML fields, shortcode coverage, table setup, frontend assets, icon sizes, and accidental sensitive or temporary files.
