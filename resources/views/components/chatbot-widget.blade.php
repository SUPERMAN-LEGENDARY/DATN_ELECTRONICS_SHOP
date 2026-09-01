{{-- resources/views/components/chatbot-widget.blade.php --}}
{{-- Include trong layout chính bằng: <x-chatbot-widget /> hoặc @include('components.chatbot-widget') --}}

<div id="ai-chatbot-root">

    {{-- Floating Toggle Button --}}
    <button id="ai-chat-toggle" aria-label="Mở chat tư vấn thông minh">
        <span id="ai-chat-pulse"></span>
        <span class="ai-toggle-inner">
            <i class="fas fa-sparkles ai-toggle-badge"></i>
            <i class="fas fa-comment-dots" id="ai-chat-icon-open"></i>
            <i class="fas fa-xmark" id="ai-chat-icon-close" style="display:none"></i>
        </span>
    </button>

    {{-- Main Chat Window Frame --}}
    <div id="ai-chat-window" class="ai-hidden">

        {{-- Header Bar --}}
        <div id="ai-chat-header">
            <div class="ai-hdr-left">
                <div class="ai-hdr-avatar">
                    <i class="fas fa-robot"></i>
                    <span class="ai-hdr-online-badge"></span>
                </div>
                <div class="ai-hdr-info">
                    <div class="ai-hdr-title">
                        <span>Trợ lý AI ES</span>
                        <span class="ai-pro-badge">AI Assistant</span>
                    </div>
                    <div class="ai-hdr-status">
                        <span class="ai-status-dot"></span> Sẵn sàng tư vấn 24/7
                    </div>
                </div>
            </div>
            <div class="ai-hdr-actions">
                <button type="button" id="ai-chat-refresh" title="Làm mới cuộc trò chuyện" aria-label="Làm mới">
                    <i class="fas fa-rotate-right"></i>
                </button>
                <button type="button" id="ai-chat-close" title="Đóng" aria-label="Đóng">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </div>

        {{-- Scrollable Chat Content Area --}}
        <div id="ai-chat-body">

            {{-- Central Welcome Screen --}}
            <div id="ai-welcome-box">
                <div class="ai-welcome-avatar-ring">
                    <div class="ai-welcome-avatar-inner">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="ai-welcome-glow"></div>
                </div>
                <div class="ai-welcome-heading">
                    Xin chào! Em là <span class="ai-brand-accent">Trợ lý AI thông minh</span>
                </div>
                <div class="ai-welcome-sub">
                    Em có thể giúp bạn <strong>so sánh sản phẩm</strong>, <strong>tư vấn cấu hình</strong>, kiểm tra <strong>giá bán & khuyến mãi</strong> ngay tức thì!
                </div>
            </div>

            {{-- Timestamp Divider --}}
            <div class="ai-timestamp-divider">
                <span>Hôm nay, <strong id="ai-current-time">12:00</strong></span>
            </div>

            {{-- Chat Messages List --}}
            <div id="ai-chat-messages"></div>

            {{-- Quick Chips Suggestions --}}
            <div id="ai-quick-chips">
                <button type="button" class="ai-chip" data-msg="Tư vấn điện thoại chơi game mượt mà">🎮 Điện thoại Gaming</button>
                <button type="button" class="ai-chip" data-msg="Tư vấn điện thoại chụp ảnh đẹp, camera sắc nét">📸 Chụp ảnh đẹp nhất</button>
                <button type="button" class="ai-chip" data-msg="Tư vấn điện thoại pin trâu dùng cả ngày">🔋 Pin trâu 5000mAh+</button>
                <button type="button" class="ai-chip" data-msg="So sánh iPhone 16 Pro Max và Samsung Galaxy S24 Ultra">⚖️ So sánh iPhone & Samsung</button>
                <button type="button" class="ai-chip" data-msg="iPhone 16 Pro Max có những phiên bản dung lượng và màu sắc nào?">💾 Các bản iPhone 16</button>
                <button type="button" class="ai-chip" data-msg="Chương trình khuyến mãi và mã giảm giá đang có">🎁 Mã giảm giá HOT</button>
            </div>

        </div>

        {{-- Footer Section --}}
        <div id="ai-chat-footer">
            <form id="ai-chat-form">
                <button type="button" id="ai-mic-btn" title="Nhập giọng nói" aria-label="Giọng nói">
                    <i class="fas fa-microphone"></i>
                </button>
                <input type="text" id="ai-chat-input" placeholder="Hỏi AI về thông số, so sánh, giá cả..." autocomplete="off" />
                <button type="submit" id="ai-send-btn" aria-label="Gửi tin nhắn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

            <div class="ai-disclaimer">
                <i class="fas fa-shield-halved"></i> Thông tin được hỗ trợ bởi Trí Tuệ Nhân Tạo & Dữ liệu kho hàng thực tế
            </div>
        </div>

    </div>
</div>

<style>
/* ============================================================
   DESIGN TOKENS & ROOT CONTAINER
   ============================================================ */
#ai-chatbot-root {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --ai-primary: #1e293b;
    --ai-primary-dark: #0f172a;
    --ai-accent: #2563eb;
    --ai-accent-hover: #1d4ed8;
    --ai-accent-light: #eff6ff;
    --ai-surface: #f8fafc;
    --ai-card-bg: #ffffff;
    --ai-line: #e2e8f0;
    --ai-line-dark: #cbd5e1;
    --ai-text-main: #0f172a;
    --ai-text-sub: #475569;
    --ai-text-muted: #94a3b8;
    --ai-success: #10b981;
    --ai-radius-lg: 22px;
    --ai-radius-md: 16px;
    --ai-radius-sm: 10px;
    --ai-shadow: 0 16px 40px -8px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(15, 23, 42, 0.06);
    --ai-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

/* ============================================================
   FLOATING TOGGLE BUTTON
   ============================================================ */
#ai-chat-toggle {
    width: 62px;
    height: 62px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, var(--ai-primary), var(--ai-primary-dark));
    color: #ffffff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 28px -4px rgba(15, 23, 42, 0.38), 0 0 0 2px rgba(255, 255, 255, 0.2);
    transition: transform 0.3s var(--ai-ease), box-shadow 0.3s var(--ai-ease);
    position: relative;
}

#ai-chat-toggle:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.48), 0 0 0 3px rgba(37, 99, 235, 0.4);
}

.ai-toggle-inner {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.ai-toggle-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    font-size: 11px;
    color: #fde047;
    animation: sparkleSpin 3s ease-in-out infinite;
}

@keyframes sparkleSpin {
    0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.9; }
    50% { transform: scale(1.25) rotate(15deg); opacity: 1; }
}

#ai-chat-pulse {
    position: absolute;
    inset: -6px;
    border-radius: 50%;
    border: 2px solid rgba(37, 99, 235, 0.5);
    animation: chatPulse 2.4s cubic-bezier(0.25, 1, 0.5, 1) infinite;
    pointer-events: none;
}

@keyframes chatPulse {
    0%   { transform: scale(0.95); opacity: 0.9; }
    70%  { transform: scale(1.4); opacity: 0; }
    100% { transform: scale(1.4); opacity: 0; }
}

/* ============================================================
   MAIN CHAT WINDOW FRAME
   ============================================================ */
#ai-chat-window {
    position: fixed;
    bottom: 98px;
    right: 24px;
    width: 410px;
    max-width: calc(100vw - 32px);
    height: 600px;
    max-height: calc(100vh - 120px);
    background: #ffffff;
    border-radius: var(--ai-radius-lg);
    box-shadow: var(--ai-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 9998;
    border: 1px solid var(--ai-line);
    transition: all 0.3s var(--ai-ease);
}

#ai-chat-window.ai-hidden {
    display: none !important;
}

#ai-chat-window.ai-entering {
    animation: windowPop 0.35s var(--ai-ease) forwards;
}

@keyframes windowPop {
    from { transform: scale(0.92) translateY(20px); opacity: 0; }
    to   { transform: scale(1) translateY(0); opacity: 1; }
}

/* ============================================================
   HEADER BAR
   ============================================================ */
#ai-chat-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 14px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    color: #ffffff;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.ai-hdr-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.ai-hdr-avatar {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.8), rgba(15, 23, 42, 0.9));
    border: 1.5px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.ai-hdr-online-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--ai-success);
    border: 2px solid #0f172a;
}

.ai-hdr-title {
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}

.ai-pro-badge {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    padding: 2px 6px;
    border-radius: 6px;
    color: #ffffff;
}

.ai-hdr-status {
    font-size: 11.5px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 2px;
}

.ai-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--ai-success);
    display: inline-block;
    box-shadow: 0 0 8px var(--ai-success);
}

.ai-hdr-actions {
    display: flex;
    gap: 6px;
}

.ai-hdr-actions button {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #ffffff;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s var(--ai-ease);
}

.ai-hdr-actions button:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.06);
}

/* ============================================================
   CHAT BODY & SCROLLABLE CONTENT
   ============================================================ */
#ai-chat-body {
    flex: 1;
    overflow-y: auto;
    background: var(--ai-surface);
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
    background: var(--ai-line-dark);
    border-radius: 4px;
}

/* Central Welcome Box */
#ai-welcome-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 14px 10px 8px;
    flex-shrink: 0;
}

.ai-welcome-avatar-ring {
    position: relative;
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1e293b, #0f172a);
    padding: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.25);
}

.ai-welcome-avatar-inner {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #60a5fa;
    font-size: 34px;
    border: 2px solid rgba(255, 255, 255, 0.15);
}

.ai-welcome-glow {
    position: absolute;
    inset: -8px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
    z-index: -1;
    animation: glowPulse 3s ease-in-out infinite alternate;
}

@keyframes glowPulse {
    0% { transform: scale(0.9); opacity: 0.4; }
    100% { transform: scale(1.15); opacity: 0.8; }
}

.ai-welcome-heading {
    font-size: 15px;
    color: var(--ai-text-main);
    font-weight: 600;
    margin-bottom: 4px;
}

.ai-brand-accent {
    color: var(--ai-accent);
    font-weight: 700;
}

.ai-welcome-sub {
    font-size: 12.5px;
    color: var(--ai-text-sub);
    line-height: 1.5;
    max-width: 320px;
}

.ai-welcome-sub strong {
    color: var(--ai-text-main);
}

/* Timestamp Divider */
.ai-timestamp-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 14px 0 10px;
    flex-shrink: 0;
}

.ai-timestamp-divider span {
    font-size: 11px;
    color: var(--ai-text-muted);
    background: rgba(226, 232, 240, 0.6);
    padding: 3px 12px;
    border-radius: 12px;
}

/* ============================================================
   MESSAGES & BUBBLES
   ============================================================ */
#ai-chat-messages {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.ai-msg-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
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
    border-radius: 10px;
    background: linear-gradient(135deg, #1e293b, #0f172a);
    color: #60a5fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    margin-top: 2px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.ai-msg-bubble {
    max-width: 86%;
    padding: 11px 15px;
    font-size: 13.5px;
    line-height: 1.6;
    border-radius: var(--ai-radius-md);
    word-break: break-word;
}

.ai-msg-row.bot .ai-msg-bubble {
    background: #ffffff;
    color: var(--ai-text-main);
    border-top-left-radius: 4px;
    border: 1px solid var(--ai-line);
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.ai-msg-row.user .ai-msg-bubble {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    border-top-right-radius: 4px;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
}

/* ============================================================
   RICH FORMATTING TRONG TIN NHẮN BOT (DỄ NHÌN, THOÁNG ĐÃNG)
   ============================================================ */
.ai-msg-bubble strong {
    font-weight: 700;
    color: #0f172a;
}

.ai-msg-row.user .ai-msg-bubble strong {
    color: #ffffff;
}

.ai-msg-h1, .ai-msg-h2, .ai-msg-h3 {
    font-weight: 700;
    margin: 8px 0 4px;
    color: #0f172a;
    line-height: 1.4;
}
.ai-msg-h1 { font-size: 15px; }
.ai-msg-h2 { font-size: 14.5px; }
.ai-msg-h3 { font-size: 14px; }

.ai-msg-li {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    margin: 4px 0;
    line-height: 1.5;
}

.ai-li-dot {
    color: var(--ai-accent);
    font-weight: 700;
    font-size: 15px;
    line-height: 1.3;
    flex-shrink: 0;
}

.ai-li-num {
    color: var(--ai-accent);
    font-weight: 700;
    font-size: 13px;
    flex-shrink: 0;
}

/* ============================================================
   DYNAMIC THINKING / SEARCHING INDICATOR (ĐÁNH LỪA THỊ GIÁC)
   ============================================================ */
.ai-typing-row {
    align-items: center;
}

.ai-pulse-anim {
    animation: botPulse 1.6s ease-in-out infinite alternate;
}

@keyframes botPulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
    100% { transform: scale(1.08); box-shadow: 0 0 12px 2px rgba(37, 99, 235, 0.6); }
}

.ai-thinking-box {
    background: #ffffff;
    border: 1px solid var(--ai-line);
    border-radius: 16px;
    border-top-left-radius: 4px;
    padding: 10px 14px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 210px;
}

.ai-thinking-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    color: var(--ai-text-sub);
    font-weight: 600;
}

.ai-thinking-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 6px;
    background: var(--ai-accent-light);
    color: var(--ai-accent);
    font-size: 11px;
}

.ai-thinking-icon i {
    animation: iconSpin 2s linear infinite;
}

@keyframes iconSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.ai-thinking-text {
    flex: 1;
    transition: opacity 0.25s ease-in-out;
    white-space: nowrap;
}

.ai-thinking-dots {
    display: flex;
    gap: 3px;
}

.ai-thinking-dots span {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--ai-accent);
    animation: typingBounce 1.2s infinite ease-in-out;
}

.ai-thinking-dots span:nth-child(2) { animation-delay: 0.2s; }
.ai-thinking-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBounce {
    0%, 80%, 100% { transform: translateY(0); opacity: 0.4; }
    40% { transform: translateY(-4px); opacity: 1; }
}

.ai-thinking-bar {
    width: 100%;
    height: 3px;
    background: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.ai-thinking-progress {
    width: 40%;
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6, #10b981);
    border-radius: 4px;
    position: absolute;
    animation: progressShimmer 1.8s infinite ease-in-out;
}

@keyframes progressShimmer {
    0% { left: -40%; }
    100% { left: 100%; }
}

/* ============================================================
   RECOMMENDED PRODUCTS CONTAINER (CARD HIỆN ĐẠI)
   ============================================================ */
.ai-products-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 10px;
    width: 100%;
}

.ai-product-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    border: 1px solid var(--ai-line);
    border-radius: 12px;
    padding: 9px 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s var(--ai-ease);
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
}

.ai-product-item:hover {
    border-color: var(--ai-accent);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.12);
    transform: translateY(-2px);
    background: #fafcff;
}

.ai-product-item img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid var(--ai-line);
    flex-shrink: 0;
    padding: 2px;
}

.ai-product-info {
    min-width: 0;
    flex: 1;
}

.ai-product-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--ai-text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 2px;
}

.ai-product-item:hover .ai-product-name {
    color: var(--ai-accent);
}

.ai-product-price-row {
    display: flex;
    align-items: center;
    gap: 6px;
}

.ai-product-price {
    font-size: 13.5px;
    font-weight: 700;
    color: #dc2626;
}

.ai-product-list-price {
    font-size: 11px;
    font-weight: 500;
    color: var(--ai-text-muted);
    text-decoration: line-through;
}

.ai-product-action-arrow {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--ai-surface);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ai-text-muted);
    font-size: 11px;
    transition: all 0.2s var(--ai-ease);
    flex-shrink: 0;
}

.ai-product-item:hover .ai-product-action-arrow {
    background: var(--ai-accent);
    color: #ffffff;
    transform: translateX(2px);
}

/* ============================================================
   QUICK SUGGESTION CHIPS
   ============================================================ */
#ai-quick-chips {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 10px 16px 4px;
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
    padding: 7px 14px;
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid var(--ai-line);
    color: var(--ai-text-sub);
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s var(--ai-ease);
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
}

.ai-chip:hover {
    background: var(--ai-primary);
    color: #ffffff;
    border-color: var(--ai-primary);
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(15, 23, 42, 0.16);
}

/* ============================================================
   INPUT FOOTER
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
    border: 1.5px solid var(--ai-line);
    border-radius: 26px;
    padding: 4px 6px 4px 14px;
    background: var(--ai-surface);
    transition: all 0.2s var(--ai-ease);
}

#ai-chat-form:focus-within {
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    border-color: var(--ai-accent);
    background: #ffffff;
}

#ai-mic-btn {
    border: none;
    background: none;
    color: var(--ai-text-muted);
    font-size: 15px;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s var(--ai-ease);
    flex-shrink: 0;
}

#ai-mic-btn:hover {
    color: var(--ai-accent);
}

#ai-chat-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 13.5px;
    color: var(--ai-text-main);
    background: transparent;
    padding: 6px 0;
    min-width: 0;
}

#ai-chat-input::placeholder {
    color: var(--ai-text-muted);
}

#ai-send-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: transform 0.2s var(--ai-ease), box-shadow 0.2s var(--ai-ease);
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
}

#ai-send-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 14px rgba(37, 99, 235, 0.4);
}

#ai-chat-input:disabled {
    opacity: 0.6;
}

#ai-send-btn:disabled,
#ai-mic-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

#ai-send-btn:disabled:hover {
    transform: none;
}

.ai-disclaimer {
    text-align: center;
    font-size: 10px;
    color: var(--ai-text-muted);
    margin-top: 8px;
    line-height: 1.4;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
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

    // Session token
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
            appendBot('Xin chào! Em là **Trợ lý AI của ElectronicShop**. Anh/chị cần tư vấn thông số kỹ thuật, so sánh máy hay xem khuyến mãi nào ạ?');
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
        appendBot('Xin chào! Cuộc trò chuyện đã được làm mới. Em có thể giúp gì cho anh/chị ạ?');
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

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /* Markdown Formatter để tin nhắn Bot hiển thị đẹp và dễ nhìn */
    function formatMarkdown(text) {
        let html = escapeHtml(text);
        
        // Bold: **text** hoặc __text__
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/__(.*?)__/g, '<strong>$1</strong>');
        
        // Italic: *text*
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Headers: ###, ##, #
        html = html.replace(/^### (.*$)/gim, '<div class="ai-msg-h3">$1</div>');
        html = html.replace(/^## (.*$)/gim, '<div class="ai-msg-h2">$1</div>');
        html = html.replace(/^# (.*$)/gim, '<div class="ai-msg-h1">$1</div>');
        
        // Bullet list: - text hoặc * text
        html = html.replace(/^\s*[-*•]\s+(.*$)/gim, '<div class="ai-msg-li"><span class="ai-li-dot">•</span><span>$1</span></div>');
        
        // Numbered list: 1. text
        html = html.replace(/^\s*(\d+)\.\s+(.*$)/gim, '<div class="ai-msg-li"><span class="ai-li-num">$1.</span><span>$2</span></div>');
        
        // Line breaks
        html = html.replace(/\n/g, '<br>');
        
        // Clean redundant <br> around list items
        html = html.replace(/(<\/div>)<br>(<div class="ai-msg-li">)/g, '$1$2');
        html = html.replace(/(<\/div>)<br>(<div class="ai-msg-h[123]">)/g, '$1$2');
        
        return html;
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

        // Typewriter animation với Markdown HTML
        const formattedHtml = formatMarkdown(text);
        
        // Nếu text ngắn thì hiển thị ngay, text dài thì gõ mượt mà trong ~600ms
        const TICK_MS = 20;
        const totalDurationMs = Math.min(1000, Math.max(200, text.length * 8));
        const totalTicks = Math.max(1, Math.round(totalDurationMs / TICK_MS));
        const charsPerTick = Math.max(1, Math.ceil(text.length / totalTicks));

        let i = 0;
        function typeChar() {
            if (i < text.length) {
                i = Math.min(text.length, i + charsPerTick);
                bubble.innerHTML = formatMarkdown(text.slice(0, i));
                scrollDown();
                setTimeout(typeChar, TICK_MS);
            } else {
                bubble.innerHTML = formattedHtml;
                if (products && products.length) {
                    renderProducts(bubble, products);
                }
                scrollDown();
            }
        }
        typeChar();
    }

    function renderProducts(parentBubble, products) {
        const container = document.createElement('div');
        container.className = 'ai-products-container';

        products.forEach(p => {
            const item = document.createElement('a');
            item.href = `{{ url('san-pham') }}/${p.slug}`;
            item.className = 'ai-product-item';

            const displayPrice = p.has_price_range ? p.min_price : p.price;
            const pricePrefix = p.has_price_range ? 'Từ ' : '';

            const thumb = p.thumbnail
                ? '/storage/' + p.thumbnail.replace(/^\/?storage\//, '')
                : '/images/no-image.png';

            item.innerHTML = `
                <img src="${thumb}" alt="${escapeHtml(p.name)}" loading="lazy">
                <div class="ai-product-info">
                    <span class="ai-product-name">${escapeHtml(p.name)}</span>
                    <div class="ai-product-price-row">
                        <span class="ai-product-price">${pricePrefix}${Math.round(displayPrice).toLocaleString('vi-VN')}đ</span>
                        ${(!p.has_price_range && p.is_on_sale) ? `<span class="ai-product-list-price">${Math.round(p.list_price).toLocaleString('vi-VN')}đ</span>` : ''}
                    </div>
                </div>
                <span class="ai-product-action-arrow" title="Xem chi tiết">
                    <i class="fas fa-arrow-right"></i>
                </span>
            `;
            container.appendChild(item);
        });

        parentBubble.appendChild(container);
        scrollDown();
    }

    /* ============================================================
       DYNAMIC THINKING & STATUS ROTATION (ĐÁNH LỪA THỊ GIÁC)
       ============================================================ */
    let typingInterval = null;
    const typingStages = [
        { icon: 'fas fa-magnifying-glass', text: 'Đang tìm kiếm thông tin sản phẩm...' },
        { icon: 'fas fa-microchip', text: 'Đang phân tích & đối chiếu thông số...' },
        { icon: 'fas fa-pen-fancy', text: 'Trợ lý AI đang soạn câu trả lời...' }
    ];

    function showTyping() {
        hideTyping();
        const row = document.createElement('div');
        row.className = 'ai-msg-row bot ai-typing-row';
        row.id = 'ai-typing-row';
        row.innerHTML = `
            <div class="ai-bot-msg-icon ai-pulse-anim"><i class="fas fa-robot"></i></div>
            <div class="ai-thinking-box">
                <div class="ai-thinking-header">
                    <span class="ai-thinking-icon"><i class="fas fa-magnifying-glass"></i></span>
                    <span class="ai-thinking-text">Đang tìm kiếm thông tin sản phẩm...</span>
                    <span class="ai-thinking-dots">
                        <span></span><span></span><span></span>
                    </span>
                </div>
                <div class="ai-thinking-bar"><div class="ai-thinking-progress"></div></div>
            </div>
        `;
        messages.appendChild(row);
        scrollDown();

        let stageIdx = 0;
        const iconEl = row.querySelector('.ai-thinking-icon i');
        const textEl = row.querySelector('.ai-thinking-text');

        // Xoay vòng các trạng thái thông minh để tạo cảm giác AI đang xử lý thực tế
        typingInterval = setInterval(() => {
            stageIdx = (stageIdx + 1) % typingStages.length;
            const stage = typingStages[stageIdx];
            if (textEl && iconEl) {
                textEl.style.opacity = '0';
                setTimeout(() => {
                    iconEl.className = stage.icon;
                    textEl.textContent = stage.text;
                    textEl.style.opacity = '1';
                }, 200);
            }
        }, 1600);
    }

    function hideTyping() {
        if (typingInterval) {
            clearInterval(typingInterval);
            typingInterval = null;
        }
        document.getElementById('ai-typing-row')?.remove();
    }

    /* Submit Form */
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (isSending) return;
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