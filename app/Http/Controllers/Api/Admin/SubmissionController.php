<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Submission::with('entreprise')->latest('completed_at');

        if ($request->filled('entreprise_id')) {
            $query->where('entreprise_id', $request->integer('entreprise_id'));
        }

        if ($request->filled('is_eligible')) {
            $query->where('is_eligible', $request->boolean('is_eligible'));
        }

        $submissions = $query->paginate(50);

        return response()->json([
            'data' => SubmissionResource::collection($submissions->items()),
            'meta' => [
                'total'        => $submissions->total(),
                'per_page'     => $submissions->perPage(),
                'current_page' => $submissions->currentPage(),
                'last_page'    => $submissions->lastPage(),
            ],
        ]);
    }

    public function show(Submission $submission): SubmissionResource
    {
        return new SubmissionResource($submission->load('entreprise'));
    }
}
