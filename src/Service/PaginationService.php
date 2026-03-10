<?php

namespace App\Service;

class PaginationService
{
    public function paginate(int $totalItems, int $currentPage, int $perPage): array
    {
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        if ($currentPage < 1 || $currentPage > $totalPages) {
            return [
                'isValid'     => false,
                'totalPages'  => $totalPages,
                'currentPage' => $currentPage,
            ];
        }

        return [
            'isValid'     => true,
            'currentPage' => $currentPage,
            'totalPages'  => $totalPages,
            'totalItems'  => $totalItems,
            'perPage'     => $perPage,
        ];
    }
}