<?php

namespace App\Services\External;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ExternalApiClient
{
    private $googlePlacesApiKey;
    private $googleSearchTextBaseUrl = "https://places.googleapis.com/v1/places:searchText";
    private $googleNearbySearchbaseUrl = "https://places.googleapis.com/v1/places:searchNearby";

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

        $response = Http::withHeaders($headers)->post($this->googleSearchTextBaseUrl, $body);

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
            'X-Goog-FieldMask' => 'places.displayName',
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

        $response = Http::withHeaders($headers)->post($this->googleNearbySearchbaseUrl, $body);

        if ($response->successful()) {
            return response()->json($response->json());
        }
        return response()->json(['error' => 'Failed to fetch'], 500);
    }
}
