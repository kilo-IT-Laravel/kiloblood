<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\BloodRequestDonor;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BloodRequestController extends Koobeni
{
    public function store()
    {
        try {
            $validated = $this->req->validate([
                'blood_type' => 'required|string',
                'name' => 'required|string',
                'location' => 'required|string',
                'quantity' => 'nullable|integer',
                'note' => 'nullable|string',
                'expired_at' => 'nullable|date',
                'doc' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($this->req->hasFile('doc')) {
                $validated['doc'] = $this->req->file('doc')->store('medical_records', 's3');
            }

            $data = BloodRequest::create([
                'donor_id' => Auth::id(),
                'blood_type' => $validated['blood_type'],
                'name' => $validated['name'],
                'location' => $validated['location'],
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? null,
                'expired_at' => $validated['expired_at'] ?? now()->addDays(7),
                'medical_records' => $validated['doc'],
                'status' => 'pending'
            ]);

            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function index()
    {
        try {
            $requests = $this->findAll->allWithPagination([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'relations' => [
                    'donors'
                ],
                'where' => [
                    ['donor_id', '!=', Auth::id()],
                    ['status', '=', 'pending'],
                    ['expired_at', '>', now()]
                ],
                'whereDoesntHave' => [
                    'donors' => function ($query) {
                        $query->where('requester_id', Auth::id())
                            ->whereNotNull('status');
                    }
                ],
                'select' => [
                    'id',
                    'donor_id',
                    'blood_type',
                    'name',
                    'location',
                    'quantity',
                    'note',
                    'status',
                    'created_at'
                ]
            ]);

            $requests->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'location' => $item->location,
                    'expired_at' => Carbon::parse($item->expired_at)->format('Y-m-d'),
                    'time' => $item->created_at->diffForHumans(),
                    'status' => $item->status,
                    'blood_type' => $item->blood_type
                ];
            });

            return $this->paginationDataResponse($requests);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function myRequests()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => BloodRequestDonor::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'relations' => [
                    'bloodRequest:id,blood_type,name,location,expired_at,status'
                ],
                'whereHas' => [
                    'bloodRequest' => function ($query) {
                        $query->where('donor_id', Auth::id());
                        // ->where('status', 'pending');
                    }
                ],
                'where' => [
                    ['status', '=', 'accepted'],
                ],
                'select' => [
                    'id',
                    'blood_request_id',
                    'requester_id',
                    'created_at'
                ]
            ]);
            $data->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->bloodRequest->name,
                    'location' => $item->bloodRequest->location,
                    'expired_at' => Carbon::parse($item->bloodRequest->expired_at)->format('Y-m-d'),
                    'time' => $item->created_at->diffForHumans(),
                    'status' => $item->bloodRequest->status,
                    'blood_type' => $item->bloodRequest->blood_type
                ];
            });

            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function donate(int $reqId)
    {
        try {
            $bloodRequest = BloodRequest::findOrFail($reqId);

            $recentDonation = BloodRequestDonor::where('requester_id', Auth::id())
                ->where('is_confirmed', true)
                ->where('created_at', '>', now()->subMonths(3))
                ->exists();

            if ($recentDonation) {
                return $this->Validation(null, 'You must wait 3 months between blood donations');
            }

            if ($bloodRequest->donor_id === Auth::id()) {
                return $this->Validation(null, 'You cannot donate to your own blood request');
            }

            $validated = $this->req->validate([
                'status' => 'required|in:accepted,rejected',
                'quantity' => 'required_if:status,accepted|integer|min:1'
            ]);

            $existingRequest = BloodRequestDonor::where('requester_id', Auth::id())
                ->where('blood_request_id', $bloodRequest->id)
                ->exists();

            if ($existingRequest) {
                return $this->Validation(null, 'You have already responded to this request');
            }

            $donation = DB::transaction(function () use ($validated, $bloodRequest) {
                return BloodRequestDonor::create([
                    'blood_request_id' => $bloodRequest->id,
                    'requester_id' => Auth::id(),
                    'quantity' => $validated['status'] === 'accepted' ? $validated['quantity'] : null,
                    'status' => $validated['status'],
                    'is_confirmed' => false
                ]);
            });

            return $this->dataResponse($donation);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function confirmDonor(int $donorId)
    {
        try {
            $bloodRequestDonor = BloodRequestDonor::findOrFail($donorId);

            if ($bloodRequestDonor->bloodRequest->donor_id !== Auth::id()) {
                return $this->Forbidden('Unauthorized');
            }

            $request = $bloodRequestDonor->bloodRequest;
            $confirmedQuantity = $request->donors()
                ->where('is_confirmed', true)
                ->sum('quantity');

            $newTotal = $confirmedQuantity + $bloodRequestDonor->quantity;

            // if ($newTotal > $request->quantity) {
            //     return $this->Validation(null, 'Confirming this donor would exceed requested quantity');
            // }

            DB::transaction(function () use ($bloodRequestDonor, $request, $newTotal) {
                $bloodRequestDonor->update([
                    'is_confirmed' => true
                ]);

                // if ($newTotal === $request->quantity) {
                //     $request->update([
                //         'status' => 'completed'
                //     ]);
                // }
                $request->update([
                    'status' => 'completed'
                ]);

                $this->notiService->useNoti(
                    $bloodRequestDonor->requester_id,
                    'confirm',
                    'Your recent donation has been successfully processed',
                    $bloodRequestDonor->id
                );
            });

            return $this->dataResponse($bloodRequestDonor->fresh());
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function cancel(int $reqId)
    {
        try {

            $bloodRequest = BloodRequest::findOrFail($reqId);

            if ($bloodRequest->donor_id !== Auth::id()) {
                return $this->Validation(null, 'Unauthorized');
            }

            if ($bloodRequest->donors()->where('is_confirmed', true)->exists()) {
                return $this->Validation(null, 'Cannot cancel request with confirmed donors');
            }

            DB::transaction(function () use ($bloodRequest) {
                $bloodRequest->update([
                    'status' => 'cancelled'
                ]);
            });

            return $this->dataResponse($bloodRequest->fresh());
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function searchForDonor()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => User::class,
                'sort' => 'latest',
                'search' => [
                    'name' => $this->req->search
                ],
                'where' => [
                    ['id', '!=', Auth::id()],
                    ['available_for_donation', '=', true]
                ],
                'select' => [
                    'id',
                    'name',
                    'avatar',
                    'location',
                    'blood_type'
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function myDonationHistory()
    {
        try {
            $data = $this->donationRequest('people_request');

            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function myRequestHistory()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'relations' => [
                    'donors' => function ($query) {
                        $query->whereNotNull('status')
                            ->with('donor');
                    }
                ],
                'where' => [
                    ['donor_id', '=', Auth::id()],
                ],
                'whereIn' => [
                    'status' => ['completed', 'cancelled']
                ],
                'select' => [
                    'id',
                    'blood_type',
                    'name',
                    'location',
                    'quantity',
                    'status',
                    'created_at',
                    'expired_at'
                ]
            ]);

            $data->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'location' => $item->location,
                    'expired_at' => Carbon::parse($item->expired_at)->format('Y-m-d'),
                    'time' => $item->created_at->diffForHumans(),
                    'status' => $item->status,
                    'blood_type' => $item->blood_type
                ];
            });

            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }












































    ////// dont know about donation request it either a request we sent people accepted or not or it is report about own action

    private function donationRequest(string $option)
    {
        switch ($option) {
            case 'people_request':
                $data = $this->findAll->allWithPagination([
                    'model' => BloodRequestDonor::class,
                    'sort' => 'latest',
                    'perPage' => $this->req->perPage,
                    'relations' => [
                        'bloodRequest:id,blood_type,name,location'
                    ],
                    'where' => [
                        ['requester_id', '=', Auth::id()],
                        ['status', '!=', null]
                    ],
                    'whereIn' => [
                        'status' => ['accepted', 'rejected']
                    ],
                    'select' => [
                        'id',
                        'blood_request_id',
                        'requester_id',
                        'status',
                        'created_at'
                    ]
                ]);
                $data->getCollection()->transform(function ($item) {
                    return [
                        'id' => $item->bloodRequest->id,
                        'name' => $item->bloodRequest->name,
                        'location' => $item->bloodRequest->location,
                        'expired_at' => Carbon::parse($item->bloodRequest->expired_at)->format('Y-m-d'),
                        'time' => $item->created_at->diffForHumans(),
                        'status' => $item->status,
                        'blood_type' => $item->bloodRequest->blood_type
                    ];
                });
                return $data;
                break;
            case 'my_request':
                $data = $this->findAll->allWithPagination([
                    'model' => BloodRequestDonor::class,
                    'sort' => 'latest',
                    'perPage' => $this->req->perPage,
                    'relations' => [
                        'bloodRequest' => function ($query) {
                            $query->where('donor_id', Auth::id())
                                ->select('id', 'blood_type', 'name', 'location', 'status', 'expired_at');
                        }
                    ],
                    'whereHas' => [
                        'bloodRequest' => function ($query) {
                            $query->where('donor_id', Auth::id());
                        }
                    ],
                    'whereIn' => [
                        'status' => ['accepted', 'rejected']
                    ],
                    'select' => [
                        'id',
                        'blood_request_id',
                        'requester_id',
                        'status',
                        'created_at'
                    ]
                ]);
                $data->getCollection()->transform(function ($item) {
                    return [
                        'id' => $item->bloodRequest->id,
                        'name' => $item->bloodRequest->name,
                        'location' => $item->bloodRequest->location,
                        'expired_at' => Carbon::parse($item->bloodRequest->expired_at)->format('Y-m-d'),
                        'time' => $item->created_at->diffForHumans(),
                        'status' => $item->status,
                        'blood_type' => $item->bloodRequest->blood_type
                    ];
                });
                return $data;
                break;
            default:
                return 'invalid option';
                break;
        }
    }
}
