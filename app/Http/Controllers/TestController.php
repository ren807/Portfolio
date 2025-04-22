<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\External\ExternalApiClient;

class TestController extends Controller
{
    protected $apiClient;

    public function __construct(ExternalApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function index(): JsonResponse
    {
        $fieldMask   = 'places.id,places.displayName,places.location,places.formattedAddress';
        $companyData = $this->apiClient->fetchCompanyData('台ずし西永', $fieldMask);

        $aaa = $this->apiClient->fetchNearbyShopApi();
        return $aaa;

        if ($companyData->getStatusCode() === 200) {
            dd($companyData->getData());
        } else {
            dd($companyData->getData());
        }
    }
}
