<?php

namespace UpAssist\PrototypeGenerator\Service;

/*
 * This file is part of the UpAssist.PrototypeGenerator package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 *
 * UpAssist, <info@upassist.com>
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;

/**
 * Looks up NodeType definitions on both Neos 8 and Neos 9.
 *
 * Neos 8 exposes a global NodeTypeManager; Neos 9 has one per Content Repository.
 * Class names are strings on purpose, so neither version needs the other's classes.
 *
 * @Flow\Scope("singleton")
 */
class NodeTypeService
{
    private const NEOS9_REGISTRY = 'Neos\ContentRepositoryRegistry\ContentRepositoryRegistry';
    private const NEOS9_CONTENT_REPOSITORY_ID = 'Neos\ContentRepository\Core\SharedModel\ContentRepository\ContentRepositoryId';
    private const NEOS8_NODE_TYPE_MANAGER = 'Neos\ContentRepository\Domain\Service\NodeTypeManager';

    /**
     * @var string
     * @Flow\InjectConfiguration(path="contentRepository")
     */
    protected $contentRepositoryId;

    /**
     * @var ObjectManagerInterface
     * @Flow\Inject
     */
    protected $objectManager;

    public function hasNodeType(string $nodeTypeName): bool
    {
        return $this->getNodeTypeManager()->hasNodeType($nodeTypeName);
    }

    /**
     * Returns the node type's own configuration (without inherited supertype configuration).
     */
    public function getLocalConfiguration(string $nodeTypeName): array
    {
        $nodeType = $this->getNodeTypeManager()->getNodeType($nodeTypeName);
        return $nodeType ? $nodeType->getLocalConfiguration() : [];
    }

    /**
     * @return object Neos 8 or Neos 9 NodeTypeManager
     */
    private function getNodeTypeManager(): object
    {
        if (class_exists(self::NEOS9_REGISTRY)) {
            $contentRepositoryId = call_user_func([self::NEOS9_CONTENT_REPOSITORY_ID, 'fromString'], $this->contentRepositoryId ?: 'default');
            return $this->objectManager->get(self::NEOS9_REGISTRY)->get($contentRepositoryId)->getNodeTypeManager();
        }

        return $this->objectManager->get(self::NEOS8_NODE_TYPE_MANAGER);
    }
}
