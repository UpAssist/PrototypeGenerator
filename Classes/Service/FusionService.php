<?php

namespace UpAssist\PrototypeGenerator\Service;

/*
 * This file is part of the UpAssist.PrototypeGenerator package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 *
 * UpAssist, <info@upassist.com>
 */

use Neos\Utility\Exception\FilesException;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class FusionService
{

    /**
     * @var array
     * @Flow\InjectConfiguration
     */
    protected $configuration;

    /**
     * Generate the prototype including all related files
     *
     * @throws FilesException
     */
    public function generateProtoType(string $prototype, array $properties = [], bool $force = false): bool
    {

        $propertiesDefinition = '';
        $propertyDefinitionEpilogue = PHP_EOL . '    ';
        $propertiesRendering = '';
        $propertyRenderingEpilogue = PHP_EOL . '            ';
        $propertiesPropTypes = '';
        $propertiesPropTypesEpilogue = PHP_EOL . '            ';
        $propertiesDefault = '';
        $propertyDefaultEpilogue = PHP_EOL . '    ';

        foreach ($properties as $propertyKey => $propertyValue) {
            $propertiesDefinition .= $this->getPropertyDefinition($propertyKey, $propertyValue, $propertyDefinitionEpilogue) . $propertyDefinitionEpilogue;
            $propertiesRendering .= $this->getPropertyRendering($propertyKey, $propertyRenderingEpilogue);
            $propertiesPropTypes .= $this->getPropertyPropType($propertyKey, $propertyValue, $propertiesPropTypesEpilogue);
            $propertiesDefault .= $this->getPropertyDefault($propertyKey, $propertyValue, $propertyDefaultEpilogue);
        }

        $propertiesDefinition = rtrim($propertiesDefinition);
        $propertiesRendering = rtrim($propertiesRendering);
        $propertiesPropTypes = sprintf($this->configuration['templates']['propTypes'], rtrim($propertiesPropTypes), rtrim($propertiesDefault));

        $inheritancePrototype = $this->getInheritancePrototype($prototype);
        $baseRenderPrototype = $this->getDefaultBaseRenderPrototype($prototype);

        $result = $this->writePrototypeToFiles($prototype, $inheritancePrototype, $baseRenderPrototype, $propertiesDefinition, $propertiesRendering, $propertiesPropTypes, $force);

        $additionalPrototypesToRender = [];
        foreach ($this->getAdditionalPrototypes($prototype) as $additionalPrototype) {
            $additionalPrototypesToRender[] = $this->getBaseRenderPrototype($additionalPrototype, $prototype);
        }

        foreach ($additionalPrototypesToRender as $additionalPrototypeToRender) {
            $result = $this->writePrototypeToFiles($additionalPrototypeToRender, $inheritancePrototype, $this->getDefaultBaseRenderPrototype($additionalPrototypeToRender), $propertiesDefinition, $propertiesRendering, $propertiesPropTypes, $force);
        }

        return $result;
    }

    /**
     * Determine the inheritance type based on nodeType + configuration
     *
     * @param string $prototype
     * @return string
     */
    public function getInheritancePrototype(string $prototype): string
    {
        switch ($prototype) {
            case !empty(preg_match('/Content/', $prototype)):
                $inheritancePrototype = $this->configuration['inheritance']['content'];
                break;
            case !empty(preg_match('/Collection/', $prototype)):
                $inheritancePrototype = $this->configuration['inheritance']['collection'];
                break;
            case !empty(preg_match('/Document/', $prototype)):
                $inheritancePrototype = $this->configuration['inheritance']['document'];
                break;
            default:
                $inheritancePrototype = $this->configuration['inheritance']['default'];
        }

        return $inheritancePrototype;
    }

    /**
     * Determine the renderer based on prototype and configuration
     *
     * @param string $prototype
     * @return string
     */
    public function getDefaultBaseRenderPrototype(string $prototype): string
    {
        switch ($prototype) {
            case !empty(preg_match('/Content/', $prototype)):
                $baseRenderPrototype = $this->configuration['rendering']['content'][0];
                break;
            case !empty(preg_match('/Collection/', $prototype)):
                $baseRenderPrototype = $this->configuration['rendering']['collection'][0];
                break;
            case !empty(preg_match('/Document/', $prototype)):
                $baseRenderPrototype = $this->configuration['rendering']['document'][0];
                break;
            case !empty(preg_match('/Atom/', $prototype)):
                $baseRenderPrototype = $this->configuration['rendering']['atom'];
                break;
            case !empty(preg_match('/Molecule/', $prototype)):
                $baseRenderPrototype = $this->configuration['rendering']['molecule'];
                break;
            case !empty(preg_match('/Organism/', $prototype)):
                $baseRenderPrototype = $this->configuration['rendering']['organism'];
                break;
            case !empty(preg_match('/Template/', $prototype)):
                $baseRenderPrototype = $this->configuration['rendering']['template'];
                break;
            default:
                $baseRenderPrototype = $this->configuration['rendering']['default'];
        }

        return $baseRenderPrototype;
    }

    /**
     * Gets the different additional types of prototype to generate from the configuration
     *
     * @param string $prototype
     * @return array
     */
    public function getAdditionalPrototypes(string $prototype): array
    {
        switch ($prototype) {
            case !empty(preg_match('/Content/', $prototype)):
                $prototypes = $this->configuration['rendering']['content'];
                break;
            case !empty(preg_match('/Collection/', $prototype)):
                $prototypes = $this->configuration['rendering']['collection'];
                break;
            case !empty(preg_match('/Document/', $prototype)):
                $prototypes = $this->configuration['rendering']['document'];
                break;
            default:
                $prototypes = [];
        }

        return $prototypes;
    }

    /**
     * Return the property definition as a string
     *
     * @param string $property
     * @param array $definition
     * @param string $epilogue
     * @return string
     */
    private function getPropertyDefinition(string $property, array $definition, string $epilogue = ''): string
    {
        $propertyDefinition = $property . ' = ';
        $inlineEditable = $definition['ui']['inlineEditable'] ?? false;
        if ($inlineEditable) {
            $propertyDefinition .= sprintf($this->configuration['templates']['properties']['definition']['inlineEditable'], $property);
        } else {
            $propertyDefinition .= sprintf($this->configuration['templates']['properties']['definition']['default'], $property);
        }

        return $propertyDefinition . $epilogue;
    }

    /**
     * Return the property rendering as a string
     *
     * @param string $property
     * @param string $epilogue
     * @return string
     */
    private function getPropertyRendering(string $property, string $epilogue = ''): string
    {
        return sprintf($this->configuration['templates']['properties']['rendering'], $property) . $epilogue;
    }

    /**
     * Return the propType for a property as a string
     *
     * @param string $property
     * @param array $propertyValue
     * @param string $epilogue
     * @return string
     */
    private function getPropertyPropType(string $property, array $propertyValue, string $epilogue = ''): string
    {
        return $property . ' = ${PropType.' . strtolower($propertyValue['type']) . '}' . $epilogue;
    }

    /**
     * Return the default value for a property as a string
     *
     * @param string $property
     * @param array $propertyValue
     * @param string $epilogue
     * @return string
     */
    private function getPropertyDefault(string $property, array $propertyValue, string $epilogue): string
    {
        switch ($propertyValue['type']) {
            case 'integer':
                $value = '0';
                break;
            case 'reference':
            case 'references':
                $value = 'null';
                break;
            case 'string':
            default:
                $value = "''";
        }
        return $property . ' = ' . $value . $epilogue;
    }

    /**
     * Determine the base render prototype
     *
     * @param string $baseRenderPrototype
     * @param string $prototype
     * @return string
     */
    private function getBaseRenderPrototype(string $baseRenderPrototype, string $prototype): string
    {
        if (preg_match('/atom|molecule|organism|template/', $baseRenderPrototype)) {
            $prototypeToReturn = str_replace([
                'Document',
                'Content',
                'Collection'
            ], $this->configuration['atomicBaseFolder'] . '.' . ucfirst($baseRenderPrototype), $prototype);
        }
        return !empty($prototypeToReturn) ? $prototypeToReturn : $baseRenderPrototype;
    }

    /**
     * Get the full prototype definition
     *
     * @param array $configuration
     * @param string $prototype
     * @param string $inheritancePrototype
     * @param string $baseRenderPrototype
     * @param string $propertiesDefinition
     * @param string $propertiesRendering
     * @param string $propTypes
     * @return string
     */
    private function getPrototype(array $configuration, string $prototype, string $inheritancePrototype, string $baseRenderPrototype, string $propertiesDefinition, string $propertiesRendering, string $propTypes): string
    {
        switch ($prototype) {
            case !empty(preg_match('/Content/', $prototype)):
                $prototype = sprintf($this->configuration['templates']['content'],
                    $configuration['comment'],
                    $prototype,
                    $inheritancePrototype,
                    $propertiesDefinition,
                    $this->getBaseRenderPrototype($baseRenderPrototype, $prototype)
                );
                break;
            case !empty(preg_match('/Collection/', $prototype)):
                $prototype = sprintf($this->configuration['templates']['collection'],
                    $configuration['comment'],
                    $prototype,
                    $inheritancePrototype,
                    $propertiesDefinition,
                    $this->getBaseRenderPrototype($baseRenderPrototype, $prototype)
                );
                break;
            case !empty(preg_match('/Document/', $prototype)):
                $prototype = sprintf($this->configuration['templates']['document'],
                    $configuration['comment'],
                    $prototype,
                    $inheritancePrototype,
                    $this->getBaseRenderPrototype($baseRenderPrototype, $prototype),
                    $propertiesDefinition,
                );
                break;
            case !empty(preg_match('/Atom|Molecule|Organism|Template/', $prototype)):
                $prototype = sprintf($this->configuration['templates']['atomic'],
                    $configuration['comment'],
                    $prototype,
                    $inheritancePrototype,
                    $propTypes,
                    $this->getBaseRenderPrototype($baseRenderPrototype, $prototype),
                    $propertiesRendering,
                    $this->configuration['extendedRenderer']
                );
                break;
            default:
                $prototype = sprintf($this->configuration['templates']['default'],
                    $configuration['comment'],
                    $prototype,
                    $inheritancePrototype,
                    $propertiesDefinition,
                    $this->getBaseRenderPrototype($baseRenderPrototype, $prototype),
                    $propertiesRendering,
                    $this->configuration['extendedRenderer']
                );
        }

        return $prototype;
    }

    /**
     * Generate and ExtendedRenderer Prototype
     *
     * @param string $prototypeName
     * @param bool $force
     * @return bool
     * @throws FilesException
     */
    public function generateExtendedRenderer(string $prototypeName, bool $force = false): bool
    {
        $prototypeName = $this->getPrototypeName($prototypeName);
        $prototype = sprintf($this->configuration['templates']['extendedRenderer'], $this->configuration['comment'], $prototypeName);
        if (!empty($this->configuration['helperFolder'])) {
            $prototypeNameArray = explode(':', $prototypeName);
            $prototypeName = $prototypeNameArray[0] . ':' . $this->configuration['helperFolder'] . '.' . $prototypeNameArray[1];
        }

        return FileService::writeFusionFile($prototypeName, $prototype, $force);
    }

    /**
     * Helper to get the prototype name
     *
     * @param string $prototype
     * @param string|null $atomicPart
     * @return string
     */
    public function getPrototypeName(string $prototype, string $atomicPart = null): string
    {
        $packageKey = $this->configuration['packageKey'];
        if (!empty($packageKey) && !strpos($prototype, ':')) {
            $prototype = $packageKey . ':' . $prototype;
        }

        if (!empty($atomicPart) && !preg_match("/{$atomicPart}/", $prototype)) {
            $prototype = str_replace(':', ":{$atomicPart}.", $prototype);
        }

        if (!empty($this->configuration['atomicBaseFolder'])) {
            if (strpos($prototype, 'Atom') || strpos($prototype, 'Molecule') || strpos($prototype, 'Organism') || strpos($prototype, 'Template')) {
                $prototype = str_replace([
                    'Atom',
                    'Molecule',
                    'Organism',
                    'Template'
                ], [
                    $this->configuration['atomicBaseFolder'] . '.Atom',
                    $this->configuration['atomicBaseFolder'] . '.Molecule',
                    $this->configuration['atomicBaseFolder'] . '.Organism',
                    $this->configuration['atomicBaseFolder'] . '.Template'
                ], $prototype);
            }
        }

        return $prototype;
    }

    /**
     * Helper to determine whether a CSS file should be generated
     *
     * @param string $prototype
     * @return bool
     */
    protected function shouldGenerateCSS(string $prototype): bool
    {
        return (bool)preg_match('/' . implode('|', $this->configuration['renderCSS']) . '/', strtolower($prototype));
    }

    /**
     * Helper to determine whether a JS file should be generated
     *
     * @param string $prototype
     * @return bool
     */
    protected function shouldGenerateJS(string $prototype): bool
    {
        return (bool)preg_match('/' . implode('|', $this->configuration['renderJS']) . '/', strtolower($prototype));
    }

    /**
     * Write the combination of files
     *
     * @param string $prototype
     * @param string $inheritancePrototype
     * @param string $baseRenderPrototype
     * @param string $propertiesDefinition
     * @param string $propertiesRendering
     * @param string $propTypes
     * @param bool $force
     * @throws FilesException
     */
    protected function writePrototypeToFiles(string $prototype, string $inheritancePrototype, string $baseRenderPrototype, string $propertiesDefinition, string $propertiesRendering, string $propTypes, bool $force = false): bool
    {
        $prototypeDefinition = $this->getPrototype($this->configuration, $prototype, $inheritancePrototype, $baseRenderPrototype, $propertiesDefinition, $propertiesRendering, $propTypes);
        $result = false;

        if ($this->shouldGenerateCSS($prototype)) {
            $css = "/** Styles for {$prototype} */" . PHP_EOL;
            $result = FileService::writeFusionFile($prototype, $css, $force, $this->configuration['cssExtension']);
        }

        if ($this->shouldGenerateJS($prototype)) {
            $js = "// JavaScript for {$prototype}" . PHP_EOL;
            $result = FileService::writeFusionFile($prototype, $js, $force, $this->configuration['jsExtension']);
        }

        if ($result !== false) {
            return FileService::writeFusionFile($prototype, $prototypeDefinition, $force);
        }

        return false;
    }
}
