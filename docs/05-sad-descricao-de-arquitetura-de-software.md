# O BARDO - PLATAFORMA WEB IMERSIVA PARA RPG DE MESA TRADICIONAL
## Descrição de Arquitetura de Software (SAD - IEEE Std 42010 / C4 Model)

---

### Controle do Documento
- **Projeto:** O Bardo (obardo.com.br)
- **Identificador:** SAD-OBAR-2026-V1
- **Versão:** 1.0.0
- **Data:** 30 de Outubro de 2026
- **Status:** Aprovado para Implementação
- **Autor:** Arquiteto de Software Chefe
- **Repositório:** [https://github.com/fortunatolf-tech/obardo](https://github.com/fortunatolf-tech/obardo)

---

## 1. Visão Geral da Arquitetura e Decisões Técnicas (ADRs)

A arquitetura do sistema **O Bardo** foi concebida sob os princípios da Arquitetura Hexagonal (Ports & Adapters) combinada com *Clean Architecture*, priorizando:
1. **Desacoplamento estrito do domínio:** As regras de negócio de RPG (cálculo de fichas, dados, inventário, sincronia) são independentes de frameworks web ou bancos de dados específicos;
2. **Eficiência extrema em PHP 8.3:** Uso intensivo dos recursos modernos de tipagem estrita, classes imutáveis e compilação JIT;
3. **Simplicidade Operacional e Alta Disponibilidade:** Eliminação de arquiteturas excessivamente complexas que demandam múltiplos microsserviços pesados;
4. **Baixa Latência de Áudio:** Utilização da Web Audio API no cliente aliada a um canal ultraleve de Server-Sent Events (SSE) e WebSockets.

### 1.1. Registros de Decisão de Arquitetura (ADRs - Architecture Decision Records)

- **ADR-01: Adoção do PHP 8.3 Moderno**
  - *Contexto:* Necessidade de um backend robusto, de alta performance e baixo consumo de memória em servidores dedicados.
  - *Decisão:* Adotar PHP 8.3 nativo com `strict_types=1`, `readonly classes`, constantes tipadas (`typed class constants`), `json_validate()` para sanitização rápida de payloads e OPcache com JIT (*Tracing*).
  - *Consequência:* Performance equivalente a plataformas compiladas com manutenção limpa e custo de infraestrutura reduzido.
- **ADR-02: Mecanismo de Sincronização em Tempo Real (SSE + Fallback WebSocket)**
  - *Contexto:* Sincronizar o estado de áudio e reviravoltas do Mestre com todos os participantes.
  - *Decisão:* Utilizar Server-Sent Events (SSE) como transporte principal para distribuição de eventos Mestre $\rightarrow$ Jogadores e WebSockets para chat bidirecional em tempo real. SSE opera sobre HTTP padrão, é imune a bloqueios de proxies corporativos e consome frações mínimas de recursos em Nginx.
- **ADR-03: Web Audio API no Frontend**
  - *Contexto:* Execução simultânea de música contínua + ambiência de chuva + disparos pontuais de efeitos sonoros sem travar a interface.
  - *Decisão:* Criar um motor de áudio cliente em JavaScript puro utilizando nós de ganho independentes (`GainNode`), `AudioContext` com compensação de *clock drift* e decodificação assíncrona de buffers.
- **ADR-04: Banco de Dados Relacional MariaDB 10.11 com JSON Columns**
  - *Contexto:* As fichas de personagem de RPG possuem atributos dinâmicos e inventários que variam conforme o sistema (OSR vs D&D 5e vs Tormenta20).
  - *Decisão:* Estrutura relacional sólida para entidades principais (Usuários, Campanhas, Membros, Sessões, Trilhas) combinada com colunas JSON indexadas e validadas para a carga flexível dos personagens.

---

## 2. Visão Arquitetural em C4 Model

### 2.1. Nível 1: Diagrama de Contexto de Sistema (System Context)

```mermaid
flowchart TD
    Mestre["Mestre da Campanha (GM)<br/>[Navegador Desktop/Mobile]"]
    Jogador["Jogadores da Campanha<br/>[Navegador Smartphone/Tablet/PC]"]
    
    subgraph OBardoSystem["O Bardo - Sistema de RPG Web"]
        AppWeb["Aplicação Web O Bardo<br/>(PHP 8.3 + MariaDB)"]
    end
    
    S3Storage["Armazenamento de Mídia / CDN<br/>(Faixas de Áudio & Handouts)"]
    OAuthServer["Provedores OAuth2<br/>(Google / Discord)"]
    
    Mestre -->|Gerencia campanha, dispara áudio e reviravoltas| AppWeb
    Jogador -->|Edita ficha, rola dados, ouve som sincronizado| AppWeb
    AppWeb -->|Armazena e recupera faixas de áudio| S3Storage
    AppWeb -->|Autentica identidade alternativa| OAuthServer
```

---

### 2.2. Nível 2: Diagrama de Contêineres (Containers Diagram)

```mermaid
flowchart TD
    subgraph Clientes["Navegadores Clientes"]
        SPA["Frontend SPA / PWA<br/>[HTML5, CSS3 Pergaminho & Ferro, ES6 Vanilla]<br/>Web Audio API Engine"]
    end

    subgraph Servidor["Servidor de Aplicação obardo.com.br"]
        ReverseProxy["Nginx 1.27 Reverse Proxy<br/>Terminação SSL / TLS 1.3"]
        PHPFPM["PHP 8.3 FPM Application Core<br/>[PSR-4, Clean Architecture, DI Container]"]
        RealtimeDaemon["Realtime Event Hub (SSE / WebSockets)<br/>[PHP Daemon Assíncrono / Redis Pub-Sub]"]
        DB[("MariaDB 10.11<br/>Schema Relacional 3FN + JSON")]
        LocalStorage["File Storage Local Otimizado<br/>/storage/audio & /storage/handouts"]
    end

    SPA -->|HTTPS / REST API JSON| ReverseProxy
    SPA -->|EventStream SSE / WSS| ReverseProxy
    ReverseProxy -->|FastCGI| PHPFPM
    ReverseProxy -->|Proxy Pass| RealtimeDaemon
    PHPFPM -->|PDO Prepared Statements| DB
    PHPFPM -->|Salva Mídias Sanitizadas| LocalStorage
    PHPFPM -->|Dispara Eventos de Sessão| RealtimeDaemon
    RealtimeDaemon -->|Transmissão Contínua de Eventos| SPA
```

---

### 2.3. Nível 3: Diagrama de Componentes do Backend (Component Diagram)

```mermaid
flowchart LR
    subgraph "Camada de Apresentação (Presentation Layer)"
        AuthController["AuthController"]
        CampaignController["CampaignController"]
        CharacterController["CharacterController"]
        AudioSyncController["AudioSyncController"]
        ChronicleController["ChronicleController"]
        SSEStreamController["SSEStreamController"]
    end

    subgraph "Camada de Aplicação (Application Layer)"
        AuthService["AuthService"]
        CampaignService["CampaignService"]
        CharacterService["CharacterService"]
        AudioEngineService["AudioEngineService"]
        DiceRollerEngine["DiceRollerEngine"]
        PlotTwistService["PlotTwistService"]
    end

    subgraph "Camada de Domínio (Domain Layer)"
        UserEntity["User Entity"]
        CampaignEntity["Campaign Entity"]
        CharacterSheetEntity["CharacterSheet Entity"]
        AudioTrackEntity["AudioTrack Entity"]
        PlotTwistEvent["PlotTwistEvent"]
    end

    subgraph "Camada de Infraestrutura (Infrastructure Layer)"
        UserRepository["MariaDBUserRepository"]
        CampaignRepository["MariaDBCampaignRepository"]
        CharacterRepository["MariaDBCharacterRepository"]
        AudioRepository["MariaDBAudioRepository"]
        SessionEventBroadcaster["Redis/SSEBroadcaster"]
    end

    AuthController --> AuthService
    CampaignController --> CampaignService
    CharacterController --> CharacterService
    AudioSyncController --> AudioEngineService
    ChronicleController --> PlotTwistService
    
    AuthService --> UserRepository
    CampaignService --> CampaignRepository
    CharacterService --> CharacterRepository
    AudioEngineService --> AudioRepository
    AudioEngineService --> SessionEventBroadcaster
    PlotTwistService --> SessionEventBroadcaster
    
    CharacterService --> DiceRollerEngine
```

---

## 3. Arquitetura em Camadas (Clean / Hexagonal)

O código-fonte segue a estrutura de namespaces em conformidade com o padrão PSR-4 (`OBardo\`):

```
src/
├── Domain/
│   ├── Campaign/
│   │   ├── Model/Campaign.php
│   │   ├── Repository/CampaignRepositoryInterface.php
│   │   └── ValueObject/CampaignCode.php
│   ├── Character/
│   │   ├── Model/CharacterSheet.php
│   │   ├── Model/AttributeSet.php
│   │   ├── Model/InventoryItem.php
│   │   ├── Service/DiceFormulaParser.php
│   │   └── Strategy/RuleSystemStrategyInterface.php (OSR, D20, Tormenta)
│   ├── Audio/
│   │   ├── Model/AudioTrack.php
│   │   ├── Model/AudioSyncState.php
│   │   └── ValueObject/PlaybackCommand.php
│   └── Story/
│       ├── Model/ChronicleEntry.php
│       ├── Model/Handout.php
│       └── Model/PlotTwist.php
├── Application/
│   ├── UseCases/
│   │   ├── Audio/BroadcastAudioCommandUseCase.php
│   │   ├── Campaign/JoinCampaignUseCase.php
│   │   ├── Character/RollDiceUseCase.php
│   │   └── Story/TriggerPlotTwistUseCase.php
│   └── DTO/
│       ├── AudioSyncCommandDTO.php
│       ├── CharacterSheetDTO.php
│       └── RollResultDTO.php
├── Infrastructure/
│   ├── Persistence/
│   │   ├── MariaDB/MariaDBConnection.php
│   │   ├── MariaDB/MariaDBCampaignRepository.php
│   │   └── MariaDB/MariaDBCharacterRepository.php
│   ├── Realtime/
│   │   ├── SSEDeliveryChannel.php
│   │   └── EventPublisherInterface.php
│   └── Security/
│       ├── Argon2idPasswordHasher.php
│       └── TokenManager.php
└── Presentation/
    ├── Http/
    │   ├── Controllers/
    │   ├── Middleware/ (AuthMiddleware, GMOnlyMiddleware, RateLimitMiddleware)
    │   └── Router.php
    └── Views/
```

---

## 4. Mecanismo de Sincronização em Tempo Real de Áudio e Eventos

A sincronização de áudio em tempo real entre o Mestre e os múltiplos jogadores é um dos diferenciais mais críticos do sistema. O desafio central é garantir que quando o Mestre disparar um som de trovoada ou iniciar uma trilha orquestrada dramática, todos os membros escutem simultaneamente, sem defasagens que arruínem o impacto cênico.

### 4.1. Protocolo de Sincronização Acústica (NTP-Lite)

```mermaid
sequenceDiagram
    autonumber
    actor GM as Mestre (GM)
    participant ClientGM as Navegador do Mestre
    participant Server as O Bardo Core (PHP 8.3)
    participant Broadcaster as SSE Realtime Hub
    participant ClientPlayer as Navegador do Jogador
    actor Player as Jogador

    GM->>ClientGM: Clica em "Iniciar Combate"<br/>(Trilha: dragao_rugido.ogg)
    ClientGM->>Server: POST /api/v1/campaigns/{id}/audio/sync<br/>Payload: { action: "PLAY", track_id: 42, offset_ms: 0 }
    Server->>Server: Valida permissão de Mestre (GMOnly)
    Server->>Server: Captura microtimestamp do servidor ($T_{server}$)
    Server->>Broadcaster: Envia evento AUDIO_STATE_CHANGED
    Server-->>ClientGM: 200 OK { status: "broadcasted", timestamp: $T_{server}$ }
    
    Broadcaster-->>ClientGM: SSE Event: AUDIO_PLAY { track_id: 42, start_epoch: $T_{server}$ }
    Broadcaster-->>ClientPlayer: SSE Event: AUDIO_PLAY { track_id: 42, start_epoch: $T_{server}$ }
    
    Note over ClientPlayer: Calcula $T_{local} - T_{server}$ via calibração prévia.<br/>Inicia Web Audio Context no offset calculado exato.
    ClientGM->>ClientGM: Reproduz áudio simultaneamente
    ClientPlayer->>Player: Áudio soa sincronizado nos fones do jogador!
```

### 4.2. Algoritmo de Compensação de Drift
No cliente JavaScript (Web Audio Engine), o objeto de reprodução calcula periodicamente a discrepância temporal:

$$\Delta t = t_{\text{atual\_cliente}} - (t_{\text{início\_servidor}} + \text{offset})$$

1. Se $|\Delta t| \le 80\text{ ms}$: Não há intervenção audível (perfeita sincronia psicofísica);
2. Se $80\text{ ms} < |\Delta t| \le 400\text{ ms}$: A taxa de reprodução (`audioNode.playbackRate.value`) é ajustada sutilmente em $\pm 2.5\%$ por 1 segundo até zerar a diferença sem distorção perceptível de afinação (*pitch*);
3. Se $|\Delta t| > 400\text{ ms}$: O ponteiro de áudio efetua um salto suave com micro-crossfade de 30ms para a posição exata.

---

## 5. Modelo de Dados Relacional MariaDB 10.11

```mermaid
erDiagram
    USERS ||--o{ CAMPAIGNS : creates
    USERS ||--o{ CAMPAIGN_MEMBERS : joins
    CAMPAIGNS ||--o{ CAMPAIGN_MEMBERS : has
    CAMPAIGNS ||--o{ CHARACTERS : contains
    USERS ||--o{ CHARACTERS : controls
    CAMPAIGNS ||--o{ CHRONICLES : records
    CAMPAIGNS ||--o{ PLOT_TWISTS : triggers
    CAMPAIGNS ||--o{ AUDIO_PLAYLISTS : configures
    AUDIO_PLAYLISTS ||--o{ AUDIO_TRACKS : includes
    CAMPAIGNS ||--o{ DICE_ROLLS : logs

    USERS {
        bigint id PK
        string name
        string email UK
        string password_hash
        string role
        timestamp created_at
    }

    CAMPAIGNS {
        bigint id PK
        bigint gm_user_id FK
        string title
        string code UK
        string rule_system
        text description
        string cover_url
        boolean is_active
        timestamp created_at
    }

    CAMPAIGN_MEMBERS {
        bigint id PK
        bigint campaign_id FK
        bigint user_id FK
        string member_role
        timestamp joined_at
    }

    CHARACTERS {
        bigint id PK
        bigint campaign_id FK
        bigint user_id FK
        string name
        string class_and_level
        int hp_current
        int hp_max
        int armor_class
        json attributes_data
        json inventory_data
        json spells_data
        timestamp updated_at
    }

    CHRONICLES {
        bigint id PK
        bigint campaign_id FK
        bigint author_user_id FK
        string title
        text content_markdown
        date session_date
        timestamp created_at
    }

    PLOT_TWISTS {
        bigint id PK
        bigint campaign_id FK
        string twist_type
        string headline
        text description
        bigint audio_sfx_id FK
        timestamp triggered_at
    }

    AUDIO_TRACKS {
        bigint id PK
        bigint owner_user_id FK
        string title
        string category
        int duration_seconds
        string file_url
        boolean is_preset
    }

    DICE_ROLLS {
        bigint id PK
        bigint campaign_id FK
        bigint user_id FK
        bigint character_id FK
        string formula
        json dice_breakdown
        int total_result
        boolean is_secret
        timestamp rolled_at
    }
```

---

## 6. Padrões de Projeto e Práticas de Engenharia Adotadas

1. **Repository Pattern:** Desacopla as entidades de domínio da camada PDO/MariaDB, facilitando a escrita de testes unitários isolados com mocks;
2. **Strategy Pattern:** Utilizado no motor de regras de fichas (`RuleSystemStrategy`), permitindo que a mesma entidade de ficha processe cálculos de acordo com OSR (bônus baseados em faixas clássicas de 3 a 18), D&D 5e (cálculo de bônus progressivo $(valor - 10)/2$) ou Tormenta20;
3. **Factory Method:** Instanciação dinâmica de eventos de reviravolta (`PlotTwistFactory`) e envelopes de notificação SSE;
4. **Observer / Event-Driven Architecture:** Quando o Mestre altera os Pontos de Vida de um monstro ou dispara um som, um `DomainEvent` é emitido e distribuído aos canais de notificação sem engessar a lógica síncrona.

---

*Arquitetura de Software homologada para a implementação em PHP 8.3 e Nginx do sistema O Bardo.*
