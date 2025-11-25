import './bootstrap';
import AlpineNotify from 'alpinejs-notify';

// Register Alpine Notify plugin
document.addEventListener('alpine:init', () => {
  if (window.Alpine) {
    window.Alpine.plugin(AlpineNotify);
  }
});

import.meta.glob([
  '../images/**',
  '../fonts/**',
]);