..  include:: /Includes.rst.txt

..  _site-configuration:

==================
Site configuration
==================

Within the site configuration of each site, it is possible to enable or disable
injection of located XML sitemaps. The appropriate setting is provided by the
following configuration:

..  confval:: sitemap_robots_inject (site)

    :Path: sitemap_robots_inject
    :type: bool

    Enable or disable injection of located XML sitemaps into the :file:`robots.txt`
    of the current site.

    ..  image:: ../Images/site-configuration.png
        :alt: Configuration of sitemap injection within the Sites module
