# AWS SDK for Yii2 - Use Amazon Web Services in your Yii2 project

Access to the SDK is achieved via roles rather than access keys.
- To use SNS service on your Elastic Beanstalk instance, create a policy that gives access to SNS Publish (example at bottom) and add to the role of your elastic-beanstalk-ec2-role.

- For local development, you must add the profile from 1Password into your ~/.aws/credentials

# Installation
Add the following to your `composer.json` file to look for packages in this repo.

```
"repositories": [
    {
        "type": "vcs",
        "url": "git@gitlab.tariffstreet.com:tariff-street/yii2-aws-sdk.git"
    }
],
```

Then..

`composer require human/yii2-aws-sdk`

# Set up

Usage
-----

To use this extension, simply add the following code in your application configuration local and prod:

```php
return [
    //....
    'components' => [
        'awssdk' => [
            'class' => 'human\awssdk\AwsSdk',
            'region' => 'eu-west-1', //i.e.: 'us-east-1'
            'version' => 'latest', //i.e.: 'latest'
            'profile' => 'kyb-sns', //for local testing only, not needed on production
            'bucket' => 'kybee-bucket',
        ],
    ],
];
```
Send SMS:
```
try {
		$result = Yii::$app->awssdk->sendSMS('HumanGarage','Hi there. This is a test SMS from kybee api','+447973470928');
	}
	catch (\Exception $e)
	{
		$result = $e->getMessage();
	}

```




Example AWS policy for publishing SNS
```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "Stmt1504536945000",
            "Effect": "Allow",
            "Action": [
                "sns:Publish"
            ],
            "Resource": [
                "*"
            ]
        }
    ]
}
```


Example AWS policy for uploading to S3
```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:PutObject",
                "s3:PutObjectAcl"
            ],
            "Resource": [
                "arn:aws:s3:::kybee-bucket/*",
                "arn:aws:s3:::kybee-bucket"
            ]
        }
    ]
}
```