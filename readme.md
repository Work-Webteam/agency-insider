# PSA Agency Intranet Project

This repository hosts the drupal install for the PSA Agency Intranet website built on Drupal. 

## Composer
Composer takes care of most of the heavy lifting for contributed files and Drupal scaffolding. After pulling this repository you will need to:

1. Run ```composer install```
2. Set up your settings.php or settings.local.php file with database settings
3. Import your database
4. Import config settings from the ./config/sync directory. The location of the config directory should be set in the settings.php or setting.local.php file (../config/sync). 

After that you should have a functional site. You may want to run ```drush updb``` along with ```drush cr``` and ```drush cron``` before you start working with the site. Alternatively you can use the drupal console. Both should be included in this sites composer.json file, and should be ready to go after running ```composer install```. 

### Patches
The Patches directory includes notes about patches that were applied when this site was put together by its original author. TODO: This should probably be moved to web/modules to bring it in line with the AtWork install.

### Web
This is where the main Drupal install resides.
