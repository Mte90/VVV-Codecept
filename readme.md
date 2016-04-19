#VVV-Codeception
[![License](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)   

#How Works
This script use [Variable-VVV](https://github.com/bradp/vv) to get the path of the local [Varying Vagrant Vagrants](https://github.com/Varying-Vagrant-Vagrants/VVV) installation.  
After that auto install [WP-Browser](https://github.com/lucatume/wp-browser) addon for codecetpion with all the dependencies (codeception itself) with composer.  
Finally generate the codeception configuration file and tests.  
THe `codeception.yml` file is filled with the standard db and wp user admin configuration of VVV, the db used is the `wordpress_unit_tests` and as localhost use the ip address of the machine (in this way use the db of VVV but the power of the host machine).   
Also for the site use the site url (extracted from the path) and for the plugin use the plugin name where that script is called.

#Try Codeception
To try Codeception on WordPress take a look on [idlikethis](https://github.com/lucatume/idlikethis/) that is an plugin example.

##Install
Place this script where you want (/usr/local/bin on Linux) and remember to add the executable permission (chmod +x).  

    cd /tmp
    git clone https://github.com/Mte90/VVV-Codeception
    chmod +x ./VVV-Codeception/vvv-codecept.php
    mv ./VVV-Codeception/vvv-codecept.php /usr/local/bin/vvv-codecept
    rm -r ./VVV-Codeception