ZF2 Jenkins builds
==================

Zend Framework 2 's individual components build files.

 - This script aims to aid to use Jenkins (or other CI) as easy.


![jekins](https://github.com/sasezaki/zf2-jenkins-builds/raw/master/media/img/jenkins-top.png)


##HOW TO USE?
### INSTALL 
phpunit, jenkins, ant &

ppw - PHP Project Wizard 
(when use zf2-jenkins-builds.php)

 - http://sebastian-bergmann.de/archives/908-PHP-Project-Wizard.html

### SETTING

    $mv builds/ /path/to/zf2/

> OR

    $sudo php zf2-jenkins-builds.php

in your zf2 clone dir.


### Permission
A lot of components unittests use temporary files.

So, you should change permission or chown tests/ dir.

    $sudo chown -R tests/Zend

### SETUP Jenkins.

 - Create new job (choose - Build a free-style)

![newjob](https://github.com/sasezaki/zf2-jenkins-builds/raw/master/media/img/jenkins-newjob.png)


 - Setting build

![antsetting](https://github.com/sasezaki/zf2-jenkins-builds/raw/master/media/img/jenkins-ant.png)


 - Test "Build Now"

![antsetting](https://github.com/sasezaki/zf2-jenkins-builds/raw/master/media/img/jenkins_ZF2Debug.png)

