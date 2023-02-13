<?php

namespace App\Services\Crawler;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CrawlerController extends Controller
{
    /**
     * Show Crawler page
     * 
     * @return View
     */
    public function index(): View
    {
        return view('crawler');
    }

    /**
     * Run crawler on an external web site
     * 
     * @param Request $request
     * @return View
     */
    public function process(Request $request): View
    {
        $validatedData = $request->validate(['site_url' => 'required|url']);
        $crawler = new Crawler($validatedData['site_url']);
        
        if ($crawler->process() === false) {
            throw ValidationException::withMessages(['site_url' => 'Crawl was unsuccessful on this URL']);
        }

        $stats = new Stats($crawler->pages, $crawler->getCount());

        return view('crawler', ['stats' => $stats]);
    }
}
