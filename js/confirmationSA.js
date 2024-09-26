function confirmDelete(event) {
    event.preventDefault(); // Prevent form submission

    // Display SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#7dcabb',
        confirmButtonText: 'Yes!'
    }).then((result) => {
        if (result.isConfirmed) {
            // If user confirms, submit the form
            event.target.submit();
        }
    });
}

function confirmCancel(event) {
    event.preventDefault(); // Prevent form submission

    // Display SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#7dcabb',
        confirmButtonText: 'Yes!',
        reverseButtons: false,  /* Prevents double-clicking */
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            // If user confirms, submit the form
            event.target.submit();
        }
    });
}