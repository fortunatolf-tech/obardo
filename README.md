# O BARDO ⚔️📜🎵
### Plataforma Web Imersiva para RPG de Mesa Tradicional (Raiz) em PHP 8.3

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-777bb4?logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MariaDB-10.11%2B-003545?logo=mariadb&logoColor=white)](https://mariadb.org/)
[![License](https://img.shields.io/badge/Licen%C3%A7a-Propriet%C3%A1ria-d4af37)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Fase%20de%20Planejamento%20%26%20Documenta%C3%A7%C3%A3o-10b981)]()
[![LGPD](https://img.shields.io/badge/Conformidade-LGPD%20(Lei%2013.709%2F18)-blue)](docs/11-ciberseguranca-e-conformidade-lgpd.md)

---

## 📖 Sobre o Projeto

**O Bardo** é uma plataforma web inovadora desenvolvida em **PHP 8.3**, voltada exclusivamente para a comunidade de jogadores e mestres de **RPG de mesa tradicional ("raiz")**.

Diferente de simuladores táteis e VTTs pesados que transformaram o RPG em um videogame dependente de placas de vídeo e mapas 3D complexos, **O Bardo** resgata a magia do **teatro da mente**, da conversa e da camaradagem em torno da mesa. Ele fornece as ferramentas essenciais para a realização de campanhas épicas e imersivas:

1. **Módulo Robusto de Fichas de Personagem:** Suporte a sistemas clássicos (OSR / D&D B/X, D&D 5e/3.5, Tormenta20 e Custom), cálculo automático de atributos, controle de inventário com slots de carga (encumbrance), grimório de magias e rolador de dados auditável com semente criptográfica CSPRNG;
2. **História Colaborativa e Crônicas em Tempo Real:** Registro contínuo das sessões no diário da guilda, linha do tempo dos acontecimentos, mural de handouts (cartas, enigmas, mapas) e **Disparador de Reviravoltas da Trama (Live Plot Twists)** exclusivo para o Mestre com efeitos visuais e sonoros simultâneos;
3. **Mesa de Som e Ambientação Acústica Sincronizada:** Soundboard com efeitos sonoros instantâneos (SFX) e reprodutor de trilhas de fundo multicamada (músicas orquestradas + ambiências climáticas contínuas) reproduzidas **simultaneamente nos dispositivos de todos os membros da sessão** via Web Audio API e eventos Server-Sent Events (SSE).

---

## 📑 Documentação Oficial do Projeto

Em estrita conformidade com as melhores práticas de Engenharia de Software, Governança de Projetos (PMI PMBOK), Padrões Internacionais (IEEE / ISO) e a Legislação Brasileira (LGPD), toda a documentação oficial foi elaborada e encontra-se disponível no diretório [`docs/`](docs/):

| Documento | Identificador | Descrição / Padrão |
| :--- | :--- | :--- |
| **[Termo de Abertura do Projeto](docs/01-termo-de-abertura-do-projeto.md)** | `CHARTER-OBAR-V1` | Justificativa, objetivos mensuráveis (OKRs), escopo, premissas, restrições e stakeholders. |
| **[Documento de Visão e Escopo](docs/02-documento-de-visao-e-escopo.md)** | `VISION-OBAR-V1` | Posicionamento de produto, análise de personas, delimitação in-scope/out-of-scope e métricas de sucesso. |
| **[Business Case](docs/03-business-case.md)** | `BIZ-OBAR-V1` | Análise de mercado TTRPG, benchmarking competitivo, modelo de negócio freemium ético, DRE e ROI em 3 anos. |
| **[SRS - Especificação de Requisitos](docs/04-srs-especificacao-de-requisitos-de-software.md)** | `SRS-OBAR-V1` | Requisitos funcionais (RF01-RF26), não-funcionais (RNF01-RNF10), regras de negócio (IEEE 830 / ISO 29148). |
| **[SAD - Descrição de Arquitetura](docs/05-sad-descricao-de-arquitetura-de-software.md)** | `SAD-OBAR-V1` | Clean Architecture em PHP 8.3, C4 Model, mecanismo de sincronia de áudio NTP-Lite e modelo relacional MariaDB. |
| **[Plano de Integração e CI/CD](docs/06-plano-de-integracao.md)** | `INT-OBAR-V1` | Pipelines do GitHub Actions (PHPStan Level 8, PHPUnit 11), serviços externos (S3/R2, Discord) e deploy zero-downtime. |
| **[Documentação Completa das APIs](docs/07-documentacao-completa-das-apis.md)** | `API-OBAR-V1` | Documentação técnica dos endpoints RESTful e canal SSE de tempo real com esquemas JSON e exemplos. |
| **[Especificação OpenAPI 3.1](docs/openapi.yaml)** | `OAS-OBAR-V1` | Arquivo padronizado YAML legível por máquina para Swagger, Postman e geração de SDKs. |
| **[Plano de Gestão de Riscos](docs/08-plano-de-gestao-de-riscos.md)** | `RSK-OBAR-V1` | Matriz de riscos 5x5 (ISO 31000 / PMBOK), mitigação de autoplay móvel, jitter de áudio e plano DRP (RTO/RPO). |
| **[Guia de Estilo UI/UX](docs/09-guia-de-estilo-ui-ux.md)** | `UIUX-OBAR-V1` | Design System *Pergaminho & Ferro*, paleta cromática WCAG AAA, escala tipográfica e anatomia de componentes. |
| **[Especificação dos Protótipos](docs/10-prototipos-de-alta-fidelidade.md)** | `PROTO-OBAR-V1` | Wireframes, fluxos de navegação e detalhamento dos componentes interativos. |
| **[Cibersegurança e Conformidade LGPD](docs/11-ciberseguranca-e-conformidade-lgpd.md)** | `SEC-LGPD-V1` | ROPA (Art. 37), direitos dos titulares (Art. 18), hashing Argon2id, OWASP ASVS e plano de resposta à ANPD. |
| **[Registro de Issues e Roadmap](docs/12-registro-de-issues-e-roadmap.md)** | `ISS-OBAR-V1` | Mapeamento das épicas do projeto, sprints, critérios de aceitação e backlog rastreável. |

---

## 🎨 Protótipos de Alta Fidelidade Interativos

Os protótipos navegáveis foram construídos em **HTML5, CSS3 e JavaScript ES6 puro com Web Audio API** (sem dependências externas), funcionando diretamente em qualquer navegador moderno no diretório [`docs/prototypes/`](docs/prototypes/):

- **[Portal dos Protótipos](docs/prototypes/index.html):** Visão geral e guia de demonstração rápida do sintetizador de áudio;
- **[Painel de Controle do Mestre (GM Dashboard)](docs/prototypes/gm-dashboard.html):** Mesa de som em tempo real com soundboard, seleção de trilhas com transição suave, disparador de reviravoltas da trama e monitor tático de PVs dos heróis;
- **[Ficha do Jogador (Player Sheet)](docs/prototypes/player-sheet.html):** Ficha responsiva mobile-first com monitor dinâmico de PVs, rolador de atributos, inventário com carga e grimório;
- **[A Taverna - Sessão ao Vivo (Live Session)](docs/prototypes/live-session-taverna.html):** Visão compartilhada do grupo com player de som sincronizado, mural de pistas reveladas e diário da guilda.

---

## 🏛️ Estrutura do Repositório

```
obardo/
├── docs/                               # Documentação Oficial Completa
│   ├── 01-termo-de-abertura-do-projeto.md
│   ├── 02-documento-de-visao-e-escopo.md
│   ├── 03-business-case.md
│   ├── 04-srs-especificacao-de-requisitos-de-software.md
│   ├── 05-sad-descricao-de-arquitetura-de-software.md
│   ├── 06-plano-de-integracao.md
│   ├── 07-documentacao-completa-das-apis.md
│   ├── 08-plano-de-gestao-de-riscos.md
│   ├── 09-guia-de-estilo-ui-ux.md
│   ├── 10-prototipos-de-alta-fidelidade.md
│   ├── 11-ciberseguranca-e-conformidade-lgpd.md
│   ├── 12-registro-de-issues-e-roadmap.md
│   ├── openapi.yaml
│   └── prototypes/                     # Protótipos Interativos Standalone
│       ├── index.html
│       ├── gm-dashboard.html
│       ├── player-sheet.html
│       ├── live-session-taverna.html
│       ├── styles.css
│       └── prototype.js
├── legacy/                             # Arquivos de prototipagem legada arquivados
│   └── android-prototype/
├── .github/                            # Workflows de CI/CD e templates de issue
├── .gitignore
└── README.md
```

---

## 🛡️ Cibersegurança & Conformidade com a LGPD

O projeto O Bardo foi concebido sob a premissa de *Privacy by Design*:
- **Hashing de Senhas:** Derivação segura com **Argon2id** (64MB memória, 4 iterações, 2 threads);
- **Eliminação de SQL Injection:** Utilização de consultas parametrizadas via PDO com emulação estritamente desabilitada;
- **Mitigação de Uploads Maliciosos:** Validação binária de *Magic Bytes* com `finfo_file()` e gravação em storage isolado sem permissão de execução (`noexec`);
- **Direitos dos Titulares (LGPD Art. 18):** Funcionalidade nativa de exportação de dados em JSON/PDF e direito ao esquecimento com exclusão física irreversível de conta e dados vinculados.

---

## 👥 Autoria & Licença

- **Patrocinador Executivo & Product Owner:** Lucas Fortunato ([@fortunatolf-tech](https://github.com/fortunatolf-tech))
- **Repositório Oficial:** [https://github.com/fortunatolf-tech/obardo](https://github.com/fortunatolf-tech/obardo)
- **Domínio Oficial de Produção:** [obardo.com.br](https://obardo.com.br)
