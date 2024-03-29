## Installation

### Bundle setup

Require this bundle with composer:

````bash
$ composer require sidus/datagrid-bundle
````

### Add the bundle to AppKernel.php

````php
<?php
/**
 * app/AppKernel.php
 */
class AppKernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            
            // Required
            new Sidus\FilterBundle\SidusFilterBundle(),
            
            // Required, obviously
            new Sidus\DataGridBundle\SidusDataGridBundle(),

            // ...
        ];
    }
}
````

### Setup basic configuration

This step is strongly recommended although optional.

Enable Symfony's Translator component:

````yaml
framework:
    translator: { fallbacks: ['%locale%'] } # Uncomment this line
    # ...
````

Enable the proper form template:

````yaml
twig:
    # ...
    form_themes:
        - 'bootstrap_4_layout.html.twig'
        - '@SidusDataGrid/Form/bootstrap4.html.twig' # Also available in bootstrap3 flavor
````
