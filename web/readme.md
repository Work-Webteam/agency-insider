# General

This is the directory where Drupal core will be installed.

Your web server should point to this directory as the root.

Composer should be used for contrib themes and modules as well as drupal core.

Patches are recorded in ../composer.json. 



# GitIgnore file
The .gitignore file has not been included in this repo - if you would like to use the more standard version create your own .gitignore file at ../.gitignore and add the following:

```
# Ignore directories generated by Composer
/vendor/
/web/core/
/web/modules/contrib/
/web/themes/contrib/
/web/profiles/contrib/

# Ignore sensitive information
/web/sites/*/settings.php
/web/sites/*/settings.local.php

# Ignore Drupal's file directory
/web/sites/*/files/

# Ignore SimpleTest multi-site environment.
/web/sites/simpletest

# Ignore files generated by PhpStorm
/.idea/

# Ignore .env files as they are personal
/.env
```

