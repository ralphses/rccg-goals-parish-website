@include('errors.layout', [
    'statusCode' => 404,
    'title' => 'We could not find that page',
    'message' => request()->is('dashboard*')
        ? 'The dashboard page or record you requested may have moved, been removed, or never existed.'
        : 'The page you requested may have moved, been removed, or the link may be incomplete.',
    'hint' => request()->is('dashboard*')
        ? 'Return to the dashboard and use the menu or listings to open the right record again.'
        : 'Use the homepage or contact page to continue finding sermons, events, and ministry information.',
    'secondaryLabel' => request()->is('dashboard*') ? 'Go Home' : 'Contact Us',
    'secondaryUrl' => request()->is('dashboard*') ? route('home') : route('contact'),
])
