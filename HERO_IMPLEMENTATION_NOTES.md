# RCCG Hero Implementation Notes

## Source

Implemented from `/Users/user/Downloads/rccg-hero-codex-pack`, using the supplied approved desktop/mobile references and original church assets.

## Decisions

- Replaced the previous three-slide Swiper hero with the pack's approved single static hero.
- Copied the supplied church photograph and RCCG logo into `public/assets/images/hero-pack/`, with the hero using the enhanced `church-enhanced.png` derivative for sharper desktop rendering.
- Preserved the existing Laravel guest layout, route names, mobile navigation infrastructure, and all homepage sections below the hero.
- Added the five-item service strip with the exact schedule and times from the pack.
- Mapped calls to action to existing routes: events, the homepage live-stream anchor, about/pastorate, media, and contact.
- Added reduced-motion-safe transitions and mobile menu state updates for `aria-expanded`, Escape, and link activation.

## Validation

Planned browser checks:

- Desktop: 1670x941 and 1440x900
- Mobile: 390x844 and 430x932
- Tablet: 768x1024

Completed local checks:

- `php artisan view:cache` passed.
- `git diff --check` passed.
- Homepage returned HTTP 200 from the existing local server.
- Rendered HTML contains the new hero, all five service items, and the copied hero asset URLs.
- Existing feature tests: 39 passed before the unrelated `DepartmentManagementTest` failure for missing department image persistence.
- Playwright screenshot capture was not completed because the local wrapper script is not executable in the current environment and the existing port was already occupied.

The supplied reference uses a transformed/recomposed version of the church photograph. The implementation uses the original supplied photo, so crop, perspective, and lighting will differ from the raster reference while retaining the same building identity.
