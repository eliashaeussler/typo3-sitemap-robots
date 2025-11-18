..  include:: /Includes.rst.txt

..  image:: https://img.shields.io/coverallsCoverage/github/eliashaeussler/typo3-sitemap-robots?logo=coveralls
    :target: https://coveralls.io/github/eliashaeussler/typo3-sitemap-robots

..  image:: https://img.shields.io/github/actions/workflow/status/eliashaeussler/typo3-sitemap-robots/ci.yaml?label=CI&logo=github
    :target: https://github.com/eliashaeussler/typo3-sitemap-robots/actions/workflows/ci.yaml

..  _contributing:

============
Contributing
============

Thanks for considering contributing to this extension! Since it is
an open source product, its successful further development depends
largely on improving and optimizing it together.

The development of this extension follows the official
`TYPO3 coding standards <https://github.com/TYPO3/coding-standards>`__.
To ensure the stability and cleanliness of the code, various code
quality tools are used and most components are covered with test
cases. In addition, we use `DDEV <https://ddev.readthedocs.io/en/stable/>`__
for local development. Make sure to set it up as described below. For
continuous integration, we use GitHub Actions.

..  _create-an-issue-first:

Create an issue first
=====================

Before you start working on the extension, please create an issue on
GitHub: https://github.com/eliashaeussler/typo3-sitemap-robots/issues

Also, please check if there is already an issue on the topic you want
to address.

..  _contribution-workflow:

Contribution workflow
=====================

..  note::

    This extension follows `Semantic Versioning <https://semver.org/>`__.

..  _preparation:

Preparation
-----------

Clone the repository first:

..  code-block:: bash

    git clone https://github.com/eliashaeussler/typo3-sitemap-robots.git
    cd typo3-sitemap-robots

Now start DDEV:

..  code-block:: bash

    ddev start

Next, install all dependencies:

..  code-block:: bash

    ddev composer install

You can access the DDEV site at https://typo3-ext-sitemap-robots.ddev.site/.

..  _analyze-code:

Analyze code
------------

..  _check-code-quality:

..  code-block:: bash

    # All analyzers
    ddev cgl analyze

    # Specific analyzers
    ddev cgl analyze:dependencies

Check code quality
------------------

..  code-block:: bash

    # All linters
    ddev cgl lint

    # Specific linters
    ddev cgl lint:composer
    ddev cgl lint:editorconfig
    ddev cgl lint:php

    # Fix all CGL issues
    ddev cgl fix

    # Fix specific CGL issues
    ddev cgl fix:composer
    ddev cgl fix:editorconfig
    ddev cgl fix:php

    # All static code analyzers
    ddev cgl sca

    # Specific static code analyzers
    ddev cgl sca:php

..  _run-tests:

Run tests
---------

..  code-block:: bash

    # All tests
    ddev test

    # Specific tests
    ddev test functional
    ddev test unit

    # All tests with code coverage
    ddev test coverage

    # Specific tests with code coverage
    ddev test coverage:functional
    ddev test coverage:unit

    # Merge code coverage of all test suites
    ddev test coverage:merge

Code coverage reports are written to :file:`.Build/coverage`. You can
open the last merged HTML report like follows:

..  code-block:: bash

    open .Build/coverage/html/_merged/index.html

..  _build-documentation:

Build documentation
-------------------

..  code-block:: bash

    # Rebuild and open documentation
    composer docs

    # Build documentation (from cache)
    composer docs:build

    # Open rendered documentation
    composer docs:open

The built docs will be stored in :file:`.Build/docs`.

..  _pull-request:

Pull Request
------------

Once you have finished your work, please **submit a pull request** and describe
what you've done: https://github.com/eliashaeussler/typo3-sitemap-robots/pulls

Ideally, your PR references an issue describing the problem
you're trying to solve. All described code quality tools are automatically
executed on each pull request for all currently supported PHP versions and TYPO3
versions.
