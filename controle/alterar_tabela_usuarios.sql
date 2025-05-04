
ALTER TABLE usuarios 
    ADD COLUMN ativo BOOLEAN DEFAULT 1,
    ADD COLUMN desativado_por INT DEFAULT NULL,
    ADD COLUMN desativado_em TIMESTAMP NULL DEFAULT NULL,
    ADD FOREIGN KEY (desativado_por) REFERENCES usuarios(id) ON DELETE SET NULL;
