<?php

use App\Console\Commands\DownloadMmdbDatabases;
use Illuminate\Support\Facades\Schedule;

Schedule::command(DownloadMmdbDatabases::class)
    ->description('Download mmdb databases from MaxMind')
    ->weekly()
    ->runInBackground();