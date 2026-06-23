<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\ReservationServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ReservationServiceInterface $reservationService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Reservation::class);
        $reservations = $this->reservationService->getAllReservations();
        return view('admin.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $this->authorize('create', Reservation::class);
        $tables = RestaurantTable::all();
        return view('admin.reservations.create', compact('tables'));
    }

    public function store(StoreReservationRequest $request)
    {
        $this->authorize('create', Reservation::class);
        $data = $request->validated();
        
        $this->reservationService->createReservation($data);

        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        $reservation->load(['table', 'creator', 'orders']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $tables = RestaurantTable::all();
        return view('admin.reservations.edit', compact('reservation', 'tables'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $data = $request->validated();

        $this->reservationService->updateReservation($reservation->id, $data);

        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);
        $this->reservationService->deleteReservation($reservation->id);
        
        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }
    
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $request->validate(['status' => 'required|in:pending,confirmed,seated,completed,cancelled,no_show']);
        
        $this->reservationService->updateStatus($reservation->id, $request->status);
        
        return back()->with('success', 'Reservation status updated successfully.');
    }
}
