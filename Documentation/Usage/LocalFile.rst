..  include:: /Includes.rst.txt

..  _local-file:

==========
Local file
==========

It is also possible to provide the contents of your :file:`robots.txt` in a local
:file:`robots.txt` file in the web root of your TYPO3 installation. If configured,
the extension tries to inject XML sitemaps during the request process the same way
as in the usage of :ref:`static routes <static-route>`.

As with :ref:`static routes <static-route>`, the injection of XML sitemaps must be
explicitly enabled in the appropriate :ref:`site configuration <site-configuration>`.

..  important::

    Your web server must be properly configured to route requests to an existing
    :file:`robots.txt` to TYPO3's entrypoint at :file:`index.php`. Otherwise, the
    underlying middleware will never be called, thus being unable to inject XML
    sitemaps during the request process.

    Read more in the :ref:`web server configuration <web-server-configuration>`
    section below.

..  _web-server-configuration:

Web server configuration
========================

In order to route requests to an existing :file:`robots.txt` file to TYPO3's main
entrypoint, your web server configuration must be modified. This depends on which
server type you're actually using.

..  _apache:

Apache
------

Add the following to the :file:`.htaccess` in the web root of your TYPO3 installation:

..  code-block:: apache

    RewriteEngine on
    RewriteRule ^robots.txt /index.php [L]

..  _nginx:

Nginx
-----

Add the following to the configuration file of your Nginx server:

..  code-block:: nginx

    location = /robots.txt {
        rewrite ^ /index.php;
    }
