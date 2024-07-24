<?php
use Aws\S3\Exception\S3Exception;

defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/libraries/aws/vendor/autoload.php';
require_once JPATH_COMPONENT . '/libraries/dotenv/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class gglmsControllerAws extends JControllerLegacy
{

    protected $acces_id;
    protected $secret_key;
    protected $region;

    protected $s3Client;
    protected $bucket;
    
    protected $site_token;
    

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_config = new gglmsModelConfig();
        $this->site_token = $this->_config->getConfigValue('aws_token');
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../libraries/aws');
        $dotenv->load();

        $this->access_id = $_ENV['AWS_ACCESS_KEY_ID'];
        $this->secret_key = $_ENV['AWS_SECRET_ACCESS_KEY'];
        $this->region = $_ENV['AWS_DEFAULT_REGION'];
        $this->bucket = $_ENV['AWS_BUCKET'];
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => [
                'key' => $this->access_id,
                'secret' => $this->secret_key,
            ],
        ]);


    }

    public function readBuckets()
    {
        try {
            $buckets = $this->s3Client->listBuckets();
            foreach ($buckets['Buckets'] as $bucket) {
                echo $bucket['Name'];
            }
        } catch (S3Exception $e) {
            echo $e->getMessage();
        }
    }


    public function loadContents()
    {

        try {
            $this->s3Client->uploadDirectory('../mediagg', $this->bucket.'/'.$this->site_token.'/mediagg');

        } catch (S3Exception $e) {
            echo $e->getMessage();
        }

    }

}