@php
    $title = $title ?? 'Example product';
    $price = $price ?? '$15.00';
    $image = $image ?? 'https://place-hold.it/300';
@endphp
<html>
<head>
    @if ($title !== 'invalid')
        <meta property="og:title" content="{{ $title }}">
    @endif
    @if ($image !== 'invalid')
        <meta property="og:image" content="{{ $image }}">
    @endif
    @if ($price !== 'invalid')
            <meta property="og:price:amount" content="{{ $price }}">
    @endif
</head>
<body>
    <p>This page is used for test responses</p>
</body>
</html>
