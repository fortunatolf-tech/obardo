-- ============================================================================
-- O BARDO - SEED INICIAL COM DADOS DE TORMENTA20 & D&D
-- ============================================================================

-- 1. Usuário Mestre Inicial (senha: Bardo2026!)
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`) VALUES
(1, 'Lucas Fortunato (Mestre)', 'mestre@obardo.com.br', '$argon2id$v=19$m=65536,t=4,p=1$Fid8R7Mg2Yahp3FdiLAqKg$3TiNMb+oWeIagXcfelVUq6/XzO3bOt95R2E4TE43WlE', 'admin'),
(2, 'Beatriz Escriba', 'beatriz@obardo.com.br', '$argon2id$v=19$m=65536,t=4,p=1$Fid8R7Mg2Yahp3FdiLAqKg$3TiNMb+oWeIagXcfelVUq6/XzO3bOt95R2E4TE43WlE', 'user'),
(3, 'Renan Ladino', 'renan@obardo.com.br', '$argon2id$v=19$m=65536,t=4,p=1$Fid8R7Mg2Yahp3FdiLAqKg$3TiNMb+oWeIagXcfelVUq6/XzO3bOt95R2E4TE43WlE', 'user');

-- 2. Campanha Modelo
INSERT INTO `campaigns` (`id`, `gm_user_id`, `title`, `code`, `rule_system`, `description`, `cover_url`) VALUES
(1, 1, 'O Coração de Rubi', 'BARDO-T20X', 'TORMENTA20', 'Uma jornada épica pelas terras de Arton em busca das relíquias ancestrais sob as ruínas da antiga Aslothia.', '/assets/covers/t20_ruby.webp');

-- 3. Membros da Campanha
INSERT INTO `campaign_members` (`campaign_id`, `user_id`, `member_role`) VALUES
(1, 1, 'gm'),
(1, 2, 'player'),
(1, 3, 'player');

-- 4. Fichas de Personagens Tormenta20 / D20
INSERT INTO `characters` (`id`, `campaign_id`, `user_id`, `name`, `class_and_level`, `race_origin`, `hp_current`, `hp_max`, `mp_current`, `mp_max`, `armor_class`, `attributes_data`, `skills_data`, `inventory_data`, `spells_data`, `notes`) VALUES
(1, 1, 1, 'Sir Galadhon', 'Guerreiro Arcano 4', 'Humano de Deheon', 34, 38, 12, 16, 19,
 '{"for": 17, "for_mod": 3, "des": 12, "des_mod": 1, "con": 15, "con_mod": 2, "int": 14, "int_mod": 2, "sab": 10, "sab_mod": 0, "car": 8, "car_mod": -1}',
 '{"atletismo": {"trained": true, "bonus": 7}, "luta": {"trained": true, "bonus": 7}, "fortitude": {"trained": true, "bonus": 6}, "guerra": {"trained": true, "bonus": 6}, "misticismo": {"trained": true, "bonus": 6}, "vontade": {"trained": true, "bonus": 4}, "iniciativa": {"trained": false, "bonus": 1}, "percepcao": {"trained": false, "bonus": 0}}',
 '[{"name": "Espada Longa de Aço-Rubi", "slots": 1, "equipped": true}, {"name": "Cota de Malha Épica", "slots": 2, "equipped": true}, {"name": "Escudo Pesado", "slots": 1, "equipped": true}, {"name": "Tochas (4)", "slots": 1, "equipped": false}, {"name": "Poção de Vida", "slots": 1, "equipped": false}]',
 '[{"name": "Armadura Arcana", "cost_pm": 1, "action": "Reação", "desc": "+5 na Defesa até o próximo turno"}, {"name": "Toque Chocante", "cost_pm": 1, "action": "Padrão", "desc": "2d8+2 de dano de eletricidade"}, {"name": "Golpe Pessoal Flamejante", "cost_pm": 2, "action": "Ataque", "desc": "+2d6 de dano de fogo no ataque"}]',
 'Devoto leal de Khalmyr. Busca redenção após a queda de sua antiga ordem nas colinas ocidentais.'),

(2, 1, 2, 'Elidyr da Torre Prateada', 'Arcanista Mago 4', 'Elfo Artoniano', 16, 24, 18, 26, 14,
 '{"for": 8, "for_mod": -1, "des": 14, "des_mod": 2, "con": 12, "con_mod": 1, "int": 19, "int_mod": 4, "sab": 14, "sab_mod": 2, "car": 12, "car_mod": 1}',
 '{"misticismo": {"trained": true, "bonus": 8}, "conhecimento": {"trained": true, "bonus": 8}, "vontade": {"trained": true, "bonus": 6}, "iniciativa": {"trained": true, "bonus": 4}, "percepcao": {"trained": true, "bonus": 4}}',
 '[{"name": "Cajado Arcano", "slots": 1, "equipped": true}, {"name": "Grimório de Feitiços", "slots": 1, "equipped": false}, {"name": "Manto de Proteção", "slots": 1, "equipped": true}]',
 '[{"name": "Bola de Fogo", "cost_pm": 3, "action": "Padrão", "desc": "6d6 de dano de fogo em área (Reflexos reduz à metade)"}, {"name": "Mísseis Mágicos", "cost_pm": 1, "action": "Padrão", "desc": "2 dardos de 1d4+1 de dano de essência sem erro"}, {"name": "Velocidade", "cost_pm": 3, "action": "Padrão", "desc": "+1 ação padrão extra por turno"}]',
 'Estudioso dos segredos da Tormenta e das runas primordiais.');

-- 5. Faixas Sonoras Padrão (CC0 / Domínio Público - O Bardo & Bardfy)
INSERT INTO `audio_tracks` (`id`, `title`, `category`, `file_url`, `duration_seconds`, `is_preset`) VALUES
(1, 'Taverna do Javali Saltitante', 'tavern', '/audio/ambient/taverna.ogg', 180, 1),
(2, 'Alaúdes de Arton (Música de Taverna)', 'tavern', '/audio/ambient/tavern_lute.wav', 64, 1),
(3, 'Feira Livre da Cidade Medieval', 'city', '/audio/ambient/cidade_medieval.ogg', 120, 1),
(4, 'Tambores de Guerra & Batalha Épica', 'combat', '/audio/ambient/combat_drums.wav', 64, 1),
(5, 'Tempestade Furiosa & Trovões', 'combat', '/audio/ambient/tempestade.ogg', 150, 1),
(6, 'Cripta Antiga & Ecos da Caverna', 'dungeon', '/audio/ambient/caverna.ogg', 240, 1),
(7, 'Dungeon Drone (Horror & Tormenta)', 'dungeon', '/audio/ambient/dungeon_drone.wav', 64, 1),
(8, 'Acampamento & Fogueira Crepitante', 'travel', '/audio/ambient/fogueira.ogg', 180, 1),
(9, 'Floresta Sombria à Noite', 'travel', '/audio/ambient/floresta_noite.ogg', 210, 1),
(10, 'Vento Uivante nas Montanhas', 'travel', '/audio/ambient/vento.ogg', 160, 1),
(11, 'Chuva na Estrada & Jornada', 'travel', '/audio/ambient/chuva.ogg', 190, 1),
(12, 'Ondas do Mar & Porto dos Aventureiros', 'travel', '/audio/ambient/mar.ogg', 140, 1);

-- 6. Estado Inicial de Sincronização
INSERT INTO `audio_sync_states` (`campaign_id`, `active_track_id`, `active_layer`, `playback_state`, `server_timestamp`, `start_offset_ms`) VALUES
(1, 1, 'music', 'playing', 1793380000000, 0);

-- 7. Crônica Inicial
INSERT INTO `chronicles` (`campaign_id`, `author_user_id`, `title`, `content_markdown`, `session_date`) VALUES
(1, 1, 'Capítulo I: Os Portões de Aslothia', 'O vento soprava cinzas frias sobre o vale enquanto a comitiva alcançava os pilares negros. Sir Galadhon ergueu seu escudo com o símbolo da balança enquanto Elidyr sentia o pulsar caótico da magia local.', '2026-10-30');

-- 8. Pista / Handout Inicial
INSERT INTO `handouts` (`campaign_id`, `title`, `description`, `image_url`, `is_revealed`) VALUES
(1, 'Carta do Cônsul Corrompido', 'A caravana de suprimentos não deve chegar a Yuvalin. Assegurem-se de que os aventureiros sejam detidos na garganta do dragão.', '/assets/handouts/letter1.webp', 1),
(1, 'Fragmento de Mapa da Cripta', 'Mostra passagem secreta atrás do altar de mármore.', '/assets/handouts/map1.webp', 1);

-- 9. Rolagem Inicial Auditável
INSERT INTO `dice_rolls` (`campaign_id`, `user_id`, `character_id`, `roller_name`, `formula`, `label`, `dice_breakdown`, `modifier`, `total_result`, `is_critical`) VALUES
(1, 1, 1, 'Sir Galadhon', '1d20+7', 'Ataque Espada Longa', '[20]', 7, 27, 1);
