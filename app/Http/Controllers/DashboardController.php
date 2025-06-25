<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $files = ['asn', 'country', 'city'];
        $lastModifiedDates = [];

        foreach ($files as $file) {
            $path = "mmdb/{$file}.tar.gz";
            if (Storage::exists($path)) {
                $lastModifiedDates[$file] = date('Y-m-d H:i:s', Storage::lastModified($path));
            } else {
                $lastModifiedDates[$file] = null;
            }
        }

        $downloadLinks = [];
        foreach (['asn', 'country', 'city'] as $type) {
            $path = "mmdb/{$type}.tar.gz";
            $downloadLinks[] = [
                'type' => $type,
                'label' => ucfirst($type) . ' Database',
                'url' => Storage::exists($path) 
                    ? Storage::temporaryUrl(
                        $path,
                        now()->addMinutes(30),
                        [
                            'ResponseContentType' => 'application/gzip',
                            'ResponseContentDisposition' => 'attachment; filename="'.$type.'.tar.gz"',
                        ]
                    )
                    : null
            ];
        }

        return Inertia::render('dashboard', [
            'downloadLinks' => $downloadLinks,
            'lastModifiedDates' => $lastModifiedDates,
        ]);
    }

    public function update()
    {
        try {
            Artisan::call('app:pull:mmdb');

            return redirect()->back()->with('success', 'Databases updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update databases: '.$e->getMessage());
        }
    }
}
