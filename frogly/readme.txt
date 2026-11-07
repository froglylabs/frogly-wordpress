=== Frogly ===
Contributors: frogly
Tags: bot detection, security, bots, ai agents, decoy
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Catches automated intruders on your site and leads them away before they reach anything real. Paste one line and you are done.

== Description ==

Frogly is a friendly bot detection service for website owners. It adds a few decoys to your pages that real visitors never see. When an automated intruder takes one, it is led off your site into a maze of dead ends, and your Frogly console shows you the catch.

What it does for you:

* One line to install. Paste the install line from your Frogly console and save. No theme edits, no DNS changes.
* Nothing visible changes. Your visitors, your design and your speed stay exactly as they are.
* Never blocks anyone. Frogly only watches and redirects the intruders that take a decoy. If it ever cannot reach its servers, your site simply carries on.
* Tells you what it caught. Open the console at any time to see every automated visitor Frogly led away, in plain words.
* Learns from every intruder, so the maze gets better while the attackers get poorer.

Frogly works on any WordPress site, including WooCommerce stores. You need a free Frogly account to use it.

== External services ==

This plugin connects to Frogly, a hosted bot detection service run by Frogly ([https://frogly.ai](https://frogly.ai)). Without the service the plugin does nothing, so there is no local-only mode.

What is loaded and sent, and when:

* On every public page view, the visitor's browser loads the Frogly widget script from `https://cdn.frogly.ai` and fetches this site's public configuration from the same host. The request includes the site ID you saved in settings.
* When an automated visitor takes a decoy, the widget reports that event to `https://api.frogly.ai` so it can be shown in your console. The report contains the site ID, the decoy that was taken and the page it was on.
* Nothing is sent from your WordPress server. The plugin itself makes no outbound requests; it only prints the script tag.

The widget sets no cookies and stores nothing in the visitor's browser. The script is loaded with a pinned integrity hash, so the browser refuses to run it if it has been altered.

Frogly terms of service: [https://frogly.ai/terms](https://frogly.ai/terms)
Frogly privacy policy: [https://frogly.ai/privacy](https://frogly.ai/privacy)

== Installation ==

1. Install and activate the plugin.
2. Sign in at [frogly.ai/app](https://frogly.ai/app) (or sign up, it is free) and add your site.
3. Click Install in the console and copy the line it shows you.
4. In WordPress go to Settings > Frogly, paste the line, and click Turn Frogly on.
5. Back in your console, the site card should read "Frogly is watching yourdomain.com" within a minute or so. Click Poke it to watch a test catch arrive.

To turn Frogly off, clear the box in Settings > Frogly and save, or deactivate the plugin. Deleting the plugin removes the saved site ID.

== Frequently Asked Questions ==

= Will my visitors notice anything? =

No. The decoys are invisible to people and to screen readers, and the script loads in the background without delaying your page.

= Does Frogly block anyone? =

No. It never blocks, challenges or slows a visitor. It only follows automated intruders that take a decoy and leads them away from your real pages.

= Does it set cookies or need a consent banner? =

The widget sets no cookies and stores nothing in the visitor's browser. Many sites will not need to change their consent setup, but please check with whoever handles your privacy compliance.

= Will it slow my site down? =

The script is small and loads async from a CDN. In our own Lighthouse runs the performance score moved by a point or two at most.

= What if Frogly's servers are down? =

Your site carries on exactly as normal. The widget quietly does nothing if it cannot reach Frogly.

= Does this work with caching plugins? =

Yes. The script tag is ordinary page markup, so it is cached along with the page.

= Can I use the install line without this plugin? =

Yes. The plugin only places the line for you so you do not have to edit your theme. If you have already pasted the line into your theme or a header and footer plugin, use one or the other, not both.

= Where do I see what was caught? =

In your console at [frogly.ai/app](https://frogly.ai/app).

== Changelog ==

= 1.0.0 =
* First release. Settings page, one install line, pinned widget.
