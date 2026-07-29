@props(['department'])

<!--Department Details Start-->
<section class="event-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="event-details__img">
                    <img src="{{ $department->image_url ?? asset('assets/images/resources/event-details-img-1.jpg') }}" alt="{{ $department->name }}" loading="eager">
                </div>
            </div>
        </div>
        <div class="event-details__bottom">
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="event-details__bottom-left">
                        <div class="event-details__bottom-content">
                            <h3 class="event-details__title">{{ $department->name }}</h3>
                            <p class="event-details__text-1">{{ $department->description }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="event-details__sidebar">
                        <ul class="event-details__sidebar-details list-unstyled">
                            <li>
                                <p>Department Leader:</p>
                                <span>{{ $department->leader->name ?? 'N/A' }}</span>
                            </li>
                            <li>
                                <p>Contact:</p>
                                <a href="mailto:{{ $department->contact ?? 'info@rccggoalsparish.com' }}">{{ $department->contact ?? 'info@rccggoalsparish.com' }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Department Details End-->
