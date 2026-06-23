<?php

namespace App\Services;

use App\Contracts\Services\ReservationServiceInterface;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ReservationService implements ReservationServiceInterface
{
    public function getAllReservations(int $perPage = 15): LengthAwarePaginator
    {
        return Reservation::with(['table', 'creator'])
            ->orderBy('reserved_at', 'desc')
            ->paginate($perPage);
    }

    public function getUpcomingReservations(): Collection
    {
        return Reservation::with(['table'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('reserved_at', '>=', now()->subHours(2)) // include slightly past to allow seating
            ->orderBy('reserved_at', 'asc')
            ->get();
    }

    public function findReservation(int $id): Reservation
    {
        return Reservation::with(['table', 'creator'])->findOrFail($id);
    }

    public function createReservation(array $data): Reservation
    {
        $this->validateCapacityAndAvailability(
            $data['table_id'], 
            $data['party_size'], 
            $data['reserved_at'], 
            $data['duration_minutes']
        );

        $data['created_by'] = auth()->id();
        
        return Reservation::create($data);
    }

    public function updateReservation(int $id, array $data): Reservation
    {
        $reservation = Reservation::findOrFail($id);
        
        $this->validateCapacityAndAvailability(
            $data['table_id'], 
            $data['party_size'], 
            $data['reserved_at'], 
            $data['duration_minutes'],
            $id
        );

        $reservation->update($data);
        return $reservation;
    }

    public function deleteReservation(int $id): void
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
    }

    public function updateStatus(int $id, string $status): Reservation
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => $status]);
        
        // Update table status if seated or completed
        if ($status === 'seated') {
            $reservation->table->update(['status' => 'occupied']);
        } elseif (in_array($status, ['completed', 'cancelled', 'no_show'])) {
            $reservation->table->update(['status' => 'available']);
        }
        
        return $reservation;
    }

    public function getAvailableTablesFor(int $partySize, string $datetime, ?int $excludeReservationId = null): Collection
    {
        $requestedTime = Carbon::parse($datetime);
        
        // Find tables that can accommodate the party size
        $tables = RestaurantTable::where('capacity', '>=', $partySize)->get();
        
        return $tables->filter(function ($table) use ($requestedTime, $excludeReservationId) {
            return $this->isTableAvailable($table->id, $requestedTime, 60, $excludeReservationId);
        });
    }

    protected function validateCapacityAndAvailability($tableId, $partySize, $datetime, $duration, $excludeReservationId = null)
    {
        $table = RestaurantTable::findOrFail($tableId);
        
        if ($partySize > $table->capacity) {
            throw ValidationException::withMessages([
                'party_size' => ["The selected table has a maximum capacity of {$table->capacity}."]
            ]);
        }

        $requestedTime = Carbon::parse($datetime);
        if (!$this->isTableAvailable($tableId, $requestedTime, $duration, $excludeReservationId)) {
            throw ValidationException::withMessages([
                'reserved_at' => ['This table is already booked for the selected date and time.']
            ]);
        }
    }

    protected function isTableAvailable($tableId, Carbon $requestedTime, $duration, $excludeReservationId = null)
    {
        $requestedEnd = $requestedTime->copy()->addMinutes($duration);

        $overlappingReservations = Reservation::where('table_id', $tableId)
            ->whereNotIn('status', ['cancelled', 'no_show', 'completed'])
            ->when($excludeReservationId, function ($query, $id) {
                return $query->where('id', '!=', $id);
            })
            ->get();

        foreach ($overlappingReservations as $reservation) {
            $resStart = Carbon::parse($reservation->reserved_at);
            $resEnd = $resStart->copy()->addMinutes($reservation->duration_minutes);

            // Check if the requested time overlaps with the reservation time
            // (StartA < EndB) and (EndA > StartB)
            if ($requestedTime->lt($resEnd) && $requestedEnd->gt($resStart)) {
                return false;
            }
        }

        return true;
    }
}
