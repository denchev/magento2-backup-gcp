<?php
namespace Htmlpet\CloudStorage\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Htmlpet\CloudStorage\Client\GoogleCloudPlatform;

/**
 * To do: Enable Object versioning
 */

class Upload extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Backup\Helper\Data
     */
    private $backupData;

    /**
     * @var \Magento\Framework\Backup\Factory
     */
    private $backupFactory;

    /**
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param \Magento\Catalog\Model\ResourceModel\Attribute $attributeResource
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        \Magento\Framework\Backup\Factory $backupFactory,
        \Magento\Backup\Helper\Data $backupData,
        GoogleCloudPlatform $gcp
    ) {
        $this->backupFactory = $backupFactory;
        $this->backupData = $backupData;
        $this->gcp = $gcp;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('htmlpet:backup:gcp:upload');
        $this->setDescription('Upload backup to GCP');

        $this->addOption(
            'projectId',
            null,
            InputOption::VALUE_REQUIRED,
            'ProjectId'
        );

        $this->addOption(
            'bucketId',
            null,
            InputOption::VALUE_REQUIRED,
            'BucketId'
        );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Backup started");

        $destination = $this->createBackup();

        $output->writeln("Backup created");

        $output->writeln("Backup archive started");
       
        $archiveDestionation = $this->createArchive($destination);

        $output->writeln("Backup archive created");

        $output->writeln("Upload to Google Cloud begins");

        $projectId = $input->getOption('projectId');
        $bucketId = $input->getOption('bucketId');

        $this->uploadToCloud($projectId, $bucketId, $archiveDestionation);
        
        $output->writeln("Upload to Google Cloud finished");

        // Clean up
        unlink($archiveDestionation);
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

    protected function uploadToCloud(string $projectId, string $bucketId, string $what)
    {
        $this->gcp->initialize($projectId, $bucketId);
        $this->gcp->upload($what);
    }

    protected function createArchive(string $source)
    {
        $archive = new \Magento\Framework\Archive\Gz();

        $destination = $source . '.gz';
        $archive->pack($source, $destination);

        return $destination;
    }
}