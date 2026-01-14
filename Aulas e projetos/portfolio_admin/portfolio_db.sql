-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30/10/2025 às 05:30
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
-- Banco de dados: `portfolio_db`
--
CREATE DATABASE IF NOT EXISTS `portfolio_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portfolio_db`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Web Development'),
(2, 'Graphic Design'),
(3, 'Data Science');

-- --------------------------------------------------------

--
-- Estrutura para tabela `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `creation_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `category_id`, `creation_date`) VALUES
(1, 'Portfolio Website', 'A personal website showcasing my work.', 1, '2023-06-15'),
(2, 'Logo Design for Client A', 'A modern logo design for a client.', 2, '2024-01-10'),
(3, 'Data Analysis Project', 'Analyzed sales data and generated insights.', 3, '2024-03-22');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_projects_by_category`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `view_projects_by_category`;
CREATE TABLE `view_projects_by_category` (
`project_id` int(11)
,`project_title` varchar(255)
,`project_description` text
,`category_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_projects_summary`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `view_projects_summary`;
CREATE TABLE `view_projects_summary` (
`project_id` int(11)
,`project_title` varchar(255)
,`project_description` text
,`category_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Estrutura para view `view_projects_by_category`
--
DROP TABLE IF EXISTS `view_projects_by_category`;

DROP VIEW IF EXISTS `view_projects_by_category`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_projects_by_category`  AS SELECT `p`.`id` AS `project_id`, `p`.`title` AS `project_title`, `p`.`description` AS `project_description`, `c`.`name` AS `category_name` FROM (`projects` `p` join `categories` `c` on(`p`.`category_id` = `c`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_projects_summary`
--
DROP TABLE IF EXISTS `view_projects_summary`;

DROP VIEW IF EXISTS `view_projects_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_projects_summary`  AS SELECT `p`.`id` AS `project_id`, `p`.`title` AS `project_title`, `p`.`description` AS `project_description`, `c`.`name` AS `category_name` FROM (`projects` `p` join `categories` `c` on(`p`.`category_id` = `c`.`id`)) ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
