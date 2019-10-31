<?php
/**
 * Bdcrops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Bdcrops.com license that is
 * available through the world-wide-web at this URL:
 * https://www.bdcrops.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Bdcrops
 * @package     Bdcrops_GiftCard
 * @copyright   Copyright (c) Bdcrops (https://www.bdcrops.com/)
 * @license     https://www.bdcrops.com/LICENSE.txt
 */

namespace Bdcrops\GiftCard\Cron;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Bdcrops\GiftCard\Helper\Template;

/**
 * Class RemoveTmpImages
 * @package Bdcrops\GiftCard\Cron
 */
class RemoveTmpImages
{
    /**
     * @var Template
     */
    protected $_helper;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * RemoveTmpImages constructor.
     *
     * @param Template $templateHelper
     */
    public function __construct(Template $templateHelper)
    {
        $this->_helper = $templateHelper;
        $this->mediaDirectory = $templateHelper->getMediaDirectory();
    }

    /**
     * @throws FileSystemException
     */
    public function execute()
    {
        $this->readDir($this->_helper->getBaseTmpMediaPath());
    }

    /**
     * Read and remove images which create from before 3 days ago
     *
     * @param $path
     *
     * @throws FileSystemException
     */
    public function readDir($path)
    {
        $items = $this->mediaDirectory->read($path);
        foreach ($items as $item) {
            if ($this->mediaDirectory->isDirectory($item)) {
                $this->readDir($item);
            } else {
                $file = $this->mediaDirectory->getAbsolutePath($item);
                if (filemtime($file) < strtotime('-3days')) {
                    $this->mediaDirectory->delete($item);
                }
            }
        }
        if (!sizeof($items)) {
            $this->mediaDirectory->delete($path);
        }
    }
}
