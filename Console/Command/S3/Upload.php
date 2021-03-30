<?php
namespace Htmlpet\CloudStorage\Console\Command\S3;

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
        $this->setName('htmlpet:backup:s3:upload');
        $this->setDescription('Upload backup to Amazon S3');

        $this->addOption(
            'key',
            null,
            InputOption::VALUE_REQUIRED,
            'Key'
        );

        $this->addOption(
            'secret',
            null,
            InputOption::VALUE_REQUIRED,
            'Secret'
        );

        $this->addOption(
            'bucketId',
            null,
            InputOption::VALUE_REQUIRED,
            'BucketId'
        );

        $this->addOption(
            'region',
            null,
            InputOption::VALUE_REQUIRED,
            'Region'
        );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Backup started");

        //$destination = $this->createBackup();
        $destination = $this->backupData->getBackupsDir() . '/123_db.sql';

        $output->writeln("Backup created");

        $output->writeln("Backup archive started");
       
        $archiveDestionation = $this->createArchive($destination);

        $output->writeln("Backup archive created");

        $output->writeln("Upload to Google Cloud begins");

        $key = $input->getOption('key');
        $secret = $input->getOption('secret');
        $bucketId = $input->getOption('bucketId');
        $region = $input->getOption('region');

        $this->uploadToCloud([
            'key' => $key,
            'secret' => $secret,
            'bucketId' => $bucketId,
            'region' => $region
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