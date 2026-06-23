<?php

namespace App\Contracts\Services;

use App\Models\Reservation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ReservationServiceInterface
{
    public function getAllReservations(int $perPage = 15): LengthAwarePaginator;

    public function getUpcomingReservations(): Collection;

    public function findReservation(int $id): Reservation;

    public function createReservation(array $data): Reservation;

    public function updateReservation(int $id, array $data): Reservation;

    public function deleteReservation(int $id): void;

    public function updateStatus(int $id, string $status): Reservation;
    
    public function getAvailableTablesFor(int $partySize, string $datetime, ?int $excludeReservationId = null): Collection;
}
