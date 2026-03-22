<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

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
        $sites = $this->getSites();

        $primary = collect();
        $others = collect();
        $sponsored = null;

        if ($query !== '') {
            $q = strtolower($query);

            // Primary results
            $primary = $sites->filter(function (array $site) use ($q) {
                return str_contains(strtolower($site['title']), $q)
                    || collect($site['tags'])->contains(fn(string $tag) => str_contains(strtolower($tag), $q));
            })->values();

            $primarySlugs = $primary->pluck('slug')->all();

            // Sponsored results
            $sponsoredCandidates = $sites->filter(function (array $site) use ($primarySlugs) {
                return $site['sponsored'] && ! in_array($site['slug'], $primarySlugs, true);
            });

            if ($sponsoredCandidates->isNotEmpty()) {
                $sponsored = $sponsoredCandidates->random();
            }

            // Other results
            $usedSlugs = collect($primarySlugs);

            if ($sponsored) {
                $usedSlugs->push($sponsored['slug']);
            }

            $others = $sites->filter(function (array $site) use ($usedSlugs) {
                return ! $usedSlugs->contains($site['slug']);
            })->shuffle()->values();
        }

        return view('pages.results', [
            'query' => $query,
            'sponsored' => $sponsored ? (object) $sponsored : null,
            'primary' => $primary->map(fn(array $site) => (object) $site),
            'others' => $others->map(fn(array $site) => (object) $site),
        ]);
    }

    // Dynamic page for slugs
    public function show(string $slug)
    {
        $site = $this->getSites()->firstWhere('slug', $slug);

        abort_if(! $site, 404);

        return view('pages.sites.show', [
            'site' => (object) $site,
        ]);
    }

    // API endpoint
    public function apiIndex()
    {
        return response()->json($this->getSites()->values()->all());
    }

    private function getSites(): Collection
    {
        try {
            return Site::query()
                ->get()
                ->map(fn(Site $site) => $this->normalizeSite($site->toArray()));
        } catch (Throwable) {
            return collect(config('sites', []))
                ->map(fn(array $site) => $this->normalizeSite($site));
        }
    }

    private function normalizeSite(array $site): array
    {
        return [
            'title' => (string) ($site['title'] ?? ''),
            'slug' => (string) ($site['slug'] ?? ''),
            'dummy_url' => (string) ($site['dummy_url'] ?? ''),
            'description' => (string) ($site['description'] ?? ''),
            'tags' => collect($site['tags'] ?? [])->map(fn(mixed $tag) => (string) $tag)->values()->all(),
            'sponsored' => (bool) ($site['sponsored'] ?? false),
        ];
    }
}