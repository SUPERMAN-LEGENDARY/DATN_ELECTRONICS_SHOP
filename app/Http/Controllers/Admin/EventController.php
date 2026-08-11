<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    // ─── Danh sách sự kiện ────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = Event::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%$q%")
                    ->orWhere('tag', 'like', "%$q%");
            });
        }

        // ── Thêm mới: lọc theo trạng thái và khoảng thời gian (theo toolbar trong index.blade.php) ──
        if ($request->filled('status')) {
            $now = now();
            switch ($request->status) {
                case 'ongoing':
                    $query->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                    break;
                case 'upcoming':
                    $query->where('is_active', true)
                        ->where('start_date', '>', $now);
                    break;
                case 'ended':
                    $query->where('end_date', '<', $now);
                    break;
                case 'draft':
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('end_date', '<=', $request->to_date);
        }
        // ── Hết phần thêm mới ──

        $events = $query->ordered()->paginate(15)->withQueryString();
        $trashedCount = Event::onlyTrashed()->count();

        // ── Thêm mới: thống kê cho 4 thẻ, dữ liệu preview và lịch sắp tới ──
        $now = now();

        $totalCount = Event::count();

        $ongoingCount = Event::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();

        $upcomingCount = Event::where('is_active', true)
            ->where('start_date', '>', $now)
            ->count();

        $endedCount = Event::where('end_date', '<', $now)->count();

        $upcomingEvents = Event::where('is_active', true)
            ->where('start_date', '>', $now)
            ->ordered()
            ->limit(6)
            ->get();

        $previewEvent = Event::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->ordered()
            ->first() ?? $events->first();

        $countdown = null;
        if ($previewEvent && $previewEvent->end_date) {
            $diff = $now->diff($previewEvent->end_date);
            $countdown = [
                'days'    => str_pad((string) $diff->days, 2, '0', STR_PAD_LEFT),
                'hours'   => str_pad((string) $diff->h, 2, '0', STR_PAD_LEFT),
                'minutes' => str_pad((string) $diff->i, 2, '0', STR_PAD_LEFT),
                'seconds' => str_pad((string) $diff->s, 2, '0', STR_PAD_LEFT),
            ];
        }
        // ── Hết phần thêm mới ──

        return view('admin.events.index', compact(
            'events', 'trashedCount',
            'totalCount', 'ongoingCount', 'upcomingCount', 'endedCount',
            'upcomingEvents', 'previewEvent', 'countdown'
        ));
    }

    // ─── Form thêm mới ────────────────────────────────────────────
    public function create(): View
    {
        return view('admin.events.form', ['event' => new Event()]);
    }

    // ─── Lưu sự kiện mới ──────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store('events', 'public'));
        }

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Thêm sự kiện thành công!');
    }

    // ─── Form chỉnh sửa ───────────────────────────────────────────
    public function edit(Event $event): View
    {
        return view('admin.events.form', compact('event'));
    }

    // ─── Cập nhật sự kiện ─────────────────────────────────────────
    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validateData($request, $event);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $event->image));
            }
            $data['image'] = Storage::url($request->file('image')->store('events', 'public'));
        } else {
            unset($data['image']);
        }

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Cập nhật sự kiện thành công!');
    }

    // ─── Bật / Ẩn hiển thị ────────────────────────────────────────
    public function toggleActive(Event $event): RedirectResponse
    {
        $event->update(['is_active' => ! $event->is_active]);

        return back()->with('success', $event->is_active
            ? "Đã hiển thị sự kiện \"{$event->title}\" trên trang chủ."
            : "Đã ẩn sự kiện \"{$event->title}\".");
    }

    // ─── Bật / Ẩn hàng loạt (chọn nhiều sự kiện) ──────────────────
    public function bulkToggle(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['integer', 'exists:events,id'],
            'action' => ['required', 'in:show,hide'],
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một sự kiện.',
            'ids.min'      => 'Vui lòng chọn ít nhất một sự kiện.',
            'action.required' => 'Hành động không hợp lệ.',
            'action.in'        => 'Hành động không hợp lệ.',
        ]);

        $active = $request->action === 'show';
        Event::whereIn('id', $request->ids)->update(['is_active' => $active]);

        return back()->with('success', $active
            ? 'Đã hiển thị các sự kiện đã chọn trên trang chủ.'
            : 'Đã ẩn các sự kiện đã chọn.');
    }

    // ─── Xoá (chuyển vào thùng rác) ────────────────────────────────
    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Đã chuyển sự kiện \"{$event->title}\" vào thùng rác.");
    }

    // ─── Thùng rác ──────────────────────────────────────────────────
    public function trash(): View
    {
        $events = Event::onlyTrashed()->ordered()->paginate(15);

        return view('admin.events.trash', compact('events'));
    }

    public function restore(int $id): RedirectResponse
    {
        $event = Event::onlyTrashed()->findOrFail($id);
        $event->restore();

        return back()->with('success', "Đã khôi phục sự kiện \"{$event->title}\".");
    }

    public function restoreAll(): RedirectResponse
    {
        Event::onlyTrashed()->restore();

        return back()->with('success', 'Đã khôi phục tất cả sự kiện trong thùng rác.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $event = Event::onlyTrashed()->findOrFail($id);

        if ($event->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $event->image));
        }

        $event->forceDelete();

        return back()->with('success', 'Đã xoá vĩnh viễn sự kiện.');
    }

    public function emptyTrash(): RedirectResponse
    {
        $trashed = Event::onlyTrashed()->get();

        foreach ($trashed as $event) {
            if ($event->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $event->image));
            }
        }

        Event::onlyTrashed()->forceDelete();

        return back()->with('success', 'Đã dọn sạch thùng rác sự kiện.');
    }

    // ─── Validate dữ liệu chung cho store/update ─────────────────
    private function validateData(Request $request, ?Event $event = null): array
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'min:3', 'max:255'],
            'tag'          => ['nullable', 'string', 'max:100'],
            'offer_text'   => ['nullable', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'button_text'  => ['nullable', 'string', 'max:100'],
            'button_link'  => ['nullable', 'string', 'max:255'],
            'bg_color'     => ['nullable', 'string', 'max:20', 'regex:/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],
            'text_color'   => ['nullable', 'string', 'max:20', 'regex:/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],
            'start_date'   => ['required', 'date'],
            'end_date'     => ['required', 'date', 'after_or_equal:start_date'],
            'sort_order'   => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active'    => ['nullable', 'boolean'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'title.required'      => 'Vui lòng nhập tên sự kiện.',
            'title.min'            => 'Tên sự kiện phải có ít nhất :min ký tự.',
            'title.max'            => 'Tên sự kiện không vượt quá :max ký tự.',
            'tag.max'               => 'Nhãn không vượt quá :max ký tự.',
            'offer_text.max'        => 'Ưu đãi nổi bật không vượt quá :max ký tự.',
            'description.max'       => 'Mô tả không vượt quá :max ký tự.',
            'button_text.max'       => 'Văn bản nút bấm không vượt quá :max ký tự.',
            'button_link.max'       => 'Đường dẫn nút bấm không vượt quá :max ký tự.',
            'bg_color.regex'        => 'Màu nền phải là mã màu hex hợp lệ, vd: #C62828.',
            'text_color.regex'      => 'Màu chữ phải là mã màu hex hợp lệ, vd: #FFFFFF.',
            'start_date.date'       => 'Ngày bắt đầu không hợp lệ.',
            'start_date.required'   => 'Vui lòng chọn ngày bắt đầu sự kiện.',
            'end_date.date'         => 'Ngày kết thúc không hợp lệ.',
            'end_date.required'     => 'Vui lòng chọn ngày kết thúc sự kiện.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'sort_order.integer'    => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min'        => 'Thứ tự hiển thị không được nhỏ hơn :min.',
            'sort_order.max'        => 'Thứ tự hiển thị không được lớn hơn :max.',
            'image.image'           => 'File tải lên phải là hình ảnh.',
            'image.mimes'           => 'Ảnh chỉ chấp nhận định dạng JPG, JPEG, PNG hoặc WEBP.',
            'image.max'             => 'Ảnh không được vượt quá 4MB.',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active']  = $request->boolean('is_active');

        unset($validated['image']);

        return $validated;
    }

    // ─── Xem chi tiết sự kiện ─────────────────────────────────────
    public function show(Event $event): View
    {
        return view('admin.events.show', compact('event'));
    }
}