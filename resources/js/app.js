import Swal from 'sweetalert2';

// Set up SweetAlert2 with dark theme defaults
window.Swal = Swal.mixin({
    theme: 'dark',
});

// Global function for confirmation dialogs
window.confirmDelete = function(message, callback) {
    Swal.fire({
        title: '확인',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '삭제',
        cancelButtonText: '취소',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
};

// Global function for error toasts
window.showErrorToast = function(message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: message,
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        customClass: {
            popup: 'dark:bg-gray-800 dark:text-white colored-toast'
        }
    });
};

// Global function for success toasts
window.showSuccessToast = function(message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: 'dark:bg-gray-800 dark:text-white colored-toast'
        }
    });
};
