=== Connections Business Directory Face Detect ===
Contributors: shazahm1@hotmail.com
Donate link: https://connections-pro.com/
Tags: addresses, address book, addressbook, bio, bios, biographies, business, businesses, business directory, business-directory, business directory plugin, directory plugin, directory widget, church, contact, contacts, connect, connections, directory, directories, hcalendar, hcard, ical, icalendar, image, images, list, lists, listings, member directory, members directory, members directories, microformat, microformats, page, pages, people, profile, profiles, post, posts, plugin, shortcode, staff, user, users, vcard, wordpress business directory, wordpress directory, wordpress directory plugin, wordpress business directory, face detect
Requires at least: 5.1
Tested up to: 5.8.1
Requires PHP: 5.6.20
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extension for the Connections Business Directory applies face detection when cropping an image.

== Description ==

This is an extension plugin for the [Connections Business Directory Plugin](http://wordpress.org/plugins/connections/) please be sure to install and active it before adding this plugin.

This extension plugin for started out just as a little experiment. I wanted to toy with face detection in images. I found this great little PHP library by [Karthik Tharavaad](https://github.com/mauricesvay/php-facedetection) that did just that. It works well for the most part but it is way too slow to be used for doing face detection on the fly. Then I remembered that [TechCruch](http://techcrunch.com/) released a library called [WP Asynchronous Tasks](https://github.com/techcrunch/wp-async-task). What this neat little library does is allow intensive processes, you know, such as face detection, be processed asynchronously. After a short while I had the face detection running in background tasks. These background task could probably be more optimized but this is probably good enough for a first release.

Read this carefully...

Limitations:
1. The first time an image in Connections is accessed, it will be processed in the background and until the image is finished processing, the image will be scaled and cropped from the center origin which is the default behavior.
2. The face detection library can only detect a single face. So I suggest this extension only be used for people directories.
3. The images should be clean and bright with the person facing as straight as possible for the best results.
4. This very well could slow down the server for a period of time if there are a large number of images in the process queue.
5. And ... use at your own risk.

[Checkout the screenshots.](http://connections-pro.com/add-on/face-detect/)

Here are other great extensions that enhance your experience with the Connections Business Directory:

Utility

* [Toolbar](http://wordpress.org/plugins/connections-toolbar/)
* [Login](http://wordpress.org/plugins/connections-business-directory-login/)

Custom Fields

* [Business Hours](http://wordpress.org/plugins/connections-business-directory-hours/)
* [Income Level](http://wordpress.org/plugins/connections-business-directory-income-levels/)
* [Education Level](http://wordpress.org/plugins/connections-business-directory-education-levels/)
* [Languages](http://wordpress.org/plugins/connections-business-directory-languages/)

== Installation ==

= Using the WordPress Plugin Search =

1. Navigate to the `Add New` sub-page under the Plugins admin page.
2. Search for `connections business directory face detect`.
3. The plugin should be listed first in the search results.
4. Click the `Install Now` link.
5. Lastly click the `Activate Plugin` link to activate the plugin.

= Uploading in WordPress Admin =

1. [Download the plugin zip file](http://wordpress.org/plugins/connections-business-directory-face-detect/) and save it to your computer.
2. Navigate to the `Add New` sub-page under the Plugins admin page.
3. Click the `Upload` link.
4. Select Connections Business Directory Face Detect zip file from where you saved the zip file on your computer.
5. Click the `Install Now` button.
6. Lastly click the `Activate Plugin` link to activate the plugin.

= Using FTP =

1. [Download the plugin zip file](http://wordpress.org/plugins/connections-business-directory-face-detect/) and save it to your computer.
2. Extract the Connections Business Directory Face Detect zip file.
3. Create a new directory named `connections-business-directory-face-detect` directory in the `../wp-content/plugins/` directory.
4. Upload the files from the folder extracted in Step 2.
4. Activate the plugin on the Plugins admin page.

== Frequently Asked Questions ==

None yet....

== Screenshots ==

[Screenshots can be found here.](http://connections-pro.com/add-on/face-detect/)

1. Examples showing Face Detection results on business portraits.

Photos by [stockimages](http://www.freedigitalphotos.net/images/view_photog.php?photogid=4096) via [freedigitalphotos.net](http://www.freedigitalphotos.net/)

== Changelog ==

= 1.1 09/23/2021 =
* NEW: Add `FaceDetector::imageType()` helper function.
* NEW: Add support for GIFs and PNGs.
* TWEAK: Remove use of create function.
* BUG: Ensure `$this->canvas` is not NULL before processing else throw exception.
* OTHER: Add screenshot to readme.txt.
* OTHER: Add photo credit to readme.txt.
* OTHER: Correct misspellings.
* OTHER: Update copyright year.
* OTHER: Update PHP requires to 5.6.20.
* OTHER: Update URLs from `http` to `https`.
* OTHER: Update requires and tested to WordPress version in readme.txt.
* DEV: Update .gitignore to exclude phpStorm.
* DEV: phpDoc update.
* DEV: Line separators.

= 1.0 09/16/2014 =
* Initial release.

== Upgrade Notice ==

= 1.0 =
Initial release.

= 1.1 =
It is recommended to back up before updating. Requires WordPress >= 5.1 and PHP >= 5.6.20 PHP version >= 7.2 recommended.

