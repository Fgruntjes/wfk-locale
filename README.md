wfk-locale
==========

Adds locale detection based on url and a locale selector.

Installation
------------

From the root of your ZF2 Skeleton Application run

    ./composer.phar require wfk/wfk-locale

You can add the locale selector by calling the wfkLocale view helper. This generates an ul with all available locales and there url.

    $this->wfkLocale();