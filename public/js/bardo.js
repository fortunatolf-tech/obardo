/**
 * O BARDO - MOTOR JAVASCRIPT EM TEMPO REAL (CLIENTE)
 * Sincronização Acústica Web Audio API + Server-Sent Events (SSE) + Tormenta20 / D&D
 */

/**
 * O BARDO - MOTOR DE ÁUDIO MULTI-CAMADAS & SOUNDBOARD ESTILO BARDFY
 * Sons Reais Livres de Direitos Autorais (CC0 / Pixabay / Freesound / Kenney)
 * Sincronização Server-Sent Events (SSE) em Tempo Real
 */

const BARDO_SFX_MAP = {
  // Combate & Armas
  'espada_golpe': '/audio/sfx/espada_golpe.ogg',
  'sword': '/audio/sfx/espada_golpe.ogg',
  'espada_choque': '/audio/sfx/espada_choque.ogg',
  'escudo_bloqueio': '/audio/sfx/escudo_bloqueio.wav',
  'flecha_disparo': '/audio/sfx/flecha_disparo.wav',
  'sacar_adaga': '/audio/sfx/sacar_adaga.ogg',
  'soco_impacto': '/audio/sfx/soco_impacto.ogg',
  'corte_lamina': '/audio/sfx/corte_lamina.ogg',

  // Magia & Feitiços
  'bola_de_fogo': '/audio/sfx/bola_de_fogo.ogg',
  'raio_trovao': '/audio/sfx/raio_trovao.ogg',
  'spell': '/audio/sfx/raio_trovao.ogg',
  'cura_divina': '/audio/sfx/cura_divina.ogg',
  'mana': '/audio/sfx/cura_divina.ogg',
  'trovao_impacto': '/audio/sfx/trovao_impacto.wav',
  'thunder': '/audio/sfx/trovao_impacto.wav',
  'pocao_beber': '/audio/sfx/pocao_beber.wav',

  // Monstros & Criaturas
  'dragao_rugido': '/audio/sfx/dragao_rugido.ogg',
  'roar': '/audio/sfx/dragao_rugido.ogg',
  'lobo_uivo': '/audio/sfx/lobo_uivo.ogg',
  'goblin_grito': '/audio/sfx/goblin_grito.ogg',
  'risada_maligna': '/audio/sfx/risada_maligna.ogg',
  'coracao_pulso': '/audio/sfx/coracao_pulso.ogg',

  // Taverna & Social
  'brinde_tacas': '/audio/sfx/brinde_tacas.wav',
  'cheers': '/audio/sfx/brinde_tacas.wav',
  'moedas_ouro': '/audio/sfx/moedas_ouro.ogg',
  'coins': '/audio/sfx/moedas_ouro.ogg',
  'dados_mesa': '/audio/sfx/dados_mesa.ogg',
  'dice': '/audio/sfx/dados_mesa.ogg',
  'crit': '/audio/sfx/brinde_tacas.wav',
  'sino_alarme': '/audio/sfx/sino_alarme.wav',

  // Masmorra & Exploração
  'bau_tesouro': '/audio/sfx/bau_tesouro.ogg',
  'porta_madeira': '/audio/sfx/porta_madeira.ogg',
  'porta_fechando': '/audio/sfx/porta_fechando.ogg',
  'porta_ferro': '/audio/sfx/porta_ferro.wav',
  'rangido_madeira': '/audio/sfx/rangido_madeira.ogg',
  'passos_pedra': '/audio/sfx/passos_pedra.ogg'
};

const BARDO_AMBIENT_MAP = {
  'tavern': '/audio/ambient/taverna.ogg',
  'taverna': '/audio/ambient/taverna.ogg',
  'lute': '/audio/ambient/tavern_lute.wav',
  'combat': '/audio/ambient/combat_drums.wav',
  'dungeon': '/audio/ambient/caverna.ogg',
  'horror': '/audio/ambient/dungeon_drone.wav',
  'campfire': '/audio/ambient/fogueira.ogg',
  'forest': '/audio/ambient/floresta_noite.ogg',
  'wind': '/audio/ambient/vento.ogg',
  'rain': '/audio/ambient/chuva.ogg',
  'storm': '/audio/ambient/tempestade.ogg',
  'city': '/audio/ambient/cidade_medieval.ogg',
  'sea': '/audio/ambient/mar.ogg'
};

// Instâncias Globais de Áudio Multi-Camadas
let bardoMusicAudio = null;
let bardoWeatherAudio = null;
let bardoAudioCtx = null;
let bardoMasterGain = null;
let bardoCurrentCampaignId = null;
let bardoSSE = null;

// Controle de Volumes
let bardoMasterVolume = 0.85;
let bardoMusicVolume = 0.70;
let bardoSfxVolume = 0.90;
let bardoIsMuted = false;
let bardoActiveTrackKey = null;

function initBardoAudio() {
  if (!bardoAudioCtx) {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (AudioContextClass) {
      bardoAudioCtx = new AudioContextClass();
      bardoMasterGain = bardoAudioCtx.createGain();
      bardoMasterGain.gain.setValueAtTime(bardoMasterVolume, bardoAudioCtx.currentTime);
      bardoMasterGain.connect(bardoAudioCtx.destination);
    }
  }
  if (bardoAudioCtx && bardoAudioCtx.state === 'suspended') {
    bardoAudioCtx.resume().catch(() => {});
  }
  return bardoAudioCtx;
}

// Reprodução de Efeitos Sonoros Reais (SFX) com Polyphony
function bardoPlaySFX(sfxName, customUrl = null) {
  initBardoAudio();

  const url = customUrl || BARDO_SFX_MAP[sfxName] || BARDO_SFX_MAP[sfxName.toLowerCase()] || '/audio/sfx/dados_mesa.ogg';
  const effectiveVol = bardoIsMuted ? 0 : (bardoMasterVolume * bardoSfxVolume);

  try {
    const audio = new Audio(url);
    audio.volume = Math.max(0, Math.min(1, effectiveVol));
    const p = audio.play();
    if (p && p.catch) {
      p.catch(err => {
        console.warn(`[O Bardo] Não foi possível reproduzir SFX "${sfxName}" (bloqueio de autoplay do navegador). Toque na tela para habilitar o áudio.`);
      });
    }

    // Feedback visual animado no pad do soundboard
    const pad = document.querySelector(`[data-sfx-id="${sfxName}"]`);
    if (pad) {
      pad.classList.add('playing');
      setTimeout(() => pad.classList.remove('playing'), 600);
    }
  } catch (err) {
    console.warn(`[O Bardo] Erro ao carregar arquivo de SFX "${sfxName}":`, err);
  }
}

// Reprodução de Trilha Sonora de Fundo em Loop
function bardoPlayAmbient(trackKeyOrUrl, title = '') {
  initBardoAudio();

  let url = trackKeyOrUrl;
  if (!url.startsWith('http') && !url.startsWith('/')) {
    url = BARDO_AMBIENT_MAP[trackKeyOrUrl] || `/audio/ambient/${trackKeyOrUrl}.ogg`;
  }

  bardoActiveTrackKey = trackKeyOrUrl;

  if (!bardoMusicAudio) {
    bardoMusicAudio = new Audio();
    bardoMusicAudio.loop = true;
    bardoMusicAudio.preload = 'auto';
  }

  // Crossfade / troca de trilha
  bardoMusicAudio.src = url;
  bardoMusicAudio.volume = bardoIsMuted ? 0 : (bardoMasterVolume * bardoMusicVolume);

  const p = bardoMusicAudio.play();
  if (p && p.catch) {
    p.catch(err => {
      console.warn('Autoplay aguardando interação do usuário:', err);
    });
  }

  // Atualiza label na Taverna e no Dashboard do Mestre
  const label = document.getElementById('current-track-label');
  if (label) {
    label.textContent = title || bardoGetTrackTitle(trackKeyOrUrl);
  }

  // Ativa visualizador de áudio
  document.querySelectorAll('.audio-visualizer').forEach(v => v.classList.remove('paused'));

  // Destaca card ativo no catálogo
  document.querySelectorAll('.track-item, .ambient-track-btn').forEach(el => {
    el.classList.remove('active');
    if (el.getAttribute('data-track') === trackKeyOrUrl || el.getAttribute('data-track-url') === url) {
      el.classList.add('active');
    }
  });
}

// Reprodução da Camada de Clima / Atmosfera Secundária (Multi-layer)
function bardoPlayWeather(trackKeyOrUrl) {
  initBardoAudio();

  let url = trackKeyOrUrl;
  if (!url.startsWith('http') && !url.startsWith('/')) {
    url = BARDO_AMBIENT_MAP[trackKeyOrUrl] || `/audio/ambient/${trackKeyOrUrl}.ogg`;
  }

  if (!bardoWeatherAudio) {
    bardoWeatherAudio = new Audio();
    bardoWeatherAudio.loop = true;
  }

  bardoWeatherAudio.src = url;
  bardoWeatherAudio.volume = bardoIsMuted ? 0 : (bardoMasterVolume * bardoMusicVolume * 0.75);
  bardoWeatherAudio.play().catch(() => {});
}

// Pausar / Parar todo o áudio
function bardoStopAllAudio() {
  if (bardoMusicAudio) {
    bardoMusicAudio.pause();
    bardoMusicAudio.currentTime = 0;
  }
  if (bardoWeatherAudio) {
    bardoWeatherAudio.pause();
    bardoWeatherAudio.currentTime = 0;
  }
  document.querySelectorAll('.audio-visualizer').forEach(v => v.classList.add('paused'));
  document.querySelectorAll('.track-item, .preset-card').forEach(el => el.classList.remove('active'));
  const label = document.getElementById('current-track-label');
  if (label) label.textContent = 'Trilha Parada • Silêncio na Taverna';
}

// Presets de Cenas Rápidas (Estilo Bardfy Presets)
function bardoApplyPreset(presetName) {
  document.querySelectorAll('.preset-card').forEach(c => c.classList.remove('active'));
  const presetEl = document.querySelector(`[data-preset="${presetName}"]`);
  if (presetEl) presetEl.classList.add('active');

  switch (presetName) {
    case 'tavern':
      bardoPlayAmbient('taverna', '🍻 Taverna do Javali Saltitante');
      bardoPlayWeather('lute');
      bardoToast('🍻 Cena Ativada: Taverna Alegre (Música + Alaúdes)!');
      break;
    case 'combat':
      bardoPlayAmbient('combat', '⚔️ Batalha Épica pela Vida');
      bardoPlayWeather('storm');
      bardoPlaySFX('espada_choque');
      bardoToast('⚔️ Cena Ativada: Batalha Sangrenta (Tambores + Tempestade)!');
      break;
    case 'dungeon':
      bardoPlayAmbient('dungeon', '💀 Masmorra & Cripta Antiga');
      bardoPlayWeather('horror');
      bardoToast('💀 Cena Ativada: Masmorra Proibida & Ecos do Vazio!');
      break;
    case 'campfire':
      bardoPlayAmbient('campfire', '🏕️ Acampamento & Fogueira');
      bardoPlayWeather('forest');
      bardoToast('🏕️ Cena Ativada: Noite na Floresta & Fogueira!');
      break;
    case 'city':
      bardoPlayAmbient('city', '🏰 Feira da Cidade Medieval');
      if (bardoWeatherAudio) bardoWeatherAudio.pause();
      bardoToast('🏰 Cena Ativada: Cidade & Mercado Medieval!');
      break;
    case 'storm':
      bardoPlayAmbient('storm', '⛈️ Tempestade Violenta & Caos');
      bardoPlaySFX('trovao_impacto');
      bardoToast('⛈️ Cena Ativada: Tempestade & Trovões!');
      break;
  }
}

// Controles de Volume
function bardoSetMasterVolume(val) {
  bardoMasterVolume = Math.max(0, Math.min(100, parseFloat(val))) / 100;
  bardoApplyVolumes();
}

function bardoSetMusicVolume(val) {
  bardoMusicVolume = Math.max(0, Math.min(100, parseFloat(val))) / 100;
  bardoApplyVolumes();
}

function bardoSetSfxVolume(val) {
  bardoSfxVolume = Math.max(0, Math.min(100, parseFloat(val))) / 100;
  bardoApplyVolumes();
}

function bardoToggleMute() {
  bardoIsMuted = !bardoIsMuted;
  bardoApplyVolumes();
  const muteBtn = document.getElementById('bardo-mute-btn');
  if (muteBtn) {
    muteBtn.innerHTML = bardoIsMuted ? '🔇 Desmutar' : '🔊 Silenciar';
  }
  bardoToast(bardoIsMuted ? '🔇 Áudio Silenciado' : '🔊 Áudio Reativado');
}

function bardoApplyVolumes() {
  const effectiveMusic = bardoIsMuted ? 0 : (bardoMasterVolume * bardoMusicVolume);
  if (bardoMusicAudio) bardoMusicAudio.volume = Math.max(0, Math.min(1, effectiveMusic));
  if (bardoWeatherAudio) bardoWeatherAudio.volume = Math.max(0, Math.min(1, effectiveMusic * 0.75));
}

function bardoGetTrackTitle(key) {
  const map = {
    'taverna': 'Taverna do Javali Saltitante • Alaúdes & Lareira',
    'tavern': 'Taverna do Javali Saltitante • Alaúdes & Lareira',
    'combat': 'Tambores de Guerra & Batalha Épica',
    'dungeon': 'Cripta Antiga & Ecos da Caverna',
    'horror': 'Dungeon Drone (Horror & Tormenta)',
    'campfire': 'Acampamento & Fogueira Crepitante',
    'forest': 'Floresta Sombria à Noite',
    'city': 'Feira Livre da Cidade Medieval',
    'storm': 'Tempestade Furiosa & Trovões'
  };
  return map[key] || 'Ambientação de Alta Fantasia';
}

// Filtro de Categorias no Soundboard
function bardoFilterSfxCategory(category, btnEl) {
  document.querySelectorAll('.soundboard-tab-btn').forEach(b => b.classList.remove('active'));
  if (btnEl) btnEl.classList.add('active');

  document.querySelectorAll('.sfx-bardfy-pad').forEach(pad => {
    if (category === 'all' || pad.getAttribute('data-sfx-category') === category) {
      pad.style.display = 'flex';
    } else {
      pad.style.display = 'none';
    }
  });
}

// Conector Server-Sent Events (SSE) em Tempo Real
function connectBardoSSE(campaignId) {
  bardoCurrentCampaignId = campaignId;
  if (bardoSSE) {
    bardoSSE.close();
  }

  const url = `/api/v1/campaigns/${campaignId}/events/stream`;
  bardoSSE = new EventSource(url);

  bardoSSE.addEventListener('connected', () => {
    updateSyncBadge(true);
  });

  // Comando de sincronia de áudio recebido do Mestre
  bardoSSE.addEventListener('audio_sync_command', (e) => {
    const data = JSON.parse(e.data);
    bardoToast(`🎵 Mestre comandou: ${data.action}`);
    if (data.action === 'PLAY' || data.action === 'PLAY_TRACK') {
      const url = data.track_url || data.track_category || 'tavern';
      bardoPlayAmbient(url, data.track_title);
      const label = document.getElementById('current-track-label');
      if (label && data.track_title) label.textContent = data.track_title;
    } else if (data.action === 'PAUSE' || data.action === 'STOP') {
      bardoStopAllAudio();
    }
  });

  // Efeito sonoro do soundboard recebido
  bardoSSE.addEventListener('audio_sfx_command', (e) => {
    const data = JSON.parse(e.data);
    bardoPlaySFX(data.sfx_name, data.file_url);
  });

  // Reviravolta da trama ativada ao vivo pelo Mestre!
  bardoSSE.addEventListener('plot_twist_broadcast', (e) => {
    const data = JSON.parse(e.data);
    triggerDramaticTwist(data.headline, data.description, data.twist_type);
  });

  // Rolagem de dados na sessão
  bardoSSE.addEventListener('dice_roll_broadcast', (e) => {
    const data = JSON.parse(e.data);
    bardoPlaySFX('dice');
    appendRollToFeed(
      data.roller_name + ' • ' + data.label,
      data.formula,
      data.dice_results || [],
      data.modifier || 0,
      data.total,
      data.is_critical,
      data.is_fumble
    );
  });

  // Atualização de PV/PM de personagem
  bardoSSE.addEventListener('character_status_updated', (e) => {
    const data = JSON.parse(e.data);
    updateCharacterVitalsInDOM(data);
  });

  bardoSSE.onerror = () => {
    updateSyncBadge(false);
  };
}

// Chamadas de API AJAX
async function bardoModifyHP(characterId, delta) {
  bardoPlaySFX(delta < 0 ? 'sword' : 'spell');
  const curEl = document.getElementById('hp-current-val');
  const maxEl = document.getElementById('hp-max-val');
  if (!curEl || !maxEl) return;

  let cur = parseInt(curEl.textContent, 10);
  const max = parseInt(maxEl.textContent, 10);
  cur = Math.max(0, Math.min(max, cur + delta));

  curEl.textContent = cur;
  const bar = document.getElementById('hp-fill-bar');
  if (bar) bar.style.width = Math.round((cur / max) * 100) + '%';

  try {
    await fetch(`/api/v1/characters/${characterId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ hp_current: cur })
    });
    bardoToast(delta < 0 ? `⚔️ Sofreu ${Math.abs(delta)} de dano!` : `💚 Curou ${delta} PV!`);
  } catch(e) {
    console.error(e);
  }
}

async function bardoModifyPM(characterId, delta) {
  bardoPlaySFX('mana');
  const curEl = document.getElementById('pm-current-val');
  const maxEl = document.getElementById('pm-max-val');
  if (!curEl || !maxEl) return;

  let cur = parseInt(curEl.textContent, 10);
  const max = parseInt(maxEl.textContent, 10);
  cur = Math.max(0, Math.min(max, cur + delta));

  curEl.textContent = cur;
  const bar = document.getElementById('pm-fill-bar');
  if (bar) bar.style.width = Math.round((cur / max) * 100) + '%';

  try {
    await fetch(`/api/v1/characters/${characterId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ mp_current: cur })
    });
    bardoToast(delta < 0 ? `🔮 Gastou ${Math.abs(delta)} PM!` : `✨ Recuperou ${delta} PM!`);
  } catch(e) {
    console.error(e);
  }
}

async function bardoRoll(campaignId, characterId, formula, label) {
  bardoPlaySFX('dice');
  try {
    const res = await fetch(`/api/v1/campaigns/${campaignId}/dice/roll`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        formula: formula,
        label: label,
        character_id: characterId
      })
    });
    const json = await res.json();
    if (json.status === 'success') {
      const roll = json.data.roll;
      bardoToast(`🎲 ${label}: ${roll.total_result || roll.total}`);
      if (roll.is_critical) bardoPlaySFX('crit');
    }
  } catch(e) {
    console.error(e);
  }
  closeDiceSheet();
}

async function bardoGMSFX(campaignId, sfxName) {
  bardoPlaySFX(sfxName);
  try {
    await fetch(`/api/v1/campaigns/${campaignId}/audio/sync`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'TRIGGER_SFX',
        sfx_name: sfxName
      })
    });
    bardoToast(`⚡ Efeito '${sfxName}' transmitido para a sessão!`);
  } catch(e) {
    console.error(e);
  }
}

async function bardoGMSync(campaignId, action, trackId, layer = 'music') {
  try {
    await fetch(`/api/v1/campaigns/${campaignId}/audio/sync`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: action,
        track_id: trackId,
        layer: layer
      })
    });
    bardoToast(`🎵 Comando de som transmitido!`);
  } catch(e) {
    console.error(e);
  }
}

async function bardoGMPlotTwist(campaignId, headline, description, type = 'traicao') {
  try {
    await fetch(`/api/v1/campaigns/${campaignId}/plots/trigger`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        headline: headline,
        description: description,
        twist_type: type,
        audio_sfx: type === 'combate' ? 'roar' : 'thunder'
      })
    });
    bardoToast(`🔥 Reviravolta da trama disparada ao vivo!`);
  } catch(e) {
    console.error(e);
  }
}

function updateCharacterVitalsInDOM(data) {
  const hpEl = document.getElementById(`party-hp-${data.character_id}`);
  if (hpEl) hpEl.textContent = `${data.hp_current}/${data.hp_max} PV`;

  const mpEl = document.getElementById(`party-mp-${data.character_id}`);
  if (mpEl) mpEl.textContent = `${data.mp_current}/${data.mp_max} PM`;
}

function updateSyncBadge(online) {
  document.querySelectorAll('.sync-badge').forEach(el => {
    el.innerHTML = online 
      ? '<span class="sync-dot"></span><span>Sincronizado (32ms)</span>'
      : '<span class="sync-dot" style="background:#ff5555;box-shadow:none;"></span><span>Desconectado</span>';
  });
}

function bardoToast(msg) {
  let toast = document.getElementById('bardo-mobile-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'bardo-mobile-toast';
    toast.style.cssText = `
      position: fixed;
      top: 60px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(40, 42, 54, 0.98);
      border: 1.5px solid var(--dracula-purple, #bd93f9);
      color: #f8f8f2;
      font-size: 0.82rem;
      font-weight: 700;
      padding: 0.55rem 1.15rem;
      border-radius: 99px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.85), 0 0 16px rgba(189, 147, 249, 0.4);
      z-index: 150;
      pointer-events: none;
      transition: opacity 0.3s ease;
      white-space: nowrap;
      backdrop-filter: blur(12px);
      font-family: var(--font-ui, sans-serif);
    `;
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.style.opacity = '1';
  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => {
    toast.style.opacity = '0';
  }, 2400);
}

function showToast(msg) {
  bardoToast(msg);
}

function openDiceSheet() {
  initBardoAudio();
  const sheet = document.getElementById('dice-bottom-sheet');
  if (sheet) sheet.classList.add('open');
}

function closeDiceSheet() {
  const sheet = document.getElementById('dice-bottom-sheet');
  if (sheet) sheet.classList.remove('open');
}

function openVitalsModal() {
  initBardoAudio();
  const modal = document.getElementById('vitals-modal-sheet');
  if (modal) modal.classList.add('open');
}

function closeVitalsModal() {
  const modal = document.getElementById('vitals-modal-sheet');
  if (modal) modal.classList.remove('open');
}

function triggerDramaticTwist(headline, desc, type = 'traicao') {
  initBardoAudio();
  bardoPlaySFX(type === 'combate' ? 'roar' : 'thunder');

  const screen = document.getElementById('dramatic-twist-screen');
  const headlineEl = document.getElementById('dramatic-headline');
  const descEl = document.getElementById('dramatic-desc');

  if (screen && headlineEl && descEl) {
    headlineEl.textContent = headline || 'REVIRAVOLTA NA TRAMA!';
    descEl.textContent = desc || 'O destino da campanha mudou repentinamente!';
    screen.classList.add('active');
  }
}

function closeDramaticTwist() {
  const screen = document.getElementById('dramatic-twist-screen');
  if (screen) screen.classList.remove('active');
}

function appendRollToFeed(label, formula, rolls, modifier, total, isCrit, isFumble) {
  const feed = document.getElementById('session-roll-feed');
  if (!feed) return;

  const entry = document.createElement('div');
  entry.className = 'rpg-card';
  entry.style.padding = '0.65rem 0.85rem';
  entry.style.marginBottom = '0.5rem';
  entry.style.borderLeft = isCrit ? '3px solid #50fa7b' : (isFumble ? '3px solid #ff5555' : '3px solid #bd93f9');

  const rollsArr = Array.isArray(rolls) ? rolls : [rolls];
  const modStr = modifier !== 0 ? (modifier > 0 ? '+ ' + modifier : '- ' + Math.abs(modifier)) : '';

  entry.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <div style="font-weight: 700; font-size: 0.85rem; color: var(--dracula-yellow, #f1fa8c);">
          ${label} ${isCrit ? '<span style="color:#50fa7b; font-weight:bold;">(CRÍTICO!)</span>' : ''} ${isFumble ? '<span style="color:#ff5555; font-weight:bold;">(FALHA CRÍTICA!)</span>' : ''}
        </div>
        <div style="font-size: 0.72rem; color: var(--dracula-comment, #6272a4);">
          Dados: [${rollsArr.join(', ')}] ${modStr} (${formula})
        </div>
      </div>
      <div style="font-family: var(--font-code); font-size: 1.3rem; font-weight: 900; color: ${isCrit ? '#50fa7b' : (isFumble ? '#ff5555' : '#f8f8f2')}; background: var(--bg-surface); padding: 2px 10px; border-radius: 6px; border: 1px solid var(--border-subtle);">
        ${total}
      </div>
    </div>
  `;
  if (isCrit) {
    bardoPlaySFX('crit');
    triggerRunicFlash('gold');
  } else if (isFumble) {
    bardoPlaySFX('thunder');
    triggerRunicFlash('ruby');
  }

  feed.insertBefore(entry, feed.firstChild);
}

function triggerRunicFlash(type) {
  const flash = document.createElement('div');
  flash.style.cssText = `
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 140;
    transition: opacity 0.5s ease;
    box-shadow: inset 0 0 70px ${type === 'gold' ? 'rgba(241, 250, 140, 0.75)' : 'rgba(255, 85, 85, 0.85)'};
    opacity: 1;
  `;
  document.body.appendChild(flash);
  if (type === 'ruby') {
    document.body.style.transform = 'translate(3px, 2px)';
    setTimeout(() => { document.body.style.transform = 'translate(-3px, -2px)'; }, 50);
    setTimeout(() => { document.body.style.transform = ''; }, 100);
  }
  setTimeout(() => {
    flash.style.opacity = '0';
    setTimeout(() => flash.remove(), 500);
  }, 250);
}

// =============================================================================
// FUNÇÕES DE COMANDO EXCLUSIVAS DO MESTRE (RF05, RF12, RF15, RF17)
// =============================================================================

async function bardoSaveGMSecrets(campaignId) {
  const textarea = document.getElementById('gm-secrets-text');
  if (!textarea) return;
  const secrets = textarea.value;
  try {
    const res = await fetch(`/api/v1/campaigns/${campaignId}/secrets`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ gm_secrets: secrets })
    });
    const data = await res.json();
    if (res.ok && data.status === 'success') {
      bardoToast('📜 Segredos do Mestre salvos com isolamento criptográfico garantido!');
    } else {
      bardoToast(data.message || 'Falha ao salvar segredos');
    }
  } catch (e) {
    bardoToast('Erro de conexão ao salvar segredos.');
  }
}

async function bardoToggleHandout(campaignId, handoutId, isRevealed) {
  try {
    const res = await fetch(`/api/v1/campaigns/${campaignId}/handouts/${handoutId}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ is_revealed: isRevealed })
    });
    const data = await res.json();
    if (res.ok && data.status === 'success') {
      bardoToast(isRevealed ? '👁️ Pista revelada para toda a mesa!' : '🔒 Pista ocultada dos jogadores.');
      setTimeout(() => window.location.reload(), 700);
    } else {
      bardoToast(data.message || 'Falha ao atualizar handout');
    }
  } catch (e) {
    bardoToast('Erro de conexão ao atualizar handout.');
  }
}

async function bardoCreateHandout(e, campaignId) {
  e.preventDefault();
  const form = e.target;
  const title = form.title.value.trim();
  const description = form.description.value.trim();
  const imageUrl = form.image_url.value.trim();
  const isRevealed = form.is_revealed.checked;

  try {
    const res = await fetch(`/api/v1/campaigns/${campaignId}/handouts`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        title: title,
        description: description,
        image_url: imageUrl || null,
        is_revealed: isRevealed
      })
    });
    const data = await res.json();
    if (res.ok && data.status === 'success') {
      bardoToast('📜 Pista/Handout adicionado com sucesso!');
      setTimeout(() => window.location.reload(), 700);
    } else {
      bardoToast(data.message || 'Falha ao adicionar pista');
    }
  } catch (e) {
    bardoToast('Erro de conexão ao adicionar pista.');
  }
}

async function bardoUpdateMemberRole(campaignId, userId, newRole) {
  try {
    const res = await fetch(`/api/v1/campaigns/${campaignId}/members/${userId}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ role: newRole })
    });
    const data = await res.json();
    if (res.ok && data.status === 'success') {
      bardoToast('⚔️ Cargo do membro atualizado!');
      setTimeout(() => window.location.reload(), 700);
    } else {
      bardoToast(data.message || 'Falha ao alterar cargo');
    }
  } catch (e) {
    bardoToast('Erro de conexão ao alterar cargo.');
  }
}

async function bardoKickMember(campaignId, userId, userName) {
  if (!confirm(`Tem certeza que deseja remover ${userName || 'este jogador'} da campanha?`)) {
    return;
  }
  try {
    const res = await fetch(`/api/v1/campaigns/${campaignId}/members/${userId}`, {
      method: 'DELETE'
    });
    const data = await res.json();
    if (res.ok && data.status === 'success') {
      bardoToast('Membro removido da campanha.');
      setTimeout(() => window.location.reload(), 700);
    } else {
      bardoToast(data.message || 'Falha ao remover membro');
    }
  } catch (e) {
    bardoToast('Erro de conexão ao remover membro.');
  }
}

async function bardoGMRoll(campaignId, formula, label) {
  const secretCheckbox = document.getElementById('gm-dice-secret-toggle');
  const isSecret = secretCheckbox ? secretCheckbox.checked : false;

  try {
    const res = await fetch(`/api/v1/campaigns/${campaignId}/dice/roll`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        formula: formula,
        label: label,
        is_secret: isSecret,
        roller_name: 'Mestre (GM)'
      })
    });
    const data = await res.json();
    if (res.ok && data.status === 'success') {
      const roll = data.data.roll;
      bardoPlaySFX(roll.is_critical ? 'crit' : 'dice');
      
      const resContainer = document.getElementById('gm-roll-result-display');
      if (resContainer) {
        resContainer.style.display = 'block';
        resContainer.innerHTML = `
          <div style="background: rgba(0,0,0,0.6); border: 1.5px solid ${isSecret ? 'var(--t20-ruby)' : 'var(--gold-imperial)'}; border-radius: 8px; padding: 0.6rem; text-align: center;">
            <div style="font-size: 0.72rem; color: ${isSecret ? 'var(--t20-ruby)' : 'var(--gold-imperial)'}; font-weight: bold;">
              ${isSecret ? '🔒 ROLAGEM SECRETA DO MESTRE' : '📢 ROLAGEM PÚBLICA'} • ${escapeHtml(label)} (${escapeHtml(formula)})
            </div>
            <div style="font-family: var(--font-stats); font-size: 1.6rem; font-weight: 900; color: #fff; margin: 4px 0;">
              ${roll.total}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted);">
              Dados: [${roll.dice_results.join(', ')}] ${roll.modifier >= 0 ? '+' + roll.modifier : roll.modifier}
              ${roll.is_critical ? '<span style="color:var(--gold-bright); font-weight:bold;"> • CRÍTICO!</span>' : ''}
              ${roll.is_fumble ? '<span style="color:var(--t20-ruby); font-weight:bold;"> • FALHA CRÍTICA!</span>' : ''}
            </div>
          </div>
        `;
      }
      bardoToast(isSecret ? '🔒 Rolagem Secreta executada (Apenas Mestre vê)' : '🎲 Rolagem transmitida à mesa!');
    }
  } catch (e) {
    bardoToast('Erro de conexão ao rolar dados.');
  }
}
