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
   ROOT & FLOATING TOGGLE BUTTON
   ============================================================ */
#ai-chatbot-root {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

#ai-chat-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #0284c7, #0ea5e9);
    color: #ffffff;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 24px rgba(14, 165, 233, 0.45);
    transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s;
    position: relative;
}
#ai-chat-toggle:hover {
    transform: translateY(-3px) scale(1.06);
    box-shadow: 0 10px 30px rgba(14, 165, 233, 0.55);
}

#ai-chat-pulse {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    border: 2px solid rgba(14, 165, 233, 0.45);
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
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.16), 0 2px 12px rgba(14, 165, 233, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 9998;
    border: 1px solid #e2e8f0;
}

#ai-chat-window.ai-hidden {
    display: none !important;
}

#ai-chat-window.ai-entering {
    animation: windowPop 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes windowPop {
    from { transform: scale(0.9) translateY(16px); opacity: 0; }
    to   { transform: scale(1) translateY(0); opacity: 1; }
}

/* ============================================================
   HEADER BAR (DMX Blue Tone)
   ============================================================ */
#ai-chat-header {
    background: #0284c7;
    padding: 12px 16px;
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
    background: rgba(255, 255, 255, 0.2);
    border: 1.5px solid rgba(255, 255, 255, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    color: #ffffff;
}

.ai-hdr-title {
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}

.ai-hdr-status {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 2px;
}

.ai-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #4ade80;
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
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #ffffff;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.18s, transform 0.15s;
}
.ai-hdr-actions button:hover {
    background: rgba(255, 255, 255, 0.35);
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
    background: #cbd5e1;
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
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7, #0ea5e9);
    padding: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    box-shadow: 0 8px 24px rgba(14, 165, 233, 0.22);
}

.ai-welcome-avatar-inner {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 42px;
    border: 2px solid #ffffff;
}

.ai-welcome-heading {
    font-size: 15.5px;
    color: #1e293b;
    font-weight: 500;
    margin-bottom: 6px;
}

.ai-brand-accent {
    color: #0284c7;
    font-weight: 700;
}

.ai-welcome-sub {
    font-size: 13.5px;
    color: #334155;
    line-height: 1.5;
    max-width: 310px;
}

/* Timestamp Divider */
.ai-timestamp-divider {
    text-align: center;
    font-size: 12px;
    color: #64748b;
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
    animation: msgFadeIn 0.25s ease-out;
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
    background: #0284c7;
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
    background: #f1f5f9;
    color: #0f172a;
    border-top-left-radius: 4px;
}

.ai-msg-row.user .ai-msg-bubble {
    background: #0284c7;
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
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 10px;
    text-decoration: none;
    color: inherit;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.ai-product-item:hover {
    border-color: #0284c7;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.12);
}

.ai-product-item img {
    width: 48px;
    height: 48px;
    object-fit: contain;
    border-radius: 6px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    flex-shrink: 0;
}

.ai-product-info {
    min-width: 0;
    flex: 1;
}

.ai-product-name {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

.ai-product-price {
    font-size: 13px;
    font-weight: 700;
    color: #dc2626;
    margin-top: 2px;
}

/* Typing Dots */
.ai-typing-dots {
    background: #f1f5f9;
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
    background: #94a3b8;
    animation: typingBounce 1.2s infinite ease-in-out;
}
.ai-typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.ai-typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typingBounce {
    0%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-6px); background: #0284c7; }
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
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    color: #0284c7;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.18s, border-color 0.18s, transform 0.15s, box-shadow 0.18s;
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.08);
}
.ai-chip:hover {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.22);
}

/* ============================================================
   INPUT FOOTER (Pill Form & Disclaimer Text)
   ============================================================ */
#ai-chat-footer {
    background: #ffffff;
    padding: 12px 16px 14px;
    border-top: 1px solid #f1f5f9;
    flex-shrink: 0;
}

#ai-chat-form {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1.5px solid #0284c7;
    border-radius: 26px;
    padding: 4px 6px 4px 14px;
    background: #ffffff;
    transition: box-shadow 0.2s;
}
#ai-chat-form:focus-within {
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

#ai-mic-btn {
    border: none;
    background: none;
    color: #64748b;
    font-size: 16px;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.18s;
    flex-shrink: 0;
}
#ai-mic-btn:hover {
    color: #0284c7;
}

#ai-chat-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 14px;
    color: #0f172a;
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
    background: #0284c7;
    color: #ffffff;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.18s, transform 0.15s;
}
#ai-send-btn:hover {
    background: #0369a1;
    transform: scale(1.05);
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

        // Typewriter animation
        let i = 0;
        const speed = Math.max(10, Math.min(25, Math.round(2000 / Math.max(text.length, 1))));
        function typeChar() {
            if (i < text.length) {
                bubble.textContent += text[i++];
                scrollDown();
                setTimeout(typeChar, speed);
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

            const price = p.discount_percent > 0
                ? Math.round(p.price * (1 - p.discount_percent / 100))
                : p.price;

            const thumb = p.thumbnail
                ? '/storage/' + p.thumbnail.replace(/^\/?storage\//, '')
                : '/images/no-image.png';

            item.innerHTML = `
                <img src="${thumb}" alt="${escapeHtml(p.name)}">
                <div class="ai-product-info">
                    <span class="ai-product-name">${escapeHtml(p.name)}</span>
                    <span class="ai-product-price">${price.toLocaleString('vi-VN')}đ</span>
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
        const text = input.value.trim();
        if (!text) return;

        if (chips) chips.style.display = 'none';

        appendUser(text);
        input.value = '';
        showTyping();

        try {
            const res = await fetch('{{ route('chatbot.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text }),
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
        }
    });
})();
</script>
