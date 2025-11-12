<div class="page-header">
    <div>
        <h1 class="page-title">Raporlar</h1>
        <div class="page-breadcrumb">
            <span>Analiz</span>
            <span>›</span>
            <span>Raporlar</span>
        </div>
    </div>
</div>

<!-- Customer Statistics -->
<div class="row" style="margin-bottom: 2rem;">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= number_format($customer_stats['total_customers'] ?? 0) ?></div>
                <div class="stat-label">Toplam Müşteri (30 Gün)</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #7A8B5C 0%, #6B7C4D 100%);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= formatPrice($customer_stats['avg_order_value'] ?? 0) ?></div>
                <div class="stat-label">Ortalama Sipariş Değeri</div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Report -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Satış Raporu (Son 30 Gün)</h3>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Sipariş Sayısı</th>
                        <th>Gelir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales_data)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem; color: #999;">
                                Veri bulunamadı
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $totalOrders = 0;
                        $totalRevenue = 0;
                        foreach ($sales_data as $sale):
                            $totalOrders += $sale['orders'];
                            $totalRevenue += $sale['revenue'];
                        ?>
                            <tr>
                                <td><?= date('d.m.Y', strtotime($sale['date'])) ?></td>
                                <td><?= number_format($sale['orders']) ?></td>
                                <td><strong><?= formatPrice($sale['revenue']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background: #f8f9fa; font-weight: 600;">
                            <td>TOPLAM</td>
                            <td><?= number_format($totalOrders) ?></td>
                            <td><strong style="color: var(--admin-success);"><?= formatPrice($totalRevenue) ?></strong></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">En Çok Satılan Ürünler (Son 30 Gün)</h3>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ürün Adı</th>
                        <th>Satış Sayısı</th>
                        <th>Toplam Adet</th>
                        <th>Toplam Gelir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($top_products)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: #999;">
                                Veri bulunamadı
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($top_products as $index => $product): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="width: 24px; height: 24px; border-radius: 50%; background: var(--admin-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">
                                            <?= $index + 1 ?>
                                        </span>
                                        <strong><?= htmlspecialchars($product['name']) ?></strong>
                                    </div>
                                </td>
                                <td><?= number_format($product['total_sold']) ?></td>
                                <td><?= number_format($product['total_quantity']) ?> adet</td>
                                <td><strong><?= formatPrice($product['total_revenue']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
