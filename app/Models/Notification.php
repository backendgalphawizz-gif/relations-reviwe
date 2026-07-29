<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\services\FCMService;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
       'title',
       'description',
       'image',
       'send_to',
       'send_to_user_ids',
       'createdBy',
       'modifiedBy'
    ];

    public function sendToLabel(): string
    {
        return match ($this->send_to) {
            'all' => 'All (Customers + Advisors)',
            'all_customers' => 'All Customers',
            'single_customer' => 'Single Customer',
            'all_advisors' => 'All Advisors',
            'single_advisor' => 'Single Advisor',
            default => '—',
        };
    }

    public function sendToUserIds(): array
    {
        if (empty($this->send_to_user_ids)) {
            return [];
        }
        $decoded = json_decode($this->send_to_user_ids, true);

        return is_array($decoded) ? array_map('intval', $decoded) : [];
    }
}
