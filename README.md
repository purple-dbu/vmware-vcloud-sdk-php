vmware-vcloud-sdk-php
=====================

VMware vCloud SDK for PHP - Repository for importing with Composer

License
-------

See vCloudSDKforPHP-License.docx


Setup a development environment
-------------------------------


### 1/a. Install Composer (skip if already installed) ###

    curl -sS https://getcomposer.org/installer | php

If the installation work correctly, this should display:

    #!/usr/bin/env php
    All settings correct for using Composer
    Downloading...

    Composer successfully installed to: /mnt/hgfs/Documents/sandbox/vcloud/composer.phar
    Use it: php composer.phar


### 1/b. Upgrade Composer (skip if freshly installed) ###

    php composer.phar self-update

This will upgrade Composer to the latest version. If Composer is already
up-to-date, this will display:

    You are using the latest composer version.


### 2. Install/upgrade dependencies ###

    php composer.phar install

If the installation work correctly, this should display something like:

    Loading composer repositories with package information
    Initializing PEAR repository http://pear2.php.net
    Installing dependencies (including require-dev)
      - Installing pear-pear2.php.net/pear2_http_request (0.1.0)
        Downloading: 100%
    Writing lock file
    Generating autoload files
