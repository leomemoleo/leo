<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Sadakat Puanlarım
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Points Summary -->
                <div class="card" style="background: linear-gradient(135deg, var(--gold) 0%, #c99d30 100%); color: white; margin-bottom: 2rem;">
                    <div class="card-body" style="padding: 2rem; text-align: center;">
                        <div style="font-size: 3rem; font-weight: 700; margin-bottom: 0.5rem;">
                            <?= number_format($summary['total_points'] ?? 0) ?> Puan
                        </div>
                        <div style="font-size: 1.125rem; opacity: 0.9;">
                            Toplam Sadakat Puanınız
                        </div>
                    </div>
                </div>

                <!-- Points Stats -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="card">
                        <div class="card-body" style="text-align: center; padding: 1.5rem;">
                            <div style="font-size: 2rem; color: #4CAF50; margin-bottom: 0.5rem;">
                                +<?= number_format($summary['earned_this_month'] ?? 0) ?>
                            </div>
                            <div style="color: #666; font-weight: 600;">Bu Ay Kazanılan</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body" style="text-align: center; padding: 1.5rem;">
                            <div style="font-size: 2rem; color: #f44336; margin-bottom: 0.5rem;">
                                -<?= number_format($summary['spent_this_month'] ?? 0) ?>
                            </div>
                            <div style="color: #666; font-weight: 600;">Bu Ay Kullanılan</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body" style="text-align: center; padding: 1.5rem;">
                            <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;">
                                <?= number_format($summary['total_earned'] ?? 0) ?>
                            </div>
                            <div style="color: #666; font-weight: 600;">Toplam Kazanılan</div>
                        </div>
                    </div>
                </div>

                <!-- How It Works -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid #eee;">
                        <h3 style="margin: 0;"><i class="fas fa-info-circle"></i> Nasıl Çalışır?</h3>
                    </div>
                    <div class="card-body" style="padding: 2rem;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
                            <div style="text-align: center;">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary), #6a7a4f); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem;">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h4 style="margin: 0 0 0.5rem;">Alışveriş Yapın</h4>
                                <p style="margin: 0; color: #666; font-size: 0.9375rem;">
                                    Her 10 TL harcamada 1 puan kazanın
                                </p>
                            </div>

                            <div style="text-align: center;">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--gold), #c99d30); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem;">
                                    <i class="fas fa-star"></i>
                                </div>
                                <h4 style="margin: 0 0 0.5rem;">Puan Biriktirin</h4>
                                <p style="margin: 0; color: #666; font-size: 0.9375rem;">
                                    Puanlarınız hesabınızda birikir
                                </p>
                            </div>

                            <div style="text-align: center;">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #4CAF50, #45a049); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem;">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <h4 style="margin: 0 0 0.5rem;">İndirim Kazanın</h4>
                                <p style="margin: 0; color: #666; font-size: 0.9375rem;">
                                    Puanlarınızı indirimlerde kullanın
                                </p>
                            </div>
                        </div>

                        <div style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 1rem;"><i class="fas fa-gift"></i> Puan Kazanma Yolları</h4>
                            <ul style="margin: 0; padding-left: 1.5rem; color: #666;">
                                <li style="margin-bottom: 0.5rem;">Her 10 TL harcamada 1 puan</li>
                                <li style="margin-bottom: 0.5rem;">İlk siparişinizde 50 bonus puan</li>
                                <li style="margin-bottom: 0.5rem;">Doğum gününüzde 100 bonus puan</li>
                                <li style="margin-bottom: 0.5rem;">Arkadaş davetinde 200 puan</li>
                                <li>Ürün yorumu yazınca 25 puan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Points History -->
                <div class="card">
                    <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid #eee;">
                        <h3 style="margin: 0;"><i class="fas fa-history"></i> Puan Geçmişi</h3>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <?php if (empty($history)): ?>
                            <div style="text-align: center; padding: 3rem 2rem; color: #999;">
                                <i class="fas fa-history" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                Henüz puan hareketiniz bulunmuyor.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #f8f9fa;">
                                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Tarih</th>
                                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Açıklama</th>
                                            <th style="padding: 1rem; text-align: right; border-bottom: 2px solid #dee2e6;">Puan</th>
                                            <th style="padding: 1rem; text-align: right; border-bottom: 2px solid #dee2e6;">Bakiye</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($history as $item): ?>
                                            <tr style="border-bottom: 1px solid #eee;">
                                                <td style="padding: 1rem;">
                                                    <?= date('d.m.Y H:i', strtotime($item['created_at'])) ?>
                                                </td>
                                                <td style="padding: 1rem;">
                                                    <?= htmlspecialchars($item['description']) ?>
                                                </td>
                                                <td style="padding: 1rem; text-align: right; font-weight: 600;">
                                                    <?php if ($item['points'] > 0): ?>
                                                        <span style="color: #4CAF50;">+<?= number_format($item['points']) ?></span>
                                                    <?php else: ?>
                                                        <span style="color: #f44336;"><?= number_format($item['points']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="padding: 1rem; text-align: right; font-weight: 500;">
                                                    <?= number_format($item['balance_after']) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
}

table {
    font-size: 0.9375rem;
}
</style>
