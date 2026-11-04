# O Bardo • Licenciamento e Fontes de Áudio Royalty-Free (Estilo Bardfy)

Este documento atesta a conformidade de todos os recursos de áudio e sonoplastia integrados ao aplicativo web **O Bardo**, inspirados na experiência imersiva do [Bardfy](https://www.bardfy.com.br/) e estritamente em conformidade com as leis de direitos autorais e licenças abertas **Creative Commons Zero (CC0) / Public Domain / Royalty-Free**.

---

## 1. Trilhas de Ambientação Contínua (Loops Acústicos)
Localização: `/public/audio/ambient/` (servido via `/audio/ambient/`)

| Arquivo | Título / Tema | Categoria | Fonte / Autor | Licença |
| :--- | :--- | :--- | :--- | :--- |
| `taverna.ogg` | Taverna do Javali Saltitante | Taverna & Social | Freesound / ivolipa | CC0 Public Domain |
| `tavern_lute.wav` | Alaúdes de Arton | Taverna & Música | DnD-Soundscape / Fantasy Tabletop | CC0 Public Domain |
| `cidade_medieval.ogg`| Feira Livre da Cidade Medieval | Cidade & Mercado | Freesound / bolkmar | CC0 Public Domain |
| `combat_drums.wav` | Tambores de Guerra & Batalha Épica | Combate & Clímax | DnD-Soundscape / RPG Audio | CC0 Public Domain |
| `tempestade.ogg` | Tempestade Furiosa & Trovões | Combate & Clima | Freesound / Audeption | CC0 Public Domain |
| `caverna.ogg` | Cripta Antiga & Ecos da Caverna | Masmorra & Mistério | Freesound / Kinoton | CC0 Public Domain |
| `dungeon_drone.wav` | Dungeon Drone (Horror & Tormenta) | Masmorra & Horror | DnD-Soundscape / Tabletop Drone | CC0 Public Domain |
| `fogueira.ogg` | Acampamento & Fogueira Crepitante | Natureza & Viagem | Freesound / NickTayloe | CC0 Public Domain |
| `floresta_noite.ogg` | Floresta Sombria à Noite | Natureza & Viagem | Freesound / Borgory | CC0 Public Domain |
| `vento.ogg` | Vento Uivante nas Montanhas | Natureza & Viagem | Freesound / IanStarGem | CC0 Public Domain |
| `chuva.ogg` | Chuva na Estrada & Jornada | Natureza & Viagem | Freesound / samesamesame | CC0 Public Domain |
| `mar.ogg` | Ondas do Mar & Porto dos Aventureiros | Natureza & Viagem | Freesound / kkenny101 | CC0 Public Domain |

---

## 2. Soundboard de Efeitos Sonoros Instantâneos (SFX)
Localização: `/public/audio/sfx/` (servido via `/audio/sfx/`)

### ⚔️ Combate & Armas
- `espada_golpe.ogg`: Golpe de espada cortante (Freesound / JohnBuhr - CC0)
- `espada_choque.ogg`: Choque metálico / Deflexão de lâminas (Freesound / Christopherderp - CC0)
- `sacar_adaga.ogg`: Lâmina rápida sendo desembainhada (Kenney RPG Audio - CC0)
- `escudo_bloqueio.wav`: Bloqueio tático com escudo (Kenney RPG Audio - CC0)
- `flecha_disparo.wav`: Disparo e impacto de flecha (DnD-Soundscape / Mario Sanchez - CC0)
- `corte_lamina.ogg`: Corte crítico afiado (Kenney RPG Audio - CC0)
- `soco_impacto.ogg`: Golpe corpo a corpo / pancada (Freesound / joe_anderson22 - CC0)

### 🔮 Magia & Feitiços
- `bola_de_fogo.ogg`: Explosão ígnea mágica (Freesound / qubodup - CC0)
- `raio_trovao.ogg`: Descarga arcana de eletricidade (Freesound / loganzsound - CC0)
- `cura_divina.ogg`: Prece sagrada e cura restauradora (Freesound / IENBA - CC0)
- `trovao_impacto.wav`: Trovão retumbante (Freesound Nature - CC0)
- `pocao_beber.wav`: Poção alquímica sendo bebida (Kenney RPG Audio - CC0)

### 🐉 Monstros & Criaturas
- `dragao_rugido.ogg`: Rugido apocalíptico de dragão vermelho (Freesound / qubodup - CC0)
- `lobo_uivo.ogg`: Uivo distante de alcateia (Freesound / betchkal - CC0)
- `goblin_grito.ogg`: Emboscada estridente de goblins (Freesound / spookymodem - CC0)
- `risada_maligna.ogg`: Gargalhada sinistra do vilão (Freesound / WannyManny - CC0)
- `coracao_pulso.ogg`: Tensão dramática e batimentos cardíacos (Freesound / Cloud-10 - CC0)

### 🍻 Taverna & Social
- `brinde_tacas.wav`: Canecas de cerveja se chocando em brinde (DnD-Soundscape / Herbert Nicolas - CC0)
- `moedas_ouro.ogg`: Bolsa de moedas de ouro tilintando (Kenney RPG Audio - CC0)
- `dados_mesa.ogg`: Rolagem realista de dados poliédricos na madeira (Kenney RPG Audio / Public Domain)
- `sino_alarme.wav`: Badalo de alarme do templo/fortaleza (Freesound Modal Acoustics - CC0)

### 🗝️ Masmorra & Exploração
- `bau_tesouro.ogg`: Baú antigo sendo aberto (Freesound / steprock - CC0)
- `porta_madeira.ogg`: Porta pesada de madeira rangendo e abrindo (Kenney RPG Audio - CC0)
- `porta_fechando.ogg`: Porta se fechando (Kenney RPG Audio - CC0)
- `porta_ferro.wav`: Portão de ferro e grade da masmorra (DnD-Soundscape - CC0)
- `rangido_madeira.ogg`: Rangido assustador de tábua (Kenney RPG Audio - CC0)
- `passos_pedra.ogg`: Passos ecoando no chão de pedra (Kenney RPG Audio - CC0)

---

## 3. Arquitetura de Sincronização em Tempo Real (Mestre -> Jogadores)
1. **Mixagem Multi-Camada**:
   - Camada 1 (Música de Fundo contínua em loop)
   - Camada 2 (Atmosfera/Clima secundário contínuo)
   - Camada 3 (Soundboard Polifônico Instantâneo em 0ms)
2. **Propagação Server-Sent Events (SSE)**:
   - Ao ativar qualquer cena, trilha ou som no painel do Mestre, o evento `audio_sync_command` ou `audio_sfx_command` é transmitido via HTTP/2 SSE para todos os celulares e navegadores dos jogadores na Taverna e na Ficha.
   - Volumes independentes com controle de ganho Web Audio API e suporte a mute geral.
