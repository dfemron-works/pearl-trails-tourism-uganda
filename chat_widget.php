<style>
#safariChatToggle {
    position: fixed;
    bottom: 20px;
    left: 20px;
    background: var(--accent-neon, #00f59b);
    color: #0b1310;
    border: none;
    border-radius: 30px;
    padding: 12px 18px;
    font-weight: 800;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(0, 245, 155, 0.45);
    z-index: 2200;
}
#safariChatBox {
    display: none;
    position: fixed;
    bottom: 76px;
    left: 20px;
    width: 370px;
    max-width: 92vw;
    background: #13201b;
    border: 2px solid var(--accent-neon, #00f59b);
    border-radius: 14px;
    z-index: 2250;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.85);
    overflow: hidden;
}
.chat-header {
    background: #192b24;
    padding: 12px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color, #243b32);
}
.chat-messages {
    height: 260px;
    overflow-y: auto;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: 13px;
}
.bot-msg, .user-msg {
    padding: 9px 12px;
    border-radius: 10px;
    max-width: 88%;
    line-height: 1.45;
    white-space: pre-line;
}
.bot-msg {
    background: #0b1310;
    border: 1px solid var(--border-color, #243b32);
    color: var(--text-light, #f0fdf4);
    align-self: flex-start;
}
.user-msg {
    background: var(--accent-neon, #00f59b);
    color: #0b1310;
    font-weight: 600;
    align-self: flex-end;
}
.quick-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    padding: 8px 12px;
    border-top: 1px solid var(--border-color, #243b32);
    background: #0f1a16;
}
.quick-chips button {
    padding: 4px 8px;
    font-size: 11px;
    margin: 0;
}
.chat-input-row {
    display: flex;
    gap: 6px;
    padding: 10px;
    background: #13201b;
    border-top: 1px solid var(--border-color, #243b32);
}
.chat-input-row input {
    margin: 0;
    padding: 8px;
    font-size: 13px;
    flex: 1;
}
@media print { #safariChatToggle, #safariChatBox { display: none !important; } }
</style>

<button type="button" id="safariChatToggle" class="no-print" onclick="window.toggleSafariChat()">💬 My Safari Chat Assistant </button>

<div id="safariChatBox" class="no-print">
    <div class="chat-header">
        <strong style="color:var(--accent-neon, #00f59b); font-size:14px;">🦁 Pearl Trails Intelligent Concierge</strong>
        <button type="button" onclick="window.toggleSafariChat()" style="padding:2px 8px; font-size:11px;">✕</button>
    </div>
    <div class="chat-messages" id="safariChatFeed">
        <div class="bot-msg">
            Webale / Welcome to <strong>Pearl Trails Tours &amp; Travel Uganda Ltd</strong>! Ask me about safari prices, live weather, Uganda &amp; world geography, 4x4 Land Cruiser rates, or your bookings!
        </div>
    </div>
    <div class="quick-chips">
        <button type="button" onclick="window.askConcierge('Show live safari packages and prices')">🦁 Packages</button>
        <button type="button" onclick="window.askConcierge('What is the weather in Bwindi?')">🌦️ Bwindi Weather</button>
        <button type="button" onclick="window.askConcierge('Tell me the geography of Uganda')">🧭 Uganda Geo</button>
        <button type="button" onclick="window.askConcierge('What are the upcoming holiday discounts?')">🔥 Holiday Deals</button>
        <button type="button" onclick="window.askConcierge('Check my booking status')">🎟️ My Bookings</button>
    </div>
    <form class="chat-input-row" onsubmit="event.preventDefault(); window.sendChatInput();">
        <input type="text" id="safariChatInput" placeholder="Say Hi or ask anything..." required>
        <button type="submit" style="padding:8px 12px;">Ask</button>
    </form>
</div>

<script>
window.toggleSafariChat = function() {
    var box = document.getElementById('safariChatBox');
    if (!box) return;
    box.style.display = (box.style.display === 'block') ? 'none' : 'block';
};

window.appendChatBubble = function(text, isUser) {
    var feed = document.getElementById('safariChatFeed');
    if (!feed) return null;
    var div = document.createElement('div');
    div.className = isUser ? 'user-msg' : 'bot-msg';
    div.textContent = text;
    feed.appendChild(div);
    feed.scrollTop = feed.scrollHeight;
    return div;
};

window.askConcierge = function(question) {
    window.appendChatBubble(question, true);
    var typingBubble = window.appendChatBubble('Thinking...', false);

    var formData = new FormData();
    formData.append('visitor_msg', question);

    fetch('chat_api.php', { method: 'POST', body: formData })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (typingBubble) {
                typingBubble.textContent = data.reply || 'How else can I assist with your Uganda safari?';
            }
            var feed = document.getElementById('safariChatFeed');
            if (feed) feed.scrollTop = feed.scrollHeight;
        })
        .catch(function() {
            if (typingBubble) {
                typingBubble.textContent = 'Connection hiccup. You can reach our Nagojje desk directly at +256 772 845 910.';
            }
        });
};

window.sendChatInput = function() {
    var inp = document.getElementById('safariChatInput');
    if (!inp || !inp.value.trim()) return;
    var text = inp.value.trim();
    inp.value = '';
    window.askConcierge(text);
};
</script>
