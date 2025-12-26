@extends('layouts.admin')

@section('title')
    Mctools Settings
@endsection

@section('content-header')
    <h1>Mctools Dashboard<small>Monitor usage and configure settings.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Mctools</li>
    </ol>
@endsection

@section('content')
    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ number_format($stats['total_downloads']) }}</h3>
                    <p>Total Downloads</p>
                </div>
                <div class="icon">
                    <i class="fa fa-download"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $stats['downloads_today'] }}</h3>
                    <p>Downloads Today</p>
                </div>
                <div class="icon">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ \Illuminate\Support\Number::fileSize($stats['total_bandwidth']) }}</h3>
                    <p>Total Bandwidth</p>
                </div>
                <div class="icon">
                    <i class="fa fa-database"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ \Illuminate\Support\Number::fileSize($stats['bandwidth_today']) }}</h3>
                    <p>Bandwidth Today</p>
                </div>
                <div class="icon">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Settings Box --}}
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">General Settings</h3>
                </div>
                <form action="{{ route('admin.mctools.update') }}" method="POST">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="curseforge_api_key" class="control-label">CurseForge API Key</label>
                            <div>
                                <input type="text" name="curseforge_api_key" value="{{ $config->curseforge_api_key }}" class="form-control" />
                                <p class="text-muted"><small>Required to fetch data and install content from CurseForge. You can get a key from the <a href="https://console.curseforge.com/" target="_blank">CurseForge Console</a>.</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-primary pull-right">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Downloads by Provider --}}
        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Downloads by Provider</h3>
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="providerChart" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Popular Items --}}
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Top 10 Most Downloaded</h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Provider</th>
                                <th class="text-right">Downloads</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['popular_items'] as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td><span class="label label-primary">{{ $item->category }}</span></td>
                                    <td><span class="label {{ $item->provider === 'modrinth' ? 'label-success' : 'label-warning' }}">{{ ucfirst($item->provider) }}</span></td>
                                    <td class="text-right"><strong>{{ $item->download_count }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No downloads yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Downloads --}}
        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Recent Downloads</h3>
                </div>
                <div class="box-body">
                    <ul class="timeline timeline-inverse">
                        @forelse($stats['recent_downloads'] as $download)
                            <li>
                                <i class="fa fa-download bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fa fa-clock-o"></i> {{ $download->created_at->diffForHumans() }}</span>
                                    <h3 class="timeline-header">{{ $download->item_name }}</h3>
                                    <div class="timeline-body">
                                        <span class="label label-primary">{{ $download->category }}</span>
                                        <span class="label {{ $download->provider === 'modrinth' ? 'label-success' : 'label-warning' }}">{{ ucfirst($download->provider) }}</span>
                                        @if($download->version_name)
                                            <span class="label label-default">{{ $download->version_name }}</span>
                                        @endif
                                        @if($download->file_size > 0)
                                            <span class="label label-info">{{ \Illuminate\Support\Number::fileSize($download->file_size) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li>
                                <i class="fa fa-info bg-gray"></i>
                                <div class="timeline-item">
                                    <div class="timeline-body text-muted">
                                        No downloads yet
                                    </div>
                                </div>
                            </li>
                        @endforelse
                        <li>
                            <i class="fa fa-clock-o bg-gray"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Downloads by Category --}}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Downloads by Category</h3>
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="categoryChart" style="height:250px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Provider Chart
        const providerData = @json($stats['downloads_by_provider']);
        const providerLabels = providerData.map(item => item.provider.charAt(0).toUpperCase() + item.provider.slice(1));
        const providerCounts = providerData.map(item => item.count);

        new Chart(document.getElementById('providerChart'), {
            type: 'doughnut',
            data: {
                labels: providerLabels,
                datasets: [{
                    data: providerCounts,
                    backgroundColor: ['#00c0ef', '#f39c12']
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true
            }
        });

        // Category Chart
        const categoryData = @json($stats['downloads_by_category']);
        const categoryLabels = categoryData.map(item => item.category);
        const categoryCounts = categoryData.map(item => item.count);

        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Downloads',
                    data: categoryCounts,
                    backgroundColor: '#3c8dbc'
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
