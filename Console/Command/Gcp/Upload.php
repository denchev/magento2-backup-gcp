<?php
namespace Htmlpet\CloudStorage\Console\Command\Gcp;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class Upload extends \Htmlpet\CloudStorage\Console\Command\AbstractUpload
{
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
        //$destination = $this->backupData->getBackupsDir() . '/123_db.sql';

        $output->writeln("Backup created");

        $output->writeln("Backup archive started");
       
        $archiveDestionation = $this->createArchive($destination);

        $output->writeln("Backup archive created");

        $output->writeln("Upload to Google Cloud begins");

        $projectId = $input->getOption('projectId');
        $bucketId = $input->getOption('bucketId');

        $this->uploadToCloud([
            'projectId' => $projectId, 
            'bucketId' => $bucketId
        ], $archiveDestionation);
        
        $output->writeln("Upload to Google Cloud finished");

        // Clean up
        unlink($archiveDestionation);
    }

    protected function uploadToCloud(array $options, string $what)
    {
        $this->client->initialize($options);
        $this->client->upload($what);
    }
}