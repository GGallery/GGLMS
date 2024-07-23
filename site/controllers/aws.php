<?php
use Aws\S3\Exception\S3Exception;

defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/libraries/aws/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class gglmsControllerAws extends JControllerLegacy{

    protected $acces_id;
    protected $secret_key;
    protected $region;
    /*
    protected $site_token;
    */

    public function __construct($config = array()){
        echo'   <script> console.log("wooo1111") </script>';
        parent::__construct($config);

        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_config = new gglmsModelConfig();
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $this->access_id = getenv('AWS_ACCESS_KEY_ID');
        $this->secret_key =getenv('AWS_SECRET_ACCESS_KEY');
        $this->region = getenv('AWS_DEFAULT_REGION');


    }

    public function readBuckets(){
        echo'   <script> console.log("wooo2222") </script>';
       try{ $s3Client = new S3Client([
        'version' => 'latest',
        'region'  => $this->region,
        'credentials' => [
            'key'    => $this->access_id ,
            'secret' => $this->secret_key,
        ],
    ]);
        
        $buckets = $s3Client->listBuckets();
        foreach ($buckets['$Buckets'] as $bucket) {
            echo $bucket['Name'];
        }}
        catch(S3Exception $e){
            echo $e->getMessage();
        }
    }

}