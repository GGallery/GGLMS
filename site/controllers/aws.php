<?php
defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/libraries/aws/vendor/autoload.php';
require_once JPATH_COMPONENT . '/libraries/dotenv/vendor/autoload.php';

require_once JPATH_COMPONENT . '/models/config.php';
require_once JPATH_ADMINISTRATOR . '/components/com_gglms/models/awstoken.php';


use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class gglmsControllerAws extends JControllerLegacy
{
    protected $_japp;
    protected $_params;
    protected $_db;
    protected $_config;
    protected $s3Client;
    protected $bucket;
    protected $bucketEndpoint;
    protected $site_token;


    public function __construct($config = array()){
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_db = JFactory::getDbo();
        $this->_config = new gglmsModelConfig();
        $this->site_token = $this->_config->getConfigValue('aws_token');

        if($this->site_token=='') {
            $_aws = new gglmsModelAwsToken();
            $this->site_token = $_aws->setToken();
        }

        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../libraries/aws');
        $dotenv->load();

        $access_id = $_ENV['AWS_ACCESS_KEY_ID'];
        $secret_key = $_ENV['AWS_SECRET_ACCESS_KEY'];
        $region = $_ENV['AWS_DEFAULT_REGION'];
        $this->bucketEndpoint = $_ENV['AWS_BUCKET_ENDPOINT'];
        $this->bucket = $_ENV['AWS_BUCKET'];
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'endpoint' => $this->bucketEndpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $access_id,
                'secret' => $secret_key,
            ],
        ]);
    }

    public function readBuckets(){
        try {
            $buckets = $this->s3Client->listBuckets();
            foreach ($buckets['Buckets'] as $bucket) {
                echo $bucket['Name'];
            }
        } catch (S3Exception $e) {
            echo $e->getMessage();
        }
    }


//    public function loadContents($db_host = null,
//                                    $db_user = null,
//                                    $db_password = null,
//                                    $db_database = null,
//                                    $db_prefix = null,
//                                    $db_driver = null){
//
//        $db_option = array();
//        try {
//            if (!is_null($db_host)||!is_null($db_driver)) {
//
//                $db_option['driver'] = $db_driver;
//                $db_option['host'] = $db_host;
//                $db_option['user'] = $db_user;
//                $db_option['password'] = utilityHelper::encrypt_decrypt('decrypt', $db_password, "GGallery00!", "GGallery00!");
//                $db_option['database'] = $db_database;
//                $db_option['prefix'] = $db_prefix;
//
//                $this->_db = JDatabaseDriver::getInstance($db_option);
//                $this->site_token = $this->getSiteToken();
//            }
//            $pathRoot = utilityHelper::getSiteRoot();
//            $this->s3Client->uploadDirectory($pathRoot.'/mediagg',
//                $this->bucket.'/'.$this->site_token.'/mediagg',
//                [
//                    'before_upload' => function (\Aws\Command $command) {
//                        $command['ACL'] = 'public-read'; // Set the ACL to public-read
//                    }
//                ]);
//            echo "Directory uploaded successfully.\n";
//            return 1;
//
//        } catch (S3Exception $e) {
//            echo $e->getMessage();
//            return 0;
//        }
//
//    }

    public function loadContents($db_host = null,
                                 $db_user = null,
                                 $db_password = null,
                                 $db_database = null,
                                 $db_prefix = null,
                                 $db_driver = null)
    {
        $db_option = array();
        try {
            if (!is_null($db_host) || !is_null($db_driver)) {

                $db_option['driver'] = $db_driver;
                $db_option['host'] = $db_host;
                $db_option['user'] = $db_user;
                $db_option['password'] = utilityHelper::encrypt_decrypt('decrypt', $db_password, "GGallery00!", "GGallery00!");
                $db_option['database'] = $db_database;
                $db_option['prefix'] = $db_prefix;

                $this->_db = JDatabaseDriver::getInstance($db_option);
                $this->site_token = $this->getSiteToken();
            }

            $pathRoot = utilityHelper::getSiteRoot();
            $directoryPath = $pathRoot . '/mediagg';
            $bucketPath = $this->bucket . '/' . $this->site_token . '/mediagg';

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directoryPath, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                $keyName = str_replace($directoryPath . '/', '', $filePath);

                $this->s3Client->putObject([
                    'Bucket' => $this->bucket,
                    'Key' => $bucketPath . '/' . $keyName,
                    'SourceFile' => $filePath,
                    'ACL' => 'public-read',
                ]);
            }

            echo "Directory uploaded successfully\n";
            return 1;

        } catch (S3Exception $e) {
            echo "Error uploading directory: " . $e->getMessage();
            return 0;
        }
    }

    private function getSiteToken(){
        try {
            $query = $this->_db->getQuery(true)
                ->select('config_value')
                ->from('#__gg_configs')
                ->where("config_key='aws_token'");

            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            if($result === false){
                throw new Exception('Non riesco a leggere il token aws', E_USER_ERROR);
            }

            return $result;
        } catch (Exception $e) {
            DEBUGG::error($e, 'getConfigValue');
        }

        return false;
    }

    public function getAwsMediaUrl(){
        $s3Url = 'https://bucket-gal.s3.us-east-2.amazonaws.com/'.$this->site_token;

        return $s3Url;
    }

}
