..  include:: /Includes.rst.txt

..  _usage:

=====
Usage
=====

At the moment, the extension provides two possible ways to inject located
XML sitemaps in a requested :file:`robots.txt` file. This section describes
how to properly use each of them and what steps are necessary to enable all
provided features.

..  toctree::
    :maxdepth: 1

    StaticRoute
    LocalFile

..  _usage-example:

Example
=======

The following example describes what processes happen when a :file:`robots.txt`
file is requested and XML sitemaps get injected.

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
