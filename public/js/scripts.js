document.addEventListener("DOMContentLoaded", function() {
    let deleteLinks = document.querySelectorAll(".delete-link");
    
    deleteLinks.forEach(function(link) {
        link.addEventListener("click", function(event) {
            let confirmDelete = confirm("Bạn có chắc chắn muốn xóa không?");
            if (!confirmDelete) {
                event.preventDefault();
            }
        });
    });
});
