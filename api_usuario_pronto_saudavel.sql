-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/11/2025 às 20:13
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
-- Banco de dados: `api_usuario_pronto_saudavel`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES
(1, 'Marmitas', 'Pratos completos e balanceados, perfeitos para almoço ou jantar.'),
(2, 'Sopas e Caldos', 'Opções leves e nutritivas para aquecer seu dia.'),
(3, 'Sucos e Bebidas', 'Sucos naturais prensados a frio e outras bebidas saudáveis.'),
(4, 'Sobremesas', 'Opções de sobremesas saudáveis e funcionais para fechar sua refeição.'),
(5, 'Outros Produtos', 'Temperos especiais, tortas e outros itens da chefe.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `endereco`
--

CREATE TABLE `endereco` (
  `id` int(11) NOT NULL,
  `cep` varchar(10) NOT NULL,
  `rua` varchar(255) NOT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(120) NOT NULL,
  `cidade` varchar(60) NOT NULL,
  `estado` char(2) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `item_pedido`
--

CREATE TABLE `item_pedido` (
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `valor_unitario` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `data_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('PENDENTE','PREPARANDO','ENVIANDO','ENTREGUE','CANCELADO') NOT NULL DEFAULT 'PENDENTE',
  `metodo_pagamento` enum('CARTAO DEBITO','CARTAO CREDITO','VR','VA','PIX') DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `endereco_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estoque` int(11) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `imagem_url` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `valor`, `estoque`, `ativo`, `imagem_url`, `categoria_id`) VALUES
(1, 'Marmita Fitness Frango', 'Delicioso frango grelhado com purê de batata doce e brócolis frescos.', 27.00, 50, 1, '/public/images/products/marmita_frango.jpg', 1),
(2, 'Marmita Low Carb Carne', 'Escalopes de filé mignon ao molho madeira com arroz de couve-flor.', 32.50, 40, 1, '/public/images/products/marmita_carne.jpg', 1),
(3, 'Marmita Vegana Grão de Bico', 'Saboroso curry de grão de bico com legumes variados e arroz integral.', 28.00, 30, 1, '/public/images/products/marmita_vegana.jpg', 1),
(4, 'Caldo Verde Detox', 'Tradicional caldo verde com couve, bacon artesanal e um toque de gengibre.', 19.90, 60, 1, '/public/images/products/caldo_verde.jpg', 2),
(5, 'Sopa de Abóbora com Gengibre', 'Sopa cremosa de abóbora cabotiá com gengibre e sementes tostadas.', 18.00, 45, 1, '/public/images/products/sopa_abobora.jpg', 2),
(6, 'Suco Verde Prensado a Frio', 'Suco detox de couve, maçã verde, limão, gengibre e hortelã. 500ml.', 14.00, 80, 1, '/public/images/products/suco_verde.jpg', 3),
(7, 'Suco de Laranja Natural', 'Puro suco de laranja espremido na hora. Sem adição de açúcar. 500ml.', 12.00, 100, 1, '/public/images/products/suco_laranja.jpg', 3),
(8, 'Sobremesa Fit - Brownie', 'Brownie de chocolate 70% com whey protein e nozes. Sem açúcar.', 15.00, 35, 1, '/public/images/products/brownie_fit.jpg', 4),
(9, 'Torta de Frango Caseira', 'Fatia generosa de torta de frango cremosa com massa integral.', 17.50, 25, 1, '/public/images/products/torta_frango.jpg', 5),
(10, 'Mix de Temperos da Chefe', 'Mix de ervas secas e especiarias selecionadas pela Personal Chefe.', 22.00, 70, 1, '/public/images/products/temperos.jpg', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp(),
  `tipo_usuario` enum('cliente','admin') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `telefone`, `data_cadastro`, `tipo_usuario`) VALUES
(1, 'Julio', 'julio@botelho.com', '$2y$10$/rml9sB35SqpbJaUQ.WYW.WcWTBQR0km4ZbnKMXiKBPXx.MHAsgVS', '11940404040', '2025-11-06 14:44:48', 'cliente'),
(3, 'Guilherme', 'guilherme@gmail.com', '$2y$10$ByV7N34kyzCitOi5fb/4KODi.AycPex2dk6t3NCCb/Cy3GAgDv6Wq', '1234567890', '2025-11-06 19:14:29', 'cliente');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `item_pedido`
--
ALTER TABLE `item_pedido`
  ADD PRIMARY KEY (`pedido_id`,`produto_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `endereco_id` (`endereco_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telefone` (`telefone`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `endereco`
--
ALTER TABLE `endereco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `endereco`
--
ALTER TABLE `endereco`
  ADD CONSTRAINT `endereco_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `item_pedido`
--
ALTER TABLE `item_pedido`
  ADD CONSTRAINT `item_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
