# JMS Serializer Config Converter (JSCC)

[![Build Status](https://travis-ci.org/gam6itko/jms-serializer-config-converter.svg?branch=master)](https://travis-ci.org/gam6itko/jms-serializer-config-converter)

Convert [jms-serialize](https://jmsyst.com/libs/serializer) metadata configuration to another format.

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
