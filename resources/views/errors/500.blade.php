@include('errors.layout', [
    'statusCode' => 500,
    'title' => 'Something unexpected happened',
    'message' => request()->is('dashboard*')
        ? 'We hit an unexpected server problem while loading this dashboard experience.'
        : 'We hit an unexpected server problem while loading this page.',
    'hint' => request()->is('dashboard*')
        ? 'Please return to the dashboard and try the action again in a moment.'
        : 'Please return to the homepage and try again in a moment.',
    'secondaryLabel' => request()->is('dashboard*') ? 'Go Home' : 'Contact Us',
    'secondaryUrl' => request()->is('dashboard*') ? route('home') : route('contact'),
])
