ActiveForm with required save for Yii 2
============================

This widget makes it necessary to save the activeform.
Before closing the page with unsaved data, the script will ask for confirmation of the action

REQUIREMENTS
------------

You should generally follow [Yii 2 requirements](https://github.com/yiisoft/yii2/blob/master/README.md).
The minimum is that your Web server supports PHP 7.0.


## INSTALLATION

### Install via subtree and composer

Include like subtree to scl/tools directory of project
git subtree add --squash --prefix=scl/composer/yii2-activeform git@gitlab.icerockdev.com:scl/scl-yii/activeform.git tag_name

Example `git subtree add --squash --prefix=scl/composer/yii2-activeform git@gitlab.icerockdev.com:scl/scl-yii/activeform.git master`

=======
````json
{
    "require": {
        "scl/yii2-activeform": "@dev"
    },
    "repositories": [
        {
            "type": "path",
            "url":  "scl/composer/yii2-activeform"
        }
    ]
}
````

## CONFIGURING

To use this extension, you should add some settings in your application AppAsset configuration file.
It may be like the following:

`backend/assets/AppAsset.php`
```php
    public $depends = [
        ...
        'scl\activeform\ActiveFormAsset'
    ];
```

## USAGE

Example of simple usage:

```php
use scl\activeform\ActiveFormRequiredSave;

<?php $form = ActiveFormRequiredSave::begin(); ?>

...

<?php ActiveFormRequiredSave::end(); ?>

```
