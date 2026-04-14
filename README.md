# xgate-laravel-project
Laravel + Livewire example project for X-Gate

## Setup

- `docker compose up -d`
- `docker compose exec app php artisan migrate --seed`
- `docker compose exec node npm run build`

## Notes

- Media rendering skips missing files on disk, so stale seeded records won't spam the browser console.
- If you add or change frontend classes, rebuild assets in the `node` container.
