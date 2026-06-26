@extends('layouts.admin')
@section('title', isset($news) ? 'Sửa bài viết' : 'Thêm bài viết')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px
    }

    .page-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a
    }

    .btn-back {
        padding: 8px 16px;
        background: #fff;
        color: #555;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px
    }

    .btn-back:hover {
        background: #f5f5f5
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start
    }

    @media(max-width:900px) {
        .form-grid {
            grid-template-columns: 1fr
        }
    }

    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        padding: 24px
    }

    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: #333;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0
    }

    .form-group {
        margin-bottom: 18px
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        outline: none;
        transition: border .2s
    }

    .form-control:focus {
        border-color: #1E88E5;
        box-shadow: 0 0 0 3px rgba(30, 136, 229, .1)
    }

    textarea.form-control {
        min-height: 400px;
        resize: vertical;
        font-family: inherit
    }

    select.form-control {
        cursor: pointer
    }

    .error-msg {
        color: #C62828;
        font-size: 12px;
        margin-top: 4px
    }

    .thumb-preview {
        width: 100%;
        border-radius: 8px;
        object-fit: cover;
        margin-top: 10px;
        display: none;
        max-height: 200px
    }

    .thumb-preview.show {
        display: block
    }

    .thumb-existing {
        width: 100%;
        border-radius: 8px;
        object-fit: cover;
        max-height: 180px;
        margin-bottom: 10px
    }

    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .toggle {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px
    }

    .toggle input {
        opacity: 0;
        width: 0;
        height: 0
    }

    .slider {
        position: absolute;
        inset: 0;
        background: #ccc;
        border-radius: 24px;
        cursor: pointer;
        transition: .2s
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: .2s
    }

    input:checked+.slider {
        background: #2E7D32
    }

    input:checked+.slider:before {
        transform: translateX(20px)
    }

    .toggle-label {
        font-size: 14px;
        color: #444;
        font-weight: 500
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        background: #1565C0;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px
    }

    .btn-submit:hover {
        background: #0D47A1
    }

    .hint {
        font-size: 11px;
        color: #999;
        margin-top: 4px
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <a href="{{ route('admin.news.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
    <h1 class="page-title">
        {{ isset($news) ? 'Sửa bài viết: ' . Str::limit($news->title, 50) : 'Thêm bài viết mới' }}
    </h1>
</div>

<form method="POST"
    action="{{ isset($news) ? route('admin.news.update', $news) : route('admin.news.store') }}"
    enctype="multipart/form-data" id="newsForm" novalidate>
    @csrf
    @if(isset($news)) @method('PUT') @endif

    <div class="form-grid">
        {{-- Cột trái: nội dung chính --}}
        <div>
            <div class="card">
                <div class="card-title"><i class="fas fa-file-alt" style="color:#1E88E5"></i> Nội dung bài viết</div>

                <div class="form-group">
                    <label for="title">Tiêu đề <span style="color:red">*</span></label>
                    <input type="text" id="title" name="title" class="form-control"
                        value="{{ old('title', $news->title ?? '') }}"
                        placeholder="Nhập tiêu đề bài viết…" required>
                    <div class="error-msg" id="title-error">@error('title'){{ $message }}@enderror</div>
                </div>

                <div class="form-group">
                    <label for="excerpt">Mô tả ngắn</label>
                    <textarea id="excerpt" name="excerpt" class="form-control" rows="3"
                        placeholder="Tóm tắt ngắn gọn nội dung bài viết, hiển thị ở danh sách tin tức…">{{ old('excerpt', $news->excerpt ?? '') }}</textarea>
                    <div class="hint">Hiển thị ở trang danh sách tin tức / preview, không cần quá dài.</div>
                    <div class="error-msg" id="excerpt-error">@error('excerpt'){{ $message }}@enderror</div>
                </div>

                <div class="form-group">
                    <label for="content">Nội dung <span style="color:red">*</span></label>
                    <textarea id="content" name="content" class="form-control"
                        placeholder="Nhập nội dung bài viết…" required>{{ old('content', $news->content ?? '') }}</textarea>
                    <div class="error-msg" id="content-error">@error('content'){{ $message }}@enderror</div>
                </div>
            </div>
        </div>

        {{-- Cột phải: meta & ảnh --}}
        <div style="display:flex;flex-direction:column;gap:16px">
            <div class="card">
                <div class="card-title"><i class="fas fa-cog" style="color:#1E88E5"></i> Cấu hình</div>

                <div class="form-group">
                    <label for="news_category_id">Danh mục <span style="color:red">*</span></label>
                    <select id="news_category_id" name="news_category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            @selected(old('news_category_id', $news->news_category_id ?? '') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="error-msg" id="news_category_id-error">@error('news_category_id'){{ $message }}@enderror</div>
                </div>

                <div class="form-group">
                    <label for="published_at">Ngày đăng</label>
                    <input type="datetime-local" id="published_at" name="published_at" class="form-control"
                        value="{{ old('published_at', isset($news->published_at) ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                    <div class="hint">Để trống = không xác định ngày đăng</div>
                    @error('published_at')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Hiển thị</label>
                    <div class="toggle-wrap">
                        <label class="toggle">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $news->is_active ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span class="toggle-label">Công khai bài viết</span>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas {{ isset($news) ? 'fa-save' : 'fa-plus' }}"></i>
                    {{ isset($news) ? 'Lưu thay đổi' : 'Thêm bài viết' }}
                </button>
            </div>

            <div class="card">
                <div class="card-title"><i class="fas fa-image" style="color:#1E88E5"></i> Ảnh thumbnail</div>

                @if(isset($news) && $news->thumbnail)
                <img src="{{ asset('storage/'.$news->thumbnail) }}" class="thumb-existing" alt="thumbnail">
                <div class="hint" style="margin-bottom:8px">Chọn ảnh mới để thay thế</div>
                @endif

                <div class="form-group" style="margin-bottom:0">
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control"
                        accept="image/*" onchange="previewThumb(this)">
                    <div class="hint">JPG, PNG, WebP – tối đa 2MB</div>
                    @error('thumbnail')<div class="error-msg">{{ $message }}</div>@enderror
                    <img id="thumb-preview" class="thumb-preview" src="" alt="preview">
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    // Khởi tạo CKEditor cho ô Mô tả ngắn (toolbar gọn) và Nội dung (đầy đủ)
    CKEDITOR.replace('excerpt', {
        height: 130,
        toolbar: [{
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline']
            },
            {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList']
            },
            {
                name: 'links',
                items: ['Link', 'Unlink']
            },
        ],
    });

    CKEDITOR.replace('content', {
        height: 420,
    });

    function previewThumb(input) {
        const preview = document.getElementById('thumb-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.add('show');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ── Validate form ─────────────────────────────────────────────
    (function() {
        const form = document.getElementById('newsForm');

        function setError(inputId, message) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.style.borderColor = '#C62828';
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
        }

        function clearError(inputId) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(inputId + '-error');
            if (input) input.style.borderColor = '';
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        }

        ['title', 'content', 'news_category_id'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', () => clearError(id));
            document.getElementById(id)?.addEventListener('change', () => clearError(id));
        });

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Đồng bộ dữ liệu từ CKEditor về <textarea> gốc trước khi kiểm tra
            if (typeof CKEDITOR !== 'undefined') {
                if (CKEDITOR.instances.content) CKEDITOR.instances.content.updateElement();
                if (CKEDITOR.instances.excerpt) CKEDITOR.instances.excerpt.updateElement();
            }

            // Tiêu đề
            const title = document.getElementById('title');
            if (!title || !title.value.trim()) {
                setError('title', 'Vui lòng nhập tiêu đề bài viết.');
                isValid = false;
            } else {
                clearError('title');
            }

            // Nội dung
            const content = document.getElementById('content');
            if (!content || !content.value.trim()) {
                setError('content', 'Vui lòng nhập nội dung bài viết.');
                isValid = false;
            } else {
                clearError('content');
            }

            // Danh mục
            const category = document.getElementById('news_category_id');
            if (!category || !category.value) {
                setError('news_category_id', 'Vui lòng chọn danh mục.');
                isValid = false;
            } else {
                clearError('news_category_id');
            }

            if (!isValid) {
                e.preventDefault();
                const firstError = form.querySelector('[style*="border-color: rgb(198, 40, 40)"], [style*="border-color:#C62828"]');
                if (firstError) firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    })();
</script>
@endpush