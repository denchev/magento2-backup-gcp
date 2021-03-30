<?php
namespace Htmlpet\CloudStorage\Console\Command;

/**
 * To do: 
 * 
 * Enable Object versioning in GCP
 * Use Manager to get the proper cloud client
 * 
 */
class AbstractUpload extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Backup\Helper\Data
     */
    protected $backupData;

    /**
     * @var \Magento\Framework\Backup\Factory
     */
    protected $backupFactory;

    /**
     * @var \Htmlpet\CloudStorage\Api\Client\ClientInterface
     */
    protected $client;

    public function __construct(
        \Magento\Framework\Backup\Factory $backupFactory,
        \Magento\Backup\Helper\Data $backupData,
        \Htmlpet\CloudStorage\Api\Client\ClientInterface $client
    )
    {
        $this->backupFactory = $backupFactory;
        $this->backupData = $backupData;
        $this->client = $client;

        parent::__construct();
    }

    protected function createBackup(): string
    {
        $type = \Magento\Framework\Backup\Factory::TYPE_DB;
        $time = time();

        $backupManager = $this->backupFactory->create(
            $type
        )->setBackupExtension(
            $this->backupData->getExtensionByType($type)
        )->setTime(
            $time
        )->setBackupsDir(
            $this->backupData->getBackupsDir()
        );

        $backupManager->create();

        $destination = $this->backupData->getBackupsDir() . '/' . $time . '_db.sql';

        return $destination;
    }

    protected function createArchive(string $source)
    {
        $archive = new \Magento\Framework\Archive\Gz();

        $destination = $source . '.gz';
        $archive->pack($source, $destination);

        return $destination;
    }
}