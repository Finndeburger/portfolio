<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    // Homepage
    public function index()
    {
        return view('pages.home');
    }

    // Results page
    public function results(Request $request)
    {
        $query = $request->input('q', '');
        $sites = Site::all();

        $primary = collect();
        $others = collect();
        $sponsored = null;

        if ($query !== '') {
            $q = strtolower($query);

            // Primary results
            $primary = $sites->filter(function (Site $site) use ($q) {
                return str_contains(strtolower($site->title), $q) || collect($site->tags)->contains(fn($tag) => str_contains(strtolower($tag), $q));
            })->values();

            $primarySlugs = $primary->pluck('slug')->all();

            // Sponsored results
            $sponsoredCandidates = $sites->filter(function (Site $site) use ($primarySlugs) {
                return $site->sponsored && ! in_array($site->slug, $primarySlugs);
            });

            if ($sponsoredCandidates->isNotEmpty()) {
                $sponsored = $sponsoredCandidates->random();
            }

            // Other results
            $usedSlugs = collect($primarySlugs);

            if ($sponsored) {
                $usedSlugs->push($sponsored->slug);
            }

            $others = $sites->filter(function (Site $site) use ($usedSlugs) {
                return ! $usedSlugs->contains($site->slug);
            })->shuffle()->values();
        }

        return view('pages.results', [
            'query' => $query,
            'sponsored' => $sponsored,
            'primary' => $primary,
            'others' => $others
        ]);
    }

    // Dynamic page for slugs
    public function show(string $slug) {
        $site = Site::where('slug', $slug)->firstOrFail();

        return view('pages.sites.show', [
            'site' => $site,
        ]);
    }

    // API endpoint
    public function apiIndex() {
        return response()->json(Site::all());
    }
}