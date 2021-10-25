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
    public static function writeLocalisationFile(): void
    {

    }

    /**
     * @throws FilesException
     */
    public static function writeFusionFile(string $prototype, string $prototypeDefinition, bool $force = false, string $fileType = 'fusion'): bool
    {
        $packageKey = explode(':', $prototype)[0];
        $fusionPath = explode(':', $prototype)[1];

        if (preg_match('/Atom|Molecule|Organism|Template/', $fusionPath)) {
            $parts = explode('.', $fusionPath);
            $fusionPath = implode('.', array_merge($parts, [end($parts)]));
        }
        $fileNameAndPath = Files::concatenatePaths([
                FLOW_PATH_ROOT,
                'DistributionPackages',
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
