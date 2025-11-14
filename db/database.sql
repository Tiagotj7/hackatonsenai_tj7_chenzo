CREATE TABLE sectors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(150) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE ticket_status (
  id TINYINT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(30) NOT NULL UNIQUE,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE request_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  setor_id INT NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_request_types_sector FOREIGN KEY (setor_id)
    REFERENCES sectors (id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE users_admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  cargo VARCHAR(100) NOT NULL,
  setor_id INT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_admin_sector FOREIGN KEY (setor_id)
    REFERENCES sectors (id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  protocolo VARCHAR(20) NOT NULL UNIQUE,
  solicitante_nome VARCHAR(150) NOT NULL,
  matricula VARCHAR(50) NOT NULL,
  cargo VARCHAR(100) NOT NULL,
  curso VARCHAR(100) NULL,
  local_problema VARCHAR(100) NOT NULL,
  descricao TEXT NOT NULL,
  tipo_id INT NOT NULL,
  setor_id INT NOT NULL,
  prioridade ENUM('Urgente','Média','Baixa') NOT NULL,
  email VARCHAR(150) NULL,
  status_id TINYINT NOT NULL,
  image_path VARCHAR(255) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  opened_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tickets_tipo FOREIGN KEY (tipo_id)
    REFERENCES request_types (id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_tickets_setor FOREIGN KEY (setor_id)
    REFERENCES sectors (id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_tickets_status FOREIGN KEY (status_id)
    REFERENCES ticket_status (id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE ticket_movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  user_id INT NULL,
  status_id TINYINT NOT NULL,
  resposta TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mov_ticket FOREIGN KEY (ticket_id)
    REFERENCES tickets (id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_mov_user FOREIGN KEY (user_id)
    REFERENCES users_admin (id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_mov_status FOREIGN KEY (status_id)
    REFERENCES ticket_status (id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO ticket_status (id, nome) VALUES
  (1, 'Aberta'), (2, 'Em andamento'), (3, 'Concluída')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

INSERT INTO sectors (id, nome, email) VALUES
  (1, 'TI', 'ti@exemplo.local'),
  (2, 'Manutenção', 'manutencao@exemplo.local'),
  (3, 'Estrutural', 'estrutural@exemplo.local'),
  (4, 'Secretaria', 'secretaria@exemplo.local')
ON DUPLICATE KEY UPDATE nome = VALUES(nome), email = VALUES(email);

INSERT INTO request_types (id, nome, setor_id) VALUES
  (1, 'Suporte TI', 1),
  (2, 'Infra TI (Rede/Internet)', 1),
  (3, 'Equipamento de Laboratório', 2),
  (4, 'Predial/Elétrica', 3),
  (5, 'Secretaria/Acadêmico', 4)
ON DUPLICATE KEY UPDATE nome = VALUES(nome), setor_id = VALUES(setor_id);