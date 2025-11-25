{{-- Toast Notification Container Component --}}
<div 
    x-data="{ 
        init() {
            // Listen for Livewire toast events
            Livewire.on('toast', (event) => {
                const type = event.type || 'info';
                const message = event.message || '';
                
                // Use Alpine Notify to show toast
                if (this.$notify) {
                    this.$notify(message, type);
                }
            });
        }
    }"
    x-init="init()"
    aria-live="polite"
    aria-atomic="true"
    class="fixed top-4 right-4 z-50 space-y-2"
>
    {{-- Toast notifications will be injected here by Alpine Notify --}}
    <div x-notify class="space-y-2"></div>
</div>

<style>
    /* Custom styles for Alpine Notify toasts */
    [x-notify] .notify {
        @apply rounded-lg shadow-lg p-4 mb-2 max-w-sm animate-slide-in-down;
        @apply transition-all duration-300 ease-in-out;
    }
    
    [x-notify] .notify.success {
        @apply bg-green-500 text-white;
    }
    
    [x-notify] .notify.error {
        @apply bg-red-500 text-white;
    }
    
    [x-notify] .notify.warning {
        @apply bg-yellow-500 text-white;
    }
    
    [x-notify] .notify.info {
        @apply bg-blue-500 text-white;
    }
    
    [x-notify] .notify.dark {
        @apply bg-gray-800 text-white;
    }
    
    /* Dark mode support */
    .dark [x-notify] .notify.success {
        @apply bg-green-600;
    }
    
    .dark [x-notify] .notify.error {
        @apply bg-red-600;
    }
    
    .dark [x-notify] .notify.warning {
        @apply bg-yellow-600;
    }
    
    .dark [x-notify] .notify.info {
        @apply bg-blue-600;
    }
</style>
