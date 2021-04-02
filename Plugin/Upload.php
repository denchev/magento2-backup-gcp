<?php
namespace Htmlpet\CloudStorage\Plugin;

use Closure;
use Magento\Backup\Model\Db;
use Magento\Framework\App\Filesystem\DirectoryList;

class Upload
{
    private $awsFactory;

    private $file;

    private $_filesystem;

    private $varDirectory;

    public function __construct(
        \Magento\AwsS3\Driver\AwsS3Factory $awsFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->awsFactory = $awsFactory;
        $this->file = $file;
        $this->_filesystem = $filesystem;
        $this->varDirectory = $this->_filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * Upload backup to the cloud
     */
    public function aroundCreateBackup(Db $subject, Closure $proceed, \Magento\Framework\Backup\Db\BackupInterface $backup)
    {
        $proceed($backup);

        try {
            $awsDriver = $this->awsFactory->create();

            $source = $this->varDirectory->getAbsolutePath($backup->getPath() . '/' . $backup->getFileName());
            $sourceArchive = $this->createArchive($source);

            $this->file->copy($sourceArchive, 'backups/' . basename($sourceArchive), $awsDriver);

            // Unlink local copy
            $this->varDirectory->delete($sourceArchive);
            $this->varDirectory->delete($source);
        } catch(\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    protected function createArchive(string $source): string
    {
        $archive = new \Magento\Framework\Archive\Gz();

        $destination = $source . '.gz';
        $archive->pack($source, $destination);

        return $destination;
    }
}