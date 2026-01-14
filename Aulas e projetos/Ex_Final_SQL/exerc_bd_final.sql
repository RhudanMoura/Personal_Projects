-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 10/11/2025 às 18:56
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
-- Banco de dados: `exerc_bd_final`
--
CREATE DATABASE IF NOT EXISTS `exerc_bd_final` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `exerc_bd_final`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'Ana Silva', 'ana.silva@email.com', '912345678', '2025-11-06 00:43:58'),
(2, 'Bruno Costa', 'bruno.costa@email.com', '913456789', '2025-11-06 00:43:58'),
(3, 'Carla Mendes', 'carla.mendes@email.com', '914567890', '2025-11-06 00:43:58'),
(4, 'Diego Rocha', 'diego.rocha@email.com', '915678901', '2025-11-06 00:43:58');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `customer_sales_summary`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `customer_sales_summary`;
CREATE TABLE `customer_sales_summary` (
`customer_id` int(10) unsigned
,`customer_name` varchar(100)
,`number_of_tickets_purchased` bigint(21)
,`total_spent` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `venue` varchar(255) NOT NULL,
  `capacity` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `events`
--

INSERT INTO `events` (`id`, `name`, `description`, `date`, `time`, `venue`, `capacity`) VALUES
(1, 'Rock Night', 'Show de rock com bandas locais.', '2025-11-25', '20:00:00', 'Arena Central', 5000),
(2, 'Jazz & Blues Festival', 'Festival com artistas de jazz e blues.', '2025-12-10', '19:30:00', 'Teatro Municipal', 1200),
(3, 'Pop Stars Live', 'Show com grandes nomes da música pop.', '2026-01-15', '21:00:00', 'Estádio da Cidade', 15000),
(4, 'Indie Music Experience', 'Festival de bandas independentes.', '2025-12-05', '18:00:00', 'Parque das Luzes', 3000);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `event_details`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `event_details`;
CREATE TABLE `event_details` (
`event_id` int(10) unsigned
,`event_name` varchar(255)
,`description` text
,`date` date
,`time` time
,`venue` varchar(255)
,`total_tickets` int(10) unsigned
,`sold_tickets` bigint(21)
,`available_tickets` bigint(21) unsigned
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `event_sales_summary`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `event_sales_summary`;
CREATE TABLE `event_sales_summary` (
`event_id` int(10) unsigned
,`event_name` varchar(255)
,`number_of_tickets_sold` bigint(21)
,`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sales`
--

INSERT INTO `sales` (`id`, `ticket_id`, `customer_id`, `sale_date`, `quantity`, `total_price`) VALUES
(1, 1, 1, '2025-11-10 03:00:00', 1, 50.00),
(2, 2, 2, '2025-11-11 03:00:00', 1, 50.00),
(3, 4, 3, '2025-11-15 03:00:00', 1, 75.00),
(4, 6, 4, '2025-11-20 03:00:00', 1, 120.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `seat_number` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tickets`
--

INSERT INTO `tickets` (`id`, `event_id`, `seat_number`, `price`, `available`, `created_at`) VALUES
(1, 1, 'A01', 50.00, 1, '2025-11-06 00:44:07'),
(2, 1, 'A02', 50.00, 1, '2025-11-06 00:44:07'),
(3, 1, 'A03', 50.00, 1, '2025-11-06 00:44:07'),
(4, 2, 'B01', 75.00, 1, '2025-11-06 00:44:07'),
(5, 2, 'B02', 75.00, 1, '2025-11-06 00:44:07'),
(6, 3, 'C01', 120.00, 1, '2025-11-06 00:44:07'),
(7, 3, 'C02', 120.00, 1, '2025-11-06 00:44:07'),
(8, 3, 'C03', 120.00, 1, '2025-11-06 00:44:07'),
(9, 4, 'D01', 60.00, 1, '2025-11-06 00:44:07'),
(10, 4, 'D02', 60.00, 1, '2025-11-06 00:44:07');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `tickets_status`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `tickets_status`;
CREATE TABLE `tickets_status` (
`ticket_id` int(10) unsigned
,`event_name` varchar(255)
,`price` decimal(10,2)
,`seat_number` varchar(50)
,`status` varchar(9)
);

-- --------------------------------------------------------

--
-- Estrutura para view `customer_sales_summary`
--
DROP TABLE IF EXISTS `customer_sales_summary`;

DROP VIEW IF EXISTS `customer_sales_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `customer_sales_summary`  AS SELECT `c`.`id` AS `customer_id`, `c`.`name` AS `customer_name`, count(`s`.`id`) AS `number_of_tickets_purchased`, sum(`s`.`total_price`) AS `total_spent` FROM (`customers` `c` left join `sales` `s` on(`s`.`customer_id` = `c`.`id`)) GROUP BY `c`.`id` ;

-- --------------------------------------------------------

--
-- Estrutura para view `event_details`
--
DROP TABLE IF EXISTS `event_details`;

DROP VIEW IF EXISTS `event_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `event_details`  AS SELECT `e`.`id` AS `event_id`, `e`.`name` AS `event_name`, `e`.`description` AS `description`, `e`.`date` AS `date`, `e`.`time` AS `time`, `e`.`venue` AS `venue`, `e`.`capacity` AS `total_tickets`, count(`s`.`id`) AS `sold_tickets`, `e`.`capacity`- count(`s`.`id`) AS `available_tickets` FROM ((`events` `e` left join `tickets` `t` on(`e`.`id` = `t`.`event_id`)) left join `sales` `s` on(`t`.`id` = `s`.`ticket_id`)) GROUP BY `e`.`id`, `e`.`name`, `e`.`description`, `e`.`date`, `e`.`time`, `e`.`venue`, `e`.`capacity` ;

-- --------------------------------------------------------

--
-- Estrutura para view `event_sales_summary`
--
DROP TABLE IF EXISTS `event_sales_summary`;

DROP VIEW IF EXISTS `event_sales_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `event_sales_summary`  AS SELECT `e`.`id` AS `event_id`, `e`.`name` AS `event_name`, count(`s`.`id`) AS `number_of_tickets_sold`, sum(`s`.`total_price`) AS `total_revenue` FROM ((`events` `e` left join `tickets` `t` on(`t`.`event_id` = `e`.`id`)) left join `sales` `s` on(`s`.`ticket_id` = `t`.`id`)) GROUP BY `e`.`id` ;

-- --------------------------------------------------------

--
-- Estrutura para view `tickets_status`
--
DROP TABLE IF EXISTS `tickets_status`;

DROP VIEW IF EXISTS `tickets_status`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tickets_status`  AS SELECT `t`.`id` AS `ticket_id`, `e`.`name` AS `event_name`, `t`.`price` AS `price`, `t`.`seat_number` AS `seat_number`, CASE WHEN `s`.`id` is null THEN 'Available' ELSE 'Sold' END AS `status` FROM ((`tickets` `t` join `events` `e` on(`t`.`event_id` = `e`.`id`)) left join `sales` `s` on(`s`.`ticket_id` = `t`.`id`)) ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_ticket` (`ticket_id`),
  ADD KEY `fk_sales_customer` (`customer_id`);

--
-- Índices de tabela `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tickets_event` (`event_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sales_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
