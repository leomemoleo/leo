<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin: 0;">
                Bildirimlerim
            </h1>
            <?php if ($unread_count > 0): ?>
                <a href="/account/notifications?mark_all_read=1" class="btn btn-outline-primary">
                    <i class="fas fa-check-double"></i> Tümünü Okundu İşaretle
                </a>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <div><?php include __DIR__ . '/../../components/account-sidebar.php'; ?></div>

            <div>
                <?php if (empty($notifications)): ?>
                    <div class="card" style="text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-bell-slash" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                        <h3 style="margin: 0 0 1rem;">Bildiriminiz Yok</h3>
                        <p style="color: #666;">Sipariş, kargo ve kampanya bildirimleri burada görünecek.</p>
                    </div>
                <?php else: ?>
                    <div class="notifications-list">
                        <?php
                        $typeIcons = [
                            'order' => 'fas fa-shopping-bag',
                            'return' => 'fas fa-undo-alt',
                            'payment' => 'fas fa-credit-card',
                            'shipping' => 'fas fa-truck',
                            'promotion' => 'fas fa-tag',
                            'system' => 'fas fa-cog',
                            'review' => 'fas fa-star',
                            'wishlist' => 'fas fa-heart',
                            'loyalty' => 'fas fa-gift'
                        ];
                        $typeColors = [
                            'order' => '#2196F3',
                            'return' => '#FF9800',
                            'payment' => '#4CAF50',
                            'shipping' => '#9C27B0',
                            'promotion' => '#F44336',
                            'system' => '#607D8B',
                            'review' => '#FFC107',
                            'wishlist' => '#E91E63',
                            'loyalty' => '#00BCD4'
                        ];
                        foreach ($notifications as $notification):
                            $icon = $notification['icon'] ?: ($typeIcons[$notification['type']] ?? 'fas fa-bell');
                            $color = $typeColors[$notification['type']] ?? '#666';
                        ?>
                            <div class="card notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>" style="margin-bottom: 1rem;">
                                <div class="card-body" style="padding: 1.5rem; display: flex; gap: 1rem;">
                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: <?= $color ?>20; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="<?= $icon ?>" style="color: <?= $color ?>; font-size: 1.25rem;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.5rem; font-size: 1rem; font-weight: 600;">
                                            <?= htmlspecialchars($notification['title']) ?>
                                        </h4>
                                        <p style="margin: 0 0 0.5rem; color: #666; font-size: 0.9375rem;">
                                            <?= nl2br(htmlspecialchars($notification['message'])) ?>
                                        </p>
                                        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.875rem; color: #999;">
                                            <span><i class="far fa-clock"></i> <?= date('d.m.Y H:i', strtotime($notification['created_at'])) ?></span>
                                            <?php if ($notification['action_url']): ?>
                                                <a href="<?= htmlspecialchars($notification['action_url']) ?>" style="color: var(--primary);">
                                                    <i class="fas fa-arrow-right"></i> Görüntüle
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <form method="POST" action="/account/notifications/delete/<?= $notification['id'] ?>" onsubmit="return confirm('Bildirimi silmek istediğinize emin misiniz?');">
                                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-ghost" title="Sil">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<style>
.notification-item.unread { border-left: 4px solid var(--primary); background: #f8f9ff; }
</style>
