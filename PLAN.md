# LookAtMe — Full Build Plan

> A step-by-step mega prompt to rebuild the complete "LookAtMe" search engine portfolio site in Laravel + Alpine.js.
> Follow each step in order. Each step says exactly what to create or change, and why.

---

## What we're building

A Google-like search engine called **LookAtMe** that serves as a portfolio site. The user lands on a search page, types a query, gets real-time suggestions as they type, and sees search results that link to "sites" (portfolio pages rendered inside a fake browser window).

### Key features
- **Real-time typeahead search** — suggestions appear as you type, powered by Alpine.js
- **Smart character blocking** — you can only type characters that still match at least one site (like Google's autocomplete, prevents dead-end queries)
- **Keyboard navigation** — Arrow Up/Down cycle through suggestions, Enter navigates
- **Search result ordering** — results are categorized: Sponsored (random ad-like result), Primary (actual matches), Others (remaining sites, shuffled)
- **Fake browser chrome** — site pages render inside a macOS-style browser window with traffic light dots, back button, and URL bar showing a fake URL
- **Dynamic site pages** — sites are stored in a database; adding a new site automatically makes it searchable and viewable

### Tech stack
- **Laravel 13** — PHP framework (routing, controllers, Eloquent ORM, Blade templates, migrations, seeders)
- **SQLite** — lightweight database (already configured as default)
- **Alpine.js** — lightweight JS framework for search interactivity (loaded via Vite)
- **Tailwind CSS v4** — utility-first CSS (already configured via Vite)
- **Vite** — asset bundler (already configured)

---

## Current state of the project

The Laravel project already has:
- Two Blade layouts: `layouts/app.blade.php` (default) and `layouts/browser.blade.php` (fake browser chrome)
- Four page views: `pages/home.blade.php`, `pages/results.blade.php`, `pages/sites/about.blade.php`, `pages/sites/test.blade.php`
- Four images in `public/assets/`: `logo.png`, `Barcelona.jpeg`, `FinnHarmensLogo.jpeg`, `FinnHarmensLogoBlack.jpeg`
- Simple closure routes in `routes/web.php` (no controller, no database, no search logic)
- Vite + Tailwind CSS configured and building
- `resources/js/app.js` only imports bootstrap (no Alpine.js, no app logic)
- No database tables, no models, no controllers, no seeders beyond the default User ones

---

## Phase 1: Database & Site Model

### Step 1 — Create the `sites` migration

**What:** A migration file that creates the `sites` table in the database.

**Why:** Migrations are Laravel's way of defining database tables in code. Instead of writing raw SQL, you describe your table structure in PHP. This means your database schema is version-controlled and can be recreated on any machine with `php artisan migrate`.

**Command to generate it:**
```bash
php artisan make:migration create_sites_table
```

This creates a file at `database/migrations/YYYY_MM_DD_HHMMSS_create_sites_table.php`.

**Edit the generated file's `up()` method to define these columns:**

```php
public function up(): void
{
    Schema::create('sites', function (Blueprint $table) {
        $table->id();                          // Auto-incrementing integer primary key
        $table->string('title');               // "About", "Test", etc.
        $table->string('slug')->unique();      // "about", "test" — used in URLs, must be unique
        $table->string('dummy_url');           // Fake URL shown in the browser bar: "https://finnharmens.com/about"
        $table->text('description');           // Search result snippet text
        $table->json('tags');                  // JSON array of search keywords: ["portfolio", "about", "finn"]
        $table->boolean('sponsored')->default(false);  // Whether this site can appear as a "Sponsored" result
        $table->timestamps();                  // created_at and updated_at columns (auto-managed by Laravel)
    });
}
```

**Key Laravel concepts:**
- `Schema::create('sites', ...)` — creates a new table called `sites`
- `$table->string('slug')->unique()` — adds a unique index so no two sites can have the same slug
- `$table->json('tags')` — stores a JSON array; Laravel can cast this to/from a PHP array automatically
- `$table->boolean('sponsored')->default(false)` — defaults to `false` if not explicitly set
- `$table->timestamps()` — adds `created_at` and `updated_at` columns

---

### Step 2 — Create the `Site` Eloquent model

**What:** A PHP class that represents a row in the `sites` table.

**Why:** Eloquent is Laravel's ORM (Object-Relational Mapping). Instead of writing SQL queries, you work with PHP objects. `Site::all()` fetches all rows. `Site::where('slug', 'about')->first()` finds one row. The model class tells Laravel how to read and write data for this table.

**Command to generate it:**
```bash
php artisan make:model Site
```

This creates `app/Models/Site.php`.

**Edit the file to add casts:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected function casts(): array
    {
        return [
            'tags' => 'array',          // Automatically JSON-decode the tags column into a PHP array
            'sponsored' => 'boolean',   // Automatically cast to true/false instead of 1/0
        ];
    }
}
```

**Key Laravel concepts:**
- By convention, the `Site` model maps to the `sites` table (lowercase plural of the class name)
- `casts()` tells Laravel to automatically convert columns when reading/writing. Without this, `tags` would be a raw JSON string and `sponsored` would be `0` or `1`
- You don't need to list columns — Eloquent reads them from the database schema automatically

---

### Step 3 — Create the `SiteSeeder`

**What:** A seeder class that inserts initial site data into the database.

**Why:** Seeders populate your database with starting data. Instead of manually inserting rows, you define them in code. Run `php artisan db:seed` and your data appears. This is especially useful when developing — you can wipe and reseed the database at any time with `php artisan migrate:fresh --seed`.

**Command to generate it:**
```bash
php artisan make:seeder SiteSeeder
```

This creates `database/seeders/SiteSeeder.php`.

**Edit the file:**

```php
<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        Site::create([
            'title' => 'About',
            'slug' => 'about',
            'dummy_url' => 'https://finnharmens.com/about',
            'description' => 'Original portfolio site.',
            'tags' => ['portfolio', 'about', 'about me', 'finn', 'info'],
            'sponsored' => false,
        ]);

        Site::create([
            'title' => 'Test',
            'slug' => 'test',
            'dummy_url' => 'https://finnharmens.dev/test',
            'description' => 'Test site for testing routing.',
            'tags' => ['test', 'development', 'code', 'placeholder', 'temporary'],
            'sponsored' => false,
        ]);
    }
}
```

**Then wire it into `database/seeders/DatabaseSeeder.php`:**

In the `run()` method, add:
```php
$this->call([
    SiteSeeder::class,
]);
```

You can keep or remove the existing `User::factory()` call — it's up to you.

**Key Laravel concepts:**
- `Site::create([...])` inserts a new row. Because we set up the `tags` cast, passing a PHP array automatically stores it as JSON.
- `$this->call([SiteSeeder::class])` tells the main seeder to also run `SiteSeeder`

---

### Step 4 — Run migrations and seed

**Commands:**
```bash
php artisan migrate:fresh --seed
```

This drops all tables, recreates them from migrations, then runs all seeders.

**Verify it worked:**
```bash
php artisan tinker
```
Then in tinker:
```php
App\Models\Site::all();
```

You should see 2 Site records with all the correct data. Press `Ctrl+C` to exit tinker.

**What `migrate:fresh --seed` does:**
1. Drops all existing tables (fresh start)
2. Runs every migration in `database/migrations/` in order (creates `users`, `cache`, `jobs`, and `sites` tables)
3. Runs `DatabaseSeeder`, which calls `SiteSeeder`

---

## Phase 2: Controller & Routes

### Step 5 — Create the `SiteController`

**What:** A controller class that handles HTTP requests and returns responses.

**Why:** Right now, routes use inline closures (anonymous functions). That's fine for simple cases, but as logic grows (database queries, search ordering), it's better to move that logic into a controller. Each method in the controller handles one route.

**Command to generate it:**
```bash
php artisan make:controller SiteController
```

This creates `app/Http/Controllers/SiteController.php`.

**Edit the file with these methods:**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Home page — the search landing page.
     */
    public function index()
    {
        return view('pages.home');
    }

    /**
     * Search results page.
     * Reads ?q= from the URL, queries the database, and orders results
     * into sponsored / primary / others — just like a real search engine.
     */
    public function results(Request $request)
    {
        $query = $request->input('q', '');
        $sites = Site::all();

        $primary = collect();
        $others = collect();
        $sponsored = null;

        if ($query !== '') {
            $q = strtolower($query);

            // Primary results: sites where the title or any tag matches the query
            $primary = $sites->filter(function (Site $site) use ($q) {
                return str_contains(strtolower($site->title), $q)
                    || collect($site->tags)->contains(fn ($tag) => str_contains(strtolower($tag), $q));
            })->values();

            $primarySlugs = $primary->pluck('slug')->all();

            // Sponsored: pick one random site marked as sponsored that isn't already a primary result
            $sponsoredCandidates = $sites->filter(function (Site $site) use ($primarySlugs) {
                return $site->sponsored && ! in_array($site->slug, $primarySlugs);
            });

            if ($sponsoredCandidates->isNotEmpty()) {
                $sponsored = $sponsoredCandidates->random();
            }

            // Others: everything not in primary or sponsored, shuffled randomly
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
            'others' => $others,
        ]);
    }

    /**
     * Dynamic site page — renders a site inside the browser chrome layout.
     * If the slug doesn't exist in the database, returns 404.
     */
    public function show(string $slug)
    {
        $site = Site::where('slug', $slug)->firstOrFail();

        return view('pages.sites.show', [
            'site' => $site,
        ]);
    }

    /**
     * API endpoint — returns all sites as JSON.
     * Used by Alpine.js on the client side to power search suggestions.
     */
    public function apiIndex()
    {
        return response()->json(Site::all());
    }
}
```

**Key Laravel concepts:**
- `Request $request` — Laravel automatically injects the current HTTP request. `$request->input('q', '')` reads the `?q=` query parameter, defaulting to empty string.
- `Site::all()` — fetches every row from the `sites` table as a Collection (Laravel's enhanced array).
- `->filter(...)` — Collection method that keeps only items matching the callback (like JavaScript's `.filter()`).
- `->pluck('slug')` — extracts just the `slug` values from a collection.
- `->shuffle()` — randomly reorders items.
- `->values()` — resets array keys to 0, 1, 2... (prevents gaps after filtering).
- `Site::where('slug', $slug)->firstOrFail()` — finds the first Site matching the slug, or automatically throws a 404 error if not found.
- `response()->json(...)` — returns a JSON response instead of an HTML view.
- The search ordering logic mirrors the original Nuxt `getOrderedResults()`: primary matches first, then a random sponsored result, then everything else shuffled.

---

### Step 6 — Update routes/web.php

**What:** Replace the closure-based routes with controller routes and add new routes.

**Why:** Instead of inline functions, we point routes to controller methods. We also add a dynamic `/sites/{slug}` route for any site in the database, and an `/api/sites` endpoint that returns JSON for Alpine.js.

**Replace the entire contents of `routes/web.php` with:**

```php
<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', [SiteController::class, 'index']);

// Search results
Route::get('/results', [SiteController::class, 'results']);

// API endpoint for Alpine.js — returns all sites as JSON
Route::get('/api/sites', [SiteController::class, 'apiIndex']);

// Custom site pages (defined before the dynamic {slug} route so they take priority)
Route::get('/sites/about', fn () => view('pages.sites.about'));
Route::get('/sites/test', fn () => view('pages.sites.test'));

// Dynamic site page — catches any /sites/whatever that isn't about or test
Route::get('/sites/{slug}', [SiteController::class, 'show']);
```

**Key Laravel concepts:**
- `[SiteController::class, 'index']` — tells Laravel to call the `index()` method on `SiteController` when this route is hit.
- Route order matters: `/sites/about` and `/sites/test` are defined BEFORE `/sites/{slug}`. If `{slug}` came first, it would catch "about" and "test" and try to render the generic show page instead of the custom ones.
- `/sites/{slug}` — the `{slug}` part is a route parameter. Whatever the user puts in the URL (e.g., `/sites/portfolio`) gets passed as the `$slug` argument to `show()`.
- `/api/sites` returns JSON. This isn't a "real" API with authentication — it's a simple endpoint that Alpine.js fetches to get the list of sites for client-side search.

---

### Step 7 — Create pages/sites/show.blade.php

**What:** A generic Blade view for dynamically rendered site pages.

**Why:** When someone visits `/sites/portfolio` (a site that doesn't have its own custom Blade file), we need a generic template that shows the site's title and description inside the fake browser chrome.

**Create `resources/views/pages/sites/show.blade.php`:**

```blade
@extends('layouts.browser')

@section('title', $site->title)
@section('browser-url', $site->dummy_url)

@section('content')
<div class="p-10">
    <h1 class="text-3xl font-bold mb-4">{{ $site->title }}</h1>
    <p>{{ $site->description }}</p>
</div>
@endsection
```

**Key Laravel concepts:**
- `$site` is the Site model instance passed from the controller: `return view('pages.sites.show', ['site' => $site])`
- `$site->title` accesses the `title` column. Eloquent models let you access columns as properties.
- `$site->dummy_url` is shown in the browser chrome URL bar via `@section('browser-url', ...)`. The browser layout yields this value.
- `@extends('layouts.browser')` means this page renders inside the fake browser window (traffic light dots, URL bar, etc.)

---

### Step 7b — Create partials/result-card.blade.php

**What:** A reusable Blade partial for a single search result card.

**Why:** The results page shows multiple result cards (sponsored, primary, others). Instead of duplicating the HTML for each, we extract it into a partial and `@include` it. This is Blade's version of components.

**Create `resources/views/partials/result-card.blade.php`:**

```blade
<div class="mb-6">
    @if($isSponsored ?? false)
        <div class="text-xs text-[#70757a] mb-1">Sponsored</div>
    @endif
    <p class="text-sm text-[#202124] truncate">{{ $site->dummy_url }}</p>
    <a href="/sites/{{ $site->slug }}" class="text-xl text-[#1a0dab] hover:underline font-medium">
        {{ $site->title }}
    </a>
    <p class="text-sm text-[#4d5156] mt-1">{{ $site->description }}</p>
</div>
```

**Key Laravel concepts:**
- `@include('partials.result-card', ['site' => $site, 'isSponsored' => true])` — includes this file and passes variables to it
- `$isSponsored ?? false` — the null coalescing operator. If `$isSponsored` isn't passed, it defaults to `false`
- This mirrors the original Nuxt `ResultCard.vue` component: dummyUrl on top, title as a blue link, description below, optional "Sponsored" label

---

## Phase 3: Alpine.js Search Interactivity

### Step 8 — Install Alpine.js

**What:** Add Alpine.js as a dependency, import it in app.js, and add a CSS utility.

**Why:** Alpine.js is a lightweight JavaScript framework (like a simpler Vue.js) that adds interactivity directly in your HTML using directives like `x-data`, `x-model`, `x-show`. It's perfect for features like our typeahead search — no build step required beyond the Vite bundle we already have.

**Command:**
```bash
npm install alpinejs
```

**Edit `resources/js/app.js` — replace the entire file with:**

```js
import './bootstrap';
import Alpine from 'alpinejs';

// Register the searchApp component (used on home and results pages)
Alpine.data('searchApp', () => ({
    query: '',
    sites: [],
    suggestions: [],
    activeIndex: -1,

    /**
     * Called automatically when Alpine initializes the component.
     * Fetches all sites from the Laravel API endpoint so we can
     * do search matching entirely on the client side.
     */
    init() {
        fetch('/api/sites')
            .then(res => res.json())
            .then(data => { this.sites = data; });
    },

    /**
     * Check if a site matches a query string.
     * Matches against the title and all tags (case-insensitive).
     * This mirrors the original Nuxt matchesSite() function.
     */
    matchesSite(site, q) {
        const query = q.toLowerCase();
        return site.title.toLowerCase().includes(query)
            || site.tags.some(tag => tag.toLowerCase().includes(query));
    },

    /**
     * Check if the user is allowed to type the next character.
     * Returns true only if appending the character to the current query
     * would still match at least one site. This prevents dead-end typing —
     * if no site has "xyz" in its title or tags, you can't type "xyz".
     *
     * This is the "magic" of the original search — it feels like Google's
     * autocomplete because you can only type valid queries.
     */
    canType(nextChar) {
        const candidate = (this.query + nextChar).toLowerCase();
        if (candidate.length === 0) return true;
        return this.sites.some(site => this.matchesSite(site, candidate));
    },

    /**
     * Update the suggestions dropdown based on the current query.
     * Filters all sites to only those matching. Resets keyboard
     * navigation position whenever the query changes.
     */
    updateSuggestions() {
        this.activeIndex = -1;
        if (!this.query) {
            this.suggestions = [];
            return;
        }
        this.suggestions = this.sites.filter(site => this.matchesSite(site, this.query));
    },

    /**
     * Handles all keyboard events on the search input.
     * - Enter: navigate to results (using active suggestion or raw query)
     * - ArrowDown: move highlight down through suggestions (wraps around)
     * - ArrowUp: move highlight up through suggestions (wraps around)
     * - Regular character: block if canType() returns false
     * - Control keys (Backspace, Delete, etc.): always allowed
     */
    handleKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let searchQuery = this.query.trim();
            if (this.activeIndex >= 0 && this.activeIndex < this.suggestions.length) {
                searchQuery = this.suggestions[this.activeIndex].title;
            }
            if (searchQuery) {
                window.location.href = '/results?q=' + encodeURIComponent(searchQuery);
            }
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (this.suggestions.length > 0) {
                this.activeIndex = (this.activeIndex + 1) % this.suggestions.length;
            }
            return;
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (this.suggestions.length > 0) {
                this.activeIndex = this.activeIndex <= 0
                    ? this.suggestions.length - 1
                    : this.activeIndex - 1;
            }
            return;
        }

        // Allow control keys (Backspace, Delete, Arrow keys, etc.)
        if (e.key.length > 1) return;

        // Block the character if no site would match
        if (!this.canType(e.key)) {
            e.preventDefault();
        }
    },

    /**
     * Called when a user clicks on a suggestion in the dropdown.
     */
    selectSuggestion(site) {
        window.location.href = '/results?q=' + encodeURIComponent(site.title);
    },
}));

Alpine.start();
```

**Add the `x-cloak` CSS rule to `resources/css/app.css` — add this line at the bottom:**

```css
[x-cloak] { display: none !important; }
```

**Key concepts:**
- `Alpine.data('searchApp', () => ({...}))` registers a reusable component. Any element with `x-data="searchApp()"` gets all these properties and methods.
- `init()` is a special Alpine lifecycle hook — called automatically when the component initializes.
- `fetch('/api/sites')` calls our Laravel API endpoint from Step 6 to get the site data.
- `x-cloak` is an Alpine attribute that hides elements until Alpine has initialized. The CSS rule ensures elements with `x-cloak` are hidden during page load (prevents flash of unstyled content).
- All the search logic (matching, canType, suggestions) runs entirely in the browser. The `/api/sites` endpoint is only called once on page load.

---

### Step 9 — Update home.blade.php (wire up Alpine.js search)

**What:** Replace the plain HTML form with an Alpine.js-powered interactive search.

**Why:** The plain form only submits on Enter and reloads the page. With Alpine.js, we get:
- Real-time suggestions as you type
- Character blocking (can't type queries that match nothing)
- Keyboard navigation through suggestions
- Clicking a suggestion navigates immediately

**Replace the search form section in `resources/views/pages/home.blade.php`.**

Find this block (the plain `<form>` around line 48):
```blade
{{-- Search — plain form that submits to /results --}}
<form action="/results" method="GET" class="relative w-[500px]">
    ...entire form...
</form>
```

Replace it with:
```blade
{{-- Search — Alpine.js powered search with suggestions --}}
<div x-data="searchApp()" class="relative w-[500px]">
    <div
        class="flex items-center rounded-xl h-[60px] bg-[#d9d9d9] border-2 border-[#a6a6a6]"
        :class="{ 'rounded-b-none': suggestions.length > 0 }"
    >
        <svg class="absolute left-4" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
            <g fill="none" stroke="#a6a6a6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="m21 21l-4.34-4.34"/>
                <circle cx="11" cy="11" r="8"/>
            </g>
        </svg>
        <input
            x-model="query"
            @keydown="handleKeydown($event)"
            @input="updateSuggestions()"
            type="text"
            placeholder="Search or type URL"
            class="w-full h-full pl-12 pr-4 text-lg rounded-xl focus:outline-none bg-transparent text-gray-800"
            style="line-height: 60px;"
        />
    </div>

    {{-- Suggestions dropdown --}}
    <div
        x-show="suggestions.length > 0"
        x-cloak
        class="absolute top-full left-0 w-full bg-white rounded-b-xl border border-t-0 border-[#a6a6a6] shadow-lg z-50 overflow-hidden"
    >
        <template x-for="(site, i) in suggestions" :key="site.slug">
            <button
                @click="selectSuggestion(site)"
                @mouseenter="activeIndex = i"
                class="flex items-center gap-3 w-full px-4 py-2.5 text-left hover:bg-[#f0f0f0] transition-colors"
                :class="{ 'bg-[#f0f0f0]': i === activeIndex }"
            >
                <svg class="w-4 h-4 text-[#a6a6a6] shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="m21 21l-4.34-4.34"/>
                        <circle cx="11" cy="11" r="8"/>
                    </g>
                </svg>
                <span class="text-gray-800 truncate" x-text="site.title.toLowerCase()"></span>
            </button>
        </template>
    </div>
</div>
```

**Key Alpine.js directives explained:**
- `x-data="searchApp()"` — activates the searchApp component on this element. All child elements can use its properties and methods.
- `x-model="query"` — two-way binding between the input value and the `query` property. Typing updates `query`; programmatically changing `query` updates the input.
- `@keydown="handleKeydown($event)"` — calls `handleKeydown()` on every key press, passing the browser event object.
- `@input="updateSuggestions()"` — calls `updateSuggestions()` after the input value changes (fires after `x-model` updates `query`).
- `x-show="suggestions.length > 0"` — shows/hides the dropdown based on whether there are suggestions.
- `x-cloak` — hides this element until Alpine initializes (prevents flash of the dropdown on page load).
- `x-for="(site, i) in suggestions"` — loops over suggestions, creating a button for each.
- `:class="{ 'bg-[#f0f0f0]': i === activeIndex }"` — dynamically adds a background class when this suggestion is highlighted (via keyboard or mouse).
- `@click="selectSuggestion(site)"` — navigates to results when a suggestion is clicked.
- `@mouseenter="activeIndex = i"` — highlights the suggestion when the mouse hovers over it.
- `x-text="site.title.toLowerCase()"` — sets the text content dynamically.

---

### Step 10 — Update results.blade.php (server-rendered results + Alpine search bar)

**What:** Replace the hardcoded results with server-rendered dynamic results from the controller, and add the Alpine search bar so you can search again from the results page.

**Why:** The controller now passes `$sponsored`, `$primary`, and `$others` to the view (from Step 5). We use `@include('partials.result-card')` to render each result (from Step 7b). The search bar gets Alpine.js so it works the same as the home page.

**Replace the entire contents of `resources/views/pages/results.blade.php` with:**

```blade
@extends('layouts.app')

@section('title', 'LookAtMe - Results')

@section('content')
<div class="px-10 py-8 max-w-2xl">
    {{-- Search bar with Alpine.js --}}
    <div class="mb-8" x-data="searchApp()" x-init="query = '{{ addslashes($query) }}'">
        <div class="relative w-[500px]">
            <div
                class="flex items-center rounded-xl h-[60px] bg-[#d9d9d9] border-2 border-[#a6a6a6]"
                :class="{ 'rounded-b-none': suggestions.length > 0 }"
            >
                <svg class="absolute left-4" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                    <g fill="none" stroke="#a6a6a6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="m21 21l-4.34-4.34"/>
                        <circle cx="11" cy="11" r="8"/>
                    </g>
                </svg>
                <input
                    x-model="query"
                    @keydown="handleKeydown($event)"
                    @input="updateSuggestions()"
                    type="text"
                    placeholder="Search..."
                    class="w-full h-full pl-12 pr-4 text-lg rounded-xl focus:outline-none bg-transparent text-gray-800"
                    style="line-height: 60px;"
                />
            </div>

            {{-- Suggestions dropdown --}}
            <div
                x-show="suggestions.length > 0"
                x-cloak
                class="absolute top-full left-0 w-full bg-white rounded-b-xl border border-t-0 border-[#a6a6a6] shadow-lg z-50 overflow-hidden"
            >
                <template x-for="(site, i) in suggestions" :key="site.slug">
                    <button
                        @click="selectSuggestion(site)"
                        @mouseenter="activeIndex = i"
                        class="flex items-center gap-3 w-full px-4 py-2.5 text-left hover:bg-[#f0f0f0] transition-colors"
                        :class="{ 'bg-[#f0f0f0]': i === activeIndex }"
                    >
                        <svg class="w-4 h-4 text-[#a6a6a6] shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                <path d="m21 21l-4.34-4.34"/>
                                <circle cx="11" cy="11" r="8"/>
                            </g>
                        </svg>
                        <span class="text-gray-800 truncate" x-text="site.title.toLowerCase()"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Results --}}
    @if($query)
        @if($primary->isEmpty() && !$sponsored)
            <p class="text-[#70757a]">No results found for "{{ $query }}".</p>
        @else
            @if($sponsored)
                @include('partials.result-card', ['site' => $sponsored, 'isSponsored' => true])
            @endif

            @foreach($primary as $site)
                @include('partials.result-card', ['site' => $site])
            @endforeach

            @foreach($others as $site)
                @include('partials.result-card', ['site' => $site])
            @endforeach
        @endif
    @else
        <p class="text-[#70757a]">Type something to search.</p>
    @endif
</div>
@endsection
```

**Key concepts:**
- `x-init="query = '{{ addslashes($query) }}'"` — pre-fills the Alpine `query` property with the server-side `$query` value. `addslashes()` escapes quotes so the JS string doesn't break.
- `$primary->isEmpty()` — Collection method that checks if there are zero results.
- `@include('partials.result-card', ['site' => $site, 'isSponsored' => true])` — renders the result card partial, passing the site and a sponsored flag.
- `@foreach($primary as $site)` — loops over each primary result.
- The results are rendered SERVER-SIDE by PHP (not by Alpine.js). Only the search bar at the top uses Alpine.js for typeahead. This is a good pattern: use server rendering for the initial page, client-side JS only where interactivity is needed.

---

## Phase 4: Polish & Verify

### Step 11 — Rebuild assets

```bash
npm run build
```

This bundles Alpine.js into the built JavaScript file. Verify there are no build errors.

### Step 12 — Run migrations (if not already done)

```bash
php artisan migrate:fresh --seed
```

### Step 13 — Verify routes

```bash
php artisan route:list
```

You should see these routes:
| Method | URI | Action |
|--------|-----|--------|
| GET | / | SiteController@index |
| GET | /results | SiteController@results |
| GET | /api/sites | SiteController@apiIndex |
| GET | /sites/about | Closure |
| GET | /sites/test | Closure |
| GET | /sites/{slug} | SiteController@show |

### Step 14 — Test everything

Start the dev server:
```bash
php artisan serve
```

**Test checklist:**
1. **Home page** (`/`) — loads with logo, search bar, quick action buttons
2. **Type "ab"** — "About" suggestion should appear in the dropdown
3. **Type "xyz"** — characters should be blocked (nothing types)
4. **Arrow keys** — pressing Down/Up highlights suggestions
5. **Enter on suggestion** — navigates to `/results?q=About`
6. **Results page** — shows "About" as a primary result with its dummyUrl and description
7. **Click a result** — navigates to `/sites/about`, rendered inside the browser chrome
8. **Back button** — in the browser chrome, goes back to results
9. **API endpoint** (`/api/sites`) — returns JSON array with 2 site objects
10. **404 test** (`/sites/nonexistent`) — shows Laravel's 404 page

---

## How to add a new site later

Once everything is working, adding a new "site" to the search engine is simple:

1. **Add a row to the database.** Either create a new seeder entry and re-seed, or use Tinker:
   ```bash
   php artisan tinker
   ```
   ```php
   App\Models\Site::create([
       'title' => 'Projects',
       'slug' => 'projects',
       'dummy_url' => 'https://finnharmens.com/projects',
       'description' => 'All my projects and work.',
       'tags' => ['projects', 'work', 'portfolio', 'code'],
       'sponsored' => false,
   ]);
   ```

2. **Optionally create a custom Blade view.** If you want `/sites/projects` to have a custom design (like the About page), create `resources/views/pages/sites/projects.blade.php` and add a route for it in `web.php` BEFORE the `{slug}` route:
   ```php
   Route::get('/sites/projects', fn () => view('pages.sites.projects'));
   ```
   If you don't create a custom view, the generic `show.blade.php` template will be used automatically.

3. **That's it.** The site is now searchable from the home page, appears in results, and has its own page.

---

## File summary

Files to **create** (6):
- `database/migrations/YYYY_MM_DD_create_sites_table.php` — database schema
- `app/Models/Site.php` — Eloquent model
- `database/seeders/SiteSeeder.php` — initial data
- `app/Http/Controllers/SiteController.php` — request handlers
- `resources/views/pages/sites/show.blade.php` — generic site page
- `resources/views/partials/result-card.blade.php` — reusable result card

Files to **modify** (4):
- `database/seeders/DatabaseSeeder.php` — call SiteSeeder
- `routes/web.php` — controller routes + API + dynamic slug
- `resources/js/app.js` — Alpine.js + searchApp component
- `resources/css/app.css` — add `[x-cloak]` rule
- `resources/views/pages/home.blade.php` — Alpine.js search with suggestions
- `resources/views/pages/results.blade.php` — server-rendered results + Alpine search bar

Files that stay **unchanged** (6):
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/browser.blade.php`
- `resources/views/pages/sites/about.blade.php`
- `resources/views/pages/sites/test.blade.php`
- `vite.config.js`
- `public/assets/*` (all images)

**npm package to install:** `alpinejs`
