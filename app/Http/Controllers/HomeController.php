<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use Inertia\Inertia;

class HomeController extends Controller
{
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function index()
    {
        return Inertia::render('Home', [
            'categories' => $this->homeService->getCategories(),
            'latestWomenBags' => $this->homeService->getLatestWomenBags(),
        ]);
    }

    public function search()
    {
        return Inertia::render('Search', [
            'categories' => $this->homeService->getCategories(),
            'womenSeasonalBags' => $this->homeService->getWomenSeasonalBags(),
            'menSeasonalBags' => $this->homeService->getMenSeasonalBags(),
        ]);
    }
}
