<?php

namespace UpAssist\PrototypeGenerator\Command;

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

use Neos\Flow\Cli\CommandController;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Exception\FilesException;
use UpAssist\PrototypeGenerator\Service\FusionService;
use UpAssist\PrototypeGenerator\Service\LocalisationService;
use UpAssist\PrototypeGenerator\Service\NodeTypeService;

/**
 *
 */
class GeneratorCommandController extends CommandController
{
    /**
     * @var FusionService
     * @Flow\Inject
     */
    protected $fusionService;

    /**
     * @var LocalisationService
     * @Flow\Inject
     */
    protected $localisationService;

    /**
     * @var NodeTypeService
     * @Flow\Inject
     */
    protected $nodeTypeService;

  /**
   * Renders prototypes for your nodetype
   *
   * @param string|null $nodeType
   * @param bool $force
   * @throws FilesException
   */
    public function prototypeCommand(string $nodeType, bool $force = false)
    {
        $nodeType = $this->fusionService->getPrototypeName($nodeType);
        $this->outputLine(sprintf('<info>💡 Looking for %s...</info>', $nodeType));
        if (!$this->nodeTypeService->hasNodeType($nodeType)) {
            $this->outputLine('<error>💩 The nodeType you provided does not seem to exist...</error>');
            $this->sendAndExit();
        }

        $this->outputLine('<info>🚀 NodeType found.</info>');
        $this->outputLine('<comment>🌱 Starting generation...</comment>');

        // Read the nodetype definition
        $properties = $this->nodeTypeService->getLocalConfiguration($nodeType)['properties'] ?? [];

        // Generate the Prototype file and the definition
        if ($this->fusionService->generateProtoType($nodeType, $properties, $force)) {
            $this->outputLine('<info>🚀 Prototype(s) successful generated. Please modify your file(s) when your nodeType definition is complete.</info>');
            $this->sendAndExit();
        }

        $this->outputLine('<error>💡 Nothing done. To overwrite an existing file, add `--force` to your command. Please be aware this will make manual changes undone.</error>');
        $this->sendAndExit();
    }

    /**
     * Renders Atom prototype
     * @param string $prototypeName
     * @param bool $force
     * @throws FilesException
     */
    public function atomCommand(string $prototypeName, bool $force = false)
    {
        $this->generatePrototype($prototypeName, [], $force, 'Atom');
    }

    /**
     * Renders Molecule prototype
     *
     * @param string $prototypeName
     * @param bool $force
     * @throws FilesException
     */
    public function moleculeCommand(string $prototypeName, bool $force = false)
    {
        $this->generatePrototype($prototypeName, [], $force, 'Molecule');
    }

    /**
     * Renders Organism prototype
     *
     * @param string $prototypeName
     * @param bool $force
     * @throws FilesException
     */
    public function organismCommand(string $prototypeName, bool $force = false)
    {
        $this->generatePrototype($prototypeName, [], $force, 'Organism');
    }

    /**
     * Renders Template prototype
     *
     * @param string $prototypeName
     * @param bool $force
     * @throws FilesException
     */
    public function templateCommand(string $prototypeName, bool $force = false)
    {
        $this->generatePrototype($prototypeName, [], $force, 'Template');
    }

    /**
     * @param string $prototype
     * @param array $properties
     * @param bool $force
     * @param string|null $atomicPart
     * @throws FilesException
     */
    private function generatePrototype(string $prototype, array $properties = [], bool $force = false, string $atomicPart = null): void
    {
        if ($atomicPart) {
            $prototype = $this->fusionService->getPrototypeName($prototype, $atomicPart);
        }

        if ($this->fusionService->generateProtoType($prototype, $properties, $force)) {
            $this->outputLine('<info>🚀 Prototype successful generated. Please modify your file when your nodeType definition is complete.</info>');
            $this->sendAndExit();
        }

        $this->outputLine('<error>💡 Nothing done. To overwrite an existing file, add `--force` to your command. Please be aware this will make manual changes undone.</error>');
        $this->sendAndExit();
    }

    /**
     * Renders ExtendedRenderer prototype
     *
     * @param string $prototypeName
     * @param bool $force
     * @throws FilesException
     */
    public function extendedRendererCommand(string $prototypeName, bool $force = false)
    {
        if ($this->fusionService->generateExtendedRenderer($prototypeName, $force)) {
            $this->outputLine('<info>🚀 Prototype successful generated. Please modify your file when your nodeType definition is complete.</info>');
            $this->sendAndExit();
        }

        $this->outputLine('<error>💡 Nothing done. To overwrite an existing file, add `--force` to your command. Please be aware this will make manual changes undone.</error>');
        $this->sendAndExit();
    }

    /**
     * Generate XLF files
     *
     * @param string $nodeType
     * @param bool $force
     */
    public function xlfCommand(string $nodeType, bool $force = false)
    {
        if ($this->localisationService->generateXliff($nodeType, $force)) {
            $this->outputLine('<info>Generated XLF file</info>');
            $this->sendAndExit();
        }

        $this->outputLine('<error>Something went wrong...</error>');
        $this->sendAndExit();
    }
}
