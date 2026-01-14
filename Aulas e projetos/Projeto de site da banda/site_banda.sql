-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/09/2025 às 16:39
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `site_banda`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `created_at`, `status`) VALUES
(1, NULL, 67.96, '2025-08-28 07:21:57', 'Processado'),
(2, NULL, 63.88, '2025-08-29 04:56:12', 'Pendente'),
(3, 3, 16.99, '2025-08-31 11:49:49', 'Pendente'),
(4, 5, 47.97, '2025-09-01 11:48:00', 'Pendente'),
(5, 3, 16.99, '2025-09-02 15:09:35', 'Pendente'),
(6, 3, 75.79, '2025-09-02 15:11:07', 'Pendente'),
(7, 3, 16.99, '2025-09-02 15:14:30', 'Pendente'),
(8, 3, 16.99, '2025-09-02 15:20:53', 'Pendente'),
(9, 3, 16.99, '2025-09-02 15:21:37', 'Pendente'),
(10, 3, 152.68, '2025-09-02 15:24:20', 'Pendente'),
(11, 3, 89.70, '2025-09-02 15:34:10', 'Pendente'),
(12, 3, 58.80, '2025-09-02 15:36:05', 'Pendente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 2, 4, 16.99),
(2, 2, 2, 1, 16.99),
(3, 2, 1, 1, 15.99),
(4, 2, 4, 1, 30.90),
(5, 3, 2, 1, 16.99),
(6, 4, 1, 3, 15.99),
(7, 5, 2, 1, 16.99),
(8, 6, 2, 1, 16.99),
(9, 6, 3, 1, 18.90),
(10, 6, 5, 1, 39.90),
(11, 7, 2, 1, 16.99),
(12, 8, 2, 1, 16.99),
(13, 9, 2, 1, 16.99),
(14, 10, 5, 3, 39.90),
(15, 10, 1, 1, 15.99),
(16, 10, 2, 1, 16.99),
(17, 11, 15, 3, 29.90),
(18, 12, 3, 1, 18.90),
(19, 12, 5, 1, 39.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image`) VALUES
(1, 'Album X', 'Album X - Primeiro Álbum do Ed Sheeran.', 15.99, 'cds', 'Imagens/albumX.jpg'),
(2, 'Album +', 'Album + - Segundo Álbum do Ed Sheeran.', 16.99, 'cds', 'Imagens/+EdSheeran.jpg'),
(3, 'Album Divide', 'Album Divide - Terceiro Álbum do Ed Sheeran.', 18.90, 'cds', 'Imagens/DivideAlbum.jpg'),
(4, 'T-Shirt Divide', 'T-Shirt do Album Divide.', 30.90, 'merch', 'Imagens T-shirt/T-shirt1.jpg'),
(5, 'T-Shirt Especial', 'Edição Especial de todos os Álbuns', 39.90, 'merch', 'Imagens T-shirt/T-shirt2.jpg'),
(15, 'T_Shirt', 'T-Shirt com qualidade 100% algodão', 29.90, NULL, 'img_adm/T-shirt3.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `user_type` enum('user','admin') DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `user_type`, `profile_pic`) VALUES
(1, 'rhuds', 'rhuds@hotmail.com', '$2y$10$yQO0gAxmrSt7dEMIFeeBCu0LDvudKH9LNcidarN56.a3/Mf50.REm', 'user', 'img_register/travelsite.jpg'),
(3, 'rhu', 'rhu@hotmail.com', '$2y$10$iRGWbgJ3qGupTAA1IRkmxOEIBiVJ6oI9wQLmxXR7g7fgRlOiFn7YW', 'admin', 'img_register/Rhudanius.jpg'),
(5, 'tobias', 'tobias@hotmail.com', '$2y$10$6No0yt.STTUA6fBcc0KkS.hPESf7333PE.aAG55E0I58J.OtHKwk6', 'admin', 'img_register/cliente1.jpg');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
