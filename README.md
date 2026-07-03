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

## Local Checks

```bash
node tools/check.mjs
node --test tests/*.test.mjs
```

The checks verify plugin structure, XML fields, shortcode coverage, table setup, frontend assets, icon sizes, and accidental sensitive or temporary files.

