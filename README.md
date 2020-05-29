# JMS Serializer Config Converter (JSCC)

[![Build Status](https://travis-ci.org/gam6itko/jms-serializer-config-converter.svg?branch=master)](https://travis-ci.org/gam6itko/jms-serializer-config-converter)
[![Coverage Status](https://coveralls.io/repos/github/gam6itko/jms-serializer-config-converter/badge.svg?branch=master)](https://coveralls.io/github/gam6itko/jms-serializer-config-converter?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/53ec90f41542a0495d1b/maintainability)](https://codeclimate.com/github/gam6itko/jms-serializer-config-converter/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/53ec90f41542a0495d1b/test_coverage)](https://codeclimate.com/github/gam6itko/jms-serializer-config-converter/test_coverage)

Converts [jms-serializer](https://jmsyst.com/libs/serializer) metadata configuration to another format.

My small project with the serializer configuration in the annotations has grown to large sizes, and it is necessary to break it into several small ones. 
Also, I need to convert configurations of a serializer to files for different projects. 

If you have the same problem then this library will help you.

## Installation
```bash
composer require gam6itko/jms-serializer-config-converter --dev
```

## Supports
|            | from | to |
|------------|------|----|
| yaml       | ✓    | ✓  |
| xml        | ✓    | ✓  |
| annotation | ✓    | ⨉  |

## Usage

```php
use Gam6itko\JSCC\Converter\Converter;use Gam6itko\JSCC\Denormalizer\XmlDenormalizer;use Gam6itko\JSCC\Denormalizer\YamlDenormalizer;use Gam6itko\JSCC\Normalizer\AnnotationNormalizer;use Gam6itko\JSCC\Normalizer\XmlNormalizer;use Gam6itko\JSCC\Normalizer\YamlNormalizer;use Metadata\Driver\FileLocator;

// normalizers
$xmlFileLocator = new FileLocator([
    'Namespace' => 'folder_with_xml'
]);
$xmlNormalizer = new XmlNormalizer($xmlFileLocator);

$yamlFileLocator = new FileLocator([
    'Namespace' => 'folder_with_yaml'
]);
$yamlNormalizer = new YamlNormalizer($yamlFileLocator);

$annotationNormalizer = new AnnotationNormalizer();

// denormalizers
$xmlDenormalizer = new XmlDenormalizer([
    'Namespace' => 'folder_where_to_save_xml'
]);

$yamlDenormalizer = new YamlDenormalizer([
    'Namespace' => 'folder_where_to_save_yaml'
]);

// show time
$converter = new Converter(
    [
        'annotation' => $annotationNormalizer,
        'annot' => $annotationNormalizer,
        'xml'   => $xmlNormalizer,
        'yaml'  => $yamlNormalizer,
        'yml'   => $yamlNormalizer,
        'foo'   => $yamlNormalizer,
    ],
    [
        'xml'  => $xmlDenormalizer,
        'yaml' => $yamlDenormalizer,
        'bar'  => $yamlDenormalizer,
    ]
);

$refClass = new \ReflectionClass('Namespace\ClassName');
// get annotation from `Namespace\ClassName` class and save it to `folder_where_to_save_xml`
$converter->convert($refClass, 'annotation', 'yaml');
// get yaml from `folder_with_xml` and save it to `folder_where_to_save_yaml`
$converter->convert($refClass, 'xml', 'yaml');
// get yaml from `folder_with_yaml` and save it to `folder_where_to_save_xml`
$converter->convert($refClass, 'yml', 'xml');
// get yaml from `folder_with_yaml` and save it to `folder_where_to_save_yaml`
$converter->convert($refClass, 'foo', 'bar');
// exception here !!!
$converter->convert($refClass, 'xml', 'annotation');
```
