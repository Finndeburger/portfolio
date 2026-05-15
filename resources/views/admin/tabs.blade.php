<div class="mb-8 flex flex-wrap gap-2">
    <a href="{{ route('admin.users') }}"
        class="rounded-lg px-4 py-2 text-sm font-semibold {{ request()->routeIs('admin.users') ? 'bg-[#212121] text-white' : 'border border-[#bdbdbd] text-[#212121] hover:bg-[#ececec]' }}">
        Users
    </a>

    <a href="{{ route('admin.sites') }}"
        class="rounded-lg px-4 py-2 text-sm font-semibold {{ request()->routeIs('admin.sites') || request()->routeIs('admin.sites.info') ? 'bg-[#212121] text-white' : 'border border-[#bdbdbd] text-[#212121] hover:bg-[#ececec]' }}">
        Sites
    </a>

    <a href="{{ route('admin.sites.create') }}"
        class="rounded-lg px-4 py-2 text-sm font-semibold {{ request()->routeIs('admin.sites.create') ? 'bg-[#212121] text-white' : 'border border-[#bdbdbd] text-[#212121] hover:bg-[#ececec]' }}">
        Site create
    </a>
</div>
