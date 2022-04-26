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
use Neos\Utility\Files;

class FileService
{
    public static function writeLocalisationFile(string $nodeTypeName, string $contents, string $language, array $packageParentFolder = [], bool $force = false): bool
    {
        $packageKey = explode(':', $nodeTypeName)[0];
        $xlfPath = explode(':', $nodeTypeName)[1];
        $fileNameAndPath = Files::concatenatePaths([
                FLOW_PATH_ROOT,
                implode('/', $packageParentFolder),
                $packageKey,
                'Resources',
                'Private',
                'Translations',
                $language,
                'NodeTypes'
            ]) . '/' . str_replace('.', '/', $xlfPath) . '.xlf';

        if (!file_exists($fileNameAndPath) || $force) {
            Files::createDirectoryRecursively(dirname($fileNameAndPath));
            file_put_contents($fileNameAndPath, $contents);
            return true;
        }

        return false;
    }

    /**
     * @throws FilesException
     */
    public static function writeFusionFile(string $prototype, string $prototypeDefinition, array $packageParentFolder = [], bool $force = false, string $fileType = 'fusion'): bool
    {
        $packageKey = explode(':', $prototype)[0];
        $fusionPath = explode(':', $prototype)[1];

        if (preg_match('/Atom|Molecule|Organism|Template/', $fusionPath)) {
            $parts = explode('.', $fusionPath);
            $fusionPath = implode('.', array_merge($parts, [end($parts)]));
        }
        $fileNameAndPath = Files::concatenatePaths([
                FLOW_PATH_ROOT,
                implode('/', $packageParentFolder),
                $packageKey,
                'Resources',
                'Private',
                'Fusion'
            ]) . '/' . str_replace('.', '/', $fusionPath) . '.' . $fileType;

        if (!file_exists($fileNameAndPath) || $force) {
            Files::createDirectoryRecursively(dirname($fileNameAndPath));
            file_put_contents($fileNameAndPath, $prototypeDefinition);
            return true;
        }

        return false;

    }
}
