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

use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\Flow\Annotations as Flow;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * @Flow\Scope("singleton")
 */
class LocalisationService
{
    /**
     * @var array
     * @Flow\InjectConfiguration
     */
    protected $configuration;

    /**
     * @var FusionService
     * @Flow\Inject
     */
    protected $fusionService;

    /**
     * @var NodeTypeManager
     * @Flow\Inject
     */
    protected $nodeTypeManager;

    public function __construct()
    {

    }

    public function generateXliff(string $prototype, bool $force = false): bool
    {
        // @ Todo: check for generation of Components as well
        // @ Todo: check for Mixins...
        // @ Todo: proper checks before executing...
        $prototypeName = $this->fusionService->getPrototypeName($prototype);
        $defaultLanguage = $this->configuration['languages']['default'];
        $additionalLanguages = $this->configuration['languages']['additional'];
        $defaultTemplate = $this->configuration['xlf']['structure']['default'];
        $additionalTemplate = $this->configuration['xlf']['structure']['additional'];
        $defaultLabelTemplate = $this->configuration['xlf']['label']['default'];
        $additionalLabelTemplate = $this->configuration['xlf']['label']['additional'];
        $nodeTypeDefinition = $this->nodeTypeManager->getNodeType($prototypeName);
        $labels = $this->labelHelper($nodeTypeDefinition->getLocalConfiguration(), 'label', $prototypeName);
        $labelsDefinition = '';
        $additionalLabelsDefinition = '';
        $result = false;

        foreach ($labels as $label) {
            $value = str_replace('.', '', preg_replace('/^(.*?)\./', '', $label));
            $labelsDefinition .= '            ' . sprintf($defaultLabelTemplate, $label, ucfirst($value));
            $additionalLabelsDefinition .= sprintf($additionalLabelTemplate, $label, ucfirst($value), ucfirst($value));
        }

        $defaultXlfDefinition = sprintf($defaultTemplate, $this->configuration['packageKey'], $defaultLanguage, $labelsDefinition);

        if (FileService::writeLocalisationFile($prototypeName, $defaultXlfDefinition, $defaultLanguage, $this->configuration['packageParentFolder'], $force)) {
            $result = true;
        }

        if ($additionalLanguages) {
            foreach ($additionalLanguages as $language) {
                $xlfDefinition = sprintf($additionalTemplate, $this->configuration['packageKey'], $defaultLanguage, $language, $additionalLabelsDefinition);
                if (FileService::writeLocalisationFile($prototypeName, $xlfDefinition, $language, $this->configuration['packageParentFolder'], $force)) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $array
     * @param string $needle
     * @param string $prototypeName
     * @return array
     */
    protected function labelHelper(array $array, string $needle, string $prototypeName)
    {
        $iterator = new RecursiveArrayIterator($array);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
        $return = [];
        foreach ($recursive as $key => $value) {
            if ($key === $needle) {
                $fullPrototypeName = explode(':', $prototypeName);
                $fullPrototypeName = $fullPrototypeName[0] . ':NodeTypes.' . $fullPrototypeName[1];
                $return[] = str_replace($fullPrototypeName . ':', '', $value);
            }
        }
        return $return;
    }

}
