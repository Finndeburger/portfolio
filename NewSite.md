# Creating a new site

1. Edit the seeder (in the db/seeders folder)

   ~~~
   Site::create([
    'title' => 'My New Site',
    'slug' => 'my-new-site',
    'dummy_url' => 'https://example.com/my-new-site',
    'description' => 'A description for search results',
    'tags' => ['tag1', 'tag2', 'tag3'],
    'sponsored' => false,
    'database_connection' => null,
    'database_table' => null,
    'database_meta' => null,
   ]);
2. Then run ```php artisan migrate:fresh --seed```

3. Add a folder in views/sites

4. Add the index.blade.php file

5. Add the content
```@extends('layouts.app')```

```@section('content')```

````@endsection```

OR for browser style

```@extends('layouts.browser')```

```@section('title', $site->title)```

```@section('browser-url', $site->dummy_url)```