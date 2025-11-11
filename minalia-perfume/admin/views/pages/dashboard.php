<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div class="page-breadcrumb">
        <span>Ana Sayfa</span>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Bu Ayki Satışlar</div>
                <div class="stat-value"><?= formatPrice($stats['total_revenue']) ?></div>
                <div class="stat-change <?= $stats['sales_change'] >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= $stats['sales_change'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= number_format(abs($stats['sales_change']), 1) ?>% geçen aya göre
                </div>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-lira-sign"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Toplam Sipariş</div>
                <div class="stat-value"><?= $stats['total_orders'] ?></div>
                <div class="stat-change">Bu ay</div>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Toplam Müşteri</div>
                <div class="stat-value"><?= $stats['total_customers'] ?></div>
                <div class="stat-change">Kayıtlı kullanıcılar</div>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Bekleyen Siparişler</div>
                <div class="stat-value"><?= $stats['pending_orders'] ?></div>
                <div class="stat-change">İşlem bekliyor</div>
            </div>
            <div class="stat-icon danger">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

<!-- Sales Chart -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Aylık Satış Grafiği (Son 12 Ay)</h3>
    </div>
    <div class="card-body">
        <canvas id="salesChart" style="max-height: 350px;"></canvas>
    </div>
</div>

<!-- Recent Orders & Low Stock -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Son Siparişler</h3>
            <a href="<?= ADMIN_URL ?>/orders" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> Tümünü Gör
            </a>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Tutar</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: #999;">
                                    Henüz sipariş yok
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><strong>#<?= $order['order_number'] ?></strong></td>
                                    <td><?= htmlspecialchars($order['customer_name'] ?? 'Misafir') ?></td>
                                    <td><?= formatPrice($order['total_amount']) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Bekliyor',
                                            'processing' => 'İşleniyor',
                                            'completed' => 'Tamamlandı',
                                            'cancelled' => 'İptal'
                                        ];
                                        $color = $statusColors[$order['status']] ?? 'info';
                                        $label = $statusLabels[$order['status']] ?? $order['status'];
                                        ?>
                                        <span style="padding: 0.25rem 0.75rem; background: var(--admin-<?= $color ?>); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                            <?= $label ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Düşük Stok</h3>
        </div>
        <div class="card-body">
            <?php if (empty($low_stock_products)): ?>
                <p style="text-align: center; color: #999; padding: 2rem 0;">
                    Stok uyarısı yok
                </p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($low_stock_products as $product): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; margin-bottom: 0.25rem; font-size: 0.9rem;">
                                    <?= htmlspecialchars($product['name']) ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #999;">
                                    <?= htmlspecialchars($product['brand_name']) ?>
                                </div>
                            </div>
                            <div>
                                <span style="padding: 0.25rem 0.75rem; background: #ffebee; color: #f44336; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                    <?= $product['stock_quantity'] ?> adet
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Reviews -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Son Yorumlar</h3>
        <a href="<?= ADMIN_URL ?>/reviews" class="btn btn-sm btn-primary">
            <i class="fas fa-eye"></i> Tümünü Gör
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($recent_reviews)): ?>
            <p style="text-align: center; color: #999; padding: 2rem 0;">
                Henüz yorum yok
            </p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <?php foreach ($recent_reviews as $review): ?>
                    <div style="padding-bottom: 1.5rem; border-bottom: 1px solid #f0f0f0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <div>
                                <strong><?= htmlspecialchars($review['customer_name']) ?></strong>
                                <span style="color: #999; margin: 0 0.5rem;">•</span>
                                <span style="color: #666;"><?= htmlspecialchars($review['product_name']) ?></span>
                            </div>
                            <div style="color: #D4AF37;">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star<?= $i < $review['rating'] ? '' : '-o' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p style="color: #666; margin: 0; font-size: 0.9rem;">
                            <?= htmlspecialchars($review['comment']) ?>
                        </p>
                        <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #999;">
                            <?= date('d.m.Y H:i', strtotime($review['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Prepare sales data
const salesData = <?= json_encode($monthly_sales ?? []) ?>;
const months = salesData.map(item => item.month_name || item.month);
const revenues = salesData.map(item => parseFloat(item.revenue));
const orderCounts = salesData.map(item => parseInt(item.order_count));

// Sales Chart
const ctx = document.getElementById('salesChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Gelir (₺)',
                    data: revenues,
                    borderColor: '#8B9A66',
                    backgroundColor: 'rgba(139, 154, 102, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Sipariş Sayısı',
                    data: orderCounts,
                    borderColor: '#D4AF37',
                    backgroundColor: 'rgba(212, 175, 55, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.datasetIndex === 0) {
                                    label += new Intl.NumberFormat('tr-TR', {
                                        style: 'currency',
                                        currency: 'TRY'
                                    }).format(context.parsed.y);
                                } else {
                                    label += context.parsed.y + ' adet';
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('tr-TR', {
                                style: 'currency',
                                currency: 'TRY',
                                maximumFractionDigits: 0
                            }).format(value);
                        },
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' adet';
                        },
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}
</script>
