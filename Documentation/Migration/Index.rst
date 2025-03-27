..  include:: /Includes.rst.txt

..  _migration:

=========
Migration
=========

This page lists all notable changes and required migrations when
upgrading to a new major version of this extension.

..  _version-0.2.0:

Version 0.2.0
=============

Site configuration parameter type changed
-----------------------------------------

-   Type of site configuration parameter :ref:`sitemap_robots_inject <confval-sitemap-robots-inject-site>`
    changed from `check` to `select`.
-   The following migration applies:

    +   Value `true`/`1` should be migrated to `default` or `all`.
    +   Value `false`/`0` should be migrated to an empty string.

-   Use the dedicated upgrade wizard to automatically perform the required migrations.
