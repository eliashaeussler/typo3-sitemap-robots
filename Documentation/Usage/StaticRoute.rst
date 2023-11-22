..  include:: /Includes.rst.txt

..  _static-route:

============
Static route
============

The contents of your :file:`robots.txt` can be configured as
:ref:`static route <t3coreapi:sitehandling-staticRoutes>` in your site configuration.
If configured, the extension tries to inject XML sitemaps during the request process. A
custom middleware reacts on requests to :file:`robots.txt` and tries to locate
XML sitemaps for the current site, which will then be injected into the response body.

In order to use this feature, injection of XML sitemaps must be explicitly enabled for
each site in the appropriate :ref:`site configuration <site-configuration>`.
