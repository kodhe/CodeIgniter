###################
What is CodeIgniter
###################

CodeIgniter is an Application Development Framework - a toolkit - for people
who build web sites using PHP. Its goal is to enable you to develop projects
much faster than you could if you were writing code from scratch, by providing
a rich set of libraries for commonly needed tasks, as well as a simple
interface and logical structure to access these libraries. CodeIgniter lets
you creatively focus on your project by minimizing the amount of code needed
for a given task.

*************
CodeIgniter 3
*************

This repository is for the legacy version, CodeIgniter 3.
`CodeIgniter 4 <https://github.com/codeigniter4/CodeIgniter4>`_ is the latest
version of the framework.

CodeIgniter 3 is the legacy version of the framework, intended for use with PHP
5.6+. This version is in maintenance, receiving mostly just security updates.

*******************
Release Information
*******************

This repo contains in-development code for future releases. To download the
latest stable release please visit the `CodeIgniter Downloads
<https://codeigniter.com/download>`_ page.

**************************
Changelog and New Features
**************************

You can find a list of all changes for each release in the `user
guide change log <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/changelog.rst>`_.

*******************
Server Requirements
*******************

PHP version 5.6 or newer is recommended.

It should work on 5.4.8 as well, but we strongly advise you NOT to run
such old versions of PHP, because of potential security and performance
issues, as well as missing features.

************
Installation
************

Please see the `installation section <https://codeigniter.com/userguide3/installation/index.html>`_
of the CodeIgniter User Guide.

*******
License
*******

Please see the `license
agreement <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/license.rst>`_.

*********
Resources
*********

-  `User Guide <https://codeigniter.com/userguide3/>`_
-  `Contributing Guide <https://github.com/bcit-ci/CodeIgniter/blob/develop/contributing.md>`_
-  `Language File Translations <https://github.com/bcit-ci/codeigniter3-translations>`_
-  `Community Forums <https://forum.codeigniter.com/>`_
-  `Community Wiki <https://github.com/bcit-ci/CodeIgniter/wiki>`_
-  `Community Slack Channel <https://codeigniterchat.slack.com>`_

Report security issues to our `Security Panel <mailto:security@codeigniter.com>`_
or via our `page on HackerOne <https://hackerone.com/codeigniter>`_, thank you.

***************
Acknowledgement
***************

The CodeIgniter team would like to thank EllisLab, all the
contributors to the CodeIgniter project and you, the CodeIgniter user.

#######################
Kodhe Framework Support
#######################

This project is a fork of the official `bcit-ci/CodeIgniter
<https://github.com/bcit-ci/CodeIgniter>`_ repository, further developed
to run on top of the `Kodhe Framework <https://packagist.org/packages/kodhe/>`_
packages. The legacy ``system/`` folder is no longer used: the framework
core is now provided by Kodhe packages installed through Composer, while a
compatibility (legacy) layer keeps existing CI3 code and tests working with
minimal changes.

**************
Kodhe Packages
**************

See ``composer.json`` for the full list of required packages, including:

-  ``kodhe/framework`` - core framework (replaces the CI3 ``system/``):
   ``Application``, ``common.php``, helpers and libraries
-  ``kodhe/database`` - database layer & ORM (replaces ``CI_DB_*``)
-  ``kodhe/http``, ``kodhe/session``, ``kodhe/cache`` - HTTP, session and
   caching components
-  ``kodhe/email``, ``kodhe/encrypt``, ``kodhe/validation``,
   ``kodhe/upload``, etc. - remaining libraries shipped as separate packages

*******************
New Path Structure
*******************

The application bootstrap (``bootstrap/app.php``) and the test bootstrap
(``tests/Bootstrap.php``) define the following constants::

    SYSPATH           => vendor/kodhe/framework/   (fallback: packages/framework/)
    BASEPATH          => SYSPATH                   (used by the legacy CI3 common.php)
    SYSTEM_PATH       => SYSPATH . 'src/'          (used by tests/mocks/*.php helpers)
    CI_HELPER_PATH    => SYSPATH . 'src/Support/Helpers/'
    CI_LANGUAGE_PATH  => SYSPATH . 'Resources/language/'

**************************
Compatibility Layer (Shim)
**************************

-  ``tests/aliases.php`` - maps legacy CI3 classes (``CI_Benchmark``,
   ``CI_Config``, ``CI_Input``, ``CI_Controller``, ``CI_Model``,
   ``CI_Session``, etc.) to their Kodhe equivalents via ``class_alias()``,
   guarded by ``class_exists(..., FALSE)`` so test mocks still take
   precedence. The alias map follows
   ``packages/framework/src/Config/Setup.php``.
-  Legacy helpers & language files - CI3 helpers (e.g. ``url_helper``,
   ``form_helper``) are loaded from ``src/Support/Helpers/*_helper.php``
   and language files from ``Resources/language/english/*_lang.php``.
-  ``tests/mocks/`` - ``ci_testcase.php`` and the mock autoloader were
   adjusted so ``helper()`` / ``lang()`` look up files under the Kodhe
   paths above.

*****************
Running The Tests
*****************

The legacy CI3 test suite (``tests/codeigniter/``) has been adapted to the
Kodhe bootstrap. To run it::

    composer install
    cd tests
    ../vendor/bin/phpunit -c phpunit.xml

Notes:

-  ``tests/phpunit.xml`` was modernized for PHPUnit 9/10
   (``<coverage><include>`` format).
-  Test database configuration lives in ``tests/database/db.php``.
-  The original CI3 bootstrap is kept as ``tests/Bootstrap.php.orig-ci3``
   for reference.

***************
Status Checklist
***************

-  [x] Kodhe dependencies declared in ``composer.json``
-  [x] Application bootstrap uses ``Kodhe\Framework\Foundation\Application``
-  [x] ``tests/Bootstrap.php`` rewritten for Kodhe paths
-  [x] ``tests/aliases.php`` (CI3 -> Kodhe shim)
-  [x] ``tests/mocks/ci_testcase.php`` & mock autoloader adapted
-  [x] ``tests/phpunit.xml`` modernized
-  [ ] Full verification of the ``tests/codeigniter/`` suite against the
     Kodhe runtime (requires ``composer install`` + PHPUnit; run per
     directory: ``core``, ``database``, ``helpers``, ``libraries``)

*******
License
*******

Legacy CodeIgniter code remains under the MIT License (see each file's
header); Kodhe packages follow the license of their respective repositories.
