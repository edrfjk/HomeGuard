@extends('layouts.app')

@section('title', $device->name . ' — HomeGuard')
@section('page-title', $device->name)
@section('page-subtitle', $device->location . ' · ' . ($device->status === 'online' ? 'Live' : 'Offline'))

@section('content')
@php $latest = $latestReading; @endphp

<div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Back --}}
    <a href="/devices" class="btn btn-ghost btn-sm" style="align-self:flex-start;">
        <i class="fas fa-arrow-left"></i> Back to Devices
    </a>

    {{-- ── Live Sensor Cards ── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;" id="liveCards">

        {{-- Status --}}
        <div class="stat-card {{ $device->status === 'online' ? 'green' : 'blue' }} fade-up">
            <div class="stat-icon {{ $device->status === 'online' ? 'green' : 'blue' }}">
                <i class="fas fa-signal"></i>
            </div>
            <div class="stat-label">Status</div>
            <div style="margin:10px 0 4px;">
                <span class="status-pill {{ $device->status }}">
                    <span class="status-dot {{ $device->status }}"></span>
                    {{ ucfirst($device->status) }}
                </span>
            </div>
            <div class="stat-sub mono" style="font-size:10px;">
                @if($device->last_seen)
                    Seen {{ $device->last_seen->diffForHumans() }}
                @else
                    Never connected
                @endif
            </div>
        </div>

        {{-- Temperature --}}
        <div class="stat-card orange fade-up" style="animation-delay:.06s">
            <div class="stat-icon orange"><i class="fas fa-thermometer-half"></i></div>
            <div class="stat-label">Temperature</div>
            <div class="stat-value orange" id="liveTemp">{{ $latest ? number_format($latest->temperature, 1) : '--' }}<span style="font-size:18px;">°C</span></div>
            @if($threshold && $latest)
                <div class="stat-sub">
                    @if($latest->temperature >= $threshold->temp_critical)
                        <span style="color:var(--danger);">⚠ Critical (>{{ $threshold->temp_critical }}°)</span>
                    @elseif($latest->temperature >= $threshold->temp_warning)
                        <span style="color:var(--warn);">! Warning (>{{ $threshold->temp_warning }}°)</span>
                    @else
                        <i class="fas fa-check"></i> Normal range
                    @endif
                </div>
            @endif
        </div>

        {{-- Humidity --}}
        <div class="stat-card blue fade-up" style="animation-delay:.12s">
            <div class="stat-icon blue"><i class="fas fa-droplet"></i></div>
            <div class="stat-label">Humidity</div>
            <div class="stat-value blue" id="liveHumid">{{ $latest ? number_format($latest->humidity, 1) : '--' }}<span style="font-size:18px;">%</span></div>
            @if($threshold && $latest)
                <div class="stat-sub">
                    @if($latest->humidity >= $threshold->humidity_critical)
                        <span style="color:var(--danger);">⚠ Critical</span>
                    @elseif($latest->humidity >= $threshold->humidity_warning)
                        <span style="color:var(--warn);">! Warning</span>
                    @else
                        <i class="fas fa-check"></i> Normal range
                    @endif
                </div>
            @endif
        </div>

        {{-- Gas Level --}}
        <div class="stat-card red fade-up" style="animation-delay:.18s">
            <div class="stat-icon red"><i class="fas fa-fire-flame-curved"></i></div>
            <div class="stat-label">Gas Level</div>
            <div class="stat-value red" id="liveGas">{{ $latest ? round($latest->gas_level) : '--' }}<span style="font-size:14px;"> ppm</span></div>
            @if($threshold && $latest)
                <div class="stat-sub">
                    @if($latest->gas_level >= $threshold->gas_critical)
                        <span style="color:var(--danger);">⚠ Critical</span>
                    @elseif($latest->gas_level >= $threshold->gas_warning)
                        <span style="color:var(--warn);">! Warning</span>
                    @else
                        <i class="fas fa-check"></i> Normal range
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── Charts Section ── --}}
    <div class="card card-p fade-up" style="animation-delay:.24s">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:#fff;margin:0;">
                    <i class="fas fa-chart-line" style="color:var(--accent);margin-right:8px;"></i>Sensor History
                </h3>
                <p style="font-size:11px;color:var(--text-muted);margin:4px 0 0;font-family:'Space Mono',monospace;" id="chartSubtitle">Loading...</p>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                <button onclick="loadChart(24, this)"   class="range-btn active btn btn-sm">24 H</button>
                <button onclick="loadChart(168, this)"  class="range-btn btn btn-ghost btn-sm">7 D</button>
                <button onclick="loadChart(720, this)"  class="range-btn btn btn-ghost btn-sm">30 D</button>
                <button onclick="loadChart(1, this)"  class="range-btn btn btn-ghost btn-sm" style="display:none" id="refreshBtn">
                    <i class="fas fa-rotate-right"></i>
                </button>
            </div>
        </div>

        <div id="chartLoading" style="text-align:center;padding:40px;display:none;">
            <i class="fas fa-spinner fa-spin" style="font-size:28px;color:var(--accent);"></i>
            <p style="color:var(--text-muted);margin-top:12px;font-size:13px;">Loading data...</p>
        </div>

        <div id="chartError" style="text-align:center;padding:30px;display:none;">
            <i class="fas fa-triangle-exclamation" style="font-size:28px;color:var(--warn);"></i>
            <p style="color:var(--text-muted);margin-top:10px;font-size:13px;">Failed to load chart data</p>
        </div>

        <div id="chartEmpty" style="text-align:center;padding:40px;display:none;">
            <i class="fas fa-satellite-dish" style="font-size:32px;color:var(--text-dim);"></i>
            <p style="color:var(--text-muted);margin-top:12px;font-size:13px;">No readings yet in this time range</p>
        </div>

        <div id="chartsGrid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;">
            <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:10px;padding:16px;">
                <div style="font-size:11px;color:var(--warn);font-family:'Space Mono',monospace;margin-bottom:12px;">
                    <i class="fas fa-thermometer-half" style="margin-right:6px;"></i>TEMPERATURE (°C)
                </div>
                <div style="height:200px;"><canvas id="tempChart"></canvas></div>
            </div>
            <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:10px;padding:16px;">
                <div style="font-size:11px;color:var(--accent);font-family:'Space Mono',monospace;margin-bottom:12px;">
                    <i class="fas fa-droplet" style="margin-right:6px;"></i>HUMIDITY (%)
                </div>
                <div style="height:200px;"><canvas id="humidChart"></canvas></div>
            </div>
            <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:10px;padding:16px;">
                <div style="font-size:11px;color:var(--danger);font-family:'Space Mono',monospace;margin-bottom:12px;">
                    <i class="fas fa-fire-flame-curved" style="margin-right:6px;"></i>GAS LEVEL (ppm)
                </div>
                <div style="height:200px;"><canvas id="gasChart"></canvas></div>
            </div>
        </div>
    </div>

    {{-- ── Latest Camera Image ── --}}
    @if($latestImage)
    <div class="card card-p fade-up" style="animation-delay:.3s">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:#fff;margin:0;">
                    <i class="fas fa-camera" style="color:var(--accent);margin-right:8px;"></i>Camera Captures
                </h3>
                <p style="font-size:11px;color:var(--text-muted);margin:4px 0 0;font-family:'Space Mono',monospace;">
                    {{ $device->cameraImages()->count() }} total captures
                </p>
            </div>
            <a href="{{ route('camera.gallery', $device->id) }}" class="btn btn-ghost btn-sm">
                <i class="fas fa-images"></i> Gallery
            </a>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;">
            @foreach($device->cameraImages()->latest()->take(6)->get() as $img)
                <div style="position:relative;aspect-ratio:16/9;border-radius:8px;overflow:hidden;cursor:pointer;border:1px solid var(--border);"
                     onclick="openModal('{{ $img->getImageUrl() }}', '{{ $img->created_at->format('M d, Y - h:i A') }}')">
                    <img src="{{ $img->getImageUrl() }}" style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                         onmouseenter="this.style.transform='scale(1.05)'" onmouseleave="this.style.transform=''" loading="lazy">
                    @php $bc = match($img->trigger_type){ 'alert'=>'var(--danger)', 'manual'=>'var(--accent)', default=>'var(--warn)' }; @endphp
                    <div style="position:absolute;top:6px;left:6px;background:{{ $bc }};color:{{ $img->trigger_type==='manual'?'#000':'#fff' }};padding:2px 7px;border-radius:4px;font-size:9px;font-weight:700;font-family:'Space Mono',monospace;">
                        {{ strtoupper($img->trigger_type) }}
                    </div>
                    <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,.8));padding:8px 8px 6px;font-size:10px;color:#fff;font-family:'Space Mono',monospace;">
                        {{ $img->created_at->diffForHumans() }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Recent Alerts ── --}}
    <div class="card card-p fade-up" style="animation-delay:.36s">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
            <h3 style="font-size:15px;font-weight:700;color:#fff;margin:0;">
                <i class="fas fa-bell" style="color:var(--warn);margin-right:8px;"></i>Recent Alerts
            </h3>
            <a href="/alerts?device={{ $device->id }}" class="btn btn-ghost btn-sm">View all</a>
        </div>

        @forelse($alerts as $alert)
            <a href="{{ route('alerts.show', $alert) }}" class="alert-item {{ $alert->severity }}">
                <span style="font-size:16px;">{{ $alert->severity === 'critical' ? '🚨' : ($alert->severity === 'warning' ? '⚠️' : 'ℹ️') }}</span>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
                        <span style="font-size:13px;font-weight:600;color:#fff;">{{ str_replace('_', ' ', ucfirst($alert->type)) }}</span>
                        <span class="alert-pill {{ $alert->severity }}">{{ strtoupper($alert->severity) }}</span>
                        @if($alert->status === 'resolved')
                            <span class="alert-pill info">RESOLVED</span>
                        @endif
                    </div>
                    <div style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $alert->message }}</div>
                    <div style="font-size:10px;color:var(--text-dim);margin-top:3px;font-family:'Space Mono',monospace;">{{ $alert->created_at->diffForHumans() }}</div>
                </div>
            </a>
        @empty
            <div style="text-align:center;padding:24px;color:var(--text-muted);">
                <i class="fas fa-shield-halved" style="font-size:28px;display:block;color:var(--safe);margin-bottom:8px;"></i>
                <p style="font-size:13px;margin:0;">No alerts for this device</p>
            </div>
        @endforelse
    </div>

    {{-- ── Safety Thresholds ── --}}
    @if($threshold)
    <div class="card card-p fade-up" style="animation-delay:.42s">
        <h3 style="font-size:15px;font-weight:700;color:#fff;margin:0 0 18px;">
            <i class="fas fa-sliders" style="color:var(--accent);margin-right:8px;"></i>Safety Thresholds
        </h3>
        <form method="POST" action="{{ route('devices.updateThresholds', $device) }}">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;">
                {{-- Temperature --}}
                <div>
                    <div style="font-size:11px;color:var(--warn);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-thermometer-half" style="margin-right:6px;"></i>TEMPERATURE (°C)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="form-group" style="margin:0">
                            <label class="form-label">Warning</label>
                            <input type="number" name="temp_warning" value="{{ $threshold->temp_warning }}" step="0.5" class="form-control">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label class="form-label">Critical</label>
                            <input type="number" name="temp_critical" value="{{ $threshold->temp_critical }}" step="0.5" class="form-control">
                        </div>
                    </div>
                </div>
                {{-- Humidity --}}
                <div>
                    <div style="font-size:11px;color:var(--accent);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-droplet" style="margin-right:6px;"></i>HUMIDITY (%)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="form-group" style="margin:0">
                            <label class="form-label">Warning</label>
                            <input type="number" name="humidity_warning" value="{{ $threshold->humidity_warning }}" step="1" class="form-control">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label class="form-label">Critical</label>
                            <input type="number" name="humidity_critical" value="{{ $threshold->humidity_critical }}" step="1" class="form-control">
                        </div>
                    </div>
                </div>
                {{-- Gas --}}
                <div>
                    <div style="font-size:11px;color:var(--danger);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-fire-flame-curved" style="margin-right:6px;"></i>GAS LEVEL (ppm)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="form-group" style="margin:0">
                            <label class="form-label">Warning</label>
                            <input type="number" name="gas_warning" value="{{ $threshold->gas_warning }}" step="10" class="form-control">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label class="form-label">Critical</label>
                            <input type="number" name="gas_critical" value="{{ $threshold->gas_critical }}" step="10" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-top:20px;display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Thresholds
                </button>
            </div>
        </form>
    </div>
    @endif

</div>

{{-- Image Modal --}}
<div id="imgModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.9);align-items:center;justify-content:center;padding:20px;" onclick="closeModal()">
    <img id="imgModalSrc" style="max-width:90vw;max-height:85vh;border-radius:10px;object-fit:contain;">
    <div id="imgModalTime" style="position:absolute;bottom:30px;left:50%;transform:translateX(-50%);color:#fff;font-size:12px;font-family:'Space Mono',monospace;background:rgba(0,0,0,.6);padding:6px 14px;border-radius:99px;"></div>
    <button onclick="closeModal()" style="position:absolute;top:20px;right:20px;background:rgba(255,255,255,.1);border:none;color:#fff;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:16px;">✕</button>
</div>

@endsection

@section('scripts')
<script>
const DEVICE_ID = {{ $device->id }};
const CHART_URL = '/device/' + DEVICE_ID + '/chart-data';

Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = 'rgba(255,255,255,0.05)';

let tempChart, humidChart, gasChart;

function makeChart(id, label, color, data, labels) {
    const ctx = document.getElementById(id).getContext('2d');
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label,
                data,
                borderColor: color,
                backgroundColor: color.replace(')', ', 0.07)').replace('rgb', 'rgba'),
                borderWidth: 2,
                pointRadius: data.length > 100 ? 0 : 3,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            animation: { duration: 400 },
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false, backgroundColor: '#0f1823', titleColor: '#e2e8f0', bodyColor: '#94a3b8', borderColor: 'rgba(34,211,238,.2)', borderWidth: 1 } },
            scales: {
                x: { ticks: { maxTicksLimit: 6, font: { family: 'Space Mono', size: 10 } }, grid: { color: 'rgba(255,255,255,0.03)' } },
                y: { ticks: { font: { family: 'Space Mono', size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' } }
            }
        }
    });
}

function destroyCharts() {
    [tempChart, humidChart, gasChart].forEach(c => { if(c) c.destroy(); });
}

async function loadChart(hours, btn) {
    // Update button styles
    document.querySelectorAll('.range-btn').forEach(b => {
        b.className = 'range-btn btn btn-ghost btn-sm';
    });
    if (btn) { btn.className = 'range-btn active btn btn-sm'; }

    document.getElementById('chartLoading').style.display = 'block';
    document.getElementById('chartsGrid').style.display = 'none';
    document.getElementById('chartError').style.display = 'none';
    document.getElementById('chartEmpty').style.display = 'none';

    try {
        const res = await fetch(`${CHART_URL}?hours=${hours}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();

        document.getElementById('chartLoading').style.display = 'none';

        if (!json.readings || json.readings.length === 0) {
            document.getElementById('chartEmpty').style.display = 'block';
            document.getElementById('chartSubtitle').textContent = '0 readings';
            return;
        }

        const labels = json.readings.map(r => r.time);
        const temps  = json.readings.map(r => r.temperature);
        const humids = json.readings.map(r => r.humidity);
        const gases  = json.readings.map(r => r.gas_level);

        destroyCharts();
        document.getElementById('chartsGrid').style.display = 'grid';

        tempChart  = makeChart('tempChart',  'Temperature (°C)', 'rgb(251,191,36)', temps, labels);
        humidChart = makeChart('humidChart', 'Humidity (%)',     'rgb(34,211,238)',  humids, labels);
        gasChart   = makeChart('gasChart',   'Gas (ppm)',        'rgb(248,113,113)', gases, labels);

        const hLabel = hours <= 24 ? `Last ${hours}h` : hours <= 168 ? 'Last 7 days' : 'Last 30 days';
        document.getElementById('chartSubtitle').textContent = `${json.count} readings · ${hLabel}`;
    } catch(e) {
        document.getElementById('chartLoading').style.display = 'none';
        document.getElementById('chartError').style.display = 'block';
        console.error('Chart load error:', e);
    }
}

// Image modal
function openModal(url, time) {
    const m = document.getElementById('imgModal');
    document.getElementById('imgModalSrc').src = url;
    document.getElementById('imgModalTime').textContent = time;
    m.style.display = 'flex';
}
function closeModal() {
    document.getElementById('imgModal').style.display = 'none';
}

// Auto-refresh live values every 30s
async function refreshLive() {
    try {
        const res = await fetch(`/api/device/{{ $device->device_id }}/latest-reading`);
        if (!res.ok) return;
        const data = await res.json();
        if (data.reading) {
            const r = data.reading;
            document.getElementById('liveTemp').innerHTML  = parseFloat(r.temperature).toFixed(1) + '<span style="font-size:18px;">°C</span>';
            document.getElementById('liveHumid').innerHTML = parseFloat(r.humidity).toFixed(1)    + '<span style="font-size:18px;">%</span>';
            document.getElementById('liveGas').innerHTML   = Math.round(r.gas_level)              + '<span style="font-size:14px;"> ppm</span>';
        }
    } catch(e) { /* silent */ }
}

// Init
loadChart(24, document.querySelector('.range-btn.active'));
setInterval(refreshLive, 30000);
</script>
@endsection
