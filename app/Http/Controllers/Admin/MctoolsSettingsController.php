<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Pterodactyl\Models\MctoolsConfig;
use Pterodactyl\Models\MctoolsDownload;
use Pterodactyl\Http\Controllers\Controller;
use Prologue\Alerts\AlertsMessageBag;

class MctoolsSettingsController extends Controller
{
    public function __construct(private AlertsMessageBag $alerts)
    {
    }

    public function index()
    {
        $config = MctoolsConfig::first() ?? new MctoolsConfig();
        
        // Calculate statistics
        $totalDownloads = MctoolsDownload::count();
        $totalBandwidth = MctoolsDownload::sum('file_size');
        $downloadsToday = MctoolsDownload::whereDate('created_at', today())->count();
        $bandwidthToday = MctoolsDownload::whereDate('created_at', today())->sum('file_size');
        
        // Most popular items
        $popularItems = MctoolsDownload::select('item_name', 'provider', 'category')
            ->selectRaw('COUNT(*) as download_count')
            ->groupBy('item_name', 'provider', 'category')
            ->orderByDesc('download_count')
            ->limit(10)
            ->get();
        
        // Downloads by category
        $downloadsByCategory = MctoolsDownload::select('category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category')
            ->get();
        
        // Downloads by provider
        $downloadsByProvider = MctoolsDownload::select('provider')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('provider')
            ->get();
        
        // Recent downloads
        $recentDownloads = MctoolsDownload::orderByDesc('created_at')
            ->limit(15)
            ->get();
        
        return view('admin.mctools.index', [
            'config' => $config,
            'stats' => [
                'total_downloads' => $totalDownloads,
                'total_bandwidth' => $totalBandwidth,
                'downloads_today' => $downloadsToday,
                'bandwidth_today' => $bandwidthToday,
                'popular_items' => $popularItems,
                'downloads_by_category' => $downloadsByCategory,
                'downloads_by_provider' => $downloadsByProvider,
                'recent_downloads' => $recentDownloads,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'curseforge_api_key' => 'nullable|string',
        ]);

        $config = MctoolsConfig::first() ?? new MctoolsConfig();
        $config->curseforge_api_key = $request->input('curseforge_api_key');
        $config->save();

        $this->alerts->success('Mctools settings have been updated.')->flash();

        return redirect()->route('admin.mctools');
    }
}
