<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Crawler\Interfaces\CrawlerInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CrawlerController extends Controller
{
    public function __construct(protected CrawlerInterface $crawler)
    {
    }

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

        if ($this->crawler->process($validatedData['site_url']) === false) {
            throw ValidationException::withMessages([
                'site_url' => 'Crawl was unsuccessful on this URL'
            ]);
        }

        return view('crawler', ['res' => $this->crawler->getResult()]);
    }
}
