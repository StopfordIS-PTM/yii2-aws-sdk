<?php

namespace tomyates\awssdk;
use yii\base\Component;
use Aws;

class AwsSdk extends Component
{
    /*
     * @var array specifies the AWS profile
     */
    public $profile = null;

    /*
     * @var array specifies the bucket
     */
    public $bucket = null;

    /*
     * @var string specifies the AWS region
     */
    public $region = null;

    /*
     * @var string specifies the AWS version
     */
    public $version = null;

    /*
     * @var array specifies extra params
     */
    public $extra = [];

    /**
     * @var Aws\Sdk instance
     */
    protected $_awssdk;

    /**
     * Initializes (if needed) and fetches the AWS SDK instance
     * @return Aws\Sdk instance
     */
    public function getAwsSdk()
    {
        if (empty($this->_awssdk) || !$this->_awssdk instanceof Aws\Sdk) {
            $this->setAwsSdk();
        }
        return $this->_awssdk;
    }
    /**
     * Sets the AWS SDK instance
     */
    public function setAwsSdk()
    {
        $this->_awssdk = new Aws\Sdk(array_merge([
            'profile' => $this->profile,
            'region'=>$this->region,
            'version'=>$this->version
        ],$this->extra));
    }

	/**
	* Send SMS Helper
	*/
	public function sendSMS($senderID,$message,$number)
	{
		$awssdk = $this->getAwsSdk();
		$sns = $awssdk->createSns();
		$args = [
			"Message" => $message,
			"PhoneNumber" => $number,
			"MessageAttributes" => [
				'AWS.SNS.SMS.SenderID' => [
					'DataType' => 'String',
					'StringValue'=> $senderID
				],
				'AWS.SNS.SMS.SMSType' => [
					'DataType' => 'String',
					'StringValue'=> 'Promotional'
				]
			]
		];

		return $sns->publish($args);
	}

    /**
	* Upload Base64Encoded Image String Helper
	*/
	public function uploadBase64($path,$base64string)
	{
		$awssdk = $this->getAwsSdk();
		$image_parts = explode(";base64,", $base64string);
		$image_type_aux = explode("image/", $image_parts[0]);
		$image_type = $image_type_aux[1];
		$image_base64 = $image_parts[1];
		
        $s3 = $awssdk->createS3();
    
	    // Upload data.
	    $result = $s3->putObject(array(
	        'Bucket' => $this->bucket,
	        'Key'    => $path.'.'.$image_type,
	        'Body'   =>  base64_decode($image_base64),
			'Type'	=> 'image/' . $image_type,
	        'ACL'    => 'public-read'
	    ));

        return $result;

	}


}
