<?php $content = ob_start(); ?>

<div class="content-header">
    <h1><i class="fas fa-sms"></i> SMS Yönetimi</h1>
    <div class="header-actions">
        <a href="/admin/sms/send" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> SMS Gönder
        </a>
        <a href="/admin/sms/send-bulk" class="btn btn-secondary">
            <i class="fas fa-users"></i> Toplu SMS
        </a>
        <a href="/admin/sms/settings" class="btn btn-outline">
            <i class="fas fa-cog"></i> Ayarlar
        </a>
    </div>
</div>

<!-- SMS Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-blue">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= number_format($stats['today']) ?></div>
            <div class="stat-label">Bugün Gönderilen</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-green">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= number_format($stats['this_month']) ?></div>
            <div class="stat-label">Bu Ay</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-purple">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= number_format($stats['total']) ?></div>
            <div class="stat-label">Toplam Gönderim</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon <?= $stats['failed'] > 0 ? 'bg-red' : 'bg-gray' ?>">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= number_format($stats['failed']) ?></div>
            <div class="stat-label">Başarısız</div>
        </div>
    </div>
</div>

<!-- SMS Balance -->
<?php if ($balance['success']): ?>
<div class="alert alert-info" style="margin: 2rem 0;">
    <i class="fas fa-wallet"></i>
    <strong>SMS Bakiyesi:</strong>
    <?php if (isset($balance['credits'])): ?>
        <?= number_format($balance['credits']) ?> Kredi
    <?php elseif (isset($balance['sms'])): ?>
        <?= number_format($balance['sms']) ?> SMS
    <?php endif; ?>
    <span style="margin-left: 1rem; opacity: 0.8;">
        Provider: <?= ucfirst($_ENV['SMS_PROVIDER'] ?? 'Netgsm') ?>
    </span>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="card" style="margin-bottom: 1rem;">
    <div class="card-body" style="padding: 1rem;">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <label style="font-weight: 600;">Filtrele:</label>
            <button class="btn btn-sm" onclick="filterLogs('all')">Tümü</button>
            <button class="btn btn-sm" onclick="filterLogs('today')">Bugün</button>
            <button class="btn btn-sm" onclick="filterLogs('failed')">Başarısız</button>
            <button class="btn btn-sm btn-outline" onclick="refreshLogs()">
                <i class="fas fa-sync"></i> Yenile
            </button>
        </div>
    </div>
</div>

<!-- SMS Logs Table -->
<div class="card">
    <div class="card-header">
        <h3>SMS Geçmişi</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Telefon</th>
                        <th>Mesaj</th>
                        <th>Durum</th>
                        <th>Provider</th>
                        <th>Tarih</th>
                    </tr>
                </thead>
                <tbody id="sms-logs-table">
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td>#<?= $log['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($log['phone']) ?></strong>
                        </td>
                        <td>
                            <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($log['message']) ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($log['status'] === 'sent'): ?>
                                <span class="badge badge-success">Gönderildi</span>
                            <?php elseif ($log['status'] === 'delivered'): ?>
                                <span class="badge badge-success">Teslim</span>
                            <?php elseif ($log['status'] === 'failed'): ?>
                                <span class="badge badge-danger">Başarısız</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Beklemede</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($log['provider']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterLogs(filter) {
    fetch(`/admin/sms/get-logs?filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateLogsTable(data.logs);
            }
        });
}

function refreshLogs() {
    location.reload();
}

function updateLogsTable(logs) {
    const tbody = document.getElementById('sms-logs-table');
    tbody.innerHTML = logs.map(log => `
        <tr>
            <td>#${log.id}</td>
            <td><strong>${log.phone}</strong></td>
            <td>
                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    ${log.message}
                </div>
            </td>
            <td>
                ${getStatusBadge(log.status)}
            </td>
            <td>${log.provider}</td>
            <td>${formatDate(log.created_at)}</td>
        </tr>
    `).join('');
}

function getStatusBadge(status) {
    const badges = {
        'sent': '<span class="badge badge-success">Gönderildi</span>',
        'delivered': '<span class="badge badge-success">Teslim</span>',
        'failed': '<span class="badge badge-danger">Başarısız</span>',
        'pending': '<span class="badge badge-warning">Beklemede</span>'
    };
    return badges[status] || badges['pending'];
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../../layouts/admin.php'; ?>
