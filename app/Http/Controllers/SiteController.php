<?php

namespace App\Http\Controllers;

use App\Models\Coffee;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
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

    // Dynamic page for mini sites and subpages.
    // /sites/{slug}           => pages.sites.{slug}.index
    // /sites/{slug}/{page...} => pages.sites.{slug}.{page}
    public function show(string $slug, ?string $page = null)
    {
        $site = $this->getSites()->firstWhere('slug', $slug);

        abort_if(! $site, 404);

        $page = trim((string) $page, '/');
        $page = $page === '' ? 'index' : $page;

        // Keep view resolution safe and predictable.
        abort_unless((bool) preg_match('/^[a-z0-9\-\/]+$/i', $page), 404);

        $view = sprintf('pages.sites.%s.%s', $slug, str_replace('/', '.', $page));

        if (View::exists($view)) {
            $data = [
                'site' => (object) $site,
                'currentPage' => $page,
            ];

            if ($slug === 'bergcoffee') {
                $data['coffees'] = Coffee::all();
            }

            return view($view, $data);
        }

        // For mini sites without a custom folder/index, use the generic template.
        if ($page === 'index') {
            return view('pages.sites.show', [
                'site' => (object) $site,
                'currentPage' => $page,
            ]);
        }

        abort(404);
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
            'database_connection' => $site['database_connection'] ?? null,
            'database_table' => $site['database_table'] ?? null,
            'database_meta' => $site['database_meta'] ?? null,
        ];
    }
}