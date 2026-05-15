<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Support\ReadableSecrets;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function users(): View
    {
        return view('admin.users', [
            'users' => User::query()->latest()->get(),
        ]);
    }

    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('status', 'Your own admin account was protected from deletion.');
        }

        $user->delete();

        return back()->with('status', 'User deleted.');
    }

    public function destroyAllUsers(Request $request): RedirectResponse
    {
        $currentId = (int) $request->user()->id;

        User::query()->where('id', '!=', $currentId)->delete();

        return back()->with('status', 'All users (except your own account) were deleted.');
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        if ((int) $request->user()->id === (int) $user->id && $validated['role'] !== 'admin') {
            return back()->with('status', 'Your own admin role cannot be removed from this panel.');
        }

        $user->forceFill([
            'role' => $validated['role'],
        ])->save();

        return back()->with('status', sprintf('Role updated for %s: %s.', $user->email, $validated['role']));
    }

    public function resetUserPassword(User $user): RedirectResponse
    {
        $generatedPassword = ReadableSecrets::password();

        $user->forceFill([
            'password' => $generatedPassword,
            'remember_token' => Str::random(60),
        ])->save();

        return back()->with('status', sprintf(
            'Password reset for %s. Temporary password: %s',
            $user->email,
            $generatedPassword
        ));
    }

    public function sites(): View
    {
        $sites = Site::query()->orderBy('title')->get()->map(function (Site $site) {
            $folder = resource_path('views/pages/sites/'.$site->slug);
            $pages = [];

            if (File::isDirectory($folder)) {
                $pages = collect(File::files($folder))
                    ->filter(fn ($file) => $file->getExtension() === 'php')
                    ->map(fn ($file) => str_replace('.blade', '', $file->getBasename('.php')))
                    ->values()
                    ->all();
            }

            return [
                'model' => $site,
                'pages' => $pages,
            ];
        });

        return view('admin.sites', [
            'sites' => $sites,
        ]);
    }

    public function siteInfo(Site $site): View
    {
        return view('admin.site-info', [
            'site' => $site,
            'tags' => implode(', ', $site->tags ?? []),
        ]);
    }

    public function updateSiteInfo(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:sites,slug,'.$site->id],
            'dummy_url' => ['required', 'url', 'max:255'],
            'description' => ['required', 'string'],
            'tags' => ['nullable', 'string'],
            'sponsored' => ['nullable', 'boolean'],
            'database_connection' => ['nullable', 'string', 'max:255'],
            'database_table' => ['nullable', 'string', 'max:255'],
            'database_meta' => ['nullable', 'string'],
        ]);

        $databaseMeta = null;

        if (! empty($validated['database_meta'])) {
            $decoded = json_decode($validated['database_meta'], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $databaseMeta = $decoded;
            }
        }

        $site->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'dummy_url' => $validated['dummy_url'],
            'description' => $validated['description'],
            'tags' => collect(explode(',', (string) ($validated['tags'] ?? '')))
                ->map(fn ($tag) => trim($tag))
                ->filter()
                ->values()
                ->all(),
            'sponsored' => (bool) ($validated['sponsored'] ?? false),
            'database_connection' => $validated['database_connection'] ?? null,
            'database_table' => $validated['database_table'] ?? null,
            'database_meta' => $databaseMeta,
        ]);

        return back()->with('status', 'Site metadata saved.');
    }

    public function siteCreate(): View
    {
        return view('admin.site-create');
    }

    public function storeSiteCreate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:sites,slug'],
            'dummy_url' => ['nullable', 'url', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'tags' => ['nullable', 'string'],
            'sponsored' => ['nullable', 'boolean'],
            'database_connection' => ['nullable', 'string', 'max:255'],
            'database_table' => ['nullable', 'string', 'max:255'],
            'database_meta' => ['nullable', 'string'],
            'create_about' => ['nullable', 'boolean'],
            'create_contact' => ['nullable', 'boolean'],
            'create_assets_folder' => ['nullable', 'boolean'],
            'force_overwrite' => ['nullable', 'boolean'],
        ]);

        $slug = $validated['slug'];
        $siteTitle = $validated['title'];
        $dummyUrl = $validated['dummy_url'] ?? $this->defaultDummyUrl($slug);
        $description = $validated['description'];

        $databaseMeta = null;

        if (! empty($validated['database_meta'])) {
            $decoded = json_decode($validated['database_meta'], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $databaseMeta = $decoded;
            }
        }

        Site::query()->create([
            'title' => $siteTitle,
            'slug' => $slug,
            'dummy_url' => $dummyUrl,
            'description' => $description,
            'tags' => collect(explode(',', (string) ($validated['tags'] ?? '')))
                ->map(fn (string $tag) => trim($tag))
                ->filter()
                ->values()
                ->all(),
            'sponsored' => (bool) ($validated['sponsored'] ?? false),
            'database_connection' => $validated['database_connection'] ?? null,
            'database_table' => $validated['database_table'] ?? null,
            'database_meta' => $databaseMeta,
        ]);

        $forceOverwrite = (bool) ($validated['force_overwrite'] ?? false);
        $siteViewPath = resource_path('views/pages/sites/'.$slug);

        if (! File::isDirectory($siteViewPath)) {
            File::makeDirectory($siteViewPath, 0755, true);
        }

        $this->writeTemplateIfAllowed(
            $siteViewPath.'/index.blade.php',
            $this->indexTemplate($siteTitle, $slug, $dummyUrl, $description),
            $forceOverwrite
        );

        if ((bool) ($validated['create_about'] ?? false)) {
            $this->writeTemplateIfAllowed(
                $siteViewPath.'/about.blade.php',
                $this->aboutTemplate($siteTitle, $slug, $dummyUrl),
                $forceOverwrite
            );
        }

        if ((bool) ($validated['create_contact'] ?? false)) {
            $this->writeTemplateIfAllowed(
                $siteViewPath.'/contact.blade.php',
                $this->contactTemplate($siteTitle, $slug, $dummyUrl),
                $forceOverwrite
            );
        }

        if ((bool) ($validated['create_assets_folder'] ?? false)) {
            $assetsPath = public_path('assets/sites/'.$slug);

            if (! File::isDirectory($assetsPath)) {
                File::makeDirectory($assetsPath, 0755, true);
            }

            $readmePath = $assetsPath.'/README.txt';

            if (! File::exists($readmePath) || $forceOverwrite) {
                File::put(
                    $readmePath,
                    "Drop images or files for this mini-site here.\nUsed by slug: {$slug}\n"
                );
            }
        }

        return redirect()
            ->route('admin.sites')
            ->with('status', sprintf('Site "%s" created with scaffold files.', $siteTitle));
    }

    private function defaultDummyUrl(string $slug): string
    {
        $base = rtrim((string) config('app.url', 'https://example.com'), '/');

        return $base.'/sites/'.$slug;
    }

    private function writeTemplateIfAllowed(string $path, string $content, bool $forceOverwrite): void
    {
        if (File::exists($path) && ! $forceOverwrite) {
            return;
        }

        File::put($path, $content);
    }

    private function indexTemplate(string $siteTitle, string $slug, string $dummyUrl, string $description): string
    {
        return <<<BLADE
@extends('layouts.browser')

@section('title', '{$siteTitle}')
@section('browser-url', '{$dummyUrl}')

@section('content')
<section class="min-h-full bg-[#f8f8f8] text-[#212121] px-8 py-10">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold mb-3">{$siteTitle}</h1>
        <p class="text-[#4b5563] mb-8">{$description}</p>

        <nav class="flex flex-wrap gap-3 mb-8">
            <a href="{{ route('sites.show', ['slug' => '{$slug}']) }}" class="px-3 py-1.5 rounded-lg border border-[#c4c4c4] hover:bg-white">Home</a>
            <a href="{{ route('sites.show', ['slug' => '{$slug}', 'page' => 'about']) }}" class="px-3 py-1.5 rounded-lg border border-[#c4c4c4] hover:bg-white">About</a>
            <a href="{{ route('sites.show', ['slug' => '{$slug}', 'page' => 'contact']) }}" class="px-3 py-1.5 rounded-lg border border-[#c4c4c4] hover:bg-white">Contact</a>
        </nav>

        <div class="rounded-xl border border-[#d8d8d8] bg-white p-6">
            <p class="text-sm text-[#6b7280]">Generated scaffold for mini-site slug: {$slug}</p>
        </div>
    </div>
</section>
@endsection
BLADE;
    }

    private function aboutTemplate(string $siteTitle, string $slug, string $dummyUrl): string
    {
        $url = rtrim($dummyUrl, '/').'/about';

        return <<<BLADE
@extends('layouts.browser')

@section('title', '{$siteTitle} - About')
@section('browser-url', '{$url}')

@section('content')
<section class="min-h-full bg-[#f8f8f8] text-[#212121] px-8 py-10">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-3">About {$siteTitle}</h1>
        <p class="text-[#4b5563] mb-8">This page was generated automatically from the admin Site create tab.</p>

        <a href="{{ route('sites.show', ['slug' => '{$slug}']) }}" class="inline-flex px-3 py-1.5 rounded-lg border border-[#c4c4c4] hover:bg-white">Back to home</a>
    </div>
</section>
@endsection
BLADE;
    }

    private function contactTemplate(string $siteTitle, string $slug, string $dummyUrl): string
    {
        $url = rtrim($dummyUrl, '/').'/contact';

        return <<<BLADE
@extends('layouts.browser')

@section('title', '{$siteTitle} - Contact')
@section('browser-url', '{$url}')

@section('content')
<section class="min-h-full bg-[#f8f8f8] text-[#212121] px-8 py-10">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-3">Contact {$siteTitle}</h1>
        <p class="text-[#4b5563] mb-8">Use this page to add your contact form or social links.</p>

        <a href="{{ route('sites.show', ['slug' => '{$slug}']) }}" class="inline-flex px-3 py-1.5 rounded-lg border border-[#c4c4c4] hover:bg-white">Back to home</a>
    </div>
</section>
@endsection
BLADE;
    }
}
