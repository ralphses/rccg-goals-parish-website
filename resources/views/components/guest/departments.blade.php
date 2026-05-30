@props(['departments', 'sort'])

<!--Events Page Start-->
<section class="events-page py-5">
    <div class="container">

        <!-- Section Title -->
        <div class="section-title text-center mb-3">
            <span class="section-sub-title">Several Units</span>
            <h2 class="section-title__title">Church Departments</h2>
        </div>

        <!-- Search and Sort -->
        <div class="row mb-4 align-items-center">
            <!-- Search Form -->
            <div class="col-xl-8 col-lg-8 col-md-12 mb-3 mb-lg-0">
                <form class="events-search-form d-flex shadow-sm rounded-pill overflow-hidden w-100" method="GET" action="{{ route('departments') }}">
                    <input type="text" name="query" class="form-control border-0 px-4 py-3"
                        placeholder="Search departments..." value="{{ request('query') }}">
                    <button type="submit" class="btn btn-primary px-4 rounded-end">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Sort Dropdown -->
            <div class="col-xl-4 col-lg-4 col-md-12 text-lg-end">
                <form id="sortForm" method="GET" action="{{ route('departments') }}">
                    <select name="sort" class="form-select shadow-sm rounded-pill w-100 py-3 px-3 border-0 mt-2 mt-lg-0" onchange="document.getElementById('sortForm').submit();">
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Sort by Latest</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>
                    <input type="hidden" name="query" value="{{ request('query') }}">
                </form>
            </div>
        </div>

        <div class="row">
            @foreach ($departments as $department)
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <!--Department Single-->
                <div class="events__single shadow-sm rounded overflow-hidden">
                    <div class="events__img position-relative">
                        <img src="{{ asset('assets/images/resources/events-img-1.jpg') }}" alt="{{ $department->name }}" class="img-fluid">
                    </div>
                    <div class="events__content p-3">
                        <h3 class="events__title mb-2"><a href="{{ route('department', $department) }}">{{ $department->name }}</a></h3>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{ $departments->links('vendor.pagination.modern') }}
    </div>
</section>
<!--Events Page End-->