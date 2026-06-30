<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Bienes Incautados' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/modern_styles.css?v=<?= time() ?>">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header-principal {
            background-color: #073A6B;
        }
        .card-header {
            background-color: #073A6B;
            color: white;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #073A6B;
            border-color: #073A6B;
        }
        .btn-primary:hover {
            background-color: #052849;
            border-color: #052849;
        }
        .page-item.active .page-link {
            background-color: #073A6B;
            border-color: #073A6B;
            color: white;
        }
        .page-link {
            color: #073A6B;
        }
        .table th {
            background-color: #e9ecef;
        }
        .text-primary {
            color: #073A6B !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
        }
        .modal {
            z-index: 9999 !important;
        }
    </style>
</head>
<body>
    <header class="header-principal text-white p-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="img/logo.jpg" alt="Logo" width="50" height="50" class="me-3">
                    <h1 class="h3">Sistema de Gestión de Bienes Incautados</h1>
                </div>
            </div>
        </div>
    </header>