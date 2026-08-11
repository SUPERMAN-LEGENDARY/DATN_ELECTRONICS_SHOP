{{-- resources/views/components/chatbot-widget.blade.php --}}
{{-- Include trong layout chính bằng: <x-chatbot-widget /> hoặc @include('components.chatbot-widget') --}}

<div id="ai-chatbot-root">

    {{-- Floating Toggle Button --}}
    <button id="ai-chat-toggle" aria-label="Mở chat tư vấn">
        <span id="ai-chat-pulse"></span>
        <i class="fas fa-comment-dots" id="ai-chat-icon-open"></i>
        <i class="fas fa-xmark" id="ai-chat-icon-close" style="display:none"></i>
    </button>

    {{-- Main Chat Window Frame --}}
    <div id="ai-chat-window" class="ai-hidden">

        {{-- Header Bar --}}
        <div id="ai-chat-header">
            <div class="ai-hdr-left">
                <div class="ai-hdr-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="ai-hdr-info">
                    <div class="ai-hdr-title">ElectronicShop</div>
                    <div class="ai-hdr-status">
                        <span class="ai-status-dot"></span> Trực tuyến
                    </div>
                </div>
            </div>
            <div class="ai-hdr-actions">
                <button type="button" id="ai-chat-refresh" title="Làm mới cuộc trò chuyện">
                    <i class="fas fa-rotate-right"></i>
                </button>
                <button type="button" id="ai-chat-close" title="Đóng">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </div>

        {{-- Scrollable Chat Content Area --}}
        <div id="ai-chat-body">

            {{-- Central Welcome Screen (DMX Avatar Style) --}}
            <div id="ai-welcome-box">
                <div class="ai-welcome-avatar-ring">
                    <div class="ai-welcome-avatar-inner">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
                <div class="ai-welcome-heading">
                    Chào anh/chị, em là <span class="ai-brand-accent">Trợ lý AI ES</span>
                </div>
                <div class="ai-welcome-sub">
                    Em trả lời thắc mắc và giúp anh/chị lựa chọn<br>sản phẩm phù hợp
                </div>
            </div>

            {{-- Timestamp Divider --}}
            <div class="ai-timestamp-divider">
                Hôm nay, <span id="ai-current-time">13:09</span>
            </div>

            {{-- Chat Messages List --}}
            <div id="ai-chat-messages"></div>

            {{-- Quick Chips Suggestions (Chuyên mục Bán Điện Thoại) --}}
            <div id="ai-quick-chips">
                <button type="button" class="ai-chip" data-msg="Tư vấn bán điện thoại giá rẻ dưới 5 triệu">📱 Điện thoại giá rẻ</button>
                <button type="button" class="ai-chip" data-msg="Tư vấn mua iPhone chính hãng mới nhất">🍎 iPhone chính hãng</button>
                <button type="button" class="ai-chip" data-msg="Tư vấn các dòng điện thoại Samsung Galaxy">🤖 Samsung Galaxy</button>
                <button type="button" class="ai-chip" data-msg="Điện thoại nào bán chạy nhất hiện nay?">🔥 Điện thoại HOT</button>
                <button type="button" class="ai-chip" data-msg="Chương trình khuyến mãi giảm giá điện thoại">🎁 Khuyến mãi điện thoại</button>
            </div>

        </div>

        {{-- Footer Section (Pill Input & Disclaimer) --}}
        <div id="ai-chat-footer">
            <form id="ai-chat-form">
                <button type="button" id="ai-mic-btn" title="Nhập giọng nói">
                    <i class="fas fa-microphone"></i>
                </button>
                <input type="text" id="ai-chat-input" placeholder="Nhập tin nhắn..." autocomplete="off" />
                <button type="submit" id="ai-send-btn" aria-label="Gửi tin nhắn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

            <div class="ai-disclaimer">
                Giá, tồn kho và khuyến mãi có thể thay đổi, cần xác nhận lại trước khi mua<br>
                Thông tin chỉ mang tính tham khảo, được tư vấn bởi Trí Tuệ Nhân Tạo
            </div>
        </div>

    </div>
</div>

<style>
/* ============================================================
   ROOT & FLOATING TOGGLE BUTTON — đồng bộ design token với trang home
   ============================================================ */
#ai-chatbot-root {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    --ai-black: var(--sm-black, #2b2b2b);
    --ai-ink: var(--sm-ink, #121212);
    --ai-gray: var(--sm-gray, #545454);
    --ai-line: var(--sm-line, #dcdcdc);
    --ai-surface: var(--sm-surface, #f7f7f7);
    --ai-blue: var(--sm-blue, #2b2b2b);
    --ai-radius: var(--sm-radius, 24px);
    --ai-ease: var(--sm-ease, cubic-bezier(.25,.46,.45,.94));
}

#ai-chat-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: none;
    background: var(--ai-black);
    color: #ffffff;
    font-size: 22px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.28);
    transition: transform .3s var(--ai-ease), box-shadow .3s var(--ai-ease);
    position: relative;
}
#ai-chat-toggle:hover {
    transform: translateY(-3px) scale(1.06);
    box-shadow: 0 14px 36px rgba(0, 0, 0, 0.38);
}

#ai-chat-pulse {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    border: 2px solid rgba(33, 137, 255, 0.45);
    animation: chatPulse 2.4s ease-out infinite;
    pointer-events: none;
}
@keyframes chatPulse {
    0%   { transform: scale(1); opacity: 0.8; }
    70%  { transform: scale(1.5); opacity: 0; }
    100% { transform: scale(1.5); opacity: 0; }
}

/* ============================================================
   MAIN CHAT WINDOW FRAME
   ============================================================ */
#ai-chat-window {
    position: fixed;
    bottom: 96px;
    right: 24px;
    width: 380px;
    max-width: calc(100vw - 32px);
    height: 560px;
    max-height: calc(100vh - 120px);
    background: #ffffff;
    border-radius: var(--ai-radius);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18), 0 2px 12px rgba(0, 0, 0, 0.06);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 9998;
    border: 1px solid var(--ai-line);
}

#ai-chat-window.ai-hidden {
    display: none !important;
}

#ai-chat-window.ai-entering {
    animation: windowPop 0.3s var(--ai-ease) forwards;
}
@keyframes windowPop {
    from { transform: scale(0.94) translateY(16px); opacity: 0; }
    to   { transform: scale(1) translateY(0); opacity: 1; }
}

/* ============================================================
   HEADER BAR (đen tuyệt đối, đồng bộ header/CTA của trang home)
   ============================================================ */
#ai-chat-header {
    background: var(--ai-black);
    padding: 14px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    color: #ffffff;
}

.ai-hdr-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ai-hdr-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.14);
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    color: #ffffff;
}

.ai-hdr-title {
    font-family: 'Manrope', 'Inter', sans-serif;
    font-size: 15px;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
    line-height: 1.2;
}

.ai-hdr-status {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 2px;
}

.ai-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--ai-blue);
    display: inline-block;
}

.ai-hdr-actions {
    display: flex;
    gap: 6px;
}

.ai-hdr-actions button {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.12);
    border: none;
    color: #ffffff;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .2s var(--ai-ease), transform .2s var(--ai-ease);
}
.ai-hdr-actions button:hover {
    background: rgba(255, 255, 255, 0.24);
    transform: scale(1.05);
}

/* ============================================================
   CHAT BODY & SCROLLABLE CONTENT
   ============================================================ */
#ai-chat-body {
    flex: 1;
    overflow-y: auto;
    background: #ffffff;
    padding: 16px;
    display: flex;
    flex-direction: column;
    scroll-behavior: smooth;
}

#ai-chat-body::-webkit-scrollbar {
    width: 5px;
}
#ai-chat-body::-webkit-scrollbar-track {
    background: transparent;
}
#ai-chat-body::-webkit-scrollbar-thumb {
    background: var(--ai-line);
    border-radius: 4px;
}

/* Central Welcome Box */
#ai-welcome-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 12px 8px 8px;
    flex-shrink: 0;
}

.ai-welcome-avatar-ring {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: var(--ai-black);
    padding: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    box-shadow: 0 10px 26px rgba(0, 0, 0, 0.18);
}

.ai-welcome-avatar-inner {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: var(--ai-black);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 40px;
    border: 2px solid #ffffff;
}

.ai-welcome-heading {
    font-family: 'Manrope', 'Inter', sans-serif;
    font-size: 15.5px;
    color: var(--ai-ink);
    font-weight: 500;
    margin-bottom: 6px;
}

.ai-brand-accent {
    color: var(--ai-blue);
    font-weight: 800;
}

.ai-welcome-sub {
    font-size: 13.5px;
    color: var(--ai-gray);
    line-height: 1.5;
    max-width: 310px;
}

/* Timestamp Divider */
.ai-timestamp-divider {
    text-align: center;
    font-size: 12px;
    color: var(--ai-gray);
    margin: 14px 0 10px;
    flex-shrink: 0;
}

/* ============================================================
   MESSAGES & BUBBLES
   ============================================================ */
#ai-chat-messages {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.ai-msg-row {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    animation: msgFadeIn 0.3s var(--ai-ease);
}
@keyframes msgFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.ai-msg-row.user {
    justify-content: flex-end;
}

.ai-bot-msg-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--ai-black);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    margin-top: 2px;
}

.ai-msg-bubble {
    max-width: 82%;
    padding: 10px 14px;
    font-size: 14px;
    line-height: 1.5;
    border-radius: 16px;
    word-break: break-word;
    white-space: pre-wrap;
}

.ai-msg-row.bot .ai-msg-bubble {
    background: var(--ai-surface);
    color: var(--ai-ink);
    border-top-left-radius: 4px;
}

.ai-msg-row.user .ai-msg-bubble {
    background: var(--ai-black);
    color: #ffffff;
    border-top-right-radius: 4px;
}

/* Recommended Products Container */
.ai-products-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 8px;
    width: 100%;
}

.ai-product-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid var(--ai-line);
    border-radius: 14px;
    padding: 8px 10px;
    text-decoration: none;
    color: inherit;
    transition: border-color .2s var(--ai-ease), box-shadow .2s var(--ai-ease), transform .2s var(--ai-ease);
}
.ai-product-item:hover {
    border-color: var(--ai-black);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.ai-product-item img {
    width: 48px;
    height: 48px;
    object-fit: contain;
    border-radius: 8px;
    background: var(--ai-surface);
    border: 1px solid var(--ai-line);
    flex-shrink: 0;
}

.ai-product-info {
    min-width: 0;
    flex: 1;
}

.ai-product-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--ai-ink);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

.ai-product-price {
    font-family: 'Manrope', 'Inter', sans-serif;
    font-size: 13px;
    font-weight: 800;
    color: var(--ai-black);
    margin-top: 2px;
}

.ai-product-list-price {
    font-family: 'Manrope', 'Inter', sans-serif;
    font-size: 11px;
    font-weight: 500;
    color: var(--ai-muted, #9a9a9a);
    text-decoration: line-through;
    margin-left: 6px;
}

/* Typing Dots */
.ai-typing-dots {
    background: var(--ai-surface);
    padding: 12px 16px;
    border-radius: 16px;
    border-top-left-radius: 4px;
    display: flex;
    gap: 4px;
}
.ai-typing-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--ai-gray);
    animation: typingBounce 1.2s infinite ease-in-out;
}
.ai-typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.ai-typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typingBounce {
    0%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-6px); background: var(--ai-black); }
}

/* Quick Suggestion Chips (Smooth Scroll) */
#ai-quick-chips {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 12px 16px 6px;
    margin: 4px -16px 0;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
    flex-shrink: 0;
}
#ai-quick-chips::-webkit-scrollbar {
    display: none;
}

.ai-chip {
    flex-shrink: 0;
    white-space: nowrap;
    padding: 7px 15px;
    border-radius: 20px;
    background: var(--ai-surface);
    border: 1px solid var(--ai-line);
    color: var(--ai-ink);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s var(--ai-ease), border-color .2s var(--ai-ease), transform .2s var(--ai-ease), box-shadow .2s var(--ai-ease);
}
.ai-chip:hover {
    background: var(--ai-black);
    color: #ffffff;
    border-color: var(--ai-black);
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
}

/* ============================================================
   INPUT FOOTER (Pill Form & Disclaimer Text)
   ============================================================ */
#ai-chat-footer {
    background: #ffffff;
    padding: 12px 16px 14px;
    border-top: 1px solid var(--ai-line);
    flex-shrink: 0;
}

#ai-chat-form {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1.5px solid var(--ai-black);
    border-radius: 26px;
    padding: 4px 6px 4px 14px;
    background: #ffffff;
    transition: box-shadow .2s var(--ai-ease);
}
#ai-chat-form:focus-within {
    box-shadow: 0 0 0 3px rgba(33, 137, 255, 0.18);
    border-color: var(--ai-blue);
}

#ai-mic-btn {
    border: none;
    background: none;
    color: var(--ai-gray);
    font-size: 16px;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color .2s var(--ai-ease);
    flex-shrink: 0;
}
#ai-mic-btn:hover {
    color: var(--ai-black);
}

#ai-chat-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 14px;
    color: var(--ai-ink);
    background: transparent;
    padding: 6px 0;
    min-width: 0;
}
#ai-chat-input::placeholder {
    color: #94a3b8;
}

#ai-send-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: var(--ai-black);
    color: #ffffff;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .2s var(--ai-ease), transform .2s var(--ai-ease);
}
#ai-send-btn:hover {
    background: var(--ai-blue);
    transform: scale(1.06);
}

#ai-chat-input:disabled {
    opacity: .6;
}
#ai-send-btn:disabled,
#ai-mic-btn:disabled {
    opacity: .5;
    cursor: not-allowed;
}
#ai-send-btn:disabled:hover {
    background: var(--ai-black);
    transform: none;
}

.ai-disclaimer {
    text-align: center;
    font-size: 10.5px;
    color: #94a3b8;
    margin-top: 10px;
    line-height: 1.45;
}
</style>

<script>
(function () {
    const toggleBtn  = document.getElementById('ai-chat-toggle');
    const closeBtn   = document.getElementById('ai-chat-close');
    const refreshBtn = document.getElementById('ai-chat-refresh');
    const win        = document.getElementById('ai-chat-window');
    const form       = document.getElementById('ai-chat-form');
    const input      = document.getElementById('ai-chat-input');
    const messages   = document.getElementById('ai-chat-messages');
    const iconOpen   = document.getElementById('ai-chat-icon-open');
    const iconClose  = document.getElementById('ai-chat-icon-close');
    const chips      = document.getElementById('ai-quick-chips');
    const chatBody   = document.getElementById('ai-chat-body');
    const timeElem   = document.getElementById('ai-current-time');
    const sendBtn    = document.getElementById('ai-send-btn');
    const micBtn     = document.getElementById('ai-mic-btn');

    // Session token phía client: chatbot.send cần 1 sessionToken ổn định để
    // GeminiChatService::handle() load đúng lịch sử hội thoại (AiSession::session_token).
    // Lưu trong localStorage để giữ nguyên phiên chat qua các lần load lại trang.
    const SESSION_TOKEN_KEY = 'ai_chat_session_token';
    function getSessionToken() {
        let token = localStorage.getItem(SESSION_TOKEN_KEY);
        if (!token) {
            token = (window.crypto && crypto.randomUUID)
                ? crypto.randomUUID()
                : 'sess-' + Date.now() + '-' + Math.random().toString(16).slice(2);
            localStorage.setItem(SESSION_TOKEN_KEY, token);
        }
        return token;
    }

    // Chống bấm gửi/enter nhiều lần liên tiếp trong lúc đang chờ phản hồi từ server.
    let isSending = false;
    function setSendingState(sending) {
        isSending = sending;
        input.disabled = sending;
        if (sendBtn) sendBtn.disabled = sending;
        if (micBtn) micBtn.disabled = sending;
    }

    function updateTime() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        if (timeElem) timeElem.textContent = `${hh}:${mm}`;
    }
    updateTime();

    function openChat() {
        win.classList.remove('ai-hidden');
        win.classList.add('ai-entering');
        win.addEventListener('animationend', () => win.classList.remove('ai-entering'), { once: true });
        iconOpen.style.display  = 'none';
        iconClose.style.display = 'block';

        if (messages.children.length === 0) {
            appendBot('Dạ em có thể giúp gì ạ.');
        }
        setTimeout(() => input.focus(), 250);
    }

    function closeChat() {
        win.classList.add('ai-hidden');
        iconOpen.style.display  = 'block';
        iconClose.style.display = 'none';
    }

    function resetChat() {
        messages.innerHTML = '';
        localStorage.removeItem(SESSION_TOKEN_KEY);
        if (chips) chips.style.display = 'flex';
        appendBot('Dạ em có thể giúp gì ạ.');
    }

    toggleBtn.addEventListener('click', () => win.classList.contains('ai-hidden') ? openChat() : closeChat());
    closeBtn.addEventListener('click', closeChat);
    refreshBtn?.addEventListener('click', resetChat);

    /* Quick Chips */
    chips?.querySelectorAll('.ai-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            input.value = chip.dataset.msg;
            if (chips) chips.style.display = 'none';
            form.dispatchEvent(new Event('submit', { cancelable: true }));
        });
    });

    function scrollDown() {
        if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
    }

    function appendUser(text) {
        const row = document.createElement('div');
        row.className = 'ai-msg-row user';
        row.innerHTML = `<div class="ai-msg-bubble">${escapeHtml(text)}</div>`;
        messages.appendChild(row);
        scrollDown();
    }

    function appendBot(text, products = []) {
        const row = document.createElement('div');
        row.className = 'ai-msg-row bot';

        const icon = document.createElement('div');
        icon.className = 'ai-bot-msg-icon';
        icon.innerHTML = '<i class="fas fa-robot"></i>';

        const bubble = document.createElement('div');
        bubble.className = 'ai-msg-bubble';

        row.appendChild(icon);
        row.appendChild(bubble);
        messages.appendChild(row);

        // Typewriter animation. Tổng thời gian hiển thị bị CHẶN TRẦN (không phụ thuộc độ dài
        // text) — trước đây tính theo ms/ký tự không giới hạn tổng, nên câu trả lời dài (gần
        // 2048 token) có thể mất tới 20-30s mới hiện hết chữ. Giờ luôn hoàn thành trong khoảng
        // 300ms (text ngắn) đến 1.8s (text dài), bằng cách hiện nhiều ký tự mỗi tick thay vì 1.
        const TICK_MS = 20;
        const totalDurationMs = Math.min(1800, Math.max(300, text.length * 12));
        const totalTicks = Math.max(1, Math.round(totalDurationMs / TICK_MS));
        const charsPerTick = Math.max(1, Math.ceil(text.length / totalTicks));

        let i = 0;
        function typeChar() {
            if (i < text.length) {
                i = Math.min(text.length, i + charsPerTick);
                bubble.textContent = text.slice(0, i);
                scrollDown();
                setTimeout(typeChar, TICK_MS);
            } else if (products && products.length) {
                renderProducts(bubble, products);
            }
        }
        typeChar();
    }

    function renderProducts(parentBubble, products) {
        const container = document.createElement('div');
        container.className = 'ai-products-container';

        products.forEach(p => {
            const item = document.createElement('a');
            item.href = `/products/${p.slug}`;
            item.className = 'ai-product-item';

            // p.price là giá bán thật sự; p.list_price chỉ hiển thị gạch ngang khi đang
            // giảm giá (p.is_on_sale). Khi sản phẩm có biến thể (p.has_price_range), hiển thị
            // "Từ ..." theo p.min_price thay vì giá gốc, vì các phiên bản có thể rẻ/đắt hơn.
            const displayPrice = p.has_price_range ? p.min_price : p.price;
            const pricePrefix = p.has_price_range ? 'Từ ' : '';

            const thumb = p.thumbnail
                ? '/storage/' + p.thumbnail.replace(/^\/?storage\//, '')
                : '/images/no-image.png';

            item.innerHTML = `
                <img src="${thumb}" alt="${escapeHtml(p.name)}">
                <div class="ai-product-info">
                    <span class="ai-product-name">${escapeHtml(p.name)}</span>
                    <span class="ai-product-price">${pricePrefix}${Math.round(displayPrice).toLocaleString('vi-VN')}đ</span>
                    ${(!p.has_price_range && p.is_on_sale) ? `<span class="ai-product-list-price">${Math.round(p.list_price).toLocaleString('vi-VN')}đ</span>` : ''}
                </div>
            `;
            container.appendChild(item);
        });

        parentBubble.appendChild(container);
        scrollDown();
    }

    function showTyping() {
        const row = document.createElement('div');
        row.className = 'ai-msg-row bot ai-typing-indicator';
        row.id = 'ai-typing-row';
        row.innerHTML = `
            <div class="ai-bot-msg-icon"><i class="fas fa-robot"></i></div>
            <div class="ai-typing-dots">
                <span></span><span></span><span></span>
            </div>
        `;
        messages.appendChild(row);
        scrollDown();
    }

    function hideTyping() {
        document.getElementById('ai-typing-row')?.remove();
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /* Submit Form */
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (isSending) return; // đang chờ phản hồi lượt trước, chặn gửi chồng request
        const text = input.value.trim();
        if (!text) return;

        if (chips) chips.style.display = 'none';

        appendUser(text);
        input.value = '';
        setSendingState(true);
        showTyping();

        try {
            const res = await fetch('{{ route('chatbot.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text, session_token: getSessionToken() }),
            });
            const data = await res.json();
            hideTyping();

            if (!res.ok) {
                appendBot(data.error || 'Có lỗi xảy ra, vui lòng thử lại.');
                return;
            }
            appendBot(data.reply, data.products || []);
        } catch (err) {
            hideTyping();
            appendBot('Không thể kết nối tới máy chủ, vui lòng thử lại sau.');
        } finally {
            setSendingState(false);
            input.focus();
        }
    });
})();
</script>