<?php
namespace Taichuchu\AwsSms\Sms;


use Aws\Sns\SnsClient;
use Illuminate\Support\ServiceProvider;

class SmsProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton(Sms::class, function () {
            $config = config('services.aws.sms');

            $client = new SnsClient([
                'credentials' => [
                    'key' => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => 'latest',
            ]);

            return new Sms($client, $config['from'], $config['max_price_usd']);
        });
    }
}