<?php
// Handle Display Mode Toggle (Web vs Mobile) & Language Selection in Session
if (isset($_GET['set_display']) && in_array($_GET['set_display'], ['web', 'mobile'], true)) {
    $_SESSION['display_mode'] = $_GET['set_display'];
}
if (isset($_GET['set_lang'])) {
    $_SESSION['app_lang'] = preg_replace('/[^a-zA-Z\-]/', '', $_GET['set_lang']);
}

$displayMode = $_SESSION['display_mode'] ?? 'web';
$appLang = $_SESSION['app_lang'] ?? 'en';

// 29-Language Registry (Ugandan, Regional East/Central African & International Languages)
$ALL_LANGUAGES = [
    'en'      => 'English',
    'sw'      => 'Kiswahili',
    'afr'     => 'Africana (Pan-African)',
    'fr'      => 'French (Français)',
    'de'      => 'German (Deutsch)',
    'zh-CN'   => 'Chinese (中文)',
    'lg'      => 'Luganda',
    'nyn'     => 'Runyankore',
    'cgg'     => 'Rukiga',
    'ttj'     => 'Rutooro',
    'nyo'     => 'Runyoro',
    'rw'      => 'Kinyarwanda',
    'fmb'     => 'Kifumbira (Rufumbira)',
    'nrg'     => 'Kinyaruguru (Runyaruguru)',
    'koo'     => 'Rukonzo',
    'kpz'     => 'Sabin (Kupsabiny)',
    'luo'     => 'Luo',
    'xog'     => 'Lusoga',
    'laj'     => 'Langi',
    'ach'     => 'Acholi',
    'teo'     => 'Ateso',
    'kdi'     => 'Japadhola',
    'myx'     => 'Lugisu (Lumasaba)',
    'mhi'     => 'Madi',
    'kdj'     => 'Karamojong',
    'gwr'     => 'Lugwere',
    'nyd'     => 'Lunyole',
    'ln'      => 'Congolese (Lingala)',
    'ar-SD'   => 'Sudanese (Arabic / Juba)',
];
?>

<style>
body.mobile-view-mode {
    max-width: 420px;
    margin: 15px auto;
    border: 8px solid #243b32;
    border-radius: 28px;
    min-height: 92vh;
    box-shadow: 0 0 40px rgba(0, 245, 155, 0.25);
    overflow-x: hidden;
}
body.mobile-view-mode header { padding: 10px 14px; }
body.mobile-view-mode header nav a { padding: 10px 8px; font-size: 12px; }
body.mobile-view-mode .container { margin: 12px; padding: 14px; }
body.mobile-view-mode .grid { grid-template-columns: 1fr; }

#settingsModal {
    display: none;
    position: fixed;
    top: 72px;
    right: 20px;
    width: 340px;
    max-width: 92vw;
    background: #13201b;
    border: 2px solid var(--accent-neon);
    border-radius: 12px;
    padding: 18px;
    z-index: 2000;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.75);
}

#signLanguageBar {
    display: none;
    position: fixed;
    bottom: 14px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(11, 19, 16, 0.96);
    border: 2px solid #ffb703;
    border-radius: 12px;
    padding: 12px 18px;
    z-index: 2100;
    max-width: 92vw;
    text-align: center;
    box-shadow: 0 0 25px rgba(255, 183, 3, 0.35);
}
.sign-gesture-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    margin-top: 8px;
}
.sign-tile {
    background: #192b24;
    border: 1px solid var(--accent-neon);
    border-radius: 6px;
    padding: 6px 8px;
    min-width: 42px;
}
.sign-tile span { font-size: 22px; display: block; }
.sign-tile small { font-size: 11px; color: var(--accent-neon); font-weight: bold; }
</style>

<div id="settingsModal" role="dialog" aria-label="System Settings and Accessibility">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <strong style="color:var(--accent-neon); font-size:16px;">⚙️ System & Accessibility Settings</strong>
        <button type="button" onclick="window.toggleSettingsModal()" style="padding:3px 9px; background:var(--danger-neon); border-color:var(--danger-neon); color:#fff;">✕</button>
    </div>

    <label style="font-size:13px; color:var(--text-muted);">🌐 System Language (29 Languages)</label>
    <select id="systemLangSelect" onchange="window.applySystemLanguage(this.value)" style="margin-bottom:12px;">
        <?php foreach ($ALL_LANGUAGES as $code => $label): ?>
            <option value="<?= htmlspecialchars($code) ?>" <?= $appLang === $code ? 'selected' : '' ?>>
                <?= htmlspecialchars($label) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label style="font-size:13px; color:var(--text-muted);">📱 Display Layout Mode</label>
    <div style="display:flex; gap:8px; margin:6px 0 14px;">
        <button type="button" onclick="window.setDisplayFormat('web')" style="flex:1; padding:7px; font-size:12px;">🖥️ Web Display</button>
        <button type="button" onclick="window.setDisplayFormat('mobile')" class="btn-edit" style="flex:1; padding:7px; font-size:12px;">📱 Mobile Display</button>
    </div>

    <label style="font-size:13px; color:var(--text-muted);">♿ Accessibility Support</label>
    <div style="display:flex; flex-direction:column; gap:8px; margin-top:6px;">
        <button type="button" id="voiceAssistBtn" onclick="window.toggleVoiceAssistant()" style="width:100%; text-align:left;">
            🔊 Voice Assistant (Blind Support): OFF
        </button>
        <button type="button" id="signLangBtn" onclick="window.toggleSignLanguage()" class="btn-edit" style="width:100%; text-align:left;">
            🤟 Sign Language Interpreter (Deaf): OFF
        </button>
    </div>
</div>

<div id="signLanguageBar">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:15px;">
        <strong style="color:#ffb703; font-size:13px;">🤟 Sign Language Visual Interpreter (Hover any text/button)</strong>
        <button type="button" onclick="window.toggleSignLanguage()" style="padding:2px 8px; font-size:11px;">Hide</button>
    </div>
    <div id="signWordLabel" style="color:var(--accent-neon); font-weight:bold; margin-top:4px; font-size:14px;">Ready — Point at any package or menu item</div>
    <div id="signGesturesContainer" class="sign-gesture-row"></div>
</div>

<script src="translations_dict.js"></script>
<script>
window.toggleSettingsModal = function() {
    var modal = document.getElementById('settingsModal');
    if (!modal) return;
    modal.style.display = (modal.style.display === 'block') ? 'none' : 'block';
};

window.setDisplayFormat = function(mode) {
    localStorage.setItem('pt_display_mode', mode);
    if (mode === 'mobile') {
        document.body.classList.add('mobile-view-mode');
        window.speakText('Switched to mobile display format.');
    } else {
        document.body.classList.remove('mobile-view-mode');
        window.speakText('Switched to wide web display format.');
    }
};

var voiceEnabled = false;

window.speakText = function(text) {
    if (!voiceEnabled || !('speechSynthesis' in window) || !text) return;
    window.speechSynthesis.cancel();
    var utterance = new SpeechSynthesisUtterance(text.trim());
    utterance.rate = 1.0;
    window.speechSynthesis.speak(utterance);
};

window.enableVoiceAssist = function(state) {
    voiceEnabled = state;
    localStorage.setItem('pt_voice_assist', state ? 'on' : 'off');
    var btn = document.getElementById('voiceAssistBtn');
    if (btn) {
        btn.textContent = state ? '🔊 Voice Assistant (Blind Support): ON' : '🔊 Voice Assistant (Blind Support): OFF';
    }
    if (state) {
        window.speakText('Voice assistant enabled.');
    } else if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }
};

window.toggleVoiceAssistant = function() {
    window.enableVoiceAssist(!voiceEnabled);
};

var signEnabled = false;
var aslHandMap = {
    'A':'✊','B':'✋','C':'🤏','D':'☝️','E':'✊','F':'👌','G':'👉','H':'✌️','I':'🤙','J':'🤙',
    'K':'✌️','L':'👆','M':'✊','N':'✊','O':'👌','P':'👇','Q':'👇','R':'🤞','S':'✊','T':'✊',
    'U':'✌️','V':'✌️','W':'🤟','X':'☝️','Y':'🤙','Z':'☝️',' ':'👐'
};

window.enableSignLanguage = function(state) {
    signEnabled = state;
    localStorage.setItem('pt_sign_lang', state ? 'on' : 'off');
    var bar = document.getElementById('signLanguageBar');
    var btn = document.getElementById('signLangBtn');
    if (bar) bar.style.display = state ? 'block' : 'none';
    if (btn) btn.textContent = state ? '🤟 Sign Language Interpreter (Deaf): ON' : '🤟 Sign Language Interpreter (Deaf): OFF';
};

window.toggleSignLanguage = function() {
    window.enableSignLanguage(!signEnabled);
};

window.renderSignGestures = function(phrase) {
    if (!signEnabled || !phrase) return;
    var clean = phrase.replace(/[^a-zA-Z ]/g, '').trim().slice(0, 18);
    if (!clean) return;
    var label = document.getElementById('signWordLabel');
    var container = document.getElementById('signGesturesContainer');
    if (!label || !container) return;

    label.textContent = 'Signing: "' + clean.toUpperCase() + '"';
    container.innerHTML = '';

    var upper = clean.toUpperCase();
    for (var i = 0; i < upper.length; i++) {
        var ch = upper.charAt(i);
        var tile = document.createElement('div');
        tile.className = 'sign-tile';

        var iconSpan = document.createElement('span');
        iconSpan.textContent = aslHandMap[ch] || '🖐️';

        var charSmall = document.createElement('small');
        charSmall.textContent = (ch === ' ') ? 'SPACE' : ch;

        tile.appendChild(iconSpan);
        tile.appendChild(charSmall);
        container.appendChild(tile);
    }
};

window.applySystemLanguage = function(langCode) {
    localStorage.setItem('pt_selected_lang', langCode);

    if (langCode === 'en') {
        var urlReset = new URL(window.location.href);
        urlReset.searchParams.set('set_lang', 'en');
        window.location.href = urlReset.toString();
        return;
    }

    if (typeof window.translateWholeSystem === 'function') {
        window.translateWholeSystem(langCode);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('pt_display_mode') === 'mobile' || '<?= $displayMode ?>' === 'mobile') {
        document.body.classList.add('mobile-view-mode');
    }
    if (localStorage.getItem('pt_voice_assist') === 'on') {
        window.enableVoiceAssist(true);
    }
    if (localStorage.getItem('pt_sign_lang') === 'on') {
        window.enableSignLanguage(true);
    }

    var savedLang = localStorage.getItem('pt_selected_lang') || '<?= $appLang ?>';
    var langDropdown = document.getElementById('systemLangSelect');
    if (langDropdown && savedLang) {
        langDropdown.value = savedLang;
    }
    if (savedLang && savedLang !== 'en' && typeof window.translateWholeSystem === 'function') {
        window.translateWholeSystem(savedLang);
    }
});

document.addEventListener('mouseover', function(e) {
    if (voiceEnabled) {
        var vTarget = e.target.closest('a, button, h2, h3, .price, td, th, label');
        if (vTarget && vTarget.innerText) {
            window.speakText(vTarget.innerText);
        }
    }
    if (signEnabled) {
        var sTarget = e.target.closest('a, button, h2, h3, th');
        if (sTarget && sTarget.innerText) {
            window.renderSignGestures(sTarget.innerText);
        }
    }
});
</script>
