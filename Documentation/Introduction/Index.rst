..  include:: /Includes.rst.txt

..  _introduction:

============
Introduction
============

..  _what-it-does:

What does it do?
================

The extension provides a middleware to enhance :file:`robots.txt` with located
XML sitemaps. This is done by appending sitemap configurations to the response
body when requesting :file:`robots.txt`. Injection of sitemap configurations can
be managed on a per-site basis and is disabled by default.

Under the hood, `EXT:sitemap_locator <https://extensions.typo3.org/extension/sitemap_locator>`__
is used to locate XML sitemaps.

..  _features:

Features
========

-   :ref:`Middleware <usage>` to inject sitemap configurations into :file:`robots.txt`
-   :ref:`Configuration <configuration>` option to manage sitemap injection on a
    per-site basis
-   Supports :ref:`static routes <static-route>` and :ref:`local files <local-file>`
-   Compatible with TYPO3 11.5 LTS, 12.4 LTS and 13.3 (see :ref:`version matrix <version-matrix>`)

..  _support:

Support
=======

There are several ways to get support for this extension:

* Slack: https://typo3.slack.com/archives/C060KATSL5V
* GitHub: https://github.com/eliashaeussler/typo3-sitemap-robots/issues

..  _license:

License
=======

This extension is licensed under
`GNU General Public License 2.0 (or later) <https://www.gnu.org/licenses/old-licenses/gpl-2.0.html>`_.
