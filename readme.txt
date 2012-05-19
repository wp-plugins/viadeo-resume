=== Viadeo Resume ===
Contributors: Magetys
Donate link: http://www.magetys.com
Tags: viadeo, resume, cv, curriculum vitae
Requires at least: 3.0
Tested up to: 3.3.2
Stable tag: 1.0.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show your resume or your contacts' resumes generated with professional social network Viadeo profiles (http://www.viadeo.com)

== Description ==

[Viadeo](http://www.viadeo.com) is the second professional social networks around the world with a total membership base of over 45 million professionals. With this plugin, you can show your resume or your contacts' resumes generated with Viadeo profiles. This plugin use the new [Viadeo API](http://dev.viadeo.com).

When you have set your personal Viadeo Access Token on the administration page of Viadeo Resume plugin. You can insert your resume on every page or post with the short code **[viadeo-resume]**.

You can also insert the resume of your Viadeo contacts with the short code **[viadeo-resume profile="*nickname*"]**.
Usually Viadeo nicknames are formatted like this : *firstname.lastname*.
When you write a post, you can generate the short code if you click on "add a Viadeo Resume" button.

== Installation ==

For an automatic installation through WordPress:

1. Go to the 'Add New' plugins screen in your WordPress admin area
2. Search for 'Viadeo Resume'
3. Click 'Install Now' and activate the plugin

For a manual installation via FTP:

1. Upload the viadeo_resume folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' screen in your WordPress admin area

To upload the plugin through WordPress, instead of FTP:

1. Upload the downloaded zip file on the 'Add New' plugins screen (see the 'Upload' tab) in your WordPress admin area and activate.

On the Viadeo Resume administration plugin page, you need to set your personal Viadeo Access Token. For getting it, follow this step :

1. Connect to your Viadeo account on the web application at [http://www.viadeo.com](http://www.viadeo.com)

2. Go to this page : [http://dev.viadeo.com/documentation/authentication/request-an-api-key/](http://dev.viadeo.com/documentation/authentication/request-an-api-key/)

3. Complete all fields of the form to get your Access Token (informations that you set aren’t important for what we want)
   - Set an application name
   - Set an application description
   - Upload an application logo
   - Set an application URL
   - Accept the Viadeo API terms of usage
   - Save information

Now, you can get your personal Access Token

== Screenshots ==

1. Plugin configuration page
2. Just write [viadeo-resume] on a post
3. Generated resume sample

== Frequently Asked Questions ==

= There are some dependencies to install before using this plugin ? =

Yes, You'll need to install cURL for PHP, to install it on a Debian server :
> sudo apt-get install curl libcurl3 libcurl3-dev php5-curl

= I have some questions (need help, have an idea), how can I contact you? =

Feel free to contact us through our [blog](http://www.magetys.com).

== Changelog ==

= 1.0.4 =

* Fix a bug with SSL on some versions of cURL

= 1.0.3 =

* Notify user if cURL is running or not on plugin configuration page

= 1.0 =

* First public release
