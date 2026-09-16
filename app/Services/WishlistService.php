<?php
namespace App\Services;
use Illuminate\Support\Facades\Cookie;
class WishlistService
{
    const COOKIE_NAME = 'wishlist';
    const COOKIE_MINUTES = 525600;
    public function ids(): array
    {
        $value = (string) request()->cookie(self::COOKIE_NAME, '');
        if ($value === '') {
            return [];
        }
        return array_values(array_unique(array_filter(array_map('intval', explode(',', $value)))));
    }
    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }
    public function add(int $productId): array
    {
        $ids = $this->ids();
        if (!in_array($productId, $ids, true)) {
            $ids[] = $productId;
        }
        $this->remember($ids);
        return $ids;
    }
    public function remove(int $productId): array
    {
        $ids = array_values(array_diff($this->ids(), [$productId]));
        $this->remember($ids);
        return $ids;
    }
    private function remember(array $ids): void
    {
        Cookie::queue(self::COOKIE_NAME, implode(',', $ids), self::COOKIE_MINUTES);
    }
}
