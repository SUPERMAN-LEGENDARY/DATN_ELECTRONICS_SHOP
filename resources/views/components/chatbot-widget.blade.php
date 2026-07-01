{{-- resources/views/components/chatbot-widget.blade.php --}}
{{-- Include trong layout chính bằng: <x-chatbot-widget /> hoặc @include('components.chatbot-widget') --}}

<div id="ai-chatbot-root">
    <button id="ai-chat-toggle" aria-label="Mở chat tư vấn">
        💬
    </button>

    <div id="ai-chat-window" class="hidden">
        <div id="ai-chat-header">
            <span>Trợ lý tư vấn</span>
            <button id="ai-chat-close" aria-label="Đóng">✕</button>
        </div>

        <div id="ai-chat-messages"></div>

        <form id="ai-chat-form">
            <input type="text" id="ai-chat-input" placeholder="Nhập câu hỏi, vd: tìm laptop dưới 20 triệu..." autocomplete="off" />
            <button type="submit">Gửi</button>
        </form>
    </div>
</div>

<style>
#ai-chatbot-root { position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: inherit; }
#ai-chat-toggle {
    width: 56px; height: 56px; border-radius: 50%; border: none;
    background: #2563eb; color: #fff; font-size: 24px; cursor: pointer;
    box-shadow: 0 4px 14px rgba(0,0,0,.2);
}
#ai-chat-window {
    position: absolute; bottom: 70px; right: 0; width: 360px; max-width: 90vw;
    height: 520px; max-height: 75vh; background: #fff; border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,.25); display: flex; flex-direction: column; overflow: hidden;
}
#ai-chat-window.hidden { display: none; }
#ai-chat-header {
    background: #2563eb; color: #fff; padding: 12px 16px; display: flex;
    justify-content: space-between; align-items: center; font-weight: 600;
}
#ai-chat-header button { background: none; border: none; color: #fff; cursor: pointer; font-size: 16px; }
#ai-chat-messages { flex: 1; overflow-y: auto; padding: 12px; background: #f8fafc; }
.ai-msg { margin-bottom: 10px; display: flex; }
.ai-msg.user { justify-content: flex-end; }
.ai-msg .bubble {
    max-width: 80%; padding: 8px 12px; border-radius: 10px; font-size: 14px; line-height: 1.4; white-space: pre-wrap;
}
.ai-msg.user .bubble { background: #2563eb; color: #fff; border-bottom-right-radius: 2px; }
.ai-msg.bot .bubble { background: #e5e7eb; color: #111827; border-bottom-left-radius: 2px; }
.ai-products { display: flex; flex-direction: column; gap: 8px; margin-top: 8px; }
.ai-product-card {
    display: flex; gap: 8px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;
    padding: 8px; text-decoration: none; color: inherit;
}
.ai-product-card img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; background: #f1f5f9; }
.ai-product-card .info { font-size: 13px; }
.ai-product-card .info .name { font-weight: 600; display: block; }
.ai-product-card .info .price { color: #dc2626; font-weight: 600; }
#ai-chat-form { display: flex; border-top: 1px solid #e5e7eb; }
#ai-chat-form input { flex: 1; border: none; padding: 12px; font-size: 14px; outline: none; }
#ai-chat-form button {
    border: none; background: #2563eb; color: #fff; padding: 0 16px; cursor: pointer; font-weight: 600;
}
.ai-typing { font-size: 13px; color: #6b7280; font-style: italic; padding: 4px 12px; }
</style>

<script>
(function () {
    const toggleBtn = document.getElementById('ai-chat-toggle');
    const closeBtn  = document.getElementById('ai-chat-close');
    const win       = document.getElementById('ai-chat-window');
    const form      = document.getElementById('ai-chat-form');
    const input     = document.getElementById('ai-chat-input');
    const messages  = document.getElementById('ai-chat-messages');

    toggleBtn.addEventListener('click', () => {
        win.classList.toggle('hidden');
        if (!win.classList.contains('hidden') && messages.children.length === 0) {
            appendBot('Chào bạn 👋 Mình có thể giúp tìm sản phẩm, so sánh, hoặc giải đáp thắc mắc. Bạn cần gì nhé?');
        }
    });
    closeBtn.addEventListener('click', () => win.classList.add('hidden'));

    function appendUser(text) {
        const div = document.createElement('div');
        div.className = 'ai-msg user';
        div.innerHTML = `<div class="bubble"></div>`;
        div.querySelector('.bubble').textContent = text;
        messages.appendChild(div);
        scrollDown();
    }

    function appendBot(text, products = []) {
        const div = document.createElement('div');
        div.className = 'ai-msg bot';
        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.textContent = text;
        div.appendChild(bubble);

        if (products && products.length) {
            const wrap = document.createElement('div');
            wrap.className = 'ai-products';
            products.forEach(p => {
                const a = document.createElement('a');
                a.href = `/products/${p.slug}`;
                a.className = 'ai-product-card';
                const finalPrice = p.discount_percent > 0
                    ? Math.round(p.price * (1 - p.discount_percent / 100))
                    : p.price;
                a.innerHTML = `
                    <img src="${p.thumbnail ? '/storage/' + p.thumbnail.replace(/^\/?storage\//,'') : '/images/no-image.png'}" alt="">
                    <div class="info">
                        <span class="name">${p.name}</span>
                        <span class="price">${finalPrice.toLocaleString('vi-VN')} đ</span>
                    </div>`;
                wrap.appendChild(a);
            });
            div.appendChild(wrap);
        }

        messages.appendChild(div);
        scrollDown();
    }

    function showTyping() {
        const div = document.createElement('div');
        div.className = 'ai-typing';
        div.id = 'ai-typing-indicator';
        div.textContent = 'Đang trả lời...';
        messages.appendChild(div);
        scrollDown();
    }
    function hideTyping() {
        document.getElementById('ai-typing-indicator')?.remove();
    }

    function scrollDown() {
        messages.scrollTop = messages.scrollHeight;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

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
            appendBot('Không thể kết nối tới máy chủ, vui lòng thử lại.');
        }
    });
})();
</script>