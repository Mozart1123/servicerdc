{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $url)
    <url>
        <loc>{{ is_array($url) ? $url['loc'] : $url->loc }}</loc>
        <lastmod>{{ is_array($url) ? $url['lastmod'] : $url->lastmod }}</lastmod>
        <changefreq>{{ is_array($url) ? $url['changefreq'] : $url->changefreq }}</changefreq>
        <priority>{{ is_array($url) ? $url['priority'] : $url->priority }}</priority>
    </url>
@endforeach
</urlset>
