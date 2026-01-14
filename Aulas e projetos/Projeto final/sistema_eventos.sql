-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/12/2025 às 19:30
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
-- Banco de dados: `sistema_eventos`
--
CREATE DATABASE IF NOT EXISTS `sistema_eventos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_eventos`;

DELIMITER $$
--
-- Procedimentos
--
DROP PROCEDURE IF EXISTS `CleanOldCartItems`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CleanOldCartItems` ()   BEGIN
    DELETE FROM cart WHERE added_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);
END$$

DROP PROCEDURE IF EXISTS `CreatePurchase`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CreatePurchase` (IN `p_user_id` INT, IN `p_event_id` INT, IN `p_quantity` INT, IN `p_attendee_name` VARCHAR(200), IN `p_attendee_email` VARCHAR(255))   BEGIN
    -- DECLARE: Variáveis locais para armazenar valores temporários
    DECLARE v_event_price DECIMAL(10,2);    -- Preço do evento
    DECLARE v_available_tickets INT;        -- Bilhetes disponíveis
    DECLARE v_purchase_code VARCHAR(20);    -- Código da compra
    DECLARE v_purchase_id INT;              -- ID da compra criada
    
    -- Verificar disponibilidade de bilhetes
    -- SELECT INTO: Armazena resultado da query nas variáveis
    SELECT price, available_tickets INTO v_event_price, v_available_tickets
    FROM events 
    WHERE id = p_event_id AND is_active = 1; -- Apenas eventos ativos
    
    -- IF: Verifica se há bilhetes suficientes
    IF v_available_tickets >= p_quantity THEN
        -- SET: Atribui valor à variável
        -- CONCAT: Concatena strings para criar código único
        -- NOW(): Data/hora atual
        -- RAND(): Número aleatório para garantir unicidade
        -- LPAD: Preenche com zeros à esquerda
        SET v_purchase_code = CONCAT('PUR', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 10000), 4, '0'));
        
        -- Inserir registro na tabela purchases
        INSERT INTO purchases (purchase_code, user_id, total_amount)
        VALUES (v_purchase_code, p_user_id, v_event_price * p_quantity);
        
        -- LAST_INSERT_ID(): Retorna o ID do último registro inserido
        SET v_purchase_id = LAST_INSERT_ID();
        
        -- Inserir bilhetes individuais
        -- Usa uma subquery para gerar múltiplos registros baseado na quantidade
        INSERT INTO tickets (purchase_id, event_id, ticket_code, attendee_name, attendee_email, price)
        SELECT 
            v_purchase_id,                                  -- ID da compra
            p_event_id,                                     -- ID do evento
            CONCAT('TICK', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 100000), 5, '0')), -- Código único
            p_attendee_name,                                -- Nome do participante
            p_attendee_email,                               -- Email do participante
            v_event_price                                   -- Preço do bilhete
        FROM (SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) numbers
        -- WHERE: Limita a quantidade de bilhetes inseridos
        WHERE numbers.n <= p_quantity;
        
        -- Atualizar contador de bilhetes disponíveis no evento
        UPDATE events 
        SET available_tickets = available_tickets - p_quantity
        WHERE id = p_event_id;
        
        -- SELECT: Retorna resultados para o chamador
        SELECT v_purchase_id as purchase_id, v_purchase_code as purchase_code;
    ELSE
        -- SIGNAL: Lança um erro personalizado se não há bilhetes suficientes
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Não há bilhetes suficientes disponíveis';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `newsletter` tinyint(1) DEFAULT 0,
  `status` enum('pending','read','replied','closed') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Acionadores `contacts`
--
DROP TRIGGER IF EXISTS `before_contacts_update`;
DELIMITER $$
CREATE TRIGGER `before_contacts_update` BEFORE UPDATE ON `contacts` FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'Rhudan Moura', 'rhudan@hotmail.com', '962691886', 'parceria', 'Quero ser parceiro', 1, '2025-12-08 17:38:29'),
(2, 'tarcisio Jeferson', 'tarcisio1214314@gmail.com', '355353535', 'problema', 'Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!! Mensagem teste de tamanho!!', 0, '2025-12-08 18:28:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `available_tickets` int(11) NOT NULL,
  `max_tickets_per_user` int(11) DEFAULT 4,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `short_description`, `event_date`, `end_date`, `location`, `address`, `price`, `available_tickets`, `max_tickets_per_user`, `category_id`, `image_url`, `is_featured`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Festival de Jazz de Outono', 'Jazz suave para noites frias.', 'O melhor do Jazz.', '2025-11-10 20:00:00', NULL, 'Casa da Música', NULL, 45.00, 0, 4, 1, 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?auto=format&fit=crop&w=1350&q=80', 0, 1, 1, '2025-12-02 17:48:27', '2025-12-02 17:48:27'),
(2, 'Web Summit 2025', 'Onde o futuro nasce.', 'Tecnologia e Inovação.', '2025-11-15 09:00:00', NULL, 'Altice Arena', NULL, 150.00, 0, 4, 5, 'https://images.unsplash.com/photo-1544144433-d50aff500b91?auto=format&fit=crop&w=1350&q=80', 0, 1, 1, '2025-12-02 17:48:27', '2025-12-02 17:48:27'),
(3, 'Teatro: O Fantasma', 'Clássico imperdível.', 'Drama no palco.', '2025-11-20 21:00:00', NULL, 'Teatro Nacional', NULL, 25.00, 0, 4, 2, 'uploads/events/event_692f3258e4a03.png', 0, 1, 1, '2025-12-02 17:48:27', '2025-12-02 18:39:20'),
(4, 'Maratona da Ponte', 'Corra sobre o tejo.', '42km de desafio.', '2025-11-28 07:00:00', NULL, 'Ponte 25 de Abril', NULL, 15.00, 0, 4, 3, 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=1350&q=80', 0, 1, 1, '2025-12-02 17:48:27', '2025-12-02 17:48:27'),
(5, 'Concerto de Natal Solidário', 'Celebre o Natal ajudando quem precisa com a Orquestra Sinfónica.', 'Música clássica de Natal.', '2025-12-20 18:00:00', NULL, 'Coliseu dos Recreios', NULL, 20.00, 493, 4, 1, 'uploads/events/event_692f35f4393cb.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 22:14:02'),
(6, 'O Lago dos Cisnes - Ballet', 'O ballet mais famoso do mundo interpretado pela companhia nacional.', 'Ballet clássico imperdível.', '2025-12-22 21:00:00', NULL, 'Teatro Camões', NULL, 55.00, 141, 4, 2, 'uploads/events/event_69371f051fb9b.jpeg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 18:55:01'),
(7, 'Exposição: Luzes de Lisboa', 'Fotografias noturnas da cidade.', 'Exposição fotográfica.', '2025-12-28 10:00:00', NULL, 'Museu da Eletricidade', NULL, 12.00, 1000, 4, 4, 'uploads/events/event_692f365d7946c.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 18:56:29'),
(8, 'Réveillon 2026', 'A maior festa de passagem de ano da cidade.', 'Festa de Ano Novo.', '2025-12-31 22:00:00', NULL, 'Praça do Comércio', NULL, 5.00, 5000, 4, 7, 'uploads/events/event_69371f195d457.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 18:55:21'),
(9, 'Stand-up Comedy: Rir é o Remédio', 'Uma noite de gargalhadas com comediantes convidados.', 'Comédia stand-up ao vivo.', '2026-01-05 22:00:00', NULL, 'Teatro Villaret', NULL, 18.00, 80, 4, 2, 'uploads/events/event_69371f76a807b.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 18:56:54'),
(10, 'Oficina de Ciência Divertida', 'Experiências malucas para pequenos cientistas.', 'Workshop educativo.', '2026-01-10 10:00:00', NULL, 'Pavilhão do Conhecimento', NULL, 10.00, 20, 4, 8, 'uploads/events/event_69371fabbdf63.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 18:57:47'),
(11, 'Rock in Rio Winter Edition', 'A versão de inverno do maior festival de rock.', 'Rock e Pop no inverno.', '2026-01-15 16:00:00', NULL, 'Parque da Bela Vista', NULL, 80.00, 5000, 4, 1, 'uploads/events/event_692f36d24d035.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 18:58:26'),
(12, 'Grande Prémio de Futsal', 'Final do campeonato nacional de Futsal.', 'Desporto de alta competição.', '2026-01-20 15:00:00', NULL, 'Pavilhão Atlântico', NULL, 15.00, 3000, 4, 3, 'uploads/events/event_692f3721428a1.jpeg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 18:59:45'),
(13, 'Marketing Digital Summit 2026', 'As tendências do futuro do marketing.', 'Conferência de negócios.', '2026-01-25 09:00:00', NULL, 'FIL - Feira Internacional', NULL, 90.00, 400, 4, 5, 'uploads/events/event_69371ff95c3fa.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 18:59:05'),
(14, 'Jantar às Escuras', 'Uma experiência sensorial única.', 'Jantar temático.', '2026-01-30 20:00:00', NULL, 'Restaurante Sensorial', NULL, 65.00, 40, 4, 6, 'uploads/events/event_693720578dd17.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 19:00:39'),
(15, 'Histórias na Biblioteca', 'Contos de fadas narrados ao vivo.', 'Leitura para crianças.', '2026-02-07 11:00:00', NULL, 'Biblioteca Municipal', NULL, 5.00, 30, 4, 8, 'uploads/events/event_693720a25c1f2.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 19:01:54'),
(16, 'Noite de Fado em Alfama', 'Jantar e concerto com os melhores fadistas da nova geração.', 'Fado tradicional e jantar.', '2026-02-10 20:30:00', NULL, 'Clube de Fado', NULL, 45.50, 50, 4, 1, 'uploads/events/event_692f376cb8c6b.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 19:01:00'),
(17, 'Festival do Chocolate', 'Um paraíso para os amantes de cacau.', 'Degustação de chocolates.', '2026-02-14 11:00:00', NULL, 'Campo Pequeno', NULL, 8.00, 2000, 4, 6, 'uploads/events/event_692f39bfc7332.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 19:10:55'),
(18, 'Romeu e Julieta Moderno', 'Uma releitura urbana do clássico de Shakespeare.', 'Teatro contemporâneo.', '2026-02-14 21:00:00', NULL, 'Teatro da Trindade', NULL, 25.00, 120, 4, 2, 'uploads/events/event_692f39cde08de.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 19:11:09'),
(19, 'Carnaval de Rua 2026', 'O maior desfile de carnaval da cidade.', 'Festa popular de rua.', '2026-02-17 15:00:00', NULL, 'Avenida da Liberdade', NULL, 5.00, 10000, 4, 7, 'uploads/events/event_692f39d9e5d76.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 19:28:36'),
(20, 'Workshop de Pintura a Óleo', 'Aprenda técnicas clássicas com mestres da pintura.', 'Workshop prático de arte.', '2026-02-20 14:00:00', NULL, 'Atelier de Belas Artes', NULL, 60.00, 15, 4, 4, 'uploads/events/event_692f39e79d613.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 19:11:35'),
(21, 'Startups & Investidores', 'Onde as ideias encontram o capital.', 'Networking e Pitch.', '2026-02-28 14:00:00', NULL, 'Hub Criativo do Beato', NULL, 45.00, 150, 4, 5, 'uploads/events/event_693720f62978d.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 19:03:18'),
(23, 'Rota dos Vinhos: Dão e Douro', 'Prova de vinhos selecionados.', 'Enologia e degustação.', '2026-03-05 18:00:00', NULL, 'Garrafeira Nacional', NULL, 35.00, 30, 4, 6, 'uploads/events/event_69372153a147c.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 19:04:51'),
(24, 'Visita Guiada: Lisboa Secreta', 'Descubra os segredos escondidos da capital.', 'Passeio cultural a pé.', '2026-03-15 10:00:00', NULL, 'Ponto de Encontro: Rossio', NULL, 10.00, 20, 4, 4, 'uploads/events/event_693721bcee106.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 19:06:36'),
(25, 'Sinfonia de Primavera', 'Dê as boas-vindas à primavera com Vivaldi.', 'Concerto clássico.', '2026-03-21 19:00:00', NULL, 'Centro Cultural de Belém', NULL, 35.00, 200, 4, 1, 'uploads/events/event_692f3ad102836.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 19:15:29'),
(26, 'Festival da Primavera', 'Música, flores e gastronomia ao ar livre.', 'Festival sazonal.', '2026-03-22 12:00:00', NULL, 'Jardim da Estrela', NULL, 5.00, 1000, 4, 7, 'uploads/events/event_692f3b45bff13.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-03 01:16:12'),
(27, 'Torneio de Ténis Open', 'Os melhores tenistas nacionais em competição.', 'Ténis ao ar livre.', '2026-04-10 09:00:00', NULL, 'Clube de Ténis do Jamor', NULL, 30.00, 500, 4, 3, 'uploads/events/event_69372211beaa4.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 19:08:01'),
(28, 'Feira Medieval', 'Uma viagem no tempo com trajes a rigor.', 'Evento histórico.', '2026-04-15 10:00:00', NULL, 'Castelo de São Jorge', NULL, 12.00, 800, 4, 7, 'uploads/events/event_692f3b1809ea7.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 19:16:40'),
(29, 'Zoológico: Dia dos Animais', 'Visita guiada especial aos bastidores do zoo.', 'Educação ambiental.', '2026-04-25 09:30:00', NULL, 'Jardim Zoológico', NULL, 22.00, 60, 4, 8, 'uploads/events/event_6937224acdac0.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-08 19:08:58'),
(30, 'Aula de Yoga no Parque', 'Conecte-se com a natureza neste evento de bem-estar.', 'Yoga para todos os níveis.', '2026-05-01 10:00:00', NULL, 'Parque Eduardo VII', NULL, 5.00, 100, 4, 3, 'uploads/events/event_692f3b27ca1d8.jpg', 0, 1, 1, '2025-12-02 17:57:23', '2025-12-02 19:16:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `event_categories`
--

DROP TABLE IF EXISTS `event_categories`;
CREATE TABLE `event_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `event_categories`
--

INSERT INTO `event_categories` (`id`, `name`, `description`, `is_active`, `created_at`) VALUES
(1, 'Música', 'Concertos, festivais e eventos musicais', 1, '2025-11-22 22:31:05'),
(2, 'Teatro', 'Peças de teatro, óperas e performances', 1, '2025-11-22 22:31:05'),
(3, 'Desporto', 'Eventos desportivos e competições', 1, '2025-11-22 22:31:05'),
(4, 'Arte & Cultura', 'Exposições, museus e eventos culturais', 1, '2025-11-22 22:31:05'),
(5, 'Conferências', 'Palestras, workshops e conferências', 1, '2025-11-22 22:31:05'),
(6, 'Gastronomia', 'Festivais de comida e eventos culinários', 1, '2025-11-22 22:31:05'),
(7, 'Festivais', 'Festivais temáticos e celebrações', 1, '2025-11-22 22:31:05'),
(8, 'Infantil', 'Eventos para crianças e famílias', 1, '2025-11-22 22:31:05');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `event_sales`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `event_sales`;
CREATE TABLE `event_sales` (
`id` int(11)
,`title` varchar(255)
,`event_date` datetime
,`price` decimal(10,2)
,`available_tickets` int(11)
,`tickets_sold` bigint(21)
,`tickets_available` bigint(22)
,`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `newsletter_subscriptions`
--

DROP TABLE IF EXISTS `newsletter_subscriptions`;
CREATE TABLE `newsletter_subscriptions` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unsubscribed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `pending_contacts`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `pending_contacts`;
CREATE TABLE `pending_contacts` (
`id` int(11)
,`first_name` varchar(100)
,`last_name` varchar(100)
,`email` varchar(255)
,`subject` varchar(100)
,`submission_date` timestamp
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `purchases`
--

DROP TABLE IF EXISTS `purchases`;
CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `purchase_code` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` enum('credit_card','debit_card','mbway','multibanco') DEFAULT 'credit_card',
  `payment_reference` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `purchases`
--

INSERT INTO `purchases` (`id`, `purchase_code`, `user_id`, `total_amount`, `payment_status`, `payment_method`, `payment_reference`, `created_at`, `paid_at`) VALUES
(1, 'PUR202512027053', 2, 40.00, 'paid', 'credit_card', NULL, '2025-12-03 01:41:43', '2025-12-03 01:41:43'),
(2, 'PUR202512028196', 2, 330.00, 'paid', 'credit_card', NULL, '2025-12-03 01:41:43', '2025-12-03 01:41:43'),
(3, 'PUR202512029586', 2, 165.00, 'paid', 'credit_card', NULL, '2025-12-03 01:42:59', '2025-12-03 01:42:59'),
(4, 'PUR202512035658', 2, 40.00, 'paid', 'credit_card', NULL, '2025-12-03 20:32:51', '2025-12-03 20:32:51'),
(5, 'PUR202512087017', 2, 40.00, 'paid', 'credit_card', NULL, '2025-12-08 21:35:36', '2025-12-08 21:35:36'),
(6, 'PUR202512082997', 2, 20.00, 'paid', 'credit_card', NULL, '2025-12-08 22:14:02', '2025-12-08 22:14:02');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `sales_report`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `sales_report`;
CREATE TABLE `sales_report` (
`purchase_code` varchar(20)
,`first_name` varchar(100)
,`last_name` varchar(100)
,`email` varchar(255)
,`total_amount` decimal(10,2)
,`payment_status` enum('pending','paid','failed','refunded')
,`payment_method` enum('credit_card','debit_card','mbway','multibanco')
,`created_at` timestamp
,`ticket_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
(1, 'site_name', 'Sistema de Eventos', 'string', 'Nome do site', '2025-11-22 22:31:05'),
(2, 'currency', 'EUR', 'string', 'Moeda padrão', '2025-11-22 22:31:05'),
(3, 'max_tickets_per_event', '10', 'number', 'Máximo de bilhetes por evento por utilizador', '2025-11-22 22:31:05'),
(4, 'booking_timeout_minutes', '15', 'number', 'Tempo em minutos para expiração do carrinho', '2025-11-22 22:31:05'),
(5, 'contact_email', 'info@eventos.pt', 'string', 'Email de contacto principal', '2025-11-22 22:31:05'),
(6, 'support_email', 'suporte@eventos.pt', 'string', 'Email de suporte', '2025-11-22 22:31:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `ticket_code` varchar(20) NOT NULL,
  `attendee_name` varchar(200) NOT NULL,
  `attendee_email` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tickets`
--

INSERT INTO `tickets` (`id`, `purchase_id`, `event_id`, `ticket_code`, `attendee_name`, `attendee_email`, `price`, `is_used`, `used_at`, `created_at`) VALUES
(1, 1, 5, 'TICK2025120247270', 'Rhudan Moura', 'rhuds@hotmail.com', 20.00, 0, NULL, '2025-12-03 01:41:43'),
(2, 1, 5, 'TICK2025120224754', 'Rhudan Moura', 'rhuds@hotmail.com', 20.00, 0, NULL, '2025-12-03 01:41:43'),
(4, 2, 6, 'TICK2025120235539', 'Rhudan Moura', 'rhuds@hotmail.com', 55.00, 0, NULL, '2025-12-03 01:41:43'),
(5, 2, 6, 'TICK2025120231815', 'Rhudan Moura', 'rhuds@hotmail.com', 55.00, 0, NULL, '2025-12-03 01:41:43'),
(7, 2, 6, 'TICK2025120266848', 'Rhudan Moura', 'rhuds@hotmail.com', 55.00, 0, NULL, '2025-12-03 01:41:43'),
(8, 2, 6, 'TICK2025120276864', 'Rhudan Moura', 'rhuds@hotmail.com', 55.00, 0, NULL, '2025-12-03 01:41:43'),
(11, 3, 6, 'TICK2025120273634', 'Rhudan Moura', 'rhuds@hotmail.com', 55.00, 0, NULL, '2025-12-03 01:42:59'),
(12, 3, 6, 'TICK2025120280579', 'Rhudan Moura', 'rhuds@hotmail.com', 55.00, 0, NULL, '2025-12-03 01:42:59'),
(13, 3, 6, 'TICK2025120281992', 'Rhudan Moura', 'rhuds@hotmail.com', 55.00, 0, NULL, '2025-12-03 01:42:59'),
(14, 4, 5, 'TICK2025120335083', 'Rhudan Moura', 'rhuds@hotmail.com', 20.00, 0, NULL, '2025-12-03 20:32:51'),
(15, 4, 5, 'TICK2025120305670', 'Rhudan Moura', 'rhuds@hotmail.com', 20.00, 0, NULL, '2025-12-03 20:32:51'),
(16, 5, 5, 'TICK2025120868956', 'Rhudan Moura', 'rhuds@hotmail.com', 20.00, 0, NULL, '2025-12-08 21:35:36'),
(17, 5, 5, 'TICK2025120834271', 'Rhudan Moura', 'rhuds@hotmail.com', 20.00, 0, NULL, '2025-12-08 21:35:36'),
(19, 6, 5, 'TICK2025120840051', 'Rhudan Moura', 'rhuds@hotmail.com', 20.00, 0, NULL, '2025-12-08 22:14:02');

--
-- Acionadores `tickets`
--
DROP TRIGGER IF EXISTS `after_ticket_delete`;
DELIMITER $$
CREATE TRIGGER `after_ticket_delete` AFTER DELETE ON `tickets` FOR EACH ROW BEGIN
    -- TAREFA A: Repor o Estoque (Aumenta +1 no evento)
    UPDATE events 
    SET available_tickets = available_tickets + 1 
    WHERE id = OLD.event_id;

    -- TAREFA B: Atualizar o Dinheiro (Subtrai o preço da compra original)
    UPDATE purchases
    SET total_amount = total_amount - OLD.price
    WHERE id = OLD.purchase_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `before_ticket_insert`;
DELIMITER $$
CREATE TRIGGER `before_ticket_insert` BEFORE INSERT ON `tickets` FOR EACH ROW BEGIN
    IF NEW.ticket_code IS NULL THEN
        SET NEW.ticket_code = CONCAT('TICK', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 100000), 5, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('admin','client') DEFAULT 'client',
  `email_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `user_type`, `email_verified`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Sistema', 'admin@eventos.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'admin', 0, 1, '2025-11-22 22:31:05', '2025-11-27 05:35:22'),
(2, 'Rhudan', 'Moura', 'rhuds@hotmail.com', '$2y$10$NtN8TyOiqEydHw7N69z62OlXc0lVwJX3OTo2NXIb9x91DELIujuhi', '', 'admin', 0, 1, '2025-11-25 23:45:06', '2025-12-10 18:17:37'),
(3, 'Rhu', 'Moura', 'rhu@hotmail.com', '$2y$10$QMEDfG0wHxWc4LKPz8FHCO3zCSYMprvdfLR/y3TQtEDy3KIQ8CMoG', NULL, 'client', 0, 0, '2025-12-03 00:35:42', '2025-12-03 00:38:50');

-- --------------------------------------------------------

--
-- Estrutura para view `event_sales`
--
DROP TABLE IF EXISTS `event_sales`;

DROP VIEW IF EXISTS `event_sales`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `event_sales`  AS SELECT `e`.`id` AS `id`, `e`.`title` AS `title`, `e`.`event_date` AS `event_date`, `e`.`price` AS `price`, `e`.`available_tickets` AS `available_tickets`, count(`t`.`id`) AS `tickets_sold`, `e`.`available_tickets`- count(`t`.`id`) AS `tickets_available`, sum(`t`.`price`) AS `total_revenue` FROM (`events` `e` left join `tickets` `t` on(`e`.`id` = `t`.`event_id` and `t`.`purchase_id` in (select `purchases`.`id` from `purchases` where `purchases`.`payment_status` = 'paid'))) GROUP BY `e`.`id` ;

-- --------------------------------------------------------

--
-- Estrutura para view `pending_contacts`
--
DROP TABLE IF EXISTS `pending_contacts`;

DROP VIEW IF EXISTS `pending_contacts`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pending_contacts`  AS SELECT `contacts`.`id` AS `id`, `contacts`.`first_name` AS `first_name`, `contacts`.`last_name` AS `last_name`, `contacts`.`email` AS `email`, `contacts`.`subject` AS `subject`, `contacts`.`submission_date` AS `submission_date` FROM `contacts` WHERE `contacts`.`status` = 'pending' ORDER BY `contacts`.`submission_date` DESC ;

-- --------------------------------------------------------

--
-- Estrutura para view `sales_report`
--
DROP TABLE IF EXISTS `sales_report`;

DROP VIEW IF EXISTS `sales_report`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sales_report`  AS SELECT `p`.`purchase_code` AS `purchase_code`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `u`.`email` AS `email`, `p`.`total_amount` AS `total_amount`, `p`.`payment_status` AS `payment_status`, `p`.`payment_method` AS `payment_method`, `p`.`created_at` AS `created_at`, count(`t`.`id`) AS `ticket_count` FROM ((`purchases` `p` join `users` `u` on(`p`.`user_id` = `u`.`id`)) left join `tickets` `t` on(`p`.`id` = `t`.`purchase_id`)) GROUP BY `p`.`id` ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_event` (`user_id`,`event_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Índices de tabela `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_submission_date` (`submission_date`);

--
-- Índices de tabela `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_event_date` (`event_date`),
  ADD KEY `idx_location` (`location`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_active` (`is_active`);

--
-- Índices de tabela `event_categories`
--
ALTER TABLE `event_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Índices de tabela `newsletter_subscriptions`
--
ALTER TABLE `newsletter_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Índices de tabela `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_code` (`purchase_code`),
  ADD KEY `idx_purchase_code` (`purchase_code`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Índices de tabela `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Índices de tabela `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_code` (`ticket_code`),
  ADD KEY `idx_ticket_code` (`ticket_code`),
  ADD KEY `idx_purchase_id` (`purchase_id`),
  ADD KEY `idx_event_id` (`event_id`),
  ADD KEY `idx_is_used` (`is_used`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `event_categories`
--
ALTER TABLE `event_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `newsletter_subscriptions`
--
ALTER TABLE `newsletter_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
