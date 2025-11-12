<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">Tercihlerim</h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <div><?php include __DIR__ . '/../../components/account-sidebar.php'; ?></div>

            <div>
                <form method="POST" action="/account/preferences">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-body" style="padding: 2rem;">
                            <h3 style="margin: 0 0 1.5rem;"><i class="fas fa-palette"></i> Parfüm Notaları</h3>
                            <p style="color: #666; margin-bottom: 1.5rem;">Beğendiğiniz koku notalarını seçin, size özel öneriler alalım.</p>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                                <?php
                                $notes = ['Çiçek', 'Meyve', 'Odunsu', 'Baharatlı', 'Taze', 'Doğu', 'Amber', 'Vanilya', 'Misk'];
                                $selectedNotes = $preferences['preferred_notes'] ?? [];
                                foreach ($notes as $note):
                                    $checked = in_array($note, $selectedNotes) ? 'checked' : '';
                                ?>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; cursor: pointer;">
                                        <input type="checkbox" name="preferred_notes[]" value="<?= $note ?>" <?= $checked ?>>
                                        <span><?= $note ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-body" style="padding: 2rem;">
                            <h3 style="margin: 0 0 1.5rem;"><i class="fas fa-tags"></i> Favori Markalar</h3>
                            <select name="preferred_brands[]" multiple class="form-control" style="height: 200px;">
                                <?php
                                $selectedBrands = $preferences['preferred_brands'] ?? [];
                                foreach ($brands as $brand):
                                    $selected = in_array($brand['id'], $selectedBrands) ? 'selected' : '';
                                ?>
                                    <option value="<?= $brand['id'] ?>" <?= $selected ?>><?= htmlspecialchars($brand['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Ctrl/Cmd tuşuyla birden fazla seçebilirsiniz</small>
                        </div>
                    </div>

                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-body" style="padding: 2rem;">
                            <h3 style="margin: 0 0 1.5rem;"><i class="fas fa-wallet"></i> Bütçe Aralığı</h3>
                            <select name="budget_range" class="form-control">
                                <option value="">Seçiniz</option>
                                <option value="0-500" <?= ($preferences['budget_range'] ?? '') === '0-500' ? 'selected' : '' ?>>0 - 500 TL</option>
                                <option value="500-1000" <?= ($preferences['budget_range'] ?? '') === '500-1000' ? 'selected' : '' ?>>500 - 1.000 TL</option>
                                <option value="1000-2000" <?= ($preferences['budget_range'] ?? '') === '1000-2000' ? 'selected' : '' ?>>1.000 - 2.000 TL</option>
                                <option value="2000+" <?= ($preferences['budget_range'] ?? '') === '2000+' ? 'selected' : '' ?>>2.000 TL ve üzeri</option>
                            </select>
                        </div>
                    </div>

                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-body" style="padding: 2rem;">
                            <h3 style="margin: 0 0 1.5rem;"><i class="fas fa-calendar-alt"></i> Kullanım Amacı</h3>
                            <select name="occasion_type" class="form-control">
                                <option value="">Seçiniz</option>
                                <option value="daily" <?= ($preferences['occasion_type'] ?? '') === 'daily' ? 'selected' : '' ?>>Günlük Kullanım</option>
                                <option value="office" <?= ($preferences['occasion_type'] ?? '') === 'office' ? 'selected' : '' ?>>İş/Ofis</option>
                                <option value="evening" <?= ($preferences['occasion_type'] ?? '') === 'evening' ? 'selected' : '' ?>>Gece/Özel Gün</option>
                                <option value="sport" <?= ($preferences['occasion_type'] ?? '') === 'sport' ? 'selected' : '' ?>>Spor</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Tercihleri Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
