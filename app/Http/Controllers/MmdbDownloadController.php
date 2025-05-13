<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MmdbDownloadController extends Controller
{
    const DOWNLOAD_URLS = [
        'asn' => 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-ASN&license_key={license_key}&suffix=tar.gz',
        'city' => 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key={license_key}&suffix=tar.gz',
        'country' => 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key={license_key}&suffix=tar.gz',
    ];

    public function downloadAll()
    {
        $licenseKey = env('GEOIP_MAXMIND_LICENSE_KEY');
        if (empty($licenseKey)) {
            throw new \RuntimeException('MaxMind license key not configured');
        }

        Storage::makeDirectory('mmdb');

        foreach (self::DOWNLOAD_URLS as $type => $url) {
            $this->downloadAndStore(
                str_replace('{license_key}', $licenseKey, $url),
                $type
            );
        }

        return response()->json(['message' => 'Files downloaded successfully'], 200);
    }

    private function downloadAndStore(string $url, string $type)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'mmdb_');
        try {
            $response = Http::timeout(120)->send('GET', $url, [
                'sink' => $tempFile,
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException("HTTP request failed with status {$response->status()}");
            }

            // Verify downloaded file exists and has content
            if (! file_exists($tempFile) || filesize($tempFile) === 0) {
                throw new \RuntimeException('Downloaded file is empty or missing');
            }

            $destination = "mmdb/{$type}.tar.gz";
            Storage::put($destination, file_get_contents($tempFile));

            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to process {$type} database: " . $e->getMessage());
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function downloadFile(Request $request, string $type)
    {
        // Ensure the user is authenticated via Sanctum
        $request->user(); // This will throw an exception if the user is not authenticated

        $filePath = "mmdb/{$type}.tar.gz";

        if (! Storage::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->streamDownload(function () use ($filePath) {
            $stream = Storage::readStream($filePath);
            fpassthru($stream);
            fclose($stream);
        }, "{$type}.tar.gz");
    }
}
