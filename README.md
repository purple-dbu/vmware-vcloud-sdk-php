vmware-vcloud-sdk-php
=====================

VMware vCloud SDK for PHP - Repository for importing with Composer



Usage
-----

### Step 1. Install Composer (skip if already installed) ###

Go to your folder root and execute:

    curl -sS https://getcomposer.org/installer | php

If the installation work correctly, this should display:

    #!/usr/bin/env php
    All settings correct for using Composer
    Downloading...

    Composer successfully installed to: /mnt/hgfs/Documents/sandbox/vcloud/composer.phar
    Use it: php composer.phar

At this step, you should have the following files in your project's folder:

    $ls -Al
    - composer.phar (~900 kB)

Then, execute:

    [ ! -e composer.json ] && echo -e '{\n  \n}' > composer.json && php composer.phar install

If the installation work correctly, this should display:

    Loading composer repositories with package information
    Installing dependencies (including require-dev)
    Nothing to install or update
    Generating autoload files

At this step, you should have the following files in your project's folder:

    $ ls -Al
    - composer.phar (~900 kB)
    - composer.json (7 B)
    - vendor
      - autoload.php (~182 B)
      - composer
        - autoload_classmap.php (~150 B)
        - autoload_namespaces.php (~150 B)
        - autoload_real.php (~1 kB)
        - ClassLoader.php (~7 kB)


### Step 2. Edit your composer.json ###


VMware vCloud SDK for PHP requires PEAR's package HTTP_Request2. To manage this
dependency for your project, you can either:

1. (Option A) install HTTP_Request2 through Composer (recommended),
2. (Option B) use a PEAR package for your system, and install HTTP_Request2 through `pear
upgrade-all && pear install HTTP_Request2`.


#### Option A. PEAR's HTTP_Request2 is NOT installed on your system ####

Otherwise, if HTTP_Request2 is **NOT** installed on your system, you need to add
the following lines to your composer.json instead:

    "repositories": [
      {
          "type": "pear",
          "url": "http://pear.php.net"
      }
    ],
    "require": {
      "vmware/vcloud-sdk": "5.1.2",
      "pear-pear/HTTP_Request2": "*"
    }


#### Option B. PEAR's HTTP_Request2 is installed on your system ####

If HTTP_Request2 is installed on your system, all you need is to add the
following lines to your composer.json:

    "require": {
      "vmware/vcloud-sdk": "5.1.2"
    }


### Step 3. Update dependencies ###

    php composer.phar update

If the installation work correctly, this should display:

    Loading composer repositories with package information
    Initializing PEAR repository http://pear.php.net
    Updating dependencies (including require-dev)
      - Installing vmware/vcloud-sdk (5.1.2 8f2e517)
        Cloning 8f2e517dd3e5c858d7729148bac526414d1444e3

      - Installing pear-pear.php.net/xml_util (1.2.1)
        Downloading: 100%
      - Installing pear-pear.php.net/console_getopt (1.3.1)
        Downloading: 100%
      - Installing pear-pear.php.net/structures_graph (1.0.4)
        Downloading: 100%
      - Installing pear-pear.php.net/archive_tar (1.3.11)
        Downloading: 100%
      - Installing pear-pear.php.net/pear (1.9.4)
        Downloading: 100%
      - Installing pear-pear.php.net/net_url2 (2.0.0)
        Downloading: 100%
      - Installing pear-pear.php.net/http_request2 (2.1.1)
        Downloading: 100%
    Writing lock file
    Generating autoload files



### Step 4. Use it! ###

Now, you should be able to use VMware vCloud SDK for PHP by simply use the
following PHP code:

    require_once 'vendor/autoload.php';


    $host = '192.168.0.100';

    $auth = array(
      'username' => 'username@organization',
      'password' => 'password',
    );

    $httpConfig = array(
        'proxy_host' => null,
        'proxy_port' => null,
        'proxy_user' => null,
        'proxy_password' => null,
        'ssl_verify_peer' => false,
        'ssl_verify_host' => false,
        'ssl_cafile'  => null,
      );

    echo 'Authenticating on ' . $host . '... ';
    $service = VMware_VCloud_SDK_Service::getService();
    try {
      $result = $service->login($host, $auth, $httpConfig);
      echo "OK\n";
    }
    catch(Exception $e) {
      echo 'NOK - ' . $e->getMessage() . "\n";
      exit(1);
    }

    ...



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
