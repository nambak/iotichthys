import Swal from 'sweetalert2';

// Set up SweetAlert2 with dark theme defaults
window.Swal = Swal.mixin({
    customClass: {
        popup: 'dark:bg-gray-800 dark:text-white',
        title: 'dark:text-white',
        htmlContainer: 'dark:text-gray-300',
        confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 mr-2',
        cancelButton: 'bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 mr-2',
    },
    buttonsStyling: false,
    background: 'var(--swal2-background, #1f2937)',
    color: 'var(--swal2-color, #f9fafb)',
    showClass: {
        popup: 'animate__animated animate__fadeInDown'
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
    }
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
