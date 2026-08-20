<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $row)
    <url>
        <loc>{{ $row[0] }}</loc>
        @if($row[1])
            <lastmod>{{ $row[1]->toAtomString() }}</lastmod>
        @endif
    </url>
@endforeach
</urlset>
