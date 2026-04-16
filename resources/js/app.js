import './bootstrap';

function registerDarkModeStore() {
    window.Alpine.store('darkMode', {
        on: localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
        
        toggle() {
            this.on = !this.on;
            localStorage.setItem('darkMode', String(this.on));
            document.documentElement.classList.toggle('dark', this.on);
        },
        
        init() {
            document.documentElement.classList.toggle('dark', this.on);
        }
    });
}

function registerMediaGallery() {
    window.Alpine.data('mediaGallery', (items) => ({
        open: false,
        index: 0,
        items: items,
        loading: false,
        zoom: 1,
        touchStartX: 0,
        show(i) {
            this.index = i;
            this.zoom = 1;
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.open = false;
            this.zoom = 1;
            document.body.style.overflow = '';
        },
        next() {
            this.loading = true;
            this.zoom = 1;
            this.index = (this.index + 1) % this.items.length;
            setTimeout(() => this.loading = false, 100);
        },
        prev() {
            this.loading = true;
            this.zoom = 1;
            this.index = (this.index - 1 + this.items.length) % this.items.length;
            setTimeout(() => this.loading = false, 100);
        },
        toggleZoom() {
            this.zoom = this.zoom === 1 ? 2 : 1;
        }
    }));
}

if (window.Alpine) {
    registerDarkModeStore();
    registerMediaGallery();
} else {
    document.addEventListener('alpine:init', () => {
        registerDarkModeStore();
        registerMediaGallery();
    });
}
