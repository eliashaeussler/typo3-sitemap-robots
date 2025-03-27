..  include:: /Includes.rst.txt

..  _configuration:

=============
Configuration
=============

By default, no XML sitemaps are injected into :file:`robots.txt`. You
need to explicitly enable this feature in site configuration of each
site you want it to use.

..  _site-configuration:

Site configuration
==================

Within the site configuration, it is possible to enable or disable
injection of located XML sitemaps. The appropriate setting is provided
by the following configuration:

..  confval:: sitemap_robots_inject (site)
    :Path: sitemap_robots_inject
    :type: string (empty, :php:`default` or :php:`all`)

    Configure injection of located XML sitemaps into the :file:`robots.txt`
    of the current site.

    ..  versionadded:: 0.2.0

        `Feature: #65 - Add option to inject sitemap language versions into robots.txt <https://github.com/eliashaeussler/typo3-sitemap-robots/pull/65>`__

        The configuration changed from a checkbox to a single select type
        which allows to switch between injection of default language only
        (`default`) or all available site languages (`all`).

        Previous configuration values can be migrated using a dedicated
        upgrade wizard.

    ..  image:: ../Images/site-configuration.png
        :alt: Configuration of sitemap injection within the Sites module
