<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINALIA Kurulum Sihirbazı</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .installer-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }

        .installer-header {
            background: linear-gradient(135deg, #7a8b5c 0%, #6a7a4f 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .installer-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .installer-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .progress-bar {
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: white;
            transition: width 0.3s ease;
        }

        .installer-body {
            padding: 2.5rem;
        }

        .step-title {
            font-size: 1.75rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .step-description {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1.05rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #7a8b5c;
            box-shadow: 0 0 0 3px rgba(122, 139, 92, 0.1);
        }

        .form-help {
            font-size: 0.875rem;
            color: #999;
            margin-top: 0.25rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
        }

        .checkbox-group:hover {
            background: #e9ecef;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #7a8b5c 0%, #6a7a4f 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(122, 139, 92, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-success {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #d32f2f;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #4CAF50;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ff9800;
        }

        .requirements-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }

        .requirements-table tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .requirements-table td {
            padding: 1rem 0.5rem;
        }

        .requirements-table td:first-child {
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-pass {
            background: #d4edda;
            color: #155724;
        }

        .status-fail {
            background: #f8d7da;
            color: #721c24;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .feature-card {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .feature-card-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .feature-card h3 {
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .feature-card p {
            font-size: 0.9375rem;
            color: #666;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: white;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .installer-body > * {
            animation: fadeIn 0.5s ease;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .installer-header h1 {
                font-size: 1.5rem;
            }

            .installer-body {
                padding: 1.5rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <div class="logo">🌸</div>
            <h1>MINALIA Parfüm</h1>
            <p>Kurulum Sihirbazı</p>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= ($this->step / 5) * 100 ?>%;"></div>
        </div>

        <div class="installer-body">
            <?php if (!empty($this->getErrors())): ?>
                <?php foreach ($this->getErrors() as $error): ?>
                    <div class="alert alert-danger">
                        <strong>❌ Hata:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($this->getSuccess())): ?>
                <?php foreach ($this->getSuccess() as $success): ?>
                    <div class="alert alert-success">
                        <strong>✅ Başarılı:</strong> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
