<?php declare(strict_types=1);

namespace Gam6itko\JSCC;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
trait ConfigPropertiesTrait
{
    private function extractClassPropertyTypes(\ReflectionClass $reflectionClass): array
    {
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        $result = [];
        foreach ($properties as $p) {
            $docComment = $p->getDocComment();
            if (!preg_match('/@var (\S+)/', $docComment, $matches)) {
                $result[$p->getName()] = 'string';
                continue;
            }

            $type = preg_replace(
                [
                    '/\|null/',
                    '/\[\]/',
                    '/Config/',
                ],
                [
                    '',
                    'Array',
                    '',
                ],
                $matches[1]
            );
            $type = lcfirst($type);
            $result[$p->getName()] = $type;
        }

        return $result;
    }
}
