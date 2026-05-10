-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 10, 2026 lúc 05:52 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `e_wallet`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `credit_cards`
--

CREATE TABLE `credit_cards` (
  `card_number` char(6) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `cvv` char(3) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `credit_cards`
--

INSERT INTO `credit_cards` (`card_number`, `expiry_date`, `cvv`, `description`) VALUES
('111111', '2022-10-10', '411', 'Thẻ không giới hạn'),
('222222', '2022-11-11', '443', 'Tối đa 1 triệu/lần'),
('333333', '2022-12-12', '577', 'Thẻ luôn hết tiền');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `otp_code` char(6) NOT NULL,
  `type` enum('transfer','reset_password') NOT NULL,
  `expired_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phone_cards`
--

CREATE TABLE `phone_cards` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `carrier` enum('Viettel','Mobifone','Vinaphone') DEFAULT NULL,
  `card_code` char(10) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phone_cards`
--

INSERT INTO `phone_cards` (`id`, `transaction_id`, `carrier`, `card_code`, `amount`) VALUES
(1, 13, 'Viettel', '1097640226', 10000.00),
(2, 14, 'Viettel', '1062011133', 10000.00),
(3, 15, 'Viettel', '1015310251', 10000.00),
(4, 16, 'Viettel', '1058941872', 10000.00),
(5, 17, 'Viettel', '1064699915', 10000.00),
(6, 25, 'Viettel', '1089256072', 20000.00),
(7, 26, 'Viettel', '1098680534', 10000.00),
(8, 51, 'Viettel', '1041540215', 10000.00),
(9, 54, 'Mobifone', '2080149957', 100000.00),
(10, 54, 'Mobifone', '2060242893', 100000.00),
(11, 54, 'Mobifone', '2068402998', 100000.00),
(12, 54, 'Mobifone', '2031522548', 100000.00),
(13, 54, 'Mobifone', '2012216753', 100000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_phone` varchar(15) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `type` enum('deposit','withdraw','transfer','buy_card') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `fee` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`id`, `sender_id`, `receiver_phone`, `receiver_id`, `type`, `amount`, `fee`, `total_amount`, `status`, `note`, `created_at`) VALUES
(13, 1, NULL, 1, 'buy_card', 10000.00, 0.00, 10000.00, 'success', NULL, '2026-05-10 12:47:55'),
(14, 1, NULL, 1, 'buy_card', 10000.00, 0.00, 10000.00, 'success', NULL, '2026-05-10 12:54:57'),
(15, 1, NULL, 1, 'buy_card', 10000.00, 0.00, 10000.00, 'success', NULL, '2026-05-10 12:59:06'),
(16, 1, NULL, 1, 'buy_card', 10000.00, 0.00, 10000.00, 'success', NULL, '2026-05-10 12:59:19'),
(17, 1, NULL, 1, 'buy_card', 10000.00, 0.00, 10000.00, 'success', NULL, '2026-05-10 13:00:26'),
(18, 1, NULL, NULL, 'deposit', 100000.00, 0.00, 100000.00, 'success', 'Nạp tiền từ thẻ 111111', '2026-05-10 13:14:47'),
(19, 2, NULL, NULL, 'deposit', 10000000.00, 0.00, 10000000.00, 'success', 'Nạp tiền từ thẻ 111111', '2026-05-10 13:17:31'),
(20, 2, NULL, 1, 'transfer', 10000.00, 500.00, 10000.00, 'success', 'hihi', '2026-05-10 13:18:09'),
(21, 2, NULL, 2, 'deposit', 100000.00, 0.00, 100000.00, 'success', 'Nạp tiền từ thẻ 111111', '2026-05-10 13:27:25'),
(22, 2, NULL, 2, 'withdraw', 5000000.00, 250000.00, 5250000.00, 'success', 'Rút tiền thành công', '2026-05-10 13:36:20'),
(23, 2, NULL, 2, 'deposit', 10000000.00, 0.00, 10000000.00, 'success', 'Nạp tiền từ thẻ 111111', '2026-05-10 13:37:28'),
(24, 2, NULL, 2, 'withdraw', 6000000.00, 300000.00, 6300000.00, '', 'Admin từ chối', '2026-05-10 13:38:05'),
(25, 2, NULL, 2, 'buy_card', 20000.00, 0.00, 20000.00, 'success', NULL, '2026-05-10 13:43:35'),
(26, 2, NULL, 2, 'buy_card', 10000.00, 0.00, 10000.00, 'success', NULL, '2026-05-10 13:44:13'),
(27, 1, NULL, 2, 'transfer', 10000000.00, 500000.00, 10000000.00, '', 'Admin từ chối', '2026-05-10 14:17:51'),
(28, 1, NULL, 2, 'transfer', 10000000.00, 500000.00, 10000000.00, '', 'Admin từ chối', '2026-05-10 14:18:40'),
(29, 1, NULL, 2, 'transfer', 10000000.00, 500000.00, 10000000.00, '', 'Admin từ chối', '2026-05-10 14:34:31'),
(30, 1, NULL, 2, 'transfer', 10000000.00, 500000.00, 10000000.00, '', 'Admin từ chối', '2026-05-10 14:39:01'),
(31, 1, '0786493778', NULL, 'transfer', 10000000.00, 500000.00, 10000000.00, '', 'Admin từ chối', '2026-05-10 14:48:21'),
(32, 1, '0786493778', NULL, 'transfer', 6000000.00, 300000.00, 6000000.00, '', 'Admin từ chối', '2026-05-10 14:49:14'),
(33, 1, '0786493778', NULL, 'transfer', 6000000.00, 300000.00, 6000000.00, 'success', 'Admin đã duyệt', '2026-05-10 14:54:22'),
(34, 1, NULL, 1, 'deposit', 100000000.00, 0.00, 100000000.00, 'success', 'Nạp tiền từ thẻ 111111', '2026-05-10 14:55:39'),
(35, 1, '0786493778', NULL, 'transfer', 10000000.00, 500000.00, 10500000.00, 'success', 'Admin đã duyệt', '2026-05-10 14:56:09'),
(36, 1, '0786493778', NULL, 'transfer', 10000000.00, 500000.00, 10500000.00, 'success', 'Admin đã duyệt', '2026-05-10 14:57:08'),
(37, 2, '0983423848', NULL, 'transfer', 10000000.00, 500000.00, 10500000.00, 'success', 'Admin đã duyệt', '2026-05-10 15:05:08'),
(38, 1, '0786493778', NULL, 'transfer', 10000000.00, 500000.00, 10500000.00, 'success', 'Admin đã duyệt', '2026-05-10 15:05:50'),
(39, 2, '0983423848', NULL, 'transfer', 10000000.00, 500000.00, 10000000.00, 'success', 'Admin đã duyệt', '2026-05-10 15:10:20'),
(40, 1, '0983423848', NULL, 'transfer', 10000000.00, 0.00, 0.00, 'success', 'Nhận tiền từ 2', '2026-05-10 15:10:29'),
(41, 1, '0786493778', NULL, 'transfer', 10000000.00, 500000.00, 10000000.00, 'success', 'Admin đã duyệt', '2026-05-10 15:11:10'),
(42, 2, NULL, 2, 'deposit', 100000000.00, 0.00, 100000000.00, 'success', 'Nạp tiền từ thẻ 111111', '2026-05-10 15:16:18'),
(43, 2, '0983423848', NULL, 'transfer', 10000000.00, 500000.00, 10500000.00, 'success', 'Admin đã duyệt', '2026-05-10 15:16:41'),
(44, 1, '0983423848', NULL, 'transfer', 10000000.00, 0.00, 0.00, 'success', 'Nhận tiền từ 2', '2026-05-10 15:16:48'),
(45, 1, '0786493778', NULL, 'transfer', 10000000.00, 500000.00, 10500000.00, 'success', 'Admin đã duyệt', '2026-05-10 15:22:23'),
(46, 2, '0786493778', NULL, 'transfer', 10000000.00, 0.00, 0.00, 'success', 'Nhận tiền từ 1', '2026-05-10 15:22:33'),
(47, 2, '0983423848', NULL, 'transfer', 10000000.00, 500000.00, 10000000.00, 'success', 'Admin đã duyệt', '2026-05-10 15:25:45'),
(48, 1, '0983423848', NULL, 'transfer', 10000000.00, 0.00, 0.00, 'success', 'Nhận tiền từ 2', '2026-05-10 15:25:55'),
(49, 1, '0786493778', NULL, 'transfer', 10000000.00, 500000.00, 10500000.00, 'success', 'Admin đã duyệt', '2026-05-10 15:28:17'),
(50, 2, '0786493778', NULL, 'transfer', 10000000.00, 0.00, 0.00, 'success', 'Nhận tiền từ 1', '2026-05-10 15:28:57'),
(51, 1, NULL, 1, 'buy_card', 10000.00, 0.00, 10000.00, 'success', NULL, '2026-05-10 15:30:24'),
(52, 6, NULL, 6, 'deposit', 1000000.00, 0.00, 1000000.00, 'success', 'Nạp tiền từ thẻ 111111', '2026-05-10 15:47:26'),
(53, 1, NULL, 2, 'transfer', 100000.00, 5000.00, 100000.00, 'success', 'hehe', '2026-05-10 15:49:41'),
(54, 2, NULL, 2, 'buy_card', 100000.00, 0.00, 500000.00, 'success', NULL, '2026-05-10 15:50:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `id_front_image` varchar(255) DEFAULT NULL,
  `id_back_image` varchar(255) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `status` enum('pending','verified','disabled','locked','waiting_for_updates') DEFAULT 'pending',
  `role` enum('admin','user') DEFAULT 'user',
  `is_first_login` tinyint(1) DEFAULT 1,
  `abnormal_login_count` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `lock_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone_number`, `date_of_birth`, `address`, `id_front_image`, `id_back_image`, `balance`, `status`, `role`, `is_first_login`, `abnormal_login_count`, `locked_until`, `created_at`, `login_attempts`, `lock_until`) VALUES
(1, '0983423848', '$2y$10$j3nla5W7FOv5Gve4h.rdneYRmEzTiXRpa.qLrI4/F1TDsBSb8wBDa', 'gia khiem', 'giakhiem090206@gmail.com', '0983423848', '2026-05-09', 'ád', '1778415491_front_hinh 1.jpg', '1778415491_back_hinh 2.jpg', 81449500.00, 'verified', 'user', 0, 0, NULL, '2026-05-10 09:54:52', 0, NULL),
(2, '0786493778', '$2y$10$yr0HDvcKw9qXC2M64asAheDrUwwdfOvesqCaQYjea3FSNP.NFeioy', 'ngoc', 'khiemthan01@gmail.com', '0786493778', '2026-05-02', 'ads', 'uploads/1778419004_front_hinh 2.jpg', 'uploads/1778419004_back_images.jpg', 103405000.00, 'verified', 'user', 0, 0, NULL, '2026-05-10 13:16:44', 0, NULL),
(6, '0123456789', '$2y$10$4VjatOEUmzt63aoF/tLeHuBKKufCjd9UZRCh3U9nh0n.2PfjlCLNq', 'gia khiem 1', '524k0031@student.tdtu.edu.vn', '0123456789', '2026-05-02', 'hehe', '1778428014_front_images.jpg', '1778428014_back_images (1).jpg', 1000000.00, 'verified', 'user', 0, 0, NULL, '2026-05-10 15:45:45', 0, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `credit_cards`
--
ALTER TABLE `credit_cards`
  ADD PRIMARY KEY (`card_number`);

--
-- Chỉ mục cho bảng `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `phone_cards`
--
ALTER TABLE `phone_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `phone_cards`
--
ALTER TABLE `phone_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD CONSTRAINT `otp_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `phone_cards`
--
ALTER TABLE `phone_cards`
  ADD CONSTRAINT `phone_cards_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`);

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
