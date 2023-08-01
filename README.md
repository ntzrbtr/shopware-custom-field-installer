# Shopware custom field installer

This package provides a simple way to install and uninstall custom fields in your Shopware installation: it leverages
the code in the Shopware core for app handling and makes it usable stand-alone.

The package provides a new command `netzarbeiter:custom-fields:update` which updates the custom fields in your Shopware
installation. The command takes a XML file following the Manifest standard of Shopware apps as an input and updates the
database.

For more information on custom fields in apps, see https://developer.shopware.com/docs/guides/plugins/apps/custom-data/custom-fields.

## Usage

```bash
bin/console netzarbeiter:custom-fields:install <file>
```

```bash
bin/console netzarbeiter:custom-fields:uninstall <file>
```

As the core of the bundle is implemented inside a service, you could also use it without the command and e.g. integrate
it into the installation process of your Shopware plugins:

```php
<?php declare(strict_types=1);

namespace Swag\BasicExample;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;

class SwagBasicExample extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        $customFieldInstaller = $this->container->get(CustomFieldInstaller::class);
        $customFieldInstaller->install($this->getPath() . '/Resources/app/custom-field-set.xml', $installContext->getContext());
    }
}
```

## XML file

The XML file provides must comply with the schema of the app system; that means, that you also need to provide a minimal
`meta` section within the file.

Here's a minimalistic example:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<manifest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/shopware/platform/trunk/src/Core/Framework/App/Manifest/Schema/manifest-2.0.xsd">
    <meta>
        <name>MyExampleApp</name>
        <author>Your Company Ltd.</author>
        <copyright>(c) by Your Company Ltd.</copyright>
        <version>1.0.0</version>
        <license>MIT</license>
    </meta>
    <custom-fields>
        <!-- register each custom field set you may want to add -->
        <custom-field-set>
            <!-- the technical name of the custom field set, needs to be unique, therefor use your vendor prefix -->
            <name>swag_example_set</name>
            <!-- Translatable, the label of the field set -->
            <label>Example Set</label>
            <label lang="de-DE">Beispiel-Set</label>
            <!-- define the entities to which your field set should be assigned -->
            <related-entities>
                <order/>
            </related-entities>
            <!-- define the fields in your set -->
            <fields>
                <!-- the element type, defines the type of the field -->
                <!-- the name needs to be unique, therefore use your vendor prefix -->
                <text name="swag_code">
                    <!-- Translatable, the label of the field -->
                    <label>Example field</label>
                    <!-- Optional, Default = 1, order your fields by specifying the position -->
                    <position>1</position>
                    <!-- Optional, Default = false, mark a field as required -->
                    <required>false</required>
                    <!-- Optional, Translatable, the help text for the field -->
                    <help-text>Example field</help-text>
                </text>
                <float name="swag_test_float_field">
                    <label>Test float field</label>
                    <label lang="de-DE">Test-Kommazahlenfeld</label>
                    <help-text>This is an float field.</help-text>
                    <position>2</position>
                    <!-- some elements allow more configuration, like placeholder, main and max values etc. -->
                    <!-- Your IDE should give you pretty good autocompletion support to explore the configuration for a given type -->
                    <placeholder>Enter an float...</placeholder>
                    <min>0.5</min>
                    <max>1.6</max>
                    <steps>0.2</steps>
                </float>
            </fields>
        </custom-field-set>
    </custom-fields>
</manifest>
```

## Installation

Make sure Composer is installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require <package-name>
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable
version of this bundle:

```console
$ composer require <package-name>
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Netzarbeiter\Shopware\CustomFieldInstaller\NetzarbeiterShopwareCustomFieldInstallerBundle::class => ['all' => true],
];
```
