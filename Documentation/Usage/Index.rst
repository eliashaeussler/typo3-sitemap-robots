..  include:: /Includes.rst.txt

..  _usage:

=====
Usage
=====

You can use this extension to inject XML sitemaps into :file:`robots.txt` in case
your :file:`robots.txt` is configured as :ref:`static route <t3coreapi:sitehandling-staticRoutes>`
in your site configuration. Once injection of XML sitemaps is enabled for a site,
a custom middleware reacts on requests to :file:`robots.txt` and tries to locate
XML sitemaps for the current site, which will then be injected into the response body.

..  _usage-example:

Example
=======

..  rst-class:: bignums

1.  Base URL

    The base URL of your site is `https://www.example.com/`.

2.  XML sitemaps

    Currently, the following XML sitemaps are available for your site:

    -   `https://www.example.com/sitemap.xml`
    -   `https://www.example.com/sitemap-news.xml`

3.  :file:`robots.txt`

    Given the following content of your :file:`robots.txt` route:

    ::

        User-agent: *
        Allow: /
        Disallow: /forbidden/

4.  Sitemap injection

    If you have :ref:`injection of XML sitemaps <site-configuration>` enabled in your site
    configuration and each configured XML sitemap actually exists, the :file:`robots.txt`
    will be enhanced like follows:

    ::

        User-agent: *
        Allow: /
        Disallow: /forbidden/

        Sitemap: https://www.example.com/sitemap.xml
        Sitemap: https://www.example.com/sitemap-news.xml

    ..  note::

        If any located XML sitemap does not exist or is inaccessible, it is not injected
        into the response body of your :file:`robots.txt`. The validation of XML sitemaps
        happens in :php:meth:`EliasHaeussler\\Typo3SitemapLocator\\Sitemap\\SitemapLocator::isValidSitemap`.

..  seealso::

    Read more about how to configure your XML sitemaps in the documentation of
    :ref:`EXT:sitemap_locator <sitemap-locator:configuration>`.
