<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Crawler</title>
</head>

<body>

    <form method="post">
        @csrf
        <label for="myURL">Enter an https:// URL:</label>

        <input id="myURL" name="site_url" required value="https://agencyanalytics.com" />
        <button>Submit</button>
    </form>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @isset($res)
    <hr>
    <table>
        <thead>
            <tr>
                <h3>Crawler total statistics</h3>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Number of pages crawled</td>
                <td>{{ $res->getPageCount() }}</td>
            </tr>
            <tr>
                <td>Number of a unique images</td>
                <td>{{ $res->getImageCount() }}</td>
            </tr>
            <tr>
                <td>Number of unique internal links</td>
                <td>{{ $res->getInternalLinksCount() }}</td>
            </tr>
            <tr>
                <td>Number of unique external links</td>
                <td>{{ $res->getExternalLinksCount() }}</td>
            </tr>
            <tr>
                <td>Average page load in seconds</td>
                <td>{{ $res->getAverageLoadTime() }} sec</td>
            </tr>
            <tr>
                <td>Average word count</td>
                <td>{{ $res->getAverageWordCount() }}</td>
            </tr>
            <tr>
                <td>Average title length</td>
                <td>{{ $res->getAverageTitleLength() }}</td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table>
        <thead>
            <tr>
                <h3>Pages statistics</h3>
            </tr>
            <tr>
            <td>Page URL</td>
            <td>Status</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($res->getPages() as $page)
            <tr>
                <td>{{ $page->URL }}</td>
                <td>{{ $page->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <h3>Pages scrapped data</h3>
    {{ dd($res->getPages()) }}
    @endisset

</body>

</html>