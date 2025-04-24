<?php

namespace App\Services\External;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ExternalApiClient
{
    private $googlePlacesApiKey;
    private $googlePlaceApiBaseUrl = "https://places.googleapis.com/v1/places";

    public function __construct()
    {
        $this->googlePlacesApiKey = env("API_KEY", "");
    }

    public function fetchCompanyData(string $companyName, string $fieldMask): JsonResponse
    {
        $headers = [
            'Content-Type'     => 'application/json',
            'X-Goog-Api-Key'   => $this->googlePlacesApiKey,
            'X-Goog-FieldMask' => $fieldMask,
        ];

        $body = [
            'textQuery'    => $companyName,
            'languageCode' => 'ja',
            'regionCode'   => 'JP',
        ];

        $response = Http::withHeaders($headers)->post($this->googlePlaceApiBaseUrl . ":searchText", $body);

        if ($response->successful()) {
            return response()->json($response->json());
        }
        return response()->json(['error' => 'Failed to fetch'], 500);
    }

    public function fetchNearbyShopApi(): JsonResponse
    {
        $headers = [
            'Content-Type'     => 'application/json',
            'X-Goog-Api-Key'   => $this->googlePlacesApiKey,
            'X-Goog-FieldMask' => 'places.displayName,places.id',
        ];

        $body = [
            'includedTypes'    => "restaurant",
            "maxResultCount"   => 10,
            "locationRestriction" => [
                "circle" => [
                    "center" => [
                        "latitude" => 35.6784458,
                        "longitude" => 139.6354873
                    ],
                    "radius" => 500.0
                ],
            ],
            'languageCode' => 'ja',
            'regionCode'   => 'JP',
        ];

        $response = Http::withHeaders($headers)->post($this->googlePlaceApiBaseUrl . ":searchNearby", $body);

        if ($response->successful()) {
            return response()->json($response->json());
        }
        return response()->json(['error' => 'Failed to fetch'], 500);
    }

    public function fetchShopDetailsFromApi()
    {
        $headers = [
            'Content-Type'     => 'application/json',
            'X-Goog-Api-Key'   => $this->googlePlacesApiKey,
            'X-Goog-FieldMask' => 'displayName,currentOpeningHours,priceLevel,priceRange'
        ];

        $body = [
            'languageCode' => 'ja',
        ];

        $placeId = "ChIJ__8OSU_yGGARheQN-_kVpjs";

        $response = Http::withHeaders($headers)->get("{$this->googlePlaceApiBaseUrl}/{$placeId}", $body);

        if ($response->successful()) {
            return response()->json($response->json());
        }
        return response()->json(['error' => 'Failed to fetch'], 500);
    }
}
