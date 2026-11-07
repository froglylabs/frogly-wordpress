# Frogly for WordPress

The official WordPress plugin for [Frogly](https://frogly.ai), a friendly bot detection service for website owners. It places a few decoys that real visitors never see; automated intruders that take one are led away from the site, and the owner is told. It never blocks anyone.

The plugin does one job: it prints the pinned Frogly install line on every public page so you do not have to edit your theme.

## Install

1. Download the latest `frogly.zip` from [Releases](../../releases) and upload it under Plugins > Add New > Upload Plugin, then activate it.
2. Sign in at [frogly.ai/app](https://frogly.ai/app) (or sign up, it is free) and add your site.
3. Click Install in the console and copy the line it shows you.
4. In WordPress go to Settings > Frogly, paste the line, and click Turn Frogly on.

To turn it off, clear the box and save, or deactivate the plugin. Deleting the plugin removes the saved site ID.

## What it does, exactly

- Stores one option, `frogly_site_id`, which is the site ID from your install line. Nothing else is stored.
- Enqueues `https://cdn.frogly.ai/w/core.v5.js` on public pages with the site ID, a pinned `integrity` hash, `crossorigin="anonymous"` and `async`.
- Makes no outbound requests from your server. Only the visitor's browser talks to Frogly.
- Prints nothing in wp-admin, the login screen or the customizer preview.

Each widget version is frozen once its hash is pinned. A new widget version ships as a new plugin version.

## Requirements

WordPress 5.8 or newer, PHP 7.4 or newer.

## Development

The plugin lives in `frogly/`. To build a zip for upload:

```
cd frogly-wordpress
zip -r frogly.zip frogly -x "*.DS_Store"
```

Passes [Plugin Check](https://wordpress.org/plugins/plugin-check/) with no errors or warnings.

## License

GPLv2 or later. See `frogly/LICENSE.txt`.

Questions: support@frogly.ai
