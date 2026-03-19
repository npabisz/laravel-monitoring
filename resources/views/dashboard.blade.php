<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Dashboard</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --card-hover: #253349;
            --border: #334155;
            --text: #e2e8f0;
            --text-muted: #94a3b8;
            --green: #22c55e;
            --green-bg: rgba(34,197,94,0.1);
            --red: #ef4444;
            --red-bg: rgba(239,68,68,0.1);
            --yellow: #eab308;
            --yellow-bg: rgba(234,179,8,0.1);
            --blue: #3b82f6;
            --blue-bg: rgba(59,130,246,0.1);
            --purple: #a855f7;
            --purple-bg: rgba(168,85,247,0.1);
            --cyan: #06b6d4;
            --cyan-bg: rgba(6,182,212,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        .header { background: var(--card); border-bottom: 1px solid var(--border); padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10; }
        .header h1 { font-size: 18px; font-weight: 600; }
        .header-controls { display: flex; gap: 12px; align-items: center; }
        .header-controls select, .header-controls button { background: var(--bg); color: var(--text); border: 1px solid var(--border); border-radius: 6px; padding: 6px 12px; font-size: 13px; cursor: pointer; }
        .header-controls button:hover { background: var(--card-hover); }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
        .status-dot.ok { background: var(--green); box-shadow: 0 0 6px var(--green); }
        .status-dot.warn { background: var(--yellow); box-shadow: 0 0 6px var(--yellow); }
        .status-dot.error { background: var(--red); box-shadow: 0 0 6px var(--red); }
        .status-dot.off { background: var(--text-muted); }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .grid { display: grid; gap: 16px; }
        .grid-4 { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        .grid-2 { grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); }
        .grid-1 { grid-template-columns: 1fr; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 16px 20px; }
        .card-title { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px; }
        .card-value { font-size: 28px; font-weight: 700; line-height: 1.2; }
        .card-sub { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .card-value.green { color: var(--green); }
        .card-value.red { color: var(--red); }
        .card-value.yellow { color: var(--yellow); }
        .card-value.blue { color: var(--blue); }
        .card-value.purple { color: var(--purple); }
        .card-value.cyan { color: var(--cyan); }
        .section-title { font-size: 14px; font-weight: 600; color: var(--text-muted); margin: 24px 0 12px; text-transform: uppercase; letter-spacing: 1px; }
        .chart-container { position: relative; height: 200px; overflow: hidden; }
        .chart-canvas { width: 100%; height: 100%; }
        .bar-chart { display: flex; align-items: flex-end; gap: 2px; height: 100%; padding-top: 20px; }
        .bar { flex: 1; min-width: 3px; border-radius: 2px 2px 0 0; transition: height 0.3s; position: relative; }
        .bar:hover { opacity: 0.8; }
        .bar-tooltip { display: none; position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); background: var(--bg); border: 1px solid var(--border); padding: 4px 8px; border-radius: 4px; font-size: 11px; white-space: nowrap; z-index: 5; pointer-events: none; }
        .bar:hover .bar-tooltip { display: block; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { text-align: left; padding: 10px 12px; color: var(--text-muted); font-weight: 500; border-bottom: 1px solid var(--border); font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 8px 12px; border-bottom: 1px solid var(--border); }
        tr:hover td { background: var(--card-hover); }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-request { background: var(--blue-bg); color: var(--blue); }
        .badge-query { background: var(--purple-bg); color: var(--purple); }
        .badge-slow { background: var(--red-bg); color: var(--red); }
        .badge-ok { background: var(--green-bg); color: var(--green); }
        .tabs { display: flex; gap: 0; border-bottom: 1px solid var(--border); margin-bottom: 16px; }
        .tab { padding: 8px 16px; font-size: 13px; color: var(--text-muted); cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; }
        .tab:hover { color: var(--text); }
        .tab.active { color: var(--blue); border-bottom-color: var(--blue); }
        .no-data { text-align: center; padding: 40px; color: var(--text-muted); }
        .refresh-indicator { font-size: 11px; color: var(--text-muted); }
        .sql-text { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 12px; word-break: break-all; max-width: 500px; }
        @media (max-width: 768px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            <span class="status-dot off" id="statusDot"></span>
            Monitoring Dashboard
        </h1>
        <div class="header-controls">
            <span class="refresh-indicator" id="lastUpdate">--</span>
            <select id="periodSelect">
                <option value="5">Last 5 min</option>
                <option value="15">Last 15 min</option>
                <option value="30">Last 30 min</option>
                <option value="60" selected>Last 1 hour</option>
                <option value="360">Last 6 hours</option>
                <option value="1440">Last 24 hours</option>
            </select>
            <select id="refreshSelect">
                <option value="0">Auto-refresh: Off</option>
                <option value="10">Every 10s</option>
                <option value="30" selected>Every 30s</option>
                <option value="60">Every 60s</option>
            </select>
            <button onclick="fetchData()">Refresh</button>
        </div>
    </div>

    <div class="container">
        <!-- Summary Cards -->
        <div class="grid grid-4" id="summaryCards">
            <div class="card"><div class="card-title">HTTP Requests</div><div class="card-value" id="httpTotal">--</div><div class="card-sub" id="httpSub">--</div></div>
            <div class="card"><div class="card-title">Avg Response</div><div class="card-value" id="httpAvg">--</div><div class="card-sub" id="httpAvgSub">--</div></div>
            <div class="card"><div class="card-title">Error Rate</div><div class="card-value" id="errorRate">--</div><div class="card-sub" id="errorSub">--</div></div>
            <div class="card"><div class="card-title">Slow Requests</div><div class="card-value" id="slowReq">--</div><div class="card-sub">above threshold</div></div>
            <div class="card"><div class="card-title">Queue Depth</div><div class="card-value" id="queueDepth">--</div><div class="card-sub" id="queueSub">--</div></div>
            <div class="card"><div class="card-title">Jobs Processed</div><div class="card-value" id="jobsProcessed">--</div><div class="card-sub" id="jobsSub">--</div></div>
            <div class="card"><div class="card-title">DB Queries</div><div class="card-value" id="dbQueries">--</div><div class="card-sub" id="dbSub">--</div></div>
            <div class="card"><div class="card-title">Slow Queries</div><div class="card-value" id="dbSlow">--</div><div class="card-sub" id="dbSlowSub">--</div></div>
        </div>

        <!-- System Cards -->
        <div class="section-title">System & Redis</div>
        <div class="grid grid-4">
            <div class="card"><div class="card-title">CPU Load (1m)</div><div class="card-value" id="cpuLoad">--</div></div>
            <div class="card"><div class="card-title">Redis Memory</div><div class="card-value" id="redisMem">--</div><div class="card-sub" id="redisMemSub">--</div></div>
            <div class="card"><div class="card-title">Redis Ops/sec</div><div class="card-value" id="redisOps">--</div><div class="card-sub" id="redisHitRate">--</div></div>
            <div class="card"><div class="card-title">Disk Free</div><div class="card-value" id="diskFree">--</div></div>
        </div>

        <!-- Charts -->
        <div class="section-title">Timeline</div>
        <div class="grid grid-2">
            <div class="card">
                <div class="card-title">Requests / minute</div>
                <div class="chart-container"><div class="bar-chart" id="chartRequests"></div></div>
            </div>
            <div class="card">
                <div class="card-title">Response time (ms) - avg / max</div>
                <div class="chart-container"><div class="bar-chart" id="chartDuration"></div></div>
            </div>
            <div class="card">
                <div class="card-title">Queue depth</div>
                <div class="chart-container"><div class="bar-chart" id="chartQueue"></div></div>
            </div>
            <div class="card">
                <div class="card-title">DB queries / minute</div>
                <div class="chart-container"><div class="bar-chart" id="chartDb"></div></div>
            </div>
        </div>

        <!-- Slow Logs -->
        <div class="section-title">Slow Logs</div>
        <div class="card">
            <div class="tabs">
                <div class="tab active" data-type="">All</div>
                <div class="tab" data-type="request">Requests</div>
                <div class="tab" data-type="query">Queries</div>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Detail</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="slowLogsBody">
                        <tr><td colspan="6" class="no-data">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const basePath = '{{ rtrim(config("monitoring.dashboard.path", "monitoring"), "/") }}';
        let refreshTimer = null;
        let currentSlowType = '';

        // Tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                currentSlowType = tab.dataset.type;
                fetchSlowLogs();
            });
        });

        document.getElementById('periodSelect').addEventListener('change', fetchData);
        document.getElementById('refreshSelect').addEventListener('change', () => {
            clearInterval(refreshTimer);
            const sec = parseInt(document.getElementById('refreshSelect').value);
            if (sec > 0) refreshTimer = setInterval(fetchData, sec * 1000);
        });

        function fetchData() {
            const minutes = document.getElementById('periodSelect').value;
            fetch(`/${basePath}/api/data?minutes=${minutes}`)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'no_data') {
                        document.getElementById('statusDot').className = 'status-dot off';
                        return;
                    }
                    updateSummary(data.summary);
                    updateCharts(data.timeline);
                    document.getElementById('lastUpdate').textContent = 'Updated: ' + new Date().toLocaleTimeString();
                })
                .catch(() => {
                    document.getElementById('statusDot').className = 'status-dot error';
                });

            fetchSlowLogs();
        }

        function fetchSlowLogs() {
            const minutes = document.getElementById('periodSelect').value;
            let url = `/${basePath}/api/slow-logs?minutes=${minutes}&limit=50`;
            if (currentSlowType) url += `&type=${currentSlowType}`;

            fetch(url)
                .then(r => r.json())
                .then(data => renderSlowLogs(data.data))
                .catch(() => {});
        }

        function updateSummary(s) {
            // Status dot
            const dot = document.getElementById('statusDot');
            if (s.error_rate > 5) dot.className = 'status-dot error';
            else if (s.error_rate > 1 || s.queue_depth_default > 100) dot.className = 'status-dot warn';
            else dot.className = 'status-dot ok';

            // HTTP
            setVal('httpTotal', fmtNum(s.http_requests_total));
            setVal('httpSub', `2xx: ${fmtNum(s.http_requests_2xx)} | 4xx: ${fmtNum(s.http_requests_4xx)} | 5xx: ${fmtNum(s.http_requests_5xx)}`);

            const avg = s.http_avg_duration;
            setVal('httpAvg', avg < 1000 ? avg + ' ms' : (avg / 1000).toFixed(1) + ' s', avg > 2000 ? 'red' : avg > 500 ? 'yellow' : 'green');
            setVal('httpAvgSub', `max: ${fmtMs(s.http_max_duration)}`);

            setVal('errorRate', s.error_rate + '%', s.error_rate > 5 ? 'red' : s.error_rate > 1 ? 'yellow' : 'green');
            setVal('errorSub', `${fmtNum(s.http_requests_5xx)} errors`);

            setVal('slowReq', fmtNum(s.http_slow_requests), s.http_slow_requests > 10 ? 'red' : s.http_slow_requests > 0 ? 'yellow' : 'green');

            // Queue
            const totalDepth = s.queue_depth_high + s.queue_depth_default + s.queue_depth_low;
            setVal('queueDepth', fmtNum(totalDepth), totalDepth > 100 ? 'red' : totalDepth > 20 ? 'yellow' : 'green');
            setVal('queueSub', `H: ${s.queue_depth_high} | D: ${s.queue_depth_default} | L: ${s.queue_depth_low}`);

            setVal('jobsProcessed', fmtNum(s.queue_jobs_processed), 'blue');
            setVal('jobsSub', `failed: ${fmtNum(s.queue_jobs_failed)}`);

            // DB
            setVal('dbQueries', fmtNum(s.db_queries_total));
            setVal('dbSub', `avg: ${s.db_avg_query_ms}ms | max: ${fmtMs(s.db_max_query_ms)}`);

            setVal('dbSlow', fmtNum(s.db_slow_queries), s.db_slow_queries > 10 ? 'red' : s.db_slow_queries > 0 ? 'yellow' : 'green');
            setVal('dbSlowSub', `above ${document.getElementById('periodSelect').selectedOptions[0].text}`);

            // System
            setVal('cpuLoad', s.cpu_load !== null ? s.cpu_load : 'N/A', s.cpu_load > 5 ? 'red' : s.cpu_load > 2 ? 'yellow' : 'green');
            setVal('redisMem', s.redis_memory_mb !== null ? s.redis_memory_mb + ' MB' : 'N/A', 'cyan');
            setVal('redisMemSub', s.redis_clients !== null ? s.redis_clients + ' clients' : '');
            setVal('redisOps', s.redis_ops_per_sec !== null ? fmtNum(s.redis_ops_per_sec) : 'N/A', 'cyan');
            setVal('redisHitRate', s.redis_hit_rate !== null ? 'hit rate: ' + s.redis_hit_rate + '%' : '');
            setVal('diskFree', s.disk_free_gb !== null ? s.disk_free_gb + ' GB' : 'N/A', s.disk_free_gb < 2 ? 'red' : s.disk_free_gb < 5 ? 'yellow' : 'green');
        }

        function updateCharts(timeline) {
            if (!timeline || timeline.length === 0) return;

            renderBarChart('chartRequests', timeline, 'requests', '--blue');
            renderDualBarChart('chartDuration', timeline, 'avg_duration', 'max_duration', '--blue', '--red');
            renderStackedBarChart('chartQueue', timeline, ['queue_high', 'queue_default', 'queue_low'], ['--red', '--yellow', '--blue']);
            renderBarChart('chartDb', timeline, 'db_queries', '--purple');
        }

        function renderBarChart(containerId, data, key, color) {
            const container = document.getElementById(containerId);
            const max = Math.max(...data.map(d => d[key]), 1);
            container.innerHTML = data.map(d => {
                const h = Math.max((d[key] / max) * 100, 1);
                return `<div class="bar" style="height:${h}%;background:var(${color})"><div class="bar-tooltip">${d.time}: ${fmtNum(d[key])}</div></div>`;
            }).join('');
        }

        function renderDualBarChart(containerId, data, key1, key2, color1, color2) {
            const container = document.getElementById(containerId);
            const max = Math.max(...data.map(d => d[key2]), 1);
            container.innerHTML = data.map(d => {
                const h1 = Math.max((d[key1] / max) * 100, 1);
                const h2 = Math.max((d[key2] / max) * 100, 1);
                return `<div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;gap:1px;position:relative">
                    <div class="bar" style="width:100%;height:${h2}%;background:var(${color2});opacity:0.3;position:absolute;bottom:0"><div class="bar-tooltip">${d.time}: max ${fmtMs(d[key2])}</div></div>
                    <div class="bar" style="width:100%;height:${h1}%;background:var(${color1});position:absolute;bottom:0"><div class="bar-tooltip">${d.time}: avg ${fmtMs(d[key1])}</div></div>
                </div>`;
            }).join('');
        }

        function renderStackedBarChart(containerId, data, keys, colors) {
            const container = document.getElementById(containerId);
            const max = Math.max(...data.map(d => keys.reduce((s, k) => s + d[k], 0)), 1);
            container.innerHTML = data.map(d => {
                const total = keys.reduce((s, k) => s + d[k], 0);
                const segments = keys.map((k, i) => {
                    const h = (d[k] / max) * 100;
                    return h > 0 ? `<div style="width:100%;height:${h}%;background:var(${colors[i]})"></div>` : '';
                }).join('');
                return `<div style="flex:1;display:flex;flex-direction:column-reverse;height:100%;gap:0;position:relative">
                    ${segments}
                    <div class="bar-tooltip" style="display:none;position:absolute;bottom:100%;left:50%;transform:translateX(-50%);background:var(--bg);border:1px solid var(--border);padding:4px 8px;border-radius:4px;font-size:11px;white-space:nowrap;z-index:5">${d.time}: ${total}</div>
                </div>`;
            }).join('');

            // Add hover for stacked
            container.querySelectorAll(':scope > div').forEach(el => {
                const tip = el.querySelector('.bar-tooltip');
                el.addEventListener('mouseenter', () => tip.style.display = 'block');
                el.addEventListener('mouseleave', () => tip.style.display = 'none');
            });
        }

        function renderSlowLogs(logs) {
            const tbody = document.getElementById('slowLogsBody');
            if (!logs || logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="no-data">No slow logs in this period</td></tr>';
                return;
            }
            tbody.innerHTML = logs.map(log => `<tr>
                <td><span class="badge badge-${log.type}">${log.type}</span></td>
                <td><span class="badge badge-slow">${fmtMs(log.duration_ms)}</span></td>
                <td>${log.type === 'request'
                    ? `<strong>${log.method}</strong> ${escHtml(truncate(log.url, 70))}`
                    : `<span class="sql-text">${escHtml(truncate(log.sql, 80))}</span>`
                }</td>
                <td>${log.status_code ? statusBadge(log.status_code) : (log.connection || '-')}</td>
                <td>${log.user_id || '-'}</td>
                <td>${log.time}</td>
            </tr>`).join('');
        }

        function setVal(id, value, colorClass) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = value;
            if (colorClass && el.classList.contains('card-value')) {
                el.className = 'card-value ' + colorClass;
            }
        }

        function fmtNum(n) {
            if (n === null || n === undefined) return 'N/A';
            if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
            if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
            return n.toString();
        }

        function fmtMs(ms) {
            if (ms === null || ms === undefined) return 'N/A';
            if (ms >= 1000) return (ms / 1000).toFixed(1) + 's';
            return Math.round(ms) + 'ms';
        }

        function truncate(s, len) { return s && s.length > len ? s.substring(0, len) + '...' : (s || ''); }
        function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
        function statusBadge(code) {
            const cls = code >= 500 ? 'badge-slow' : code >= 400 ? 'badge-request' : 'badge-ok';
            return `<span class="badge ${cls}">${code}</span>`;
        }

        // Initial load
        fetchData();

        // Auto-refresh
        const initialRefresh = parseInt(document.getElementById('refreshSelect').value);
        if (initialRefresh > 0) refreshTimer = setInterval(fetchData, initialRefresh * 1000);
    </script>
</body>
</html>
