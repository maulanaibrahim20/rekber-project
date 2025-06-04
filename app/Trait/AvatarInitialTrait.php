<?php

namespace App\Trait;

trait AvatarInitialTrait
{
    /**
     * Generate HTML avatar image or initials badge.
     *
     * @param string|null $imagePath Path gambar relatif dari storage (misal 'profiles/user.jpg')
     * @param string $fullName Nama lengkap untuk ambil inisial
     * @param int $size Ukuran width & height (default 40)
     * @param string $class Tambahan class CSS (default rounded-circle)
     * @return string HTML markup avatar
     */
    public function generateAvatarHtml(?string $imagePath, string $fullName, int $size = 40, string $class = 'rounded-circle'): string
    {
        if ($imagePath) {
            return '<img src="' . asset('storage/' . $imagePath) . '" class="' . $class . '" width="' . $size . '" height="' . $size . '" alt="avatar">';
        }

        $initials = collect(explode(' ', trim($fullName)))
            ->filter()
            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('');

        return '<div class="bg-secondary text-white fw-bold d-inline-flex align-items-center justify-content-center ' . $class . '" style="width:' . $size . 'px; height:' . $size . 'px; font-size:' . max(10, $size / 2.5) . 'px;">'
            . $initials .
            '</div>';
    }
}
