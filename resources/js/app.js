import './bootstrap';
import Swal from 'sweetalert2';

window.addEventListener('swal:confirm', event => {
    Swal.fire({
        title: event.detail.title,
        text: event.detail.text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: event.detail.confirmButtonText,
        cancelButtonText: event.detail.cancelButtonText,
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('swal:confirmed');
        }
    });
});

window.addEventListener('swal:success', event => {
    Swal.fire({
        title: event.detail.title,
        text: event.detail.text,
        icon: 'success',
    });
});

window.addEventListener('swal:error', event => {
    Swal.fire({
        title: event.detail.title,
        text: event.detail.text,
        icon: 'error',
    });
});
