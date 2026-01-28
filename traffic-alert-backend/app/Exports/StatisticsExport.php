<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\SuKienGiaoThong;
use App\Models\NguoiDung;
use App\Models\KetQuaAI;
use App\Models\ThongBao;
use App\Models\BaiDang;
use Illuminate\Support\Facades\DB;

class StatisticsExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new OverviewSheet($this->startDate, $this->endDate),
            new EventsSheet($this->startDate, $this->endDate),
            new AlertsSheet($this->startDate, $this->endDate),
            new UsersSheet($this->startDate, $this->endDate),
            new AIResultsSheet($this->startDate, $this->endDate),
        ];
    }
}

// Overview Sheet
class OverviewSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Tổng quan';
    }

    public function headings(): array
    {
        return [
            'Chỉ số',
            'Giá trị',
            'Ghi chú'
        ];
    }

    public function collection()
    {
        $query = SuKienGiaoThong::query();
        if ($this->startDate) $query->where('bat_dau_luc', '>=', $this->startDate);
        if ($this->endDate) $query->where('bat_dau_luc', '<=', $this->endDate);

        $totalEvents = $query->count();
        $activeEvents = (clone $query)->whereIn('trang_thai_id', [1, 2])->count();
        $totalUsers = NguoiDung::count();
        $totalAI = KetQuaAI::count();
        $verifiedAI = KetQuaAI::where('da_xac_minh', true)->count();
        $totalNotifications = ThongBao::count();
        $sentNotifications = ThongBao::where('trang_thai_gui', 'sent')->count();
        $totalAlerts = BaiDang::count();
        $approvedAlerts = BaiDang::where('trang_thai', 'da_duyet')->count();

        return collect([
            ['Tổng số sự kiện', $totalEvents, 'Tất cả sự kiện giao thông'],
            ['Sự kiện đang hoạt động', $activeEvents, 'Sự kiện đang diễn ra'],
            ['Tổng người dùng', $totalUsers, 'Tất cả người dùng đã đăng ký'],
            ['Tổng cảnh báo', $totalAlerts, 'Tất cả bài đăng cảnh báo'],
            ['Cảnh báo đã duyệt', $approvedAlerts, 'Cảnh báo đã được phê duyệt'],
            ['Kết quả AI', $totalAI, 'Tổng số lần phát hiện AI'],
            ['AI đã xác minh', $verifiedAI, 'Kết quả AI đã được xác minh'],
            ['Tổng thông báo', $totalNotifications, 'Tất cả thông báo'],
            ['Thông báo đã gửi', $sentNotifications, 'Thông báo đã gửi thành công'],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            ],
        ];
    }
}

// Events Sheet
class EventsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Sự kiện';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Loại sự kiện',
            'Đường',
            'Phường/Xã',
            'Trạng thái',
            'Nguồn',
            'Bắt đầu',
            'Kết thúc',
            'Mô tả'
        ];
    }

    public function collection()
    {
        $query = SuKienGiaoThong::with(['loaiSuKien', 'duong.phuongXa', 'trang_thai_su_kien']);
        
        if ($this->startDate) $query->where('bat_dau_luc', '>=', $this->startDate);
        if ($this->endDate) $query->where('bat_dau_luc', '<=', $this->endDate);

        return $query->get()->map(function ($event) {
            return [
                $event->id,
                $event->loaiSuKien->ten ?? 'N/A',
                $event->duong->ten ?? 'N/A',
                $event->duong?->phuongXa?->ten ?? 'N/A',
                $event->trang_thai_su_kien->ten ?? 'N/A',
                $event->nguon,
                $event->bat_dau_luc?->format('d/m/Y H:i'),
                $event->ket_thuc_luc?->format('d/m/Y H:i'),
                $event->mo_ta ?? ''
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            ],
        ];
    }
}

// Alerts Sheet
class AlertsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Cảnh báo';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Người dùng',
            'Loại',
            'Mức độ',
            'Đường',
            'Trạng thái',
            'Mô tả',
            'Ngày tạo'
        ];
    }

    public function collection()
    {
        $query = BaiDang::with(['nguoiDung', 'duong', 'mucDoSuKien']);
        
        if ($this->startDate) $query->where('created_at', '>=', $this->startDate);
        if ($this->endDate) $query->where('created_at', '<=', $this->endDate);

        return $query->get()->map(function ($alert) {
            return [
                $alert->id,
                $alert->nguoiDung?->ten_dang_nhap ?? 'Hệ thống',
                $alert->loai_canh_bao === 'traffic' ? 'Giao thông' : 'Ngập lụt',
                $alert->mucDoSuKien?->ten ?? 'N/A',
                $alert->duong?->ten ?? 'N/A',
                $alert->trang_thai,
                $alert->mo_ta ?? '',
                $alert->created_at->format('d/m/Y H:i')
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F59E0B']],
            ],
        ];
    }
}

// Users Sheet
class UsersSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Người dùng';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên đăng nhập',
            'Email',
            'Số điện thoại',
            'Vai trò',
            'Trạng thái',
            'Số cảnh báo',
            'Ngày đăng ký'
        ];
    }

    public function collection()
    {
        return NguoiDung::withCount('baiDang')->get()->map(function ($user) {
            return [
                $user->id,
                $user->ten_dang_nhap,
                $user->email,
                $user->so_dien_thoai ?? 'N/A',
                $user->vai_tro,
                $user->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Không hoạt động',
                $user->bai_dang_count,
                $user->created_at->format('d/m/Y H:i')
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B5CF6']],
            ],
        ];
    }
}

// AI Results Sheet
class AIResultsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Kết quả AI';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Media ID',
            'Nhãn phát hiện',
            'Độ tin cậy (%)',
            'Đã xác minh',
            'Ngày phát hiện'
        ];
    }

    public function collection()
    {
        $query = KetQuaAI::query();
        
        if ($this->startDate) $query->where('created_at', '>=', $this->startDate);
        if ($this->endDate) $query->where('created_at', '<=', $this->endDate);

        return $query->get()->map(function ($result) {
            return [
                $result->id,
                $result->media_id,
                $result->nhan,
                round($result->do_tin_cay * 100, 2),
                $result->da_xac_minh ? 'Có' : 'Không',
                $result->created_at->format('d/m/Y H:i')
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EF4444']],
            ],
        ];
    }
}
