At a first look, it seem just another Cake powered CMS, but it's a lot more. In fact, the CMS functionality is achieved by a core extension, which can be easily disabled if not needed.

This project is ment to be used as a skeleton application that can be easily extended to fit needs of Web 2.0 applications.

Examples of applications [StarLight][site] is well fitted for: custom websites, (micro)publishing systems, facebook apps, google apps, productivity and intranet, mockups and prototype applications or any other Web 2.0 projects.

Organized in the form of a cake application (*/app*), it can be extended using specialy crafted cake plugins (*/app/extensions*).


## Sl core features

* Written in **PHP5**, using cake convetions and with lots of phpdoc comments;
* Auto installer and database schema, both present;
* Optimized cookie and session handling (no DB support for now) accessible during bootstrap;
* Extended configuration class with runtime persistence, inheritance, context-awareness and i10n support;
* Themes, plugins, autoloaded configurations and hooks - all supported;
* Pheme - a simple template engine with support for runtime local/global skinning and i10n;
* With jQuery out-of-box (JS forms validation is just one of the features);
* Automagic content delivery network (CDN) support (both StarLight and 3rd party) for JS libraries;
* 100% compatible with native cake plugins (any 3rd party plugin should work and can be easily converted to an extension, see DebugKit as an example).


## Core extensions

* **Api** - Bridge to 3rd party web services (Facebook, GMail through SwiftMailer, hqSms, a.o.)
* **Auth** - Security stuff, ACL and user accounts management;
* **Cms** - Basic content management: articles organized in a tree fashion (nodes), tags (that can be grouped), images, uploads, contact forms, navigation and blocks;
* **DebugKit** - 3rd party debugging plugin (automagically activates and detects the presence of Interactive plugin).


## If you decide to use Sl, a standard aproach would be:

1. Download or clone Sl's [github][] repo
2. Rename */app/config/site.sample.php* to */app/config/site.php* and set your evironment settings
3. Download any needed 3rd party extensions and remove/rename unneeded ones (see */app/extensions*)
4. Make sure you have write permissions for the following folders (recursive):
	* /app/tmp
	* /app/webroot/files
5. Point your browser to the URL pointing to your newly deployed Sl to start the one-time setup process

If developing your own extensions, make sure you set **debug** setting to **1** or **2** in */app/config/core.php*. 
The *DebugKit* extension, *Interactive* plugin and te *Sl::krumo()* method, all are here to address your debugging needs.


## Links

* [Official site][site]
* [github][]

[site]: http://starlightcms.info
[github]: http://github.com/z7/StarLight

*[Sl]: StarLight




# CakePHP README

CakePHP is a rapid development framework for PHP which uses commonly known design patterns like Active Record, Association Data Mapping, Front Controller and MVC. Our primary goal is to provide a structured framework that enables PHP users at all levels to rapidly develop robust web applications, without any loss to flexibility.

* [The Cake Software Foundation - promoting development related to CakePHP](http://cakefoundation.org/)
* [CakePHP - the rapid development PHP framework](http://www.cakephp.org)
* [Cookbook - user documentation for learning about CakePHP](http://book.cakephp.org)
* [API - quick reference to CakePHP](http://api.cakephp.org)
* [The Bakery - everything CakePHP](http://bakery.cakephp.org)
* [The Show - live and archived podcasts about CakePHP and more](http://live.cakephp.org)
* [CakePHP Google Group - community mailing list and forum](http://groups.google.com/group/cake-php)
* [#cakephp on irc.freenode.net - chat with CakePHP developers](irc://irc.freenode.net/cakephp)
* [CakeForge - open development for CakePHP](http://cakeforge.org)
* [CakePHP gear](http://www.cafepress.com/cakefoundation)
* [Recommended Reading](http://astore.amazon.com/cakesoftwaref-20/)
