# O BARDO - PLATAFORMA WEB IMERSIVA PARA RPG DE MESA TRADICIONAL
## Documentação Completa das APIs do Sistema (REST & Realtime SSE)

---

### Identificação do Documento
- **Projeto:** O Bardo
- **Identificador:** API-OBAR-2026-V1
- **Versão da API:** v1
- **Padrão:** OpenAPI 3.1.0 / RESTful JSON / SSE (Server-Sent Events)
- **Base URL:** `https://obardo.com.br/api/v1`
- **Repositório:** [https://github.com/fortunatolf-tech/obardo](https://github.com/fortunatolf-tech/obardo)

---

## 1. Padrões Globais, Autenticação e Tratamento de Erros

### 1.1. Cabeçalhos Padrão
Todas as requisições para a API devem incluir:
- `Content-Type: application/json` (para métodos POST, PUT, PATCH);
- `Accept: application/json` (ou `text/event-stream` para conexões em tempo real);
- `Authorization: Bearer <TOKEN_DE_SESSAO>` (obrigatório para endpoints protegidos).

### 1.2. Padrão de Erros (RFC 7807 Problem Details)
Todas as respostas de erro retornam um objeto padronizado com código HTTP apropriado:

```json
{
  "type": "https://obardo.com.br/errors/validation-failed",
  "title": "Erro de Validação de Dados",
  "status": 422,
  "detail": "O campo 'formula' de rolagem de dados é inválido.",
  "instance": "/api/v1/campaigns/12/dice/roll",
  "invalid_params": [
    {
      "name": "formula",
      "reason": "Notação de dados '2d99999' ultrapassa o limite permitido de faces."
    }
  ]
}
```

---

## 2. Módulo de Autenticação e Usuários (`/auth`)

### 2.1. `POST /api/v1/auth/register`
Cria uma nova conta de usuário.

- **Requisição:**
```json
{
  "name": "Lucas Mestre",
  "email": "mestre@obardo.com.br",
  "password": "SenhaUltraSegura2026!",
  "terms_accepted": true
}
```

- **Resposta de Sucesso (201 Created):**
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "Lucas Mestre",
      "email": "mestre@obardo.com.br",
      "role": "user",
      "created_at": "2026-10-30T14:30:00Z"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "expires_in": 86400
  }
}
```

---

### 2.2. `POST /api/v1/auth/login`
Autentica o usuário e emite o token de sessão.

- **Requisição:**
```json
{
  "email": "mestre@obardo.com.br",
  "password": "SenhaUltraSegura2026!"
}
```

- **Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "Lucas Mestre",
      "email": "mestre@obardo.com.br",
      "role": "user"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "expires_in": 86400
  }
}
```

---

## 3. Módulo de Campanhas (`/campaigns`)

### 3.1. `POST /api/v1/campaigns`
Cria uma nova campanha de RPG. O criador torna-se o Mestre (*GM*).

- **Requisição:**
```json
{
  "title": "A Maldição de Valfenda Negra",
  "rule_system": "OSR_BX",
  "description": "Uma masmorra clássica e impiedosa sob as colinas cinzentas.",
  "cover_image_url": "https://obardo.com.br/assets/covers/dungeon1.webp"
}
```

- **Resposta de Sucesso (201 Created):**
```json
{
  "status": "success",
  "data": {
    "id": 12,
    "title": "A Maldição de Valfenda Negra",
    "code": "BARDO-9X4F",
    "rule_system": "OSR_BX",
    "gm_user_id": 1,
    "invite_url": "https://obardo.com.br/join/BARDO-9X4F",
    "created_at": "2026-10-30T14:35:00Z"
  }
}
```

---

### 3.2. `POST /api/v1/campaigns/{id}/join`
Permite a um jogador ingressar na campanha utilizando o código de convite.

- **Requisição:**
```json
{
  "invite_code": "BARDO-9X4F"
}
```

- **Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Você ingressou com sucesso na campanha!",
  "data": {
    "campaign_id": 12,
    "member_role": "player",
    "joined_at": "2026-10-30T14:40:00Z"
  }
}
```

---

## 4. Módulo de Fichas de Personagem (`/characters`)

### 4.1. `POST /api/v1/campaigns/{campaign_id}/characters`
Cria um novo personagem associado à campanha.

- **Requisição:**
```json
{
  "name": "Thorgar, o Destemido",
  "class_and_level": "Guerreiro 3",
  "hp_max": 28,
  "hp_current": 28,
  "armor_class": 16,
  "attributes": {
    "forca": 16,
    "destreza": 12,
    "constituicao": 15,
    "inteligencia": 9,
    "sabedoria": 10,
    "carisma": 8
  },
  "inventory": [
    { "id": "item-1", "name": "Espada Longa", "weight_slots": 1, "quantity": 1, "equipped": true },
    { "id": "item-2", "name": "Cota de Malha", "weight_slots": 3, "quantity": 1, "equipped": true },
    { "id": "item-3", "name": "Tochas (6)", "weight_slots": 1, "quantity": 6, "equipped": false }
  ]
}
```

- **Resposta de Sucesso (201 Created):**
```json
{
  "status": "success",
  "data": {
    "id": 45,
    "campaign_id": 12,
    "user_id": 2,
    "name": "Thorgar, o Destemido",
    "status": "alive",
    "encumbrance_status": "unencumbered"
  }
}
```

---

### 4.2. `PATCH /api/v1/characters/{id}/status`
Atualização rápida de Pontos de Vida e condições (disparada pelo Jogador ou Mestre).

- **Requisição:**
```json
{
  "hp_current": 21,
  "conditions": ["envenenado"]
}
```

- **Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 45,
    "hp_current": 21,
    "hp_max": 28,
    "conditions": ["envenenado"],
    "broadcasted": true
  }
}
```

---

## 5. Módulo de Rolagem de Dados (`/dice`)

### 5.1. `POST /api/v1/campaigns/{campaign_id}/dice/roll`
Processa uma rolagem de dados avaliada matematicamente no backend.

- **Requisição:**
```json
{
  "character_id": 45,
  "formula": "1d20+3",
  "purpose": "Ataque com Espada Longa",
  "is_secret": false
}
```

- **Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "data": {
    "roll_id": 1024,
    "roller_name": "Thorgar, o Destemido",
    "formula": "1d20+3",
    "purpose": "Ataque com Espada Longa",
    "dice_results": [17],
    "modifier": 3,
    "total": 20,
    "is_critical": false,
    "is_secret": false,
    "timestamp": "2026-10-30T14:45:12.184Z"
  }
}
```

---

## 6. Módulo de Áudio e Ambientação Sincronizada (`/audio`)

### 6.1. `POST /api/v1/campaigns/{campaign_id}/audio/sync`
**Acesso Exclusivo do Mestre (GMOnly).**  
Comanda a reprodução, pausa, volume ou efeito sonoro em todos os dispositivos da sessão.

- **Requisição de Exemplo 1 (Iniciar Trilha de Combate):**
```json
{
  "action": "PLAY_TRACK",
  "layer": "music",
  "track_id": 88,
  "track_title": "Batalha nas Catacumbas",
  "track_url": "https://obardo.com.br/storage/audio/tracks/battle_catacombs.ogg",
  "fade_seconds": 3,
  "start_offset_ms": 0
}
```

- **Requisição de Exemplo 2 (Disparo de Efeito Sonoro / SFX):**
```json
{
  "action": "TRIGGER_SFX",
  "sfx_id": 14,
  "sfx_title": "Trovão Ensurdecedor",
  "sfx_url": "https://obardo.com.br/storage/audio/sfx/thunder_crack.ogg",
  "volume": 1.0
}
```

- **Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Comando sonoro transmitido para todos os membros da sessão.",
  "data": {
    "server_timestamp": 1793371512390,
    "active_music_track_id": 88,
    "listeners_count": 5
  }
}
```

---

## 7. Módulo de Reviravoltas da Trama (`/plots`)

### 7.1. `POST /api/v1/campaigns/{campaign_id}/plots/trigger`
**Acesso Exclusivo do Mestre (GMOnly).**  
Aciona uma reviravolta instantânea na narrativa, impactando a tela e os autofalantes de todo o grupo.

- **Requisição:**
```json
{
  "twist_type": "BETRAYAL",
  "headline": "A MÁSCARA CAI!",
  "description": "Lorde Cassian desembainha sua adaga embebida em veneno negro. Os guardas bloqueiam as saídas da sala do trono.",
  "audio_sfx_url": "https://obardo.com.br/storage/audio/sfx/dramatic_reveal.ogg",
  "bg_theme": "crimson_pulse",
  "duration_seconds": 8
}
```

- **Resposta de Sucesso (200 OK):**
```json
{
  "status": "success",
  "message": "Reviravolta da trama ativada com sucesso!",
  "data": {
    "twist_id": 55,
    "triggered_at": "2026-10-30T14:50:00Z"
  }
}
```

---

## 8. Canal de Eventos em Tempo Real (SSE - Server-Sent Events)

### 8.1. `GET /api/v1/campaigns/{campaign_id}/events/stream`
Conexão persistente HTTP (`text/event-stream`) aberta pelo navegador de cada participante.

- **Headers de Conexão:**
  - `Accept: text/event-stream`
  - `Authorization: Bearer <TOKEN>`
- **Exemplo de Fluxo de Mensagens Recebidas pelo Navegador:**

```
event: connection_established
data: {"message":"Conectado à Taverna da campanha 12","server_time":1793371800100}

event: audio_sync_command
data: {"action":"PLAY_TRACK","layer":"music","track_url":"https://obardo.com.br/storage/audio/tracks/battle.ogg","server_timestamp":1793371805000}

event: dice_roll_broadcast
data: {"roll_id":1024,"roller":"Thorgar","formula":"1d20+3","total":20}

event: plot_twist_broadcast
data: {"headline":"A MÁSCARA CAI!","description":"Lorde Cassian traiu a guilda!","bg_theme":"crimson_pulse","sfx_url":"https://obardo.com.br/storage/audio/sfx/dramatic.ogg"}
```

---

*Documentação oficial de APIs RESTful e SSE homologada para o projeto O Bardo.*
