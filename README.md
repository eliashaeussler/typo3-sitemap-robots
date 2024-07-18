<div align="center">

![Extension icon](Resources/Public/Icons/Extension.svg)

# TYPO3 extension `sitemap_robots`

[![Coverage](https://img.shields.io/codecov/c/github/eliashaeussler/typo3-sitemap-robots?logo=codecov&token=0lBkWpVYlM)](https://codecov.io/gh/eliashaeussler/typo3-sitemap-robots)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/eliashaeussler/typo3-sitemap-robots?logo=codeclimate)](https://codeclimate.com/github/eliashaeussler/typo3-sitemap-robots/maintainability)
[![CGL](https://img.shields.io/github/actions/workflow/status/eliashaeussler/typo3-sitemap-robots/cgl.yaml?label=cgl&logo=github)](https://github.com/eliashaeussler/typo3-sitemap-robots/actions/workflows/cgl.yaml)
[![Tests](https://img.shields.io/github/actions/workflow/status/eliashaeussler/typo3-sitemap-robots/tests.yaml?label=tests&logo=github)](https://github.com/eliashaeussler/typo3-sitemap-robots/actions/workflows/tests.yaml)
[![Supported TYPO3 versions](https://typo3-badges.dev/badge/sitemap_robots/typo3/shields.svg)](https://extensions.typo3.org/extension/sitemap_robots)
[![Slack](https://img.shields.io/badge/slack-%23ext--sitemap__robots-4a154b?logo=slack)](https://typo3.slack.com/archives/C06532567LM)

</div>

An extension for TYPO3 CMS that enhances `robots.txt` from site configuration with
located XML sitemaps. Injection of sitemap configuration can be managed on a per-site
basis and is done by an additional frontend middleware. The extension supports
static routes as well as injection into local files.

## 🚀 Features

* Middleware to inject sitemap configurations into `robots.txt`
* Manage sitemap injection on a per-site basis using site configuration
* Supports static routes and local files
* Compatible with TYPO3 11.5 LTS, 12.4 LTS and 13.2

## 🔥 Installation

### Composer

[![Packagist](https://img.shields.io/packagist/v/eliashaeussler/typo3-sitemap-robots?label=version&logo=packagist)](https://packagist.org/packages/eliashaeussler/typo3-sitemap-robots)
[![Packagist Downloads](https://img.shields.io/packagist/dt/eliashaeussler/typo3-sitemap-robots?color=brightgreen)](https://packagist.org/packages/eliashaeussler/typo3-sitemap-robots)

```bash
composer require eliashaeussler/typo3-sitemap-robots
```

### TER

[![TER version](https://typo3-badges.dev/badge/sitemap_robots/version/shields.svg)](https://extensions.typo3.org/extension/sitemap_robots)
[![TER downloads](https://typo3-badges.dev/badge/sitemap_robots/downloads/shields.svg)](https://extensions.typo3.org/extension/sitemap_robots)

Download the zip file from
[TYPO3 extension repository (TER)](https://extensions.typo3.org/extension/sitemap_robots).

## 📙 Documentation

Please have a look at the
[official extension documentation](https://docs.typo3.org/p/eliashaeussler/typo3-sitemap-robots/main/en-us/).

## ⭐ License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE.md).
