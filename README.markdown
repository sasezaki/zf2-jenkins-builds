ZF2 Jenkins builds
==================

Zend Framework 2 's individual components build files.

 - This script aims to aid to use Jenkins (or other CI) as easy.

##HOW TO USE?
### INSTALL 
phpunit, jenkins, ant &

ppw - PHP Project Wizard 
(when use zf2-jenkins-builds.php)

 - http://sebastian-bergmann.de/archives/908-PHP-Project-Wizard.html

### SETTING

> $mv builds/ /path/to/zf2/

    OR

> $sudo php zf2-jenkins-builds.php

in your zf2 clone dir.


### Permission
A lot of components unittests use temporary files.
So, you should change permission or chown tests/ dir.
> $sudo chown -R tests/Zend

### SETUP Jenkins.

