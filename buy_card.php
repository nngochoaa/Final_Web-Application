<?php
session_start();
include 'db_config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mua Thẻ Cào - Ví Điện Tử</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-provider { cursor: pointer; border: 2px solid #eee; transition: 0.3s; border-radius: 10px; }
        .card-provider:hover { border-color: #007bff; transform: translateY(-3px); }
        .provider-radio:checked + .card-provider { border-color: #007bff; background-color: #e7f1ff; box-shadow: 0 4px 10px rgba(0,123,255,0.2); }
        .fw-bold-brand { letter-spacing: 1px; font-size: 1rem; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4 text-primary fw-bold">🎟️ Mua Thẻ Điện Thoại</h3>
                    <form action="process_buy_card.php" method="POST">
                        <label class="form-label fw-bold text-secondary">1. Chọn nhà mạng</label>
                        <div class="row g-2 mb-4">
                            <div class="col-4">
                                <input type="radio" name="carrier" value="Viettel" id="viettel" class="btn-check provider-radio" required>
                                <label class="card card-body card-provider p-3 text-center" for="viettel">
                                    <span class="fw-bold-brand" style="color: #ed1c24;">VIETTEL</span>
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" name="carrier" value="Mobifone" id="mobi" class="btn-check provider-radio">
                                <label class="card card-body card-provider p-3 text-center" for="mobi">
                                    <span class="fw-bold-brand" style="color: #0054a6;">MOBIFONE</span>
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" name="carrier" value="Vinaphone" id="vina" class="btn-check provider-radio">
                                <label class="card card-body card-provider p-3 text-center" for="vina">
                                    <span class="fw-bold-brand" style="color: #00a1e4;">VINAPHONE</span>
                                </label>
                            </div>
                        </div>

                        <label class="form-label fw-bold text-secondary">2. Chọn mệnh giá</label>
                        <select name="amount" class="form-select form-select-lg mb-4" style="border-radius: 10px;">
                            <option value="10000">10,000đ</option>
                            <option value="20000">20,000đ</option>
                            <option value="50000">50,000đ</option>
                            <option value="100000">100,000đ</option>
                        </select>

                        <label class="form-label fw-bold text-secondary">3. Số lượng</label>
                        <input type="number" name="quantity" class="form-control form-control-lg mb-4" min="1" max="5" value="1" style="border-radius: 10px;">

                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold" style="border-radius: 10px; padding: 15px;">Thanh toán ngay</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>