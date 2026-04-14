# Security Scan Report (Pattern-Based)

Scope: quick, pattern-based scan for common unsafe code patterns.

## Summary

Checked patterns:
- `DB::raw`
- `eval(`
- `shell_exec(`
- `exec(`
- `passthru(`
- `system(`
- `<?=`
- `{!! !!}`

## Files with matches

- `public/vendor/livewire/livewire.esm.js`
- `public/vendor/livewire/livewire.csp.min.js.map`
- `public/vendor/livewire/livewire.csp.esm.js`
- `public/vendor/livewire/livewire.min.js`
- `public/vendor/livewire/livewire.csp.min.js`
- `public/vendor/livewire/livewire.csp.js`
- `public/vendor/livewire/livewire.js`
- `public/vendor/livewire/livewire.min.js.map`
- `public/vendor/livewire/livewire.csp.esm.js.map`
- `public/vendor/livewire/livewire.esm.js.map`

Results:
- `DB::raw`: No matches found
- `eval(`: No matches found
- `shell_exec(`: No matches found
- `exec(`: **Matches found** (see below)
- `passthru(`: No matches found
- `system(`: No matches found
- `<?=`: No matches found
- `{!! !!}`: No matches found

## Findings (file:line)

### `exec(`

> Note: All matches were found in vendored Livewire JavaScript under `public/vendor/livewire/` (including minified bundles and sourcemaps). These are typically third-party assets; review in the context of your supply-chain / asset update strategy.

- `public/vendor/livewire/livewire.esm.js:210`
- `public/vendor/livewire/livewire.esm.js:10707`
- `public/vendor/livewire/livewire.csp.esm.js:216`
- `public/vendor/livewire/livewire.csp.esm.js:14505`
- `public/vendor/livewire/livewire.csp.js:217`
- `public/vendor/livewire/livewire.csp.js:15482`
- `public/vendor/livewire/livewire.js:9840`
- `public/vendor/livewire/livewire.csp.min.js:2`
- `public/vendor/livewire/livewire.csp.min.js:89`
- `public/vendor/livewire/livewire.min.js:82`
- `public/vendor/livewire/livewire.csp.min.js.map:4`
- `public/vendor/livewire/livewire.min.js.map:4`
- `public/vendor/livewire/livewire.csp.esm.js.map:4`
- `public/vendor/livewire/livewire.esm.js.map:4`
