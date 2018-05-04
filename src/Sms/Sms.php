<?php
namespace Taichuchu\AwsSms\Sms;

use Aws\Sns\SnsClient;
use function GuzzleHttp\Promise\settle;
use Illuminate\Support\Facades\Log;

class Sms
{
    private $snsClient;

    private $from;

    private $maxPriceUsed;

    public function __construct(SnsClient $snsClient, string $from, string $maxPriceUsed)
    {
        $this->snsClient = $snsClient;
        $this->from = $from;
        $this->maxPriceUsed = $maxPriceUsed;
    }

    public function sendSms(Message $message, string $mobile)
    {
        $arguments = [
            'Message' => $message->content,
            'PhoneNumber' => $mobile,
            'MessageAttributes' => [
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' =>  $this->from,
                ],
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' =>  $message->type
                ],
                'AWS.SNS.SMS.MaxPrice' => [
                    'DataType' => 'String',
                    'StringValue' =>  $this->maxPriceUsed
                ]
            ]
        ];

        return $this->snsClient->publish($arguments);
    }

    public function sendSmsBatch(Message $message, array $mobiles)
    {
        $arguments = [
            'Message' => $message->content,
            'PhoneNumber' => null,
            'MessageAttributes' => [
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' =>  $this->from,
                ],
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' =>  $message->type
                ],
                'AWS.SNS.SMS.MaxPrice' => [
                    'DataType' => 'String',
                    'StringValue' =>  $this->maxPriceUsed
                ]
            ]
        ];

        $promis = [];

        foreach ($mobiles as $mobile) {
            try {
                $arguments['PhoneNumber'] = $mobile;
                $promis[] = $this->snsClient->publishAsync($arguments);
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }

        return settle($promis)->wait(true);
    }
}
