<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreCvRequest;
use App\Models\Cv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    /**
     * GET /client/cv
     * Return the authenticated client's CV.
     */
    public function show(Request $request): JsonResponse
    {
        $cv = $request->user()->cv;

        if (!$cv) {
            return response()->json([
                'message'  => 'Aucun CV trouvé. Veuillez en créer un avant de postuler.',
                'has_cv'   => false,
            ], 404);
        }

        return response()->json([
            'has_cv' => true,
            'data'   => $cv,
        ]);
    }

    /**
     * POST /client/cv
     * Create or update (upsert) the client's CV.
     */
    public function store(StoreCvRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Handle CV file upload if base64 or path provided
        $cv = Cv::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated,
        );

        $created = $cv->wasRecentlyCreated;

        return response()->json([
            'message' => $created ? 'CV créé avec succès.' : 'CV mis à jour avec succès.',
            'data'    => $cv->fresh(),
        ], $created ? 201 : 200);
    }
}
