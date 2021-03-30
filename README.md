# magento2-backup-gcp
A Magento 2 module that allows regular (daily, weekly, monthly) backups of data (media files, database) to a Google Cloud Platform (GCP) Cloud Storage account.

Version 1:
- CLI only
- Schedule matrix (Media and/or Database vs How often)

Version 2:
- CLI + GUI

Version 3:
- Some level of restoration capabilities

Usage:
 - Create credentials file, name it "google-cloud-keys.json" and put it inside "var/" folder. Make sure it is not readable from outside!
 - php bin/magento htmlpet:backup:gcp:upload --projectId '{projectId}' --bucketId '{bucketId}'