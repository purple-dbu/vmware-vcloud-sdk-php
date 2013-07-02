vmware-vcloud-sdk-php
=====================

VMware vCloud SDK for PHP - Repository for importing with Composer



Usage
-----

### Step 1. Edit your composer.json ###


VMware vCloud SDK for PHP requires PEAR's package HTTP_Request2. To manage this
dependency for your project, you can either:

1. (Option A) install HTTP_Request2 through Composer (recommended),
2. (Option B) use a PEAR package for your system, and install HTTP_Request2 through `pear
upgrade-all && pear install HTTP_Request2`.


#### Option A. PEAR's HTTP_Request2 is NOT installed on your system ####

Otherwise, if HTTP_Request2 is **NOT** installed on your system, you need to add
the following lines to your composer.json instead:

    "require": {
      vmware/vcloud-sdk": "5.1.2",
      "pear-pear/HTTP_Request2": "*"
    }


#### Option B. PEAR's HTTP_Request2 is installed on your system ####

If HTTP_Request2 is installed on your system, all you need is to add the
following lines to your composer.json:

    "require": {
      vmware/vcloud-sdk": "5.1.2"
    }



License
-------

See vCloudSDKforPHP-License.docx



Setup a development environment
-------------------------------

This section is intented for contributors would would fork this project.


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
