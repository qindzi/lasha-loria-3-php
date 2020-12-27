<?php

namespace StephaneCoinon\Mailtrap;

use Illuminate\Support\ServiceProvider;
use StephaneCoinon\Mailtrap\Client;
use StephaneCoinon\Mailtrap\Model;

class MailtrapServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $client = new Client(config('services.mailtrap.token'));
        Model::boot($client);
        Model::returnArraysAsLaravelCollections();
    }

    public function register()
    {
        //
    }
}
